<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Academic\CourseInstance;

class TeacherController extends Controller
{
    private function resolveTeacher()
    {
        $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        if (!$employee) abort(404);

        $teacher = \App\Models\HR\Teacher::where('employee_id', $employee->employee_id)->first();
        if (!$teacher) abort(404);

        return [$employee, $teacher];
    }

    public function dashboard()
    {
        [$employee, $teacher] = $this->resolveTeacher();

        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')
            ->latest('start_date')->first();

        $contract = \App\Models\HR\TeacherContract::with('contractType')
            ->where('teacher_id', $teacher->teacher_id)
            ->where('is_active', true)
            ->when($currentPatch, fn($q) => $q->where('patch_id', $currentPatch->patch_id))
            ->latest('created_at')
            ->first();

        $allInstances = \App\Models\Academic\CourseInstance::with([
            'courseTemplate',
            'level',
            'sessions',
            'enrollments.report',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->whereIn('status', ['Active', 'Upcoming', 'Completed'])
        ->get();

        $activeInstances    = $allInstances->where('status', 'Active')->values();
        $upcomingInstances  = $allInstances->where('status', 'Upcoming')->values();
        $completedInstances = $allInstances->where('status', 'Completed')->values();

        $totalCourses  = $activeInstances->count() + $upcomingInstances->count();

        $totalStudents = $activeInstances->concat($upcomingInstances)
            ->sum(fn($i) => $i->enrollments->count());

        $sessionsThisMonth = $allInstances->sum(function ($inst) {
            return $inst->sessions->filter(
                fn($s) => Carbon::parse($s->session_date)->isCurrentMonth()
            )->count();
        });

        $daysUntilSalary = now()->daysInMonth - now()->day;

        $pendingReports = 0;
        $lateReports    = 0;

        foreach ($completedInstances as $inst) {
            $deadline = $inst->end_date
                ? Carbon::parse($inst->end_date)->addDays(3)
                : null;

            foreach ($inst->enrollments as $enr) {
                $reportStatus = $enr->report?->status ?? null;

                if (in_array($reportStatus, [null, 'Draft'])) {
                    $pendingReports++;

                    if ($deadline && now()->gt($deadline)) {
                        $lateReports++;
                    }
                }
            }
        }

        $restrictedStudents = $allInstances->sum(
            fn($i) => $i->enrollments->where('restriction_flag', true)->count()
        );

        $alerts = [];

        foreach ($activeInstances as $inst) {
            if (!$inst->end_date) continue;
            $daysLeft = (int) now()->diffInDays(Carbon::parse($inst->end_date), false);
            if ($daysLeft >= 0 && $daysLeft <= 7) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => '📅',
                    'msg'  => ($inst->courseTemplate->name ?? 'Course') .
                              ' ends in ' . $daysLeft . ' day' . ($daysLeft !== 1 ? 's' : ''),
                    'link' => route('teacher.courses.show', $inst->course_instance_id),
                    'cta'  => 'View Course',
                ];
            }
        }

        if ($pendingReports > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => '📝',
                'msg'  => $pendingReports . ' student report' . ($pendingReports > 1 ? 's' : '') . ' pending submission',
                'link' => route('teacher.reports.index'),
                'cta'  => 'Submit Reports',
            ];
        }

        if ($lateReports > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => '⚠️',
                'msg'  => $lateReports . ' report' . ($lateReports > 1 ? 's' : '') . ' overdue — past the 3-day deadline',
                'link' => route('teacher.reports.index'),
                'cta'  => 'Fix Now',
            ];
        }

        if ($restrictedStudents > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => '🔒',
                'msg'  => $restrictedStudents . ' student' . ($restrictedStudents > 1 ? 's' : '') . ' restricted due to outstanding payment',
                'link' => route('teacher.courses'),
                'cta'  => 'View Courses',
            ];
        }

        return view('teacher.dashboard', compact(
            'employee', 'teacher', 'contract', 'currentPatch',
            'activeInstances', 'upcomingInstances', 'completedInstances',
            'totalCourses', 'totalStudents', 'sessionsThisMonth',
            'pendingReports', 'lateReports', 'restrictedStudents',
            'daysUntilSalary', 'alerts'
        ));
    }

    public function schedule(Request $request)
    {
        [, $teacher] = $this->resolveTeacher();

        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')
            ->latest('start_date')->first();

        if (!$teacher) abort(404);

        $instances = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'branch', 'room',
            'instanceSchedules.timeSlot', 'sessions', 'enrollments.student',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->where('patch_id', $currentPatch?->patch_id)
        ->whereIn('status', ['Active', 'Upcoming', 'Completed'])
        ->get();

        $filterPair = $request->query('pair');
        $filterSlot = $request->query('slot');

        $filtered = $instances->filter(function ($inst) use ($filterPair, $filterSlot) {
            $schedule = $inst->instanceSchedules->first();
            if ($filterPair && $schedule?->day_of_week !== $filterPair) return false;
            if ($filterSlot && $schedule?->time_slot_id != $filterSlot) return false;
            return true;
        });

        $timeSlots = \App\Models\Academic\TimeSlot::where('is_active', true)->get();

        return view('teacher.schedule', compact(
            'currentPatch', 'instances', 'filtered',
            'timeSlots', 'filterPair', 'filterSlot'
        ));
    }

    public function courses()
    {
        [, $teacher] = $this->resolveTeacher();

        $activeCourses = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'patch',
            'instanceSchedules.timeSlot', 'sessions', 'enrollments',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->whereIn('status', ['Active', 'Upcoming'])
        ->orderByDesc('start_date')->get();

        $completedCourses = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'patch', 'sessions',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->where('status', 'Completed')
        ->orderByDesc('end_date')->limit(10)->get();

        $stats = [
            'active'    => $activeCourses->where('status', 'Active')->count(),
            'upcoming'  => $activeCourses->where('status', 'Upcoming')->count(),
            'completed' => $completedCourses->count(),
            'students'  => $activeCourses->sum(fn($i) => $i->enrollments->count()),
        ];

        return view('teacher.courses', compact('activeCourses', 'completedCourses', 'stats'));
    }

    public function pendingApprovals()
    {
        $teacher = \App\Models\HR\Teacher::where('employee_id',
            \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id')
        )->first();

        $pending = CourseInstance::with(['courseTemplate', 'patch', 'instanceSchedules.timeSlot'])
            ->where('teacher_id', $teacher->teacher_id)
            ->where('status', 'Pending_Approval')
            ->latest()
            ->get();

        return view('teacher.pending-approvals', compact('pending'));
    }

    public function approveInstance($id)
    {
        $teacher = \App\Models\HR\Teacher::where('employee_id',
            \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id')
        )->first();

        $instance = CourseInstance::with(['instanceSchedules', 'sessions'])
            ->where('teacher_id', $teacher->teacher_id)
            ->where('status', 'Pending_Approval')
            ->findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($instance) {

            $completedSessions = $instance->sessions->where('status', 'Completed');
            $completedCount    = $completedSessions->count();
            $totalSessions     = (int) ceil((float)$instance->total_hours / (float)$instance->session_duration);
            $remainingToMake   = max(0, $totalSessions - $completedCount);

            \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
                ->where('status', '!=', 'Completed')
                ->delete();

            if ($remainingToMake > 0) {
                $this->generateApprovedSessions(
                    $instance,
                    $instance->instanceSchedules->all(),
                    $completedCount,
                    $remainingToMake,
                    $completedSessions->max('session_date')
                );
            }

            $instance->update(['status' => 'Upcoming']);

            $scEmployee = \App\Models\HR\Employee::find($instance->created_by_employee_id);
            if ($scEmployee) {
                \DB::table('user_notification')->insert([
                    'employee_id'         => $scEmployee->employee_id,
                    'title'               => 'Course Instance Approved',
                    'message'             => 'Teacher approved "' . ($instance->courseTemplate?->name ?? 'the course') . '" — sessions have been generated.',
                    'related_entity_type' => 'course_instance',
                    'related_entity_id'   => $instance->course_instance_id,
                    'is_read'             => false,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        });

        return back()->with('success', 'Course approved — sessions generated successfully.');
    }

    private function generateApprovedSessions(CourseInstance $instance, array $schedules, int $completedCount, int $remainingToMake, ?string $lastCompletedDate): void
    {
        $dayMap = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];

        $pairCount = count($schedules);
        if ($pairCount === 0 || $remainingToMake <= 0) return;

        $perPair   = (int) floor($remainingToMake / $pairCount);
        $remainder = $remainingToMake % $pairCount;

        $maxExisting = (int) \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->max('session_number');
        $startNum = max($completedCount, $maxExisting);

        $floorDate = \Carbon\Carbon::parse($instance->start_date);
        if ($lastCompletedDate) {
            $afterCompleted = \Carbon\Carbon::parse($lastCompletedDate)->addDay();
            if ($afterCompleted->gt($floorDate)) $floorDate = $afterCompleted;
        }

        $usedDates = $instance->sessions->where('status', 'Completed')
            ->map(fn($s) => \Carbon\Carbon::parse($s->session_date)->toDateString())
            ->flip();

        $sessionNum = $startNum + 1;
        $made       = 0;

        foreach ($schedules as $i => $schedule) {
            $need       = $perPair + ($i === 0 ? $remainder : 0);
            $targetDays = $dayMap[$schedule->day_of_week] ?? [];
            if (empty($targetDays) || $need <= 0) continue;

            $cursor = $floorDate->copy();
            $end    = \Carbon\Carbon::parse($instance->end_date);
            $count  = 0;

            while ($cursor->lte($end) && $count < $need && $made < $remainingToMake) {
                $dateStr = $cursor->toDateString();
                if (in_array($cursor->dayOfWeek, $targetDays) && !$usedDates->has($dateStr)) {
                    $startDateTime = \Carbon\Carbon::parse(
                        $dateStr . ' ' . \Carbon\Carbon::parse($schedule->start_time)->format('H:i:s')
                    );
                    $endDateTime = $startDateTime->copy()->addHours((float) $instance->session_duration);

                    \App\Models\Academic\CourseSession::create([
                        'course_instance_id'      => $instance->course_instance_id,
                        'session_date'            => $dateStr,
                        'start_time'              => $startDateTime->format('H:i:s'),
                        'end_time'                => $endDateTime->format('H:i:s'),
                        'session_number'          => $sessionNum,
                        'room_id'                 => $instance->room_id,
                        'generated_from_schedule' => true,
                        'status'                  => 'Scheduled',
                    ]);

                    $sessionNum++;
                    $count++;
                    $made++;
                }
                $cursor->addDay();
            }
        }

        $all = \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->orderBy('session_date')->orderBy('start_time')->get();
        foreach ($all as $s) {
            \DB::table('course_session')->where('course_session_id', $s->course_session_id)
                ->update(['session_number' => -$s->course_session_id]);
        }
        foreach ($all as $idx => $s) {
            \DB::table('course_session')->where('course_session_id', $s->course_session_id)
                ->update(['session_number' => $idx + 1]);
        }
    }

    public function rejectInstance(Request $request, $id)
    {
        $teacher = \App\Models\HR\Teacher::where('employee_id',
            \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id')
        )->first();

        $instance = CourseInstance::with(['instanceSchedules', 'courseTemplate', 'sessions', 'enrollments'])
            ->where('teacher_id', $teacher->teacher_id)
            ->where('status', 'Pending_Approval')
            ->findOrFail($id);

        $hasHistory = $instance->enrollments->isNotEmpty()
            || $instance->sessions->where('status', 'Completed')->isNotEmpty();

        \Illuminate\Support\Facades\DB::transaction(function () use ($instance, $request, $hasHistory) {

            $scEmployee = \App\Models\HR\Employee::find($instance->created_by_employee_id);
            if ($scEmployee) {
                $msg = $hasHistory
                    ? 'Teacher rejected the schedule change to "' . ($instance->courseTemplate?->name ?? 'the course') . '". The course was kept as-is. Reason: ' . ($request->reason ?? 'No reason provided.')
                    : 'Teacher rejected "' . ($instance->courseTemplate?->name ?? 'the course') . '". Reason: ' . ($request->reason ?? 'No reason provided.');

                \DB::table('user_notification')->insert([
                    'employee_id'         => $scEmployee->employee_id,
                    'title'               => 'Course Instance Rejected',
                    'message'             => $msg,
                    'related_entity_type' => 'course_instance',
                    'related_entity_id'   => $instance->course_instance_id,
                    'is_read'             => false,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }

            if ($hasHistory) {
                $instance->update(['status' => 'Upcoming']);
            } else {
                $instance->instanceSchedules()->delete();
                \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)->delete();
                $instance->delete();
            }
        });

        return back()->with('success', 'Course instance rejected.');
    }
    public function courseShow($id)
    {
        [, $teacher] = $this->resolveTeacher();

        $instance = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'patch',
            'instanceSchedules.timeSlot', 'branch', 'room',
            'sessions' => fn($q) => $q->orderBy('session_number'),
            'enrollments.student.phones',
            'enrollments.attendances',
            'enrollments.placementTest',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->findOrFail($id);

        $todaySession = $instance->sessions->first(function ($s) {
            if ($s->status !== 'Scheduled') return false;
            if (!Carbon::parse($s->session_date)->isToday()) return false;
            $start    = Carbon::parse($s->start_time);
            $deadline = $start->copy()->addMinutes(20);
            return now()->between($start, $deadline);
        });

        $totalSessions     = $instance->sessions->count();
        $completedSessions = $instance->sessions->where('status', 'Completed')->count();

        return view('teacher.course-show', compact(
            'instance', 'todaySession', 'totalSessions', 'completedSessions'
        ));
    }
}