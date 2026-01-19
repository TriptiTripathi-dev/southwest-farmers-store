<?php

namespace App\Imports;

use App\Models\StockRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockRequestImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Find Product by SKU
        $product = Product::where('sku', $row['sku'])->first();

        // Only create request if product exists and quantity is valid
        if ($product && isset($row['quantity']) && $row['quantity'] > 0) {
            return new StockRequest([
                'store_id'           => $storeId,
                'product_id'         => $product->id,
                'requested_quantity' => $row['quantity'],
                'status'             => 'pending',
            ]);
        }

        return null;
    }
}