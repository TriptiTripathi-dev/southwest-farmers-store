<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StorePurchaseOrder;
use App\Models\StorePurchaseOrderItem;
use App\Models\StoreOrderSchedule;
use App\Models\StoreStock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "--- Testing Store PO System ---\n";
    $storeId = 1;
    $po = StorePurchaseOrder::create([
        'store_id' => $storeId,
        'po_number' => StorePurchaseOrder::generatePONumber($storeId),
        'status' => 'pending',
    ]);
    echo "Created PO: {$po->po_number}\n";

    $product = Product::first();
    $item = $po->items()->create([
        'product_id' => $product->id,
        'quantity' => 50,
        'unit_cost' => 100,
        'total_cost' => 5000
    ]);
    $po->calculateTotals();
    echo "PO Totals: Items={$po->total_items}, Amount={$po->total_amount}\n";

    echo "\n--- Testing Stock Levels ---\n";
    $stock = StoreStock::where('store_id', $storeId)->where('product_id', $product->id)->first();
    if ($stock) {
        $stock->update(['min_stock' => 10, 'max_stock' => 100]);
        echo "Updated Stock Levels for {$product->product_name}: Min=10, Max=100\n";
    }

    echo "\n--- Testing Order Schedule ---\n";
    $schedule = StoreOrderSchedule::updateOrCreate(
        ['store_id' => $storeId],
        ['expected_day' => 'Monday']
    );
    echo "Expected Order Day for Store {$storeId}: {$schedule->expected_day}\n";

    DB::rollBack();
    echo "\nVerification complete (Transaction rolled back).\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    DB::rollBack();
}
