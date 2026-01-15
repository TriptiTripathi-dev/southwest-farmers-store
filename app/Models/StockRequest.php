<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 
        'product_id', 
        'requested_quantity', 
        'status', 
        'admin_note'
    ];

    // Relationship: The product being requested
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship: The store making the request
    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}