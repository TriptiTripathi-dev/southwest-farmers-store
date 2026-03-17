<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

function check_constraints($table) {
    echo "--- Constraints for $table ---\n";
    $constraints = DB::select("
        SELECT
            conname AS constraint_name,
            contype AS constraint_type
        FROM
            pg_constraint
        WHERE
            conrelid = '$table'::regclass;
    ");
    print_r($constraints);
    
    echo "\n--- Indexes for $table ---\n";
    $indexes = DB::select("
        SELECT
            indexname,
            indexdef
        FROM
            pg_indexes
        WHERE
            tablename = '$table';
    ");
    print_r($indexes);
}

check_constraints('product_categories');
check_constraints('store_stocks');
