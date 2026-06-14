@extends('admin.layouts.app')
@section('title', 'New Employee')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.create-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#1B4FA8;color:#1B4FA8;text-decoration:none}
.form-card{max-width:860px;background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 4px 24px rgba(27,79,168,0.07)}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.form-body{padding:28px 32px}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);margin-top:4px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;margin-bottom:20px}
.form-field{display:flex;flex-direction:column;gap:5px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.req{color:#F5911E;margin-left:2px}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;appearance:none}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.form-divider{height:1px;background:rgba(27,79,168,0.06);margin:20px 0}
.form-footer{padding:20px 32px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-submit{padding:11px 28px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}
.btn-cancel{padding:10px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;text-decoration:none;transition:all 0.2s}
.btn-cancel:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8;text-decoration:none}
.slot-card{background:rgba(27,79,168,0.02);border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:14px 16px}
.day-label{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;cursor:pointer;font-size:12px;color:#4A5A7A;background:#fff;transition:all 0.2s;user-select:none}
.day-label:hover{border-color:#1B4FA8}
.day-label input{accent-color:#1B4FA8}
.day-label input:checked ~ *{color:#1B4FA8}
@media(max-width:680px){.form-grid{grid-template-columns:1fr}.create-page{padding:18px 14px}.form-body{padding:18px 20px}}
</style>

<div class="create-page">
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">New Employee</h1>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;max-width:860px">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('admin.employees.store') }}" onsubmit="return validateForm()">
            @csrf
            <div class="form-body">

                {{-- ── Account Info ── --}}
                <div class="sec-label">Account Information</div>
                <div class="form-grid">
                    <div class="form-field">
                        <label class="form-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Password <span class="req">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Role <span class="req">*</span></label>
                        <select name="role_id" id="roleSelect" class="form-control" onchange="onRoleChange()" required>
                            <option value="">— Select Role —</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->role_id }}" data-name="{{ $role->role_name }}"
                                {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- ── Employment Details ── --}}
                <div class="sec-label">Employment Details</div>
                <div class="form-grid">
                    <div class="form-field">
                        <label class="form-label">Branch <span class="req">*</span></label>
                        <select name="branch_id" class="form-control" required>
                            <option value="">— Select Branch —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}" {{ old('branch_id') == $b->branch_id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Salary (LE/month)</label>
                        <input type="number" name="salary" class="form-control" value="{{ old('salary') }}" placeholder="e.g. 8000">
                    </div>
                </div>

                {{-- ══ TEACHER SECTION ══ --}}
                <div id="teacherSection" style="display:none">
                    <div class="form-divider"></div>
                    <div class="sec-label">Teacher Details</div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label class="form-label">English Level <span class="req">*</span></label>
                            <select name="english_level_id" class="form-control">
                                <option value="">— Select Level —</option>
                                @foreach($englishLevels as $lvl)
                                <option value="{{ $lvl->english_level_id }}">{{ $lvl->level_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Contract Type</label>
                            <select name="contract_type_id" id="contractTypeSelect" class="form-control" onchange="fillMaxSessions()">
                                <option value="" data-max="">— Select —</option>
                                @foreach($contractTypes as $ct)
                                <option value="{{ $ct->contract_type_id }}" data-max="{{ $ct->max_sessions_allowed }}">
                                    {{ $ct->name }} (max {{ $ct->max_sessions_allowed }} sessions)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Max Sessions / Patch</label>
                            <input type="number" name="max_sessions" id="maxSessionsInput" class="form-control"
                                   readonly placeholder="Auto from contract type" min="1"
                                   style="background:rgba(27,79,168,0.03);cursor:not-allowed;color:#7A8A9A">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Assign to Patch</label>
                            <select name="patch_id" class="form-control">
                                <option value="">— Select Patch —</option>
                                @foreach($patches as $p)
                                <option value="{{ $p->patch_id }}">{{ $p->name }} ({{ $p->status }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ── Availability ── --}}
                    <div class="form-divider"></div>
                    <div class="sec-label">Availability — Teaching Slots</div>
                    <div style="font-size:11px;color:#7A8A9A;margin-bottom:14px">
                        Select which day pairs this teacher is available for each time slot.
                    </div>

                    @php
                        $dayPairs = ['sat_tue' => 'Sat & Tue', 'sun_wed' => 'Sun & Wed', 'mon_thu' => 'Mon & Thu'];
                    @endphp

                    <div style="display:flex;flex-direction:column;gap:10px">
                        @foreach($timeSlots as $slot)
                        <div class="slot-card">
                            <div style="margin-bottom:10px">
                                <span style="font-weight:600;color:#1A2A4A;font-size:13px">{{ $slot->name }}</span>
                                <span style="font-size:11px;color:#7A8A9A;margin-left:8px">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                </span>
                                <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8;margin-left:8px">
                                    {{ $slot->slot_type }}
                                </span>
                            </div>
                            <div style="display:flex;gap:10px;flex-wrap:wrap">
                                @foreach($dayPairs as $day => $label)
                                <label class="day-label" id="lbl-{{ $slot->time_slot_id }}-{{ $day }}">
                                    <input type="checkbox"
                                           name="availability[]"
                                           value="{{ $slot->time_slot_id }}:{{ $day }}"
                                           onchange="styleLabel(this)">
                                    {{ $label }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {{-- ── END TEACHER SECTION ── --}}

                {{-- ══ CS SECTION ══ --}}
                <div id="csSection" style="display:none">
                    <div class="form-divider"></div>
                    <div class="sec-label">CS Target (Current Patch)</div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label class="form-label">Monthly Target (LE)</label>
                            <input type="number" name="target_amount" class="form-control" placeholder="e.g. 20000">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Assign to Patch</label>
                            <select name="patch_id" class="form-control">
                                <option value="">— Select Patch —</option>
                                @foreach($patches as $p)
                                <option value="{{ $p->patch_id }}">{{ $p->name }} ({{ $p->status }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{-- ── END CS SECTION ── --}}

            </div>
            <div class="form-footer">
                <a href="{{ route('admin.employees.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Create Employee</button>
            </div>
        </form>
    </div>
</div>

<script>
function onRoleChange() {
    const sel      = document.getElementById('roleSelect');
    const roleName = sel.options[sel.selectedIndex]?.dataset.name ?? '';
    document.getElementById('teacherSection').style.display = roleName === 'Teacher'           ? 'block' : 'none';
    document.getElementById('csSection').style.display      = roleName === 'Customer Service'  ? 'block' : 'none';
}

function fillMaxSessions() {
    const sel   = document.getElementById('contractTypeSelect');
    const max   = sel.options[sel.selectedIndex]?.dataset.max ?? '';
    document.getElementById('maxSessionsInput').value = max;
}

function styleLabel(checkbox) {
    const label = checkbox.closest('label');
    if (checkbox.checked) {
        label.style.borderColor = '#1B4FA8';
        label.style.background  = 'rgba(27,79,168,0.05)';
        label.style.color       = '#1B4FA8';
    } else {
        label.style.borderColor = 'rgba(27,79,168,0.12)';
        label.style.background  = '#fff';
        label.style.color       = '#4A5A7A';
    }
}
function validateForm() {
    const teacherSection = document.getElementById('teacherSection');
    if (teacherSection.style.display === 'none') return true; // مش teacher → مش محتاج

    const checked = document.querySelectorAll('input[name="availability[]"]:checked');
    if (checked.length === 0) {
        let err = document.getElementById('availabilityError');
        if (!err) {
            err = document.createElement('div');
            err.id = 'availabilityError';
            err.style.cssText = 'color:#DC2626;font-size:11px;padding:10px 14px;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);border-radius:4px;margin-top:10px';
            err.textContent = '⚠ Please select at least one availability slot.';
            document.querySelector('.slot-card')?.closest('div[style]')?.after(err);
        }
        err.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    return true;
}

onRoleChange();
</script>
@endsection