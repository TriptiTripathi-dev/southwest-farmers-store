<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enquiry escalation flow: Store (default) -> Warehouse -> Main Super Admin.
 * status starts at 'new' (visible to the store), moves to 'escalated_warehouse'
 * when the store forwards it, then 'escalated_admin' when warehouse staff
 * forward it further to the Super Admin specifically, and 'resolved' when closed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'status')) {
                $table->string('status')->default('new')->after('is_read');
            }
            if (!Schema::hasColumn('enquiries', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('enquiries', 'escalated_to_admin_at')) {
                $table->timestamp('escalated_to_admin_at')->nullable()->after('escalated_at');
            }
            if (!Schema::hasColumn('enquiries', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('escalated_to_admin_at');
            }
            if (!Schema::hasColumn('enquiries', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable()->after('resolved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            foreach (['status', 'escalated_at', 'escalated_to_admin_at', 'resolved_at', 'resolution_notes'] as $col) {
                if (Schema::hasColumn('enquiries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
