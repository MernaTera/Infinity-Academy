<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('lead', 'assigned_at')) {
            Schema::table('lead', function (Blueprint $table) {
                $table->timestamp('assigned_at')->nullable()->after('owner_cs_id');
                $table->index(['owner_cs_id', 'assigned_at'], 'idx_lead_owner_assigned');
            });
        }

        DB::table('lead')
            ->whereNotNull('owner_cs_id')
            ->whereNull('assigned_at')
            ->update(['assigned_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('lead', 'assigned_at')) {
            Schema::table('lead', function (Blueprint $table) {
                $table->dropIndex('idx_lead_owner_assigned');
                $table->dropColumn('assigned_at');
            });
        }
    }
};