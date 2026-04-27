<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\PaymentPlan;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use App\Services\AuditService;

class PaymentPolicyController extends Controller
{
    public function index()
    {
        $plans = PaymentPlan::with('createdBy')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'    => $plans->count(),
            'active'   => $plans->where('is_active', true)->count(),
            'approval' => $plans->where('requires_admin_approval', true)->count(),
        ];

        return view('admin.payment-policy.index', compact('plans', 'stats'));
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:100',
            'deposit_percentage'      => 'required|numeric|min:0|max:100',
            'installment_count'       => 'required|integer|min:0',
            'grace_period_days'       => 'required|integer|min:0',
            'requires_admin_approval' => 'boolean',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        PaymentPlan::create([
            'name'                    => $request->name,
            'deposit_percentage'      => $request->deposit_percentage,
            'installment_count'       => $request->installment_count,
            'grace_period_days'       => $request->grace_period_days,
            'requires_admin_approval' => $request->boolean('requires_admin_approval'),
            'is_active'               => true,
            'created_by_admin_id'     => $adminId,
        ]);
        return back()->with('success', 'Payment plan created successfully.');
    }

    public function updatePlan(Request $request, $id)
    {
        $plan = PaymentPlan::findOrFail($id);

        $request->validate([
            'name'               => 'required|string|max:100',
            'deposit_percentage' => 'required|numeric|min:0|max:100',
            'grace_period_days'  => 'required|integer|min:0',
        ]);

        $plan->update($request->only([
            'name', 'deposit_percentage', 'grace_period_days', 'requires_admin_approval'
        ]));

        AuditService::updated('payment_plan', $id, 'name', $plan->getOriginal('name'), $plan->name);
        return back()->with('success', 'Plan updated.');
    }

    public function togglePlan($id)
    {
        $plan = PaymentPlan::findOrFail($id);
        AuditService::updated('payment_plan', $id, 'is_active', $plan->is_active, !$plan->is_active);
        $plan->update(['is_active' => !$plan->is_active]);
        return back()->with('success', 'Plan ' . ($plan->is_active ? 'activated' : 'deactivated') . '.');
    }
}