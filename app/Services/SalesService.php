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
        if ($filterType === 'week') {
            return [
                'start' => \Carbon\Carbon::parse($day)->startOfWeek(),
                'end'   => \Carbon\Carbon::parse($day)->endOfWeek(),
            ];
        }
        if ($filterType === 'month') {
            return [
                'start' => \Carbon\Carbon::parse($month)->startOfMonth(),
                'end'   => \Carbon\Carbon::parse($month)->endOfMonth(),
            ];
        }
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
        'week'  => \Carbon\Carbon::parse($day)->format('Y-m'), 
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

        // ── Calls made BY this CS within the period ──
        // Filter by the call's own timestamp (call_datetime) and the CS who made
        // it (cs_id) — NOT by when the lead was created. This is what makes the
        // numbers accurate: a call this month on an old lead still counts.
        $callsQuery = LeadCallLog::where('cs_id', $employee->employee_id)
            ->when($range['start'], fn($q) => $q->whereBetween('call_datetime', [$range['start'], $range['end']]));

        $totalCalls = (clone $callsQuery)->count();
        $unanswered = (clone $callsQuery)->whereIn('outcome', ['No_Answer', 'Wrong_Number'])->count();
        $answered   = $totalCalls - $unanswered;

        // ── Leads owned by this CS ──
        // "Total leads" here = leads this CS is responsible for. We do NOT filter
        // these by created_at, otherwise old-but-active leads vanish and the whole
        // panel reads zero. Registered = those converted (optionally within period).
        $ownedLeads = Lead::where('owner_cs_id', $employee->employee_id);
        $totalLeads = (clone $ownedLeads)->count();

        // Registrations attributed to this CS within the period (accurate: uses
        // the enrollment creation date, which is the real conversion moment).
        $registered = Enrollment::where('created_by_cs_id', $employee->employee_id)
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->count();

        $conversion = $totalCalls > 0
            ? round(($registered / $totalCalls) * 100, 1)
            : 0;

        return [
            'totalLeads'  => $totalLeads,
            'totalCalls'  => $totalCalls,
            'answered'    => $answered,
            'unanswered'  => $unanswered,
            'registered'  => $registered,
            'conversion'  => $conversion,
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

                $deposit  = $splits->where('allocation_type', 'Direct')
                                ->filter(fn($r) => $r->financialTransaction?->transaction_category === 'Course')
                                ->sum('amount_allocated');

                $testFee  = $splits->where('allocation_type', 'Direct')
                                ->filter(fn($r) => $r->financialTransaction?->transaction_category === 'Test')
                                ->sum('amount_allocated');

                $material = $splits->where('allocation_type', 'Shared')
                                ->sum('amount_allocated');

                return [
                    'student_name' => $enrollment?->student?->full_name ?? '—',
                    'course'       => $enrollment?->courseTemplate?->name ?? '—',
                    'deposit'      => $deposit,
                    'test_fee'     => $testFee,   // ← جديد
                    'material'     => $material,
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