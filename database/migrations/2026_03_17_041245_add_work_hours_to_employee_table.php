<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store a fixed daily work window (start/end time) on the employee. Used
     * for Customer Service and Student Care staff so their shift times show on
     * their dashboards and the admin employee card. Nullable — not every role
     * has set hours.
     */
    public function up(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->time('work_start_time')->nullable()->after('salary');
            $table->time('work_end_time')->nullable()->after('work_start_time');
        });
    }

    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn(['work_start_time', 'work_end_time']);
        });
    }
};