<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Seeder;
use App\Models\Enrollment\Enrollment;
use App\Models\Student\Student;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;
use App\Models\HR\Employee;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $instances = CourseInstance::all();
        $patches = Patch::all();
        $cs = Employee::first();

        $data = [];

        foreach ($students as $student) {
            $instance = $instances->random();

            $data[] = [
                'student_id' => $student->student_id,
                'course_instance_id' => $instance->course_instance_id,
                'patch_id' => $instance->patch_id,
                'level_id' => $instance->level_id,
                'sublevel_id' => $instance->sublevel_id,
                'enrollment_type' => 'Group',
                'delivery_mood' => 'Offline',
                'actual_start_date' => now(),
                'final_price' => 1500,
                'payment_plan_id' => 1, // ⚠️ لازم يكون موجود
                'status' => 'Active',
                'created_by_cs_id' => $cs->employee_id,
            ];
        }

        Enrollment::insert($data);
    }
}