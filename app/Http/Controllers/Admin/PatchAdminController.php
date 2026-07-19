<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\Patch;
use App\Models\Core\Branch;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\BreakSlot;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class PatchAdminController extends Controller
{
    private function adminEmployeeId(): ?int
    {
        return Employee::where('user_id', auth()->id())->value('employee_id');
    }

    public function index()
    {
        $patches = Patch::with('branch')
            ->withCount([
                'courseInstances',
                'courseInstances as active_courses_count' => function ($q) {
                    $q->where('status', 'Active');
                }
            ])
            ->orderByDesc('start_date')
            ->get();
        $timeSlots  = TimeSlot::where('is_active', true)->get();
        $breakSlots = BreakSlot::where('is_active', true)->get();
        $branches   = Branch::all();
        
        $stats = [
            'total'    => $patches->count(),
            'active'   => $patches->where('status', 'Active')->count(),
            'upcoming' => $patches->where('status', 'Upcoming')->count(),
            'closed'   => $patches->where('status', 'Closed')->count(),
        ];

        return view('admin.patches.index', compact(
            'patches', 'stats', 'timeSlots', 'breakSlots', 'branches'
        ));
    }

    // ── Create ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100|unique:patch,name',
            'branch_id'  => 'required|exists:branch,branch_id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $overlap = Patch::where('branch_id', $request->branch_id)
            ->where('status', '!=', 'Closed')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })->exists();

        if ($overlap) {
            return back()->with('error', 'Overlapping patch exists for this branch in the selected period.');
        }

        Patch::create([
            'name'                => $request->name,
            'branch_id'           => $request->branch_id,
            'start_date'          => $request->start_date,
            'end_date'            => $request->end_date,
            'status'              => 'Upcoming',
            'is_locked'           => false,
            'is_placeholder'      => false,
            'created_by_admin_id' => $this->adminEmployeeId(),
        ]);

        return back()->with('success', 'Patch created successfully.');
    }

    // ── Edit ──────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $patch = Patch::findOrFail($id);

        if ($this->hasActiveCourses($id)) {
            return back()->with('error', 'Cannot edit patch — there are active courses running in this patch.');
        }

        if ($patch->status === 'Closed') {
            return back()->with('error', 'Cannot edit a closed patch.');
        }

        $request->validate([
            'name'       => 'required|string|max:100|unique:patch,name,' . $id . ',patch_id',
            'branch_id'  => 'required|exists:branch,branch_id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        // Check overlap excluding current patch
        $overlap = Patch::where('branch_id', $request->branch_id)
            ->where('patch_id', '!=', $id)
            ->where('status', '!=', 'Closed')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })->exists();

        if ($overlap) {
            return back()->with('error', 'Overlapping patch exists for this branch.');
        }

        $patch->update([
            'name'       => $request->name,
            'branch_id'  => $request->branch_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return back()->with('success', 'Patch updated successfully.');
    }

    // ── Delete ────────────────────────────────────────────────────
    public function destroy($id)
    {
        $patch = Patch::findOrFail($id);

        if ($patch->status === 'Active') {
            return back()->with('error', 'Cannot delete an active patch.');
        }

        if ($this->hasActiveCourses($id)) {
            return back()->with('error', 'Cannot delete patch — there are active courses running in this patch.');
        }

        $blockers = [];

        if (\App\Models\Academic\CourseInstance::where('patch_id', $id)->exists()) {
            $blockers[] = 'course instances';
        }

        if (\App\Models\Finance\FinancialTransaction::where('patch_id', $id)->exists()) {
            $blockers[] = 'financial transactions';
        }

        if (\DB::table('waiting_list')->where('requested_patch_id', $id)->exists()) {
            $blockers[] = 'waiting list entries';
        }

        if (class_exists(\App\Models\Enrollment\CsTarget::class) 
            && \App\Models\Enrollment\CsTarget::where('patch_id', $id)->exists()) {
            $blockers[] = 'CS targets';
        }

        if (class_exists(\App\Models\HR\TeacherContract::class)
            && \App\Models\HR\TeacherContract::where('patch_id', $id)->exists()) {
            $blockers[] = 'teacher contracts';
        }

        if (\App\Models\Finance\RevenueSplit::where('patch_id', $id)->exists()) {
            $blockers[] = 'revenue splits';
        }

        if (!empty($blockers)) {
            return back()->with('error', 
                'Cannot delete patch — it has related records: ' . implode(', ', $blockers) . '. Close it instead to preserve history.'
            );
        }

        $patch->delete();
        return back()->with('success', 'Patch deleted successfully.');
    }

    // ── Status ────────────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $patch = Patch::findOrFail($id);
        $request->validate(['action' => 'required|in:activate,close,lock,unlock']);

        if (in_array($request->action, ['close', 'lock', 'activate']) && $this->hasActiveCourses($id)) {
            return back()->with('error', "Cannot {$request->action} patch — there are active courses running in this patch.");
        }

        match($request->action) {
            'activate' => $patch->update(['status' => 'Active']),
            'close'    => $patch->update(['status' => 'Closed', 'is_locked' => true]),
            'lock'     => $patch->update(['is_locked' => true]),
            'unlock'   => $patch->update(['is_locked' => false]),
        };

        return back()->with('success', 'Patch updated successfully.');
    }

    private function hasActiveCourses($patchId): bool
    {
        return \App\Models\Academic\CourseInstance::where('patch_id', $patchId)
            ->where('status', 'Active')
            ->exists();
    }
    // ── Time Slots ────────────────────────────────────────────────
    public function storeTimeSlot(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
            'slot_type'  => 'required|in:Morning,Midday,Night',
        ]);

        TimeSlot::create([
            'name'                => $request->name,
            'start_time'          => $request->start_time,
            'end_time'            => $request->end_time,
            'slot_type'           => $request->slot_type,
            'is_active'           => true,
            'created_by_admin_id' => $this->adminEmployeeId(),
        ]);

        return back()->with('success', 'Time slot added.');
    }

    public function toggleTimeSlot($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->update(['is_active' => !$slot->is_active]);
        return back()->with('success', 'Time slot updated.');
    }

    public function slotsIndex()
    {
        $timeSlots  = TimeSlot::orderBy('start_time')->get();
        $breakSlots = BreakSlot::orderBy('start_time')->get();

        return view('admin.slots.index', compact('timeSlots', 'breakSlots'));
    }

    public function updateTimeSlot(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
            'slot_type'  => 'required|in:Morning,Midday,Night',
        ]);

        $slot = TimeSlot::findOrFail($id);
        $slot->update([
            'name'       => $request->name,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'slot_type'  => $request->slot_type,
        ]);

        return back()->with('success', 'Time slot updated.');
    }

    public function updateBreakSlot(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $break = BreakSlot::findOrFail($id);
        $break->update([
            'name'       => $request->name,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
        ]);

        return back()->with('success', 'Break slot updated.');
    }
    // ── Break Slots ───────────────────────────────────────────────
    public function storeBreakSlot(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        BreakSlot::create([
            'name'                => $request->name,
            'start_time'          => $request->start_time,
            'end_time'            => $request->end_time,
            'is_active'           => true,
            'created_by_admin_id' => $this->adminEmployeeId(),
        ]);

        return back()->with('success', 'Break slot added.');
    }

    public function toggleBreakSlot($id)
    {
        $break = BreakSlot::findOrFail($id);
        $break->update(['is_active' => !$break->is_active]);
        return back()->with('success', 'Break slot updated.');
    }
}