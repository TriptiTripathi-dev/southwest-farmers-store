<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix missing primary key and duplicate IDs in store_details first
        DB::statement("
            DELETE FROM store_details a 
            USING store_details b 
            WHERE a.ctid < b.ctid 
              AND a.id = b.id
        ");

        try {
            Schema::table('store_details', function (Blueprint $table) {
                $table->primary('id');
            });
        } catch (\Exception $e) {
            // Primary key already exists or other constraint issue handled
        }

        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained('store_details')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_categories');
    }
};
