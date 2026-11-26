<?php

namespace App\Support;

use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseSession;

class TeacherLoad
{
    public static function sessionsInPatch(int $teacherId, int $patchId, array $statuses, ?int $excludeInstanceId = null): int
    {
        $taught = CourseSession::where('teacher_id', $teacherId)
            ->where('status', '!=', 'Cancelled')
            ->whereHas('courseInstance', fn($q) =>
                $q->where('patch_id', $patchId)
                  ->whereIn('status', $statuses)
                  ->when($excludeInstanceId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeInstanceId))
            )
            ->count();

        $planned = CourseInstance::where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', $statuses)
            ->when($excludeInstanceId, fn($q) => $q->where('course_instance_id', '!=', $excludeInstanceId))
            ->whereDoesntHave('sessions')
            ->get()
            ->sum(fn($ci) => (float) $ci->session_duration > 0
                ? (int) ceil((float) $ci->total_hours / (float) $ci->session_duration)
                : 0);

        return $taught + $planned;
    }
}
