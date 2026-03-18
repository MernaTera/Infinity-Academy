<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Seeder;
use App\Models\Enrollment\PlacementTest;
use App\Models\Student;
use App\Models\Academic\Level;
use App\Models\HR\Employee;

class PlacementTestSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $levels = Level::all();
        $cs = Employee::first();

        $data = [];

        foreach ($students as $student) {
            $level = $levels->random();

            $data[] = [
                'student_id' => $student->student_id,
                'score' => rand(50, 100),
                'assigned_level_id' => $level->level_id,
                'test_fee' => 100,
                'fee_paid' => 1,
                'created_by_cs_id' => $cs->employee_id,
            ];
        }

        PlacementTest::insert($data);
    }
}