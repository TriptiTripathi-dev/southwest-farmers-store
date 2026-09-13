<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kitchen_locations previously had no store link at all — one generic "Store
 * Kitchen" row served every location. Adding store_id so each physical store
 * can have its own kitchen inventory (item 7). Nullable to keep existing
 * Warehouse-side rows (type='warehouse'/'sub_kitchen') untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kitchen_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('kitchen_locations', 'store_id')) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kitchen_locations', function (Blueprint $table) {
            if (Schema::hasColumn('kitchen_locations', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });
    }
};
