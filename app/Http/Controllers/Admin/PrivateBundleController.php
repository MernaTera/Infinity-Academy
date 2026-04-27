<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\PrivateBundle;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class PrivateBundleController extends Controller
{
    public function index()
    {
        $bundles = PrivateBundle::with('createdBy')
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'    => $bundles->count(),
            'active'   => $bundles->where('is_active', true)->count(),
            'inactive' => $bundles->where('is_active', false)->count(),
            'total_enrollments' => $bundles->sum('enrollments_count'),
        ];

        return view('admin.bundles.index', compact('bundles', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0.5',
            'price' => 'required|numeric|min:0',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->value('employee_id');

        PrivateBundle::create([
            'hours'               => $request->hours,
            'price'               => $request->price,
            'is_active'           => true,
            'created_by_admin_id' => $adminId,
        ]);

        return back()->with('success', 'Bundle created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0.5',
            'price' => 'required|numeric|min:0',
        ]);

        PrivateBundle::findOrFail($id)->update([
            'hours' => $request->hours,
            'price' => $request->price,
        ]);

        return back()->with('success', 'Bundle updated successfully.');
    }

    public function toggle($id)
    {
        $bundle = PrivateBundle::findOrFail($id);
        $bundle->update(['is_active' => !$bundle->is_active]);
        return back()->with('success', 'Bundle status updated.');
    }

    public function destroy($id)
    {
        $bundle = PrivateBundle::withCount('enrollments')->findOrFail($id);

        if ($bundle->enrollments_count > 0) {
            return back()->with('error', 'Cannot delete bundle — it has active enrollments.');
        }

        $bundle->delete();
        return back()->with('success', 'Bundle deleted successfully.');
    }
}