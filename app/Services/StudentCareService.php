<?php

namespace App\Services;
use App\Models\Enrollment\Enrollment;

class StudentCareService
{
    public function getWaitingList()
    {
        return Enrollment::with(['student'])
            ->whereNull('course_instance_id')
            ->get();
    }
}