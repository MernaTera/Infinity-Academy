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

        // Session length in hours (from its start/end times), used to deduct
        // from private students' remaining bundle hours.
        $sessionHours = 0;
        if ($session->start_time && $session->end_time) {
            $mins = Carbon::parse($session->start_time)->diffInMinutes(Carbon::parse($session->end_time));
            $sessionHours = round($mins / 60, 2);
        }
        if ($sessionHours <= 0) {
            // Fallback to the instance's configured session duration.
            $sessionHours = (float) ($session->courseInstance->session_duration ?? 0);
        }

        $csEmployeeId = $employee?->employee_id;

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

            // ── Private hours deduction ────────────────────────────────
            // Only private enrolments with a bundle track hours. Group
            // students are unaffected.
            $isPrivate = $enrollment->enrollment_type === 'Private'
                && $enrollment->hours_remaining !== null;

            if ($isPrivate && $sessionHours > 0 && $enrollment->status !== 'Restricted') {

                $shouldDeduct = false;

                if ($status === 'Present') {
                    // Attending always consumes the student's hours.
                    $shouldDeduct = true;
                } elseif ($status === 'Absent') {
                    // First two absences in THIS course are free; from the
                    // third absence onward, the missed session's hours are
                    // deducted as a penalty. Count prior absences for this
                    // enrolment (excluding the row we just wrote for this
                    // session, since that's the current one).
                    $priorAbsences = Attendance::where('enrollment_id', $enrollment->enrollment_id)
                        ->where('status', 'Absent')
                        ->where('course_session_id', '!=', $sessionId)
                        ->count();

                    // priorAbsences does not include this session. If the
                    // student already had >= 2 absences before this one, then
                    // this absence is the 3rd (or later) → deduct.
                    if ($priorAbsences >= 2) {
                        $shouldDeduct = true;
                    }
                }

                if ($shouldDeduct) {
                    $newRemaining = max(0, (float) $enrollment->hours_remaining - $sessionHours);
                    $enrollment->hours_remaining = $newRemaining;

                    // Log the deduction.
                    \DB::table('bundle_usage_log')->insert([
                        'enrollment_id'     => $enrollment->enrollment_id,
                        'course_session_id' => $sessionId,
                        'hours_deducted'    => $sessionHours,
                        'reason'            => 'ATTENDANCE',
                        'created_by_cs_id'  => $csEmployeeId,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    // If the bundle is now exhausted, restrict the student
                    // until they buy more hours.
                    if ($newRemaining <= 0) {
                        $enrollment->status = 'Restricted';
                        $enrollment->restriction_flag = true;
                    }

                    $enrollment->save();
                }
            }
        }

        $session->update(['status' => 'Completed']);

        $instance = $session->courseInstance;
        $remainingSessions = CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();

        if ($remainingSessions === 0) {
            Enrollment::where('course_instance_id', $instance->course_instance_id)
                ->where('enrollment_type', 'Private')
                ->where('status', 'Active')
                ->where('hours_remaining', '>', 0)
                ->update(['status' => 'Completed']);
        }

        return back()->with('success', 'Attendance saved successfully.');
    }
}