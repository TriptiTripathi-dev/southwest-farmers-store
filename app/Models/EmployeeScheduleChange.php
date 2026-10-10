<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Schedule change history (kitchen spec 9.1: meaningful change history). */
class EmployeeScheduleChange extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['employee_schedule_id', 'action', 'changes', 'user_id', 'user_name'];

    protected $casts = ['changes' => 'array', 'created_at' => 'datetime'];
}
