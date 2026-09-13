<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Store app's MenuCategoryController/MenuItemController (and their
 * SoftDeletes-using models) were already written expecting `store_id`,
 * `image`, and `deleted_at` on these shared tables — every "Prepared Menus"
 * page 500'd because none of those columns actually existed (client-reported
 * bug). These tables were originally created by the Warehouse app's own
 * migration, scoped instead by `kitchen_location_id` — that column is left
 * untouched for backward compatibility; the columns added here are additive
 * and only used by the Store side going forward.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_categories', 'store_id')) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('menu_categories', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('menu_categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'store_id')) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('menu_items', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('menu_categories', function (Blueprint $table) {
            foreach (['store_id', 'image', 'deleted_at'] as $col) {
                if (Schema::hasColumn('menu_categories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('menu_items', function (Blueprint $table) {
            foreach (['store_id', 'deleted_at'] as $col) {
                if (Schema::hasColumn('menu_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
