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
        $employee     = Employee::where('user_id', auth()->id())->first();
        $currentPatch = Patch::active()->latest('start_date')->first();

        // ══ LEADS ══════════════════════════════════════════════
        $myLeads = Lead::where('owner_cs_id', $employee?->employee_id);

        $leadsStats = [
            'my_total'      => (clone $myLeads)->count(),
            'my_active'     => (clone $myLeads)->whereIn('status', ['Waiting','Call_Again','Scheduled_Call'])->count(),
            'my_registered' => (clone $myLeads)->where('status', 'Registered')->count(),
            'my_overdue'    => (clone $myLeads)->where('updated_at', '<=', now()->subDays(4))
                                ->whereIn('status', ['Waiting','Call_Again'])->count(),
            'public'        => Lead::whereNull('owner_cs_id')->where('is_active', true)->count(),
        ];

        // ══ SALES / REVENUE ════════════════════════════════════
        $target = \App\Models\Enrollment\CsTarget::where('employee_id', $employee?->employee_id)
            ->where('patch_id', $currentPatch?->patch_id)
            ->first();

        $achieved = RevenueSplit::where('employee_id', $employee?->employee_id)
            ->where('patch_id', $currentPatch?->patch_id)
            ->sum('amount_allocated');

        $targetAmount = $target?->target_amount ?? 0;
        $remaining    = max(0, $targetAmount - $achieved);
        $percentage   = $targetAmount > 0 ? round(($achieved / $targetAmount) * 100, 1) : 0;

        $salesStats = [
            'target'        => $targetAmount,
            'achieved'      => $achieved,
            'remaining'     => $remaining,
            'percentage'    => $percentage,
            'registrations' => Enrollment::where('created_by_cs_id', $employee?->employee_id)
                                ->where('patch_id', $currentPatch?->patch_id)->count(),
        ];

        // ══ OUTSTANDING ════════════════════════════════════════
        $myEnrollments = Enrollment::where('created_by_cs_id', $employee?->employee_id)
            ->whereIn('status', ['Active', 'Restricted'])
            ->with('financialTransactions')
            ->get();

        $outstandingCount  = 0;
        $restrictedCount   = 0;
        $totalOutstanding  = 0;

        foreach ($myEnrollments as $e) {
            $paid      = $e->financialTransactions->whereIn('transaction_type', ['Payment','Installment'])->sum('amount')
                       - $e->financialTransactions->where('transaction_type', 'Refund')->sum('amount');
            $remaining = max(0, $e->final_price - $paid);
            if ($remaining > 0) {
                $outstandingCount++;
                $totalOutstanding += $remaining;
            }
            if ($e->restriction_flag) $restrictedCount++;
        }

        $outstandingStats = [
            'count'      => $outstandingCount,
            'restricted' => $restrictedCount,
            'total_le'   => $totalOutstanding,
        ];

        // ══ CALLS DUE TODAY ════════════════════════════════════
        $callsDueToday = Lead::where('owner_cs_id', $employee?->employee_id)
            ->whereDate('next_call_at', today())
            ->count();

        // ══ RECENT LEADS (5 أحدث) ═════════════════════════════
        $recentLeads = Lead::where('owner_cs_id', $employee?->employee_id)
            ->with(['courseTemplate'])
            ->latest()
            ->limit(5)
            ->get();

        // ══ RECENT PAYMENTS (5 أحدث) ══════════════════════════
        $recentPayments = \App\Models\Finance\FinancialTransaction::where('created_by_employee_id', $employee?->employee_id)
            ->with(['enrollment.student', 'enrollment.courseTemplate'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'employee',
            'currentPatch',
            'leadsStats',
            'salesStats',
            'outstandingStats',
            'callsDueToday',
            'recentLeads',
            'recentPayments',
        ));
    }
}