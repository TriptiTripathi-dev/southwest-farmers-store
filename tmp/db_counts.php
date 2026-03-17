<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- PRODUCT CATEGORIES --- \n";
$totalCats = DB::table('product_categories')->count();
$uniqueCatIds = DB::table('product_categories')->distinct('id')->count();
echo "Total Rows: $totalCats\n";
echo "Unique IDs: $uniqueCatIds\n";

echo "\n--- STORE STOCKS --- \n";
$totalStocks = DB::table('store_stocks')->count();
$uniqueStockIds = DB::table('store_stocks')->distinct('id')->count();
$uniqueStockProductStorePairs = DB::table('store_stocks')->select('store_id', 'product_id')->distinct()->get()->count();
echo "Total Rows: $totalStocks\n";
echo "Unique IDs: $uniqueStockIds\n";
echo "Unique Store-Product Pairs: $uniqueStockProductStorePairs\n";
