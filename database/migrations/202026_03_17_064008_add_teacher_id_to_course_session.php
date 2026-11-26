<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('course_session', 'teacher_id')) {
            Schema::table('course_session', function (Blueprint $table) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('course_instance_id');
                $table->index('teacher_id', 'idx_session_teacher');
            });
        }

        DB::statement('
            UPDATE course_session s
            JOIN course_instance ci ON ci.course_instance_id = s.course_instance_id
            SET s.teacher_id = ci.teacher_id
            WHERE s.teacher_id IS NULL
        ');
    }

    public function down(): void
    {
        if (Schema::hasColumn('course_session', 'teacher_id')) {
            Schema::table('course_session', function (Blueprint $table) {
                $table->dropIndex('idx_session_teacher');
                $table->dropColumn('teacher_id');
            });
        }
    }
};
