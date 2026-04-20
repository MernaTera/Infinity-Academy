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
            'enrollments'
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

        $instances = CourseInstance::with(['courseTemplate','teacher','enrollments'])
            ->where('status', 'Upcoming')
            ->where('course_template_id', $waiting->enrollment->course_template_id)
            ->get();
        $instance = CourseInstance::with('enrollments')->findOrFail($request->course_instance_id);

        if ($instance->isFull()) {
            return back()->with('error', 'Instance is full');
        }

        $waiting->enrollment->update([
            'course_instance_id' => $instance->course_instance_id,
            'status' => 'Active'
        ]);

        $waiting->update([
            'status' => 'Assigned'
        ]);

        return back()->with('success', 'Student assigned successfully');
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
        $allEnrollments = \App\Models\Enrollment\Enrollment::with([
            'student',
            'courseInstance.courseTemplate',
            'courseInstance.patch',
            'paymentPlan',
            'createdByCs',
            'installmentSchedules' => fn($q) => $q->orderBy('due_date'),
            'financialTransactions',
        ])
        ->whereIn('status', ['Active', 'Restricted'])
        ->get();

        $enrollments = $allEnrollments->filter(function ($e) {
            $paid     = $e->financialTransactions
                ->whereIn('transaction_type', ['Payment', 'Installment'])
                ->sum('amount');
            $refunded = $e->financialTransactions
                ->where('transaction_type', 'Refund')
                ->sum('amount');
            $balance  = $e->final_price - ($paid - $refunded);
            $e->remaining_balance = $balance;
            $e->total_paid        = $paid - $refunded;
            return $balance > 0;
        });

        $stats = [
            'total_outstanding' => $enrollments->sum('remaining_balance'),
            'count'             => $enrollments->count(),
            'restricted'        => $enrollments->where('status', 'Restricted')->count(),
            'overdue'           => $enrollments->filter(fn($e) =>
                $e->installmentSchedules->where('status', 'Overdue')->isNotEmpty()
            )->count(),
        ];

        return view('student-care.outstanding', compact('enrollments', 'stats'));
    }

    public function postponed()
    {
        // Group postponements
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

        // Private postponements
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

        // ── Academic Status ──
        $activeCourses   = \App\Models\Academic\CourseInstance::where('status', 'Active')->count();
        $upcomingCourses = \App\Models\Academic\CourseInstance::where('status', 'Upcoming')->count();
        $totalStudents   = \App\Models\Enrollment\Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = \App\Models\Enrollment\Enrollment::where('status', 'Restricted')->count();
        $postponedStudents  = \App\Models\Enrollment\Postponement::where('status', 'Active')->count();
        $waitingList        = \App\Models\Enrollment\WaitingList::where('status', 'Active')->count();

        // ── Alerts ──
        // Courses ending within 7 days
        $endingSoon = \App\Models\Academic\CourseInstance::where('status', 'Active')
            ->where('end_date', '<=', now()->addDays(7))
            ->where('end_date', '>=', now())
            ->with(['courseTemplate', 'teacher.employee'])
            ->get();

        // Full groups
        $fullGroups = \App\Models\Academic\CourseInstance::where('status', 'Active')
            ->where('type', 'Group')
            ->withCount('enrollments')
            ->get()
            ->filter(fn($i) => $i->enrollments_count >= $i->capacity);

        // Expired postponements not yet handled
        $expiredPostponements = \App\Models\Enrollment\Postponement::where('status', 'Active')
            ->where('expected_return_date', '<', now())
            ->with(['enrollment.student', 'enrollment.courseInstance.courseTemplate'])
            ->get();

        // Expiring soon postponements (within 7 days)
        $expiringSoon = \App\Models\Enrollment\Postponement::where('status', 'Active')
            ->where('expected_return_date', '>=', now())
            ->where('expected_return_date', '<=', now()->addDays(7))
            ->with(['enrollment.student'])
            ->get();

        // ── Retention ──
        // Group: students in last session
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

        // Private: students with last 4 hours
        $nearCompletionPrivate = \App\Models\Enrollment\Enrollment::where('status', 'Active')
            ->where('enrollment_type', 'Private')
            ->whereNotNull('hours_remaining')
            ->where('hours_remaining', '<=', 4)
            ->with(['student', 'courseInstance.courseTemplate'])
            ->get();

        // ── Recent Activity ──
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

}
