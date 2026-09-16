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
    ];

    protected $casts = [
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'total_hours' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }
}
