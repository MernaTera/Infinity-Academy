<?php

namespace App\Http\Controllers;

use App\Models\Academic\CourseSession;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function show($sessionId)
    {
        $session = CourseSession::with([
            'courseInstance.courseTemplate',
            'courseInstance.level',
            'courseInstance.sublevel',
            'courseInstance.teacher.employee',
            'courseInstance.enrollments.student.phones',
            'courseInstance.enrollments.attendances',
        ])->findOrFail($sessionId);

        $sessionDate = Carbon::parse($session->session_date);
        $isToday     = $sessionDate->isToday();

        $nowTime   = now()->format('H:i');
        $startTime = Carbon::parse($session->start_time)->format('H:i');
        $endTime   = Carbon::parse($session->end_time)->format('H:i');

        $isOpen      = $isToday
                    && $nowTime >= $startTime
                    && $nowTime <= $endTime
                    && $session->status !== 'Cancelled';

        $isCompleted = $session->status === 'Completed';
        $isPast      = !$isToday && $sessionDate->isPast();
        $isFuture    = $sessionDate->isFuture();

        $existingAttendance = [];
        foreach ($session->courseInstance->enrollments as $enrollment) {
            $att = $enrollment->attendances
                ->where('course_session_id', $session->course_session_id)
                ->first();
            $existingAttendance[$enrollment->enrollment_id] = $att?->status;
        }

        return view('student-care.attendance.index', compact(
            'session', 'isOpen', 'isCompleted', 'isPast', 'isFuture', 'existingAttendance'
        ));
    }
}
