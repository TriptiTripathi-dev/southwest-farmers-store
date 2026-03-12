<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickPosSetting extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'printer_enabled',
        'scanner_enabled',
        'scale_enabled',
        'cash_drawer_enabled',
        'auto_print_receipt'
    ];

    protected $casts = [
        'printer_enabled' => 'boolean',
        'scanner_enabled' => 'boolean',
        'scale_enabled' => 'boolean',
        'cash_drawer_enabled' => 'boolean',
        'auto_print_receipt' => 'boolean',
    ];
}
