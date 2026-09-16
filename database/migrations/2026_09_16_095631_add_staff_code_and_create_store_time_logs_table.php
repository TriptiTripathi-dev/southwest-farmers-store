<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Item 4 (Store): "All store employees must have their store ID's for
 * clock in and clock out." Adds a staff_code (the "store ID") to every
 * store employee, and a store_time_logs table mirroring the Warehouse
 * side's proven KitchenTimeLog pattern.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_users', function (Blueprint $table) {
            if (!Schema::hasColumn('store_users', 'staff_code')) {
                $table->string('staff_code')->nullable()->unique()->after('id');
            }
        });

        // Backfill existing staff with a generated code so "all" employees
        // have one immediately, not just newly-created ones.
        $users = DB::table('store_users')->whereNull('staff_code')->orderBy('id')->get(['id', 'store_id']);
        foreach ($users as $user) {
            $code = 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
            DB::table('store_users')->where('id', $user->id)->update(['staff_code' => $code]);
        }

        if (!Schema::hasTable('store_time_logs')) {
            Schema::create('store_time_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_user_id');
                $table->unsignedBigInteger('store_id')->nullable();
                $table->timestamp('clock_in_at')->nullable();
                $table->timestamp('clock_out_at')->nullable();
                $table->decimal('total_hours', 6, 2)->nullable();
                $table->unsignedInteger('break_minutes')->default(0);
                $table->string('status')->default('clocked_in');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('store_user_id');
                $table->index('store_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_time_logs');

        Schema::table('store_users', function (Blueprint $table) {
            if (Schema::hasColumn('store_users', 'staff_code')) {
                $table->dropColumn('staff_code');
            }
        });
    }
};
