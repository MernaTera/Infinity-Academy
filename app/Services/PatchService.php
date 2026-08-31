<?php

namespace App\Services;

use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\HR\Teacher;
use App\Models\HR\TeacherContract;

class PatchService
{
    /**
     * @param array $context {
     *   @var int    $course_template_id
     *   @var string $type            Group|Private
     *   @var string $delivery_mood   Online|Offline
     *   @var int    $level_id        (nullable)
     *   @var int    $sublevel_id     (nullable)
     * }
     * @return array<array{type:string,case:?string,label:string,patch_id:?int,requires_teacher_choice:bool}>
     */
    public function getOptions(array $context): array
    {
        $options = [];

        $courseTemplateId = $context['course_template_id'] ?? null;
        $type             = $context['type']               ?? null;
        $mode             = $context['delivery_mood']      ?? null;
        $levelId          = $context['level_id']           ?? null;
        $sublevelId       = $context['sublevel_id']        ?? null;

        if (!$courseTemplateId || !$type || !$mode) {
            return $this->buildFallbackOptions();
        }

        $currentPatch = Patch::where('status', 'Active')->first();

        if ($currentPatch) {
            $currentOption = $this->evaluateCurrentPatch(
                $currentPatch, $type, $mode, $courseTemplateId, $levelId, $sublevelId
            );
            if ($currentOption) {
                $options[] = $currentOption;
            }
        }

        $nextPatch = Patch::where('status', 'Upcoming')
            ->orderBy('start_date')
            ->first();

        if ($nextPatch) {
            $options[] = [
                'type'                    => 'next',
                'case'                    => null,
                'label'                   => 'Next Patch — ' . $nextPatch->name
                                            . ' (Start: ' . $nextPatch->start_date . ')',
                'patch_id'                => $nextPatch->patch_id,
                'requires_teacher_choice' => false,
            ];
        }

        $options[] = [
            'type'                    => 'custom',
            'case'                    => null,
            'label'                   => 'Choose Specific Date',
            'patch_id'                => null,
            'requires_teacher_choice' => false,
        ];

        return $options;
    }

    private function evaluateCurrentPatch(
        Patch $currentPatch,
        string $type,
        string $mode,
        int $courseTemplateId,
        ?int $levelId,
        ?int $sublevelId
    ): ?array {
        if ($type === 'Group') {
            $existing = $this->findJoinableGroupCourse(
                $currentPatch, $mode, $courseTemplateId, $levelId, $sublevelId
            );

            if ($existing) {
                $courseName = $existing->courseTemplate?->name ?? 'course';
                return [
                    'type'                    => 'current',
                    'case'                    => 'A',
                    'label'                   => 'Current Patch — Join existing "' . $courseName . '" ('
                                                . $existing->enrollments_count . '/' . $existing->capacity . ' seats)',
                    'patch_id'                => $currentPatch->patch_id,
                    'course_instance_id'      => $existing->course_instance_id,
                    'requires_teacher_choice' => false,
                ];
            }

            $teacher = $this->findAvailableTeacher(
                $currentPatch, $mode, $levelId, $sublevelId, $courseTemplateId
            );

            if ($teacher) {
                return [
                    'type'                    => 'current',
                    'case'                    => 'B',
                    'label'                   => 'Current Patch — Start new group with available teacher',
                    'patch_id'                => $currentPatch->patch_id,
                    'course_instance_id'      => null,
                    'requires_teacher_choice' => false,
                    'auto_teacher_id'         => $teacher->teacher_id, 
                ];
            }
        }

        if ($type === 'Private') {
            $teacher = $this->findAvailableTeacher(
                $currentPatch, $mode, $levelId, $sublevelId, $courseTemplateId
            );

            if ($teacher) {
                return [
                    'type'                    => 'current',
                    'case'                    => 'B',
                    'label'                   => 'Current Patch — Teacher available for private session',
                    'patch_id'                => $currentPatch->patch_id,
                    'course_instance_id'      => null,
                    'requires_teacher_choice' => true,
                ];
            }
        }

        return null;
    }

    private function findJoinableGroupCourse(
        Patch $patch,
        string $mode,
        int $courseTemplateId,
        ?int $levelId,
        ?int $sublevelId
    ) {
        $candidates = CourseInstance::where('course_template_id', $courseTemplateId)
            ->where('patch_id', $patch->patch_id)
            ->where('type', 'Group')
            ->where('delivery_mood', $mode)
            ->when($levelId, fn($q) => $q->where('level_id', $levelId))
            ->when($sublevelId, fn($q) => $q->where('sublevel_id', $sublevelId))
            ->whereIn('status', ['Active', 'Upcoming'])
            ->orderByRaw("FIELD(status, 'Active', 'Upcoming')")
            ->withCount('enrollments')
            ->get();

        foreach ($candidates as $instance) {
            if ($instance->enrollments_count >= $instance->capacity) {
                continue;
            }

            if ($instance->status === 'Active') {
                $completed = $instance->sessions()->where('status', 'Completed')->count();
                if ($completed >= 3) {
                    continue;
                }
            }

            return $instance;
        }

        return null;
    }

    private function findAvailableTeacher(
        Patch $patch,
        string $mode,
        ?int $levelId,
        ?int $sublevelId,
        int $courseTemplateId
    ) {
        // A teacher's contract starts at a given patch and stays in effect for
        // that patch AND every later one — it is NOT per-patch. So a teacher is
        // available for THIS patch if they have an active contract on any patch
        // whose start_date is on or before this patch's start_date. (Previously
        // this required a contract for the exact patch, so a brand-new patch had
        // no available teachers until each was re-contracted.)
        $contracts = TeacherContract::with(['contractType', 'teacher', 'patch'])
            ->where('is_active', true)
            ->whereHas('teacher', fn($q) => $q->where('is_active', true))
            ->whereHas('contractType', fn($q) => $q->where('is_active', true))
            ->whereHas('patch', fn($q) => $q->where('start_date', '<=', $patch->start_date))
            ->get();

        // Keep only the earliest-starting contract per teacher (their standing
        // contract), so max-session limits are read from the right row.
        $contracts = $contracts
            ->sortBy(fn($c) => optional($c->patch)->start_date)
            ->unique('teacher_id');

        foreach ($contracts as $contract) {
            $teacher = $contract->teacher;
            if (!$teacher) continue;

            if (!$this->teacherLevelSufficient($teacher, $levelId, $sublevelId, $courseTemplateId)) {
                continue;
            }

            $maxAllowed = (int) ($contract->contractType?->max_sessions_allowed ?? 0);
            if ($maxAllowed <= 0) continue;

            // Session usage is still counted within THIS patch (each patch has
            // its own session budget).
            $used = $this->countTeacherSessionsInPatch($teacher->teacher_id, $patch->patch_id);

            if ($used < $maxAllowed) {
                return $teacher;
            }
        }

        return null;
    }


    private function teacherLevelSufficient(Teacher $teacher, ?int $levelId, ?int $sublevelId, int $courseTemplateId): bool
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

        if ($courseTemplateId) {
            $course = CourseTemplate::find($courseTemplateId);
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

    private function buildFallbackOptions(): array
    {
        $options = [];

        $nextPatch = Patch::where('status', 'Upcoming')
            ->orderBy('start_date')
            ->first();

        if ($nextPatch) {
            $options[] = [
                'type'                    => 'next',
                'case'                    => null,
                'label'                   => 'Next Patch — ' . $nextPatch->name
                                            . ' (Start: ' . $nextPatch->start_date . ')',
                'patch_id'                => $nextPatch->patch_id,
                'requires_teacher_choice' => false,
            ];
        }

        $options[] = [
            'type'                    => 'custom',
            'case'                    => null,
            'label'                   => 'Choose Specific Date',
            'patch_id'                => null,
            'requires_teacher_choice' => false,
        ];

        return $options;
    }
}