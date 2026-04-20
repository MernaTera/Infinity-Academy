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
    public function index()
    {
        $patches = Patch::with('branch')
            ->withCount('courseInstances')
            ->orderByDesc('start_date')
            ->get();

        $timeSlots = TimeSlot::where('is_active', true)->get();
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

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100|unique:patch,name',
            'branch_id'  => 'required|exists:branch,branch_id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        // No overlapping patches in same branch
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

        $adminId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        Patch::create([
            'name'                => $request->name,
            'branch_id'           => $request->branch_id,
            'start_date'          => $request->start_date,
            'end_date'            => $request->end_date,
            'status'              => 'Upcoming',
            'is_locked'           => false,
            'is_placeholder'      => false,
            'created_by_admin_id' => $adminId,
        ]);

        return back()->with('success', 'Patch created successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $patch = Patch::findOrFail($id);

        $request->validate([
            'action' => 'required|in:activate,close,lock,unlock',
        ]);

        match($request->action) {
            'activate' => $patch->update(['status' => 'Active']),
            'close'    => $patch->update(['status' => 'Closed', 'is_locked' => true]),
            'lock'     => $patch->update(['is_locked' => true]),
            'unlock'   => $patch->update(['is_locked' => false]),
        };

        return back()->with('success', 'Patch updated successfully.');
    }

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
            'created_by_admin_id' => Employee::where('user_id', auth()->id())->first()?->employee_id,
        ]);

        return back()->with('success', 'Time slot added.');
    }

    public function toggleTimeSlot($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->update(['is_active' => !$slot->is_active]);
        return back()->with('success', 'Time slot updated.');
    }

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
            'created_by_admin_id' => Employee::where('user_id', auth()->id())->first()?->employee_id,
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