<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\LevelPackage;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class LevelPackageController extends Controller
{
    public function index()
    {
        $packages = LevelPackage::with(['courseTemplate', 'createdBy'])
            ->orderByDesc('created_at')
            ->get();

        $courses = CourseTemplate::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total'    => $packages->count(),
            'active'   => $packages->where('is_active', true)->count(),
            'inactive' => $packages->where('is_active', false)->count(),
            'courses'  => $packages->unique('course_template_id')->count(),
        ];

        return view('admin.packages.index', compact('packages', 'courses', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'name'               => 'required|string|max:100',
            'levels_count'       => 'required|integer|min:1',
            'package_price'      => 'required|numeric|min:0',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->value('employee_id');

        LevelPackage::create([
            'course_template_id'  => $request->course_template_id,
            'name'                => $request->name,
            'levels_count'        => $request->levels_count,
            'package_price'       => $request->package_price,
            'is_active'           => true,
            'created_by_admin_id' => $adminId,
        ]);

        return back()->with('success', 'Package created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'name'               => 'required|string|max:100',
            'levels_count'       => 'required|integer|min:1',
            'package_price'      => 'required|numeric|min:0',
        ]);

        $package = LevelPackage::findOrFail($id);
        $package->update([
            'course_template_id' => $request->course_template_id,
            'name'               => $request->name,
            'levels_count'       => $request->levels_count,
            'package_price'      => $request->package_price,
        ]);

        return back()->with('success', 'Package updated successfully.');
    }

    public function toggle($id)
    {
        $package = LevelPackage::findOrFail($id);
        $package->update(['is_active' => !$package->is_active]);
        return back()->with('success', 'Package status updated.');
    }

    public function destroy($id)
    {
        LevelPackage::findOrFail($id)->delete();
        return back()->with('success', 'Package deleted successfully.');
    }
}