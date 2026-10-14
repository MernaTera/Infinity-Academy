<?php

namespace App\Http\Controllers;

use App\Services\SalesService;
use App\Models\Academic\Patch;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    protected SalesService $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    /*
    |------------------------------------------------------------------
    | Main Sales Table View
    |------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $employee   = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $filterType = $request->query('filter', 'month'); 
        $month      = $request->query('month', now()->format('Y-m'));
        $day        = $request->query('day', now()->format('Y-m-d'));

        $data = $this->salesService->getSalesData($employee, null, $filterType, $month, $day);

        $allRows = collect($data['revenueRows'] ?? []);
        $data['revenueTotals'] = [
            'count'    => $allRows->count(),
            'deposit'  => $allRows->sum('deposit'),
            'test_fee' => $allRows->sum('test_fee'),
            'material' => $allRows->sum('material'),
            'total'    => $allRows->sum('total'),
        ];
        $data['revenueRows'] = \App\Support\PaginateCollection::make($allRows, 25);

        return view('sales.index', array_merge($data, [
            'employee'   => $employee,
            'filterType' => $filterType,
            'month'      => $month,
            'day'        => $day,
        ]));
    }

    /*
    |------------------------------------------------------------------
    | AJAX — Daily Revenue (for chart)
    |------------------------------------------------------------------
    */
    public function dailyBreakdown(Request $request)
    {
        $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $patchId  = $request->query('patch_id');

        $patch = $patchId
            ? Patch::findOrFail($patchId)
            : Patch::active()->latest('start_date')->first();

        $daily = $this->salesService->getDailyRevenue($employee, $patch);

        return response()->json($daily);
    }
}