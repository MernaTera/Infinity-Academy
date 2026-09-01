<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Only add an index if it doesn't already exist — makes this migration
     * safe to run even if some indexes were added by hand before.
     */
    private function missing(string $index): bool
    {
        return count(DB::select("SHOW INDEX FROM `lead` WHERE Key_name = ?", [$index])) === 0;
    }

    public function up(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            // Used by the archived list and by the dashboard status counts.
            if ($this->missing('lead_status_index'))        $table->index('status');

            // Used by the "due calls" scope (next_call_at <= now()).
            if ($this->missing('lead_next_call_at_index'))  $table->index('next_call_at');

            // Used EVERY MINUTE by the scheduler (releaseExpiredLeads / archiveOldLeads).
            if ($this->missing('lead_updated_at_index'))    $table->index('updated_at');

            // Used by every list's ORDER BY created_at DESC (latest()).
            if ($this->missing('lead_created_at_index'))    $table->index('created_at');

            // Composite: speeds up the CS dashboard counts (owner_cs_id + status).
            if ($this->missing('lead_owner_status_idx'))    $table->index(['owner_cs_id', 'status'], 'lead_owner_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            foreach ([
                'lead_status_index',
                'lead_next_call_at_index',
                'lead_updated_at_index',
                'lead_created_at_index',
                'lead_owner_status_idx',
            ] as $idx) {
                if (count(DB::select("SHOW INDEX FROM `lead` WHERE Key_name = ?", [$idx])) > 0) {
                    $table->dropIndex($idx);
                }
            }
        });
    }
};