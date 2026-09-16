<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreTimeLog;
use App\Models\StoreUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Item 4: staff clock in/out, using each employee's store ID (staff_code).
 * Mirrors the Warehouse app's KitchenStaffScheduleController pattern.
 */
class StoreTimeClockController extends Controller
{
    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;

        $staff = StoreUser::where('store_id', $storeId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeLogs = StoreTimeLog::where('store_id', $storeId)
            ->whereNull('clock_out_at')
            ->pluck('store_user_id')
            ->flip();

        $recentLogs = StoreTimeLog::where('store_id', $storeId)
            ->with('staff')
            ->latest('clock_in_at')
            ->paginate(20);

        return view('store.time-clock.index', compact('staff', 'activeLogs', 'recentLogs'));
    }

    public function clockIn(Request $request)
    {
        $validated = $request->validate([
            'staff_code' => 'required|string|exists:store_users,staff_code',
            'notes' => 'nullable|string',
        ]);

        $storeId = Auth::user()->store_id;

        $employee = StoreUser::where('staff_code', $validated['staff_code'])
            ->where('store_id', $storeId)
            ->first();

        if (!$employee) {
            return back()->with('error', 'That store ID does not belong to an employee at this store.');
        }

        $existing = StoreTimeLog::where('store_user_id', $employee->id)
            ->whereNull('clock_out_at')
            ->first();

        if ($existing) {
            return back()->with('error', "{$employee->name} is already clocked in.");
        }

        StoreTimeLog::create([
            'store_user_id' => $employee->id,
            'store_id' => $storeId,
            'clock_in_at' => Carbon::now(),
            'status' => 'clocked_in',
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', "{$employee->name} clocked in successfully.");
    }

    public function clockOut(Request $request, StoreTimeLog $timeLog)
    {
        if (Auth::user()->store_id != $timeLog->store_id) {
            abort(403);
        }

        if ($timeLog->clock_out_at) {
            return back()->with('error', 'This time log is already clocked out.');
        }

        $now = Carbon::now();
        $clockIn = Carbon::parse($timeLog->clock_in_at);
        $breakMinutes = (int) $request->input('break_minutes', 0);
        $diffMinutes = max(0, $clockIn->diffInMinutes($now) - $breakMinutes);
        $totalHours = round($diffMinutes / 60, 2);

        $timeLog->update([
            'clock_out_at' => $now,
            'break_minutes' => $breakMinutes,
            'total_hours' => $totalHours,
            'status' => 'clocked_out',
            'notes' => $request->input('notes', $timeLog->notes),
        ]);

        return back()->with('success', "Clocked out. Total recorded hours: {$totalHours}h.");
    }
}
