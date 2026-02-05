<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreStock extends Model
{
    protected $fillable = [
        'store_id', 
        'product_id', 
        'quantity', 
        'selling_price',
        'min_stock', // Added
        'max_stock'  // Added
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