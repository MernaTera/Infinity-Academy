<?php

namespace App\Services;

use App\Models\Academic\CourseInstance;

class GroupDetectionService
{
    public function detect($data)
    {
        $instance = CourseInstance::where('course_template_id', $data['course_template_id'])
            ->where('level_id', $data['level_id'])
            ->where('status', 'Active')
            ->get()
            ->first(function ($instance) {

                $completed = $instance->sessions()
                    ->where('status', 'Completed')
                    ->count();

                $capacity = $instance->capacity;
                $current = $instance->enrollments()->count();

                return $completed <= 3 && $current < $capacity;
            });

        if ($instance) {
            return [
                'type' => 'direct',
                'instance' => $instance
            ];
        }

        return [
            'type' => 'waiting'
        ];
    }
}
