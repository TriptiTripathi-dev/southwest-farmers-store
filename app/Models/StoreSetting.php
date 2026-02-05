<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $table = 'store_settings';

    protected $fillable = [
        'store_id',
        'app_name',
        'app_phone',
        'support_email',
        'address',
        'logo',
        'favicon',
        'login_logo',
        'currency',       // Added
        'vat_percentage', // Added
    ];
}