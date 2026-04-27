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
        $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $filterType = $request->query('filter', 'patch'); 
        $patchId  = $request->query('patch_id');
        $month = $request->query('month', now()->format('Y-m'));
        $day = $request->query('day', now()->format('Y-m-d'));
        // Default to current active patch
       $currentPatch = $patchId
            ? Patch::findOrFail($patchId)
            : Patch::active()->latest('start_date')->first();

        $allPatches  = Patch::orderByDesc('start_date')->get();

        $data = $this->salesService->getSalesData($employee, $currentPatch, $filterType, $month, $day);

        return view('sales.index', array_merge($data, [
            'currentPatch' => $currentPatch,
            'allPatches'   => $allPatches,
            'employee'     => $employee,
            'filterType'   => $filterType,
            'month'        => $month,
            'day'          => $day,
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