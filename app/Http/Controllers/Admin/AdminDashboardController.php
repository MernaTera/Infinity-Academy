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
        $period = $request->query('period', 'patch');

        $currentPatch  = Patch::where('status', 'Active')->latest('start_date')->first();
        $upcomingPatch = Patch::where('status', 'Upcoming')->latest('start_date')->first();

        [$from, $to] = $this->periodRange($period, $currentPatch);

        // ── Helper closures ───────────────────────────────────────────
        // Apply period to a financial_transaction query (uses patch_id for patch period)
        $applyFtPeriod = function ($q) use ($period, $currentPatch, $from, $to) {
            if ($period === 'patch' && $currentPatch) {
                return $q->where('patch_id', $currentPatch->patch_id);
            }
            if ($from) {
                return $q->whereBetween('created_at', [$from, $to]);
            }
            return $q;
        };

        // Apply period to a date-based query (created_at only)
        $applyDatePeriod = function ($q) use ($from, $to) {
            if ($from) {
                return $q->whereBetween('created_at', [$from, $to]);
            }
            return $q;
        };

        // ── Revenue ───────────────────────────────────────────────────
        $patchRevenue = $currentPatch
            ? FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
                ->where('patch_id', $currentPatch->patch_id)->sum('amount')
            : 0;

        $periodRevenue = $applyFtPeriod(
            FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
        )->sum('amount');

        $totalRevenue = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])->sum('amount');

        $totalRefunded = $applyFtPeriod(
            FinancialTransaction::where('transaction_type', 'Refund')
        )->sum('amount');

        // ── Payment methods breakdown ─────────────────────────────────
        $paidInstallmentIds = DB::table('installment_schedule')
            ->where('status', 'Paid')
            ->pluck('transaction_id')
            ->filter()
            ->toArray();

        $ftMethods = $applyFtPeriod(
            DB::table('financial_transaction')
                ->where(function ($q) use ($paidInstallmentIds) {
                    $q->where('transaction_type', 'Payment')
                    ->orWhere(function ($q2) use ($paidInstallmentIds) {
                        $q2->where('transaction_type', 'Installment')
                            ->whereIn('transaction_id', $paidInstallmentIds);
                    });
                })
        )->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
        ->groupBy('payment_method')
        ->get()->keyBy('payment_method');

        // Cash — 
        $cashRevenue = (float)($ftMethods['Cash']?->total ?? 0);
        $cashCount   = (int)  ($ftMethods['Cash']?->count ?? 0);

        // InstaPay = Transfer(ft) + Instapay(dp)
        $instapayRevenue = ($ftMethods['Transfer']?->total ?? 0) + ($dpMethods['Instapay']?->total ?? 0);
        $instapayCount   = ($ftMethods['Transfer']?->count ?? 0) + ($dpMethods['Instapay']?->count ?? 0);

        // Vodafone = Online(ft) + Vodafone_Cash(dp)
        $vodafoneRevenue = ($ftMethods['Online']?->total ?? 0) + ($dpMethods['Vodafone_Cash']?->total ?? 0);
        $vodafoneCount   = ($ftMethods['Online']?->count ?? 0) + ($dpMethods['Vodafone_Cash']?->count ?? 0);

        // Card — ft بس
        $cardRevenue = (float)($ftMethods['Card']?->total ?? 0);
        $cardCount   = (int)  ($ftMethods['Card']?->count ?? 0);

        // ── Revenue trend (last 7 days — always all-time trend) ───────
        $revenueTrend = FinancialTransaction::whereIn('transaction_type', ['Payment', 'Installment'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $trendDays = []; $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $trendDays[]   = now()->subDays($i)->format('d M');
            $trendValues[] = $revenueTrend[$d]?->total ?? 0;
        }

        // ── Enrollments by period ─────────────────────────────────────
        $periodEnrollments = Enrollment::when($period === 'patch' && $currentPatch,
                fn($q) => $q->where('patch_id', $currentPatch->patch_id))
            ->when($period !== 'patch' && $from,
                fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->count();

        // ── Outstanding — accurate calculation ────────────────────────
        $allActiveEnrollments = Enrollment::with([
            'financialTransactions',
            'installmentSchedules',
        ])->whereIn('status', ['Active', 'Restricted', 'Waiting'])
          ->whereNotNull('final_price')
          ->get();

        $totalOutstanding = $allActiveEnrollments->sum(function ($e) {
            $totalFees = (float) $e->final_price
                + (float) $e->financialTransactions->where('transaction_category', 'Material')->sum('amount')
                + (float) $e->financialTransactions->where('transaction_category', 'Test')->sum('amount');

            $paidInstIds = $e->installmentSchedules
                ->where('status', 'Paid')
                ->pluck('transaction_id')->filter()->toArray();

            $paid = (float) $e->financialTransactions->where('transaction_type', 'Payment')->sum('amount')
                  + (float) $e->financialTransactions->where('transaction_type', 'Installment')
                        ->whereIn('transaction_id', $paidInstIds)->sum('amount')
                  - (float) $e->financialTransactions->where('transaction_type', 'Refund')->sum('amount');

            $remaining = $totalFees - $paid;
            return $remaining < 0.01 ? 0 : $remaining;
        });

        // ── Installments ──────────────────────────────────────────────
        $pendingInstallments = InstallmentSchedule::where('status', 'Pending')->count();
        $overdueInstallments = InstallmentSchedule::where('status', 'Overdue')->count();
        $pendingApprovals    = \App\Models\Finance\InstallmentApprovalLog::where('status', 'Pending')->count();
        $pendingRefunds      = RefundRequest::where('status', 'Pending')->count();
        $pendingReports      = Report::where('status', 'Submitted')->count();

        // ── Academic ──────────────────────────────────────────────────
        $activeCourses      = CourseInstance::where('status', 'Active')->count();
        $upcomingCourses    = CourseInstance::where('status', 'Upcoming')->count();
        $completedCourses   = CourseInstance::where('status', 'Completed')
            ->when($from, fn($q) => $q->whereBetween('updated_at', [$from, $to]))->count();
        $totalStudents      = Enrollment::whereIn('status', ['Active', 'Restricted'])->count();
        $restrictedStudents = Enrollment::where('status', 'Restricted')->count();
        $waitingList        = WaitingList::where('status', 'Active')->count();

        $instances   = CourseInstance::where('status', 'Active')->withCount('enrollments')->get();
        $avgCapacity = $instances->count() > 0
            ? round($instances->avg(fn($i) => $i->capacity > 0 ? ($i->enrollments_count / $i->capacity * 100) : 0))
            : 0;

        // ── CS Performance ────────────────────────────────────────────
        $csEmployees = Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')
            ->get()
            ->map(function ($emp) use ($currentPatch, $from, $to, $period) {
                $revQ = RevenueSplit::where('employee_id', $emp->employee_id);
                if ($period === 'patch' && $currentPatch)
                    $revQ->where('patch_id', $currentPatch->patch_id);
                elseif ($from)
                    $revQ->whereBetween('created_at', [$from, $to]);
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
                if ($period === 'patch' && $currentPatch)
                    $regQ->where('patch_id', $currentPatch->patch_id);
                elseif ($from)
                    $regQ->whereBetween('created_at', [$from, $to]);
                $registrations = $regQ->count();

                $emp->patch_revenue = $revenue;
                $emp->target        = $target;
                $emp->achievement   = $target > 0 ? round($revenue / $target * 100) : 0;
                $emp->registrations = $registrations;
                $emp->leads_count   = Lead::where('owner_cs_id', $emp->employee_id)->count();
                return $emp;
            })
            ->sortByDesc('patch_revenue');

        $totalTarget   = $csEmployees->sum('target');
        $totalAchieved = $csEmployees->sum('patch_revenue');
        $targetPct     = $totalTarget > 0 ? round($totalAchieved / $totalTarget * 100) : 0;

        // ── HR ────────────────────────────────────────────────────────
        $totalEmployees = Employee::where('status', 'Active')->count();
        $totalTeachers  = Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Teacher'))
            ->where('status', 'Active')->count();

        // ── Revenue by course — handles null course_instance_id ───────
        $revenueByCourse = DB::table('financial_transaction as ft')
            ->join('enrollment as e', 'ft.enrollment_id', '=', 'e.enrollment_id')
            ->leftJoin('course_instance as ci', 'e.course_instance_id', '=', 'ci.course_instance_id')
            ->leftJoin('course_template as ct1', 'ci.course_template_id', '=', 'ct1.course_template_id')
            ->leftJoin('course_template as ct2', 'e.course_template_id', '=', 'ct2.course_template_id')
            ->whereIn('ft.transaction_type', ['Payment', 'Installment'])
            ->when($period === 'patch' && $currentPatch, fn($q) => $q->where('ft.patch_id', $currentPatch->patch_id))
            ->when($period !== 'patch' && $from, fn($q) => $q->whereBetween('ft.created_at', [$from, $to]))
            ->select(
                DB::raw('COALESCE(ct1.name, ct2.name) as name'),
                DB::raw('SUM(ft.amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('COALESCE(ct1.name, ct2.name)'))
            ->having('name', '!=', null)
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // ── Revenue by branch ─────────────────────────────────────────
        $revenueByBranch = DB::table('financial_transaction as ft')
            ->join('branch', 'ft.branch_id', '=', 'branch.branch_id')
            ->whereIn('ft.transaction_type', ['Payment', 'Installment'])
            ->when($period === 'patch' && $currentPatch, fn($q) => $q->where('ft.patch_id', $currentPatch->patch_id))
            ->when($period !== 'patch' && $from, fn($q) => $q->whereBetween('ft.created_at', [$from, $to]))
            ->select('branch.name', DB::raw('SUM(ft.amount) as total'))
            ->groupBy('branch.name')
            ->orderByDesc('total')
            ->get();

        // ── Enrollment trend (last 14 days — always) ──────────────────
        $enrollTrend = Enrollment::where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $enrollDays = []; $enrollValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $enrollDays[]   = now()->subDays($i)->format('d M');
            $enrollValues[] = $enrollTrend[$d]?->total ?? 0;
        }

        // ── Recent Enrollments ────────────────────────────────────────
        $recentEnrollments = Enrollment::with(['student', 'courseTemplate', 'courseInstance.courseTemplate', 'createdByCs'])
            ->when($period === 'patch' && $currentPatch, fn($q) => $q->where('patch_id', $currentPatch->patch_id))
            ->when($period !== 'patch' && $from, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->latest()->limit(10)->get();

        // ── Leads stats ───────────────────────────────────────────────
        $totalLeads = Lead::when($from, fn($q) => $q->whereBetween('created_at', [$from, $to]))->count();
        $convertedLeads = Lead::where('status', 'Registered')
            ->when($from, fn($q) => $q->whereBetween('updated_at', [$from, $to]))->count();
        $conversionRate = $totalLeads > 0 ? round($convertedLeads / $totalLeads * 100) : 0;

        return view('admin.dashboard', compact(
            'currentPatch', 'upcomingPatch', 'period',
            'patchRevenue', 'periodRevenue', 'totalRevenue', 'totalRefunded',
            'totalOutstanding', 'periodEnrollments',
            'cashRevenue', 'cashCount',
            'instapayRevenue', 'instapayCount',
            'vodafoneRevenue', 'vodafoneCount',
            'cardRevenue', 'cardCount',
            'trendDays', 'trendValues', 'enrollDays', 'enrollValues',
            'pendingInstallments', 'overdueInstallments', 'pendingApprovals',
            'pendingRefunds', 'pendingReports',
            'activeCourses', 'upcomingCourses', 'completedCourses',
            'totalStudents', 'restrictedStudents', 'waitingList', 'avgCapacity',
            'csEmployees', 'totalTarget', 'totalAchieved', 'targetPct',
            'totalEmployees', 'totalTeachers',
            'revenueByCourse', 'revenueByBranch',
            'recentEnrollments',
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
            default => [null, null],
        };
    }
}