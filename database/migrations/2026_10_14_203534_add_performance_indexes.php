<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasIndex('enrollment', 'idx_enrollment_status')) {
            Schema::table('enrollment', function (Blueprint $table) {
                $table->index('status', 'idx_enrollment_status');
            });
        }

        if (!Schema::hasIndex('course_session', 'idx_session_status_date')) {
            Schema::table('course_session', function (Blueprint $table) {
                $table->index(['status', 'session_date'], 'idx_session_status_date');
            });
        }

        if (!Schema::hasIndex('financial_transaction', 'idx_tx_enrollment_type')) {
            Schema::table('financial_transaction', function (Blueprint $table) {
                $table->index(['enrollment_id', 'transaction_type'], 'idx_tx_enrollment_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('enrollment', 'idx_enrollment_status')) {
            Schema::table('enrollment', fn (Blueprint $table) => $table->dropIndex('idx_enrollment_status'));
        }

        if (Schema::hasIndex('course_session', 'idx_session_status_date')) {
            Schema::table('course_session', fn (Blueprint $table) => $table->dropIndex('idx_session_status_date'));
        }

        if (Schema::hasIndex('financial_transaction', 'idx_tx_enrollment_type')) {
            Schema::table('financial_transaction', fn (Blueprint $table) => $table->dropIndex('idx_tx_enrollment_type'));
        }
    }
};
