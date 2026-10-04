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
        'min_quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
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

    public function transactions()
    {
        return $this->hasMany(KitchenStockTransaction::class, 'kitchen_stock_id');
    }

    /** Available = On Hand - Reserved (never below 0). */
    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity - (float) $this->reserved_quantity);
    }

    /** Out of Stock / Below Minimum / Available. */
    public function getStockStatusAttribute(): string
    {
        if ((float) $this->quantity <= 0) {
            return 'Out of Stock';
        }
        if ((float) $this->min_quantity > 0 && $this->available_quantity < (float) $this->min_quantity) {
            return 'Below Minimum';
        }

        return 'Available';
    }
}
