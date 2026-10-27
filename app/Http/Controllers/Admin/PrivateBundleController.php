<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\PrivateBundle;
use App\Models\HR\Employee;
use App\Models\Academic\CourseTemplate;
use Illuminate\Http\Request;

class PrivateBundleController extends Controller
{
    public function index()
    {
        $bundles = PrivateBundle::with(['createdBy', 'courseTemplate'])
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'    => $bundles->count(),
            'active'   => $bundles->where('is_active', true)->count(),
            'inactive' => $bundles->where('is_active', false)->count(),
            'total_enrollments' => $bundles->sum('enrollments_count'),
        ];

        $courses = CourseTemplate::where('is_active', true)->orderBy('name')->get();

        return view('admin.bundles.index', compact('bundles', 'stats', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:100',
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'hours'              => 'required|numeric|min:0.5',
            'price'              => 'required|numeric|min:0',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->value('employee_id');

        PrivateBundle::create([
            'name'                => trim($request->name),
            'course_template_id'  => $request->course_template_id,
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
            'name'               => 'required|string|max:100',
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'hours'              => 'required|numeric|min:0.5',
            'price'              => 'required|numeric|min:0',
        ]);

        PrivateBundle::findOrFail($id)->update([
            'name'               => trim($request->name),
            'course_template_id' => $request->course_template_id,
            'hours'              => $request->hours,
            'price'              => $request->price,
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