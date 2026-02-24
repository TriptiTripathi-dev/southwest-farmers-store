<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "--- Database Tables ---\n";
    $tables = DB::select('SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = \'public\'');
    foreach ($tables as $table) {
        echo "- " . $table->tablename . "\n";
    }

    echo "\n--- checking expected new tables ---\n";
    $expected = ['store_purchase_orders', 'store_purchase_order_items', 'store_order_schedules'];
    foreach ($expected as $table) {
        if (Schema::hasTable($table)) {
            echo "Table $table EXISTS.\n";
        } else {
            echo "Table $table MISSING.\n";
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
