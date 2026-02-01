<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportStatusLog extends Model
{
    protected $guarded = [];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function changedBy()
    {
        return $this->morphTo();
    }
}