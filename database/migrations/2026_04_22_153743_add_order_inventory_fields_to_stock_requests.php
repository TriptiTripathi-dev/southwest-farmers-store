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
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->boolean('reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('store_users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->string('gm_email')->nullable();
            $table->string('gm_phone')->nullable();
            $table->string('vp_email')->nullable();
            $table->string('vp_phone')->nullable();
            $table->string('received_by_name')->nullable();
            $table->string('receiving_progress')->default('open'); // open, received, partially_received
            $table->timestamp('received_at')->nullable();
            $table->integer('received_qty')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'department_id', 'reviewed', 'reviewed_by', 'reviewed_at',
                'gm_email', 'gm_phone', 'vp_email', 'vp_phone',
                'received_by_name', 'receiving_progress', 'received_at', 'received_qty'
            ]);
        });
    }
};
