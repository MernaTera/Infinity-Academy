<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\Room;
use App\Models\Core\Branch;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $adminBranch   = $adminEmployee?->branch; 

        $rooms = Room::with(['branch', 'createdByAdmin'])
            ->withCount('courseInstances')
            ->where('branch_id', $adminEmployee->branch_id)
            ->orderBy('name')
            ->get();

        $branches = Branch::orderBy('name')->get();

        $stats = [
            'total'   => $rooms->count(),
            'active'  => $rooms->where('is_active', true)->count(),
            'offline' => $rooms->where('room_type', 'Offline')->count(),
            'online'  => $rooms->where('room_type', 'Online')->count(),
        ];

        return view('admin.rooms.index', compact('rooms', 'branches', 'stats', 'adminBranch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:room,name',
            'capacity'  => 'required|integer|min:1',
            'room_type' => 'required|in:Offline,Online',
        ]);

        $adminEmployee = Employee::where('user_id', auth()->id())->first();

        Room::create([
            'name'                => $request->name,
            'branch_id'           => $adminEmployee->branch_id,
            'capacity'            => $request->capacity,
            'room_type'           => $request->room_type,
            'is_active'           => true,
            'created_by_admin_id' => $adminEmployee->employee_id,
        ]);

        return back()->with('success', 'Room created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:room,name,'.$id.',room_id',
            'capacity'  => 'required|integer|min:1',
            'room_type' => 'required|in:Offline,Online',
        ]);

        $adminEmployee = Employee::where('user_id', auth()->id())->first();

        Room::findOrFail($id)->update([
            'name'      => $request->name,
            'branch_id' => $adminEmployee->branch_id, 
            'capacity'  => $request->capacity,
            'room_type' => $request->room_type,
        ]);

        return back()->with('success', 'Room updated successfully.');
    }

    public function toggle($id)
    {
        $room = Room::findOrFail($id);
        $room->update(['is_active' => !$room->is_active]);
        return back()->with('success', 'Room status updated.');
    }

    public function destroy($id)
    {
        $room = Room::withCount('courseInstances')->findOrFail($id);

        if ($room->course_instances_count > 0) {
            return back()->with('error', 'Cannot delete room — it has active course instances.');
        }

        $room->delete();
        return back()->with('success', 'Room deleted successfully.');
    }
}