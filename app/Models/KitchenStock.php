<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenStock extends Model
{
    protected $fillable = [
        'kitchen_location_id',
        'item_type',
        'item_id',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function location()
    {
        return $this->belongsTo(KitchenLocation::class, 'kitchen_location_id');
    }

    // item_type is currently always 'product' from the Store side transfer flow.
    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
}
