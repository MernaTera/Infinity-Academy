<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE course_session MODIFY COLUMN start_time TIME NOT NULL");
        DB::statement("ALTER TABLE course_session MODIFY COLUMN end_time TIME NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE course_session MODIFY COLUMN start_time DATETIME NOT NULL");
        DB::statement("ALTER TABLE course_session MODIFY COLUMN end_time DATETIME NOT NULL");
    }
};
