<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\BundleUsageLog;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\CourseSession;
use App\Models\HR\Employee;

class BundleUsageLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $session = CourseSession::first();
        $employee = Employee::first();

        BundleUsageLog::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'course_session_id' => $session->session_id,
            'hours_deducted' => 2,
            'reason' => 'ATTENDANCE',
            'created_by_cs_id' => $employee->employee_id,
        ]);
    }
}
