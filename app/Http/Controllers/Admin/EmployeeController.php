<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use App\Models\Core\Branch;
use App\Models\HR\Teacher;
use App\Models\Academic\EnglishLevel;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\CsTarget;
use App\Models\Academic\Patch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;

class EmployeeController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Employee List
    |------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $roleFilter   = $request->query('role');
        $statusFilter = $request->query('status');

        $employees = Employee::with(['user.role', 'teacher'])
            ->when($statusFilter, fn($q) =>
                $q->where('status', $statusFilter))
            ->when($roleFilter, fn($q) =>
                $q->whereHas('user.role', fn($q2) =>
                    $q2->where('role_name', $roleFilter)))
            ->latest()
            ->paginate(20);

        $roles = Role::where('role_name', '!=', 'Admin')->get();

        $stats = [
            'total'    => Employee::count(),
            'active'   => Employee::where('status', 'Active')->count(),
            'inactive' => Employee::where('status', 'Inactive')->count(),
            'cs'       => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))->count(),
            'teachers' => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Teacher'))->count(),
            'sc'       => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Student Care'))->count(),
            'admin'    => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Admin'))->count(), 

        ];

        return view('admin.employees.index', compact('employees', 'roles', 'stats', 'roleFilter', 'statusFilter'));
    }

    /*
    |------------------------------------------------------------------
    | Create Form
    |------------------------------------------------------------------
    */
    public function create()
    {
        $roles         = Role::where('role_name', '!=', 'Student')->get();
        $branches      = Branch::all();
        $englishLevels = EnglishLevel::all();
        $patches       = Patch::whereIn('status', ['Active', 'Upcoming'])->get();
        $contractTypes = \App\Models\HR\ContractType::where('is_active', true)->get();
        $timeSlots     = \App\Models\Academic\TimeSlot::where('is_active', true)->orderBy('start_time')->get(); // ✅

        return view('admin.employees.create', compact(
            'roles', 'branches', 'englishLevels', 'patches', 'contractTypes', 'timeSlots'
        ));
    }

    /*
    |------------------------------------------------------------------
    | Store
    |------------------------------------------------------------------
    */
public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:8',
            'role_id'          => 'required|exists:role,role_id',
            'branch_id'        => 'required|exists:branch,branch_id',
            'salary'           => 'nullable|numeric|min:0',
            'english_level_id' => 'nullable|exists:english_level,english_level_id',
            'contract_type_id' => 'nullable|exists:contract_type,contract_type_id',
            'max_sessions'     => 'nullable|integer|min:1',
            'patch_id'         => 'nullable|exists:patch,patch_id',
            'availability'     => 'nullable|array',
            'availability.*'   => 'string',
            'target_amount'    => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role_id'   => $request->role_id,
                'is_active' => true,
            ]);

            $employee = Employee::create([
                'full_name' => $request->name,
                'user_id'   => $user->id,
                'branch_id' => $request->branch_id,
                'salary'    => $request->salary,
                'status'    => 'Active',
                'hired_at'  => now(),
            ]);

            $adminId = Employee::where('user_id', auth()->id())->value('employee_id');
            $role    = Role::find($request->role_id);

            // ── Teacher ───────────────────────────────────────────────
            if ($role?->role_name === 'Teacher' && $request->english_level_id) {

                $teacher = Teacher::create([
                    'employee_id'      => $employee->employee_id,
                    'english_level_id' => $request->english_level_id,
                    'is_active'        => true,
                ]);

                // ✅ TeacherContract
                if ($request->contract_type_id && $request->patch_id) {
                    \App\Models\HR\TeacherContract::create([
                        'teacher_id'          => $teacher->teacher_id,
                        'patch_id'            => $request->patch_id,
                        'contract_type_id'    => $request->contract_type_id,
                        'is_active'           => true,
                        'created_by_admin_id' => $adminId,
                    ]);
                }

                // ✅ TeacherAvailability — INSIDE transaction, AFTER teacher created
                if ($request->filled('availability')) {
                    foreach ($request->availability as $item) {
                        [$slotId, $day] = explode(':', $item);
                        \App\Models\HR\TeacherAvailability::create([
                            'teacher_id'   => $teacher->teacher_id,
                            'time_slot_id' => (int) $slotId,
                            'day_of_week'  => $day,
                        ]);
                    }
                }
            }

            // ── CS Target ─────────────────────────────────────────────
            if ($role?->role_name === 'Customer Service' && $request->target_amount && $request->patch_id) {
                CsTarget::create([
                    'employee_id'         => $employee->employee_id,
                    'patch_id'            => $request->patch_id,
                    'target_amount'       => $request->target_amount,
                    'is_locked'           => false,
                    'created_by_admin_id' => $adminId,
                ]);
            }
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /*
    |------------------------------------------------------------------
    | Show Profile
    |------------------------------------------------------------------
    */
    public function show($id)
    {
        $employee = Employee::with([
            'user.role',
            'branch',
            'teacher.englishLevel',
            'teacher.contractTypes.patch',
            'teacher.availability.timeSlot',
        ])->findOrFail($id);

        $currentPatch = Patch::active()->latest('start_date')->first();
        $roleName     = $employee->user?->role?->role_name;
        
        $csData = null;
        if ($roleName === 'Customer Service') {
            $currentMonth = now()->format('Y-m');

            $target = CsTarget::where('employee_id', $employee->employee_id)
                ->where('month', $currentMonth)
                ->first();

            $achieved = RevenueSplit::where('employee_id', $employee->employee_id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount_allocated');

            $leads = \App\Models\Leads\Lead::where('owner_cs_id', $employee->employee_id);

            $csData = [
                'target'        => $target?->target_amount ?? 0,
                'target_id'     => $target?->target_id,
                'current_month' => $currentMonth,
                'achieved'      => $achieved,
                'registrations' => \App\Models\Enrollment\Enrollment::where('created_by_cs_id', $employee->employee_id)
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'total_leads'   => (clone $leads)->count(),
                'active_leads'  => (clone $leads)->whereIn('status', ['Waiting','Call_Again'])->count(),
            ];
        }

        $teacherData = null;
        if ($roleName === 'Teacher' && $employee->teacher) {
            $teacherData = [
                'active_courses'  => \App\Models\Academic\CourseInstance::where('teacher_id', $employee->teacher->teacher_id)
                    ->where('status', 'Active')->count(),
                'upcoming_courses' => \App\Models\Academic\CourseInstance::where('teacher_id', $employee->teacher->teacher_id)
                    ->where('status', 'Upcoming')->count(),
                'total_students'  => \App\Models\Enrollment\Enrollment::whereHas('courseInstance', fn($q) =>
                    $q->where('teacher_id', $employee->teacher->teacher_id)
                    ->whereIn('status', ['Active','Upcoming'])
                )->whereIn('status', ['Active','Restricted'])->count(),
                'contract' => $employee->teacher->contractTypes
                    ->where('is_active', true)
                    ->sortByDesc('created_at')
                    ->first(),
                'assigned_courses' => \App\Models\Academic\CourseInstance::with([
                    'courseTemplate', 'patch', 'instanceSchedules.timeSlot',
                ])
                ->where('teacher_id', $employee->teacher->teacher_id)
                ->whereIn('status', ['Active', 'Upcoming', 'Pending_Approval'])
                ->orderByRaw("FIELD(status, 'Active', 'Upcoming', 'Pending_Approval')")
                ->get(),
            ];
        }

        return view('admin.employees.show', compact(
            'employee', 'roleName', 'csData', 'teacherData', 'currentPatch'
        ));
    }
    
    public function assignContract(Request $request, $id)
    {
        $request->validate([
            'contract_type_id' => 'required|exists:contract_type,contract_type_id',
            'patch_id'         => 'required|exists:patch,patch_id',
        ]);

        $employee = Employee::findOrFail($id);
        $teacher  = \App\Models\HR\Teacher::where('employee_id', $employee->employee_id)->firstOrFail();
        $adminId  = Employee::where('user_id', auth()->id())->value('employee_id');

        \App\Models\HR\TeacherContract::updateOrCreate(
            [
                'teacher_id' => $teacher->teacher_id,
                'patch_id'   => $request->patch_id,
            ],
            [
                'contract_type_id'    => $request->contract_type_id,
                'is_active'           => true,
                'created_by_admin_id' => $adminId,
            ]
        );

        return back()->with('success', 'Contract assigned successfully.');
    }
    /*
    |------------------------------------------------------------------
    | Edit
    |------------------------------------------------------------------
    */
    public function edit($id)
    {
        $employee      = Employee::with(['user.role', 'teacher'])->findOrFail($id);
        $branches      = Branch::all();
        $englishLevels = EnglishLevel::all();
        $patches       = Patch::whereIn('status', ['Active','Upcoming'])->get();

        AuditService::log('employee', $employee->employee_id, 'Edit Employee', 'Accessed edit form for employee');

        return view('admin.employees.edit', compact('employee', 'branches', 'englishLevels', 'patches'));
    }

    /*
    |------------------------------------------------------------------
    | Update
    |------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        if($request->salary != $employee->salary){
            AuditService::updated('employee', $employee->employee_id, 'salary', $employee->salary, $request->salary);
        }
        if($request->branch_id != $employee->branch_id){
            AuditService::updated('employee', $employee->employee_id, 'branch_id', $employee->branch_id, $request->branch_id);
        }
        if($request->status != $employee->status){
            AuditService::updated('employee', $employee->employee_id, 'status', $employee->status, $request->status);
        }
        $request->validate([
            'salary'    => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branch,branch_id',
            'status'    => 'required|in:Active,Inactive',
        ]);

        $employee->update([
            'salary'    => $request->salary,
            'branch_id' => $request->branch_id,
            'status'    => $request->status,
        ]);

        if ($request->filled('new_password')) {
            $request->validate(['new_password' => 'min:8']);
            $employee->user->update(['password' => Hash::make($request->new_password)]);
        }

        return back()->with('success', 'Employee updated successfully.');
    }

    /*
    |------------------------------------------------------------------
    | Toggle Active / Inactive
    |------------------------------------------------------------------
    */
    public function toggle($id)
    {
        $employee  = Employee::with('user')->findOrFail($id);
        $newStatus = $employee->status === 'Active' ? 'Inactive' : 'Active';
        $user      = $employee->user; 

        AuditService::updated('employee', $employee->employee_id, 'status', $employee->status, $newStatus);

        $employee->update(['status' => $newStatus]);

        if ($user) {
            $user->update(['is_active' => $newStatus === 'Active']);

            if ($newStatus === 'Inactive') {
                \DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }
        }

        return back()->with('success', "Employee {$newStatus} successfully.");
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'salary'        => 'nullable|numeric|min:0', 
            'target_amount' => 'nullable|numeric|min:0',
            'target_month'  => 'required|string',
        ]);

        $employee = Employee::with('user')->findOrFail($id);

        $employee->update([
            'full_name' => $request->full_name,
            'salary'    => $request->salary,        
        ]);
        $employee->user->update(['name' => $request->full_name]);

        if ($request->filled('new_password')) {
            \DB::table('users')
                ->where('id', $employee->user_id)
                ->update(['password' => \Hash::make($request->new_password)]);
        }

        if ($request->filled('target_amount')) {
            $adminEmployee = Employee::where('user_id', auth()->id())->first();
            CsTarget::updateOrCreate(
                [
                    'employee_id' => $employee->employee_id,
                    'month'       => $request->target_month,
                ],
                [
                    'target_amount'       => $request->target_amount,
                    'created_by_admin_id' => $adminEmployee->employee_id,
                ]
            );
        }

        return back()->with('success', 'Employee updated successfully.');
    }
}