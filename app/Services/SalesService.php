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

    public function getSalesData(Employee $employee, ?Patch $patch, string $filterType = 'patch', string $month = '', string $day = ''): array
    {
        return [
            'kpis'          => $this->getKPIs($employee, $patch, $filterType, $month, $day),
            'followupStats' => $this->getFollowupStats($employee, $patch, $filterType, $month, $day),
            'revenueRows'   => $this->getRevenueTable($employee, $patch, $filterType, $month, $day),
            'dailyRevenue'  => $this->getDailyRevenue($employee, $patch, $filterType, $month, $day),
        ];
    }

    private function getDateRange(string $filterType, ?Patch $patch, string $month, string $day): array
    {
        if ($filterType === 'day') {
            return [
                'start' => \Carbon\Carbon::parse($day)->startOfDay(),
                'end'   => \Carbon\Carbon::parse($day)->endOfDay(),
            ];
        }
        if ($filterType === 'month') {
            return [
                'start' => \Carbon\Carbon::parse($month)->startOfMonth(),
                'end'   => \Carbon\Carbon::parse($month)->endOfMonth(),
            ];
        }
        // patch
        return [
            'start' => $patch?->start_date ? \Carbon\Carbon::parse($patch->start_date)->startOfDay() : null,
            'end'   => $patch?->end_date   ? \Carbon\Carbon::parse($patch->end_date)->endOfDay()     : now(),
        ];
    }

public function getKPIs(Employee $employee, ?Patch $patch, string $filterType = 'patch', string $month = '', string $day = ''): array
{
    $range = $this->getDateRange($filterType, $patch, $month, $day);

    // ── Target by month ──
    $targetMonth = match($filterType) {
        'patch' => $patch?->start_date
                    ? \Carbon\Carbon::parse($patch->start_date)->format('Y-m')
                    : now()->format('Y-m'),
        'month' => $month,
        'day'   => \Carbon\Carbon::parse($day)->format('Y-m'),
    };

    $target = CsTarget::where('employee_id', $employee->employee_id)
        ->where('month', $targetMonth)
        ->first();

    $achieved = RevenueSplit::where('employee_id', $employee->employee_id)
        ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
        ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
        ->sum('amount_allocated');

    $registrations = Enrollment::where('created_by_cs_id', $employee->employee_id)
        ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
        ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
        ->count();

    $targetAmount = $target?->target_amount ?? 0;
    $remaining    = $targetAmount > 0 ? max(0, $targetAmount - $achieved) : null;
    $pct          = $targetAmount > 0 ? round(($achieved / $targetAmount) * 100, 1) : null;

    return [
        'target'        => $targetAmount,
        'achieved'      => $achieved,
        'remaining'     => $remaining,
        'percentage'    => $pct,
        'registrations' => $registrations,
        'filter_type'   => $filterType,
        'target_month'  => $targetMonth,
    ];
}

    public function getFollowupStats(Employee $employee, ?Patch $patch, string $filterType = 'patch', string $month = '', string $day = ''): array
    {
        $range = $this->getDateRange($filterType, $patch, $month, $day);

        $baseLeads = Lead::where('owner_cs_id', $employee->employee_id)
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]));

        $totalLeads = (clone $baseLeads)->count();
        $registered = (clone $baseLeads)->where('status', 'Registered')->count();
        $leadIds    = (clone $baseLeads)->pluck('lead_id');
        $totalCalls = LeadCallLog::whereIn('lead_id', $leadIds)->count();
        $unanswered = LeadCallLog::whereIn('lead_id', $leadIds)->where('outcome', 'No_Answer')->count();
        $answered   = $totalCalls - $unanswered;
        $conversion = $totalLeads > 0 ? round(($registered / $totalLeads) * 100, 1) : 0;

        return compact('totalLeads', 'totalCalls', 'answered', 'unanswered', 'registered', 'conversion') + [
            'total_leads' => $totalLeads,
            'total_calls' => $totalCalls,
        ];
    }

    public function getRevenueTable(Employee $employee, ?Patch $patch, string $filterType = 'patch', string $month = '', string $day = ''): \Illuminate\Support\Collection
    {
        $range = $this->getDateRange($filterType, $patch, $month, $day);

        return RevenueSplit::with([
                'financialTransaction.enrollment.student',
                'financialTransaction.enrollment.courseTemplate',
            ])
            ->where('employee_id', $employee->employee_id)
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->get()
            ->groupBy(fn($r) => $r->financialTransaction?->enrollment_id)
            ->map(function ($splits) {
                $first      = $splits->first();
                $enrollment = $first->financialTransaction?->enrollment;
                return [
                    'student_name' => $enrollment?->student?->full_name ?? '—',
                    'course'       => $enrollment?->courseTemplate?->name ?? '—',
                    'deposit'      => $splits->where('allocation_type', 'Direct')->sum('amount_allocated'),
                    'material'     => $splits->where('allocation_type', 'Shared')->sum('amount_allocated'),
                    'total'        => $splits->sum('amount_allocated'),
                    'date'         => $first->created_at?->format('d M Y'),
                ];
            })->values();
    }

    public function getDailyRevenue(Employee $employee, ?Patch $patch, string $filterType = 'patch', string $month = '', string $day = ''): array
    {
        $range = $this->getDateRange($filterType, $patch, $month, $day);

        $rows = RevenueSplit::where('employee_id', $employee->employee_id)
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->selectRaw('DATE(created_at) as day, SUM(amount_allocated) as total')
            ->groupBy('day')->orderBy('day')->get();

        return [
            'labels' => $rows->pluck('day'),
            'values' => $rows->pluck('total'),
        ];
    }
}