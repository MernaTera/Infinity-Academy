<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HR\ContractType;
use App\Models\HR\Employee;
use App\Support\BranchContext;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ContractTypeAdminController extends Controller
{
    public function index()
    {
        $contractTypes = ContractType::with('createdByAdmin')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.contract-types.index', compact('contractTypes'));
    }

    public function store(Request $request)
    {
        // Contract-type names are unique PER BRANCH, not globally: the same name
        // (e.g. "Full-time") may exist in different branches. Scope the unique
        // check to the current branch so a duplicate name is only rejected
        // within the same branch.
        $branchId = BranchContext::currentBranchId();

        $request->validate([
            'name'                 => [
                'required', 'string', 'max:100',
                Rule::unique('contract_type', 'name')->where(fn($q) => $q->where('branch_id', $branchId)),
            ],
            'max_sessions_allowed' => 'required|integer|min:1|max:200',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->value('employee_id');

        ContractType::create([
            'name'                 => $request->name,
            'max_sessions_allowed' => $request->max_sessions_allowed,
            'is_active'            => true,
            'created_by_admin_id'  => $adminId,
        ]);

        return back()->with('success', 'Contract type created successfully.');
    }

    public function update(Request $request, $id)
    {
        // Same per-branch uniqueness as store(), ignoring the row being edited.
        $branchId = BranchContext::currentBranchId();

        $request->validate([
            'name'                 => [
                'required', 'string', 'max:100',
                Rule::unique('contract_type', 'name')
                    ->ignore($id, 'contract_type_id')
                    ->where(fn($q) => $q->where('branch_id', $branchId)),
            ],
            'max_sessions_allowed' => 'required|integer|min:1|max:200',
        ]);

        ContractType::findOrFail($id)->update([
            'name'                 => $request->name,
            'max_sessions_allowed' => $request->max_sessions_allowed,
        ]);

        return back()->with('success', 'Contract type updated.');
    }

    public function toggle($id)
    {
        $ct = ContractType::findOrFail($id);
        $ct->update(['is_active' => !$ct->is_active]);
        return back()->with('success', 'Status updated.');
    }
}