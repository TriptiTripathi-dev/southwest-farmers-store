<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id', 
        'department_id', // <--- YE ADD KIYA HAI
        'category_id', 
        'subcategory_id', 
        'product_name', 
        'sku', 
        'barcode',
        'unit',
        'price',
        'icon',
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
    
    // <--- YE RELATIONSHIP ADD KI HAI
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

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


    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function ingredients()
    {
        return $this->hasMany(ProductIngredient::class, 'product_id');
    }

    public function usedInRecipes()
    {
        return $this->hasMany(ProductIngredient::class, 'ingredient_id');
    }
}