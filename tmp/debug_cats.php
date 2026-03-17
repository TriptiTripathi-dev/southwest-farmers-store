<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

$names = ['Cosmetics', 'Grocery', 'PRODUCE', 'MEAT', 'BEVERAGES'];

foreach ($names as $name) {
    echo "--- Category: $name ---\n";
    $cats = DB::table('product_categories')->where('name', $name)->get();
    foreach ($cats as $cat) {
        echo "ID: {$cat->id} | Name: {$cat->name} | Created: {$cat->created_at}\n";
    }
    echo "\n";
}
