<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Columns in product_categories:\n";
print_r(Schema::getColumnListing('product_categories'));

echo "\nColumns in store_stocks:\n";
print_r(Schema::getColumnListing('store_stocks'));
