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
use App\Models\Enrollment\Postponement;
use App\Models\Leads\Lead;


class StudentCareController extends Controller
{
    protected $service;

    public function __construct(StudentCareService $service)
    {
        $this->service = $service;
    }

    public function waitingList()
    {
        $waiting = WaitingList::whereHas('enrollment')->with([
            'enrollment.student.phones',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.levelPackage',
            'enrollment.teacher.employee',
        ])->get()
          ->sortByDesc(fn($w) => $w->enrollment->enrollment_id)
          ->values();

        $instances = CourseInstance::with([
            'courseTemplate',
            'level',
            'sublevel',
            'teacher',
            'activeEnrollments',
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
            'note' => 'nullable|string|max:2000',
        ]);

        $waiting = WaitingList::with('enrollment')->findOrFail($request->waiting_id);

        if ($waiting->enrollment === null) {
            return back()->with('error', 'This entry is not available for your branch.');
        }

        $instance = CourseInstance::with('enrollments')
            ->withCount([
                'sessions as completed_sessions_count' => fn($q) => $q->where('status', 'Completed'),
            ])
            ->findOrFail($request->course_instance_id);

        $studentType = $waiting->enrollment->enrollment_type;   // 'Group' | 'Private'
        $courseType  = $instance->type;                          // 'Group' | 'Private'

        if ($studentType && $courseType && $studentType !== $courseType) {
            return back()->with('error',
                "Type mismatch: this is a {$studentType} student and cannot be assigned to a {$courseType} course. " .
                "Please assign them to a {$studentType} course instance."
            );
        }

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

        if (filled($request->note)) {
            \App\Models\Enrollment\EnrollmentNote::create([
                'enrollment_id'          => $waiting->enrollment->enrollment_id,
                'note'                   => trim($request->note),
                'created_by_employee_id' => \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id'),
            ]);
        }

        return back()->with('success', 'Student assigned successfully');
    }


    public function cancelWaiting(Request $request, $id)
    {
        $waiting = WaitingList::with('enrollment')->findOrFail($id);

        if ($waiting->enrollment === null) {
            return back()->with('error', 'This entry is not available for your branch.');
        }

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
        return view('student-care.postponed', array_merge($this->postponedData(), [
            'canResume'  => true,
            'canExpire'  => true,
            'actionBase' => url('student-care/postponed'),
        ]));
    }


    public function csPostponed()
    {
        return view('student-care.postponed', array_merge($this->postponedData(), [
            'canResume'  => false,
            'canExpire'  => false,
            'actionBase' => url('student-care/postponed'),
        ]));
    }


    private function postponedData(): array
    {
        $with = [
            'enrollment.student.phones',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.privateBundle',
            'enrollment.levelPackage',
            'enrollment.attendances',
            'enrollment.courseInstance.courseTemplate',
            'createdBy',
        ];

        $groupPostponed = Postponement::with($with)
            ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Group'))
            ->whereIn('status', ['Active', 'Expired'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->get();

        $privatePostponed = Postponement::with($with)
            ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Private'))
            ->whereIn('status', ['Active', 'Expired'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'active'        => Postponement::where('status', 'Active')
                                    ->whereHas('enrollment', fn($q) => $q)->count(),
            'expired'       => Postponement::where('status', 'Expired')
                                    ->whereHas('enrollment', fn($q) => $q)->count(),
            'returned'      => Postponement::where('status', 'Returned')
                                    ->whereHas('enrollment', fn($q) => $q)->count(),
            'expiring_soon' => Postponement::where('status', 'Active')
                                    ->whereHas('enrollment', fn($q) => $q)
                                    ->whereDate('expected_return_date', '<=', now()->addDays(7))
                                    ->count(),
        ];

        return compact('groupPostponed', 'privatePostponed', 'stats');
    }


    public function resumePostponement($id)
    {
        $postponement = Postponement::with('enrollment')->whereHas('enrollment')->findOrFail($id);

        if ($postponement->status !== 'Active') {
            return back()->with('error', 'Only an active postponement can be resumed.');
        }

        $enrollment = $postponement->enrollment;
        if (!$enrollment || $enrollment->status !== 'Postponed') {
            return back()->with('error', 'This enrollment is no longer postponed.');
        }

        $employeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');
        $waiting    = null;

        $resumed = \Illuminate\Support\Facades\DB::transaction(function () use ($postponement, $enrollment, $employeeId, &$waiting) {
            $locked = Postponement::whereKey($postponement->postponement_id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'Active') {
                return false;
            }

            $currentPatch = Patch::where('status', 'Active')->orderByDesc('start_date')->first();

            $locked->update([
                'status'             => 'Returned',
                'actual_return_date' => now()->toDateString(),
            ]);

            $oldStatus = $enrollment->status;
            $enrollment->update([
                'status'             => 'Waiting',
                'course_instance_id' => null,
            ]);

            $existing  = WaitingList::where('enrollment_id', $enrollment->enrollment_id)->first();
            $resumeTag = 'Resumed from postponement on ' . now()->format('d M Y');
            $notes     = trim(($existing?->notes ? $existing->notes . ' | ' : '') . $resumeTag);

            $waiting = WaitingList::updateOrCreate(
                ['enrollment_id' => $enrollment->enrollment_id],
                [
                    'requested_patch_id'      => $currentPatch?->patch_id,
                    'preferred_type'          => 'Current_Patch',
                    'preferred_delivery_type' => $enrollment->enrollment_type ?? 'Group',
                    'preferred_delivery_mood' => $enrollment->delivery_mood ?? 'Offline',
                    'preferred_start_date'    => null,
                    'preferred_days'          => $existing?->preferred_days,
                    'status'                  => 'Active',
                    'notes'                   => $notes,
                    'created_by_cs_id'        => $existing?->created_by_cs_id ?? $employeeId,
                ]
            );

            \App\Services\AuditService::updated('postponement', $locked->postponement_id, 'status', 'Active', 'Returned');
            \App\Services\AuditService::updated('enrollment', $enrollment->enrollment_id, 'status', $oldStatus, 'Waiting');

            return true;
        });

        if (!$resumed) {
            return back()->with('error', 'This postponement was already processed.');
        }

        if ($waiting) {
            event(new \App\Events\WaitingListUpdated($waiting));
        }

        $name = $enrollment->student?->full_name ?? 'The student';

        return back()->with('success', "{$name} is back on the waiting list (Current Patch) with the same course, details and notes.");
    }

 
    public function expirePostponement($id)
    {
        $postponement = Postponement::with('enrollment')->whereHas('enrollment')->findOrFail($id);

        if ($postponement->status !== 'Active') {
            return back()->with('error', 'Only an active postponement can be expired.');
        }

        $postponement->update(['status' => 'Expired']);

        if ($postponement->enrollment) {
            $postponement->enrollment->update(['status' => 'Expired']);
        }

        return back()->with('success', 'Postponement marked as expired. The enrollment is forfeited with no refund.');
    }


    public function addEnrollmentNote(Request $request, $enrollmentId)
    {
        $request->validate(['note' => 'required|string|max:2000']);

        $enrollment = Enrollment::findOrFail($enrollmentId);

        \App\Models\Enrollment\EnrollmentNote::create([
            'enrollment_id'          => $enrollment->enrollment_id,
            'note'                   => trim($request->note),
            'created_by_employee_id' => \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id'),
        ]);

        return back()->with('success', 'Note added.');
    }

    public function updateEnrollmentNote(Request $request, $noteId)
    {
        $request->validate(['note' => 'required|string|max:2000']);

        $note = \App\Models\Enrollment\EnrollmentNote::with('enrollment')->findOrFail($noteId);

        if ($note->enrollment === null) {
            return back()->with('error', 'This note is not available for your branch.');
        }

        $note->update(['note' => trim($request->note)]);

        return back()->with('success', 'Note updated.');
    }

    public function deleteEnrollmentNote($noteId)
    {
        $note = \App\Models\Enrollment\EnrollmentNote::with('enrollment')->findOrFail($noteId);

        if ($note->enrollment === null) {
            return back()->with('error', 'This note is not available for your branch.');
        }

        $note->delete();

        return back()->with('success', 'Note deleted.');
    }

    public function dashboard()
    {
        $me = \App\Models\HR\Employee::where('user_id', auth()->id())->first();

        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')
            ->latest('start_date')->first();

        $upcomingPatch = \App\Models\Academic\Patch::where('status', 'Upcoming')
            ->oldest('start_date')->first();

        $activeCourses   = \App\Models\Academic\CourseInstance::where('status', 'Active')->count();
        $upcomingCourses = \App\Models\Academic\CourseInstance::where('status', 'Upcoming')->count();
        $totalStudents   = \App\Models\Enrollment\Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = \App\Models\Enrollment\Enrollment::where('status', 'Restricted')->count();
        $postponedStudents  = \App\Models\Enrollment\Postponement::where('status', 'Active')->whereHas('enrollment')->count();
        $waitingList        = \App\Models\Enrollment\WaitingList::whereHas('enrollment')->where('status', 'Active')->count();

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
            ->whereHas('enrollment')
            ->where('expected_return_date', '<', now())
            ->with(['enrollment.student', 'enrollment.courseInstance.courseTemplate'])
            ->get();

        $expiringSoon = \App\Models\Enrollment\Postponement::where('status', 'Active')
            ->whereHas('enrollment')
            ->where('expected_return_date', '>=', now())
            ->where('expected_return_date', '<=', now()->addDays(7))
            ->with(['enrollment.student'])
            ->get();

        $nearCompletionGroup = \App\Models\Enrollment\Enrollment::where('status', 'Active')
            ->whereHas('courseInstance', fn($q) => $q->where('type', 'Group')->where('status', 'Active'))
            ->with(['student', 'courseInstance.courseTemplate', 'courseInstance.sessions'])
            ->get()
            ->filter(function ($e) {
                $sessions  = $e->courseInstance?->sessions ?? collect();
                $total     = $sessions->where('status', '!=', 'Cancelled')->count();
                $completed = $sessions->where('status', 'Completed')->count();
                $remaining = $total - $completed;
                return $total > 0 && $remaining <= 1;
            })
            ->reject(fn($e) => $this->hasRenewed($e))
            ->values();

        $nearCompletionPrivate = \App\Models\Enrollment\Enrollment::where('status', 'Active')
            ->where('enrollment_type', 'Private')
            ->whereNotNull('hours_remaining')
            ->where('hours_remaining', '<=', 4)
            ->with(['student', 'courseInstance.courseTemplate'])
            ->get()
            ->reject(fn($e) => $this->hasRenewed($e))
            ->values();
            
        $recentInstances = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'teacher.employee', 'enrollments'
        ])->where('status', 'Active')
        ->latest()
        ->limit(5)
        ->get();

        return view('student-care.dashboard', compact(
            'me',
            'currentPatch', 'upcomingPatch',
            'activeCourses', 'upcomingCourses', 'totalStudents',
            'restrictedStudents', 'postponedStudents', 'waitingList',
            'endingSoon', 'fullGroups', 'expiredPostponements', 'expiringSoon',
            'nearCompletionGroup', 'nearCompletionPrivate',
            'recentInstances'
        ));
    }

    public function nearCompletion(Request $request)
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
                'privateBundle' => fn($q) => $q->withoutGlobalScope('branch'),
                'teacher',
            ])
            ->orderBy('hours_remaining')
            ->get()
            ->reject(fn($e) => $this->hasRenewed($e))
            ->values();

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
                $sessions  = $e->courseInstance?->sessions ?? collect();
                $total     = $sessions->where('status', '!=', 'Cancelled')->count();
                $completed = $sessions->where('status', 'Completed')->count();
                $remaining = $total - $completed;

                if ($total <= 0 || $remaining > 2) {
                    return false;
                }

                if ($e->package_id) {
                    return (int) $e->package_units_remaining <= 0;
                }

                return app(\App\Services\PackageProgressionService::class)->nextUnit($e) === null;
            })
            ->reject(fn($e) => $this->hasRenewed($e))
            ->values();

        $privateCount = $nearCompletionPrivate->count();
        $groupCount   = $nearCompletionGroup->count();

        $tab = $request->query('tab');
        if (!in_array($tab, ['private', 'group'], true)) {
            $tab = ($privateCount === 0 && $groupCount > 0) ? 'group' : 'private';
        }

        $search = trim((string) $request->query('q', ''));

        $nearCompletionPrivate = \App\Support\PaginateCollection::make(
            \App\Support\EnrollmentSearch::filter($nearCompletionPrivate, $search), 25
        );
        $nearCompletionGroup = \App\Support\PaginateCollection::make(
            \App\Support\EnrollmentSearch::filter($nearCompletionGroup, $search), 25
        );

        return view('leads.near-completion', compact(
            'nearCompletionPrivate', 'nearCompletionGroup',
            'privateCount', 'groupCount', 'tab', 'search'
        ));
    }

    private function hasRenewed($e): bool
    {
        return Enrollment::where('student_id', $e->student_id)
            ->where('enrollment_type', $e->enrollment_type)
            ->where('enrollment_id', '>', $e->enrollment_id)
            ->whereNotIn('status', ['Cancelled'])
            ->exists();
    }


    public function continuePackage($enrollmentId)
    {
        $current = Enrollment::findOrFail($enrollmentId);

        if (!$current->package_id || (int) $current->package_units_remaining <= 0) {
            return back()->with('error', 'This enrollment has no remaining package units.');
        }

        $next = app(\App\Services\PackageProgressionService::class)->advance($current);

        if (!$next) {
            return back()->with('error', 'No further levels/sublevels available in this course for the package.');
        }

        return back()->with('success',
            'Next package level created (free) and added to the waiting list.');
    }
}