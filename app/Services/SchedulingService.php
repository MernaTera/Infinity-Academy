<?php
namespace App\Services;

use App\Models\Academic\InstanceSchedule;
use App\Models\Academic\CourseInstance;
use App\Models\HR\TeacherAvailability;
use App\Models\Academic\BreakSlot;

class SchedulingService
{
    public function getTeacherAvailability($teacherId)
    {
        return TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->get();
    }

    public function validateSchedule($data)
    {
        $start = $data['start_time'];
        $slot = $data['time_slot'];

        if ($start < $slot->start_time || $start >= $slot->end_time) {
            throw new \Exception('Start time must be inside selected slot');
        }

        $breaks = BreakSlot::all();

        foreach ($breaks as $break) {
            if ($start >= $break->start_time && $start < $break->end_time) {
                throw new \Exception('Selected time is inside break');
            }
        }
    }

    public function storeSchedule($instanceId, $data)
    {
        return InstanceSchedule::create([
            'course_instance_id' => $instanceId,
            'day_of_week' => $data['day_of_week'],
            'time_slot_id' => $data['time_slot_id'],
            'start_time' => $data['start_time'],
            'created_by_employee_id' => auth()->user()->employees->first()->employee_id ?? null,
        ]);
    }
}