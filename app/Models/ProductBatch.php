<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'cost_price',
        'quantity',
        'is_active',
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        // Casting numeric fields helps ensure PHP handles them as numbers, not strings
        'quantity' => 'float', 
        'cost_price' => 'float',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Helper Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHasStock($query)
    {
        return $query->where('quantity', '>', 0);
    }
}