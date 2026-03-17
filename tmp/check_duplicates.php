<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductCategory;
use App\Models\StoreStock;
use Illuminate\Support\Facades\DB;

echo "--- CATEGORY DUPLICATES ---\n";
$catDuplicates = DB::table('product_categories')
    ->select('name', DB::raw('COUNT(*) as count'))
    ->groupBy('name')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($catDuplicates->isEmpty()) {
    echo "No duplicate categories found by name.\n";
} else {
    foreach ($catDuplicates as $cat) {
        echo "Category: {$cat->name} | Count: {$cat->count}\n";
    }
}

echo "\n--- STORE STOCK DUPLICATES ---\n";
$stockDuplicates = DB::table('store_stocks')
    ->select('store_id', 'product_id', DB::raw('COUNT(*) as count'))
    ->groupBy('store_id', 'product_id')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($stockDuplicates->isEmpty()) {
    echo "No duplicate store stock records found.\n";
} else {
    foreach ($stockDuplicates as $stock) {
        echo "StoreID: {$stock->store_id} | ProductID: {$stock->product_id} | Count: {$stock->count}\n";
    }
}
