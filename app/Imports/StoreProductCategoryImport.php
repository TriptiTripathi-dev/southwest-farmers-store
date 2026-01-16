<?php

namespace App\Imports;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoreProductCategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        return new ProductCategory([
            'store_id'  => $storeId, // Force Local Store ID
            'name'      => $row['name'],
            'code'      => $row['code'] ?? strtoupper(substr($row['name'], 0, 3)) . rand(100,999),
            'is_active' => true,
        ]);
    }
}