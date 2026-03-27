<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Support\Str;

class TestProductSeeder extends Seeder
{
    public function run(): void
    {
        $department = \App\Models\Department::firstOrCreate(['name' => 'Grocery'], [
            'code' => 'GRC',
            'is_active' => true
        ]);

        $category = ProductCategory::firstOrCreate(['name' => 'Vegetables'], [
            'code' => 'VEG',
            'is_active' => true
        ]);

        $subcategory = ProductSubcategory::firstOrCreate(['name' => 'Leafy Greens', 'category_id' => $category->id], [
            'code' => 'LFY',
            'is_active' => true
        ]);

        $product1 = Product::create([
            'product_name' => 'Fresh Spinach',
            'department_id' => $department->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 20,
            'sku' => 'SPIN001',
            'description' => 'Fresh Spinach picked daily.',
            'is_active' => true,
            'unit_type' => 'units',
            'store_id' => 1,
        ]);

        \App\Models\StoreStock::create([
            'store_id' => 1,
            'product_id' => $product1->id,
            'quantity' => 100,
            'selling_price' => 20
        ]);

        $product2 = Product::create([
            'product_name' => 'Carrots',
            'department_id' => $department->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 30,
            'sku' => 'CARR001',
            'description' => 'Crispy and sweet carrots.',
            'is_active' => true,
            'unit_type' => 'units',
            'store_id' => 1,
        ]);

        \App\Models\StoreStock::create([
            'store_id' => 1,
            'product_id' => $product2->id,
            'quantity' => 200,
            'selling_price' => 30
        ]);
        
        echo "Test products seeded!\n";
    }
}
