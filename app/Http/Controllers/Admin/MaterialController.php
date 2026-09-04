<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HR\Employee;
use App\Models\Enrollment\Material;
use App\Models\Academic\CourseTemplate;
use App\Services\AuditService;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::orderByDesc('created_at')->get();

        $branchMaterialIds = $materials->pluck('material_id')->all();

        $assignments = DB::table('material_assignment')
            ->whereIn('material_assignment.material_id', $branchMaterialIds)
            ->leftJoin('course_template', 'course_template.course_template_id', '=', 'material_assignment.course_template_id')
            ->leftJoin('level', 'level.level_id', '=', 'material_assignment.level_id')
            ->leftJoin('sublevel', 'sublevel.sublevel_id', '=', 'material_assignment.sublevel_id')
            ->select(
                'material_assignment.*',
                'course_template.name as course_name',
                'level.name as level_name',
                'sublevel.name as sublevel_name',
            )
            ->get()
            ->groupBy('material_id');

        $courses = CourseTemplate::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total'  => $materials->count(),
            'active' => $materials->where('is_active', 1)->count(),
            'assigned' => DB::table('material_assignment')
                ->whereIn('material_id', $branchMaterialIds)
                ->distinct('material_id')->count('material_id'),
        ];

        return view('admin.materials.index', compact('materials', 'assignments', 'courses', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'revenue_type' => 'required|in:Individual,Shared',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->value('employee_id');

        Material::create([
            'name'                => $request->name,
            'price'               => $request->price,
            'revenue_type'        => $request->revenue_type,
            'is_active'           => true,
            'created_by_admin_id' => $adminId,
        ]);

        return back()->with('success', 'Material created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'revenue_type' => 'required|in:Individual,Shared',
        ]);

        $material = Material::findOrFail($id);
        $material->update([
            'name'          => $request->name,
            'price'         => $request->price,
            'revenue_type'  => $request->revenue_type,
        ]);

        return back()->with('success', 'Material updated successfully.');
    }

    public function toggle($id)
    {
        $material = Material::findOrFail($id);
        $material->update(['is_active' => !$material->is_active]);
        return back()->with('success', 'Material status updated.');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'material_id'        => 'required|exists:materials,material_id',
            'course_template_id' => 'nullable|exists:course_template,course_template_id',
            'level_id'           => 'nullable|exists:level,level_id',
            'sublevel_id'        => 'nullable|exists:sublevel,sublevel_id',
            'is_mandatory'       => 'boolean',
        ]);

        if (Material::find($request->material_id) === null) {
            return back()->with('error', 'This material is not available for your branch.');
        }

        $exists = DB::table('material_assignment')
            ->where('material_id', $request->material_id)
            ->where('course_template_id', $request->course_template_id)
            ->where('level_id', $request->level_id)
            ->where('sublevel_id', $request->sublevel_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This assignment already exists.');
        }

        DB::table('material_assignment')->insert([
            'material_id'        => $request->material_id,
            'course_template_id' => $request->course_template_id,
            'level_id'           => $request->level_id,
            'sublevel_id'        => $request->sublevel_id,
            'is_mandatory'       => $request->boolean('is_mandatory'),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return back()->with('success', 'Material assigned successfully.');
    }

    public function unassign($id)
    {
        DB::table('material_assignment')->where('id', $id)->delete();
        return back()->with('success', 'Assignment removed.');
    }

    public function getLevels($courseId)
    {
        $levels = DB::table('level')
            ->where('course_template_id', $courseId)
            ->where('is_active', true)
            ->orderBy('level_order')
            ->get(['level_id', 'name']);
        return response()->json($levels);
    }

    public function getSublevels($levelId)
    {
        $sublevels = DB::table('sublevel')
            ->where('level_id', $levelId)
            ->where('is_active', true)
            ->orderBy('sublevel_id')
            ->get(['sublevel_id', 'name']);
        return response()->json($sublevels);
    }
}