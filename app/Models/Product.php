<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id', 
        'category_id', 
        'subcategory_id', 
        'product_name', 
        'sku', 
        'barcode',       // Added
        'unit',          // Added
        'price',         // Fixed (was base_price)
        'icon',          // Fixed (matches DB column)
        'description', 
        'is_active'
    ];

    // Helper to check if Global
    public function getIsGlobalAttribute()
    {
        return is_null($this->store_id);
    }

    // Scopes
    public function scopeGlobal($query)
    {
        return $query->whereNull('store_id');
    }

    public function scopeLocal($query)
    {
        return $query->whereNotNull('store_id');
    }

    // Relationships
    public function storeStocks()
    {
        return $this->hasMany(StoreStock::class);
    }
    
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class);
    }
}