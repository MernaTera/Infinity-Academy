@extends('admin.layouts.app')
@section('title', $employee->full_name)

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.emp-show{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#1B4FA8;color:#1B4FA8;text-decoration:none}

.profile-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:20px;position:relative}
.profile-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.profile-header{padding:24px 28px;display:flex;align-items:center;gap:20px;border-bottom:1px solid rgba(27,79,168,0.06)}
.avatar-lg{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;flex-shrink:0}
.profile-body{padding:22px 28px}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:18px}
.info-item .label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:4px}
.info-item .value{font-size:13px;color:#1A2A4A;font-weight:500}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(245,145,30,0.15);margin-top:4px}

.stat-row{display:grid;gap:12px;margin-bottom:20px}
.stat-row-5{grid-template-columns:repeat(5,1fr)}
.stat-row-3{grid-template-columns:repeat(3,1fr)}
.stat-mini{background:#F8F6F2;border:1px solid rgba(27,79,168,0.08);border-radius:6px;padding:14px 16px}
.stat-mini .slabel{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.stat-mini .sval{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:1px;line-height:1}

.prog{background:#F0F0F0;border-radius:3px;height:4px;margin-top:8px;overflow:hidden}
.prog-fill{height:4px;border-radius:3px}

.role-badge{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px}
.role-cs{background:rgba(27,79,168,0.07);color:#1B4FA8;border:1px solid rgba(27,79,168,0.15)}
.role-teacher{background:rgba(5,150,105,0.07);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.role-sc{background:rgba(127,119,221,0.07);color:#534AB7;border:1px solid rgba(127,119,221,0.15)}
.role-admin{background:rgba(245,145,30,0.1);color:#C47010;border:1px solid rgba(245,145,30,0.2)}

.status-active{display:inline-flex;align-items:center;gap:5px;font-size:10px;color:#059669}
.status-inactive{display:inline-flex;align-items:center;gap:5px;font-size:10px;color:#DC2626}
.dot{width:6px;height:6px;border-radius:50%;background:currentColor}

.edit-form{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px}
.form-field{display:flex;flex-direction:column;gap:5px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

.btn-save{padding:10px 24px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;transition:background 0.2s}
.btn-save:hover{background:#2D6FDB}

.btn-danger{padding:9px 18px;background:transparent;border:1px solid rgba(220,38,38,0.3);border-radius:4px;color:#DC2626;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s}
.btn-danger:hover{background:rgba(220,38,38,0.06)}

@media(max-width:768px){.emp-show{padding:18px 14px}.stat-row-5,.stat-row-3{grid-template-columns:1fr 1fr}.edit-form{grid-template-columns:1fr}}
</style>

<div class="emp-show">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Employee Profile</div>
            <h1 class="page-title">{{ $employee->full_name }}</h1>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif

    @php
        $roleName  = $employee->user?->role?->role_name ?? '—';
        $roleCls   = match($roleName) { 'Customer Service'=>'role-cs', 'Teacher'=>'role-teacher', 'Student Care'=>'role-sc', 'Admin'=>'role-admin', default=>'role-cs' };
        $avatarBg  = match($roleName) { 'Customer Service'=>'rgba(27,79,168,0.1)', 'Teacher'=>'rgba(5,150,105,0.1)', 'Student Care'=>'rgba(127,119,221,0.1)', default=>'rgba(245,145,30,0.1)' };
        $avatarClr = match($roleName) { 'Customer Service'=>'#1B4FA8', 'Teacher'=>'#059669', 'Student Care'=>'#534AB7', default=>'#C47010' };
    @endphp

    {{-- Profile Card --}}
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar-lg" style="background:{{ $avatarBg }};color:{{ $avatarClr }}">
                {{ strtoupper(substr($employee->full_name, 0, 1)) }}
            </div>
            <div style="flex:1">
                <div style="font-size:18px;font-weight:500;color:#1A2A4A">{{ $employee->full_name }}</div>
                <div style="font-size:12px;color:#7A8A9A;margin-top:3px">{{ $employee->user?->email ?? '—' }}</div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap">
                    <span class="role-badge {{ $roleCls }}">{{ $roleName }}</span>
                    @if($employee->status === 'Active')
                        <span class="status-active"><div class="dot"></div> Active</span>
                    @else
                        <span class="status-inactive"><div class="dot"></div> Inactive</span>
                    @endif
                    <span style="font-size:10px;color:#AAB8C8">{{ $employee->branch?->name ?? '—' }}</span>
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-family:'Bebas Neue',sans-serif;font-size:26px;color:#1B4FA8;letter-spacing:2px">
                    {{ $employee->salary ? number_format($employee->salary).' LE' : '—' }}
                </div>
                <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-top:2px">Monthly Salary</div>
                <div style="font-size:10px;color:#AAB8C8;margin-top:4px">
                    Hired {{ \Carbon\Carbon::parse($employee->hired_at)->format('d M Y') }}
                </div>
            </div>
        </div>
        <div class="profile-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Employee ID</div>
                    <div class="value" style="font-family:monospace">#{{ $employee->employee_id }}</div>
                </div>
                <div class="info-item">
                    <div class="label">User ID</div>
                    <div class="value" style="font-family:monospace">#{{ $employee->user_id }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Last Login</div>
                    <div class="value">{{ $employee->user?->last_login_at ? \Carbon\Carbon::parse($employee->user->last_login_at)->diffForHumans() : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Account</div>
                    <div class="value">{{ $employee->user?->is_active ? 'Active' : 'Suspended' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Role-specific Performance --}}
    @if($roleName === 'Customer Service' && $csData)
    <div class="profile-card">
        <div class="profile-body">
            <div class="sec-label">CS Performance — Current Patch</div>
            <div class="stat-row stat-row-5">
                <div class="stat-mini">
                    <div class="slabel">Target</div>
                    <div class="sval" style="color:#1B4FA8">{{ number_format($csData['target']) }}</div>
                    <div style="font-size:9px;color:#AAB8C8;margin-top:3px">LE</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Achieved</div>
                    <div class="sval" style="color:#059669">{{ number_format($csData['achieved']) }}</div>
                    <div class="prog"><div class="prog-fill" style="width:{{ $csData['target'] > 0 ? min(100,round($csData['achieved']/$csData['target']*100)) : 0 }}%;background:#059669"></div></div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Registrations</div>
                    <div class="sval">{{ $csData['registrations'] }}</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Total Leads</div>
                    <div class="sval">{{ $csData['total_leads'] }}</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Active Leads</div>
                    <div class="sval" style="color:#F5911E">{{ $csData['active_leads'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($roleName === 'Teacher' && $teacherData)
    <div class="profile-card">
        <div class="profile-body">
            <div class="sec-label">Teacher Info</div>
            <div class="stat-row stat-row-3" style="margin-bottom:18px">
                <div class="stat-mini">
                    <div class="slabel">English Level</div>
                    <div class="sval" style="font-size:16px;font-family:'DM Sans',sans-serif;font-weight:500">
                        {{ $employee->teacher?->englishLevel?->level_name ?? '—' }}
                    </div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Active Courses</div>
                    <div class="sval">{{ $teacherData['active_courses'] }}</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Total Students</div>
                    <div class="sval">{{ $teacherData['total_students'] }}</div>
                </div>
            </div>
            @if($teacherData['contract'])
            <div class="sec-label">Current Contract</div>
            <div class="stat-row stat-row-3">
                <div class="stat-mini">
                    <div class="slabel">Contract Type</div>
                    <div class="sval" style="font-size:16px;font-family:'DM Sans',sans-serif">
                        {{ $teacherData['contract']->contract_type }}
                    </div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Max Sessions</div>
                    <div class="sval">{{ $teacherData['contract']->max_sessions_allowed }}</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Patch</div>
                    <div class="sval" style="font-size:14px;font-family:'DM Sans',sans-serif">
                        {{ $teacherData['contract']->patch?->name ?? '—' }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Edit Form --}}
    <div class="profile-card">
        <div class="profile-body">
            <div class="sec-label">Edit Employee</div>
            <form method="POST" action="{{ route('admin.employees.update', $employee->employee_id) }}">
                @csrf @method('PUT')
                <div class="edit-form">
                    <div class="form-field">
                        <label class="form-label">Salary (LE)</label>
                        <input type="number" name="salary" class="form-control"
                               value="{{ $employee->salary }}" placeholder="Monthly salary">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-control">
                            @foreach(\App\Models\Core\Branch::all() as $b)
                            <option value="{{ $b->branch_id }}" {{ $employee->branch_id == $b->branch_id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Active" {{ $employee->status === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $employee->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:20px;align-items:center">
                    <button type="submit" class="btn-save">Save Changes</button>
                    <form method="POST" action="{{ route('admin.employees.toggle', $employee->employee_id) }}" style="margin:0">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-danger">
                            {{ $employee->status === 'Active' ? 'Deactivate Account' : 'Activate Account' }}
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection