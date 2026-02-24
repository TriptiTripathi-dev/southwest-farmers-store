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
        $today = now()->format('l'); // Current day name (e.g., Monday)
        
        // 1. Find all stocks that are low and have a max_stock set
        $lowStocks = StoreStock::whereColumn('quantity', '<=', 'min_stock')
            ->where('max_stock', '>', 0)
            ->with('product') // Eager load product for unit_cost
            ->get()
            ->groupBy('store_id');

        $orderCount = 0;

        foreach ($lowStocks as $storeId => $items) {
            
            // 2. Check Store Schedule
            $schedule = \App\Models\StoreOrderSchedule::where('store_id', $storeId)->first();
            if ($schedule && $schedule->expected_day !== $today) {
                // Skip if it's not the scheduled day (e.g., skip Tuesday if it's Monday)
                continue;
            }

            // 3. Check for existing PENDING or APPROVED orders for this store
            $existingPO = \App\Models\StorePurchaseOrder::where('store_id', $storeId)
                ->whereIn('status', ['pending', 'approved', 'dispatched'])
                ->exists();

            if ($existingPO) {
                // Don't create another order if one is already in progress
                continue;
            }

            // 4. Create the multi-item Purchase Order
            DB::transaction(function () use ($storeId, $items, &$orderCount) {
                $po = \App\Models\StorePurchaseOrder::create([
                    'store_id' => $storeId,
                    'po_number' => \App\Models\StorePurchaseOrder::generatePONumber($storeId),
                    'status' => 'pending',
                    'store_remarks' => 'Auto-generated based on low stock levels and ' . now()->format('l') . ' schedule.',
                ]);

                foreach ($items as $stock) {
                    // Calculate replenishment quantity considering in-transit
                    $inTransitQty = (int) \App\Models\StockRequest::where('store_id', $storeId)
                        ->where('product_id', $stock->product_id)
                        ->whereIn('status', ['pending', 'approved', 'dispatched'])
                        ->sum('requested_quantity');
                    
                    $qtyToOrder = max(0, $stock->max_stock - ($stock->quantity + $inTransitQty));

                    if ($qtyToOrder > 0) {
                        $po->items()->create([
                            'product_id' => $stock->product_id,
                            'quantity' => $qtyToOrder,
                            'unit_cost' => $stock->product->cost_price ?? 0,
                            'total_cost' => $qtyToOrder * ($stock->product->cost_price ?? 0),
                        ]);
                    }
                }

                if ($po->items()->count() > 0) {
                    $po->calculateTotals();
                    $orderCount++;
                } else {
                    $po->delete(); // Nothing to order for this store
                }
            });
        }

        $this->info("Successfully generated $orderCount Purchase Orders.");
        Log::info("AutoRestock Run: Generated $orderCount POs for " . $today);
    }
}
