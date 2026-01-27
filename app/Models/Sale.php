<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model {
    protected $guarded = [];
    
    public function items() {
        return $this->hasMany(SaleItem::class);
    }
    public function store() {
        return $this->belongsTo(StoreDetail::class); // Assuming Store model exists
    }
}