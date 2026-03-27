<?php

namespace App\Services;

use App\Models\HR\TeacherAvailability;
use App\Models\Academic\CourseInstance;

class TeacherAvailabilityService
{
    public function findAvailableSlot($data)
    {
        $teachers = TeacherAvailability::with('teacher')
            ->where('day_of_week', $data['day'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->get();

        foreach ($teachers as $availability) {

            $hasConflict = CourseInstance::where('teacher_id', $availability->teacher_id)
                ->where('patch_id', $data['patch_id'])
                ->whereHas('schedules', function ($q) use ($data) {
                    $q->where('day_of_week', $data['day'])
                      ->where('time_slot_id', $data['time_slot_id']);
                })
                ->exists();

            if (!$hasConflict) {
                return $availability;
            }
        }

        return null;
    }
}
