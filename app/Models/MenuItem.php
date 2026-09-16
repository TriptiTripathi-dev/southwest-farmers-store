<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'menu_category_id',
        'name',
        'description',
        'price',
        'image',
        'is_active',
        'is_pre_cooked',
        'daily_target_quantity',
        'is_catering_only',
        'advance_notice_days',
        'rush_fee_percentage',
        'available_days',
        'is_available_today',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'is_pre_cooked' => 'boolean',
        'is_catering_only' => 'boolean',
        'is_available_today' => 'boolean',
        'rush_fee_percentage' => 'float',
        'available_days' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }
}
