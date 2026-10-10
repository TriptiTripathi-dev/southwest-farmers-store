<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSchedule;
use App\Models\StoreUser;
use App\Services\TimeClockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Employee schedules for the current store (kitchen spec 9.1): a week grid,
 * add / edit / cancel shifts, change history. Changing schedules needs
 * 'manage_schedules'.
 */
class StoreScheduleController extends Controller
{
    public function __construct(private TimeClockService $clock)
    {
    }

    protected function canManage(): bool
    {
        return (bool) Auth::user()?->hasPermission('manage_schedules');
    }

    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $tz = TimeClockService::tz();
        $weekStart = Carbon::parse($request->input('week', now($tz)->toDateString()), $tz)->startOfWeek(Carbon::MONDAY);
        $days = collect(range(0, 6))->map(fn ($i) => $weekStart->copy()->addDays($i));

        $shifts = EmployeeSchedule::where('store_id', $storeId)
            ->whereBetween('shift_date', [$days->first()->toDateString(), $days->last()->toDateString()])
            ->with('employee', 'changes')->orderBy('start_time')->get();

        $staff = StoreUser::where('store_id', $storeId)->where('is_active', true)->orderBy('name')->get()
            ->merge(StoreUser::whereIn('id', $shifts->pluck('store_user_id'))->get())->unique('id')->sortBy('name')->values();
        $grid = $shifts->groupBy(fn ($s) => $s->store_user_id . '|' . $s->shift_date->toDateString());
        $canManage = $this->canManage();

        return view('store.schedules.index', compact('days', 'staff', 'grid', 'weekStart', 'canManage', 'tz'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'store_user_id' => 'required|integer|exists:store_users,id',
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|different:start_time',
            'role' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'integer|between:0,6',
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->canManage(), 403, 'You do not have permission to change schedules.');
        $data = $this->validated($request);
        $storeId = Auth::user()->store_id;
        $tz = TimeClockService::tz();

        // One shift, or the same shift on several days of that week.
        $date = Carbon::parse($data['shift_date'], $tz);
        $dates = collect($data['repeat_days'] ?? [])->isNotEmpty()
            ? collect($data['repeat_days'])->map(fn ($d) => $date->copy()->startOfWeek(Carbon::MONDAY)->addDays((int) $d))
            : collect([$date]);

        foreach ($dates as $d) {
            $this->clock->saveSchedule([
                'store_id' => $storeId,
                'store_user_id' => (int) $data['store_user_id'],
                'shift_date' => $d->toDateString(),
                'start_time' => $data['start_time'] . ':00',
                'end_time' => $data['end_time'] . ':00',
                'role' => $data['role'] ?? null,
                'notes' => $data['notes'] ?? null,
            ], Auth::user());
        }

        return redirect()->route('store.schedules.index', ['week' => $date->toDateString()])
            ->with('success', $dates->count() === 1 ? 'Shift added.' : $dates->count() . ' shifts added.');
    }

    public function update(Request $request, int $schedule)
    {
        abort_unless($this->canManage(), 403, 'You do not have permission to change schedules.');
        $shift = EmployeeSchedule::where('store_id', Auth::user()->store_id)->findOrFail($schedule);
        $data = $this->validated($request);

        $this->clock->saveSchedule([
            'store_user_id' => (int) $data['store_user_id'],
            'shift_date' => $data['shift_date'],
            'start_time' => $data['start_time'] . ':00',
            'end_time' => $data['end_time'] . ':00',
            'role' => $data['role'] ?? null,
            'notes' => $data['notes'] ?? null,
        ], Auth::user(), $shift);

        return back()->with('success', 'Shift updated.');
    }

    public function cancel(int $schedule)
    {
        abort_unless($this->canManage(), 403, 'You do not have permission to change schedules.');
        $shift = EmployeeSchedule::where('store_id', Auth::user()->store_id)->findOrFail($schedule);
        $this->clock->cancelSchedule($shift, Auth::user());

        return back()->with('success', 'Shift cancelled.');
    }
}
