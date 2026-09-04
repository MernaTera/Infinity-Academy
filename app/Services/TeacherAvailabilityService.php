<?php

namespace App\Services;

use App\Models\HR\Teacher;
use App\Models\HR\TeacherContract;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;

class TeacherAvailabilityService
{
    public function getAvailableTeachers($data)
    {
        if (($data['patch_option'] ?? '') !== 'current') {
            return [];
        }

        $patchId = $data['patch_id'] ?? null;
        if (!$patchId) {
            return [];
        }

        $levelId    = $data['level_id']    ?? null;
        $sublevelId = $data['sublevel_id'] ?? null;
        $courseId   = $data['course_template_id'] ?? null;

        $targetPatch = \App\Models\Academic\Patch::find($patchId);
        if (!$targetPatch) return [];

        $contracts = TeacherContract::with(['contractType', 'teacher.availability', 'teacher.employee', 'patch'])
            ->where('is_active', true)
            ->whereHas('teacher', fn($q) => $q->where('is_active', true))
            ->whereHas('contractType', fn($q) => $q->where('is_active', true))
            ->whereHas('patch', fn($q) => $q->where('start_date', '<=', $targetPatch->start_date))
            ->get()
            ->sortBy(fn($c) => optional($c->patch)->start_date)  
            ->unique('teacher_id');

        $available = [];
        $seenTeacherIds = [];

        foreach ($contracts as $contract) {
            $teacher = $contract->teacher;
            if (!$teacher) continue;

            if (in_array($teacher->teacher_id, $seenTeacherIds)) continue;

            if (!$this->teacherLevelSufficient($teacher, $levelId, $sublevelId, $courseId)) {
                continue;
            }

            $maxAllowed = (int) ($contract->contractType?->max_sessions_allowed ?? 0);
            if ($maxAllowed <= 0) continue;

            $used = $this->countTeacherSessionsInPatch($teacher->teacher_id, $patchId);
            if ($used >= $maxAllowed) continue;

            if ($teacher->availability->isEmpty()) continue;

            $seenTeacherIds[] = $teacher->teacher_id;
            $available[] = $teacher;
        }

        return $available;
    }

    private function teacherLevelSufficient(Teacher $teacher, ?int $levelId, ?int $sublevelId, ?int $courseId): bool
    {
        if ($sublevelId) {
            $sub = Sublevel::find($sublevelId);
            if ($sub && $sub->teacher_min_level) {
                return $teacher->english_level_id >= $sub->teacher_min_level;
            }
        }

        if ($levelId) {
            $level = Level::find($levelId);
            if ($level && $level->teacher_level) {
                return $teacher->english_level_id >= $level->teacher_level;
            }
        }

        if ($courseId) {
            $course = CourseTemplate::find($courseId);
            if ($course && $course->english_level_id) {
                return $teacher->english_level_id >= $course->english_level_id;
            }
        }

        return true;
    }

    private function countTeacherSessionsInPatch(int $teacherId, int $patchId): int
    {
        $courses = CourseInstance::where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', ['Active', 'Upcoming', 'Completed'])
            ->get();

        $total = 0;
        foreach ($courses as $course) {
            if ($course->session_duration > 0) {
                $total += (int) ceil((float) $course->total_hours / (float) $course->session_duration);
            }
        }
        return $total;
    }
}