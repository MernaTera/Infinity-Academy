<?php

namespace App\Services;

use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;

class PatchService
{
    public function getAvailableOptions($data)
    {
        $options = [];

        // Current Patch
        $current = CourseInstance::where('course_template_id', $data['course_template_id'])
            ->where('status', 'Active')
            ->get()
            ->first(function ($instance) {

                $completed = $instance->sessions()
                    ->where('status', 'Completed')
                    ->count();

                return $completed < 4;
            });

        if ($current) {
            $options[] = [
                'type' => 'current',
                'label' => 'Join Current Patch (Started ' . $current->start_date . ')',
                'patch_id' => $current->patch_id
            ];
        }

        // Next Patch (estimated)
        $nextDate = now()->addWeeks(2);

        $options[] = [
            'type' => 'next',
            'label' => 'Next Patch (Estimated ' . $nextDate->format('Y-m-d') . ')',
            'date' => $nextDate
        ];

        return $options;
    }
}
