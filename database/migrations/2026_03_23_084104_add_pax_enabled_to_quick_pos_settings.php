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
            $table->boolean('pax_enabled')->default(false)->after('auto_print_receipt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_pos_settings', function (Blueprint $table) {
            $table->dropColumn('pax_enabled');
        });
    }
};
