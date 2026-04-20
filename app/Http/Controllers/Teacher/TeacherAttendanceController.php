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
    public function show($sessionId)
    {
        $teacher = Teacher::where('employee_id',
            Employee::where('user_id', auth()->id())->first()?->employee_id
        )->first();

        $session = CourseSession::with([
            'courseInstance.courseTemplate',
            'courseInstance.level',
            'courseInstance.enrollments.student.phones',
            'courseInstance.enrollments.attendances',
        ])->findOrFail($sessionId);

        if ($session->courseInstance->teacher_id !== $teacher->teacher_id) {
            abort(403);
        }

        $start    = Carbon::parse($session->start_time);
        $deadline = $start->copy()->addMinutes(20);
        $now      = now();

        $isOpen     = $session->status === 'Scheduled'
                   && Carbon::parse($session->session_date)->isToday()
                   && $now->between($start, $deadline);

        $isLocked   = $session->status === 'Completed'
                   || (!Carbon::parse($session->session_date)->isToday())
                   || $now->gt($deadline);

        $minutesLeft = $isOpen ? (int)$now->diffInMinutes($deadline, false) : 0;

        // Get existing attendance for this session
        $existingAttendance = Attendance::where('course_session_id', $sessionId)
            ->pluck('status', 'enrollment_id');

        return view('teacher.attendance', compact(
            'session', 'isOpen', 'isLocked', 'minutesLeft', 'existingAttendance'
        ));
    }

    public function store(Request $request, $sessionId)
    {
        $teacher = Teacher::where('employee_id',
            Employee::where('user_id', auth()->id())->first()?->employee_id
        )->first();

        $session = CourseSession::with('courseInstance')->findOrFail($sessionId);

        if ($session->courseInstance->teacher_id !== $teacher->teacher_id) {
            abort(403);
        }

        // Check window
        $start    = Carbon::parse($session->start_time);
        $deadline = $start->copy()->addMinutes(20);

        if (!now()->between($start, $deadline) || !Carbon::parse($session->session_date)->isToday()) {
            return back()->with('error', 'Attendance window is closed.');
        }

        if ($session->status === 'Completed') {
            return back()->with('error', 'Session attendance already saved.');
        }

        $enrollments = Enrollment::where('course_instance_id', $session->course_instance_id)
            ->whereIn('status', ['Active', 'Restricted'])
            ->get();

        foreach ($enrollments as $enrollment) {
            $status = $request->attendance[$enrollment->enrollment_id] ?? 'Absent';

            // Restricted students are always absent
            if ($enrollment->status === 'Restricted') {
                $status = 'Absent';
            }

            Attendance::updateOrCreate(
                [
                    'enrollment_id'    => $enrollment->enrollment_id,
                    'course_session_id'=> $sessionId,
                ],
                [
                    'status'      => $status,
                    'recorded_by' => auth()->id(),
                    'recorded_at' => now(),
                ]
            );
        }

        $session->update(['status' => 'Completed']);

        return back()->with('success', 'Attendance saved successfully.');
    }
}