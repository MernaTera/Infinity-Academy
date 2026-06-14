<?php

namespace App\Services;

use App\Models\HR\TeacherAvailability;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;
use Illuminate\Http\Request;

class TeacherAvailabilityService
{
public function getAvailableTeachers($data)
{
    if (($data['patch_option'] ?? '') !== 'current') {
        return [];
    }

    if (empty($data['patch_id'])) {
        return [];
    }

    $teachers = \App\Models\HR\Teacher::with(['availability', 'employee'])
        ->where('is_active', 1)
        ->get();

    $availableTeachers = [];

    foreach ($teachers as $teacher) {
        if (!$this->isTeacherValid($teacher, $data)) {
            continue;
        }
        $availableTeachers[] = $teacher;
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

    if (!empty($data['course_template_id'])) {
        $course = \App\Models\Academic\CourseTemplate::find($data['course_template_id']);
        if ($course && $course->english_level_id) {
            return $teacher->english_level_id >= $course->english_level_id;
        }
    }

    return true;
}
}