<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * StoreOrderController and the order edit/show/receive views were written
 * assuming these columns exist (grand total, dispatch/receive timeline,
 * remarks from both sides) — none of them did. Reads silently showed $0 /
 * "Pending" / blank; writes (edit, confirmReceive) hard-crashed with
 * "column does not exist" (item 4: "warehouse order flow isn't working").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('store_purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->nullable()->after('admin_note');
            }
            if (!Schema::hasColumn('store_purchase_orders', 'dispatched_at')) {
                $table->timestamp('dispatched_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('store_purchase_orders', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('dispatched_at');
            }
            if (!Schema::hasColumn('store_purchase_orders', 'warehouse_remarks')) {
                $table->text('warehouse_remarks')->nullable()->after('received_at');
            }
            if (!Schema::hasColumn('store_purchase_orders', 'store_remarks')) {
                $table->text('store_remarks')->nullable()->after('warehouse_remarks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('store_purchase_orders', function (Blueprint $table) {
            foreach (['total_amount', 'dispatched_at', 'received_at', 'warehouse_remarks', 'store_remarks'] as $col) {
                if (Schema::hasColumn('store_purchase_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
