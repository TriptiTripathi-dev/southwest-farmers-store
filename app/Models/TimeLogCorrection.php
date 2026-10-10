<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A correction to a time record; the original value is kept (kitchen spec 10.2). */
class TimeLogCorrection extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['store_time_log_id', 'field', 'old_value', 'new_value', 'reason', 'user_id', 'user_name'];

    protected $casts = ['created_at' => 'datetime'];
}
