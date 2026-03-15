<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stores = \App\Models\StoreDetail::select('id', 'store_name', 'latitude', 'longitude', 'city')->get();
echo json_encode($stores, JSON_PRETTY_PRINT);
