<?php

namespace App\Services;

use App\Models\HR\Employee;
use App\Models\Academic\Patch;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\CsTarget;
use App\Models\Enrollment\Enrollment;
use App\Models\Leads\Lead;
use App\Models\Leads\LeadCallLog;
use App\Models\Finance\FinancialTransaction;
use Illuminate\Support\Facades\DB;

class SalesService
{

    public function getSalesData(Employee $employee, ?Patch $patch): array
    {
        return [
            'kpis'          => $this->getKPIs($employee, $patch),
            'followupStats' => $this->getFollowupStats($employee, $patch),
            'revenueRows'   => $this->getRevenueTable($employee, $patch),
            'dailyRevenue'  => $this->getDailyRevenue($employee, $patch),
        ];
    }

    public function getKPIs(Employee $employee, ?Patch $patch): array
    {
        $target = CsTarget::where('employee_id', $employee->employee_id)
            ->where('patch_id', $patch?->patch_id)
            ->first();

        $achieved = RevenueSplit::where('employee_id', $employee->employee_id)
            ->where('patch_id', $patch?->patch_id)
            ->sum('amount_allocated');

        $registrations = Enrollment::where('created_by_cs_id', $employee->employee_id)
            ->where('patch_id', $patch?->patch_id)
            ->count();

        $targetAmount = $target?->target_amount ?? 0;
        $remaining    = max(0, $targetAmount - $achieved);
        $pct          = $targetAmount > 0
            ? round(($achieved / $targetAmount) * 100, 1)
            : 0;

        return [
            'target'        => $targetAmount,
            'achieved'      => $achieved,
            'remaining'     => $remaining,
            'percentage'    => $pct,
            'registrations' => $registrations,
        ];
    }

    public function getFollowupStats(Employee $employee, ?Patch $patch): array
    {
        $patchStart = $patch?->start_date;
        $patchEnd   = $patch?->end_date ?? now();

        $baseLeads = Lead::where('owner_cs_id', $employee->employee_id)
            ->when($patchStart, fn($q) => $q
                ->whereBetween('created_at', [$patchStart, $patchEnd]));

        $totalLeads  = (clone $baseLeads)->count();
        $registered  = (clone $baseLeads)->where('status', 'Registered')->count();

        $leadIds     = (clone $baseLeads)->pluck('lead_id');
        $totalCalls  = LeadCallLog::whereIn('lead_id', $leadIds)->count();
        $unanswered  = LeadCallLog::whereIn('lead_id', $leadIds)
            ->where('outcome', 'No_Answer')->count();
        $answered    = $totalCalls - $unanswered;
        $conversion  = $totalLeads > 0
            ? round(($registered / $totalLeads) * 100, 1)
            : 0;

        return [
            'total_leads'  => $totalLeads,
            'total_calls'  => $totalCalls,
            'answered'     => $answered,
            'unanswered'   => $unanswered,
            'registered'   => $registered,
            'conversion'   => $conversion,
        ];
    }

    public function getRevenueTable(Employee $employee, ?Patch $patch): \Illuminate\Support\Collection
    {
        return RevenueSplit::with([
                'financialTransaction.enrollment.student',
                'financialTransaction.enrollment.courseTemplate',
            ])
            ->where('employee_id', $employee->employee_id)
            ->where('patch_id', $patch?->patch_id)
            ->get()
            ->groupBy(fn($r) => $r->financialTransaction?->enrollment_id)
            ->map(function ($splits, $enrollmentId) {
                $first      = $splits->first();
                $enrollment = $first->financialTransaction?->enrollment;

                return [
                    'student_name'  => $enrollment?->student?->full_name ?? '—',
                    'course'        => $enrollment?->courseTemplate?->name ?? '—',
                    'deposit'       => $splits->whereIn('allocation_type', ['Direct'])
                                        ->sum('amount_allocated'),
                    'material'      => $splits->where('allocation_type', 'Shared')
                                        ->sum('amount_allocated'),
                    'total'         => $splits->sum('amount_allocated'),
                    'date'          => $first->created_at?->format('d M Y'),
                ];
            })
            ->values();
    }

    public function getDailyRevenue(Employee $employee, ?Patch $patch): array
    {
        $rows = RevenueSplit::where('employee_id', $employee->employee_id)
            ->where('patch_id', $patch?->patch_id)
            ->selectRaw('DATE(created_at) as day, SUM(amount_allocated) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return [
            'labels' => $rows->pluck('day'),
            'values' => $rows->pluck('total'),
        ];
    }
}