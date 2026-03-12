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
        Schema::table('store_details', function (Blueprint $table) {
            $table->string('pos_terminal_id')->nullable();
            $table->string('pos_store_id')->nullable();
            $table->string('pos_agent_secret')->nullable();
            $table->string('pos_hardware_url')->nullable();
            $table->string('pos_terminal_status')->default('offline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_details', function (Blueprint $table) {
            $table->dropColumn([
                'pos_terminal_id',
                'pos_store_id',
                'pos_agent_secret',
                'pos_hardware_url',
                'pos_terminal_status'
            ]);
        });
    }
};
