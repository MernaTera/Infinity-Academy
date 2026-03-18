<?php

namespace Database\Seeders\Attendance;

use Illuminate\Database\Seeder;
use App\Models\Attendance\Attendance;
use App\Models\Academic\CourseSession;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = CourseSession::all();
        $enrollments = Enrollment::all();
        $employee = Employee::first();

        $data = [];

        foreach ($sessions as $session) {
            foreach ($enrollments as $enrollment) {

                $data[] = [
                    'enrollment_id' => $enrollment->enrollment_id,
                    'course_session_id' => $session->session_id,
                    'status' => rand(0, 1) ? 'Present' : 'Absent',
                    'recorded_by' => $employee->employee_id,
                    'recorded_at' => now(),
                ];
            }
        }

        Attendance::insert($data);
    }
}