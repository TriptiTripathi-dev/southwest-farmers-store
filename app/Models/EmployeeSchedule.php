<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/** One scheduled shift (kitchen spec 9.1). Date/times are store local time. */
class EmployeeSchedule extends Model
{
    protected $fillable = [
        'store_id', 'store_user_id', 'shift_date', 'start_time', 'end_time', 'role', 'status', 'notes',
        'created_by_name', 'updated_by_name',
    ];

    protected $casts = ['shift_date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function changes()
    {
        return $this->hasMany(EmployeeScheduleChange::class)->latest('created_at')->latest('id');
    }

    /** Shift start in UTC (stored as local date + time). */
    public function startsAt(): Carbon
    {
        return Carbon::parse($this->shift_date->toDateString() . ' ' . $this->start_time, config('app.display_timezone', 'America/Chicago'))->utc();
    }

    /** Shift end in UTC; an end at/before the start means it ends the next day. */
    public function endsAt(): Carbon
    {
        $end = Carbon::parse($this->shift_date->toDateString() . ' ' . $this->end_time, config('app.display_timezone', 'America/Chicago'))->utc();

        return $end->lessThanOrEqualTo($this->startsAt()) ? $end->addDay() : $end;
    }

    public function label(): string
    {
        return substr($this->start_time, 0, 5) . '–' . substr($this->end_time, 0, 5) . ($this->role ? " ({$this->role})" : '');
    }
}
