<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_stocks', function (Blueprint $table) {
            // In-transit quantity (ordered but not yet received)
            $table->integer('in_transit_qty')->default(0)->after('quantity');
            
            // Status: in_stock, low_stock, out_of_stock, in_transit
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'in_transit'])
                  ->default('in_stock')
                  ->after('in_transit_qty');
            
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('store_stocks', function (Blueprint $table) {
            $table->dropColumn(['in_transit_qty', 'status']);
        });
    }
};
