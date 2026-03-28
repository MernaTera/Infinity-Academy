<?php

namespace App\Services;

use App\Models\HR\TeacherAvailability;
use App\Models\Academic\CourseInstance;

class TeacherAvailabilityService
{
    public function getAvailableTeachers($data)
    {
        $availabilities = TeacherAvailability::with('teacher')
            ->where('day_of_week', $data['day'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->get();

        $availableTeachers = [];

        foreach ($availabilities as $availability) {

            $teacher = $availability->teacher;

            // 2. check if teacher teaches this course/level
            if (!$this->isTeacherValid($teacher, $data)) {
                continue;
            }

            // 3. check conflict
            $hasConflict = CourseInstance::where('teacher_id', $teacher->teacher_id)
                ->whereHas('schedules', function ($q) use ($data) {
                    $q->where('day_of_week', $data['day'])
                      ->where('time_slot_id', $data['time_slot_id']);
                })
                ->exists();

            if (!$hasConflict) {
                $availableTeachers[] = $teacher;
            }
        }

        return $availableTeachers;
    }

    private function isTeacherValid($teacher, $data)
    {
        if (!empty($data['sublevel_id'])) {

            $sub = \App\Models\Academic\Sublevel::find($data['sublevel_id']);

            if ($sub && $sub->teacher_min_level) {
                return $teacher->english_level_id >= $sub->teacher_min_level;
            }
        }

        if (!empty($data['level_id'])) {

            $level = \App\Models\Academic\Level::find($data['level_id']);

            if ($level && $level->teacher_level) {
                return $teacher->english_level_id >= $level->teacher_level;
            }
        }
        return true;
    }
}