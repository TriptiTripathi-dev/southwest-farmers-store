<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StoreStock;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\TracksImportProgress;

class StoreProductImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts, WithEvents
{
    use TracksImportProgress;

    protected $departmentId;
    protected $categoryId;
    protected $subcategoryId;
    protected $authUserId;
    protected $storeId;

    public function __construct($categoryId, $subcategoryId, $departmentId, $authUserId = null, $importTaskId = null)
    {
        $this->categoryId = $categoryId;
        $this->subcategoryId = $subcategoryId;
        $this->departmentId = $departmentId;
        $this->authUserId = $authUserId ?? Auth::id();
        $user = Auth::user();
        $this->storeId = $user->store_id ?? $user->id;
        $this->importTaskId = $importTaskId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['product_name'])) continue;

            DB::transaction(function () use ($row) {
                $product = Product::create([
                    'store_id'       => $this->storeId,
                    'department_id'  => $this->departmentId,
                    'category_id'    => $this->categoryId,
                    'subcategory_id' => $this->subcategoryId,
                    'product_name'   => $row['product_name'],
                    'sku'            => $row['sku'] ?? null,
                    'barcode'        => $row['barcode'] ?? null,
                    'upc'            => $row['upc'] ?? null,
                    'unit'           => $row['unit'] ?? 'pcs',
                    'price'          => (float)($row['selling_price'] ?? 0),
                    'cost_price'     => (float)($row['cost_price'] ?? 0),
                    'is_active'      => true,
                ]);

                StoreStock::create([
                    'store_id'      => $this->storeId,
                    'product_id'    => $product->id,
                    'quantity'      => (float)($row['stock_quantity'] ?? 0),
                    'selling_price' => (float)($row['selling_price'] ?? 0),
                ]);
            });
        }

        $this->updateImportProgress($rows->count());
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function registerEvents(): array
    {
        return $this->getImportProgressEvents();
    }
}