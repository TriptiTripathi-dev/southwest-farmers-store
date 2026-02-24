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
        Schema::table('stock_requests', function (Blueprint $table) {
            // Add new PO-based fields
            $table->string('request_number', 50)->unique()->nullable()->after('id');
            $table->integer('total_items')->default(0)->after('store_id');
            $table->decimal('total_amount', 10, 2)->default(0)->after('total_items');
            $table->unsignedBigInteger('requested_by')->nullable()->after('store_id');
            $table->unsignedBigInteger('approved_by')->nullable()->after('requested_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Remove old item-specific fields (moved to stock_request_items)
            // Note: Commenting out for safety - uncomment after data migration
            // $table->dropColumn(['product_id', 'requested_quantity', 'fulfilled_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            // Drop new fields
            $table->dropColumn([
                'request_number',
                'total_items',
                'total_amount',
                'requested_by',
                'approved_by',
                'approved_at'
            ]);
            
            // Restore old fields if needed
            // $table->foreignId('product_id')->nullable()->constrained('products');
            // $table->integer('requested_quantity')->default(0);
            // $table->integer('fulfilled_quantity')->default(0);
        });
    }
};
