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
            'total_hours'              => 'nullable|numeric|min:1',
            'default_session_duration' => 'nullable|numeric|min:0.5',
            'max_capacity'             => 'nullable|integer|min:1',
            
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
                'total_hours'              => $request->total_hours,              
                'default_session_duration' => $request->default_session_duration, 
                'max_capacity'             => $request->max_capacity,  
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
                        'level_id'                => $level->level_id,
                        'name'                    => $sub['name'],
                        'price'                   => $sub['price'] ?? $lvl['price'],
                        'sublevel_order'          => $j + 1,
                        'total_hours'             => $sub['total_hours'] ?? $lvl['total_hours'],
                        'default_session_duration'=> $sub['default_session_duration'] ?? $lvl['default_session_duration'],
                        'max_capacity'            => $sub['max_capacity'] ?? $lvl['max_capacity'],
                        'teacher_min_level'       => $sub['teacher_level'] ?? $lvl['teacher_level'] ?? null,
                        'created_by_admin_id'     => $adminEmployeeId,
                        'is_active'               => true,
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
        $existingLevels = $course->levels
            ->sortBy('level_order')
            ->map(function ($l) {
                return [
                    'level_id' => $l->level_id,
                    'name' => $l->name,
                    'price' => $l->price,
                    'total_hours' => $l->total_hours,
                    'default_session_duration' => $l->default_session_duration,
                    'max_capacity' => $l->max_capacity,
                    'teacher_level' => $l->teacher_level,
                    'sublevels' => $l->sublevels->sortBy('sublevel_order')->map(function($s) {
                        return [
                            'sublevel_id'              => $s->sublevel_id,
                            'name'                     => $s->name,
                            'price'                    => $s->price,
                            'total_hours'              => $s->total_hours,
                            'default_session_duration' => $s->default_session_duration,
                            'max_capacity'             => $s->max_capacity,
                            'teacher_min_level'        => $s->teacher_min_level,
                        ];
                    })->values(),
                ];
            })
            ->values();
        return view('admin.courses.edit', compact('course', 'englishLevels', 'existingLevels'));
    }

    public function update(Request $request, $id)
    {
        $course = CourseTemplate::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'nullable|numeric|min:0',
            'english_level_id' => 'nullable|exists:english_level,english_level_id',
            'total_hours'              => 'nullable|numeric|min:1',
            'default_session_duration' => 'nullable|numeric|min:0.5',
            'max_capacity'             => 'nullable|integer|min:1',

            'levels'                            => 'nullable|array',
            'levels.*.level_id'                 => 'nullable|integer|exists:level,level_id',
            'levels.*.name'                     => 'required|string',
            'levels.*.price'                    => 'required|numeric|min:0',
            'levels.*.total_hours'              => 'required|numeric|min:1',
            'levels.*.default_session_duration' => 'required|numeric|min:0.5',
            'levels.*.max_capacity'             => 'required|integer|min:1',
            'levels.*.teacher_level'            => 'required|exists:english_level,english_level_id',
            'levels.*.sublevels'                => 'nullable|array',
            'levels.*.sublevels.*.sublevel_id'  => 'nullable|integer|exists:sublevel,sublevel_id',
            'levels.*.sublevels.*.name'         => 'required|string',
            'levels.*.sublevels.*.price'        => 'nullable|numeric|min:0',
        ]);

        $skipped = [];

        DB::transaction(function () use ($request, $course, &$skipped) {
            $adminEmployeeId = Employee::where('user_id', auth()->id())->first()?->employee_id;

            // ── Update course basic info ─────────────────────────
            $course->update([
                'name'                     => $request->name,
                'price'                    => $request->price,
                'english_level_id'         => $request->english_level_id,
                'total_hours'              => $request->total_hours,
                'default_session_duration' => $request->default_session_duration,
                'max_capacity'             => $request->max_capacity,
            ]);

            AuditService::updated('course_template', $course->course_template_id, 'name', $course->name, $request->name);

            // ── Process submitted levels ─────────────────────────
            $submittedLevelIds = [];

            foreach ($request->levels ?? [] as $i => $lvl) {
                $levelData = [
                    'course_template_id'       => $course->course_template_id,
                    'name'                     => $lvl['name'],
                    'price'                    => $lvl['price'],
                    'total_hours'              => $lvl['total_hours'],
                    'default_session_duration' => $lvl['default_session_duration'],
                    'max_capacity'             => $lvl['max_capacity'],
                    'teacher_level'            => $lvl['teacher_level'],
                    'level_order'              => $i + 1,
                    'is_active'                => true,
                ];

                if (!empty($lvl['level_id'])) {
                    $level = Level::find($lvl['level_id']);
                    if ($level && $level->course_template_id == $course->course_template_id) {
                        $level->update($levelData);
                    } else {
                        continue;
                    }
                } else {
                    $levelData['created_by_admin_id'] = $adminEmployeeId;
                    $level = Level::create($levelData);
                }
                $submittedLevelIds[] = $level->level_id;

                // ── Sublevels ─────────────────────────────────────
                $submittedSubIds = [];
                foreach ($lvl['sublevels'] ?? [] as $j => $sub) {
                    $subData = [
                        'level_id'                 => $level->level_id,
                        'name'                     => $sub['name'],
                        'price'                    => $sub['price'] ?? $lvl['price'],
                        'sublevel_order'           => $j + 1,
                        'total_hours'              => $sub['total_hours'] ?? $lvl['total_hours'],
                        'default_session_duration' => $sub['default_session_duration'] ?? $lvl['default_session_duration'],
                        'max_capacity'             => $sub['max_capacity'] ?? $lvl['max_capacity'],
                        'teacher_min_level'        => $sub['teacher_level'] ?? $lvl['teacher_level'] ?? null,
                        'is_active'                => true,
                    ];

                    if (!empty($sub['sublevel_id'])) {
                        $subModel = Sublevel::find($sub['sublevel_id']);
                        if ($subModel && $subModel->level_id == $level->level_id) {
                            $subModel->update($subData);
                        } else {
                            continue;
                        }
                    } else {
                        $subData['created_by_admin_id'] = $adminEmployeeId;
                        $subModel = Sublevel::create($subData);
                    }
                    $submittedSubIds[] = $subModel->sublevel_id;
                }

                // Delete removed sublevels (skip if in use)
                $removedSubs = $level->sublevels()
                    ->whereNotIn('sublevel_id', $submittedSubIds ?: [0])
                    ->get();

                foreach ($removedSubs as $sub) {
                    $inUse = \App\Models\Academic\CourseInstance::where('sublevel_id', $sub->sublevel_id)->exists()
                        || \App\Models\Enrollment\Enrollment::where('sublevel_id', $sub->sublevel_id)->exists();

                    if ($inUse) {
                        $skipped[] = "Sublevel '{$sub->name}' (in use)";
                    } else {
                        $sub->delete();
                    }
                }
            }

            // ── Delete removed levels (skip if in use) ──────────
            $removedLevels = $course->levels()
                ->whereNotIn('level_id', $submittedLevelIds ?: [0])
                ->get();

            foreach ($removedLevels as $lvl) {
                $inUse = \App\Models\Academic\CourseInstance::where('level_id', $lvl->level_id)->exists()
                    || \App\Models\Enrollment\Enrollment::where('level_id', $lvl->level_id)->exists();

                if ($inUse) {
                    $skipped[] = "Level '{$lvl->name}' (in use)";
                } else {
                    // Also try to remove its sublevels first
                    foreach ($lvl->sublevels as $s) {
                        $sInUse = \App\Models\Academic\CourseInstance::where('sublevel_id', $s->sublevel_id)->exists()
                            || \App\Models\Enrollment\Enrollment::where('sublevel_id', $s->sublevel_id)->exists();
                        if (!$sInUse) $s->delete();
                    }
                    $lvl->delete();
                }
            }
        });

        $msg = 'Course updated successfully.';
        if (!empty($skipped)) {
            $msg .= ' Note: ' . implode(', ', $skipped) . ' were kept because they have existing enrollments/instances.';
        }

        return redirect()->route('admin.courses.edit', $course->course_template_id)
            ->with('success', $msg);
    }

    public function archive($id)
    {
        $course = CourseTemplate::findOrFail($id);

        $hasActive = $course->courseInstances()
            ->whereIn('status', ['Active', 'Upcoming'])->exists();

        if ($hasActive) {
            return back()->with('error', 'Cannot archive course with active instances.');
        }

        $old = $course->is_active; 
        $course->update(['is_active' => !$old]);

        AuditService::updated('course_template', $id, 'is_active', 
            $old ? 'Active' : 'Archived', 
            $old ? 'Archived' : 'Active'
        );

        $msg = $old ? 'Course archived.' : 'Course restored.';
        return back()->with('success', $msg);
    }
}