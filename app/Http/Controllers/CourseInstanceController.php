<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Academic\CourseInstance;

class CourseInstanceController extends Controller
{
    public function index()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','enrollments'
        ])->latest()->paginate(10);

        $templates = \App\Models\Academic\CourseTemplate::all();
        $teachers  = \App\Models\HR\Teacher::all();
        $patches   = \App\Models\Academic\Patch::all();
        $branches  = \App\Models\Core\Branch::all();

        return view('student-care.course-instances.index', compact(
            'instances','templates','teachers','patches','branches'
        ));
    }
    public function instances()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','enrollments'
        ])->latest()->paginate(10);

        $templates = CourseTemplate::all();
        $teachers  = Teacher::all();
        $patches   = Patch::all();
        $branches  = Branch::all();

        return view('student-care.instances.index', compact(
            'instances','templates','teachers','patches','branches'
        ));
    }

    public function storeInstance(Request $request)
    {
        $data = $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'level_id' => 'nullable|exists:level,level_id',
            'sublevel_id' => 'nullable|exists:sublevel,sublevel_id',

            'patch_id' => 'required|exists:patch,patch_id',
            'teacher_id' => 'required|exists:teacher,teacher_id',
            'branch_id' => 'required|exists:branch,branch_id',

            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',

            'capacity' => 'required|integer|min:1',

            'delivery_mood' => 'required|in:Online,Offline',
            'type' => 'required|in:Group,Private',

            'total_hours' => 'required|numeric',
            'session_duration' => 'required|numeric',
        ]);

        CourseInstance::create([
            ...$data,
            'status' => 'Upcoming',
            'created_by_employee_id' => auth()->user()->employees->first()->employee_id ?? null,
        ]);

        return back()->with('success', 'Instance created successfully');
    }
}
