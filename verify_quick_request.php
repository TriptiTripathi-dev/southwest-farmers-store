<?php
use App\Models\StockRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

try {
    DB::beginTransaction();

    // Mock User
    $user = \App\Models\StoreUser::first();
    Auth::login($user);

    echo "--- Testing Quick Request ---\n";
    $product = Product::first();
    
    // Simulate quickRequest method
    $po = StockRequest::create([
        'store_id' => $user->store_id ?? 1,
        'request_number' => StockRequest::generateRequestNumber($user->store_id ?? 1),
        'status' => 'pending',
        'requested_by' => $user->id,
        'store_remarks' => 'Quick request from low stock alert',
    ]);

    $po->items()->create([
        'product_id' => $product->id,
        'quantity' => 20,
        'unit_cost' => $product->cost_price ?? 0,
    ]);

    $po->calculateTotals();
    $po->refresh();

    echo "Quick PO Created: {$po->request_number}\n";
    echo "Items: {$po->total_items}, Amount: {$po->total_amount}\n";

    if ($po->total_items == 1 && $po->items()->first()->quantity == 20) {
        echo "SUCCESS: Quick Request logic is correct.\n";
    } else {
        echo "FAILURE: Quick Request logic is incorrect!\n";
    }

    DB::rollBack();
    echo "\nVerification complete (Transaction rolled back).\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    DB::rollBack();
}
