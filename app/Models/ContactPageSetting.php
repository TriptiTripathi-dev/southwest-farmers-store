<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPageSetting extends Model
{
    protected $fillable = [
        'store_id',
        'header_badge',
        'header_title',
        'header_subtitle',
        'address_title',
        'address_content',
        'phone_title',
        'phone_content',
        'email_title',
        'email_content',
        'form_title',
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}
