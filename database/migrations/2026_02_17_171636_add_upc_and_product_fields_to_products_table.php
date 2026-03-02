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
        Schema::table('products', function (Blueprint $table) {
            // UPC Code - must be displayed before product name everywhere
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 50)->nullable()->after('id');
                $table->index('sku');
            }
            
            // Unit type: 'units' or 'weight'
            $table->enum('unit_type', ['units', 'weight'])->default('units')->after('unit');
            
            // Weight options for weight-based products (JSON: [10, 20, 50])
            $table->json('weight_options')->nullable()->after('unit_type');
            
            // Lead time in days (for international supply)
            $table->integer('lead_time_days')->nullable()->after('price');
            
            // Expiration requirement flag
            $table->boolean('requires_expiration')->default(false)->after('lead_time_days');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'unit_type',
                'weight_options',
                'lead_time_days',
                'requires_expiration'
            ]);
        });
    }
};
