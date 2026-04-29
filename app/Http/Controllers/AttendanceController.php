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
    | Student Care — Show
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

        $sessionDate = Carbon::parse($session->session_date);
        $isToday     = $sessionDate->isToday();

        // String comparison to avoid timezone issues
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

    /*
    |------------------------------------------------------------------
    | Student Care — Store
    |------------------------------------------------------------------
    */
    public function store(Request $request, $sessionId)
    {
        $session = CourseSession::findOrFail($sessionId);

        $nowTime   = now()->format('H:i');
        $startTime = Carbon::parse($session->start_time)->format('H:i');
        $endTime   = Carbon::parse($session->end_time)->format('H:i');
        $isToday   = Carbon::parse($session->session_date)->isToday();

        $isOpen = $isToday
               && $nowTime >= $startTime
               && $nowTime <= $endTime
               && $session->status !== 'Cancelled';

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
                    'enrollment_id'     => $enrollmentId,
                    'course_session_id' => $session->course_session_id,
                ],
                [
                    'status'      => $status,
                    'recorded_by' => $employeeId,
                    'recorded_at' => now(),
                ]
            );
        }

        $session->update(['status' => 'Completed']);

        return back()->with('success', 'Attendance saved successfully.');
    }
}