<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropForeign('enrollment_teacher_id_foreign');
        });

        DB::statement("
            UPDATE enrollment e
            LEFT JOIN teacher t ON t.teacher_id = e.teacher_id
            SET e.teacher_id = NULL
            WHERE e.teacher_id IS NOT NULL AND t.teacher_id IS NULL
        ");

        Schema::table('enrollment', function (Blueprint $table) {
            $table->foreign('teacher_id', 'enrollment_teacher_id_foreign')
                  ->references('teacher_id')
                  ->on('teacher')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
  
    
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropForeign('enrollment_teacher_id_foreign');
        });

        DB::statement("
            UPDATE enrollment e
            LEFT JOIN employee emp ON emp.employee_id = e.teacher_id
            SET e.teacher_id = NULL
            WHERE e.teacher_id IS NOT NULL AND emp.employee_id IS NULL
        ");

        Schema::table('enrollment', function (Blueprint $table) {
            $table->foreign('teacher_id', 'enrollment_teacher_id_foreign')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }
};