<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Academic\EnglishLevel;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;

class CourseAdminController extends Controller
{
    public function index()
    {
        $courses = CourseTemplate::with(['levels.sublevels', 'englishLevel'])
            ->withCount(['levels', 'courseInstances'])
            ->latest()
            ->get();

        $stats = [
            'total'    => $courses->count(),
            'active'   => $courses->where('is_active', true)->count(),
            'archived' => $courses->where('is_active', false)->count(),
        ];

        return view('admin.courses.index', compact('courses', 'stats'));
    }

    public function create()
    {
        $englishLevels = EnglishLevel::all();
        return view('admin.courses.create', compact('englishLevels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'nullable|numeric|min:0',
            'english_level_id' => 'nullable|exists:english_level,english_level_id',
            // Levels
            'levels'                           => 'nullable|array',
            'levels.*.name'                    => 'required|string',
            'levels.*.price'                   => 'required|numeric|min:0',
            'levels.*.total_hours'             => 'required|numeric|min:1',
            'levels.*.default_session_duration'=> 'required|numeric|min:0.5',
            'levels.*.max_capacity'            => 'required|integer|min:1',
            'levels.*.teacher_level'           => 'required|exists:english_level,english_level_id',
        ]);

        DB::transaction(function () use ($request) {
            $adminEmployeeId = Employee::where('user_id', auth()->id())->first()?->employee_id;

            $course = CourseTemplate::create([
                'name'             => $request->name,
                'price'            => $request->price,
                'english_level_id' => $request->english_level_id,
                'is_active'        => true,
                'created_by_admin_id' => $adminEmployeeId,
            ]);

            AuditService::created('course_template', $course->course_template_id, 'name', $course->name);

            foreach ($request->levels ?? [] as $i => $lvl) {
                $level = Level::create([
                    'course_template_id'       => $course->course_template_id,
                    'name'                     => $lvl['name'],
                    'price'                    => $lvl['price'],
                    'total_hours'              => $lvl['total_hours'],
                    'default_session_duration' => $lvl['default_session_duration'],
                    'max_capacity'             => $lvl['max_capacity'],
                    'teacher_level'            => $lvl['teacher_level'],
                    'level_order'              => $i + 1,
                    'is_active'                => true,
                    'created_by_admin_id'      => $adminEmployeeId,
                ]);

                // Sublevels
                foreach ($lvl['sublevels'] ?? [] as $j => $sub) {
                    Sublevel::create([
                        'level_id'    => $level->level_id,
                        'name'        => $sub['name'],
                        'price'       => $sub['price'] ?? $lvl['price'],
                        'level_order' => $j + 1,
                        'is_active'   => true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit($id)
    {
        $course        = CourseTemplate::with(['levels.sublevels'])->findOrFail($id);
        $englishLevels = EnglishLevel::all();
        return view('admin.courses.edit', compact('course', 'englishLevels'));
    }

    public function update(Request $request, $id)
    {
        $course = CourseTemplate::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        $course->update([
            'name'             => $request->name,
            'price'            => $request->price,
            'english_level_id' => $request->english_level_id,
        ]);

        return back()->with('success', 'Course updated successfully.');
    }

    public function archive($id)
    {
        $course = CourseTemplate::findOrFail($id);

        $hasActive = $course->courseInstances()
            ->whereIn('status', ['Active', 'Upcoming'])->exists();

        if ($hasActive) {
            return back()->with('error', 'Cannot archive course with active instances.');
        }

        $course->update(['is_active' => !$course->is_active]);
        
        AuditService::updated('course_template', $id, 'is_active', $old ? 'Active' : 'Archived', $old ? 'Archived' : 'Active');
        $msg = $course->is_active ? 'Course restored.' : 'Course archived.';
        return back()->with('success', $msg);
    }
}