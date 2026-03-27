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
        Schema::table('sales', function (Blueprint $table) {
            // Status for website orders (pending → processing → completed/cancelled)
            // Default 'completed' keeps existing POS sales valid
            $table->string('status')->default('completed')->after('payment_method');

            // Track whether the order came from the website or the in-store POS
            $table->string('source')->default('pos')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['status', 'source']);
        });
    }
};
