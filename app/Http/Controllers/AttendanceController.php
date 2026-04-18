<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Academic\CourseSession;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Patch;
use App\Models\Academic\Sublevel;
use App\Models\Academic\Room;
use App\Models\HR\Employee;
use App\Models\HR\Teacher;
use App\Models\Academic\InstanceSchedule;
use App\Models\Academic\ScheduleChangeLog;
use App\Models\Enrollment\FinancialTransaction;
use App\Models\Enrollment\InstallmentApprovalLog;
use App\Models\Enrollment\BundleUsageLog;
use App\Models\Attendance\Attendance;

class AttendanceController extends Controller
{
    public function show($sessionId)
    {
        $session = CourseSession::with([
            'courseInstance.courseTemplate',
            'courseInstance.teacher.employee',
            'courseInstance.enrollments.student.phones',
            'courseInstance.enrollments.attendances'
        ])->findOrFail($sessionId);

        return view('student-care.attendance.index', compact('session'));
    }
    public function store(Request $request)
    {
        
        $session = CourseSession::findOrFail($request->session_id);

        if ($session->isCompleted()) {
            return back()->with('error', 'Session locked');
        }

        if ($session->session_date > now()->toDateString()) {
            return back()->with('error', 'Future session');
        }

        foreach ($request->attendance as $enrollmentId => $status) {

            $enrollment = Enrollment::find($enrollmentId);

            if ($enrollment->status === 'Cancelled') continue;

            if ($enrollment->status === 'Restricted') {
                $status = 'Absent';
            }

            foreach ($request->attendance as $enrollmentId => $status) {

                Attendance::updateOrCreate(
                    [
                        'enrollment_id' => $enrollmentId,
                        'course_session_id' => $session->course_session_id
                    ],
                    [
                        'status' => $status,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }
        }

        if ($session->session_date <= now()->toDateString()) {
            $session->update(['status' => 'Completed']);
        }

        return back()->with('success', 'Saved');
    }

    public function markAttendance(Request $request, $sessionId)
    {
        $session = CourseSession::with([
            'courseInstance.enrollments'
        ])->findOrFail($sessionId);

        if (!$session->isScheduled()) {
            return back()->with('error', 'Session not available');
        }

        if ($session->session_date > now()->toDateString()) {
            return back()->with('error', 'Future session');
        }

        if ($session->isCompleted()) {
            return back()->with('error', 'Session locked');
        }

        foreach ($request->attendance as $enrollmentId => $status) {

            $enrollment = Enrollment::find($enrollmentId);

            if ($enrollment->status === 'Cancelled') continue;

            if ($enrollment->status === 'Restricted') {
                $status = 'Absent';
            }

            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $enrollmentId,
                    'course_session_id' => $sessionId,
                ],
                [
                    'status' => $status,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Attendance saved');
    }
}
