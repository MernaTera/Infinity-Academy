<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store the private student's preferred day-pair on their waiting-list
     * entry, so Student Care can see which days they asked for when assigning
     * them to a course. Nullable — group entries don't carry a day preference.
     */
    public function up(): void
    {
        Schema::table('waiting_list', function (Blueprint $table) {
            $table->enum('preferred_days', ['sat_tue', 'sun_wed', 'mon_thu'])
                  ->nullable()
                  ->after('preferred_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('waiting_list', function (Blueprint $table) {
            $table->dropColumn('preferred_days');
        });
    }
};