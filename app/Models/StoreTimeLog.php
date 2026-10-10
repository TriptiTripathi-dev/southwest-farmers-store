<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mirrors warehouse-pos's KitchenTimeLog pattern, for general store staff
 * clock-in/out (item 4).
 */
class StoreTimeLog extends Model
{
    protected $fillable = [
        'store_user_id',
        'store_id',
        'clock_in_at',
        'clock_out_at',
        'total_hours',
        'break_minutes',
        'status',
        'notes',
        'employee_schedule_id',
        'scheduled_start_at',
        'scheduled_end_at',
        'assigned_store_id',
        'clock_basis',
        'cross_location_permission_id',
        'override_by_name',
        'override_reason',
        'late_minutes',
        'early_leave_minutes',
        'clock_in_by_name',
        'clock_out_by_name',
        'corrected',
        'approved_by_name',
        'approved_at',
    ];

    protected $casts = [
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'scheduled_start_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'corrected' => 'boolean',
    ];

    /** How this clock-in was allowed (kitchen spec 9.2-9.4). */
    public const BASIS_LABELS = [
        'scheduled' => 'Scheduled',
        'cross_location' => 'Cross-location (permitted)',
        'gm_override' => 'Unscheduled - GM approved',
        'gm_entered' => 'Entered by GM',
        'legacy' => 'Before schedules',
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function assignedStore()
    {
        return $this->belongsTo(StoreDetail::class, 'assigned_store_id');
    }

    public function schedule()
    {
        return $this->belongsTo(EmployeeSchedule::class, 'employee_schedule_id');
    }

    public function corrections()
    {
        return $this->hasMany(TimeLogCorrection::class)->latest('created_at')->latest('id');
    }

    public function basisLabel(): string
    {
        return self::BASIS_LABELS[$this->clock_basis] ?? ucfirst((string) $this->clock_basis);
    }

    public function staff()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }
}
