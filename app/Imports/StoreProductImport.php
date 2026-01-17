<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StoreStock;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoreProductImport implements ToModel, WithHeadingRow
{
    protected $categoryId;
    protected $subcategoryId;
    protected $storeId;

    public function __construct($categoryId, $subcategoryId)
    {
        $this->categoryId = $categoryId;
        $this->subcategoryId = $subcategoryId;
        $this->storeId = Auth::user()->store_id ?? Auth::user()->id;
    }

    public function model(array $row)
    {
        // 1. Create Local Product
        $product = Product::create([
            'store_id'       => $this->storeId,
            'category_id'    => $this->categoryId,
            'subcategory_id' => $this->subcategoryId,
            'product_name'   => $row['product_name'],
            'sku'            => $row['sku'],
            'barcode'        => $row['barcode'] ?? null,
            'unit'           => $row['unit'] ?? 'pcs',
            'price'          => $row['selling_price'], // Base price
            'is_active'      => true,
        ]);

        // 2. Create Stock Entry
        StoreStock::create([
            'store_id'      => $this->storeId,
            'product_id'    => $product->id,
            'quantity'      => $row['stock_quantity'] ?? 0,
            'selling_price' => $row['selling_price'],
        ]);

        return $product;
    }
}