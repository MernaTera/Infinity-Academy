<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\CsTarget;
use App\Models\Enrollment\Enrollment;
use App\Models\Leads\Lead;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminSalesController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->query('filter', 'month');
        $month      = $request->query('month', now()->format('Y-m'));
        $day        = $request->query('day', now()->format('Y-m-d'));

        $range = $this->getDateRange($filterType, $month, $day);
        $targetMonth = $this->getTargetMonth($filterType, $month, $day);

        // جيبي كل الـ CS employees
        $csEmployees = Employee::with(['user.role', 'branch'])
            ->whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')
            ->get();

        $rows = $csEmployees->map(function ($emp) use ($range, $targetMonth, $filterType) {
            $target = CsTarget::where('employee_id', $emp->employee_id)
                ->where('month', $targetMonth)
                ->first();

            $achieved = RevenueSplit::where('employee_id', $emp->employee_id)
                ->whereBetween('created_at', [$range['start'], $range['end']])
                ->sum('amount_allocated');

            $registrations = Enrollment::where('created_by_cs_id', $emp->employee_id)
                ->whereBetween('created_at', [$range['start'], $range['end']])
                ->count();

            $leads = Lead::where('owner_cs_id', $emp->employee_id);
            $totalLeads  = (clone $leads)->count();
            $activeLeads = (clone $leads)->whereIn('status', ['Waiting', 'Call_Again'])->count();

            $targetAmount = $target?->target_amount ?? 0;
            $remaining    = $targetAmount > 0 ? max(0, $targetAmount - $achieved) : null;
            $pct          = $targetAmount > 0 ? round(($achieved / $targetAmount) * 100, 1) : 0;

            return [
                'employee'      => $emp,
                'target'        => $targetAmount,
                'achieved'      => $achieved,
                'remaining'     => $remaining,
                'percentage'    => $pct,
                'registrations' => $registrations,
                'total_leads'   => $totalLeads,
                'active_leads'  => $activeLeads,
            ];
        })->sortByDesc('achieved')->values();

        // Overall KPIs
        $overallKpis = [
            'total_target'        => $rows->sum('target'),
            'total_achieved'      => $rows->sum('achieved'),
            'total_registrations' => $rows->sum('registrations'),
            'top_cs'              => $rows->first(),
            'avg_achievement'     => $rows->where('target', '>', 0)->avg('percentage') ?? 0,
        ];

        // Daily breakdown for chart (all CS combined)
        $dailyData = RevenueSplit::whereIn('employee_id', $csEmployees->pluck('employee_id'))
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->selectRaw('DATE(created_at) as day, SUM(amount_allocated) as total')
            ->groupBy('day')->orderBy('day')->get();

        return view('admin.sales.index', compact(
            'rows', 'overallKpis', 'dailyData',
            'filterType', 'month', 'day', 'targetMonth'
        ));
    }

    private function getDateRange(string $filterType, string $month, string $day): array
    {
        return match($filterType) {
            'day'   => ['start' => Carbon::parse($day)->startOfDay(),   'end' => Carbon::parse($day)->endOfDay()],
            'week'  => ['start' => Carbon::parse($day)->startOfWeek(),  'end' => Carbon::parse($day)->endOfWeek()],
            default => ['start' => Carbon::parse($month)->startOfMonth(),'end' => Carbon::parse($month)->endOfMonth()],
        };
    }

    private function getTargetMonth(string $filterType, string $month, string $day): string
    {
        return match($filterType) {
            'day','week' => Carbon::parse($day)->format('Y-m'),
            default      => $month,
        };
    }
}