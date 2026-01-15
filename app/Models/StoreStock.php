<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStock extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'product_id', 'quantity'];

    // Relationship: A stock entry belongs to a Product
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