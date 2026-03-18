<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\RestrictionLog;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class RestrictionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $employee = Employee::first();

        if (!$enrollment) {
            return;
        }

        RestrictionLog::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'triggered_by' => 'Customer_Service',
            'reason' => 'absence_limit_exceeded',
            'notes' => 'Too many absences',
            'released_by' => null,
        ]);
    }
}
