@extends('admin.layouts.app')
@section('title', $employee->full_name)

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --blue:#1B4FA8;--blue-l:rgba(27,79,168,0.07);
    --orange:#F5911E;--orange-l:rgba(245,145,30,0.08);
    --green:#059669;--green-l:rgba(5,150,105,0.07);
    --red:#DC2626;--red-l:rgba(220,38,38,0.06);
    --border:rgba(27,79,168,0.09);
    --bg:#F8F6F2;--card:#fff;
    --text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;
}
*{box-sizing:border-box;}
.emp-show{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0;}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}

.pcard{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:20px;position:relative;box-shadow:0 2px 12px rgba(27,79,168,0.04);}
.pcard::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.pcard-body{padding:22px 28px;}
.pcard-header{padding:20px 28px;border-bottom:1px solid var(--border);background:rgba(27,79,168,0.01);}
.pcard-title{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:3px;color:var(--text);}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);margin-top:20px;}
.sec-label:first-child{margin-top:0;}

.stat-grid{display:grid;gap:12px;margin-bottom:0;}
.stat-grid-4{grid-template-columns:repeat(4,1fr);}
.stat-grid-3{grid-template-columns:repeat(3,1fr);}
.stat-grid-5{grid-template-columns:repeat(5,1fr);}
.stat-mini{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:14px 16px;}
.slabel{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;}
.sval{font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--blue);letter-spacing:1px;line-height:1;}

.role-badge{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;}
.role-cs{background:var(--blue-l);color:var(--blue);border:1px solid rgba(27,79,168,0.15);}
.role-teacher{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.15);}
.role-sc{background:rgba(127,119,221,0.07);color:#534AB7;border:1px solid rgba(127,119,221,0.15);}
.role-admin{background:var(--orange-l);color:#C47010;border:1px solid rgba(245,145,30,0.2);}
.status-active{display:inline-flex;align-items:center;gap:5px;font-size:10px;color:var(--green);}
.status-inactive{display:inline-flex;align-items:center;gap:5px;font-size:10px;color:var(--red);}
.dot{width:6px;height:6px;border-radius:50%;background:currentColor;}
.prog{background:#F0F0F0;border-radius:3px;height:4px;margin-top:8px;overflow:hidden;}
.prog-fill{height:4px;border-radius:3px;}

/* info pill */
.info-pill{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:var(--green-l);border:1px solid rgba(5,150,105,0.15);border-radius:6px;}
.info-pill-label{font-size:9px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);}
.info-pill-val{font-size:11px;font-weight:600;color:var(--green);}

/* course rows */
.course-row{display:flex;align-items:center;gap:14px;padding:12px 16px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.course-row:last-child{border-bottom:none;}
.course-row:hover{background:rgba(27,79,168,0.02);}
.status-pill{font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;border:1px solid;white-space:nowrap;}

/* form */
.form-control{width:100%;padding:10px 12px;border:1.5px solid rgba(27,79,168,0.12);border-radius:5px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
.field-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:6px;}
.field-group{display:flex;flex-direction:column;gap:5px;}
.edit-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;}

/* availability */
.avail-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
.avail-card{border:1.5px solid var(--border);border-radius:8px;padding:14px;transition:all 0.2s;}
.avail-card.has-slot{border-color:rgba(5,150,105,0.3);background:var(--green-l);}
.avail-card-title{font-size:11px;font-weight:600;color:var(--text);margin-bottom:10px;}
.avail-card.has-slot .avail-card-title{color:var(--green);}
.slot-select{width:100%;padding:8px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:11px;color:var(--text);background:#fff;outline:none;margin-top:6px;}
.slot-select:focus{border-color:var(--blue);}

/* buttons */
.btn-primary{padding:11px 28px;background:var(--blue);border:none;border-radius:5px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;transition:background 0.2s;}
.btn-primary:hover{background:#2D6FDB;}
.btn-danger-outline{padding:9px 18px;background:transparent;border:1px solid rgba(220,38,38,0.3);border-radius:5px;color:var(--red);font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.btn-danger-outline:hover{background:var(--red-l);}
.btn-green-outline{padding:9px 18px;background:transparent;border:1px solid rgba(5,150,105,0.3);border-radius:5px;color:var(--green);font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.btn-green-outline:hover{background:var(--green-l);}

.alert-success{background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:6px;margin-bottom:20px;font-size:13px;}
.alert-error{background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:6px;margin-bottom:20px;font-size:13px;}
.divider{height:1px;background:var(--border);margin:20px 0;}

@media(max-width:900px){
    .emp-show{padding:18px 14px;}
    .stat-grid-4,.stat-grid-3,.stat-grid-5{grid-template-columns:1fr 1fr;}
    .edit-grid,.avail-grid{grid-template-columns:1fr;}
}
</style>

<div class="emp-show">

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
    <div class="alert-success">✓ {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    @php
        $roleName  = $employee->user?->role?->role_name ?? '—';
        $roleCls   = match($roleName) { 'Customer Service'=>'role-cs', 'Teacher'=>'role-teacher', 'Student Care'=>'role-sc', 'Admin'=>'role-admin', default=>'role-cs' };
        $avatarBg  = match($roleName) { 'Customer Service'=>'var(--blue-l)', 'Teacher'=>'var(--green-l)', 'Student Care'=>'rgba(127,119,221,0.1)', default=>'var(--orange-l)' };
        $avatarClr = match($roleName) { 'Customer Service'=>'var(--blue)', 'Teacher'=>'var(--green)', 'Student Care'=>'#534AB7', default=>'#C47010' };
        $pairLabels= ['sat_tue'=>'Sat & Tue','sun_wed'=>'Sun & Wed','mon_thu'=>'Mon & Thu'];
        $allPairs  = ['sat_tue','sun_wed','mon_thu'];
    @endphp

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 1: PROFILE VIEW ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}

    {{-- Profile Hero --}}
    <div class="pcard">
        <div style="padding:24px 28px;display:flex;align-items:center;gap:20px;border-bottom:1px solid var(--border);">
            <div style="width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:24px;flex-shrink:0;background:{{ $avatarBg }};color:{{ $avatarClr }};">
                {{ strtoupper(substr($employee->full_name, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="font-size:18px;font-weight:600;">{{ $employee->full_name }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $employee->user?->email ?? '—' }}</div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap;">
                    <span class="role-badge {{ $roleCls }}">{{ $roleName }}</span>
                    @if($employee->status === 'Active')
                        <span class="status-active"><div class="dot"></div> Active</span>
                    @else
                        <span class="status-inactive"><div class="dot"></div> Inactive</span>
                    @endif
                    <span style="font-size:10px;color:var(--faint);">{{ $employee->branch?->name ?? '—' }}</span>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--blue);letter-spacing:2px;">{{ $employee->salary ? number_format($employee->salary).' LE' : '—' }}</div>
                <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);">Monthly Salary</div>
                <div style="font-size:10px;color:var(--faint);margin-top:4px;">Hired {{ \Carbon\Carbon::parse($employee->hired_at)->format('d M Y') }}</div>
            </div>
        </div>
        <div class="pcard-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;">
                <div><div class="slabel" style="font-size:8px;letter-spacing:2px;">Employee ID</div><div style="font-size:13px;font-weight:500;font-family:monospace;">#{{ $employee->employee_id }}</div></div>
                <div><div class="slabel" style="font-size:8px;letter-spacing:2px;">User ID</div><div style="font-size:13px;font-weight:500;font-family:monospace;">#{{ $employee->user_id }}</div></div>
                <div><div class="slabel" style="font-size:8px;letter-spacing:2px;">Last Login</div><div style="font-size:13px;font-weight:500;">{{ $employee->user?->last_login_at ? \Carbon\Carbon::parse($employee->user->last_login_at)->diffForHumans() : 'Never' }}</div></div>
                <div><div class="slabel" style="font-size:8px;letter-spacing:2px;">Account</div><div style="font-size:13px;font-weight:500;">{{ $employee->user?->is_active ? '✓ Active' : '✗ Suspended' }}</div></div>
            </div>
        </div>
    </div>

    {{-- CS Performance --}}
    @if($roleName === 'Customer Service' && $csData)
    <div class="pcard">
        <div class="pcard-header"><div class="pcard-title">CS Performance — {{ now()->format('F Y') }}</div></div>
        <div class="pcard-body">
            <div class="stat-grid stat-grid-5">
                <div class="stat-mini"><div class="slabel">Target</div><div class="sval" style="color:var(--blue);">{{ number_format($csData['target']) }}</div><div style="font-size:9px;color:var(--faint);margin-top:2px;">LE</div></div>
                <div class="stat-mini"><div class="slabel">Achieved</div><div class="sval" style="color:var(--green);">{{ number_format($csData['achieved']) }}</div><div class="prog"><div class="prog-fill" style="width:{{ $csData['target']>0 ? min(100,round($csData['achieved']/$csData['target']*100)) : 0 }}%;background:var(--green);"></div></div></div>
                <div class="stat-mini"><div class="slabel">Registrations</div><div class="sval">{{ $csData['registrations'] }}</div></div>
                <div class="stat-mini"><div class="slabel">Total Leads</div><div class="sval">{{ $csData['total_leads'] }}</div></div>
                <div class="stat-mini"><div class="slabel">Active Leads</div><div class="sval" style="color:var(--orange);">{{ $csData['active_leads'] }}</div></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Teacher View --}}
    @if($roleName === 'Teacher' && $teacherData)
    @php
        $availMap = $employee->teacher?->availability?->keyBy('day_of_week') ?? collect();
        $timeSlots= \App\Models\Academic\TimeSlot::where('is_active',true)->orderBy('start_time')->get();
    @endphp

    <div class="pcard">
        <div class="pcard-header"><div class="pcard-title">Teacher Overview</div></div>
        <div class="pcard-body">

            {{-- Stats --}}
            <div class="stat-grid stat-grid-4" style="margin-bottom:24px;">
                <div class="stat-mini"><div class="slabel">English Level</div><div class="sval" style="font-size:16px;font-family:'DM Sans',sans-serif;font-weight:600;">{{ $employee->teacher?->englishLevel?->level_name ?? '—' }}</div></div>
                <div class="stat-mini"><div class="slabel">Active Courses</div><div class="sval" style="color:var(--green);">{{ $teacherData['active_courses'] }}</div></div>
                <div class="stat-mini"><div class="slabel">Upcoming Courses</div><div class="sval" style="color:var(--blue);">{{ $teacherData['upcoming_courses'] }}</div></div>
                <div class="stat-mini"><div class="slabel">Active Students</div><div class="sval">{{ $teacherData['total_students'] }}</div></div>
            </div>

            {{-- Contract info --}}
            <div class="sec-label">Current Contract</div>
            @if($teacherData['contract'])
            <div class="stat-grid stat-grid-3" style="margin-bottom:24px;">
                <div class="stat-mini">
                    <div class="slabel">Patch</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);margin-top:4px;">{{ $teacherData['contract']->patch?->name ?? '—' }}</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Contract Type</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);margin-top:4px;">{{ $teacherData['contract']->contractType?->name ?? '—' }}</div>
                    <div style="font-size:10px;color:var(--faint);margin-top:2px;">max {{ $teacherData['contract']->contractType?->max_sessions_allowed ?? '—' }} sessions</div>
                </div>
                <div class="stat-mini">
                    <div class="slabel">Status</div>
                    <div style="font-size:13px;font-weight:600;margin-top:4px;color:{{ $teacherData['contract']->is_active ? 'var(--green)' : 'var(--red)' }};">{{ $teacherData['contract']->is_active ? '✓ Active' : '✗ Inactive' }}</div>
                </div>
            </div>
            @else
            <div style="padding:14px;background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);border-radius:6px;font-size:12px;color:#C47010;margin-bottom:24px;">⚠ No contract assigned for the current patch.</div>
            @endif

            {{-- Availability --}}
            <div class="sec-label">Availability</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;">
                @foreach($allPairs as $pair)
                @php $av = $availMap->get($pair); @endphp
                <div style="padding:10px 16px;border-radius:8px;border:1.5px solid {{ $av ? 'rgba(5,150,105,0.25)' : 'var(--border)' }};background:{{ $av ? 'var(--green-l)' : 'transparent' }};">
                    <div style="font-size:11px;font-weight:600;color:{{ $av ? 'var(--green)' : 'var(--faint)' }};">{{ $pairLabels[$pair] }}</div>
                    @if($av)
                    <div style="font-size:10px;color:var(--muted);margin-top:2px;">
                        {{ $av->timeSlot?->name ?? '—' }}
                        @if($av->timeSlot) · {{ \Carbon\Carbon::parse($av->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($av->timeSlot->end_time)->format('H:i') }} @endif
                    </div>
                    @else
                    <div style="font-size:10px;color:var(--faint);margin-top:2px;">Not available</div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Assigned Courses --}}
            <div class="sec-label">Assigned Courses</div>
            @if($teacherData['assigned_courses']->isNotEmpty())
            <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;">
                @foreach($teacherData['assigned_courses'] as $ci)
                @php $sc = match($ci->status) { 'Active'=>'var(--green)', 'Upcoming'=>'var(--blue)', 'Pending_Approval'=>'#C47010', default=>'var(--faint)' }; @endphp
                <div class="course-row">
                    <div style="width:7px;height:7px;border-radius:50%;background:{{ $sc }};flex-shrink:0;"></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:500;">{{ $ci->courseTemplate?->name ?? '—' }}@if($ci->level) <span style="font-size:10px;color:var(--faint);">· {{ $ci->level->name }}</span>@endif</div>
                        <div style="font-size:10px;color:var(--faint);margin-top:3px;display:flex;gap:10px;flex-wrap:wrap;">
                            <span>{{ $ci->patch?->name ?? '—' }}</span>
                            @foreach($ci->instanceSchedules as $sch)<span>{{ $pairLabels[$sch->day_of_week] ?? $sch->day_of_week }}{{ $sch->start_time ? ' · '.\Carbon\Carbon::parse($sch->start_time)->format('H:i') : '' }}</span>@endforeach
                            <span>{{ $ci->total_hours }} hrs · {{ $ci->type }}</span>
                        </div>
                    </div>
                    <span class="status-pill" style="color:{{ $sc }};border-color:{{ $sc }}20;background:{{ $sc }}10;">{{ str_replace('_',' ',$ci->status) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div style="font-size:12px;color:var(--faint);padding:10px 0;">No courses assigned yet.</div>
            @endif

        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── SECTION 2: EDIT FORM (all in one) ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}

    <div class="pcard">
        <div class="pcard-header">
            <div class="pcard-title">Edit Employee</div>
        </div>
        <div class="pcard-body">
            <form method="POST" action="{{ route('admin.employees.update-all', $employee->employee_id) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="target_month" value="{{ now()->format('Y-m') }}">
                <input type="hidden" name="role_name"   value="{{ $roleName }}">

                {{-- Basic Info --}}
                <div class="sec-label">Basic Information</div>
                <div class="edit-grid">
                    <div class="field-group">
                        <label class="field-label">Full Name</label>
                        <input type="text" name="full_name" value="{{ $employee->full_name }}" class="form-control" required>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Salary (LE)</label>
                        <input type="number" name="salary" value="{{ $employee->salary }}" class="form-control" min="0" step="0.01">
                    </div>
                    <div class="field-group">
                        <label class="field-label">New Password (Optional)</label>
                        <input type="password" name="new_password" placeholder="Leave blank to keep current" class="form-control">
                    </div>
                </div>

                {{-- Teacher Fields --}}
                @if($roleName === 'Teacher')
                <div class="sec-label">Teacher Settings</div>
                <div class="edit-grid" style="margin-bottom:20px;">
                    <div class="field-group">
                        <label class="field-label">English Level</label>
                        <select name="english_level_id" class="form-control">
                            <option value="">— Select —</option>
                            @foreach(\App\Models\Academic\EnglishLevel::all() as $lvl)
                            <option value="{{ $lvl->english_level_id }}" {{ $employee->teacher?->english_level_id == $lvl->english_level_id ? 'selected' : '' }}>
                                {{ $lvl->level_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sec-label">Contract Assignment</div>
                <div class="edit-grid" style="margin-bottom:20px;">
                    <div class="field-group">
                        <label class="field-label">Contract Type</label>
                        <select name="contract_type_id" class="form-control">
                            <option value="">— No Change —</option>
                            @foreach(\App\Models\HR\ContractType::where('is_active',true)->get() as $ct)
                            <option value="{{ $ct->contract_type_id }}"
                                {{ isset($teacherData) && $teacherData['contract']?->contract_type_id == $ct->contract_type_id ? 'selected' : '' }}>
                                {{ $ct->name }} (max {{ $ct->max_sessions_allowed }} sessions)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Patch</label>
                        <select name="patch_id" class="form-control">
                            <option value="">— No Change —</option>
                            @foreach(\App\Models\Academic\Patch::whereIn('status',['Active','Upcoming'])->get() as $p)
                            <option value="{{ $p->patch_id }}"
                                {{ isset($teacherData) && $teacherData['contract']?->patch_id == $p->patch_id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->status }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sec-label">Teaching Availability</div>
                <div class="avail-grid" style="margin-bottom:20px;">
                    @foreach($allPairs as $pair)
                    @php $av = $availMap->get($pair); @endphp
                    <div class="avail-card {{ $av ? 'has-slot' : '' }}" id="avail_card_{{ $pair }}">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <div class="avail-card-title">{{ $pairLabels[$pair] }}</div>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:11px;color:var(--muted);">
                                <input type="checkbox"
                                       name="availability_pairs[]"
                                       value="{{ $pair }}"
                                       {{ $av ? 'checked' : '' }}
                                       onchange="toggleAvailCard('{{ $pair }}', this.checked)">
                                Enable
                            </label>
                        </div>
                        <div id="slot_select_{{ $pair }}" style="{{ $av ? '' : 'display:none;' }}">
                            <label class="field-label" style="font-size:8px;">Time Slot</label>
                            <select name="time_slot_ids[{{ $pair }}]" class="slot-select">
                                <option value="">— Select Slot —</option>
                                @foreach($timeSlots as $ts)
                                <option value="{{ $ts->time_slot_id }}" {{ $av?->time_slot_id == $ts->time_slot_id ? 'selected' : '' }}>
                                    {{ $ts->name }} ({{ \Carbon\Carbon::parse($ts->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($ts->end_time)->format('H:i') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if(!$av)
                        <div id="slot_empty_{{ $pair }}" style="font-size:10px;color:var(--faint);">Not available</div>
                        @else
                        <div id="slot_empty_{{ $pair }}" style="font-size:10px;color:var(--faint);display:none;">Not available</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- CS Target --}}
                @if($roleName === 'Customer Service')
                <div class="sec-label">Monthly Target</div>
                <div class="edit-grid" style="margin-bottom:20px;">
                    <div class="field-group">
                        <label class="field-label">Target Amount (LE) — {{ now()->format('M Y') }}</label>
                        <input type="number" name="target_amount" value="{{ $csData['target'] ?? '' }}" placeholder="e.g. 15000" step="0.01" min="0" class="form-control">
                    </div>
                </div>
                @endif

                <div class="divider"></div>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <button type="submit" class="btn-primary">Save All Changes</button>
                </div>
            </form>

            <div class="divider"></div>
            <form method="POST" action="{{ route('admin.employees.toggle', $employee->employee_id) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="{{ $employee->status === 'Active' ? 'btn-danger-outline' : 'btn-green-outline' }}">
                    {{ $employee->status === 'Active' ? '⊗ Deactivate Account' : '✓ Activate Account' }}
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function toggleAvailCard(pair, checked) {
    const select = document.getElementById(`slot_select_${pair}`);
    const card   = document.getElementById(`avail_card_${pair}`);
    const empty  = document.getElementById(`slot_empty_${pair}`);
    if (checked) {
        if (select) select.style.display = 'block';
        if (empty)  empty.style.display  = 'none';
        card?.classList.add('has-slot');
    } else {
        if (select) select.style.display = 'none';
        if (empty)  empty.style.display  = 'block';
        card?.classList.remove('has-slot');
    }
}
</script>
@endsection