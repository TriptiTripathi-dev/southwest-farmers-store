<?php

namespace App\Services;

use App\Exceptions\TimeClockException;
use App\Models\ClockOverride;
use App\Models\CrossLocationPermission;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeScheduleChange;
use App\Models\StoreDetail;
use App\Models\StoreSetting;
use App\Models\StoreTimeLog;
use App\Models\StoreUser;
use App\Models\TimeLogCorrection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Scheduling and time clock rules (kitchen spec 9-10, contract B.8/B.9).
 *
 * Location: the store whose terminal (logged-in store session) records the
 * clock-in. (The client may later pick another check: network, GPS... -
 * product owner decision 9.5.)
 *
 * Clock-in is allowed when:
 *  1. the person has a shift at THIS store whose window is open
 *     (from "early minutes" before start until the shift ends) -> scheduled;
 *  2. their open shift is at another store, and they hold a cross-location
 *     permission for this store -> cross_location;
 *  3. they have no open shift, but a GM approved an override for them at this
 *     store today -> gm_override;
 *  4. or a GM enters the clock-in for them -> gm_entered.
 * Everything else is blocked with the reason. Late / early departure are
 * minutes past the grace allowance.
 */
class TimeClockService
{
    public static function tz(): string
    {
        return config('app.display_timezone', 'America/Chicago');
    }

    /** Shift (any store) whose clock-in window is open at $at. */
    public function openShift(StoreUser $employee, Carbon $at, ?int $storeId = null): ?EmployeeSchedule
    {
        $localDay = $at->copy()->timezone(self::tz());
        $candidates = EmployeeSchedule::where('store_user_id', $employee->id)->where('status', 'scheduled')
            ->whereBetween('shift_date', [$localDay->copy()->subDay()->toDateString(), $localDay->toDateString()])
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->orderBy('shift_date')->orderBy('start_time')->get();

        return $candidates->first(function (EmployeeSchedule $s) use ($at) {
            $early = StoreSetting::timeClockOptions($s->store_id)['early_minutes'];

            return $at->greaterThanOrEqualTo($s->startsAt()->subMinutes($early)) && $at->lessThan($s->endsAt());
        });
    }

    public function clockIn(StoreUser $employee, int $storeId, StoreUser $actor, ?string $notes = null, ?Carbon $at = null): StoreTimeLog
    {
        $at = $at ?? now();
        if (!$employee->is_active) {
            throw new TimeClockException("{$employee->name} is not an active employee.");
        }
        if (StoreTimeLog::where('store_user_id', $employee->id)->whereNull('clock_out_at')->exists()) {
            throw new TimeClockException("{$employee->name} is already clocked in.");
        }

        $basis = null;
        $shift = $this->openShift($employee, $at, $storeId);
        $permission = null;
        $override = null;

        if ($shift) {
            $basis = 'scheduled';
        } elseif ($elsewhere = $this->openShift($employee, $at)) {
            $permission = CrossLocationPermission::where('store_user_id', $employee->id)->where('store_id', $storeId)->first();
            if (!$permission) {
                throw new TimeClockException("{$employee->name} is scheduled at " . ($elsewhere->store->store_name ?? 'another store')
                    . ' now, not here. Clock in at that store, or ask a manager for cross-location permission.');
            }
            $shift = $elsewhere;
            $basis = 'cross_location';
        } else {
            $override = ClockOverride::where('store_user_id', $employee->id)->where('store_id', $storeId)
                ->whereDate('work_date', $at->copy()->timezone(self::tz())->toDateString())->whereNull('used_time_log_id')->latest('id')->first();
            if (!$override) {
                throw new TimeClockException("{$employee->name} is not scheduled to work now. A General Manager has to approve an unscheduled clock-in first.");
            }
            $basis = 'gm_override';
        }

        return DB::transaction(function () use ($employee, $storeId, $actor, $notes, $at, $basis, $shift, $permission, $override) {
            $log = StoreTimeLog::create([
                'store_user_id' => $employee->id,
                'store_id' => $storeId,
                'assigned_store_id' => $employee->store_id,
                'clock_in_at' => $at,
                'status' => 'clocked_in',
                'notes' => $notes,
                'employee_schedule_id' => $shift?->id,
                'scheduled_start_at' => $shift?->startsAt(),
                'scheduled_end_at' => $shift?->endsAt(),
                'clock_basis' => $basis,
                'cross_location_permission_id' => $permission?->id,
                'override_by_name' => $override?->approved_by_name,
                'override_reason' => $override?->reason,
                'late_minutes' => $this->lateMinutes($shift, $at),
                'clock_in_by_name' => $actor->name,
            ]);
            $override?->update(['used_time_log_id' => $log->id]);

            return $log;
        });
    }

    /** A GM records the clock-in for an unscheduled employee (spec 9.4). */
    public function gmEnter(StoreUser $employee, int $storeId, StoreUser $gm, string $reason, ?Carbon $at = null): StoreTimeLog
    {
        $at = $at ?? now();
        if ((int) $employee->id === (int) $gm->id) {
            throw new TimeClockException('A General Manager cannot enter their own unscheduled clock-in.');
        }
        if (StoreTimeLog::where('store_user_id', $employee->id)->whereNull('clock_out_at')->exists()) {
            throw new TimeClockException("{$employee->name} is already clocked in.");
        }
        if ($at->isFuture()) {
            throw new TimeClockException('A clock-in time cannot be in the future.');
        }

        $shift = $this->openShift($employee, $at, $storeId);

        return StoreTimeLog::create([
            'store_user_id' => $employee->id,
            'store_id' => $storeId,
            'assigned_store_id' => $employee->store_id,
            'clock_in_at' => $at,
            'status' => 'clocked_in',
            'employee_schedule_id' => $shift?->id,
            'scheduled_start_at' => $shift?->startsAt(),
            'scheduled_end_at' => $shift?->endsAt(),
            'clock_basis' => 'gm_entered',
            'override_by_name' => $gm->name,
            'override_reason' => $reason,
            'late_minutes' => $this->lateMinutes($shift, $at),
            'clock_in_by_name' => $gm->name,
        ]);
    }

    public function approveOverride(StoreUser $employee, int $storeId, StoreUser $gm, string $reason): ClockOverride
    {
        if ((int) $employee->id === (int) $gm->id) {
            throw new TimeClockException('You cannot approve your own unscheduled clock-in.');
        }

        return ClockOverride::create([
            'store_user_id' => $employee->id,
            'store_id' => $storeId,
            'work_date' => now(self::tz())->toDateString(),
            'reason' => $reason,
            'approved_by' => $gm->id,
            'approved_by_name' => $gm->name,
        ]);
    }

    public function clockOut(StoreTimeLog $log, StoreUser $actor, int $breakMinutes = 0, ?string $notes = null, ?Carbon $at = null): StoreTimeLog
    {
        if ($log->clock_out_at) {
            throw new TimeClockException('This time record is already clocked out.');
        }
        $at = $at ?? now();
        $log->fill([
            'clock_out_at' => $at,
            'break_minutes' => max(0, $breakMinutes),
            'status' => 'clocked_out',
            'notes' => $notes ?? $log->notes,
            'clock_out_by_name' => $actor->name,
        ]);
        $this->recalculate($log);
        $log->save();

        return $log;
    }

    /**
     * Manager correction (spec 10.2): each changed field is written to
     * time_log_corrections with the old value; the record needs approval again.
     *
     * @param array{clock_in_at?:?Carbon, clock_out_at?:?Carbon, break_minutes?:?int} $changes
     */
    public function correct(StoreTimeLog $log, StoreUser $manager, array $changes, string $reason): StoreTimeLog
    {
        return DB::transaction(function () use ($log, $manager, $changes, $reason) {
            $fmt = fn ($v) => $v instanceof Carbon ? $v->copy()->utc()->toDateTimeString() : ($v === null ? null : (string) $v);
            $changed = false;
            foreach (['clock_in_at', 'clock_out_at', 'break_minutes'] as $field) {
                if (!array_key_exists($field, $changes)) {
                    continue;
                }
                $old = $fmt($log->{$field});
                $new = $fmt($changes[$field]);
                if ($old === $new) {
                    continue;
                }
                TimeLogCorrection::create([
                    'store_time_log_id' => $log->id, 'field' => $field, 'old_value' => $old, 'new_value' => $new,
                    'reason' => $reason, 'user_id' => $manager->id, 'user_name' => $manager->name,
                ]);
                $log->{$field} = $changes[$field];
                $changed = true;
            }
            if (!$changed) {
                throw new TimeClockException('Nothing was changed.');
            }
            if ($log->clock_out_at && $log->clock_in_at && $log->clock_out_at->lessThanOrEqualTo($log->clock_in_at)) {
                throw new TimeClockException('Clock-out must be after clock-in.');
            }

            $log->status = $log->clock_out_at ? 'clocked_out' : 'clocked_in';
            $log->corrected = true;
            $log->approved_by_name = null;   // a corrected record needs approval again
            $log->approved_at = null;
            $log->late_minutes = $this->lateMinutes($log->schedule, $log->clock_in_at);
            $this->recalculate($log);
            $log->save();

            return $log;
        });
    }

    public function approve(StoreTimeLog $log, StoreUser $manager): void
    {
        if (!$log->clock_out_at) {
            throw new TimeClockException('Only finished (clocked-out) records can be approved.');
        }
        if ((int) $log->store_user_id === (int) $manager->id) {
            throw new TimeClockException('You cannot approve your own time record.');
        }
        $log->update(['approved_by_name' => $manager->name, 'approved_at' => now()]);
    }

    /** Create or update a shift; every change goes to the history (spec 9.1). */
    public function saveSchedule(array $data, StoreUser $user, ?EmployeeSchedule $schedule = null): EmployeeSchedule
    {
        return DB::transaction(function () use ($data, $user, $schedule) {
            if ($schedule) {
                $before = $schedule->only(array_keys($data));
                $schedule->fill($data + ['updated_by_name' => $user->name])->save();
                $diff = collect($data)->filter(fn ($v, $k) => (string) ($before[$k] instanceof \DateTimeInterface ? $before[$k]->format('Y-m-d') : $before[$k]) !== (string) $v)
                    ->map(fn ($v, $k) => ['from' => $before[$k] instanceof \DateTimeInterface ? $before[$k]->format('Y-m-d') : $before[$k], 'to' => $v])->all();
                if ($diff) {
                    $this->history($schedule, 'updated', $diff, $user);
                }
            } else {
                $schedule = EmployeeSchedule::create($data + ['created_by_name' => $user->name, 'status' => 'scheduled']);
                $this->history($schedule, 'created', $data, $user);
            }

            return $schedule;
        });
    }

    public function cancelSchedule(EmployeeSchedule $schedule, StoreUser $user): void
    {
        $schedule->update(['status' => 'cancelled', 'updated_by_name' => $user->name]);
        $this->history($schedule, 'cancelled', null, $user);
    }

    protected function history(EmployeeSchedule $s, string $action, ?array $changes, StoreUser $user): void
    {
        EmployeeScheduleChange::create(['employee_schedule_id' => $s->id, 'action' => $action, 'changes' => $changes, 'user_id' => $user->id, 'user_name' => $user->name]);
    }

    protected function lateMinutes(?EmployeeSchedule $shift, ?Carbon $clockIn): int
    {
        if (!$shift || !$clockIn) {
            return 0;
        }
        $late = (int) floor($shift->startsAt()->diffInMinutes($clockIn, false));

        return $late > StoreSetting::timeClockOptions($shift->store_id)['late_grace'] ? $late : 0;
    }

    /** Worked hours, and early-departure minutes against the scheduled end. */
    protected function recalculate(StoreTimeLog $log): void
    {
        if (!$log->clock_in_at || !$log->clock_out_at) {
            $log->total_hours = null;
            $log->early_leave_minutes = 0;

            return;
        }
        $minutes = max(0, $log->clock_in_at->diffInMinutes($log->clock_out_at) - (int) $log->break_minutes);
        $log->total_hours = round($minutes / 60, 2);

        $early = $log->scheduled_end_at ? (int) floor($log->clock_out_at->diffInMinutes($log->scheduled_end_at, false)) : 0;
        $grace = StoreSetting::timeClockOptions($log->store_id)['early_leave_grace'];
        $log->early_leave_minutes = $early > $grace ? $early : 0;
    }

    /** Stores a person may clock in at besides their own. */
    public function crossLocationStores(StoreUser $employee)
    {
        return StoreDetail::whereIn('id', CrossLocationPermission::where('store_user_id', $employee->id)->pluck('store_id'))->get();
    }
}
