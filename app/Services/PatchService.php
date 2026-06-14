<?php

namespace App\Services;

use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseSession;

class PatchService
{
    public function getOptions($courseTemplateId)
    {
        $options = [];

        $instance = CourseInstance::where('course_template_id', $courseTemplateId)
            ->whereIn('status', ['Active', 'Upcoming'])
            ->orderByRaw("FIELD(status, 'Active', 'Upcoming')")
            ->withCount('enrollments')
            ->get()
            ->first(fn($i) => $i->enrollments_count < $i->capacity);

        if ($instance) {
            $completed  = $instance->sessions()->where('status', 'Completed')->count();
            $showCurrent = $instance->status === 'Upcoming' || $completed < 3;

            if ($showCurrent) {
                $patch = Patch::find($instance->patch_id);
                if ($patch) {
                    $options[] = [
                        'type'     => 'current',
                        'label'    => 'Current Patch — ' . $instance->courseTemplate?->name
                                    . ' (' . $instance->enrollments_count . '/' . $instance->capacity . ' seats) '
                                    . '· Start: ' . $patch->start_date,
                        'patch_id' => $patch->patch_id,
                    ];
                }
            }
        }

        $nextPatch = Patch::where('status', 'Upcoming')
            ->orderBy('start_date')
            ->first();

        if ($nextPatch) {
            $options[] = [
                'type'     => 'next',
                'label'    => 'Next Patch (Start: ' . $nextPatch->start_date . ')',
                'patch_id' => $nextPatch->patch_id,
            ];
        }

        $options[] = [
            'type'  => 'custom',
            'label' => 'Choose Specific Date',
        ];

        return $options;
    }
}