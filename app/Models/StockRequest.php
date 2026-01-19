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
        'fulfilled_quantity',      // Added
        'status', 
        'admin_note',
        'store_payment_proof',     // Added
        'store_remarks',           // Added
        'warehouse_payment_proof', // Added
        'warehouse_remarks',       // Added
        'verified_at',             // Added
        'purchase_ref'             // Added
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Constants for Status
    const STATUS_PENDING = 'pending';
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    // Helper to get pending amount
    public function getPendingQuantityAttribute()
    {
        return max(0, $this->requested_quantity - ($this->fulfilled_quantity ?? 0));
    }
}