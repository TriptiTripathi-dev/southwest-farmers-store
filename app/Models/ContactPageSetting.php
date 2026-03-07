<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPageSetting extends Model
{
    protected $fillable = [
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
}
