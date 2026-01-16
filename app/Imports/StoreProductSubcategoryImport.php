<?php

namespace App\Imports;

use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoreProductSubcategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Find the parent category by code (must be visible to this store)
        $category = ProductCategory::where('code', $row['category_code'])
            ->where(function($q) use ($storeId) {
                $q->whereNull('store_id')->orWhere('store_id', $storeId);
            })->first();

        if (!$category) {
            return null; // Skip if category not found
        }

        return new ProductSubcategory([
            'store_id'    => $storeId,
            'category_id' => $category->id,
            'name'        => $row['name'],
            'code'        => $row['code'] ?? strtoupper(substr($row['name'], 0, 3)) . rand(100,999),
            'is_active'   => true,
        ]);
    }
}