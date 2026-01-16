<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreStock extends Model
{
    protected $fillable = [
        'store_id', 'product_id', 'quantity', 'selling_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship: A stock entry belongs to a Store
    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}