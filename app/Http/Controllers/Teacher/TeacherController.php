<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherController extends Controller
{
    // ─── helpers ────────────────────────────────────────────────────────────
    private function resolveTeacher()
    {
        $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        if (!$employee) abort(404);

        $teacher = \App\Models\HR\Teacher::where('employee_id', $employee->employee_id)->first();
        if (!$teacher) abort(404);

        return [$employee, $teacher];
    }

    // ─── dashboard ──────────────────────────────────────────────────────────
    public function dashboard()
    {
        [$employee, $teacher] = $this->resolveTeacher();

        // Current active patch
        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')
            ->latest('start_date')->first();

        // Active contract for current patch
        $contract = \App\Models\HR\ContractType::where('teacher_id', $teacher->teacher_id)
            ->where('is_active', true)
            ->when($currentPatch, fn($q) => $q->where('patch_id', $currentPatch->patch_id))
            ->latest('created_at')
            ->first();

        // All course instances (eager load what we need)
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

        // ── Profile stats ──────────────────────────────────────────────────
        $totalCourses  = $activeInstances->count() + $upcomingInstances->count();

        $totalStudents = $activeInstances->concat($upcomingInstances)
            ->sum(fn($i) => $i->enrollments->count());

        // Sessions this calendar month
        $sessionsThisMonth = $allInstances->sum(function ($inst) {
            return $inst->sessions->filter(
                fn($s) => Carbon::parse($s->session_date)->isCurrentMonth()
            )->count();
        });

        // Days remaining until end-of-month (salary day)
        $daysUntilSalary = now()->daysInMonth - now()->day;

        // ── Academic summary ───────────────────────────────────────────────
        $pendingReports = 0;
        $lateReports    = 0;

        foreach ($completedInstances as $inst) {
            $deadline = $inst->end_date
                ? Carbon::parse($inst->end_date)->addDays(3)
                : null;

            foreach ($inst->enrollments as $enr) {
                $reportStatus = $enr->report?->status ?? null;

                // Draft / null = pending
                if (in_array($reportStatus, [null, 'Draft'])) {
                    $pendingReports++;

                    // Late if deadline already passed
                    if ($deadline && now()->gt($deadline)) {
                        $lateReports++;
                    }
                }
            }
        }

        // Restricted students across all teacher's instances
        $restrictedStudents = $allInstances->sum(
            fn($i) => $i->enrollments->where('restriction_flag', true)->count()
        );

        // ── Alerts ─────────────────────────────────────────────────────────
        $alerts = [];

        // Upcoming course endings (≤ 7 days away)
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

        // Pending reports reminder
        if ($pendingReports > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => '📝',
                'msg'  => $pendingReports . ' student report' . ($pendingReports > 1 ? 's' : '') . ' pending submission',
                'link' => route('teacher.reports.index'),
                'cta'  => 'Submit Reports',
            ];
        }

        // Late (overdue) reports
        if ($lateReports > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => '⚠️',
                'msg'  => $lateReports . ' report' . ($lateReports > 1 ? 's' : '') . ' overdue — past the 3-day deadline',
                'link' => route('teacher.reports.index'),
                'cta'  => 'Fix Now',
            ];
        }

        // Restricted students alert
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

    // ─── schedule ───────────────────────────────────────────────────────────
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

    // ─── courses ────────────────────────────────────────────────────────────
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

    // ─── course show ────────────────────────────────────────────────────────
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