<?php

namespace App\Http\Controllers\Store;

use App\Exceptions\TimeClockException;
use App\Http\Controllers\Controller;
use App\Models\ClockOverride;
use App\Models\CrossLocationPermission;
use App\Models\EmployeeSchedule;
use App\Models\StoreDetail;
use App\Models\StoreSetting;
use App\Models\StoreTimeLog;
use App\Models\StoreUser;
use App\Services\TimeClockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manager side of timekeeping (kitchen spec 9.3-10.3):
 * review / correct / approve time records, GM override and GM-entered
 * clock-ins, cross-location permissions, clock settings, and the attendance
 * and payroll-hour reports. Permissions: approve_timesheets (review, correct,
 * approve, reports), approve_time_exceptions (GM), manage_schedules
 * (cross-location permissions, clock settings).
 */
class StoreTimeRecordsController extends Controller
{
    public function __construct(private TimeClockService $clock)
    {
    }

    protected function need(string ...$perms): void
    {
        $u = Auth::user();
        abort_unless(collect($perms)->contains(fn ($p) => $u->hasPermission($p)), 403, 'You do not have permission for this.');
    }

    protected function range(Request $request): array
    {
        $tz = TimeClockService::tz();
        $from = Carbon::parse($request->input('from', now($tz)->startOfWeek(Carbon::MONDAY)->toDateString()), $tz)->startOfDay();
        $to = Carbon::parse($request->input('to', now($tz)->toDateString()), $tz)->endOfDay();

        return [$from, $to];
    }

    protected function ownLog(int $id): StoreTimeLog
    {
        return StoreTimeLog::where('store_id', Auth::user()->store_id)->findOrFail($id);
    }

    public function index(Request $request)
    {
        $this->need('approve_timesheets', 'approve_time_exceptions');
        $storeId = Auth::user()->store_id;
        $tz = TimeClockService::tz();
        [$from, $to] = $this->range($request);

        $query = StoreTimeLog::where('store_id', $storeId)->with('staff', 'assignedStore', 'corrections')
            ->whereBetween('clock_in_at', [$from->copy()->utc(), $to->copy()->utc()])->latest('clock_in_at');
        if ($request->filled('employee')) {
            $query->where('store_user_id', $request->integer('employee'));
        }
        match ($request->input('flag')) {
            'late' => $query->where('late_minutes', '>', 0),
            'early' => $query->where('early_leave_minutes', '>', 0),
            'unapproved' => $query->whereNull('approved_at')->whereNotNull('clock_out_at'),
            'corrected' => $query->where('corrected', true),
            'exception' => $query->whereIn('clock_basis', ['cross_location', 'gm_override', 'gm_entered']),
            default => null,
        };

        $logs = $query->paginate(30)->withQueryString();
        $staff = StoreUser::where('store_id', $storeId)->orderBy('name')->get(['id', 'name', 'staff_code']);
        $todayOverrides = ClockOverride::where('store_id', $storeId)->whereDate('work_date', now($tz)->toDateString())->with('employee')->latest()->get();
        $options = StoreSetting::timeClockOptions($storeId);
        $u = Auth::user();
        $can = [
            'approve' => $u->hasPermission('approve_timesheets'),
            'gm' => $u->hasPermission('approve_time_exceptions'),
            'settings' => $u->hasPermission('manage_schedules'),
        ];

        return view('store.time-records.index', compact('logs', 'staff', 'from', 'to', 'tz', 'todayOverrides', 'options', 'can'));
    }

    public function correct(Request $request, int $log)
    {
        $this->need('approve_timesheets');
        $record = $this->ownLog($log);
        $tz = TimeClockService::tz();
        $data = $request->validate([
            'clock_in_at' => 'required|date',
            'clock_out_at' => 'nullable|date|after:clock_in_at',
            'break_minutes' => 'nullable|integer|min:0|max:600',
            'reason' => 'required|string|max:500',
        ], ['reason.required' => 'Give a reason for the correction.']);

        try {
            $this->clock->correct($record, Auth::user(), [
                'clock_in_at' => Carbon::parse($data['clock_in_at'], $tz)->utc(),
                'clock_out_at' => $data['clock_out_at'] ? Carbon::parse($data['clock_out_at'], $tz)->utc() : null,
                'break_minutes' => (int) ($data['break_minutes'] ?? 0),
            ], $data['reason']);
        } catch (TimeClockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Time record corrected; the original values are kept in its history. It needs approval again.');
    }

    public function approve(Request $request)
    {
        $this->need('approve_timesheets');
        $ids = collect($request->input('ids', []))->map(fn ($i) => (int) $i)->filter();
        $done = 0;
        $skipped = [];
        foreach (StoreTimeLog::where('store_id', Auth::user()->store_id)->whereIn('id', $ids)->with('staff')->get() as $record) {
            try {
                $this->clock->approve($record, Auth::user());
                $done++;
            } catch (TimeClockException $e) {
                $skipped[] = ($record->staff->name ?? '#' . $record->id) . ': ' . $e->getMessage();
            }
        }

        return back()->with($done ? 'success' : 'error', "{$done} record(s) approved." . ($skipped ? ' Skipped: ' . implode(' ', $skipped) : ''));
    }

    /** GM approves an unscheduled clock-in for today (the employee then clocks in at the terminal). */
    public function override(Request $request)
    {
        $this->need('approve_time_exceptions');
        $data = $request->validate(['store_user_id' => 'required|integer|exists:store_users,id', 'reason' => 'required|string|max:500']);

        try {
            $o = $this->clock->approveOverride(StoreUser::findOrFail($data['store_user_id']), Auth::user()->store_id, Auth::user(), $data['reason']);
        } catch (TimeClockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', ($o->employee->name ?? 'Employee') . ' may clock in today without a scheduled shift.');
    }

    /** GM enters the clock-in for an unscheduled employee. */
    public function gmEnter(Request $request)
    {
        $this->need('approve_time_exceptions');
        $tz = TimeClockService::tz();
        $data = $request->validate([
            'store_user_id' => 'required|integer|exists:store_users,id',
            'clock_in_at' => 'nullable|date',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $log = $this->clock->gmEnter(StoreUser::findOrFail($data['store_user_id']), Auth::user()->store_id, Auth::user(), $data['reason'],
                !empty($data['clock_in_at']) ? Carbon::parse($data['clock_in_at'], $tz)->utc() : null);
        } catch (TimeClockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', ($log->staff->name ?? 'Employee') . ' clocked in by GM entry. They can clock out at the terminal.');
    }

    public function crossLocation()
    {
        $this->need('manage_schedules', 'approve_time_exceptions');
        $storeId = Auth::user()->store_id;
        $staff = StoreUser::where('store_id', $storeId)->where('is_active', true)->orderBy('name')->get();
        $stores = StoreDetail::where('is_active', true)->where('id', '!=', $storeId)->orderBy('store_name')->get(['id', 'store_name']);
        $permissions = CrossLocationPermission::whereIn('store_user_id', $staff->pluck('id'))->with('store')->get()->groupBy('store_user_id');
        $canEdit = Auth::user()->hasPermission('manage_schedules');

        return view('store.time-records.cross-location', compact('staff', 'stores', 'permissions', 'canEdit'));
    }

    public function grantCrossLocation(Request $request)
    {
        $this->need('manage_schedules');
        $data = $request->validate(['store_user_id' => 'required|integer|exists:store_users,id', 'store_id' => 'required|integer|exists:store_details,id']);
        $employee = StoreUser::where('store_id', Auth::user()->store_id)->findOrFail($data['store_user_id']);
        if ((int) $data['store_id'] === (int) $employee->store_id) {
            return back()->with('error', 'That is already their own store.');
        }
        CrossLocationPermission::firstOrCreate(['store_user_id' => $employee->id, 'store_id' => $data['store_id']], ['granted_by_name' => Auth::user()->name]);

        return back()->with('success', "{$employee->name} may now clock in at " . StoreDetail::find($data['store_id'])->store_name . '.');
    }

    public function revokeCrossLocation(int $permission)
    {
        $this->need('manage_schedules');
        $p = CrossLocationPermission::whereIn('store_user_id', StoreUser::where('store_id', Auth::user()->store_id)->pluck('id'))->findOrFail($permission);
        $p->delete();

        return back()->with('success', 'Cross-location permission removed.');
    }

    public function updateSettings(Request $request)
    {
        $this->need('manage_schedules');
        $data = $request->validate([
            'clock_early_minutes' => 'required|integer|min:0|max:240',
            'late_grace_minutes' => 'required|integer|min:0|max:120',
            'early_leave_grace_minutes' => 'required|integer|min:0|max:120',
        ]);
        StoreSetting::updateOrCreate(['store_id' => Auth::user()->store_id], $data);

        return back()->with('success', 'Time clock settings saved.');
    }

    /** Attendance + payroll hours by employee and location (spec 10.3); CSV export. */
    public function reports(Request $request)
    {
        $this->need('approve_timesheets');
        $storeId = Auth::user()->store_id;
        $tz = TimeClockService::tz();
        [$from, $to] = $this->range($request);

        $logs = StoreTimeLog::where('store_id', $storeId)->whereBetween('clock_in_at', [$from->copy()->utc(), $to->copy()->utc()])->with('staff')->get();
        $shifts = EmployeeSchedule::where('store_id', $storeId)->where('status', 'scheduled')
            ->whereBetween('shift_date', [$from->toDateString(), $to->toDateString()])->with('employee')->get();
        $worked = $logs->pluck('employee_schedule_id')->filter()->flip();
        $store = StoreDetail::find($storeId);

        $rows = $logs->pluck('store_user_id')->merge($shifts->pluck('store_user_id'))->unique()->map(function ($uid) use ($logs, $shifts, $worked) {
            $mine = $logs->where('store_user_id', $uid);
            $myShifts = $shifts->where('store_user_id', $uid);
            $name = optional($mine->first()?->staff ?? $myShifts->first()?->employee)->name ?? ('#' . $uid);

            return [
                'name' => $name,
                'scheduled' => $myShifts->count(),
                'worked' => $mine->count(),
                'absent' => $myShifts->filter(fn ($s) => !$worked->has($s->id) && $s->endsAt()->isPast())->count(),
                'late' => $mine->where('late_minutes', '>', 0)->count(),
                'late_minutes' => (int) $mine->sum('late_minutes'),
                'early' => $mine->where('early_leave_minutes', '>', 0)->count(),
                'exceptions' => $mine->whereIn('clock_basis', ['cross_location', 'gm_override', 'gm_entered'])->count(),
                'corrected' => $mine->where('corrected', true)->count(),
                'hours' => round((float) $mine->sum('total_hours'), 2),
                'approved_hours' => round((float) $mine->whereNotNull('approved_at')->sum('total_hours'), 2),
                'open' => $mine->whereNull('clock_out_at')->count(),
            ];
        })->sortBy('name')->values();

        if ($request->input('export') === 'csv') {
            $name = 'time-report-' . ($store->store_name ?? 'store') . '-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv';

            return response()->streamDownload(function () use ($rows, $store, $from, $to) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Location', 'From', 'To', 'Employee', 'Scheduled shifts', 'Worked shifts', 'Absent', 'Late arrivals', 'Late minutes', 'Early departures', 'Exceptions (cross-location / GM)', 'Corrected', 'Hours', 'Approved hours', 'Still clocked in']);
                foreach ($rows as $r) {
                    fputcsv($out, [$store->store_name ?? '', $from->toDateString(), $to->toDateString(), $r['name'], $r['scheduled'], $r['worked'], $r['absent'], $r['late'], $r['late_minutes'], $r['early'], $r['exceptions'], $r['corrected'], $r['hours'], $r['approved_hours'], $r['open']]);
                }
                fclose($out);
            }, $name, ['Content-Type' => 'text/csv']);
        }

        return view('store.time-records.reports', compact('rows', 'from', 'to', 'store'));
    }
}
