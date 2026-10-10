<?php

namespace App\Http\Controllers\Store;

use App\Exceptions\TimeClockException;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSchedule;
use App\Models\StoreTimeLog;
use App\Models\StoreUser;
use App\Services\TimeClockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Staff Time Clock terminal (kitchen spec 9.2-9.4). Staff clock in/out with
 * their store ID at this store's terminal; the rules (schedule, location,
 * cross-location permission, GM override) are in TimeClockService.
 */
class StoreTimeClockController extends Controller
{
    public function __construct(private TimeClockService $clock)
    {
    }

    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $tz = TimeClockService::tz();

        $activeLogs = StoreTimeLog::where('store_id', $storeId)->whereNull('clock_out_at')->with('staff')->orderBy('clock_in_at')->get();
        $todayShifts = EmployeeSchedule::where('store_id', $storeId)->where('status', 'scheduled')
            ->whereDate('shift_date', now($tz)->toDateString())->with('employee')->orderBy('start_time')->get();
        $recentLogs = StoreTimeLog::where('store_id', $storeId)->with('staff')->latest('clock_in_at')->paginate(20);

        return view('store.time-clock.index', compact('activeLogs', 'todayShifts', 'recentLogs', 'tz'));
    }

    /** Store IDs are global, so staff with cross-location permission can use another store's terminal. */
    protected function employeeByCode(string $code): StoreUser
    {
        $employee = StoreUser::where('staff_code', trim($code))->first();
        if (!$employee) {
            throw new TimeClockException('No employee has that store ID.');
        }

        return $employee;
    }

    public function clockIn(Request $request)
    {
        $data = $request->validate(['staff_code' => 'required|string', 'notes' => 'nullable|string|max:500']);

        try {
            $employee = $this->employeeByCode($data['staff_code']);
            $log = $this->clock->clockIn($employee, Auth::user()->store_id, Auth::user(), $data['notes'] ?? null);
        } catch (TimeClockException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $msg = "{$employee->name} clocked in";
        $msg .= match ($log->clock_basis) {
            'cross_location' => ' (cross-location, permitted).',
            'gm_override' => " (unscheduled, approved by {$log->override_by_name}).",
            default => '.',
        };
        if ($log->late_minutes > 0) {
            $msg .= " Late by {$log->late_minutes} min.";
        }

        return back()->with('success', $msg);
    }

    /** Clock out by store ID (the employee at the terminal). */
    public function clockOutByCode(Request $request)
    {
        $data = $request->validate(['staff_code' => 'required|string', 'break_minutes' => 'nullable|integer|min:0|max:600']);

        try {
            $employee = $this->employeeByCode($data['staff_code']);
            $log = StoreTimeLog::where('store_user_id', $employee->id)->whereNull('clock_out_at')->first();
            if (!$log) {
                throw new TimeClockException("{$employee->name} is not clocked in.");
            }

            return $this->finish($log, (int) ($data['break_minutes'] ?? 0));
        } catch (TimeClockException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** Clock out from the list (a supervisor at this store's terminal). */
    public function clockOut(Request $request, StoreTimeLog $timeLog)
    {
        if (Auth::user()->store_id != $timeLog->store_id) {
            abort(403);
        }
        $request->validate(['break_minutes' => 'nullable|integer|min:0|max:600']);

        try {
            return $this->finish($timeLog, (int) $request->input('break_minutes', 0), $request->input('notes'));
        } catch (TimeClockException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function finish(StoreTimeLog $log, int $break, ?string $notes = null)
    {
        $log = $this->clock->clockOut($log, Auth::user(), $break, $notes);
        $msg = ($log->staff->name ?? 'Staff') . " clocked out. Hours: {$log->total_hours}.";
        if ($log->early_leave_minutes > 0) {
            $msg .= " Left {$log->early_leave_minutes} min early.";
        }

        return back()->with('success', $msg);
    }
}
