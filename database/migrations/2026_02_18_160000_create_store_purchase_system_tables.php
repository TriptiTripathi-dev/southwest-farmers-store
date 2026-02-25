<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('store_id')->constrained('store_details');
            $table->string('status')->default('pending'); // pending, approved, dispatched, completed, cancelled
            $table->integer('total_items')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->text('warehouse_remarks')->nullable();
            $table->text('store_remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('store_users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('store_purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_purchase_order_id')->constrained('store_purchase_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->integer('dispatched_quantity')->default(0);
            $table->integer('received_quantity')->default(0);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->timestamps();
        });

        Schema::create('store_order_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('store_details')->unique();
            $table->string('expected_day'); // Monday, Tuesday, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_order_schedules');
        Schema::dropIfExists('store_purchase_order_items');
        Schema::dropIfExists('store_purchase_orders');
    }
};
