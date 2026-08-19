<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model {
    protected $guarded = [];
    
    public function items() {
        return $this->hasMany(SaleItem::class);
    }

    public function store() {
        return $this->belongsTo(StoreDetail::class);
    }

    // NEW: Add this relationship
    public function customer() {
        return $this->belongsTo(StoreCustomer::class);
    }
    public function getInventorySourceBadgeAttribute()
    {
        if ($this->inventory_source === 'old') {
            return '<span class="badge bg-secondary">🪙 Old Stock</span>';
        } elseif ($this->inventory_source === 'new') {
            return '<span class="badge bg-success">🌟 New Stock</span>';
        } elseif ($this->inventory_source === 'mixed') {
            return '<span class="badge bg-warning text-dark">🌗 Mixed</span>';
        }
        return '<span class="badge bg-light text-dark">Unknown</span>';
    }
}