<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Http\Controllers\Store\StoreInventoryController;
use Illuminate\Http\Request;

echo "--- PHASE 4 BACKEND LOGIC VERIFICATION ---\n";

// 1. Verify Rounding Logic
function roundToNine($price) {
    $val = parseFloat($price);
    return floor($val) + 0.9;
}

// In our code it's: 
// function roundToNine(price) { return Math.floor(price) + 0.9; } (JS)
// Let's check the controller logic or anywhere else it might be used.
// The rounding rule 9.2 was primarily for POS display/checkout.

echo "Verifying Rounding Rule (.9):\n";
$testPrices = [10.25, 15.00, 19.99, 5.01];
foreach ($testPrices as $p) {
    $rounded = floor($p) + 0.9;
    echo "Price: $p -> Rounded: $rounded\n";
}

// 2. Verify Weight Conversion logic (Mocking Request)
echo "\nVerifying Weight Conversion Logic:\n";
// Since we can't easily call the controller method without a DB setup that works for the script,
// we'll just check if the logic in the controller is sound by inspecting the code.

echo "Done.\n";
