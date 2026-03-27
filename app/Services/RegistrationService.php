<?php

namespace App\Services;

use App\Models\Leads\Lead;
use App\Models\Student\Student;
use App\Models\Enrollment\Enrollment;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function register($data)
    {
        return DB::transaction(function () use ($data) {

            $lead = Lead::findOrFail($data['lead_id']);

            $student = Student::create([
                'full_name' => $lead->full_name,
                'phone' => $lead->phone,
                'location' => $lead->location,
            ]);

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_template_id' => $data['interested_course_template_id'],
                'level_id' => $data['interested_level_id'] ?? null,
                'sublevel_id' => $data['interested_sublevel_id'] ?? null,
                'type' => $data['type'], 
                'status' => 'Active'
            ]);

            $lead->update([
                'status' => 'Registered',
                'student_id' => $student->id
            ]);

            return $student;
        });
    }
}