<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseInstance;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\RefundRequest;
use App\Models\HR\Employee;
use App\Models\Leads\Lead;
use App\Models\Reports\Report;
use App\Models\Enrollment\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Period filter ───────────────────────────────────────────
        $period = $request->query('period', 'patch'); // day | week | month | patch | all

        $currentPatch  = Patch::where('status', 'Active')->latest('start_date')->first();
        $upcomingPatch = Patch::where('status', 'Upcoming')->latest('start_date')->first();

        [$from, $to] = $this->periodRange($period, $currentPatch);

        // ── Revenue by period ────────────────────────────────────────
        $revenueQuery = fn() => FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
            ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]));

        $patchRevenue = $currentPatch
            ? FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
                ->where('patch_id', $currentPatch->patch_id)->sum('amount')
            : 0;

        $periodRevenue = $revenueQuery()->sum('amount');
        $totalRevenue  = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])->sum('amount');
        $totalRefunded = FinancialTransaction::where('transaction_type', 'Refund')
            ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->sum('amount');

        // ── Payment methods breakdown ────────────────────────────────
        $paymentMethods = DB::table('deposit_payment')
            ->when($from, fn($q) => $q->whereBetween('deposit_payment.created_at', [$from, $to]))
            ->select('method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->get()
            ->keyBy('method');

        $cashRevenue      = $paymentMethods['Cash']?->total ?? 0;
        $instapayRevenue  = $paymentMethods['Instapay']?->total ?? 0;
        $vodafoneRevenue  = $paymentMethods['Vodafone_Cash']?->total ?? 0;
        $cardRevenue      = $paymentMethods['Card']?->total ?? 0;
        $transferRevenue  = $paymentMethods['Transfer']?->total ?? 0;

        $cashCount     = $paymentMethods['Cash']?->count ?? 0;
        $instapayCount = $paymentMethods['Instapay']?->count ?? 0;
        $vodafoneCount = $paymentMethods['Vodafone_Cash']?->count ?? 0;

        // ── Revenue trend (last 7 days) ──────────────────────────────
        $revenueTrend = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $trendDays   = [];
        $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $trendDays[]   = now()->subDays($i)->format('d M');
            $trendValues[] = $revenueTrend[$d]?->total ?? 0;
        }

        // ── Enrollments by period ────────────────────────────────────
        $periodEnrollments = Enrollment::when($from, fn($q) =>
            $q->whereBetween('created_at', [$from, $to])
        )->count();

        // ── Outstanding ──────────────────────────────────────────────
        $allActiveEnrollments = Enrollment::with('financialTransactions')
            ->whereIn('status', ['Active', 'Restricted'])->get();

        $totalOutstanding = $allActiveEnrollments->sum(function ($e) {
            $paid     = $e->financialTransactions->whereIn('transaction_type', ['Payment','Installment'])->sum('amount');
            $refunded = $e->financialTransactions->where('transaction_type','Refund')->sum('amount');
            return max(0, $e->final_price - ($paid - $refunded));
        });

        // ── Installments ─────────────────────────────────────────────
        $pendingInstallments = InstallmentSchedule::where('status', 'Pending')->count();
        $overdueInstallments = InstallmentSchedule::where('status', 'Overdue')->count();
        $pendingApprovals    = \App\Models\Finance\InstallmentApprovalLog::where('status', 'Pending')->count();

        // ── Refunds ──────────────────────────────────────────────────
        $pendingRefunds  = RefundRequest::where('status', 'Pending')->count();

        // ── Reports ──────────────────────────────────────────────────
        $pendingReports  = Report::where('status', 'Submitted')->count();

        // ── Academic ─────────────────────────────────────────────────
        $activeCourses      = CourseInstance::where('status', 'Active')->count();
        $upcomingCourses    = CourseInstance::where('status', 'Upcoming')->count();
        $completedCourses   = CourseInstance::where('status', 'Completed')
            ->when($from, fn($q) => $q->whereBetween('updated_at', [$from, $to]))->count();
        $totalStudents      = Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = Enrollment::where('status', 'Restricted')->count();
        $waitingList        = WaitingList::where('status', 'Active')->count();

        // ── Capacity utilisation ─────────────────────────────────────
        $instances = CourseInstance::where('status', 'Active')->withCount('enrollments')->get();
        $avgCapacity = $instances->count() > 0
            ? round($instances->avg(fn($i) =>
                $i->capacity > 0 ? ($i->enrollments_count / $i->capacity * 100) : 0))
            : 0;

        // ── CS Performance ───────────────────────────────────────────
        $csEmployees = Employee::whereHas('user.role', fn($q) =>
            $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')
            ->get()
            ->map(function ($emp) use ($currentPatch, $from, $to, $period) {
                // Revenue in selected period
                $revQ = RevenueSplit::where('employee_id', $emp->employee_id);
                if ($period === 'patch' && $currentPatch) {
                    $revQ->where('patch_id', $currentPatch->patch_id);
                } elseif ($from) {
                    $revQ->whereBetween('created_at', [$from, $to]);
                }
                $revenue = $revQ->sum('amount_allocated');

                // Target
                $target = 0;
                if ($period === 'patch' && $currentPatch) {
                    $target = \App\Models\Enrollment\CsTarget::where('employee_id', $emp->employee_id)
                        ->where('patch_id', $currentPatch->patch_id)->value('target_amount') ?? 0;
                } else {
                    $target = \App\Models\Enrollment\CsTarget::where('employee_id', $emp->employee_id)
                        ->where('month', now()->format('Y-m'))->value('target_amount') ?? 0;
                }

                // Registrations
                $regQ = Enrollment::where('created_by_cs_id', $emp->employee_id);
                if ($from) $regQ->whereBetween('created_at', [$from, $to]);
                $registrations = $regQ->count();

                $emp->patch_revenue  = $revenue;
                $emp->target         = $target;
                $emp->achievement    = $target > 0 ? round($revenue / $target * 100) : 0;
                $emp->registrations  = $registrations;
                $emp->leads_count    = Lead::where('owner_cs_id', $emp->employee_id)->count();
                return $emp;
            })
            ->sortByDesc('patch_revenue');

        $totalTarget   = $csEmployees->sum('target');
        $totalAchieved = $csEmployees->sum('patch_revenue');
        $targetPct     = $totalTarget > 0 ? round($totalAchieved / $totalTarget * 100) : 0;

        // ── HR ───────────────────────────────────────────────────────
        $totalEmployees = Employee::where('status', 'Active')->count();
        $totalTeachers  = Employee::whereHas('user.role', fn($q) =>
            $q->where('role_name', 'Teacher'))->where('status', 'Active')->count();

        // ── Revenue by course ────────────────────────────────────────
        $revenueByCourse = FinancialTransaction::query()
            ->whereIn('financial_transaction.transaction_type', ['Payment', 'Installment'])
            ->when($from, fn($q) => $q->whereBetween('financial_transaction.created_at', [$from, $to]))
            ->join('enrollment', 'financial_transaction.enrollment_id', '=', 'enrollment.enrollment_id')
            ->join('course_instance', 'enrollment.course_instance_id', '=', 'course_instance.course_instance_id')
            ->join('course_template', 'course_instance.course_template_id', '=', 'course_template.course_template_id')
            ->select('course_template.name', DB::raw('SUM(financial_transaction.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('course_template.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // ── Revenue by branch ────────────────────────────────────────
        $revenueByBranch = FinancialTransaction::query()
            ->whereIn('transaction_type', ['Payment', 'Installment'])
            ->when($from, fn($q) => $q->whereBetween('financial_transaction.created_at', [$from, $to]))
            ->join('branch', 'financial_transaction.branch_id', '=', 'branch.branch_id')
            ->select('branch.name', DB::raw('SUM(financial_transaction.amount) as total'))
            ->groupBy('branch.name')
            ->orderByDesc('total')
            ->get();

        // ── Enrollments per day (last 14 days) ──────────────────────
        $enrollTrend = Enrollment::where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $enrollDays   = [];
        $enrollValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $enrollDays[]   = now()->subDays($i)->format('d M');
            $enrollValues[] = $enrollTrend[$d]?->total ?? 0;
        }

        // ── Recent Enrollments ───────────────────────────────────────
        $recentEnrollments = Enrollment::with([
            'student', 'courseInstance.courseTemplate', 'createdByCs'
        ])->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
          ->latest()->limit(10)->get();

        // ── Leads stats ──────────────────────────────────────────────
        $totalLeads      = Lead::when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))->count();
        $convertedLeads  = Lead::where('status', 'Registered')
            ->when($from, fn($q) => $q->whereBetween('updated_at', [$from, $to]))->count();
        $conversionRate  = $totalLeads > 0 ? round($convertedLeads / $totalLeads * 100) : 0;

        return view('admin.dashboard', compact(
            // Core
            'currentPatch', 'upcomingPatch', 'period',
            // Revenue
            'patchRevenue', 'periodRevenue', 'totalRevenue', 'totalRefunded',
            'totalOutstanding', 'periodEnrollments',
            // Payment methods
            'cashRevenue', 'instapayRevenue', 'vodafoneRevenue', 'cardRevenue', 'transferRevenue',
            'cashCount', 'instapayCount', 'vodafoneCount', 'paymentMethods',
            // Trends
            'trendDays', 'trendValues', 'enrollDays', 'enrollValues',
            // Installments
            'pendingInstallments', 'overdueInstallments', 'pendingApprovals',
            'pendingRefunds', 'pendingReports',
            // Academic
            'activeCourses', 'upcomingCourses', 'completedCourses',
            'totalStudents', 'restrictedStudents', 'waitingList', 'avgCapacity',
            // CS
            'csEmployees', 'totalTarget', 'totalAchieved', 'targetPct',
            // HR
            'totalEmployees', 'totalTeachers',
            // Charts
            'revenueByCourse', 'revenueByBranch',
            // Enrollments
            'recentEnrollments',
            // Leads
            'totalLeads', 'convertedLeads', 'conversionRate',
        ));
    }

    private function periodRange(string $period, ?Patch $patch): array
    {
        return match($period) {
            'day'   => [now()->startOfDay(),   now()->endOfDay()],
            'week'  => [now()->startOfWeek(),  now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'patch' => $patch
                ? [Carbon::parse($patch->start_date)->startOfDay(), Carbon::parse($patch->end_date)->endOfDay()]
                : [null, null],
            default => [null, null], // all time
        };
    }
}