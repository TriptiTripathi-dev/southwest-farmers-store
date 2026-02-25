<?php
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "--- Testing PO Creation ---\n";
    $po = StockRequest::create([
        'store_id' => 1,
        'request_number' => 'VERIFY-' . time(),
        'status' => 'pending',
        'requested_by' => 1
    ]);

    echo "Created PO: {$po->request_number}\n";

    $product1 = Product::first();
    $product2 = Product::skip(1)->first();

    $item1 = $po->items()->create([
        'product_id' => $product1->id,
        'quantity' => 10,
        'unit_cost' => 100
    ]);

    $item2 = $po->items()->create([
        'product_id' => $product2->id,
        'quantity' => 5,
        'unit_cost' => 200
    ]);

    echo "Added items: {$product1->product_name} x 10, {$product2->product_name} x 5\n";

    $po->calculateTotals();
    $po->refresh();

    echo "PO Totals: Items={$po->total_items}, Amount={$po->total_amount}\n";

    if ($po->total_items == 2 && $po->total_amount == 2000) {
        echo "SUCCESS: PO Totals are correct.\n";
    } else {
        echo "FAILURE: PO Totals are incorrect! Expected 2 items, 2000 amount. Got {$po->total_items}, {$po->total_amount}\n";
    }

    echo "\n--- Testing Receiving Logic ---\n";
    $po->update(['status' => 'dispatched']);
    
    // Simulate confirmReceived logic from controller
    $items_to_receive = [
        ['id' => $item1->id, 'received_quantity' => 9],
        ['id' => $item2->id, 'received_quantity' => 5]
    ];

    foreach ($items_to_receive as $receivedData) {
        $item = $po->items()->find($receivedData['id']);
        $item->update(['received_quantity' => $receivedData['received_quantity']]);
        
        $stock = \App\Models\StoreStock::firstOrCreate([
            'store_id' => $po->store_id,
            'product_id' => $item->product_id
        ]);
        $oldQty = $stock->quantity;
        $stock->increment('quantity', $receivedData['received_quantity']);
        echo "Updated stock for {$item->product->product_name}: {$oldQty} -> {$stock->refresh()->quantity}\n";
    }

    $po->update(['status' => 'completed']);
    echo "PO Status: {$po->status}\n";

    DB::rollBack();
    echo "\nVerification complete (Transaction rolled back).\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    DB::rollBack();
}
