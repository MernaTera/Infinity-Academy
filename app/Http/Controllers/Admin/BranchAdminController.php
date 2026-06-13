<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Core\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BranchAdminController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['employees','courseInstances'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:150|unique:branch,name',
            'code'    => 'nullable|string|max:50|unique:branch,code',
            'address' => 'nullable|string|max:200',
            'phone'   => 'nullable|string|max:20',
        ]);

        Branch::create($request->only('name','code','address','phone') + ['is_active' => true]);

        return back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, $id)
    {
        if (!Hash::check($request->confirm_password, auth()->user()->password)) {
            return back()->with('error', 'Incorrect password. Action cancelled.')->withInput();
        }

        $request->validate([
            'name'    => 'required|string|max:150|unique:branch,name,' . $id . ',branch_id',
            'code'    => 'nullable|string|max:50|unique:branch,code,' . $id . ',branch_id',
            'address' => 'nullable|string|max:200',
            'phone'   => 'nullable|string|max:20',
        ]);

        Branch::findOrFail($id)->update($request->only('name','code','address','phone'));

        return back()->with('success', 'Branch updated successfully.');
    }

    public function toggle(Request $request, $id)
    {
        if (!Hash::check($request->confirm_password, auth()->user()->password)) {
            return back()->with('error', 'Incorrect password. Action cancelled.');
        }

        $branch = Branch::findOrFail($id);
        $branch->update(['is_active' => !$branch->is_active]);

        return back()->with('success', 'Branch status updated.');
    }

    public function destroy(Request $request, $id)
    {
        if (!Hash::check($request->confirm_password, auth()->user()->password)) {
            return back()->with('error', 'Incorrect password. Action cancelled.');
        }

        $branch = Branch::withCount(['employees','courseInstances'])->findOrFail($id);

        if ($branch->employees_count > 0 || $branch->course_instances_count > 0) {
            return back()->with('error', 'Cannot delete branch with existing employees or courses.');
        }

        $branch->delete();

        return back()->with('success', 'Branch deleted.');
    }
}