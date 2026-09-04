<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\Core\Branch;
use App\Models\Academic\Room;
use App\Services\SchedulingService;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\BreakSlot;

class CourseInstanceController extends Controller
{
    protected $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    public function index()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','sessions','instanceSchedules','room',
            'enrollments' => fn($q) => $q->where('status', '!=', 'Cancelled'),
        ])->latest()->paginate(10);

        $templates = CourseTemplate::all();
        $teachers  = Teacher::whereHas('employee')->with('employee')->get();
        $patches   = Patch::whereIn('status', ['Active', 'Upcoming'])->get();
        $branches  = Branch::all();
        $rooms     = Room::all();

        return view('student-care.course-instances.index', compact(
            'instances','templates','teachers','patches','branches','rooms'
        ));
    }

    public function create()
    {
        $templates  = CourseTemplate::orderBy('name')->get();
        $patches    = Patch::whereIn('status', ['Active', 'Upcoming'])->orderBy('start_date')->get();
        $branches   = Branch::orderBy('name')->get();
        $rooms      = Room::where('is_active', true)->orderBy('name')->get();
        $breakSlots = BreakSlot::where('is_active', true)->get(['start_time', 'end_time']);
        $employee   = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $userBranch = Branch::find($employee->branch_id);

        return view('student-care.course-instances.create', compact(
            'templates', 'patches', 'branches', 'rooms', 'breakSlots', 'userBranch',
        ));
    }

    public function storeInstance(Request $request)
    {
        $data = $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'level_id'           => 'nullable|exists:level,level_id',
            'sublevel_id'        => 'nullable|exists:sublevel,sublevel_id',
            'patch_id'           => 'required|exists:patch,patch_id',
            'teacher_id'         => 'required|exists:teacher,teacher_id',
            'branch_id'          => 'required|exists:branch,branch_id',
            'room_id'            => 'nullable|exists:room,room_id',
            'capacity'           => 'required|integer|min:1',
            'delivery_mood'      => 'required|in:Online,Offline',
            'type'               => 'required|in:Group,Private',
            'total_hours'        => 'required|numeric|min:1',
            'session_duration'   => 'required|numeric|min:0.5',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'day_of_week'        => 'required|array|min:1',
            'day_of_week.*'      => 'in:sun_wed,sat_tue,mon_thu',
            'schedule_type'      => 'nullable|in:single,double',
            'single_days'        => 'nullable|array',
            'single_days.*'      => 'nullable|integer|between:0,6',
            'start_times'        => 'required|array',
            'start_times.*'      => 'required|date_format:H:i',
            'time_slot_ids'      => 'nullable|array',
            'time_slot_ids.*'    => 'nullable|exists:time_slot,time_slot_id',
        ]);

        $dayMap = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];

        $isSingle    = ($data['schedule_type'] ?? 'double') === 'single';
        $singleDays  = $isSingle ? ($data['single_days'] ?? []) : [];
        $resolveDays = function ($pair) use ($dayMap, $singleDays) {
            $pairDays = $dayMap[$pair] ?? [];
            $sd       = $singleDays[$pair] ?? null;
            if ($sd !== null && $sd !== '' && in_array((int) $sd, $pairDays, true)) {
                return [(int) $sd];
            }
            return $pairDays;
        };

        $patch = Patch::findOrFail($data['patch_id']);

        $startDate  = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
        $endDate    = \Carbon\Carbon::parse($data['end_date'])->startOfDay();
        $patchStart = \Carbon\Carbon::parse($patch->start_date)->startOfDay();
        $patchEnd   = \Carbon\Carbon::parse($patch->end_date)->startOfDay();

        if ($startDate->lt($patchStart) || $startDate->gt($patchEnd)) {
            return back()->withInput()->withErrors([
                'start_date' => "Start date must be within the selected patch ({$patchStart->format('Y-m-d')} → {$patchEnd->format('Y-m-d')})."
            ]);
        }
        if ($endDate->gt($patchEnd)) {
            return back()->withInput()->withErrors([
                'end_date' => "End date ({$endDate->format('Y-m-d')}) exceeds the patch end date ({$patchEnd->format('Y-m-d')})."
            ]);
        }

        $allTargetDays = array_merge(...array_map(fn($p) => $resolveDays($p), $data['day_of_week']));
        $current       = \Carbon\Carbon::parse($data['start_date']);
        $patchEnd      = \Carbon\Carbon::parse($patch->end_date);
        $found         = false;

        while ($current->lte($patchEnd)) {
            if (in_array($current->dayOfWeek, $allTargetDays)) {
                $data['start_date'] = $current->toDateString();
                if ($data['end_date'] < $data['start_date']) {
                    $data['end_date'] = $data['start_date'];
                }
                $found = true;
                break;
            }
            $current->addDay();
        }

        if (!$found) {
            return back()->withInput()->withErrors([
                'day_of_week' => 'No sessions possible for the selected day pair(s) within this patch.'
            ]);
        }

        if (!empty($data['room_id'])) {
            foreach ($data['day_of_week'] as $pair) {
                $startTime = $data['start_times'][$pair] ?? null;
                if (!$startTime) continue;

                $dur        = (float) $data['session_duration'];
                [$h, $m]    = explode(':', $startTime);
                $endMins    = ((int)$h * 60 + (int)$m) + (int)($dur * 60);
                $endTime    = sprintf('%02d:%02d:00', intdiv($endMins, 60), $endMins % 60);
                $startFull  = $startTime . ':00';
                $targetDays = $resolveDays($pair);

                $conflict = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
                    $q->where('room_id', $data['room_id'])
                      ->whereIn('status', ['Active', 'Upcoming'])
                )
                ->whereBetween('session_date', [$data['start_date'], $data['end_date']])
                ->where('status', '!=', 'Cancelled')
                ->where('start_time', '<', $endTime)
                ->where('end_time',   '>', $startFull)
                ->get()
                ->first(fn($s) => in_array(\Carbon\Carbon::parse($s->session_date)->dayOfWeek, $targetDays));

                if ($conflict) {
                    $course = $conflict->courseInstance?->courseTemplate?->name ?? 'another course';
                    return back()->withInput()->withErrors([
                        'room_id' => "Room already booked on {$pair} at {$startTime} — overlaps with \"{$course}\"."
                    ]);
                }
            }
        }

        $employeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $employeeId) {

                $contract = \App\Models\HR\TeacherContract::with('contractType')
                    ->where('teacher_id', $data['teacher_id'])
                    ->where('patch_id',   $data['patch_id'])
                    ->where('is_active',  true)
                    ->first();

                $newSessions = (int) ceil((float)$data['total_hours'] / (float)$data['session_duration']);

                $sessionCount = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
                    $q->where('teacher_id', $data['teacher_id'])
                      ->where('patch_id',   $data['patch_id'])
                      ->whereIn('status', ['Active', 'Upcoming', 'Pending_Approval'])
                )->where('status', '!=', 'Cancelled')->count();

                $pendingSessionsCount = \App\Models\Academic\CourseInstance::where('teacher_id', $data['teacher_id'])
                    ->where('patch_id', $data['patch_id'])
                    ->whereIn('status', ['Upcoming', 'Pending_Approval'])
                    ->whereDoesntHave('sessions')
                    ->get()
                    ->sum(fn($ci) => (int) ceil((float)$ci->total_hours / (float)$ci->session_duration));

                $existingSessions = $sessionCount + $pendingSessionsCount;
                $maxSessions      = $contract?->contractType?->max_sessions_allowed ?? null;
                $needsApproval    = $maxSessions && ($existingSessions + $newSessions) > $maxSessions;
                $overBy           = $needsApproval ? ($existingSessions + $newSessions) - $maxSessions : 0;

                $instance = CourseInstance::create([
                    'course_template_id'     => $data['course_template_id'],
                    'level_id'               => $data['level_id']   ?? null,
                    'sublevel_id'            => $data['sublevel_id'] ?? null,
                    'patch_id'               => $data['patch_id'],
                    'teacher_id'             => $data['teacher_id'],
                    'branch_id'              => $data['branch_id']  ?? null,
                    'room_id'                => $data['room_id']    ?? null,
                    'capacity'               => $data['capacity'],
                    'delivery_mood'          => $data['delivery_mood'],
                    'type'                   => $data['type'],
                    'total_hours'            => $data['total_hours'],
                    'session_duration'       => $data['session_duration'],
                    'start_date'             => $data['start_date'],
                    'end_date'               => $data['end_date'],
                    'status'                 => $needsApproval ? 'Pending_Approval' : 'Upcoming',
                    'created_by_employee_id' => $employeeId,
                ]);

                $teacher = \App\Models\HR\Teacher::with('employee')->find($data['teacher_id']);

                if ($needsApproval) {
                    \DB::table('user_notification')->insert([
                        'employee_id'         => $teacher->employee->employee_id,
                        'title'               => 'Course Approval Required',
                        'message'             => 'You have been assigned to teach "' . ($instance->courseTemplate?->name ?? 'a new course') . '". This exceeds your contract limit by ' . $overBy . ' session(s). Please approve or reject.',
                        'related_entity_type' => 'course_instance',
                        'related_entity_id'   => $instance->course_instance_id,
                        'is_read'             => false,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    $adminEmployees = \App\Models\HR\Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Admin'))
                        ->where('branch_id', $instance->branch_id)
                        ->get();
                    foreach ($adminEmployees as $admin) {
                        \DB::table('user_notification')->insert([
                            'employee_id'         => $admin->employee_id,
                            'title'               => 'Teacher Contract Limit Exceeded',
                            'message'             => ($teacher->employee->full_name ?? 'A teacher') . ' will exceed their contract limit by ' . $overBy . ' session(s) for "' . ($instance->courseTemplate?->name ?? 'a new course') . '". Awaiting teacher approval.',
                            'related_entity_type' => 'course_instance',
                            'related_entity_id'   => $instance->course_instance_id,
                            'is_read'             => false,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);
                    }

                    foreach ($data['day_of_week'] as $pair) {
                        $startTime  = $data['start_times'][$pair]  ?? null;
                        $timeSlotId = $data['time_slot_ids'][$pair] ?? null;
                        if (!$startTime) continue;
                        $slot = TimeSlot::find($timeSlotId);
                        if ($slot) {
                            $this->schedulingService->validateSchedule([
                                'start_time'       => $startTime,
                                'session_duration' => $data['session_duration'],
                                'time_slot'        => $slot,
                            ]);
                        }
                    }
                    $this->schedulingService->storeMultipleSchedules(
                        $instance->course_instance_id,
                        $data['day_of_week'],
                        $data['start_times'],
                        $data['time_slot_ids'] ?? null
                    );
                    return;
                }

                foreach ($data['day_of_week'] as $pair) {
                    $startTime  = $data['start_times'][$pair]  ?? null;
                    $timeSlotId = $data['time_slot_ids'][$pair] ?? null;
                    if (!$startTime || !$timeSlotId) continue;
                    $slot = TimeSlot::find($timeSlotId);
                    if ($slot) {
                        $this->schedulingService->validateSchedule([
                            'start_time'       => $startTime,
                            'session_duration' => $data['session_duration'],
                            'time_slot'        => $slot,
                        ]);
                    }
                }

                \App\Models\Academic\InstanceSchedule::where('course_instance_id', $instance->course_instance_id)->delete();
                \App\Models\Academic\CourseSession::where('course_instance_id',    $instance->course_instance_id)->delete();

                $schedules = $this->schedulingService->storeMultipleSchedules(
                    $instance->course_instance_id,
                    $data['day_of_week'],
                    $data['start_times'],
                    $data['time_slot_ids'] ?? null,
                    (($data['schedule_type'] ?? 'double') === 'single') ? ($data['single_days'] ?? []) : []
                );

                $generated = $this->schedulingService->generateSessionsMultiPair($instance, $schedules);

                if ($generated === 0) {
                    $instance->instanceSchedules()->delete();
                    $instance->delete();
                    throw new \Exception('No sessions could be generated. The start date does not match the chosen day pair(s).');
                }

                if ($teacher?->employee) {
                    \DB::table('user_notification')->insert([
                        'employee_id'         => $teacher->employee->employee_id,
                        'title'               => 'New Course Assigned',
                        'message'             => 'You have been assigned to teach "' . ($instance->courseTemplate?->name ?? 'a new course') . '".',
                        'related_entity_type' => 'course_instance',
                        'related_entity_id'   => $instance->course_instance_id,
                        'is_read'             => false,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                }
            });

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['schedule' => $e->getMessage()]);
        }

        return redirect()->route('student-care.instances')
            ->with('success', 'Course instance created successfully.');
    }

    public function edit($id)
    {
        $instance = CourseInstance::with(['instanceSchedules', 'sessions'])->findOrFail($id);

        $templates  = CourseTemplate::orderBy('name')->get();
        $patches    = Patch::whereIn('status', ['Active', 'Upcoming'])->orderBy('start_date')->get();
        $branches   = Branch::orderBy('name')->get();
        $rooms      = Room::where('is_active', true)->orderBy('name')->get();
        $breakSlots = BreakSlot::where('is_active', true)->get(['start_time', 'end_time']);
        $employee   = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $userBranch = Branch::find($employee->branch_id);

        $completedCount = $instance->sessions->where('status', 'Completed')->count();

        $currentPairs      = $instance->instanceSchedules->pluck('day_of_week')->unique()->values();
        $currentStartTimes = [];
        $currentSingleDays = [];   // pair => chosen single day number (if any)
        foreach ($instance->instanceSchedules as $sch) {
            $currentStartTimes[$sch->day_of_week] = \Carbon\Carbon::parse($sch->start_time)->format('H:i');
            if ($sch->single_day !== null) {
                $currentSingleDays[$sch->day_of_week] = (int) $sch->single_day;
            }
        }
        $scheduleType = count($currentSingleDays) > 0 ? 'single' : 'double';

        return view('student-care.course-instances.create', compact(
            'instance', 'templates', 'patches', 'branches', 'rooms', 'breakSlots', 'userBranch',
            'completedCount', 'currentPairs', 'currentStartTimes', 'currentSingleDays', 'scheduleType'
        ));
    }

    public function updateInstance(Request $request, $id)
    {
        $instance = CourseInstance::with(['sessions', 'instanceSchedules'])->findOrFail($id);

        $data = $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'level_id'           => 'nullable|exists:level,level_id',
            'sublevel_id'        => 'nullable|exists:sublevel,sublevel_id',
            'patch_id'           => 'required|exists:patch,patch_id',
            'teacher_id'         => 'required|exists:teacher,teacher_id',
            'branch_id'          => 'required|exists:branch,branch_id',
            'room_id'            => 'nullable|exists:room,room_id',
            'capacity'           => 'required|integer|min:1',
            'delivery_mood'      => 'required|in:Online,Offline',
            'type'               => 'required|in:Group,Private',
            'total_hours'        => 'required|numeric|min:1',
            'session_duration'   => 'required|numeric|min:0.5',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'day_of_week'        => 'required|array|min:1',
            'day_of_week.*'      => 'in:sun_wed,sat_tue,mon_thu',
            'schedule_type'      => 'nullable|in:single,double',
            'single_days'        => 'nullable|array',
            'single_days.*'      => 'nullable|integer|between:0,6',
            'start_times'        => 'required|array',
            'start_times.*'      => 'required|date_format:H:i',
            'time_slot_ids'      => 'nullable|array',
            'time_slot_ids.*'    => 'nullable|exists:time_slot,time_slot_id',
        ]);

        $dayMap = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];

        $isSingle    = ($data['schedule_type'] ?? 'double') === 'single';
        $singleDays  = $isSingle ? ($data['single_days'] ?? []) : [];
        $resolveDays = function ($pair) use ($dayMap, $singleDays) {
            $pairDays = $dayMap[$pair] ?? [];
            $sd       = $singleDays[$pair] ?? null;
            if ($sd !== null && $sd !== '' && in_array((int) $sd, $pairDays, true)) {
                return [(int) $sd];
            }
            return $pairDays;
        };

        $completedSessions = $instance->sessions->where('status', 'Completed');
        $completedCount    = $completedSessions->count();
        $totalSessions     = (int) ceil((float)$data['total_hours'] / (float)$data['session_duration']);
        $remainingToMake   = max(0, $totalSessions - $completedCount);

        $lastCompletedDate = $completedSessions->max('session_date');

        $patch = Patch::findOrFail($data['patch_id']);
        $endDateC   = \Carbon\Carbon::parse($data['end_date'])->startOfDay();
        $patchEndC  = \Carbon\Carbon::parse($patch->end_date)->startOfDay();
        if ($endDateC->gt($patchEndC)) {
            return back()->withInput()->withErrors([
                'end_date' => "End date ({$endDateC->format('Y-m-d')}) exceeds the patch end date ({$patchEndC->format('Y-m-d')})."
            ]);
        }

        $allTargetDays = array_merge(...array_map(fn($p) => $resolveDays($p), $data['day_of_week']));

        $genStart = \Carbon\Carbon::parse($data['start_date']);
        if ($lastCompletedDate) {
            $afterCompleted = \Carbon\Carbon::parse($lastCompletedDate)->addDay();
            if ($afterCompleted->gt($genStart)) {
                $genStart = $afterCompleted;
            }
        }

        $patchEnd = \Carbon\Carbon::parse($patch->end_date);
        $found    = false;
        $cursor   = $genStart->copy();
        while ($cursor->lte($patchEnd)) {
            if (in_array($cursor->dayOfWeek, $allTargetDays)) {
                $regenStartDate = $cursor->toDateString();
                $found = true;
                break;
            }
            $cursor->addDay();
        }

        if ($remainingToMake > 0 && !$found) {
            return back()->withInput()->withErrors([
                'day_of_week' => 'No sessions possible for the selected day pair(s) within this patch after the completed sessions.'
            ]);
        }

        if (!empty($data['room_id'])) {
            foreach ($data['day_of_week'] as $pair) {
                $startTime = $data['start_times'][$pair] ?? null;
                if (!$startTime) continue;

                $dur        = (float) $data['session_duration'];
                [$h, $m]    = explode(':', $startTime);
                $endMins    = ((int)$h * 60 + (int)$m) + (int)($dur * 60);
                $endTime    = sprintf('%02d:%02d:00', intdiv($endMins, 60), $endMins % 60);
                $startFull  = $startTime . ':00';
                $targetDays = $resolveDays($pair);

                $conflict = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
                    $q->where('room_id', $data['room_id'])
                      ->where('course_instance_id', '!=', $instance->course_instance_id) // not myself
                      ->whereIn('status', ['Active', 'Upcoming'])
                )
                ->whereBetween('session_date', [$regenStartDate ?? $data['start_date'], $data['end_date']])
                ->where('status', '!=', 'Cancelled')
                ->where('start_time', '<', $endTime)
                ->where('end_time',   '>', $startFull)
                ->get()
                ->first(fn($s) => in_array(\Carbon\Carbon::parse($s->session_date)->dayOfWeek, $targetDays));

                if ($conflict) {
                    $course = $conflict->courseInstance?->courseTemplate?->name ?? 'another course';
                    return back()->withInput()->withErrors([
                        'room_id' => "Room already booked on {$pair} at {$startTime} — overlaps with \"{$course}\"."
                    ]);
                }
            }
        }

        $employeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');
        $teacherChanged = (int)$instance->teacher_id !== (int)$data['teacher_id'];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $data, $employeeId, $instance, $completedCount, $remainingToMake,
                $regenStartDate, $teacherChanged
            ) {

                $contract = \App\Models\HR\TeacherContract::with('contractType')
                    ->where('teacher_id', $data['teacher_id'])
                    ->where('patch_id',   $data['patch_id'])
                    ->where('is_active',  true)
                    ->first();

                $sessionCount = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
                    $q->where('teacher_id', $data['teacher_id'])
                      ->where('patch_id',   $data['patch_id'])
                      ->where('course_instance_id', '!=', $instance->course_instance_id)
                      ->whereIn('status', ['Active', 'Upcoming', 'Pending_Approval'])
                )->where('status', '!=', 'Cancelled')->count();

                $pendingSessionsCount = \App\Models\Academic\CourseInstance::where('teacher_id', $data['teacher_id'])
                    ->where('patch_id', $data['patch_id'])
                    ->where('course_instance_id', '!=', $instance->course_instance_id)
                    ->whereIn('status', ['Upcoming', 'Pending_Approval'])
                    ->whereDoesntHave('sessions')
                    ->get()
                    ->sum(fn($ci) => (int) ceil((float)$ci->total_hours / (float)$ci->session_duration));

                $existingSessions = $sessionCount + $pendingSessionsCount;
                $maxSessions      = $contract?->contractType?->max_sessions_allowed ?? null;
                $thisInstanceTotal = $completedCount + $remainingToMake;
                $needsApproval    = $maxSessions && ($existingSessions + $thisInstanceTotal) > $maxSessions;
                $overBy           = $needsApproval ? ($existingSessions + $thisInstanceTotal) - $maxSessions : 0;

                $instance->update([
                    'course_template_id' => $data['course_template_id'],
                    'level_id'           => $data['level_id']   ?? null,
                    'sublevel_id'        => $data['sublevel_id'] ?? null,
                    'patch_id'           => $data['patch_id'],
                    'teacher_id'         => $data['teacher_id'],
                    'branch_id'          => $data['branch_id']  ?? null,
                    'room_id'            => $data['room_id']    ?? null,
                    'capacity'           => $data['capacity'],
                    'delivery_mood'      => $data['delivery_mood'],
                    'type'               => $data['type'],
                    'total_hours'        => $data['total_hours'],
                    'session_duration'   => $data['session_duration'],
                    'start_date'         => $instance->sessions->where('status','Completed')->min('session_date')
                                            ?? ($regenStartDate ?? $data['start_date']),
                    'end_date'           => $data['end_date'],
                    'status'             => $needsApproval ? 'Pending_Approval' : 'Upcoming',
                ]);

                \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
                    ->where('status', '!=', 'Completed')
                    ->delete();

                \App\Models\Academic\InstanceSchedule::where('course_instance_id', $instance->course_instance_id)
                    ->delete();

                if ($remainingToMake > 0 && $regenStartDate) {
                    foreach ($data['day_of_week'] as $pair) {
                        $startTime  = $data['start_times'][$pair]  ?? null;
                        $timeSlotId = $data['time_slot_ids'][$pair] ?? null;
                        if (!$startTime) continue;
                        $slot = TimeSlot::find($timeSlotId);
                        if ($slot) {
                            $this->schedulingService->validateSchedule([
                                'start_time'       => $startTime,
                                'session_duration' => $data['session_duration'],
                                'time_slot'        => $slot,
                            ]);
                        }
                    }

                    $schedules = $this->schedulingService->storeMultipleSchedules(
                        $instance->course_instance_id,
                        $data['day_of_week'],
                        $data['start_times'],
                        $data['time_slot_ids'] ?? null,
                        (($data['schedule_type'] ?? 'double') === 'single') ? ($data['single_days'] ?? []) : []
                    );

                    $instance->start_date = $regenStartDate;
                    $this->generateRemainingSessions($instance, $schedules, $completedCount, $remainingToMake);
                }

                $this->resyncInstallmentDueDates($instance);

                $teacher = \App\Models\HR\Teacher::with('employee')->find($data['teacher_id']);

                if ($needsApproval && $teacher?->employee) {
                    \DB::table('user_notification')->insert([
                        'employee_id'         => $teacher->employee->employee_id,
                        'title'               => 'Course Approval Required',
                        'message'             => 'An updated course "' . ($instance->courseTemplate?->name ?? 'a course') . '" now exceeds your contract limit by ' . $overBy . ' session(s). Please approve or reject.',
                        'related_entity_type' => 'course_instance',
                        'related_entity_id'   => $instance->course_instance_id,
                        'is_read'             => false,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    $adminEmployees = \App\Models\HR\Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Admin'))
                        ->where('branch_id', $instance->branch_id)
                        ->get();
                    foreach ($adminEmployees as $admin) {
                        \DB::table('user_notification')->insert([
                            'employee_id'         => $admin->employee_id,
                            'title'               => 'Teacher Contract Limit Exceeded',
                            'message'             => ($teacher->employee->full_name ?? 'A teacher') . ' will exceed their contract limit by ' . $overBy . ' session(s) on an updated course. Awaiting teacher approval.',
                            'related_entity_type' => 'course_instance',
                            'related_entity_id'   => $instance->course_instance_id,
                            'is_read'             => false,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);
                    }
                } elseif ($teacherChanged && $teacher?->employee) {
                    \DB::table('user_notification')->insert([
                        'employee_id'         => $teacher->employee->employee_id,
                        'title'               => 'Course Assigned',
                        'message'             => 'You have been assigned to teach "' . ($instance->courseTemplate?->name ?? 'a course') . '" (updated schedule).',
                        'related_entity_type' => 'course_instance',
                        'related_entity_id'   => $instance->course_instance_id,
                        'is_read'             => false,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                }
            });

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['schedule' => $e->getMessage()]);
        }

        return redirect()->route('student-care.instances')
            ->with('success', 'Course instance updated successfully.');
    }

    private function generateRemainingSessions(CourseInstance $instance, array $schedules, int $completedCount, int $remainingToMake): void
    {
        $dayMap = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];

        $pairCount = count($schedules);
        if ($pairCount === 0) return;

        $perPair   = (int) floor($remainingToMake / $pairCount);
        $remainder = $remainingToMake % $pairCount;

        $maxExisting = (int) \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->max('session_number');
        $sessionNum = max($completedCount, $maxExisting) + 1;
        $made       = 0;

        foreach ($schedules as $i => $schedule) {
            $need       = $perPair + ($i === 0 ? $remainder : 0);
            $pairDays   = $dayMap[$schedule->day_of_week] ?? [];
            $targetDays = ($schedule->single_day !== null && in_array((int) $schedule->single_day, $pairDays, true))
                ? [(int) $schedule->single_day]
                : $pairDays;
            if (empty($targetDays) || $need <= 0) continue;

            $cursor = \Carbon\Carbon::parse($instance->start_date);
            $end    = \Carbon\Carbon::parse($instance->end_date);
            $count  = 0;

            while ($cursor->lte($end) && $count < $need && $made < $remainingToMake) {
                if (in_array($cursor->dayOfWeek, $targetDays)) {
                    $startDateTime = \Carbon\Carbon::parse(
                        $cursor->toDateString() . ' ' . \Carbon\Carbon::parse($schedule->start_time)->format('H:i:s')
                    );
                    $endDateTime = $startDateTime->copy()->addHours((float) $instance->session_duration);

                    \App\Models\Academic\CourseSession::create([
                        'course_instance_id'      => $instance->course_instance_id,
                        'session_date'            => $cursor->toDateString(),
                        'start_time'              => $startDateTime->format('H:i:s'),
                        'end_time'                => $endDateTime->format('H:i:s'),
                        'session_number'          => $sessionNum,
                        'room_id'                 => $instance->room_id,
                        'generated_from_schedule' => true,
                        'status'                  => 'Scheduled',
                    ]);

                    $sessionNum++;
                    $count++;
                    $made++;
                }
                $cursor->addDay();
            }
        }

        $all = \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->orderBy('session_date')->orderBy('start_time')->get();
        foreach ($all as $s) {
            \DB::table('course_session')->where('course_session_id', $s->course_session_id)
                ->update(['session_number' => -$s->course_session_id]);
        }
        foreach ($all as $idx => $s) {
            \DB::table('course_session')->where('course_session_id', $s->course_session_id)
                ->update(['session_number' => $idx + 1]);
        }
    }

    private function resyncInstallmentDueDates(CourseInstance $instance): void
    {
        $sessions = \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)
            ->orderBy('session_date')->orderBy('start_time')
            ->get()
            ->values();

        if ($sessions->isEmpty()) return;

        foreach ($instance->enrollments as $enrollment) {
            $pending = \App\Models\Finance\InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)
                ->where('status', 'Pending')
                ->orderBy('installment_number')
                ->get();

            foreach ($pending as $i => $schedule) {
                $session = $sessions[$i] ?? null;
                if ($session) {
                    $schedule->update(['due_date' => $session->session_date]);
                }
            }
        }
    }

    public function show($id)
    {
        $instance = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher.employee',
            'branch','patch','room',
            'enrollments' => fn($q) => $q->where('status', '!=', 'Cancelled'),
            'enrollments.student.phones',
            'enrollments.installmentSchedules',
            'sessions',
            'instanceSchedules.timeSlot',
            'enrollments.activePostponement',
        ])->findOrFail($id);

        return view('student-care.course-instances.show', compact('instance'));
    }

    public function getTeachersByCourse($courseId)
    {
        $course = CourseTemplate::find($courseId);
        if (!$course || !$course->english_level_id) return response()->json([]);
        return response()->json(
            Teacher::where('english_level_id', '>=', $course->english_level_id)
                ->whereHas('employee')
                ->with('employee')->get()
        );
    }

    public function getTeachersByLevel($englishLevelId)
    {
        return response()->json(
            Teacher::where('english_level_id', '>=', $englishLevelId)
                ->where('is_active', true)
                ->whereHas('employee')
                ->with(['employee', 'englishLevel'])
                ->get()
        );
    }

    public function getTeacherAvailablePairs(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        if (!$teacherId) return response()->json([]);

        $availability = \App\Models\HR\TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->get();

        $patchId = $request->query('patch_id');

        $result = [];
        foreach ($availability as $av) {
            $pair     = $av->day_of_week;
            $timeSlot = $av->timeSlot;

            $existingCourses = [];
            if ($patchId) {
                $instances = CourseInstance::with(['courseTemplate', 'instanceSchedules'])
                    ->where('teacher_id', $teacherId)
                    ->where('patch_id', $patchId)
                    ->whereIn('status', ['Active', 'Upcoming', 'Pending_Approval'])
                    ->get();

                foreach ($instances as $ci) {
                    $sch = $ci->instanceSchedules->firstWhere('day_of_week', $pair);
                    if ($sch) {
                        $startTime = \Carbon\Carbon::parse($sch->start_time)->format('H:i');
                        $dur       = (float)$ci->session_duration;
                        $endTime   = \Carbon\Carbon::parse($sch->start_time)->addHours($dur)->format('H:i');
                        $existingCourses[] = [
                            'name'       => $ci->courseTemplate?->name ?? '—',
                            'start_time' => $startTime,
                            'end_time'   => $endTime,
                            'status'     => $ci->status,
                            'sessions'   => $ci->sessions()->count(),
                            'start_date' => \Carbon\Carbon::parse($ci->start_date)->format('d M'),  
                            'end_date'   => \Carbon\Carbon::parse($ci->end_date)->format('d M Y'),  
                        ];
                    }
                }
            }

            $result[] = [
                'pair'            => $pair,
                'slot_name'       => $timeSlot?->name ?? '—',
                'slot_start'      => $timeSlot ? \Carbon\Carbon::parse($timeSlot->start_time)->format('H:i') : null,
                'slot_end'        => $timeSlot ? \Carbon\Carbon::parse($timeSlot->end_time)->format('H:i') : null,
                'existing_courses'=> $existingCourses,
            ];
        }

        return response()->json($result);
    }

    public function getTimeSlotsForPair(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $pair      = $request->query('pair');

        if (!$teacherId || !$pair) return response()->json([]);

        $availabilities = \App\Models\HR\TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->where('day_of_week', $pair)
            ->get()
            ->filter(fn($a) => $a->timeSlot);

        if ($availabilities->isEmpty()) return response()->json([]);

        $breakSlots = BreakSlot::where('is_active', true)->get();

        $byStart = [];
        foreach ($availabilities as $av) {
            $slot      = $av->timeSlot;
            $slotStart = \Carbon\Carbon::createFromTimeString($slot->start_time);
            $slotEnd   = \Carbon\Carbon::createFromTimeString($slot->end_time);
            $current   = $slotStart->copy();

            while ($current->lt($slotEnd)) {
                $timeStr = $current->format('H:i');
                $isBreak = false;
                foreach ($breakSlots as $b) {
                    $bStart = \Carbon\Carbon::createFromTimeString($b->start_time);
                    $bEnd   = \Carbon\Carbon::createFromTimeString($b->end_time);
                    if ($current->gte($bStart) && $current->lt($bEnd)) { $isBreak = true; break; }
                }

                $endStr = $slotEnd->format('H:i');
                if (!isset($byStart[$timeStr]) || $endStr > $byStart[$timeStr]['end']) {
                    $byStart[$timeStr] = [
                        'start'    => $timeStr,
                        'end'      => $endStr,
                        'slot_id'  => $slot->time_slot_id,
                        'is_break' => $isBreak,
                    ];
                }
                $current->addMinutes(30);
            }
        }

        ksort($byStart);
        $slots = array_values($byStart);

        return response()->json($slots);
    }


    public function getTeacherFreeDates(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $patchId   = $request->query('patch_id');
        $excludeId = $request->query('exclude_instance_id');
        if (!$teacherId || !$patchId) return response()->json([]);

        $patch = Patch::find($patchId);
        if (!$patch) return response()->json([]);

        $dayMap   = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $availability = \App\Models\HR\TeacherAvailability::where('teacher_id', $teacherId)->get();

        $pairByDay = [];
        foreach ($availability as $av) {
            foreach ($dayMap[$av->day_of_week] ?? [] as $dayNum) {
                $pairByDay[$dayNum] = $av->day_of_week;
            }
        }
        if (empty($pairByDay)) return response()->json([]);

        $sessionsByDate = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', ['Active','Upcoming','Pending_Approval'])
            ->when($excludeId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeId))
        )->where('status','!=','Cancelled')
        ->get(['session_date', 'start_time', 'end_time'])
        ->groupBy(fn($s) => \Carbon\Carbon::parse($s->session_date)->format('Y-m-d'));

        $slotsByPair = [];
        foreach ($availability as $av) {
            if ($av->timeSlot) $slotsByPair[$av->day_of_week][] = $av->timeSlot;
        }
        $breakSlots = BreakSlot::where('is_active', true)->get();

        $minBlockMins = 30;

        $isDayFull = function ($dateStr, $pair) use ($sessionsByDate, $slotsByPair, $breakSlots, $minBlockMins) {
            $slots = $slotsByPair[$pair] ?? [];
            if (empty($slots)) return false; 

            $daySessions = $sessionsByDate[$dateStr] ?? collect();

            foreach ($slots as $slot) {
                $winStart = \Carbon\Carbon::parse($dateStr . ' ' . \Carbon\Carbon::parse($slot->start_time)->format('H:i:s'));
                $winEnd   = \Carbon\Carbon::parse($dateStr . ' ' . \Carbon\Carbon::parse($slot->end_time)->format('H:i:s'));

                $busy = [];
                foreach ($breakSlots as $b) {
                    $busy[] = [
                        \Carbon\Carbon::parse($dateStr . ' ' . \Carbon\Carbon::parse($b->start_time)->format('H:i:s')),
                        \Carbon\Carbon::parse($dateStr . ' ' . \Carbon\Carbon::parse($b->end_time)->format('H:i:s')),
                    ];
                }
                foreach ($daySessions as $s) {
                    $busy[] = [
                        \Carbon\Carbon::parse($dateStr . ' ' . \Carbon\Carbon::parse($s->start_time)->format('H:i:s')),
                        \Carbon\Carbon::parse($dateStr . ' ' . \Carbon\Carbon::parse($s->end_time)->format('H:i:s')),
                    ];
                }

                $cursor = $winStart->copy();
                while ($cursor->lt($winEnd)) {
                    $next = $winEnd->copy();
                    foreach ($busy as [$bStart, $bEnd]) {
                        if ($bEnd->lte($cursor)) continue;      
                        if ($bStart->lte($cursor)) {           
                            if ($bEnd->gt($cursor)) { $cursor = $bEnd->copy(); }
                            $next = null;
                            break;
                        }
                        if ($bStart->lt($next)) $next = $bStart->copy(); 
                    }
                    if ($next === null) continue;                
                    if (abs($next->diffInMinutes($cursor)) >= $minBlockMins) return false;
                    $cursor = $next->copy();
                }
            }
            return true; 
        };

        $result  = [];
        $current = \Carbon\Carbon::parse($patch->start_date);
        $end     = \Carbon\Carbon::parse($patch->end_date);

        while ($current->lte($end)) {
            $dow = $current->dayOfWeek;
            if (isset($pairByDay[$dow])) {
                $dateStr = $current->toDateString();
                $result[] = [
                    'date'     => $dateStr,
                    'day'      => $current->format('D'),
                    'display'  => $current->format('d M'),
                    'pair'     => $pairByDay[$dow],
                    'occupied' => $isDayFull($dateStr, $pairByDay[$dow]),
                ];
            }
            $current->addDay();
        }

        return response()->json($result);
    }

    public function getOccupiedSlots(Request $request)
    {
        $teacherId  = $request->query('teacher_id');
        $startDate  = $request->query('start_date');
        $endDate    = $request->query('end_date', '2099-12-31');
        $excludeId  = $request->query('exclude_instance_id');
        $pair       = $request->query('pair');
        $singleDay  = $request->query('single_day');
        $dayMap     = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $targetDays = $dayMap[$pair] ?? [];
        if ($singleDay !== null && $singleDay !== '' && in_array((int) $singleDay, $targetDays, true)) {
            $targetDays = [(int) $singleDay];
        }

        if (!$teacherId || !$startDate) return response()->json([]);

        $occupied = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacherId)
              ->when($excludeId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeId))
        )
        ->whereBetween('session_date', [$startDate, $endDate])
        ->where('status', '!=', 'Cancelled')
        ->get()
        ->when(!empty($targetDays), fn($rows) => $rows->filter(fn($s) =>
            in_array(\Carbon\Carbon::parse($s->session_date)->dayOfWeek, $targetDays, true)
        ))
        ->map(fn($s) => [
            'start' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
            'end'   => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
        ])
        ->unique(fn($r) => $r['start'] . '-' . $r['end'])
        ->values()->toArray();

        return response()->json($occupied);
    }

    public function checkConflicts(Request $request)
    {
        $teacherId  = $request->teacher_id;
        $startDate  = $request->start_date;
        $endDate    = $request->end_date ?? '2099-12-31';
        $pairs      = (array) $request->day_of_week;
        $startTime  = $request->start_time;
        $sessionDur = (float) $request->session_duration;
        $excludeId  = $request->exclude_instance_id;

        if (!$teacherId || !$startDate || !$pairs || !$startTime) {
            return response()->json(['conflicts' => []]);
        }

        $dayMap        = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $singleDays    = (array) $request->single_days;
        $isSingle      = ($request->schedule_type ?? 'double') === 'single';
        $resolveDays   = function ($pair) use ($dayMap, $singleDays, $isSingle) {
            $pd = $dayMap[$pair] ?? [];
            $sd = $singleDays[$pair] ?? null;
            if ($isSingle && $sd !== null && $sd !== '' && in_array((int) $sd, $pd, true)) {
                return [(int) $sd];
            }
            return $pd;
        };
        $allTargetDays = array_merge(...array_map(fn($p) => $resolveDays($p), $pairs));
        $newStart      = \Carbon\Carbon::createFromTimeString($startTime);
        $newEnd        = $newStart->copy()->addHours($sessionDur);

        $existingSessions = \App\Models\Academic\CourseSession::with('courseInstance.courseTemplate')
            ->whereHas('courseInstance', fn($q) =>
                $q->where('teacher_id', $teacherId)
                  ->when($excludeId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeId))
            )
            ->whereBetween('session_date', [$startDate, $endDate])
            ->where('status', '!=', 'Cancelled')
            ->get();

        $conflicts = [];
        foreach ($existingSessions as $s) {
            if (!in_array(\Carbon\Carbon::parse($s->session_date)->dayOfWeek, $allTargetDays)) continue;
            $sStart = \Carbon\Carbon::parse($s->start_time);
            $sEnd   = \Carbon\Carbon::parse($s->end_time);
            if ($newStart->lt($sEnd) && $newEnd->gt($sStart)) {
                $conflicts[] = sprintf('%s on %s at %s → %s',
                    $s->courseInstance?->courseTemplate?->name ?? 'Course',
                    \Carbon\Carbon::parse($s->session_date)->format('D d M Y'),
                    $sStart->format('H:i'), $sEnd->format('H:i')
                );
            }
        }

        return response()->json(['conflicts' => array_values(array_unique($conflicts))]);
    }

    public function getTeacherContractInfo(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $patchId   = $request->query('patch_id');
        $excludeId = $request->query('exclude_instance_id');

        if (!$teacherId || !$patchId) return response()->json(null);

        $contract = \App\Models\HR\TeacherContract::with('contractType')
            ->where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->where('is_active', true)
            ->first();

        if (!$contract) return response()->json(null);

        $sessionCount = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacherId)
              ->where('patch_id', $patchId)
              ->whereIn('status', ['Active', 'Upcoming', 'Pending_Approval'])
              ->when($excludeId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeId))
        )->where('status', '!=', 'Cancelled')->count();

        $pendingCount = \App\Models\Academic\CourseInstance::where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', ['Upcoming', 'Pending_Approval'])
            ->when($excludeId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeId))
            ->whereDoesntHave('sessions')
            ->get()
            ->sum(fn($ci) => (int) ceil((float)$ci->total_hours / (float)$ci->session_duration));

        $existingSessions = $sessionCount + $pendingCount;
        $maxSessions      = $contract->contractType?->max_sessions_allowed ?? 0;

        return response()->json([
            'contract_name'    => $contract->contractType?->name ?? '—',
            'max_sessions'     => $maxSessions,
            'current_sessions' => $existingSessions,
            'remaining'        => max(0, $maxSessions - $existingSessions),
        ]);
    }

    public function checkRoomAvailability(Request $request)
    {
        $roomId    = $request->query('room_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $pairs     = $request->query('pairs', '');
        $startTime = $request->query('start_time');
        $duration  = (float) $request->query('duration', 2);
        $excludeId = $request->query('exclude_instance_id');

        if (!$roomId || !$startDate || !$startTime) {
            return response()->json(['available' => true]);
        }

        $pairsArr   = array_filter(explode(',', $pairs));
        $dayMap     = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $singleDays = json_decode($request->query('single_days', '{}'), true) ?: [];
        $isSingle   = ($request->query('schedule_type') ?? 'double') === 'single';
        $targetDays = array_merge(...array_map(function ($p) use ($dayMap, $singleDays, $isSingle) {
            $pd = $dayMap[$p] ?? [];
            $sd = $singleDays[$p] ?? null;
            if ($isSingle && $sd !== null && $sd !== '' && in_array((int) $sd, $pd, true)) {
                return [(int) $sd];
            }
            return $pd;
        }, $pairsArr));

        [$h, $m]   = explode(':', $startTime);
        $endMins   = ((int)$h * 60 + (int)$m) + (int)($duration * 60);
        $endTime   = sprintf('%02d:%02d:00', intdiv($endMins, 60), $endMins % 60);
        $startFull = $startTime . ':00';

        $conflict = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('room_id', $roomId)->whereIn('status', ['Active','Upcoming'])
              ->when($excludeId, fn($qq) => $qq->where('course_instance_id', '!=', $excludeId))
        )
        ->whereBetween('session_date', [$startDate, $endDate])
        ->where('status', '!=', 'Cancelled')
        ->where('start_time', '<', $endTime)
        ->where('end_time',   '>', $startFull)
        ->get()
        ->first(fn($s) => in_array(\Carbon\Carbon::parse($s->session_date)->dayOfWeek, $targetDays));

        if ($conflict) {
            $course = $conflict->courseInstance?->courseTemplate?->name ?? 'another course';
            $date   = \Carbon\Carbon::parse($conflict->session_date)->format('d M Y');
            return response()->json(['available' => false, 'message' => "Room is booked on {$date} by \"{$course}\""]);
        }

        return response()->json(['available' => true]);
    }

    public function getScheduleData($id)
    {
        $instance = CourseInstance::with('teacher')->findOrFail($id);
        $pairs    = $this->schedulingService->getTeacherAvailablePairs($instance->teacher_id);
        return response()->json(['pairs' => $pairs, 'instance' => [
            'total_hours'      => $instance->total_hours,
            'session_duration' => $instance->session_duration,
            'start_date'       => $instance->start_date,
            'end_date'         => $instance->end_date,
        ]]);
    }

    public function previewSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);
        $request->validate(['day_of_week' => 'required|in:sun_wed,sat_tue,mon_thu', 'start_time' => 'required|date_format:H:i']);
        return response()->json($this->schedulingService->previewSessions($instance, $request->day_of_week, $request->start_time));
    }

    public function storeSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);
        $data     = $request->validate([
            'day_of_week'  => 'required|in:sun_wed,sat_tue,mon_thu',
            'time_slot_id' => 'required|exists:time_slot,time_slot_id',
            'start_time'   => 'required|date_format:H:i',
        ]);
        $slot = TimeSlot::findOrFail($data['time_slot_id']);
        $this->schedulingService->validateSchedule(['start_time' => $data['start_time'], 'session_duration' => $instance->session_duration, 'time_slot' => $slot]);
        \App\Models\Academic\InstanceSchedule::where('course_instance_id', $instance->course_instance_id)->delete();
        \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)->delete();
        $schedule   = $this->schedulingService->storeSchedule($instance->course_instance_id, ['day_of_week' => $data['day_of_week'], 'time_slot_id' => $data['time_slot_id'], 'start_time' => $data['start_time']]);
        $sessionNum = 1;
        $count      = $this->schedulingService->generateSessions($instance, $schedule, $sessionNum);
        $instance->update(['status' => 'Upcoming']);
        return back()->with('success', "Schedule saved — {$count} sessions generated.");
    }

    public function postponeEnrollment(Request $request, $enrollmentId)
    {
        $enrollment = \App\Models\Enrollment\Enrollment::findOrFail($enrollmentId);
        $request->validate(['start_date' => 'required|date', 'expected_return_date' => 'required|date|after:start_date']);
        $scEmployeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->first()?->employee_id;
        \App\Models\Enrollment\Postponement::create([
            'enrollment_id'        => $enrollment->enrollment_id,
            'start_date'           => $request->start_date,
            'expected_return_date' => $request->expected_return_date,
            'status'               => 'Active',
            'reason'               => $request->reason,
            'created_by_cs_id'     => $scEmployeeId,
        ]);
        $enrollment->update(['status' => 'Postponed']);
        return back()->with('success', 'Student postponed successfully.');
    }
}