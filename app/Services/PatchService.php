<?php

namespace App\Services;

use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;

class PatchService
{
    public function getOptions($courseTemplateId)
    {
        $options = [];

        $instance = CourseInstance::where('course_template_id', $courseTemplateId)
            ->where('status', 'Active')
            ->first();

        if ($instance) {

            $completed = $instance->sessions()
                ->where('status', 'Completed')
                ->count();

            if ($completed < 3) {
                $patch = Patch::find($instance->patch_id);

                if ($patch) {
                    $options[] = [
                        'type' => 'current',
                        'label' => 'Current Patch (Start: ' . $patch->start_date . ')',
                        'patch_id' => $patch->patch_id
                    ];
                }
            }
        }

        $nextPatch = Patch::where('status', 'Upcoming')
            ->orderBy('start_date')
            ->first();

        if ($nextPatch) {
            $options[] = [
                'type' => 'next',
                'label' => 'Next Patch (Start: ' . $nextPatch->start_date . ')',
                'patch_id' => $nextPatch->patch_id
            ];
        }

        $options[] = [
            'type' => 'custom',
            'label' => 'Choose Specific Date'
        ];

        return $options;
    }
}