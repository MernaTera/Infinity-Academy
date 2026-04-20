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

}
