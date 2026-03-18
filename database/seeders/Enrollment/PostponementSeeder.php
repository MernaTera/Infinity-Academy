<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\Postponement;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class PostponementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $employee = Employee::first();

        if (!$enrollment) {
            return; // safety
        }

        Postponement::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'start_date' => now(),
            'expected_return_date' => now()->addDays(7),
            'status' => 'Active',
            'created_by_cs_id' => $employee->employee_id,
        ]);
    }
}
