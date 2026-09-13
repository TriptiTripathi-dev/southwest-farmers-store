<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StorePurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_po_id',
        'product_id',
        'requested_qty',
        'dispatched_qty',
        'pending_qty',
        'status',
        'rejection_reason',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(StorePurchaseOrder::class, 'store_po_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
