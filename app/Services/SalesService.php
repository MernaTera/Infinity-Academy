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

        $targetMonth = match($filterType) {
            'patch' => $patch?->start_date
                        ? \Carbon\Carbon::parse($patch->start_date)->format('Y-m')
                        : now()->format('Y-m'),
            'month' => $month,
            'week'  => \Carbon\Carbon::parse($day)->format('Y-m'), 
            'day'   => \Carbon\Carbon::parse($day)->format('Y-m'),
        };

        $targetAmount = CsTarget::amountFor($employee->employee_id);

        $achieved = RevenueSplit::where('employee_id', $employee->employee_id)
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->sum('amount_allocated');

        $registrations = Enrollment::where('created_by_cs_id', $employee->employee_id)
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->count();

        $targetAmount = $targetAmount ?? 0;
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

        $totalLeads = Lead::where('owner_cs_id', $employee->employee_id)->count();

        $totalCalls = DB::table('lead_history')
            ->where('changed_by', $employee->employee_id)
            ->where('new_status', 'Call_Again')
            ->when($range['start'], fn($q) => $q->whereBetween('changed_at', [$range['start'], $range['end']]))
            ->count();

        $registered = Enrollment::where('created_by_cs_id', $employee->employee_id)
            ->where('status', '!=', 'Cancelled')
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->count();

        $refunded = Enrollment::where('created_by_cs_id', $employee->employee_id)
            ->where('status', 'Cancelled')
            ->when($filterType === 'patch', fn($q) => $q->where('patch_id', $patch?->patch_id))
            ->when($range['start'], fn($q) => $q->whereBetween('created_at', [$range['start'], $range['end']]))
            ->count();

        $conversion = $totalLeads > 0
            ? round(($registered / $totalLeads) * 100, 1)
            : 0;

        return [
            'totalLeads'  => $totalLeads,
            'totalCalls'  => $totalCalls,
            'registered'  => $registered,
            'refunded'    => $refunded,
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

                $material = $splits->filter(fn($r) => $r->financialTransaction?->transaction_category === 'Material')
                                ->sum('amount_allocated');

                return [
                    'student_name' => $enrollment?->student?->full_name ?? '—',
                    'course'       => $enrollment?->courseTemplate?->name ?? '—',
                    'deposit'      => $deposit,
                    'test_fee'     => $testFee,   
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