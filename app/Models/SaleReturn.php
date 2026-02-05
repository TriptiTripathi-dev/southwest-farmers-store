<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model {
    protected $guarded = [];

    public function sale() { return $this->belongsTo(Sale::class); }
    public function items() { return $this->hasMany(SaleReturnItem::class); }
    public function customer() { return $this->belongsTo(StoreCustomer::class); }
    public function user() { return $this->belongsTo(StoreUser::class, 'created_by'); }
}
