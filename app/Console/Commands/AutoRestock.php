<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreStock;
use App\Models\StockRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoRestock extends Command
{
    protected $signature = 'stock:auto-request';
    protected $description = 'Check low stock and generate stock requests based on Max Stock levels';

    public function handle()
    {
        // 1. Find stocks where quantity is below or equal to min_stock
        // AND where max_stock is set (greater than 0)
        $lowStocks = StoreStock::whereColumn('quantity', '<=', 'min_stock')
            ->where('max_stock', '>', 0)
            ->get();

        $count = 0;

        foreach ($lowStocks as $stock) {
            
            // 2. Check if there is already a PENDING request for this product/store
            // We don't want to create duplicate requests if the admin hasn't approved the previous one yet.
            $existingRequest = StockRequest::where('store_id', $stock->store_id)
                ->where('product_id', $stock->product_id)
                ->whereIn('status', ['pending', 'dispatched']) // Don't order if one is on the way
                ->exists();

            if ($existingRequest) {
                continue;
            }

            // 3. Calculate Order Quantity
            // Replenish up to max stock considering what is already in transit.
            $inTransitQty = (int) StockRequest::where('store_id', $stock->store_id)
                ->where('product_id', $stock->product_id)
                ->where('status', 'dispatched')
                ->sum(DB::raw('COALESCE(fulfilled_quantity, requested_quantity)'));
            $qtyToOrder = max(0, $stock->max_stock - ($stock->quantity + $inTransitQty));

            if ($qtyToOrder > 0) {
                StockRequest::create([
                    'store_id' => $stock->store_id,
                    'product_id' => $stock->product_id,
                    'requested_quantity' => $qtyToOrder,
                    'status' => 'pending',
                    'store_remarks' => 'Auto-generated: Low Stock Alert',
                ]);
                
                $count++;
            }
        }

        $this->info("Successfully generated $count auto-stock requests.");
        Log::info("AutoRestock Run: Generated $count requests.");
    }
}
