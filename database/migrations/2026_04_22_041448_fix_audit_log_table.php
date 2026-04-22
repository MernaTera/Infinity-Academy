<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {

            // 1. Make changed_by nullable — system actions may not have a user
            $table->dropForeign(['changed_by']);
            $table->unsignedBigInteger('changed_by')->nullable()->change();
            $table->foreign('changed_by')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();

            // 2. Add changed_at column (was in model/design but missing from DB)
            $table->timestamp('changed_at')->nullable()->after('changed_by');
        });
    }

    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            $table->dropColumn('changed_at');

            $table->dropForeign(['changed_by']);
            $table->unsignedBigInteger('changed_by')->nullable(false)->change();
            $table->foreign('changed_by')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();
        });
    }
};