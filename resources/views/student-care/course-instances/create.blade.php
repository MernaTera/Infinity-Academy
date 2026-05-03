@extends('student-care.layouts.app')
@section('title', 'New Course Instance')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.ci-page{min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0 0 28px;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px;}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-size:10px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}
.form-layout{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;}
@media(max-width:1100px){.form-layout{grid-template-columns:1fr;}}
.form-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:20px;box-shadow:0 2px 12px rgba(27,79,168,0.05);position:relative;}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.form-card-body{padding:20px 24px 24px;}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);display:block;margin-bottom:16px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);}
.field-grid{display:grid;gap:14px;}
.field-grid-2{grid-template-columns:1fr 1fr;}
.field{display:flex;flex-direction:column;gap:5px;}
.field-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:500;}
.field-label .req{color:var(--orange);margin-left:2px;}
.field-input,.field-select{width:100%;padding:10px 12px;border:1.5px solid rgba(27,79,168,0.12);border-radius:5px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
.field-input:focus,.field-select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
.field-input:disabled,.field-select:disabled{background:#F4F4F4;color:var(--faint);cursor:not-allowed;}
.field-input::placeholder{color:var(--faint);}
.field-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-color:#fff;padding-right:30px;}
.field-select:disabled{background-image:none;}
.field-hint{font-size:10px;color:var(--faint);margin-top:3px;}
.capacity-badge{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;background:var(--blue-l);border:1px solid var(--border);border-radius:4px;font-size:12px;color:var(--blue);margin-top:4px;}
/* Pair checkboxes - ALL checkboxes */
.pair-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
.pair-option{position:relative;}
.pair-option input[type="checkbox"]{position:absolute;opacity:0;width:0;height:0;}
.pair-option label{display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 10px;border:1.5px solid var(--border);border-radius:7px;cursor:pointer;transition:all 0.2s;background:var(--card);text-align:center;}
.pair-option label:hover{border-color:rgba(27,79,168,0.3);}
.pair-option input:checked + label{border-color:var(--blue);background:var(--blue-l);}
.pair-name{font-size:12px;font-weight:600;color:var(--text);}
.pair-sub{font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;}
.pair-option input:checked + label .pair-name{color:var(--blue);}
.time-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:12px;}
.time-slot-btn{padding:8px 6px;border:1.5px solid var(--border);border-radius:5px;text-align:center;cursor:pointer;transition:all 0.2s;background:var(--card);font-size:11px;font-family:'DM Sans',sans-serif;}
.time-slot-btn:hover:not(.occupied):not(.break-time){border-color:var(--blue);background:var(--blue-l);}
.time-slot-btn.selected{border-color:var(--blue);background:var(--blue);color:#fff;}
.time-slot-btn.occupied{background:#FEF2F2;border-color:rgba(220,38,38,0.2);color:var(--red);cursor:not-allowed;opacity:0.7;}
.time-slot-btn.break-time{background:#FFFBF0;border-color:rgba(245,145,30,0.2);color:#C47010;cursor:not-allowed;}
.time-slot-time{font-weight:600;font-size:10px;}
.time-slot-label{font-size:9px;color:var(--faint);margin-top:2px;}
.time-slot-btn.selected .time-slot-label{color:rgba(255,255,255,0.7);}
.conflict-alert{background:var(--red-l);border:1px solid rgba(220,38,38,0.2);border-left:3px solid var(--red);border-radius:4px;padding:12px 16px;font-size:12px;color:var(--red);margin-top:12px;display:none;}
.conflict-alert.show{display:block;}
.preview-card{background:linear-gradient(135deg,#1A2A4A 0%,var(--blue) 100%);border-radius:8px;padding:20px;margin-top:16px;}
.prev-title{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:14px;}
.prev-row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.08);}
.prev-row:last-child{border-bottom:none;}
.prev-key{font-size:10px;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;}
.prev-val{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;color:#fff;}
.prev-val.orange{color:var(--orange);}
.prev-val.green{color:#4ADE80;}
.summary-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;position:sticky;top:24px;}
.summary-header{padding:16px 20px;border-bottom:1px solid var(--border);background:rgba(27,79,168,0.02);}
.summary-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--blue);}
.summary-body{padding:20px;}
.sum-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(27,79,168,0.04);font-size:12px;}
.sum-row:last-child{border-bottom:none;}
.sum-key{color:var(--faint);}
.sum-val{color:var(--text);font-weight:600;text-align:right;max-width:160px;}
.submit-area{padding:20px;border-top:1px solid var(--border);}
.btn-submit{width:100%;padding:13px;background:transparent;border:1.5px solid var(--blue);border-radius:5px;color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s;}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);}
.btn-submit:hover::before{transform:scaleX(1);}
.btn-submit:hover{color:#fff;}
.btn-submit span{position:relative;z-index:1;}
@media(max-width:768px){.ci-page{padding:18px 14px;}.field-grid-2{grid-template-columns:1fr;}.pair-grid{grid-template-columns:1fr;}.time-grid{grid-template-columns:repeat(3,1fr);}}
</style>

<div class="ci-page">
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care — Course Management</div>
            <h1 class="page-title">New Course Instance</h1>
        </div>
        <a href="{{ route('student-care.instances') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:14px 18px;border-radius:6px;margin-bottom:20px;font-size:13px;">
        <strong>Please fix the following:</strong>
        <ul style="margin:8px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('student-care.instance.store') }}" id="mainForm">
        @csrf
        <div class="form-layout">
            <div>

                {{-- COURSE --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Course Setup</span>
                    <div class="field-grid" style="margin-bottom:14px;">
                        <div class="field">
                            <label class="field-label">Course <span class="req">*</span></label>
                            <select name="course_template_id" id="ci_course" class="field-select" required onchange="onCourseChange()">
                                <option value="">— Select Course —</option>
                                @foreach($templates as $t)
                                <option value="{{ $t->course_template_id }}" data-english-level="{{ $t->english_level_id ?? '' }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field-grid field-grid-2">
                        <div class="field">
                            <label class="field-label">Level</label>
                            <select name="level_id" id="ci_level" class="field-select" disabled onchange="onLevelChange()">
                                <option value="">— Select Course First —</option>
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Sublevel</label>
                            <select name="sublevel_id" id="ci_sublevel" class="field-select" disabled>
                                <option value="">— Select Level First —</option>
                            </select>
                        </div>
                    </div>
                </div></div>

                {{-- ASSIGNMENT --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Assignment</span>
                    <div class="field-grid field-grid-2" style="margin-bottom:14px;">
                        <div class="field">
                            <label class="field-label">Patch <span class="req">*</span></label>
                            <select name="patch_id" class="field-select" required onchange="updateSummary()">
                                <option value="">— Select Patch —</option>
                                @foreach($patches as $p)
                                <option value="{{ $p->patch_id }}">{{ $p->name }} ({{ $p->status }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Teacher <span class="req">*</span></label>
                            <select name="teacher_id" id="ci_teacher" class="field-select" required onchange="onTeacherChange()">
                                <option value="">— Select Course First —</option>
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Branch</label>
                            <input type="text" class="field-input" value="{{ $userBranch?->name ?? '—' }}" disabled>
                            <input type="hidden" name="branch_id" value="{{ $userBranch?->branch_id }}">
                        </div>
                        <div class="field">
                            <label class="field-label">Type <span class="req">*</span></label>
                            <select name="type" class="field-select" required onchange="updateSummary()">
                                <option value="Group">Group</option>
                                <option value="Private">Private</option>
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Mode <span class="req">*</span></label>
                            <select name="delivery_mood" class="field-select" required onchange="onModeChange()">
                                <option value="Offline">Offline</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-grid field-grid-2">
                        <div class="field">
                            <label class="field-label">Room</label>
                            <select name="room_id" id="ci_room" class="field-select" onchange="onRoomChange()">
                                <option value="">— No Room —</option>
                                @foreach($rooms as $room)
                                <option value="{{ $room->room_id }}" data-capacity="{{ $room->capacity }}" data-type="{{ $room->room_type }}">
                                    {{ $room->name }} ({{ $room->room_type }}) — Cap: {{ $room->capacity }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Capacity <span class="req">*</span></label>
                            <input type="number" name="capacity" id="ci_capacity" class="field-input" placeholder="e.g. 12" min="1" required oninput="updateSummary()">
                            <div id="capacityHint" style="display:none;" class="capacity-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <span id="capacityText">—</span>
                            </div>
                        </div>
                    </div>
                </div></div>

                {{-- HOURS --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Hours & Sessions</span>
                    <div class="field-grid field-grid-2">
                        <div class="field">
                            <label class="field-label">Total Hours <span class="req">*</span></label>
                            <input type="number" name="total_hours" id="ci_total_hours" class="field-input" step="0.5" placeholder="e.g. 40" min="1" required oninput="recalculate()">
                        </div>
                        <div class="field">
                            <label class="field-label">Session Duration (hrs) <span class="req">*</span></label>
                            <input type="number" name="session_duration" id="ci_session_duration" class="field-input" step="0.5" placeholder="e.g. 2" min="0.5" required oninput="recalculate()">
                        </div>
                    </div>
                    <div id="sessionsInfo" style="display:none;margin-top:12px;padding:12px 14px;background:var(--blue-l);border:1px solid var(--border);border-radius:6px;">
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);">Sessions calculated</span>
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--blue);letter-spacing:2px;line-height:1;margin-top:4px;">
                            <span id="sessionsCount">—</span>
                            <span style="font-size:14px;color:var(--faint);font-family:'DM Sans',sans-serif;"> sessions</span>
                        </div>
                    </div>
                </div></div>

                {{-- SCHEDULE --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Schedule</span>
                    <div class="field-grid field-grid-2" style="margin-bottom:20px;">
                        <div class="field">
                            <label class="field-label">Start Date <span class="req">*</span></label>
                            <input type="date" name="start_date" id="ci_start_date" class="field-input" required onchange="onStartDateChange()">
                        </div>
                        <div class="field">
                            <label class="field-label">End Date</label>
                            <input type="date" name="end_date" id="ci_end_date" class="field-input" required readonly style="background:#F9F9F9;color:var(--faint);">
                            <span class="field-hint">Auto-calculated</span>
                        </div>
                    </div>

                    <span class="sec-label">Teaching Days <span style="color:var(--orange);font-size:11px;">* select one or more</span></span>
                    <div class="pair-grid">
                        <div class="pair-option">
                            <input type="checkbox" name="day_of_week[]" id="pair_sun_wed" value="sun_wed" onchange="onPairChange()">
                            <label for="pair_sun_wed"><div class="pair-name">Sun & Wed</div><div class="pair-sub">Pair 1</div></label>
                        </div>
                        <div class="pair-option">
                            <input type="checkbox" name="day_of_week[]" id="pair_sat_tue" value="sat_tue" onchange="onPairChange()">
                            <label for="pair_sat_tue"><div class="pair-name">Sat & Tue</div><div class="pair-sub">Pair 2</div></label>
                        </div>
                        <div class="pair-option">
                            <input type="checkbox" name="day_of_week[]" id="pair_mon_thu" value="mon_thu" onchange="onPairChange()">
                            <label for="pair_mon_thu"><div class="pair-name">Mon & Thu</div><div class="pair-sub">Pair 3</div></label>
                        </div>
                    </div>

                    <div id="timePickerSection" style="display:none;margin-top:20px;">
                        <span class="sec-label">Session Time <span style="color:var(--orange);">*</span></span>
                        <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;flex-wrap:wrap;">
                            <div class="field" style="flex:1;min-width:160px;">
                                <label class="field-label">Start Time <span class="req">*</span></label>
                                <input type="time" name="start_time" id="ci_start_time" class="field-input" step="1800" onchange="onTimeChange()">
                            </div>
                            <div style="padding-top:20px;color:var(--faint);">→</div>
                            <div class="field" style="flex:1;min-width:160px;">
                                <label class="field-label">End Time</label>
                                <input type="time" id="ci_end_time" class="field-input" readonly style="background:#F9F9F9;color:var(--faint);">
                            </div>
                        </div>
                        <input type="hidden" name="time_slot_id" id="ci_time_slot_id">
                        <div id="timeSlotsContainer">
                            <div style="font-size:11px;color:var(--faint);text-align:center;padding:20px;">Select teacher and day pair to see available times</div>
                        </div>
                        <div style="display:flex;gap:14px;margin-top:12px;flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--faint);"><div style="width:10px;height:10px;background:var(--blue);border-radius:2px;"></div> Available</div>
                            <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--faint);"><div style="width:10px;height:10px;background:#FEF2F2;border:1px solid rgba(220,38,38,0.3);border-radius:2px;"></div> Occupied</div>
                            <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--faint);"><div style="width:10px;height:10px;background:#FFFBF0;border:1px solid rgba(245,145,30,0.3);border-radius:2px;"></div> Break</div>
                        </div>
                    </div>

                    <div class="conflict-alert" id="conflictAlert">
                        <strong>⚠ Schedule Conflict Detected:</strong>
                        <div id="conflictDetails" style="margin-top:6px;"></div>
                    </div>

                    <div id="previewSection" style="display:none;margin-top:16px;">
                        <div class="preview-card">
                            <div class="prev-title">Session Preview</div>
                            <div class="prev-row"><span class="prev-key">Total Sessions</span><span class="prev-val green" id="prev-sessions">—</span></div>
                            <div class="prev-row"><span class="prev-key">Session Time</span><span class="prev-val" id="prev-time">—</span></div>
                            <div class="prev-row"><span class="prev-key">First Session</span><span class="prev-val" id="prev-first">—</span></div>
                            <div class="prev-row"><span class="prev-key">Last Session</span><span class="prev-val orange" id="prev-last">—</span></div>
                            <div class="prev-row"><span class="prev-key">End Date</span><span class="prev-val orange" id="prev-end">—</span></div>
                        </div>
                    </div>
                </div></div>

            </div>

            {{-- SUMMARY --}}
            <div>
                <div class="summary-card">
                    <div class="summary-header"><div class="summary-title">Instance Summary</div></div>
                    <div class="summary-body">
                        <div class="sum-row"><span class="sum-key">Course</span><span class="sum-val" id="sum-course">—</span></div>
                        <div class="sum-row"><span class="sum-key">Teacher</span><span class="sum-val" id="sum-teacher">—</span></div>
                        <div class="sum-row"><span class="sum-key">Type</span><span class="sum-val" id="sum-type">—</span></div>
                        <div class="sum-row"><span class="sum-key">Mode</span><span class="sum-val" id="sum-mode">—</span></div>
                        <div class="sum-row"><span class="sum-key">Capacity</span><span class="sum-val" id="sum-capacity">—</span></div>
                        <div class="sum-row"><span class="sum-key">Total Hours</span><span class="sum-val" id="sum-hours">—</span></div>
                        <div class="sum-row"><span class="sum-key">Sessions</span><span class="sum-val" id="sum-sessions">—</span></div>
                        <div class="sum-row"><span class="sum-key">Days</span><span class="sum-val" id="sum-days">—</span></div>
                        <div class="sum-row"><span class="sum-key">Time</span><span class="sum-val" id="sum-time">—</span></div>
                        <div class="sum-row"><span class="sum-key">Start</span><span class="sum-val" id="sum-start">—</span></div>
                        <div class="sum-row"><span class="sum-key">End</span><span class="sum-val" id="sum-end">—</span></div>
                    </div>
                    <div class="submit-area">
                        <button type="submit" class="btn-submit"><span>Create Course Instance</span></button>
                        <div style="font-size:10px;color:var(--faint);text-align:center;margin-top:10px;">Schedule &amp; sessions generated automatically</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let _breakSlots   = @json($breakSlots ?? []);
let _previewTimer = null;

const dayMap = { sun_wed:[0,3], sat_tue:[6,2], mon_thu:[1,4] };
const pairLabels = { sun_wed:'Sun & Wed', sat_tue:'Sat & Tue', mon_thu:'Mon & Thu' };

// ── Helpers ──
function getCheckedPairs() {
    return [...document.querySelectorAll('input[name="day_of_week[]"]:checked')].map(el => el.value);
}
function resetSelect(id, placeholder, disabled=false) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${placeholder}</option>`;
    el.disabled = disabled;
}
function setLoading(id) {
    const el = document.getElementById(id);
    el.innerHTML = '<option value="">Loading...</option>';
    el.disabled = true;
}
function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
}

// ── Course change ──
async function onCourseChange() {
    const sel      = document.getElementById('ci_course');
    const courseId = sel.value;
    const engLevel = sel.options[sel.selectedIndex]?.dataset.englishLevel || '';

    resetSelect('ci_level',   '— Select Level (optional) —', true);
    resetSelect('ci_sublevel','— Select Level First —', true);
    resetSelect('ci_teacher', '— Select Course First —', true);
    updateSummary();
    if (!courseId) return;

    try {
        setLoading('ci_level');
        const res  = await fetch(`/student-care/levels/${courseId}`);
        const data = await res.json();
        const lvl  = document.getElementById('ci_level');
        if (!data.length) {
            resetSelect('ci_level', '— No Levels —', true);
        } else {
            lvl.innerHTML = '<option value="">— No Level (optional) —</option>';
            data.forEach(l => {
                lvl.innerHTML += `<option value="${l.level_id}" data-hours="${l.total_hours??''}" data-session="${l.default_session_duration??''}" data-capacity="${l.max_capacity??''}">${l.name}</option>`;
            });
            lvl.disabled = false;
        }
    } catch { resetSelect('ci_level', '— Error loading levels —', true); }

    if (engLevel) await loadTeachers(engLevel);
}

// ── Level change ──
async function onLevelChange() {
    const lvl     = document.getElementById('ci_level');
    const levelId = lvl.value;
    const opt     = lvl.options[lvl.selectedIndex];

    resetSelect('ci_sublevel', '— Select Sublevel (optional) —', true);

    if (levelId && opt) {
        if (opt.dataset.hours)    document.getElementById('ci_total_hours').value      = opt.dataset.hours;
        if (opt.dataset.session)  document.getElementById('ci_session_duration').value = opt.dataset.session;
        if (opt.dataset.capacity) document.getElementById('ci_capacity').value          = opt.dataset.capacity;
        recalculate();
        updateSummary();
    }
    if (!levelId) return;

    try {
        setLoading('ci_sublevel');
        const res  = await fetch(`/student-care/sublevels/${levelId}`);
        const data = await res.json();
        const sub  = document.getElementById('ci_sublevel');
        if (!data.length) {
            resetSelect('ci_sublevel', '— No Sublevels —', true);
        } else {
            sub.innerHTML = '<option value="">— No Sublevel (optional) —</option>';
            data.forEach(s => {
                sub.innerHTML += `<option value="${s.sublevel_id}" data-hours="${s.total_hours??''}" data-session="${s.default_session_duration??''}">${s.name}</option>`;
            });
            sub.disabled = false;
        }
    } catch { resetSelect('ci_sublevel', '— Error —', true); }
}

// ── Load teachers ──
async function loadTeachers(englishLevelId) {
    setLoading('ci_teacher');
    try {
        const res  = await fetch(`/student-care/teachers/by-course-level/${englishLevelId}`);
        const data = await res.json();
        const sel  = document.getElementById('ci_teacher');
        if (!data.length) {
            resetSelect('ci_teacher', '— No available teachers —', true);
        } else {
            sel.innerHTML = '<option value="">— Select Teacher —</option>';
            data.forEach(t => {
                sel.innerHTML += `<option value="${t.teacher_id}">${t.employee?.full_name ?? '—'}</option>`;
            });
            sel.disabled = false;
        }
    } catch { resetSelect('ci_teacher', '— Error loading teachers —', true); }
}

// ── Teacher change ──
function onTeacherChange() {
    const sel  = document.getElementById('ci_teacher');
    const name = sel.options[sel.selectedIndex]?.text || '—';
    document.getElementById('sum-teacher').textContent = name;
    updateSummary();
    const pairs = getCheckedPairs();
    if (pairs.length) renderTimeSlots(pairs[0]);
}

// ── Room change ──
function onRoomChange() {
    const sel      = document.getElementById('ci_room');
    const opt      = sel.options[sel.selectedIndex];
    const cap      = opt?.dataset.capacity;
    const capInput = document.getElementById('ci_capacity');
    if (cap && sel.value) {
        capInput.value    = cap;
        capInput.readOnly = true;
        capInput.style.background = '#F4F4F4';
        capInput.style.color = 'var(--faint)';
        document.getElementById('capacityHint').style.display = 'flex';
        document.getElementById('capacityText').textContent   = `Room capacity: ${cap} students`;
    } else {
        capInput.readOnly = false;
        capInput.style.background = '';
        capInput.style.color = '';
        capInput.value    = '';
        document.getElementById('capacityHint').style.display = 'none';
    }
    updateSummary();
}

// ── Start date change ──
function onStartDateChange() {
    updateSummary();
    recalculate();
    const pairs = getCheckedPairs();
    if (pairs.length) renderTimeSlots(pairs[0]);
}

// ── Pair change ──
function onPairChange() {
    const pairs = getCheckedPairs();
    document.getElementById('sum-days').textContent = pairs.map(p => pairLabels[p]).join(' + ') || '—';
    document.getElementById('timePickerSection').style.display = pairs.length ? 'block' : 'none';
    if (pairs.length) renderTimeSlots(pairs[0]);
    recalculate();
    updateSummary();
}

// ── Render time slots ──
async function renderTimeSlots(pair) {
    const container = document.getElementById('timeSlotsContainer');
    const teacherId = document.getElementById('ci_teacher').value;
    const startDate = document.getElementById('ci_start_date').value;
    const endDate   = document.getElementById('ci_end_date').value;

    container.innerHTML = '<div style="font-size:11px;color:var(--faint);padding:12px;">Loading slots...</div>';

    let slots = [];
    try {
        const res = await fetch(`/student-care/time-slots-for-pair?teacher_id=${teacherId}&pair=${pair}&start_date=${startDate}&end_date=${endDate}`);
        if (res.ok) slots = await res.json();
    } catch {}
    if (!slots.length) slots = generateGenericSlots();

    let occupied = [];
    try {
        const res = await fetch(`/student-care/occupied-slots?teacher_id=${teacherId}&pair=${pair}&start_date=${startDate}&end_date=${endDate}`);
        if (res.ok) occupied = await res.json();
    } catch {}

    container.innerHTML = '<div class="time-grid">' + slots.map(slot => {
        const isBreak    = isBreakTime(slot.start);
        const isOccupied = occupied.includes(slot.start);
        const cls        = 'time-slot-btn' + (isBreak ? ' break-time' : '') + (isOccupied ? ' occupied' : '');
        const label      = isBreak ? 'Break' : (isOccupied ? 'Occupied' : 'Available');
        const click      = (!isBreak && !isOccupied) ? `onclick="selectTimeSlot(this,'${slot.start}','${slot.slot_id||''}')"` : '';
        return `<div class="${cls}" ${click} data-start="${slot.start}">
            <div class="time-slot-time">${slot.start}</div>
            <div class="time-slot-label">${label}</div>
        </div>`;
    }).join('') + '</div>';
}

function generateGenericSlots() {
    const slots = [];
    for (let h = 10; h < 21; h++) {
        slots.push({ start:`${String(h).padStart(2,'0')}:00`, slot_id:null });
        slots.push({ start:`${String(h).padStart(2,'0')}:30`, slot_id:null });
    }
    return slots;
}

function isBreakTime(t) {
    return (_breakSlots||[]).some(b => t >= b.start_time.slice(0,5) && t < b.end_time.slice(0,5));
}

function selectTimeSlot(el, startTime, slotId) {
    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('ci_start_time').value   = startTime;
    document.getElementById('ci_time_slot_id').value = slotId;
    onTimeChange();
}

// ── Time change ──
function onTimeChange() {
    const startTime = document.getElementById('ci_start_time').value;
    const dur       = parseFloat(document.getElementById('ci_session_duration').value) || 0;
    if (startTime && dur) {
        const [h,m] = startTime.split(':').map(Number);
        const total = h*60 + m + dur*60;
        const et    = `${String(Math.floor(total/60)).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
        document.getElementById('ci_end_time').value    = et;
        document.getElementById('sum-time').textContent = `${startTime} → ${et}`;
    }
    triggerPreview();
    updateSummary();
}

// ── Recalculate ──
function recalculate() {
    const totalHours = parseFloat(document.getElementById('ci_total_hours').value) || 0;
    const sessionDur = parseFloat(document.getElementById('ci_session_duration').value) || 0;

    if (totalHours && sessionDur) {
        const sessions = Math.ceil(totalHours / sessionDur);
        document.getElementById('sessionsCount').textContent = sessions;
        document.getElementById('sessionsInfo').style.display = 'block';
        document.getElementById('sum-sessions').textContent  = sessions + ' sessions';
        document.getElementById('sum-hours').textContent     = totalHours + ' hrs';

        const pairs     = getCheckedPairs();
        const startDate = document.getElementById('ci_start_date').value;
        if (pairs.length && startDate) {
            const endDate = calcEndDate(startDate, pairs, sessions);
            document.getElementById('ci_end_date').value    = endDate;
            document.getElementById('sum-end').textContent  = formatDate(endDate);
            document.getElementById('prev-end').textContent = formatDate(endDate);
        }
    } else {
        document.getElementById('sessionsInfo').style.display = 'none';
    }

    onTimeChange();
    triggerPreview();
    updateSummary();
}

// ── Calc end date ──
function calcEndDate(startDate, pairs, sessions) {
    if (!startDate || !pairs.length || !sessions) return '';
    const targetDays = pairs.flatMap(p => dayMap[p] || []);
    if (!targetDays.length) return '';
    const date = new Date(startDate + 'T00:00:00');
    let count = 0, last = null;
    for (let i = 0; i < 730 && count < sessions; i++) {
        if (targetDays.includes(date.getDay())) { count++; last = new Date(date); }
        date.setDate(date.getDate() + 1);
    }
    return last ? last.toISOString().split('T')[0] : '';
}

// ── Preview ──
function triggerPreview() {
    clearTimeout(_previewTimer);
    _previewTimer = setTimeout(fetchPreview, 500);
}
async function fetchPreview() {
    const pairs      = getCheckedPairs();
    const startDate  = document.getElementById('ci_start_date').value;
    const startTime  = document.getElementById('ci_start_time').value;
    const totalHours = parseFloat(document.getElementById('ci_total_hours').value) || 0;
    const sessionDur = parseFloat(document.getElementById('ci_session_duration').value) || 0;

    if (!startDate || !pairs.length || !startTime || !totalHours || !sessionDur) {
        document.getElementById('previewSection').style.display = 'none';
        return;
    }

    const sessions = Math.ceil(totalHours / sessionDur);
    const endDate  = calcEndDate(startDate, pairs, sessions);
    const [h,m]    = startTime.split(':').map(Number);
    const endMins  = h*60 + m + sessionDur*60;
    const endTime  = `${String(Math.floor(endMins/60)).padStart(2,'0')}:${String(endMins%60).padStart(2,'0')}`;

    document.getElementById('prev-sessions').textContent = sessions + ' sessions';
    document.getElementById('prev-time').textContent     = `${startTime} → ${endTime}`;
    document.getElementById('prev-first').textContent    = formatDate(startDate);
    document.getElementById('prev-last').textContent     = formatDate(endDate);
    document.getElementById('prev-end').textContent      = formatDate(endDate);
    document.getElementById('previewSection').style.display = 'block';

    const teacherId = document.getElementById('ci_teacher').value;
    if (teacherId && startDate && pairs.length) {
        try {
            const res = await fetch('/student-care/check-conflicts', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ teacher_id:teacherId, start_date:startDate, end_date:endDate, day_of_week:pairs, start_time:startTime, session_duration:sessionDur }),
            });
            if (res.ok) {
                const data = await res.json();
                const alert = document.getElementById('conflictAlert');
                if (data.conflicts?.length) {
                    document.getElementById('conflictDetails').innerHTML = data.conflicts.map(c => `<div>• ${c}</div>`).join('');
                    alert.classList.add('show');
                } else { alert.classList.remove('show'); }
            }
        } catch {}
    }
}

// ── Summary ──
function updateSummary() {
    const course   = document.getElementById('ci_course');
    const type     = document.querySelector('[name="type"]');
    const mode     = document.querySelector('[name="delivery_mood"]');
    const capacity = document.getElementById('ci_capacity').value;
    document.getElementById('sum-course').textContent   = course.options[course.selectedIndex]?.text?.split('(')[0].trim() || '—';
    document.getElementById('sum-type').textContent     = type?.value || '—';
    document.getElementById('sum-mode').textContent     = mode?.value || '—';
    document.getElementById('sum-capacity').textContent = capacity ? `${capacity} students` : '—';
    document.getElementById('sum-start').textContent    = formatDate(document.getElementById('ci_start_date').value);
    document.getElementById('sum-end').textContent      = formatDate(document.getElementById('ci_end_date').value);
}

function onModeChange() {
    const mode = document.querySelector('[name="delivery_mood"]').value;
    const roomSel = document.getElementById('ci_room');

    [...roomSel.options].forEach(opt => {
        if (!opt.value) return;
        const roomType = opt.dataset.type?.toLowerCase() || '';
        const match = mode === 'Online'
            ? roomType === 'online'
            : roomType !== 'online';

        opt.hidden   = !match;
        opt.disabled = !match;
    });

    const selected = roomSel.options[roomSel.selectedIndex];
    if (selected?.value && selected?.disabled) {
        roomSel.value = '';
        onRoomChange();
    }

    updateSummary();
}
document.addEventListener('DOMContentLoaded', onModeChange);
</script>
@endsection