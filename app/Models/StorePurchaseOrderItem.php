<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StorePurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_purchase_order_id',
        'product_id',
        'quantity',
        'dispatched_quantity',
        'received_quantity',
        'unit_cost',
        'total_cost'
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total_cost = $item->quantity * $item->unit_cost;
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(StorePurchaseOrder::class, 'store_purchase_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
