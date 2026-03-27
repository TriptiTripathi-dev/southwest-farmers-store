<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StoreStock;

echo "Total Products: " . Product::count() . "\n";
echo "Active Products: " . Product::where('is_active', true)->count() . "\n";
echo "Inactive Products: " . Product::where('is_active', false)->count() . "\n";
echo "Total Categories: " . ProductCategory::count() . "\n";
echo "Store Stocks: " . StoreStock::count() . "\n";

$products = Product::limit(10)->get();
foreach ($products as $p) {
    echo "Product: {$p->product_name}, ID: {$p->id}, Active: " . ($p->is_active ? 'Yes' : 'No') . ", StoreID: " . ($p->store_id ?? 'Global') . "\n";
    $stocks = StoreStock::where('product_id', $p->id)->get();
    foreach ($stocks as $s) {
        echo "  - Stock StoreID: {$s->store_id}, Qty: {$s->quantity}\n";
    }
}
