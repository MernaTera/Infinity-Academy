<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads\Lead;
use App\Models\Leads\LeadCallLog;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\RestrictionLog;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Enrollment\CsTarget;
use App\Models\Academic\Patch;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Route by role (not a hard-coded id) so CS Leader — who shares the CS
        // dashboard — is handled instead of falling through to a 403.
        return match(true) {
            $user->isAdmin()                   => redirect('/admin/dashboard'),
            $user->isSC()                      => redirect('/student-care/dashboard'),
            $user->isTeacher()                 => redirect('/teacher/dashboard'),
            $user->isCS(), $user->isCsLeader() => $this->showCsDashboard(),
            default                            => abort(403),
        };
    }

    private function showCsDashboard()
    {
        $me         = Employee::where('user_id', auth()->id())->first();
        $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $currentPatch = Patch::active()->latest('start_date')->first();

        $myLeads = Lead::where('owner_cs_id', $employee?->employee_id);

        $leadsStats = [
            'my_total'      => (clone $myLeads)->count(),
            'my_active'     => (clone $myLeads)->whereIn('status', ['Waiting','Call_Again','Scheduled_Call'])->count(),
            'my_registered' => (clone $myLeads)->where('status', 'Registered')->count(),
            'my_overdue'    => (clone $myLeads)->where('updated_at', '<=', now()->subDays(4))
                                ->whereIn('status', ['Waiting','Call_Again'])->count(),
            'public'        => Lead::whereNull('owner_cs_id')->where('is_active', true)->count(),
            'archived'      => Lead::where('owner_cs_id', $employee?->employee_id)->where('is_active', false)->count(),
        ];

        $currentMonth = now()->format('Y-m');

        // Standing (permanent) target — set once, applies every month until the
        // admin changes it. Not per-month.
        $targetAmount = CsTarget::amountFor($employee?->employee_id);

        $achieved = RevenueSplit::where('employee_id', $employee?->employee_id)
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->sum('amount_allocated');

        $targetAmount = $targetAmount ?? 0;
        $remaining    = max(0, $targetAmount - $achieved);
        $percentage   = $targetAmount > 0 ? round(($achieved / $targetAmount) * 100, 1) : 0;

        $salesStats = [
            'target'        => $targetAmount,
            'achieved'      => $achieved,
            'remaining'     => $remaining,
            'percentage'    => $percentage,
            'registrations' => Enrollment::where('created_by_cs_id', $employee?->employee_id)
                                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                                ->count(),
        ];

        $outstandingSummary = $employee
            ? (app(\App\Services\OutstandingService::class)->getOutstandingData($employee)['summary'] ?? [])
            : [];

        $outstandingStats = [
            'count'      => $outstandingSummary['total_students']    ?? 0,
            'restricted' => $outstandingSummary['restricted_count']  ?? 0,
            'total_le'   => $outstandingSummary['total_outstanding'] ?? 0,
        ];

        $postponedCount = \App\Models\Enrollment\Postponement::where('status', 'Active')
            ->whereHas('enrollment', fn ($q) => $q)
            ->count();

        $callsDueToday = Lead::where('owner_cs_id', $employee?->employee_id)
            ->whereDate('next_call_at', today())
            ->whereIn('status', ['Waiting', 'Call_Again', 'Scheduled_Call'])
            ->count();

        $recentLeads = Lead::where('owner_cs_id', $employee?->employee_id)
            ->with(['courseTemplate'])
            ->latest()
            ->limit(5)
            ->get();

        $recentPayments = \App\Models\Finance\FinancialTransaction::where('created_by_employee_id', $employee?->employee_id)
            ->with(['enrollment.student', 'enrollment.courseTemplate'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'me',
            'employee',
            'currentPatch',
            'leadsStats',
            'salesStats',
            'outstandingStats',
            'postponedCount',
            'callsDueToday',
            'recentLeads',
            'recentPayments',
        ));
    }
}