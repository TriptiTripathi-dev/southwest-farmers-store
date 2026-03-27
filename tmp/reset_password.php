<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StoreCustomer;
use Illuminate\Support\Facades\Hash;

$customer = StoreCustomer::where('email', 'testuser123@example.com')->first();
if ($customer) {
    $customer->password = Hash::make('password');
    $customer->save();
    echo "Password reset for testuser123@example.com\n";
} else {
    echo "User not found\n";
}
