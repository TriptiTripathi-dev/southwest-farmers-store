<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** GM approval for an unscheduled clock-in on one day at one store (kitchen spec 9.4). */
class ClockOverride extends Model
{
    protected $fillable = ['store_user_id', 'store_id', 'work_date', 'reason', 'approved_by', 'approved_by_name', 'used_time_log_id'];

    protected $casts = ['work_date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }
}
