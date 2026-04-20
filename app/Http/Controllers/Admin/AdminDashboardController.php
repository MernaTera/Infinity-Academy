<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseInstance;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\HR\Employee;
use App\Models\Leads\Lead;
use App\Models\Finance\CsTarget;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $currentPatch = Patch::where('status', 'Active')->latest('start_date')->first();
        $upcomingPatch = Patch::where('status', 'Upcoming')->latest('start_date')->first();

        $patchRevenue = $currentPatch
            ? FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
                ->where('patch_id', $currentPatch->patch_id)->sum('amount')
            : 0;

        $totalRevenue = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
            ->sum('amount');

        $totalRefunded = FinancialTransaction::where('transaction_type', 'Refund')->sum('amount');

        $allEnrollments = Enrollment::with('financialTransactions')
            ->whereIn('status', ['Active', 'Restricted'])->get();

        $totalOutstanding = $allEnrollments->sum(function ($e) {
            $paid     = $e->financialTransactions->whereIn('transaction_type', ['Payment','Installment'])->sum('amount');
            $refunded = $e->financialTransactions->where('transaction_type','Refund')->sum('amount');
            return max(0, $e->final_price - ($paid - $refunded));
        });

        $pendingInstallments = InstallmentSchedule::where('status', 'Pending')->count();
        $overdueInstallments = InstallmentSchedule::where('status', 'Overdue')->count();

        $activeCourses   = CourseInstance::where('status', 'Active')->count();
        $upcomingCourses = CourseInstance::where('status', 'Upcoming')->count();
        $totalStudents   = Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = Enrollment::where('status', 'Restricted')->count();

        $waitingList = \App\Models\Enrollment\WaitingList::where('status', 'Active')->count();

        $instances = CourseInstance::where('status', 'Active')
            ->withCount('enrollments')->get();
        $avgCapacity = $instances->count() > 0
            ? round($instances->avg(fn($i) =>
                $i->capacity > 0 ? ($i->enrollments_count / $i->capacity * 100) : 0))
            : 0;

        $csEmployees = Employee::with(['user', 'csTargets' => fn($q) =>
            $q->where('patch_id', $currentPatch?->patch_id)])
            ->whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')
            ->get()
            ->map(function ($emp) use ($currentPatch) {
                $revenue = RevenueSplit::where('employee_id', $emp->employee_id)
                    ->where('patch_id', $currentPatch?->patch_id)
                    ->sum('amount_allocated');
                $target  = $emp->csTargets->first()?->target_amount ?? 0;
                $emp->patch_revenue = $revenue;
                $emp->target        = $target;
                $emp->achievement   = $target > 0 ? round($revenue / $target * 100) : 0;
                return $emp;
            })
            ->sortByDesc('patch_revenue');

        $totalTarget   = $csEmployees->sum('target');
        $totalAchieved = $csEmployees->sum('patch_revenue');
        $targetPct     = $totalTarget > 0 ? round($totalAchieved / $totalTarget * 100) : 0;

        $totalEmployees   = Employee::where('status', 'Active')->count();
        $totalTeachers    = Employee::whereHas('user.role', fn($q) =>
            $q->where('role_name', 'Teacher'))->where('status', 'Active')->count();
        $pendingApprovals = \App\Models\Finance\InstallmentApprovalLog::where('status', 'Pending')->count();

        $recentEnrollments = Enrollment::with([
            'student', 'courseInstance.courseTemplate', 'createdByCs'
        ])->latest()->limit(8)->get();

        $revenueByCourse = FinancialTransaction::where('financial_transaction.patch_id', $currentPatch?->patch_id)
            ->whereIn('financial_transaction.transaction_type', ['Payment', 'Installment'])
            ->join('enrollment', 'financial_transaction.enrollment_id', '=', 'enrollment.enrollment_id')
            ->join('course_instance', 'enrollment.course_instance_id', '=', 'course_instance.course_instance_id')
            ->join('course_template', 'course_instance.course_template_id', '=', 'course_template.course_template_id')
            ->select('course_template.name', DB::raw('SUM(financial_transaction.amount) as total'))
            ->groupBy('course_template.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'currentPatch', 'upcomingPatch',
            'patchRevenue', 'totalRevenue', 'totalRefunded', 'totalOutstanding',
            'pendingInstallments', 'overdueInstallments',
            'activeCourses', 'upcomingCourses', 'totalStudents', 'restrictedStudents',
            'waitingList', 'avgCapacity',
            'csEmployees', 'totalTarget', 'totalAchieved', 'targetPct',
            'totalEmployees', 'totalTeachers', 'pendingApprovals',
            'recentEnrollments', 'revenueByCourse'
        ));
    }
}