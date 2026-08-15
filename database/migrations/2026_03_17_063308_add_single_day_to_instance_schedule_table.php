<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A schedule row stores a day PAIR (e.g. sat_tue). By default the course
     * runs on BOTH days of the pair. When Student Care picks "single day", we
     * record which one day of that pair the course actually runs on here — as a
     * PHP/Carbon day-of-week number (0=Sun … 6=Sat). NULL means both days (the
     * original behaviour).
     */
    public function up(): void
    {
        Schema::table('instance_schedule', function (Blueprint $table) {
            $table->unsignedTinyInteger('single_day')->nullable()->after('day_of_week');
        });
    }

    public function down(): void
    {
        Schema::table('instance_schedule', function (Blueprint $table) {
            $table->dropColumn('single_day');
        });
    }
};