<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentCareService;
use App\Models\Enrollment\WaitingList;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\Core\Branch;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Student\Student;
use App\Models\Student\StudentPhone;


class StudentCareController extends Controller
{
    protected $service;

    public function __construct(StudentCareService $service)
    {
        $this->service = $service;
    }

    public function waitingList()
    {
        $waiting = WaitingList::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level'
        ])->get();

        $instances = CourseInstance::with([
            'courseTemplate',
            'teacher',
            'enrollments',
        ])
        ->withCount([
            'sessions as completed_sessions_count' => fn($q) => $q->where('status', 'Completed'),
        ])
        ->whereIn('status', ['Upcoming', 'Active'])
        ->get();

        return view('student-care.waiting-list', compact('waiting', 'instances'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'waiting_id' => 'required|exists:waiting_list,waiting_id',
            'course_instance_id' => 'required|exists:course_instance,course_instance_id',
        ]);

        $waiting = WaitingList::with('enrollment')->findOrFail($request->waiting_id);

        $instance = CourseInstance::with('enrollments')
            ->withCount([
                'sessions as completed_sessions_count' => fn($q) => $q->where('status', 'Completed'),
            ])
            ->findOrFail($request->course_instance_id);

        // Business rule: enrollment type must match the course instance type.
        // A Private student cannot be placed into a Group course and vice-versa.
        $studentType = $waiting->enrollment->enrollment_type;   // 'Group' | 'Private'
        $courseType  = $instance->type;                          // 'Group' | 'Private'

        if ($studentType && $courseType && $studentType !== $courseType) {
            return back()->with('error',
                "Type mismatch: this is a {$studentType} student and cannot be assigned to a {$courseType} course. " .
                "Please assign them to a {$studentType} course instance."
            );
        }

        // Business rule: A student cannot join a group course that has completed more than 3 sessions
        if ($instance->completed_sessions_count > 2) {
            return back()->with('error',
                'This course has completed ' . $instance->completed_sessions_count .
                ' sessions. Students can only join courses that have completed 3 sessions or less.'
            );
        }

        if ($instance->isFull()) {
            return back()->with('error', 'Instance is full');
        }

        $waiting->enrollment->update([
            'course_instance_id' => $instance->course_instance_id,
            'status'             => 'Active',
        ]);

        $sessions = \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->orderBy('session_number')
            ->get();

        $schedules = \App\Models\Finance\InstallmentSchedule::where('enrollment_id', $waiting->enrollment->enrollment_id)
            ->where('status', 'Pending')
            ->orderBy('installment_number')
            ->get();

        foreach ($schedules as $i => $schedule) {
            $session = $sessions[$i] ?? null;
            if ($session) {
                $schedule->update(['due_date' => $session->session_date]);
            }
        }

        $waiting->update([
            'status' => 'Assigned'
        ]);

        return back()->with('success', 'Student assigned successfully');
    }

    /**
     * Cancel a waiting-list entry.
     * Marks the waiting record as Cancelled so it drops out of the active queue.
     * The enrollment itself is left intact (still awaiting a course) unless you
     * choose to cancel it too — here we only cancel the waiting-list placement.
     */
    public function cancelWaiting(Request $request, $id)
    {
        $waiting = WaitingList::with('enrollment')->findOrFail($id);

        if ($waiting->status === 'Assigned') {
            return back()->with('error', 'This student has already been assigned and cannot be cancelled.');
        }

        if ($waiting->status === 'Cancelled') {
            return back()->with('error', 'This waiting-list entry is already cancelled.');
        }

        $waiting->update(['status' => 'Cancelled']);

        return back()->with('success', 'Waiting-list entry cancelled.');
    }

    public function show($id)
    {
        $instance = CourseInstance::with([
            'courseTemplate',
            'teacher',
            'branch',
            'enrollments.student.phones',
            'level',
            'sublevel',
            'enrollments.student'
        ])->findOrFail($id);

        return view('student-care.course-instances.show', compact('instance'));
    }
    
    public function outstanding()
    {
        $calculator = app(\App\Services\BalanceCalculator::class);

        $allEnrollments = \App\Models\Enrollment\Enrollment::with([
            'student',
            'courseTemplate',
            'courseInstance.courseTemplate',
            'courseInstance.patch',
            'patch',
            'paymentPlan',
            'createdByCs',
            'installmentSchedules' => fn($q) => $q->orderBy('due_date'),
            'restrictionLogs'      => fn($q) => $q->whereNull('released_at'),
            'financialTransactions',
        ])
        ->whereIn('status', ['Active', 'Restricted', 'Waiting'])
        ->whereNotNull('final_price')
        ->get();

        $enrollments = $allEnrollments->map(function ($e) use ($calculator) {
            $data = $calculator->calculate($e);
            $e->total_fees        = $data['total_fees'];
            $e->total_paid        = $data['net_paid'];
            $e->remaining_balance = $data['remaining_balance'];
            return $e;
        });

        $withBalance    = $enrollments->filter(fn($e) => $e->remaining_balance > 0);
        $finishedEnrollments = $enrollments->filter(fn($e) => $e->remaining_balance == 0);

        $stats = [
            'total_outstanding' => $withBalance->sum('remaining_balance'),
            'count'             => $withBalance->count(),
            'restricted'        => $withBalance->where('status', 'Restricted')->count(),
            'overdue'           => $withBalance->filter(fn($e) =>
                $e->installmentSchedules->where('status', 'Overdue')->isNotEmpty()
            )->count(),
            'finished_count'    => $finishedEnrollments->count(),
        ];

        return view('student-care.outstanding', compact('enrollments', 'withBalance', 'finishedEnrollments', 'stats'));
    }

    public function postponed()
    {
        $groupPostponed = \App\Models\Enrollment\Postponement::with([
            'enrollment.student',
            'enrollment.courseInstance.courseTemplate',
            'enrollment.courseInstance.sessions',
            'enrollment.attendances',
            'createdBy',
        ])
        ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Group'))
        ->whereIn('status', ['Active', 'Expired'])
        ->orderBy('status')
        ->orderByDesc('created_at')
        ->get();

        $privatePostponed = \App\Models\Enrollment\Postponement::with([
            'enrollment.student',
            'enrollment.courseInstance.courseTemplate',
            'createdBy',
        ])
        ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Private'))
        ->whereIn('status', ['Active', 'Expired'])
        ->orderBy('status')
        ->orderByDesc('created_at')
        ->get();

        $stats = [
            'active'   => \App\Models\Enrollment\Postponement::where('status', 'Active')->count(),
            'expired'  => \App\Models\Enrollment\Postponement::where('status', 'Expired')->count(),
            'returned' => \App\Models\Enrollment\Postponement::where('status', 'Returned')->count(),
            'expiring_soon' => \App\Models\Enrollment\Postponement::where('status', 'Active')
                ->where('expected_return_date', '<=', now()->addDays(7))->count(),
        ];

        return view('student-care.postponed', compact('groupPostponed', 'privatePostponed', 'stats'));
    }

    public function resumePostponement(Request $request, $id)
    {
        $postponement = \App\Models\Enrollment\Postponement::with('enrollment')->findOrFail($id);

        if ($postponement->status !== 'Active') {
            return back()->with('error', 'Postponement is not active.');
        }

        $postponement->update([
            'status'             => 'Returned',
            'actual_return_date' => now()->toDateString(),
        ]);

        $postponement->enrollment->update(['status' => 'Active']);

        return back()->with('success', 'Student resumed successfully.');
    }

    public function expirePostponement($id)
    {
        $postponement = \App\Models\Enrollment\Postponement::with('enrollment')->findOrFail($id);

        $postponement->update(['status' => 'Expired']);

        $postponement->enrollment->update([
            'status' => 'Expired',
        ]);

        return back()->with('success', 'Postponement marked as expired.');
    }

    public function dashboard()
    {
        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')
            ->latest('start_date')->first();

        $upcomingPatch = \App\Models\Academic\Patch::where('status', 'Upcoming')
            ->oldest('start_date')->first();

        $activeCourses   = \App\Models\Academic\CourseInstance::where('status', 'Active')->count();
        $upcomingCourses = \App\Models\Academic\CourseInstance::where('status', 'Upcoming')->count();
        $totalStudents   = \App\Models\Enrollment\Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = \App\Models\Enrollment\Enrollment::where('status', 'Restricted')->count();
        $postponedStudents  = \App\Models\Enrollment\Postponement::where('status', 'Active')->count();
        $waitingList        = \App\Models\Enrollment\WaitingList::where('status', 'Active')->count();

        $endingSoon = \App\Models\Academic\CourseInstance::where('status', 'Active')
            ->where('end_date', '<=', now()->addDays(7))
            ->where('end_date', '>=', now())
            ->with(['courseTemplate', 'teacher.employee'])
            ->get();

        $fullGroups = \App\Models\Academic\CourseInstance::where('status', 'Active')
            ->where('type', 'Group')
            ->withCount('enrollments')
            ->get()
            ->filter(fn($i) => $i->enrollments_count >= $i->capacity);

        $expiredPostponements = \App\Models\Enrollment\Postponement::where('status', 'Active')
            ->where('expected_return_date', '<', now())
            ->with(['enrollment.student', 'enrollment.courseInstance.courseTemplate'])
            ->get();

        $expiringSoon = \App\Models\Enrollment\Postponement::where('status', 'Active')
            ->where('expected_return_date', '>=', now())
            ->where('expected_return_date', '<=', now()->addDays(7))
            ->with(['enrollment.student'])
            ->get();

        $nearCompletionGroup = \App\Models\Enrollment\Enrollment::where('status', 'Active')
            ->whereHas('courseInstance', fn($q) => $q->where('type', 'Group')->where('status', 'Active'))
            ->with(['student', 'courseInstance.courseTemplate', 'courseInstance.sessions'])
            ->get()
            ->filter(function ($e) {
                $total     = $e->courseInstance?->sessions?->count() ?? 0;
                $completed = $e->courseInstance?->sessions?->where('status','Completed')->count() ?? 0;
                $remaining = $total - $completed;
                return $remaining <= 1 && $total > 0;
            });

        $nearCompletionPrivate = \App\Models\Enrollment\Enrollment::where('status', 'Active')
            ->where('enrollment_type', 'Private')
            ->whereNotNull('hours_remaining')
            ->where('hours_remaining', '<=', 4)
            ->with(['student', 'courseInstance.courseTemplate'])
            ->get();

        $recentInstances = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'teacher.employee', 'enrollments'
        ])->where('status', 'Active')
        ->latest()
        ->limit(5)
        ->get();

        return view('student-care.dashboard', compact(
            'currentPatch', 'upcomingPatch',
            'activeCourses', 'upcomingCourses', 'totalStudents',
            'restrictedStudents', 'postponedStudents', 'waitingList',
            'endingSoon', 'fullGroups', 'expiredPostponements', 'expiringSoon',
            'nearCompletionGroup', 'nearCompletionPrivate',
            'recentInstances'
        ));
    }

    public function nearCompletion()
    {
        $nearCompletionPrivate = Enrollment::where('status', 'Active')
            ->where('enrollment_type', 'Private')
            ->whereNotNull('hours_remaining')
            ->where('hours_remaining', '<=', 4)
            ->with([
                'student.phones',
                'student.lead',
                'courseTemplate',
                'level',
                'privateBundle',
                'teacher',
            ])
            ->orderBy('hours_remaining')
            ->get();

        $nearCompletionGroup = Enrollment::where('status', 'Active')
            ->where('enrollment_type', 'Group')
            ->whereHas('courseInstance', fn($q) => $q->where('status', 'Active'))
            ->with([
                'student.phones',
                'student.lead',
                'courseInstance.courseTemplate',
                'courseInstance.level',
                'courseInstance.sessions',
            ])
            ->get()
            ->filter(function ($e) {
                $total     = $e->courseInstance?->sessions?->count() ?? 0;
                $completed = $e->courseInstance?->sessions?->where('status', 'Completed')->count() ?? 0;
                $remaining = $total - $completed;
                return $remaining <= 2 && $total > 0;
            })
            ->values();

        $privateCount = $nearCompletionPrivate->count();
        $groupCount   = $nearCompletionGroup->count();

        return view('leads.near-completion', compact(
            'nearCompletionPrivate', 'nearCompletionGroup',
            'privateCount', 'groupCount'
        ));
}

    /**
     * Continue Package — create the next prepaid enrolment in a level package.
     *
     * A package covers several units. The unit is a sublevel when the course
     * has sublevels, otherwise a level. This finds the next unit after the
     * current enrolment's, creates a new FREE enrolment for it (final_price 0),
     * decrements the remaining prepaid units, and closes out the current one's
     * package counter.
     */
    public function continuePackage($enrollmentId)
    {
        $current = Enrollment::with(['level', 'sublevel', 'courseTemplate'])
            ->findOrFail($enrollmentId);

        if (!$current->package_id || (int) $current->package_units_remaining <= 0) {
            return back()->with('error', 'This enrollment has no remaining package units.');
        }

        // Work out the next unit (sublevel within/after the current level, or
        // the next level for courses without sublevels).
        $next = $this->resolveNextPackageUnit($current);

        if (!$next) {
            return back()->with('error', 'No further levels/sublevels available in this course for the package.');
        }

        // Create the next enrolment — FREE (already paid via the package).
        $newEnrollment = Enrollment::create([
            'student_id'              => $current->student_id,
            'course_template_id'      => $current->course_template_id,
            'course_instance_id'      => null, // assigned later by Student Care
            'level_id'                => $next['level_id'],
            'sublevel_id'             => $next['sublevel_id'],
            'patch_id'                => $current->patch_id,
            'branch_id'               => $current->branch_id,
            'teacher_id'              => null,
            'enrollment_type'         => 'Group',
            'delivery_mood'           => $current->delivery_mood,
            'final_price'             => 0,
            'payment_plan_id'         => $current->payment_plan_id,
            'bundle_id'               => null,
            'package_id'              => $current->package_id,
            'package_units_remaining' => (int) $current->package_units_remaining - 1,
            'hours_remaining'         => null,
            'discount_value'          => 0,
            'status'                  => 'Waiting',
            'created_by_cs_id'        => auth()->user()->employee?->employee_id ?? null,
        ]);

        // The current enrolment has handed off its package continuation.
        $current->package_units_remaining = 0;
        $current->save();

        return back()->with('success',
            'Next package level created (free). The student can now be assigned to a class for it.');
    }

    /**
     * Given the current enrolment, return the next package unit as
     * ['level_id' => .., 'sublevel_id' => ..|null], or null if none remain.
     */
    private function resolveNextPackageUnit(Enrollment $current): ?array
    {
        $courseId = $current->course_template_id;

        // Does the current level have sublevels? If so, the package is billed
        // by sublevel; otherwise by level.
        $currentLevelHasSublevels = $current->level_id
            ? Sublevel::where('level_id', $current->level_id)->exists()
            : false;

        if ($currentLevelHasSublevels && $current->sublevel_id) {
            // 1) Try the next sublevel within the SAME level.
            $currentSub = Sublevel::find($current->sublevel_id);
            if ($currentSub) {
                $nextSub = Sublevel::where('level_id', $current->level_id)
                    ->where('sublevel_order', '>', $currentSub->sublevel_order)
                    ->orderBy('sublevel_order')
                    ->first();
                if ($nextSub) {
                    return ['level_id' => $current->level_id, 'sublevel_id' => $nextSub->sublevel_id];
                }
            }
            // 2) Exhausted this level's sublevels → first sublevel of the NEXT level.
            $nextLevel = $this->nextLevel($courseId, $current->level_id);
            if ($nextLevel) {
                $firstSub = Sublevel::where('level_id', $nextLevel->level_id)
                    ->orderBy('sublevel_order')
                    ->first();
                return [
                    'level_id'    => $nextLevel->level_id,
                    'sublevel_id' => $firstSub?->sublevel_id, // may be null if next level has none
                ];
            }
            return null;
        }

        // Course without sublevels → simply the next level.
        $nextLevel = $this->nextLevel($courseId, $current->level_id);
        if ($nextLevel) {
            return ['level_id' => $nextLevel->level_id, 'sublevel_id' => null];
        }
        return null;
    }

    /**
     * Next level in a course by level_order, after the given level.
     */
    private function nextLevel($courseId, $currentLevelId): ?Level
    {
        $currentLevel = Level::find($currentLevelId);
        if (!$currentLevel) return null;

        return Level::where('course_template_id', $courseId)
            ->where('level_order', '>', $currentLevel->level_order)
            ->orderBy('level_order')
            ->first();
    }
}