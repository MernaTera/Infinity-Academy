<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HR\Employee;
use App\Services\AuditService;

class MaterialController extends Controller
{
    public function index()
    {
        // جيبي كل الـ materials مع الـ assignments
        $materials = DB::table('materials')
            ->orderByDesc('created_at')
            ->get();

        // جيبي الـ assignments لكل material
        $assignments = DB::table('material_assignment')
            ->leftJoin('course_template', 'course_template.course_template_id', '=', 'material_assignment.course_template_id')
            ->leftJoin('level', 'level.level_id', '=', 'material_assignment.level_id')
            ->leftJoin('sublevel', 'sublevel.sublevel_id', '=', 'material_assignment.sublevel_id')
            ->select(
                'material_assignment.*',
                'course_template.name as course_name',
                'level.name as level_name',
                'sublevel.name as sublevel_name'
            )
            ->get()
            ->groupBy('material_id');

        // Courses for assignment dropdown
        $courses = DB::table('course_template')->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total'  => $materials->count(),
            'active' => $materials->where('is_active', 1)->count(),
            'assigned' => DB::table('material_assignment')->distinct('material_id')->count('material_id'),
        ];

        return view('admin.materials.index', compact('materials', 'assignments', 'courses', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'cs_percentage' => 'required|integer|min:0|max:100',
        ]);

        $adminId = Employee::where('user_id', auth()->id())->value('employee_id');

        DB::table('materials')->insert([
            'name'                => $request->name,
            'price'               => $request->price,
            'cs_percentage'       => $request->cs_percentage,
            'is_active'           => true,
            'created_by_admin_id' => $adminId,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return back()->with('success', 'Material created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'cs_percentage' => 'required|integer|min:0|max:100',
        ]);

        DB::table('materials')->where('material_id', $id)->update([
            'name'          => $request->name,
            'price'         => $request->price,
            'cs_percentage' => $request->cs_percentage,
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Material updated successfully.');
    }

    public function toggle($id)
    {
        $material = DB::table('materials')->where('material_id', $id)->first();
        DB::table('materials')->where('material_id', $id)->update([
            'is_active'  => !$material->is_active,
            'updated_at' => now(),
        ]);
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

        // تأكد مفيش duplicate
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

    // AJAX — get levels by course
    public function getLevels($courseId)
    {
        $levels = DB::table('level')
            ->where('course_template_id', $courseId)
            ->where('is_active', true)
            ->orderBy('level_order')
            ->get(['level_id', 'name']);
        return response()->json($levels);
    }

    // AJAX — get sublevels by level
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