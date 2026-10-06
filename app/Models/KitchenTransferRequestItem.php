<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenTransferRequestItem extends Model
{
    protected $fillable = ['kitchen_transfer_request_id', 'product_id', 'quantity'];

    protected $casts = ['quantity' => 'decimal:2'];

    public function request()
    {
        return $this->belongsTo(KitchenTransferRequest::class, 'kitchen_transfer_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
