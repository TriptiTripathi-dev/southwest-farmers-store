<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAttachment extends Model
{
    protected $guarded = [];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function message()
    {
        return $this->belongsTo(SupportMessage::class);
    }
}