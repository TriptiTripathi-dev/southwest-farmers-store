<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\KitchenLocation;
use App\Models\KitchenShift;
use App\Models\KitchenTimeLog;
use App\Models\StoreUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreKitchenStaffScheduleController extends Controller
{
    protected function locationForCurrentStore(): KitchenLocation
    {
        $storeId = Auth::user()->store_id;

        return KitchenLocation::firstOrCreate(
            ['store_id' => $storeId, 'type' => 'store'],
            ['name' => 'Store Kitchen']
        );
    }

    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        $shifts = KitchenShift::with(['staff', 'kitchenLocation'])
            ->where('kitchen_location_id', $location->id)
            ->whereDate('shift_date', $selectedDate)
            ->orderBy('start_time')
            ->get();

        $timeLogs = KitchenTimeLog::with(['staff', 'kitchenLocation'])
            ->where('kitchen_location_id', $location->id)
            ->whereDate('clock_in_at', $selectedDate)
            ->orderByDesc('clock_in_at')
            ->get();

        $activeClockIns = KitchenTimeLog::with(['staff', 'kitchenLocation'])
            ->where('kitchen_location_id', $location->id)
            ->whereNull('clock_out_at')
            ->get();

        $staffMembers = StoreUser::where('store_id', $storeId)->orderBy('name')->get();

        $stats = [
            'total_scheduled_today' => KitchenShift::where('kitchen_location_id', $location->id)->whereDate('shift_date', $selectedDate)->count(),
            'currently_clocked_in' => $activeClockIns->count(),
            'completed_shifts_today' => KitchenShift::where('kitchen_location_id', $location->id)->whereDate('shift_date', $selectedDate)->where('status', 'completed')->count(),
            'total_hours_today' => KitchenTimeLog::where('kitchen_location_id', $location->id)->whereDate('clock_in_at', $selectedDate)->sum('total_hours'),
        ];

        return view('store.kitchen.staff.index', compact(
            'shifts',
            'timeLogs',
            'activeClockIns',
            'staffMembers',
            'selectedDate',
            'stats'
        ));
    }

    public function storeShift(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();

        $validated = $request->validate([
            'ware_user_id' => 'required|exists:store_users,id',
            'shift_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'station' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        StoreUser::where('id', $validated['ware_user_id'])->where('store_id', $storeId)->firstOrFail();

        KitchenShift::create($validated + ['kitchen_location_id' => $location->id]);

        return redirect()->back()->with('success', 'Shift scheduled successfully.');
    }

    public function updateShift(Request $request, KitchenShift $shift)
    {
        if ($shift->kitchen_location_id != $this->locationForCurrentStore()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,absent,cancelled',
            'station' => 'nullable|string|max:100',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $shift->update($validated);

        return redirect()->back()->with('success', 'Shift updated successfully.');
    }

    public function destroyShift(KitchenShift $shift)
    {
        if ($shift->kitchen_location_id != $this->locationForCurrentStore()->id) {
            abort(403);
        }

        $shift->delete();

        return redirect()->back()->with('success', 'Shift removed.');
    }

    public function clockIn(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();

        $validated = $request->validate([
            'ware_user_id' => 'required|exists:store_users,id',
            'notes' => 'nullable|string',
        ]);

        StoreUser::where('id', $validated['ware_user_id'])->where('store_id', $storeId)->firstOrFail();

        $existing = KitchenTimeLog::where('ware_user_id', $validated['ware_user_id'])
            ->where('kitchen_location_id', $location->id)
            ->whereNull('clock_out_at')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'This staff member is already clocked in.');
        }

        KitchenTimeLog::create([
            'ware_user_id' => $validated['ware_user_id'],
            'kitchen_location_id' => $location->id,
            'clock_in_at' => Carbon::now(),
            'status' => 'clocked_in',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Staff clocked in successfully.');
    }

    public function clockOut(Request $request, KitchenTimeLog $timeLog)
    {
        if ($timeLog->kitchen_location_id != $this->locationForCurrentStore()->id) {
            abort(403);
        }

        $now = Carbon::now();
        $clockIn = Carbon::parse($timeLog->clock_in_at);
        $diffMinutes = max(0, $clockIn->diffInMinutes($now) - ($request->input('break_minutes', 0)));
        $totalHours = round($diffMinutes / 60, 2);

        $timeLog->update([
            'clock_out_at' => $now,
            'break_minutes' => $request->input('break_minutes', 0),
            'total_hours' => $totalHours,
            'status' => 'clocked_out',
            'notes' => $request->input('notes', $timeLog->notes),
        ]);

        return redirect()->back()->with('success', "Clocked out successfully. Total recorded hours: {$totalHours}h.");
    }
}
