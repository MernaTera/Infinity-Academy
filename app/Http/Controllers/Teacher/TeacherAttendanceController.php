<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Academic\CourseSession;
use App\Models\Enrollment\Enrollment;
use App\Models\Attendance\Attendance;
use App\Models\HR\Teacher;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Show — 20 min window from session start
    |------------------------------------------------------------------
    */
    public function show($sessionId)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        $session = CourseSession::with([
            'courseInstance.courseTemplate',
            'courseInstance.level',
            'courseInstance.enrollments.student.phones',
            'courseInstance.enrollments.attendances',
        ])->findOrFail($sessionId);

        if ($session->courseInstance->teacher_id !== $teacher->teacher_id) {
            abort(403);
        }

        $isToday = Carbon::parse($session->session_date)->isToday();

        // String comparison to avoid timezone issues
        $nowTime      = now()->format('H:i');
        $startTimeStr = Carbon::parse($session->start_time)->format('H:i');
        $deadlineStr  = Carbon::parse($session->start_time)->addMinutes(20)->format('H:i');

        $isOpen  = $isToday
                && $session->status === 'Scheduled'
                && $nowTime >= $startTimeStr
                && $nowTime <= $deadlineStr;

        $isLocked = !$isOpen && (
            $session->status === 'Completed'
            || !$isToday
            || $nowTime > $deadlineStr
        );

        $minutesLeft = 0;
        if ($isOpen) {
            $deadline    = Carbon::parse($session->start_time)->addMinutes(20);
            $minutesLeft = max(0, (int) now()->diffInMinutes($deadline, false));
        }

        $existingAttendance = Attendance::where('course_session_id', $sessionId)
            ->pluck('status', 'enrollment_id');

        return view('teacher.attendance', compact(
            'session', 'isOpen', 'isLocked', 'minutesLeft', 'existingAttendance'
        ));
    }

    /*
    |------------------------------------------------------------------
    | Store — 20 min window
    |------------------------------------------------------------------
    */
    public function store(Request $request, $sessionId)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        $session = CourseSession::with('courseInstance')->findOrFail($sessionId);

        if ($session->courseInstance->teacher_id !== $teacher->teacher_id) {
            abort(403);
        }

        $isToday      = Carbon::parse($session->session_date)->isToday();
        $nowTime      = now()->format('H:i');
        $startTimeStr = Carbon::parse($session->start_time)->format('H:i');
        $deadlineStr  = Carbon::parse($session->start_time)->addMinutes(20)->format('H:i');

        $isOpen = $isToday
               && $session->status === 'Scheduled'
               && $nowTime >= $startTimeStr
               && $nowTime <= $deadlineStr;

        if (!$isOpen) {
            return back()->with('error', 'Attendance window is closed. You have 20 minutes from session start.');
        }

        $enrollments = Enrollment::where('course_instance_id', $session->course_instance_id)
            ->whereIn('status', ['Active', 'Restricted'])
            ->get();

        foreach ($enrollments as $enrollment) {
            $status = $request->attendance[$enrollment->enrollment_id] ?? 'Absent';

            if ($enrollment->status === 'Restricted') {
                $status = 'Absent';
            }

            Attendance::updateOrCreate(
                [
                    'enrollment_id'     => $enrollment->enrollment_id,
                    'course_session_id' => $sessionId,
                ],
                [
                    'status'      => $status,
                    'recorded_by' => $teacher->employee_id,
                    'recorded_at' => now(),
                ]
            );
        }

        $session->update(['status' => 'Completed']);

        return back()->with('success', 'Attendance saved successfully.');
    }
}