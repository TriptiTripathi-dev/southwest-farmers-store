<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\StoreStock;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class StoreStockService
{
    /**
     * Deduct stock using FIFO (First-In-First-Out) logic.
     * Decrements quantity from the oldest batches first.
     */
    public function deductStockFIFO($storeId, $productId, $qtyToDeduct, $reason, $userId = null)
    {
        // 1. Get Batches sorted by Expiry (Oldest First)
        $batches = ProductBatch::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        if ($batches->sum('quantity') < $qtyToDeduct) {
            throw new \Exception("Not enough stock for Product ID: $productId");
        }

        $remaining = $qtyToDeduct;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $take = min($batch->quantity, $remaining);

            // Deduct from Batch
            $batch->decrement('quantity', $take);
            
            // Log Transaction
            StockTransaction::create([
                'store_id' => $storeId,
                'product_id' => $productId,
                'product_batch_id' => $batch->id,
                'type' => 'usage', // or 'audit_deduction'
                'quantity_change' => -$take,
                'remarks' => "FIFO Deduct: $reason",
                'ware_user_id' => $userId
            ]);

            $remaining -= $take;
        }

        // 2. Update Main Store Stock Count
        StoreStock::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->decrement('quantity', $qtyToDeduct);
    }
}