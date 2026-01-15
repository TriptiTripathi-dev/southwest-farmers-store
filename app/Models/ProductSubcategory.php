<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
    protected $table = 'product_subcategories';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}