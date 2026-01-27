<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model {
    protected $guarded = [];
    
    public function product() { return $this->belongsTo(Product::class); }
    public function fromStore() { return $this->belongsTo(  StoreDetail::class, 'from_store_id'); }
    public function toStore() { return $this->belongsTo(StoreDetail::class, 'to_store_id'); }
}