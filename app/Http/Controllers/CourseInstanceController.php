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

    // ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','enrollments','sessions','instanceSchedules',
        ])->latest()->paginate(10);

        $templates = CourseTemplate::all();
        $teachers  = Teacher::with('employee')->get();
        $patches   = Patch::whereIn('status', ['Active', 'Upcoming'])->get();
        $branches  = Branch::all();
        $rooms     = Room::all();

        return view('student-care.course-instances.index', compact(
            'instances','templates','teachers','patches','branches','rooms'
        ));
    }

    // ─────────────────────────────────────────────────────────────────
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

    // ─────────────────────────────────────────────────────────────────
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
            'start_times'        => 'required|array',
            'start_times.*'      => 'required|date_format:H:i',
            'time_slot_ids'      => 'nullable|array',
            'time_slot_ids.*'    => 'nullable|exists:time_slot,time_slot_id',
        ]);

        $dayMap = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];

        // ── 1. Patch date validation ──────────────────────────────────
        $patch = Patch::findOrFail($data['patch_id']);
        if ($data['start_date'] < $patch->start_date || $data['start_date'] > $patch->end_date) {
            return back()->withInput()->withErrors([
                'start_date' => "Start date must be within the selected patch ({$patch->start_date} → {$patch->end_date})."
            ]);
        }
        if ($data['end_date'] > $patch->end_date) {
            return back()->withInput()->withErrors([
                'end_date' => "End date ({$data['end_date']}) exceeds the patch end date ({$patch->end_date})."
            ]);
        }

        // ── 2. Auto-adjust start_date to first valid session day ──────
        $allTargetDays = array_merge(...array_map(fn($p) => $dayMap[$p] ?? [], $data['day_of_week']));
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

        // ── 3. Room conflict check ────────────────────────────────────
        if (!empty($data['room_id'])) {
            foreach ($data['day_of_week'] as $pair) {
                $startTime = $data['start_times'][$pair] ?? null;
                if (!$startTime) continue;

                $dur        = (float) $data['session_duration'];
                [$h, $m]    = explode(':', $startTime);
                $endMins    = ((int)$h * 60 + (int)$m) + (int)($dur * 60);
                $endTime    = sprintf('%02d:%02d:00', intdiv($endMins, 60), $endMins % 60);
                $startFull  = $startTime . ':00';
                $targetDays = $dayMap[$pair] ?? [];

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

        // ── 4. Main transaction ───────────────────────────────────────
        $employeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $employeeId) {

                // Contract limit check
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

                // Create instance
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

                // ── Pending Approval flow ─────────────────────────────
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

                    $adminEmployees = \App\Models\HR\Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Admin'))->get();
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

                    // Save schedules (no sessions yet)
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

                // ── Normal flow ───────────────────────────────────────
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
                    $data['time_slot_ids'] ?? null
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

    // ─────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $instance = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher.employee',
            'branch','patch','room',
            'enrollments.student.phones',
            'enrollments.installmentSchedules',
            'sessions',
            'instanceSchedules.timeSlot',
            'enrollments.activePostponement',
        ])->findOrFail($id);

        return view('student-care.course-instances.show', compact('instance'));
    }

    // ─────────────────────────────────────────────────────────────────
    public function getTeachersByCourse($courseId)
    {
        $course = CourseTemplate::find($courseId);
        if (!$course || !$course->english_level_id) return response()->json([]);
        return response()->json(
            Teacher::where('english_level_id', '>=', $course->english_level_id)->with('employee')->get()
        );
    }

    public function getTeachersByLevel($englishLevelId)
    {
        return response()->json(
            Teacher::where('english_level_id', '>=', $englishLevelId)
                ->where('is_active', true)
                ->with(['employee', 'englishLevel'])
                ->get()
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // Returns teacher's available pairs + for each pair: existing courses
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

            // Get existing courses for this pair in the patch
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

    // ─────────────────────────────────────────────────────────────────
    public function getTimeSlotsForPair(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $pair      = $request->query('pair');

        if (!$teacherId || !$pair) return response()->json([]);

        $availability = \App\Models\HR\TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->where('day_of_week', $pair)
            ->first();

        if (!$availability || !$availability->timeSlot) return response()->json([]);

        $slot       = $availability->timeSlot;
        $slotStart  = \Carbon\Carbon::createFromTimeString($slot->start_time);
        $slotEnd    = \Carbon\Carbon::createFromTimeString($slot->end_time);
        $breakSlots = BreakSlot::where('is_active', true)->get();
        $slots      = [];
        $current    = $slotStart->copy();

        while ($current->lt($slotEnd)) {
            $timeStr = $current->format('H:i');
            $isBreak = false;
            foreach ($breakSlots as $b) {
                $bStart = \Carbon\Carbon::createFromTimeString($b->start_time);
                $bEnd   = \Carbon\Carbon::createFromTimeString($b->end_time);
                if ($current->gte($bStart) && $current->lt($bEnd)) { $isBreak = true; break; }
            }
            $slots[] = [
                'start'    => $timeStr,
                'end'      => $slotEnd->format('H:i'),
                'slot_id'  => $slot->time_slot_id,
                'is_break' => $isBreak,
            ];
            $current->addMinutes(30);
        }

        return response()->json($slots);
    }

    // ─────────────────────────────────────────────────────────────────

    public function getTeacherFreeDates(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $patchId   = $request->query('patch_id');
        if (!$teacherId || !$patchId) return response()->json([]);

        $patch = Patch::find($patchId);
        if (!$patch) return response()->json([]);

        $dayMap   = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $availability = \App\Models\HR\TeacherAvailability::where('teacher_id', $teacherId)->get();

        // map dayOfWeek number → pair name
        $pairByDay = [];
        foreach ($availability as $av) {
            foreach ($dayMap[$av->day_of_week] ?? [] as $dayNum) {
                $pairByDay[$dayNum] = $av->day_of_week;
            }
        }
        if (empty($pairByDay)) return response()->json([]);

        $occupiedDates = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', ['Active','Upcoming','Pending_Approval'])
        )->where('status','!=','Cancelled')
        ->pluck('session_date')
        ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
        ->unique()->toArray();

        $result  = [];
        $current = \Carbon\Carbon::parse($patch->start_date);
        $end     = \Carbon\Carbon::parse($patch->end_date);

        while ($current->lte($end)) {
            $dow = $current->dayOfWeek;
            if (isset($pairByDay[$dow])) {
                $result[] = [
                    'date'     => $current->toDateString(),
                    'day'      => $current->format('D'),
                    'display'  => $current->format('d M'),
                    'pair'     => $pairByDay[$dow],
                    'occupied' => in_array($current->toDateString(), $occupiedDates),
                ];
            }
            $current->addDay();
        }

        return response()->json($result);
    }

    // ─────────────────────────────────────────────────────────────────
    public function getOccupiedSlots(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date', '2099-12-31');

        if (!$teacherId || !$startDate) return response()->json([]);

        $occupied = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacherId)
        )
        ->whereBetween('session_date', [$startDate, $endDate])
        ->where('status', '!=', 'Cancelled')
        ->pluck('start_time')
        ->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i'))
        ->unique()->values()->toArray();

        return response()->json($occupied);
    }

    // ─────────────────────────────────────────────────────────────────
    public function checkConflicts(Request $request)
    {
        $teacherId  = $request->teacher_id;
        $startDate  = $request->start_date;
        $endDate    = $request->end_date ?? '2099-12-31';
        $pairs      = (array) $request->day_of_week;
        $startTime  = $request->start_time;
        $sessionDur = (float) $request->session_duration;

        if (!$teacherId || !$startDate || !$pairs || !$startTime) {
            return response()->json(['conflicts' => []]);
        }

        $dayMap        = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $allTargetDays = array_merge(...array_map(fn($p) => $dayMap[$p] ?? [], $pairs));
        $newStart      = \Carbon\Carbon::createFromTimeString($startTime);
        $newEnd        = $newStart->copy()->addHours($sessionDur);

        $existingSessions = \App\Models\Academic\CourseSession::with('courseInstance.courseTemplate')
            ->whereHas('courseInstance', fn($q) => $q->where('teacher_id', $teacherId))
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

    // ─────────────────────────────────────────────────────────────────
    public function getTeacherContractInfo(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $patchId   = $request->query('patch_id');

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
        )->where('status', '!=', 'Cancelled')->count();

        $pendingCount = \App\Models\Academic\CourseInstance::where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', ['Upcoming', 'Pending_Approval'])
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

    // ─────────────────────────────────────────────────────────────────
    public function checkRoomAvailability(Request $request)
    {
        $roomId    = $request->query('room_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $pairs     = $request->query('pairs', '');
        $startTime = $request->query('start_time');
        $duration  = (float) $request->query('duration', 2);

        if (!$roomId || !$startDate || !$startTime) {
            return response()->json(['available' => true]);
        }

        $pairsArr   = array_filter(explode(',', $pairs));
        $dayMap     = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $targetDays = array_merge(...array_map(fn($p) => $dayMap[$p] ?? [], $pairsArr));

        [$h, $m]   = explode(':', $startTime);
        $endMins   = ((int)$h * 60 + (int)$m) + (int)($duration * 60);
        $endTime   = sprintf('%02d:%02d:00', intdiv($endMins, 60), $endMins % 60);
        $startFull = $startTime . ':00';

        $conflict = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('room_id', $roomId)->whereIn('status', ['Active','Upcoming'])
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

    // ─────────────────────────────────────────────────────────────────
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