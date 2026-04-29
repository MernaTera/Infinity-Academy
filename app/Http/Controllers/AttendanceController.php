<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Academic\CourseSession;
use App\Models\Enrollment\Enrollment;
use App\Models\Attendance\Attendance;
use App\Models\HR\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Student Care — Show attendance form
    |------------------------------------------------------------------
    */
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

        // Window logic — SC: during session time only
        $now         = Carbon::now();
        $sessionDate = Carbon::parse($session->session_date);
        $startTime = $sessionDate->copy()->setTimeFromTimeString($session->start_time);
        $endTime   = $sessionDate->copy()->setTimeFromTimeString($session->end_time);

        $isToday     = $sessionDate->isToday();
        $isOpen      = $isToday && $now->between($startTime, $endTime);
        $isCompleted = $session->status === 'Completed';
        $isPast      = $sessionDate->isPast() && !$isToday;
        $isFuture    = $sessionDate->isFuture();

        // Existing attendance keyed by enrollment_id
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

    /*
    |------------------------------------------------------------------
    | Student Care — Store attendance
    |------------------------------------------------------------------
    */
    public function store(Request $request, $sessionId)
    {
        $session = CourseSession::findOrFail($sessionId);

        // Window check — SC: during session time only
        $now       = Carbon::now();
        $startTime = Carbon::parse($session->session_date . ' ' . Carbon::parse($session->start_time)->format('H:i:s'));
        $endTime   = Carbon::parse($session->session_date . ' ' . Carbon::parse($session->end_time)->format('H:i:s'));
        $isOpen    = Carbon::parse($session->session_date)->isToday() && $now->between($startTime, $endTime);

        // Allow editing completed sessions too (SC can always edit within session time)
        if (!$isOpen) {
            return back()->with('error', 'Attendance can only be taken during session time.');
        }

        $employeeId = Employee::where('user_id', auth()->id())->value('employee_id');

        foreach ($request->attendance ?? [] as $enrollmentId => $status) {
            $enrollment = Enrollment::find($enrollmentId);
            if (!$enrollment) continue;
            if ($enrollment->status === 'Cancelled') continue;
            if ($enrollment->status === 'Restricted') $status = 'Absent';
            if (!in_array($status, ['Present', 'Absent'])) continue;

            Attendance::updateOrCreate(
                [
                    'enrollment_id'    => $enrollmentId,
                    'course_session_id'=> $session->course_session_id,
                ],
                [
                    'status'      => $status,
                    'recorded_by' => $employeeId,
                    'recorded_at' => now(),
                ]
            );
        }

        // Mark session completed
        $session->update(['status' => 'Completed']);

        return back()->with('success', 'Attendance saved successfully.');
    }
}