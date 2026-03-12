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
        Schema::table('quick_pos_settings', function (Blueprint $table) {
            $table->boolean('printer_enabled')->default(true);
            $table->boolean('scanner_enabled')->default(true);
            $table->boolean('scale_enabled')->default(true);
            $table->boolean('cash_drawer_enabled')->default(true);
            $table->boolean('auto_print_receipt')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_pos_settings', function (Blueprint $table) {
            $table->dropColumn([
                'printer_enabled',
                'scanner_enabled',
                'scale_enabled',
                'cash_drawer_enabled',
                'auto_print_receipt'
            ]);
        });
    }
};
