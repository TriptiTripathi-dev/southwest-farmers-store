<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up product_categories duplicates
        // We use ctid in Postgres to distinguish between identical rows
        DB::statement("
            DELETE FROM product_categories a 
            USING product_categories b 
            WHERE a.ctid < b.ctid 
              AND a.id = b.id
        ");

        // 2. Clean up store_stocks duplicates
        DB::statement("
            DELETE FROM store_stocks a 
            USING store_stocks b 
            WHERE a.ctid < b.ctid 
              AND a.id = b.id
        ");

        // 3. Add Primary Keys and Unique Constraints
        Schema::table('product_categories', function (Blueprint $table) {
            // First drop existing primary key if any (though we checked and it seems missing)
            // But we can just try to add it.
            try {
                $table->primary('id');
            } catch (\Exception $e) {
                // Already has primary key or other error
            }
            
            // Add unique constraint on name and store_id if store_id exists
            if (Schema::hasColumn('product_categories', 'store_id')) {
                $table->unique(['name', 'store_id'], 'unique_category_per_store');
            } else {
                $table->unique('name', 'unique_category_name');
            }
        });

        Schema::table('store_stocks', function (Blueprint $table) {
            try {
                $table->primary('id');
            } catch (\Exception $e) {
                // Already has primary key
            }
            
            $table->unique(['store_id', 'product_id'], 'unique_stock_per_product_store');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropUnique('unique_category_per_store');
            $table->dropUnique('unique_category_name');
            $table->dropPrimary('product_categories_pkey');
        });

        Schema::table('store_stocks', function (Blueprint $table) {
            $table->dropUnique('unique_stock_per_product_store');
            $table->dropPrimary('store_stocks_pkey');
        });
    }
};
