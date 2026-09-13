<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\KitchenLocation;
use App\Models\KitchenStock;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StoreStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Item 7: Kitchen Inventory screen + store-to-kitchen transfer.
 * Each store gets its own KitchenLocation (auto-created on first visit),
 * separate from the Warehouse app's central kitchen locations.
 */
class KitchenInventoryController extends Controller
{
    protected function locationForCurrentStore(): KitchenLocation
    {
        $storeId = Auth::user()->store_id;

        return KitchenLocation::firstOrCreate(
            ['store_id' => $storeId, 'type' => 'store'],
            ['name' => 'Store Kitchen']
        );
    }

    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();

        $kitchenStocks = KitchenStock::where('kitchen_location_id', $location->id)
            ->where('item_type', 'product')
            ->with('product')
            ->get();

        // Only products with stock on hand at this store are eligible to transfer.
        $storeStocks = StoreStock::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->get();

        return view('store.kitchen-inventory.index', compact('kitchenStocks', 'storeStocks'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $storeId = Auth::user()->store_id;

        try {
            DB::transaction(function () use ($request, $storeId) {
                $storeStock = StoreStock::where('store_id', $storeId)
                    ->where('product_id', $request->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$storeStock || $storeStock->quantity < $request->quantity) {
                    throw new \Exception('Not enough stock at this store to transfer that quantity.');
                }

                $storeStock->quantity -= $request->quantity;
                $storeStock->save();

                $location = $this->locationForCurrentStore();

                $kitchenStock = KitchenStock::firstOrCreate(
                    ['kitchen_location_id' => $location->id, 'item_type' => 'product', 'item_id' => $request->product_id],
                    ['quantity' => 0, 'unit' => $storeStock->product->unit ?? null]
                );
                $kitchenStock->quantity += $request->quantity;
                $kitchenStock->save();

                StockTransaction::create([
                    'store_id' => $storeId,
                    'product_id' => $request->product_id,
                    'type' => 'kitchen_transfer_out',
                    'quantity_change' => -$request->quantity,
                    'running_balance' => $storeStock->quantity,
                    'ware_user_id' => Auth::id(),
                    'remarks' => 'Transferred from store shelf to kitchen',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Stock transferred to kitchen successfully.');
    }
}
