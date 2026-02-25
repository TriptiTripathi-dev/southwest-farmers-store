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
        Schema::table('stock_request_items', function (Blueprint $table) {
            $table->integer('dispatched_quantity')->default(0)->after('quantity');
            $table->integer('received_quantity')->default(0)->after('dispatched_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_request_items', function (Blueprint $table) {
            $table->dropColumn(['dispatched_quantity', 'received_quantity']);
        });
    }
};
