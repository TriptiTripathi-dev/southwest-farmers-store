<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreStock extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
        'in_transit_qty',
        'status',
        'selling_price',
        'min_stock',
        'max_stock'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'in_transit_qty' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}