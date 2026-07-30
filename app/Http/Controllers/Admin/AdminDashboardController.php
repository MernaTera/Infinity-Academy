<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseInstance;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\WaitingList;
use App\Models\Enrollment\Postponement;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\RefundRequest;
use App\Models\HR\Employee;
use App\Models\Leads\Lead;
use App\Models\Reports\Report;
use App\Services\BalanceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'patch');

        $currentPatch  = Patch::where('status', 'Active')->latest('start_date')->first();
        $upcomingPatch = Patch::where('status', 'Upcoming')->latest('start_date')->first();

        [$from, $to]         = $this->periodRange($period, $currentPatch);
        [$prevFrom, $prevTo] = $this->previousPeriodRange($period, $currentPatch, $from, $to);

        // ═══════════════════════════════════════════════════════════
        // UNPAID INSTALLMENT TRANSACTIONS
        //
        // When an installment plan is approved, a FinancialTransaction is
        // created for EACH installment up front (so it shows in Outstanding
        // and links to the schedule). But that money is NOT received yet —
        // the schedule row stays 'Pending' until the installment is actually
        // paid. So for ALL revenue math we must EXCLUDE the transactions whose
        // schedule is still Pending/Overdue. This mirrors OutstandingService.
        // ═══════════════════════════════════════════════════════════
        $unpaidInstallmentTxIds = InstallmentSchedule::whereIn('status', ['Pending', 'Overdue'])
            ->whereNotNull('transaction_id')
            ->pluck('transaction_id')
            ->filter()
            ->toArray();

        // Helper: revenue query that counts only money ACTUALLY received
        // (Payments + Installments that are NOT still-pending), date-filtered.
        $realRevenue = function ($from = null, $to = null) use ($unpaidInstallmentTxIds) {
            $q = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
                ->when(!empty($unpaidInstallmentTxIds),
                    fn($q) => $q->whereNotIn('transaction_id', $unpaidInstallmentTxIds));
            if ($from) $q->whereBetween('created_at', [$from, $to]);
            return $q;
        };

        // ═══ TIER 1: EXECUTIVE OVERVIEW ═══

        $periodRevenue     = (clone $realRevenue($from, $to))->sum('amount');
        $prevPeriodRevenue = (clone $realRevenue($prevFrom, $prevTo))->sum('amount');

        $revenueTrendPct = $prevPeriodRevenue > 0
            ? round(($periodRevenue - $prevPeriodRevenue) / $prevPeriodRevenue * 100)
            : ($periodRevenue > 0 ? 100 : 0);

        $periodEnrollments = Enrollment::when($from,
                fn($q) => $q->whereBetween('created_at', [$from, $to]))->count();

        $prevPeriodEnrollments = Enrollment::when($prevFrom,
                fn($q) => $q->whereBetween('created_at', [$prevFrom, $prevTo]))->count();

        $enrollmentsTrendPct = $prevPeriodEnrollments > 0
            ? round(($periodEnrollments - $prevPeriodEnrollments) / $prevPeriodEnrollments * 100)
            : ($periodEnrollments > 0 ? 100 : 0);

        $totalRevenue = (clone $realRevenue())->sum('amount');

        $balanceCalc = app()->make(BalanceCalculator::class);
        $allActiveEnrollments = Enrollment::with(['financialTransactions','installmentSchedules'])
            ->whereIn('status', ['Active', 'Restricted', 'Waiting'])
            ->whereNotNull('final_price')
            ->get();

        $totalOutstanding = $allActiveEnrollments->sum(function ($e) use ($balanceCalc) {
            $result = $balanceCalc->calculate($e);
            return max(0, (float) $result['remaining_balance']);
        });

        $totalRefunded = FinancialTransaction::where('transaction_type', 'Refund')
            ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->sum('amount');

        // ═══ TIER 2: ACTION QUEUE ═══
        $pendingInstallments = InstallmentSchedule::where('status', 'Pending')->count();
        $overdueInstallments = InstallmentSchedule::where('status', 'Overdue')->count();
        $pendingApprovals    = \App\Models\Finance\InstallmentApprovalLog::where('status', 'Pending')->count();
        $pendingRefunds      = RefundRequest::where('status', 'Pending')->count();
        $pendingReports      = Report::where('status', 'Submitted')->count();

        $overdueReports = Report::whereIn('status', ['Draft'])
            ->orWhere(function($q) { $q->whereNull('status'); })->count();

        $expiringPostponements = Postponement::where('status', 'Active')
            ->where('expected_return_date', '<=', now()->addDays(7))
            ->where('expected_return_date', '>=', now())
            ->count();

        $expiredPostponements = Postponement::where('status', 'Expired')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $totalActions = $overdueInstallments + $pendingApprovals + $pendingRefunds + $pendingReports + $expiringPostponements;

        // ═══ TIER 3: FINANCIAL BREAKDOWN ═══

        // Payment methods — from financial_transaction, EXCLUDING unpaid installments.
        $ftMethodsQuery = DB::table('financial_transaction')
            ->whereIn('transaction_type', ['Payment', 'Installment']);
        if (!empty($unpaidInstallmentTxIds)) {
            $ftMethodsQuery->whereNotIn('transaction_id', $unpaidInstallmentTxIds);
        }
        if ($from) $ftMethodsQuery->whereBetween('created_at', [$from, $to]);

        $ftMethods = $ftMethodsQuery
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')->get()->keyBy('payment_method');

        $cashRevenue     = $ftMethods['Cash']?->total     ?? 0;
        $cashCount       = $ftMethods['Cash']?->count     ?? 0;
        $instapayRevenue = $ftMethods['Transfer']?->total ?? 0;   // Instapay => Transfer
        $instapayCount   = $ftMethods['Transfer']?->count ?? 0;
        $vodafoneRevenue = $ftMethods['Online']?->total   ?? 0;   // Vodafone => Online
        $vodafoneCount   = $ftMethods['Online']?->count   ?? 0;
        $cardRevenue     = $ftMethods['Card']?->total     ?? 0;
        $cardCount       = $ftMethods['Card']?->count     ?? 0;

        // Revenue trend (last 14 days) — excluding unpaid installments
        $revenueTrendQuery = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
            ->where('created_at', '>=', now()->subDays(13)->startOfDay());
        if (!empty($unpaidInstallmentTxIds)) {
            $revenueTrendQuery->whereNotIn('transaction_id', $unpaidInstallmentTxIds);
        }
        $revenueTrend = $revenueTrendQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $trendDays = []; $trendValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $trendDays[]   = now()->subDays($i)->format('d M');
            $trendValues[] = (float) ($revenueTrend[$d]?->total ?? 0);
        }

        $revenueSparkline = array_slice($trendValues, -7);

        // Revenue by Course (Top 6) — date-based, excluding unpaid installments
        $revByCourseQuery = DB::table('financial_transaction as ft')
            ->join('enrollment as e', 'ft.enrollment_id', '=', 'e.enrollment_id')
            ->leftJoin('course_instance as ci', 'e.course_instance_id', '=', 'ci.course_instance_id')
            ->leftJoin('course_template as ct1', 'ci.course_template_id', '=', 'ct1.course_template_id')
            ->leftJoin('course_template as ct2', 'e.course_template_id', '=', 'ct2.course_template_id')
            ->whereIn('ft.transaction_type', ['Payment', 'Installment']);
        if (!empty($unpaidInstallmentTxIds)) {
            $revByCourseQuery->whereNotIn('ft.transaction_id', $unpaidInstallmentTxIds);
        }
        if ($from) $revByCourseQuery->whereBetween('ft.created_at', [$from, $to]);

        $revenueByCourse = $revByCourseQuery
            ->select(
                DB::raw('COALESCE(ct1.name, ct2.name) as name'),
                DB::raw('SUM(ft.amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('COALESCE(ct1.name, ct2.name)'))
            ->having('name', '!=', null)
            ->orderByDesc('total')->limit(6)->get();

        // Revenue by Branch — date-based, excluding unpaid installments
        $revByBranchQuery = DB::table('financial_transaction as ft')
            ->join('branch', 'ft.branch_id', '=', 'branch.branch_id')
            ->whereIn('ft.transaction_type', ['Payment', 'Installment']);
        if (!empty($unpaidInstallmentTxIds)) {
            $revByBranchQuery->whereNotIn('ft.transaction_id', $unpaidInstallmentTxIds);
        }
        if ($from) $revByBranchQuery->whereBetween('ft.created_at', [$from, $to]);

        $revenueByBranch = $revByBranchQuery
            ->select('branch.name', DB::raw('SUM(ft.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('branch.name')->orderByDesc('total')->get();

        // ═══ TIER 4: ACADEMIC OPERATIONS ═══
        $activeCourses      = CourseInstance::where('status', 'Active')->count();
        $upcomingCourses    = CourseInstance::where('status', 'Upcoming')->count();
        $completedCourses   = CourseInstance::where('status', 'Completed')
            ->when($from, fn($q) => $q->whereBetween('end_date', [$from, $to]))->count();

        $totalStudents      = Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = Enrollment::where('status', 'Restricted')->count();
        $waitingList        = WaitingList::where('status', 'Active')->count();

        $activeInstances = CourseInstance::where('status', 'Active')->withCount('enrollments')->get();
        $validInstances  = $activeInstances->filter(fn($i) => $i->capacity > 0);
        $avgCapacity     = $validInstances->count() > 0
            ? round($validInstances->avg(fn($i) => ($i->enrollments_count / $i->capacity) * 100))
            : 0;

        $enrollTrend = Enrollment::where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $enrollDays = []; $enrollValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $enrollDays[]   = now()->subDays($i)->format('d M');
            $enrollValues[] = $enrollTrend[$d]?->total ?? 0;
        }

        // ═══ TIER 5: SALES & CS PERFORMANCE ═══
        $csEmployees = Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')
            ->get()
            ->map(function ($emp) use ($currentPatch, $from, $to, $period) {
                $revQ = RevenueSplit::where('employee_id', $emp->employee_id);
                if ($from) $revQ->whereBetween('created_at', [$from, $to]);
                $revenue = $revQ->sum('amount_allocated');

                $target = 0;
                if ($period === 'patch' && $currentPatch) {
                    $target = \App\Models\Enrollment\CsTarget::where('employee_id', $emp->employee_id)
                        ->where('patch_id', $currentPatch->patch_id)->value('target_amount') ?? 0;
                } else {
                    $target = \App\Models\Enrollment\CsTarget::where('employee_id', $emp->employee_id)
                        ->where('month', now()->format('Y-m'))->value('target_amount') ?? 0;
                }

                $regQ = Enrollment::where('created_by_cs_id', $emp->employee_id);
                if ($from) $regQ->whereBetween('created_at', [$from, $to]);
                $registrations = $regQ->count();

                $leadsQ = Lead::where('owner_cs_id', $emp->employee_id);
                if ($from) $leadsQ->whereBetween('created_at', [$from, $to]);

                $emp->patch_revenue = $revenue;
                $emp->target        = $target;
                $emp->achievement   = $target > 0 ? round($revenue / $target * 100) : 0;
                $emp->registrations = $registrations;
                $emp->leads_count   = $leadsQ->count();
                return $emp;
            })
            ->sortByDesc('patch_revenue');

        $totalTarget   = $csEmployees->sum('target');
        $totalAchieved = $csEmployees->sum('patch_revenue');
        $targetPct     = $totalTarget > 0 ? round($totalAchieved / $totalTarget * 100) : 0;

        $totalLeads     = Lead::when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))->count();
        $convertedLeads = Lead::where('status', 'Registered')
            ->when($from, fn($q) => $q->whereBetween('updated_at', [$from, $to]))->count();
        $conversionRate = $totalLeads > 0 ? round($convertedLeads / $totalLeads * 100) : 0;

        $recentEnrollments = Enrollment::with(['student', 'courseTemplate', 'courseInstance.courseTemplate', 'createdByCs'])
            ->when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->latest()->limit(8)->get();

        // ═══ TIER 6: WORKFORCE ═══
        $totalEmployees = Employee::where('status', 'Active')->count();
        $totalTeachers  = Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Teacher'))
            ->where('status', 'Active')->count();
        $totalCS = Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')->count();

        return view('admin.dashboard', compact(
            'currentPatch', 'upcomingPatch', 'period',
            'periodRevenue', 'prevPeriodRevenue', 'revenueTrendPct',
            'periodEnrollments', 'prevPeriodEnrollments', 'enrollmentsTrendPct',
            'totalRevenue', 'totalOutstanding', 'totalRefunded',
            'pendingInstallments', 'overdueInstallments', 'pendingApprovals',
            'pendingRefunds', 'pendingReports', 'overdueReports',
            'expiringPostponements', 'expiredPostponements', 'totalActions',
            'cashRevenue','cashCount','instapayRevenue','instapayCount',
            'vodafoneRevenue','vodafoneCount','cardRevenue','cardCount',
            'trendDays','trendValues','revenueSparkline',
            'revenueByCourse','revenueByBranch',
            'activeCourses','upcomingCourses','completedCourses',
            'totalStudents','restrictedStudents','waitingList','avgCapacity',
            'enrollDays','enrollValues',
            'csEmployees','totalTarget','totalAchieved','targetPct',
            'totalLeads','convertedLeads','conversionRate','recentEnrollments',
            'totalEmployees','totalTeachers','totalCS',
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
            default => [null, null],
        };
    }

    private function previousPeriodRange(string $period, ?Patch $patch, $from, $to): array
    {
        if (!$from) return [null, null];
        return match($period) {
            'day'   => [now()->subDay()->startOfDay(),     now()->subDay()->endOfDay()],
            'week'  => [now()->subWeek()->startOfWeek(),   now()->subWeek()->endOfWeek()],
            'month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'patch' => $patch
                ? [
                    Carbon::parse($patch->start_date)->subMonth()->startOfDay(),
                    Carbon::parse($patch->end_date)->subMonth()->endOfDay(),
                  ]
                : [null, null],
            default => [null, null],
        };
    }
}