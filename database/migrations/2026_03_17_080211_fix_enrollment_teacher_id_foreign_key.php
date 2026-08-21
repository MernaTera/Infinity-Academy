<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * enrollment.teacher_id stores a teacher.teacher_id (that's what the app,
 * the registration form validation `exists:teacher,teacher_id`, and every
 * other table — course_instance, teacher_availability, teacher_contract,
 * report — use). But the enrollment table's foreign key was mistakenly
 * pointing teacher_id at employee.employee_id.
 *
 * When a teacher_id value existed in `teacher` but not as an employee_id,
 * inserting the enrolment failed with:
 *   SQLSTATE[23000] ... enrollment_teacher_id_foreign ... REFERENCES employee(employee_id)
 *
 * This migration repoints the constraint to teacher(teacher_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop the incorrect foreign key (references employee.employee_id).
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropForeign('enrollment_teacher_id_foreign');
        });

        // 2) Null out any existing enrolment whose teacher_id is not a valid
        //    teacher_id, so the new constraint can be added without violation.
        //    (These are rows written under the old, wrong FK — their value was
        //    an employee_id that may not correspond to a teacher_id.)
        DB::statement("
            UPDATE enrollment e
            LEFT JOIN teacher t ON t.teacher_id = e.teacher_id
            SET e.teacher_id = NULL
            WHERE e.teacher_id IS NOT NULL AND t.teacher_id IS NULL
        ");

        // 3) Add the correct foreign key → teacher.teacher_id.
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