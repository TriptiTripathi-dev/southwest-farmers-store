<?php

namespace App\Imports;

use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Traits\TracksImportProgress;

class StoreProductCategoryImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts, WithEvents
{
    use TracksImportProgress;

    protected $authUserId;
    protected $storeId;

    public function __construct($authUserId = null, $importTaskId = null)
    {
        $this->authUserId = $authUserId ?? Auth::id();
        $user = Auth::user();
        $this->storeId = $user->store_id ?? $user->id;
        $this->importTaskId = $importTaskId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['name'])) continue;

            ProductCategory::create([
                'store_id'  => $this->storeId,
                'name'      => $row['name'],
                'code'      => $row['code'] ?? strtoupper(substr($row['name'], 0, 3)) . rand(100,999),
                'is_active' => 1,
            ]);
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