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

        $sessionHours = 0;
        if ($session->start_time && $session->end_time) {
            $mins = Carbon::parse($session->start_time)->diffInMinutes(Carbon::parse($session->end_time));
            $sessionHours = round($mins / 60, 2);
        }
        if ($sessionHours <= 0) {
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

            $isPrivate = $enrollment->enrollment_type === 'Private'
                && $enrollment->hours_remaining !== null;

            if ($isPrivate && $sessionHours > 0 && $enrollment->status !== 'Restricted') {

                $shouldDeduct = false;

                if ($status === 'Present') {
                    $shouldDeduct = true;
                } elseif ($status === 'Absent') {
                    $priorAbsences = Attendance::where('enrollment_id', $enrollment->enrollment_id)
                        ->where('status', 'Absent')
                        ->where('course_session_id', '!=', $sessionId)
                        ->count();

                    if ($priorAbsences >= 2) {
                        $shouldDeduct = true;
                    }
                }

                if ($shouldDeduct) {
                    $newRemaining = max(0, (float) $enrollment->hours_remaining - $sessionHours);
                    $enrollment->hours_remaining = $newRemaining;

                    \DB::table('bundle_usage_log')->insert([
                        'enrollment_id'     => $enrollment->enrollment_id,
                        'course_session_id' => $sessionId,
                        'hours_deducted'    => $sessionHours,
                        'reason'            => 'ATTENDANCE',
                        'created_by_cs_id'  => $csEmployeeId,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

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
                ->where('status', 'Active')
                ->where(function ($q) {
                    $q->where(function ($p) {                      
                        $p->where('enrollment_type', 'Private')
                          ->where('hours_remaining', '>', 0);
                    })->orWhere(function ($p) {                    
                        $p->whereNotNull('package_id')
                          ->where('package_units_remaining', '>', 0);
                    })->orWhere(function ($p) {                    
                        $p->whereNull('package_id')
                          ->where('enrollment_type', '!=', 'Private');
                    });
                })
                ->update(['status' => 'Completed']);
        }

        return back()->with('success', 'Attendance saved successfully.');
    }
}