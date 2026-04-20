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

        // Stats
        $stats = [
            'total'    => Employee::count(),
            'active'   => Employee::where('status', 'Active')->count(),
            'inactive' => Employee::where('status', 'Inactive')->count(),
            'cs'       => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Customer Service'))->count(),
            'teachers' => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Teacher'))->count(),
            'sc'       => Employee::whereHas('user.role', fn($q) => $q->where('role_name', 'Student Care'))->count(),
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
        $roles         = Role::all();
        $branches      = Branch::all();
        $englishLevels = EnglishLevel::all();
        $patches       = Patch::whereIn('status', ['Active', 'Upcoming'])->get();

        return view('admin.employees.create', compact('roles', 'branches', 'englishLevels', 'patches'));
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
            // Teacher specific
            'english_level_id' => 'nullable|exists:english_level,english_level_id',
            'contract_type'    => 'nullable|in:PT,FT,OT',
            'max_sessions'     => 'nullable|integer|min:1',
            'patch_id'         => 'nullable|exists:patch,patch_id',
        ]);

        DB::transaction(function () use ($request) {

            // Create User
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role_id'   => $request->role_id,
                'is_active' => true,
            ]);

            // Create Employee
            $employee = Employee::create([
                'full_name' => $request->name,
                'user_id'   => $user->id,
                'branch_id' => $request->branch_id,
                'salary'    => $request->salary,
                'status'    => 'Active',
                'hired_at'  => now(),
            ]);

            // If Teacher — create Teacher record + Contract
            $role = Role::find($request->role_id);
            if ($role?->role_name === 'Teacher' && $request->english_level_id) {

                $teacher = Teacher::create([
                    'employee_id'      => $employee->employee_id,
                    'english_level_id' => $request->english_level_id,
                    'is_active'        => true,
                ]);

                if ($request->contract_type && $request->patch_id) {
                    \App\Models\Academic\ContractType::create([
                        'teacher_id'           => $teacher->teacher_id,
                        'patch_id'             => $request->patch_id,
                        'contract_type'        => $request->contract_type,
                        'max_sessions_allowed' => $request->max_sessions ?? 9,
                        'is_active'            => true,
                        'created_by_admin_id'  => auth()->user()->employees->first()->employee_id,
                    ]);
                }
            }

            // If CS — create Target
            if ($role?->role_name === 'Customer Service' && $request->target_amount && $request->patch_id) {
                CsTarget::create([
                    'employee_id'        => $employee->employee_id,
                    'patch_id'           => $request->patch_id,
                    'target_amount'      => $request->target_amount,
                    'is_locked'          => false,
                    'created_by_admin_id'=> auth()->user()->employees->first()->employee_id,
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

        // CS-specific data
        $csData = null;
        if ($roleName === 'Customer Service') {
            $achieved = RevenueSplit::where('employee_id', $employee->employee_id)
                ->where('patch_id', $currentPatch?->patch_id)
                ->sum('amount_allocated');

            $target = CsTarget::where('employee_id', $employee->employee_id)
                ->where('patch_id', $currentPatch?->patch_id)
                ->first();

            $leads = \App\Models\Leads\Lead::where('owner_cs_id', $employee->employee_id);

            $csData = [
                'target'        => $target?->target_amount ?? 0,
                'achieved'      => $achieved,
                'registrations' => \App\Models\Enrollment\Enrollment::where('created_by_cs_id', $employee->employee_id)
                    ->where('patch_id', $currentPatch?->patch_id)->count(),
                'total_leads'   => (clone $leads)->count(),
                'active_leads'  => (clone $leads)->whereIn('status', ['Waiting','Call_Again'])->count(),
            ];
        }

        // Teacher-specific data
        $teacherData = null;
        if ($roleName === 'Teacher' && $employee->teacher) {
            $teacherData = [
                'active_courses'  => \App\Models\Academic\CourseInstance::where('teacher_id', $employee->teacher->teacher_id)
                    ->where('status', 'Active')->count(),
                'total_students'  => \App\Models\Enrollment\Enrollment::whereHas('courseInstance', fn($q) =>
                    $q->where('teacher_id', $employee->teacher->teacher_id))->count(),
                'contract'        => $employee->teacher->contractTypes
                    ->where('patch_id', $currentPatch?->patch_id)->first(),
            ];
        }

        return view('admin.employees.show', compact(
            'employee', 'roleName', 'csData', 'teacherData', 'currentPatch'
        ));
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

        // Reset password if provided
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
        $employee    = Employee::with('user')->findOrFail($id);
        $newStatus   = $employee->status === 'Active' ? 'Inactive' : 'Active';

        $employee->update(['status' => $newStatus]);
        $employee->user->update(['is_active' => $newStatus === 'Active']);

        return back()->with('success', "Employee {$newStatus} successfully.");
    }
}