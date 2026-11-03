<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenue_split', function (Blueprint $table) {
            $table->enum('source', ['Registration', 'Outstanding', 'Service'])
                ->default('Registration')
                ->after('allocation_type');
            $table->index(['employee_id', 'source', 'created_at'], 'idx_revenue_split_emp_source_created');
        });

        DB::statement("
            UPDATE revenue_split rs
            JOIN financial_transaction ft ON ft.transaction_id = rs.transaction_id
            SET rs.source = 'Service'
            WHERE ft.service_sale_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("DELETE FROM revenue_split WHERE source = 'Outstanding'");

        Schema::table('revenue_split', function (Blueprint $table) {
            $table->dropIndex('idx_revenue_split_emp_source_created');
            $table->dropColumn('source');
        });
    }
};
