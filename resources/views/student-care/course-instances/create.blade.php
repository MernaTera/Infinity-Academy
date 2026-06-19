@extends('student-care.layouts.app')
@section('title', 'New Course Instance')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--border:rgba(27,79,168,0.1);--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
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
.req{color:var(--orange);margin-left:2px;}
.field-input,.field-select{width:100%;padding:10px 12px;border:1.5px solid rgba(27,79,168,0.12);border-radius:5px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
.field-input:focus,.field-select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
.field-input:disabled,.field-select:disabled{background:#F4F4F4;color:var(--faint);cursor:not-allowed;}
.field-input[readonly]{background:#F4F4F4;color:var(--faint);}
.field-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-color:#fff;padding-right:30px;}
.field-select:disabled{background-image:none;}
.field-hint{font-size:10px;color:var(--faint);margin-top:3px;}

.capacity-badge{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;background:var(--blue-l);border:1px solid var(--border);border-radius:4px;font-size:12px;color:var(--blue);margin-top:4px;}

/* ── Pair Cards ── */
.pair-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
.pair-option{position:relative;}
.pair-option input[type="checkbox"]{position:absolute;opacity:0;width:0;height:0;}
.pair-card{display:flex;flex-direction:column;padding:14px 12px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;transition:all 0.2s;background:var(--card);}
.pair-card:hover:not(.pair-disabled){border-color:rgba(27,79,168,0.3);}
.pair-option input:checked + .pair-card{border-color:var(--blue);background:var(--blue-l);}
.pair-card.pair-disabled{opacity:0.4;cursor:not-allowed;background:#F8F8F8;}
.pair-name{font-size:12px;font-weight:600;color:var(--text);margin-bottom:2px;}
.pair-slot{font-size:10px;color:var(--faint);}
.pair-option input:checked + .pair-card .pair-name{color:var(--blue);}
.pair-courses{margin-top:8px;display:flex;flex-direction:column;gap:4px;}
.pair-course-item{font-size:9px;padding:3px 7px;border-radius:3px;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);color:#C47010;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* ── Time Picker ── */
.timepicker-section{margin-top:20px;}
.timepicker-block{margin-bottom:16px;padding:16px;background:rgba(27,79,168,0.02);border:1px solid var(--border);border-radius:8px;}
.timepicker-header{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--orange);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(245,145,30,0.15);display:flex;align-items:center;justify-content:space-between;}
.timepicker-slot-info{font-size:10px;color:var(--faint);font-family:monospace;}
.time-row{display:flex;align-items:center;gap:14px;margin-bottom:12px;flex-wrap:wrap;}
.time-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:8px;}
.time-slot-btn{padding:8px 6px;border:1.5px solid var(--border);border-radius:5px;text-align:center;cursor:pointer;transition:all 0.2s;background:var(--card);font-size:11px;font-family:'DM Sans',sans-serif;}
.time-slot-btn:hover:not(.occupied):not(.break-time):not(.too-late){border-color:var(--blue);background:var(--blue-l);}
.time-slot-btn.selected{border-color:var(--blue);background:var(--blue);color:#fff;}
.time-slot-btn.occupied{background:#FEF2F2;border-color:rgba(220,38,38,0.2);color:var(--red);cursor:not-allowed;opacity:0.7;}
.time-slot-btn.break-time{background:#FFFBF0;border-color:rgba(245,145,30,0.2);color:#C47010;cursor:not-allowed;}
.time-slot-btn.too-late{background:#FEF2F2;border-color:rgba(220,38,38,0.2);color:var(--red);cursor:not-allowed;opacity:0.5;}
.time-slot-time{font-weight:600;font-size:10px;}
.time-slot-label{font-size:9px;color:var(--faint);margin-top:2px;}
.time-slot-btn.selected .time-slot-label{color:rgba(255,255,255,0.7);}

/* Existing courses in pair */
.pair-existing{margin-top:10px;}
.pair-existing-title{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--orange);margin-bottom:6px;}
.pair-existing-item{display:flex;align-items:center;gap:8px;padding:6px 10px;background:rgba(245,145,30,0.05);border:1px solid rgba(245,145,30,0.15);border-radius:4px;margin-bottom:4px;font-size:11px;}
.pair-existing-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;}
.pair-existing-name{color:var(--text);font-weight:500;flex:1;}
.pair-existing-time{color:var(--faint);font-family:monospace;font-size:10px;}

/* Legend */
.slot-legend{display:flex;gap:14px;margin-top:10px;flex-wrap:wrap;}
.legend-item{display:flex;align-items:center;gap:5px;font-size:10px;color:var(--faint);}
.legend-dot{width:10px;height:10px;border-radius:2px;}

/* Conflict alert */
.conflict-alert{background:var(--red-l);border:1px solid rgba(220,38,38,0.2);border-left:3px solid var(--red);border-radius:4px;padding:12px 16px;font-size:12px;color:var(--red);margin-top:12px;display:none;}
.conflict-alert.show{display:block;}

/* Sessions info */
.sessions-info{display:none;margin-top:12px;padding:12px 14px;background:var(--blue-l);border:1px solid var(--border);border-radius:6px;}

/* Preview */
.preview-card{background:linear-gradient(135deg,#1A2A4A 0%,var(--blue) 100%);border-radius:8px;padding:20px;margin-top:16px;}
.prev-title{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:14px;}
.prev-row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.08);}
.prev-row:last-child{border-bottom:none;}
.prev-key{font-size:10px;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;}
.prev-val{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;color:#fff;}
.prev-val.orange{color:var(--orange);}
.prev-val.green{color:#4ADE80;}

/* Summary */
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

/* Schedule locked overlay */
.schedule-locked{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px;text-align:center;color:var(--faint);}
.schedule-locked svg{opacity:0.3;margin-bottom:10px;}
.schedule-locked-title{font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px;}
.schedule-locked-sub{font-size:11px;}

@media(max-width:768px){.field-grid-2{grid-template-columns:1fr;}.pair-grid{grid-template-columns:1fr;}.time-grid{grid-template-columns:repeat(3,1fr);}}
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

                {{-- ── COURSE SETUP ── --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Course Setup</span>
                    <div class="field-grid" style="margin-bottom:14px;">
                        <div class="field">
                            <label class="field-label">Course <span class="req">*</span></label>
                            <select name="course_template_id" id="ci_course" class="field-select" required onchange="onCourseChange()">
                                <option value="">— Select Course —</option>
                                @foreach($templates as $t)
                                <option value="{{ $t->course_template_id }}"
                                    data-english-level="{{ $t->english_level_id ?? '' }}"
                                    data-hours="{{ $t->total_hours ?? '' }}"
                                    data-session="{{ $t->default_session_duration ?? '' }}"
                                    data-capacity="{{ $t->max_capacity ?? '' }}">
                                    {{ $t->name }}
                                </option>
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
                            <select name="sublevel_id" id="ci_sublevel" class="field-select" disabled onchange="onSublevelChange()">
                                <option value="">— Select Level First —</option>
                            </select>
                        </div>
                    </div>
                </div></div>

                {{-- ── ASSIGNMENT ── --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Assignment</span>
                    <div class="field-grid field-grid-2" style="margin-bottom:14px;">
                        <div class="field">
                            <label class="field-label">Patch <span class="req">*</span></label>
                            <select name="patch_id" id="ci_patch" class="field-select" required onchange="onPatchChange()">
                                <option value="">— Select Patch —</option>
                                @foreach($patches as $p)
                                <option value="{{ $p->patch_id }}" data-start="{{ $p->start_date }}" data-end="{{ $p->end_date }}">
                                    {{ $p->name }} ({{ $p->status }})
                                </option>
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

                {{-- ── HOURS & SESSIONS ── --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Hours & Sessions</span>
                    <div class="field-grid field-grid-2">
                        <div class="field">
                            <label class="field-label">Total Hours <span class="req">*</span></label>
                            <input type="number" name="total_hours" id="ci_total_hours" class="field-input" step="0.5" placeholder="e.g. 24" min="1" required oninput="recalculate()">
                        </div>
                        <div class="field">
                            <label class="field-label">Session Duration (hrs) <span class="req">*</span></label>
                            <input type="number" name="session_duration" id="ci_session_duration" class="field-input" step="0.5" placeholder="e.g. 2" min="0.5" required oninput="recalculate()">
                        </div>
                    </div>
                    <div id="sessionsInfo" class="sessions-info">
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);">Sessions calculated</span>
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--blue);letter-spacing:2px;line-height:1;margin-top:4px;">
                            <span id="sessionsCount">—</span>
                            <span style="font-size:14px;color:var(--faint);font-family:'DM Sans',sans-serif;"> sessions</span>
                        </div>
                    </div>
                </div></div>

                {{-- ── SCHEDULE ── --}}
                <div class="form-card"><div class="form-card-body">
                    <span class="sec-label">Schedule</span>

                {{-- Dates --}}
                <div style="margin-bottom:20px;">
                    <label class="field-label">Start Date <span class="req">*</span></label>
                    <input type="hidden" name="start_date" id="ci_start_date">
                    <input type="hidden" name="end_date"   id="ci_end_date">

                    {{-- chips --}}
                    <div id="datechipsLocked" style="font-size:11px;color:var(--faint);padding:12px 0;">
                        Select teacher and patch first
                    </div>
                    <div id="datechipsContainer" style="display:none;"></div>

                    {{-- selected date display --}}
                    <div id="selectedDateDisplay" style="display:none;margin-top:8px;padding:8px 12px;background:var(--blue-l);border:1px solid var(--border);border-radius:5px;font-size:12px;color:var(--blue);">
                        <span id="selectedDateText">—</span>
                        <span style="float:right;cursor:pointer;color:var(--faint);" onclick="clearSelectedDate()">✕ change</span>
                    </div>

                    <div class="field" style="margin-top:8px;display:none;" id="endDateDisplay">
                        <label class="field-label">End Date (auto)</label>
                        <input type="text" id="ci_end_date_display" class="field-input" readonly style="background:#F9F9F9;color:var(--faint);">
                    </div>
                </div>

                    {{-- Teaching Days — locked until prerequisites met --}}
                    <span class="sec-label">Teaching Days</span>

                    {{-- Locked state --}}
                    <div id="scheduleLocked" class="schedule-locked">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <div class="schedule-locked-title">Select course, teacher, patch & hours first</div>
                        <div class="schedule-locked-sub">Schedule will unlock automatically</div>
                    </div>

                    {{-- Pairs grid — shown when ready --}}
                    <div id="scheduleReady" style="display:none;">
                        <div class="pair-grid" id="pairGrid">
                            {{-- populated by JS --}}
                        </div>

                        {{-- Time pickers per pair --}}
                        <div class="timepicker-section" id="timePickerSection" style="display:none;"></div>

                        {{-- Legend --}}
                        <div class="slot-legend" id="slotLegend" style="display:none;">
                            <div class="legend-item"><div class="legend-dot" style="background:var(--blue);"></div> Available</div>
                            <div class="legend-item"><div class="legend-dot" style="background:#FEF2F2;border:1px solid rgba(220,38,38,0.3);"></div> Occupied</div>
                            <div class="legend-item"><div class="legend-dot" style="background:#FFFBF0;border:1px solid rgba(245,145,30,0.3);"></div> Break</div>
                            <div class="legend-item"><div class="legend-dot" style="background:#FEF2F2;border:1px dashed rgba(220,38,38,0.3);"></div> Too Late</div>
                        </div>

                        {{-- Conflict alerts --}}
                        <div class="conflict-alert" id="conflictAlert">
                            <strong>⚠ Schedule Conflict:</strong>
                            <div id="conflictDetails" style="margin-top:6px;"></div>
                        </div>

                        {{-- Preview --}}
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
                    </div>
                </div></div>

            </div>

            {{-- ── SUMMARY ── --}}
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
                        <div style="font-size:10px;color:var(--faint);text-align:center;margin-top:10px;">Sessions generated automatically</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let _breakSlots   = @json($breakSlots ?? []);
let _previewTimer = null;
let _contractInfo = null;
let _teacherPairs = []; // available pairs from teacher with existing course info

const dayMap     = { sun_wed:[0,3], sat_tue:[6,2], mon_thu:[1,4] };
const pairLabels = { sun_wed:'Sun & Wed', sat_tue:'Sat & Tue', mon_thu:'Mon & Thu' };
const allPairs   = ['sat_tue','sun_wed','mon_thu'];

// ── Helpers ──────────────────────────────────────────────────────────────
function getCheckedPairs() {
    return [...document.querySelectorAll('input[name="day_of_week[]"]:checked')].map(el => el.value);
}
function resetSelect(id, placeholder, disabled=false) {
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = `<option value="">${placeholder}</option>`;
    el.disabled = disabled;
}
function setLoading(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = '<option value="">Loading...</option>';
    el.disabled = true;
}
function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
}
function setInstanceDefaults(hours, session, capacity) {
    const fh = document.getElementById('ci_total_hours');
    const fs = document.getElementById('ci_session_duration');
    const fc = document.getElementById('ci_capacity');
    if (hours    && fh) { fh.value = hours;    fh.readOnly = true; }
    if (session  && fs) { fs.value = session;  fs.readOnly = true; }
    if (capacity && fc) { fc.value = capacity; fc.readOnly = true; }
    recalculate(); updateSummary();
}
function clearInstanceDefaults() {
    ['ci_total_hours','ci_session_duration','ci_capacity'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.readOnly = false; el.value = '';
    });
}

// ── Readiness check — unlock schedule section ────────────────────────────
function checkScheduleReady() {
    const course   = document.getElementById('ci_course').value;
    const teacher  = document.getElementById('ci_teacher').value;
    const patch    = document.getElementById('ci_patch').value;
    const hours    = document.getElementById('ci_total_hours').value;
    const session  = document.getElementById('ci_session_duration').value;
    const ready    = course && teacher && patch && hours && session;

    document.getElementById('scheduleLocked').style.display  = ready ? 'none'  : 'flex';
    document.getElementById('scheduleReady').style.display   = ready ? 'block' : 'none';

    return !!ready;
}

// ── Course change ────────────────────────────────────────────────────────
async function onCourseChange() {
    const sel      = document.getElementById('ci_course');
    const courseId = sel.value;
    const opt      = sel.options[sel.selectedIndex];
    const engLevel = opt?.dataset.englishLevel || '';

    resetSelect('ci_level',   '— Select Level (optional) —', true);
    resetSelect('ci_sublevel','— Select Level First —',      true);
    resetSelect('ci_teacher', '— Select Course First —',     true);
    clearInstanceDefaults();
    updateSummary();
    checkScheduleReady();
    if (!courseId) return;

    const h = opt?.dataset.hours, s = opt?.dataset.session, c = opt?.dataset.capacity;
    if (h || s || c) setInstanceDefaults(h, s, c);

    const lvl = document.getElementById('ci_level');
    try {
        setLoading('ci_level');
        const res  = await fetch(`/student-care/levels/${courseId}`);
        const data = res.ok ? await res.json() : [];
        lvl.innerHTML = '<option value="">— No Level (optional) —</option>';
        data.forEach(l => {
            lvl.innerHTML += `<option value="${l.level_id}" data-hours="${l.total_hours??''}" data-session="${l.default_session_duration??''}" data-capacity="${l.max_capacity??''}">${l.name}</option>`;
        });
    } catch { lvl.innerHTML = '<option value="">— No Level (optional) —</option>'; }
    finally  { lvl.disabled = false; }

    if (engLevel) await loadTeachers(engLevel);
}

// ── Level change ─────────────────────────────────────────────────────────
async function onLevelChange() {
    const lvl = document.getElementById('ci_level');
    const opt = lvl.options[lvl.selectedIndex];
    resetSelect('ci_sublevel', '— Select Sublevel (optional) —', true);
    if (lvl.value && opt) {
        setInstanceDefaults(opt.dataset.hours, opt.dataset.session, opt.dataset.capacity);
    } else {
        const courseOpt = document.getElementById('ci_course').options[document.getElementById('ci_course').selectedIndex];
        setInstanceDefaults(courseOpt?.dataset.hours, courseOpt?.dataset.session, courseOpt?.dataset.capacity);
    }
    if (!lvl.value) return;
    const sub = document.getElementById('ci_sublevel');
    try {
        setLoading('ci_sublevel');
        const res  = await fetch(`/student-care/sublevels/${lvl.value}`);
        const data = res.ok ? await res.json() : [];
        sub.innerHTML = '<option value="">— No Sublevel (optional) —</option>';
        data.forEach(s => { sub.innerHTML += `<option value="${s.sublevel_id}" data-hours="${s.total_hours??''}" data-session="${s.default_session_duration??''}" data-capacity="${s.max_capacity??''}">${s.name}</option>`; });
    } catch { sub.innerHTML = '<option value="">— No Sublevel (optional) —</option>'; }
    finally  { sub.disabled = false; }
}

function onSublevelChange() {
    const sub    = document.getElementById('ci_sublevel');
    const opt    = sub.options[sub.selectedIndex];
    const lvlOpt = document.getElementById('ci_level').options[document.getElementById('ci_level').selectedIndex];
    if (sub.value && opt) setInstanceDefaults(opt.dataset.hours, opt.dataset.session, opt.dataset.capacity);
    else setInstanceDefaults(lvlOpt?.dataset.hours, lvlOpt?.dataset.session, lvlOpt?.dataset.capacity);
}

// ── Load teachers ────────────────────────────────────────────────────────
async function loadTeachers(englishLevelId) {
    setLoading('ci_teacher');
    try {
        const res  = await fetch(`/student-care/teachers/by-course-level/${englishLevelId}`);
        const data = await res.json();
        const sel  = document.getElementById('ci_teacher');
        if (!data.length) { resetSelect('ci_teacher', '— No available teachers —', true); return; }
        sel.innerHTML = '<option value="">— Select Teacher —</option>';
        data.forEach(t => { sel.innerHTML += `<option value="${t.teacher_id}">${t.employee?.full_name ?? '—'}</option>`; });
        sel.disabled = false;
    } catch { resetSelect('ci_teacher', '— Error —', true); }
}

// ── Patch change ─────────────────────────────────────────────────────────
async function onPatchChange() {
    const sel   = document.getElementById('ci_patch');
    const opt   = sel.options[sel.selectedIndex];
    const start = opt?.dataset.start;
    const end   = opt?.dataset.end;
    const input = document.getElementById('ci_start_date');
    if (start && end && sel.value) {
        input.min   = start;
        input.max   = end;
        input.value = start;
        onStartDateChange();
    } else {
        input.min = ''; input.max = '';
    }
    updateSummary();
    checkTeacherContract();
    refreshPairGrid();
    await loadFreeDates();
}

// ── Teacher change ───────────────────────────────────────────────────────
async function onTeacherChange() {
    const sel  = document.getElementById('ci_teacher');
    document.getElementById('sum-teacher').textContent = sel.options[sel.selectedIndex]?.text || '—';
    updateSummary();
    checkTeacherContract();
    checkScheduleReady();

    if (!sel.value) { _teacherPairs = []; renderPairGrid([]); return; }

    await refreshPairGrid();
    await loadFreeDates();
}

// ── Refresh pair grid with teacher data ──────────────────────────────────
async function refreshPairGrid() {
    const teacherId = document.getElementById('ci_teacher').value;
    const patchId   = document.getElementById('ci_patch').value;

    if (!teacherId) { renderPairGrid([]); return; }

    try {
        const url = `/student-care/teacher-available-pairs?teacher_id=${teacherId}${patchId ? '&patch_id='+patchId : ''}`;
        const res  = await fetch(url);
        _teacherPairs = res.ok ? await res.json() : [];
    } catch { _teacherPairs = []; }

    renderPairGrid(_teacherPairs);
}

// ── Render pair grid ─────────────────────────────────────────────────────
function renderPairGrid(pairsData) {
    const grid         = document.getElementById('pairGrid');
    const checkedBefore = getCheckedPairs();
    const availablePairs = pairsData.map(p => p.pair);
    grid.innerHTML = '';

    allPairs.forEach(pair => {
        const pairInfo    = pairsData.find(p => p.pair === pair);
        const isAvailable = !!pairInfo;
        const wasChecked  = checkedBefore.includes(pair);

        // Existing courses HTML
        let coursesHtml = '';
        if (pairInfo?.existing_courses?.length) {
            coursesHtml = `<div class="pair-existing">
                <div class="pair-existing-title">Existing courses</div>
                ${pairInfo.existing_courses.map(c => `
                    <div class="pair-existing-item">
                        <div class="pair-existing-dot" style="background:${c.status==='Active'?'#059669':c.status==='Upcoming'?'#1B4FA8':'#F5911E'};"></div>
                        <div style="flex:1;min-width:0;">
                            <div class="pair-existing-name">${c.name}</div>
                            <div class="pair-existing-time">${c.start_time}–${c.end_time} · ${c.start_date} → ${c.end_date}</div>
                        </div>
                    </div>`).join('')}
            </div>`;
        }

        const slotInfo = pairInfo ? `${pairInfo.slot_name} · ${pairInfo.slot_start}–${pairInfo.slot_end}` : 'Not available';

        const div = document.createElement('div');
        div.className = 'pair-option';
        div.innerHTML = `
            <input type="checkbox"
                   name="day_of_week[]"
                   id="pair_${pair}"
                   value="${pair}"
                   ${!isAvailable ? 'disabled' : ''}
                   ${isAvailable && wasChecked ? 'checked' : ''}
                   onchange="onPairChange()">
            <label for="pair_${pair}" class="pair-card ${!isAvailable ? 'pair-disabled' : ''}">
                <div class="pair-name">${pairLabels[pair]}</div>
                <div class="pair-slot">${slotInfo}</div>
                ${coursesHtml}
            </label>`;
        grid.appendChild(div);
    });

    // Auto-check if only one available pair
    if (availablePairs.length === 1) {
        const cb = document.getElementById(`pair_${availablePairs[0]}`);
        if (cb) cb.checked = true;
    }

    onPairChange();
}

// ── Pair change ───────────────────────────────────────────────────────────
function onPairChange() {
    const pairs   = getCheckedPairs();
    const section = document.getElementById('timePickerSection');

    document.getElementById('sum-days').textContent = pairs.map(p => pairLabels[p]).join(' + ') || '—';

    // Auto-adjust start date to first valid day
    adjustStartDateForPairs();

    section.innerHTML = '';
    if (!pairs.length) {
        section.style.display = 'none';
        document.getElementById('slotLegend').style.display = 'none';
        return;
    }
    section.style.display = 'block';
    document.getElementById('slotLegend').style.display = 'flex';

    pairs.forEach(pair => {
        const pairInfo = _teacherPairs.find(p => p.pair === pair);
        const div      = document.createElement('div');
        div.id         = `timepicker_${pair}`;
        div.className  = 'timepicker-block';
        div.innerHTML  = `
            <div class="timepicker-header">
                <span>${pairLabels[pair]}</span>
                ${pairInfo ? `<span class="timepicker-slot-info">${pairInfo.slot_start} – ${pairInfo.slot_end}</span>` : ''}
            </div>
            <div class="time-row">
                <div class="field" style="flex:1;min-width:140px;">
                    <label class="field-label">Start Time <span class="req">*</span></label>
                    <input type="time" name="start_times[${pair}]" id="ci_start_time_${pair}" class="field-input" step="1800" onchange="onTimeChange('${pair}')">
                </div>
                <div style="padding-top:20px;color:var(--faint);">→</div>
                <div class="field" style="flex:1;min-width:140px;">
                    <label class="field-label">End Time</label>
                    <input type="time" id="ci_end_time_${pair}" class="field-input" readonly style="background:#F9F9F9;color:var(--faint);">
                </div>
            </div>
            <input type="hidden" name="time_slot_ids[${pair}]" id="ci_time_slot_id_${pair}">
            <div id="timeSlotsContainer_${pair}">
                <div style="font-size:11px;color:var(--faint);text-align:center;padding:16px;">Loading available times...</div>
            </div>
            <div class="conflict-alert" id="conflictAlert_${pair}">
                <strong>⚠ Conflict (${pairLabels[pair]}):</strong>
                <div id="conflictDetails_${pair}" style="margin-top:6px;"></div>
            </div>`;
        section.appendChild(div);
        renderTimeSlots(pair);
    });

    recalculate();
    updateSummary();
    checkRoomAvailability();
}
// الـ _freeDates تخزني globally
let _freeDates = [];

async function loadFreeDates() {
    const teacherId = document.getElementById('ci_teacher').value;
    const patchId   = document.getElementById('ci_patch').value;

    document.getElementById('datechipsContainer').style.display = 'none';
    document.getElementById('datechipsLocked').style.display    = 'block';
    clearSelectedDate();

    if (!teacherId || !patchId) return;

    try {
        const res   = await fetch(`/student-care/teacher-free-dates?teacher_id=${teacherId}&patch_id=${patchId}`);
        _freeDates  = res.ok ? await res.json() : [];
    } catch { _freeDates = []; }

    renderDateChips();
}

function renderDateChips() {
    const container = document.getElementById('datechipsContainer');
    const locked    = document.getElementById('datechipsLocked');

    if (!_freeDates.length) {
        locked.textContent   = 'No available dates for this teacher in the selected patch.';
        locked.style.display = 'block';
        container.style.display = 'none';
        return;
    }

    locked.style.display    = 'none';
    container.style.display = 'block';

    const pairColors = { sat_tue:'#1B4FA8', sun_wed:'#059669', mon_thu:'#7F77DD' };

    container.innerHTML = `
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">
            ${_freeDates.map(d => {
                const isFree    = !d.occupied;
                const color     = pairColors[d.pair] || '#1B4FA8';
                const freeStyle = isFree
                    ? `border:1.5px solid ${color}20;background:${color}08;color:${color};cursor:pointer;`
                    : `border:1.5px solid rgba(220,38,38,0.15);background:rgba(220,38,38,0.04);color:#DC2626;cursor:not-allowed;opacity:0.6;`;
                const click = isFree ? `onclick="selectFreeDate('${d.date}','${d.pair}')"` : '';
                return `<div ${click} data-date="${d.date}"
                    style="padding:7px 12px;border-radius:6px;font-size:11px;font-weight:500;transition:all 0.15s;${freeStyle}">
                    <div style="font-weight:600;">${d.day} ${d.display}</div>
                    <div style="font-size:9px;opacity:0.7;">${d.occupied ? 'Occupied' : 'Free'}</div>
                </div>`;
            }).join('')}
        </div>
        <div style="display:flex;gap:12px;margin-top:8px;">
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;color:var(--faint);">
                <div style="width:8px;height:8px;background:var(--blue-l);border:1px solid var(--blue);border-radius:2px;"></div> Free
            </div>
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;color:var(--faint);">
                <div style="width:8px;height:8px;background:rgba(220,38,38,0.04);border:1px solid rgba(220,38,38,0.3);border-radius:2px;"></div> Occupied
            </div>
        </div>`;
}

function selectFreeDate(date, pair) {
    document.getElementById('ci_start_date').value = date;

    document.querySelectorAll('#datechipsContainer [data-date]').forEach(el => el.style.boxShadow = '');
    const chip = document.querySelector(`#datechipsContainer [data-date="${date}"]`);
    if (chip) chip.style.boxShadow = '0 0 0 2px var(--blue)';

    const d = new Date(date + 'T00:00:00');
    const formatted = d.toLocaleDateString('en-GB', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });
    document.getElementById('selectedDateText').textContent = formatted;
    document.getElementById('selectedDateDisplay').style.display = 'block';

    document.querySelectorAll('input[name="day_of_week[]"]').forEach(cb => cb.checked = false);
    const cb = document.getElementById(`pair_${pair}`);
    if (cb) cb.checked = true;

    onPairChange();
    onStartDateChange();
}
function toLocalDateStr(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function clearSelectedDate() {
    document.getElementById('ci_start_date').value = '';
    document.getElementById('ci_end_date').value   = '';
    document.getElementById('selectedDateDisplay').style.display = 'none';
    document.querySelectorAll('input[name="day_of_week[]"]').forEach(cb => cb.checked = false);
    document.getElementById('timePickerSection').innerHTML = '';
    document.getElementById('timePickerSection').style.display = 'none';
    document.getElementById('previewSection').style.display    = 'none';
    document.getElementById('sum-start').textContent = '—';
    document.getElementById('sum-end').textContent   = '—';
    document.getElementById('sum-days').textContent  = '—';
    document.getElementById('sum-time').textContent  = '—';
}
// ── Auto-adjust start date ────────────────────────────────────────────────
function adjustStartDateForPairs() {
    const pairs = getCheckedPairs();
    if (!pairs.length) return;
    const input = document.getElementById('ci_start_date');
    if (!input.value) return;

    const allTargetDays = pairs.flatMap(p => dayMap[p] || []);
    const date          = new Date(input.value + 'T00:00:00');
    const maxDate       = input.max ? new Date(input.max + 'T00:00:00') : null;

    for (let i = 0; i < 7; i++) {
        if (allTargetDays.includes(date.getDay())) {
            const adjusted = toLocalDateStr(date);
            if (adjusted !== input.value) {
                input.value = adjusted;
                recalculate();
                updateSummary();
            }
            return;
        }
        date.setDate(date.getDate() + 1);
        if (maxDate && date > maxDate) break;
    }
}

// ── Render time slots ─────────────────────────────────────────────────────
async function renderTimeSlots(pair) {
    const container  = document.getElementById(`timeSlotsContainer_${pair}`);
    if (!container) return;
    const teacherId  = document.getElementById('ci_teacher').value;
    const startDate  = document.getElementById('ci_start_date').value;
    const endDate    = document.getElementById('ci_end_date').value;
    const sessionDur = parseFloat(document.getElementById('ci_session_duration').value) || 0;

    if (!teacherId || !startDate) {
        container.innerHTML = '<div style="font-size:11px;color:var(--faint);text-align:center;padding:16px;">Select teacher and start date first</div>';
        return;
    }
    container.innerHTML = '<div style="font-size:11px;color:var(--faint);padding:10px;">Loading...</div>';

    let slots = [], occupied = [];
    try {
        const r1 = await fetch(`/student-care/time-slots-for-pair?teacher_id=${teacherId}&pair=${pair}&start_date=${startDate}&end_date=${endDate}`);
        if (r1.ok) slots = await r1.json();
    } catch {}
    if (!slots.length) slots = generateGenericSlots();
    try {
        const r2 = await fetch(`/student-care/occupied-slots?teacher_id=${teacherId}&pair=${pair}&start_date=${startDate}&end_date=${endDate}`);
        if (r2.ok) occupied = await r2.json();
    } catch {}

    container.innerHTML = '<div class="time-grid">' + slots.map(slot => {
        const isBreak    = isBreakTime(slot.start);
        const isOccupied = occupied.includes(slot.start);
        let isOutOfRange = false;
        if (sessionDur && slot.end) {
            const [sh,sm] = slot.start.split(':').map(Number);
            const [eh,em] = slot.end.split(':').map(Number);
            isOutOfRange  = (sh*60+sm + sessionDur*60) > (eh*60+em);
        }
        const cls   = 'time-slot-btn' + (isBreak ? ' break-time' : '') + (isOccupied ? ' occupied' : '') + (isOutOfRange ? ' too-late' : '');
        const label = isBreak ? 'Break' : isOccupied ? 'Occupied' : isOutOfRange ? 'Too Late' : 'Available';
        const click = (!isBreak && !isOccupied && !isOutOfRange) ? `onclick="selectTimeSlot(this,'${slot.start}','${slot.slot_id||''}','${pair}')"` : '';
        return `<div class="${cls}" ${click} data-start="${slot.start}"><div class="time-slot-time">${slot.start}</div><div class="time-slot-label">${label}</div></div>`;
    }).join('') + '</div>';
}

function generateGenericSlots() {
    const slots = [];
    for (let h = 7; h < 22; h++) {
        slots.push({ start:`${String(h).padStart(2,'0')}:00`, end:'22:00', slot_id:null });
        slots.push({ start:`${String(h).padStart(2,'0')}:30`, end:'22:00', slot_id:null });
    }
    return slots;
}

function isBreakTime(t) {
    return (_breakSlots||[]).some(b => t >= b.start_time.slice(0,5) && t < b.end_time.slice(0,5));
}

function selectTimeSlot(el, startTime, slotId, pair) {
    document.querySelectorAll(`#timeSlotsContainer_${pair} .time-slot-btn`).forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById(`ci_start_time_${pair}`).value   = startTime;
    document.getElementById(`ci_time_slot_id_${pair}`).value = slotId;
    onTimeChange(pair);
}

function onTimeChange(pair) {
    const startTime = document.getElementById(`ci_start_time_${pair}`)?.value;
    const dur       = parseFloat(document.getElementById('ci_session_duration').value) || 0;
    if (startTime && dur) {
        const [h,m] = startTime.split(':').map(Number);
        const total = h*60 + m + dur*60;
        const et    = `${String(Math.floor(total/60)).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
        document.getElementById(`ci_end_time_${pair}`).value = et;
        const times = getCheckedPairs().map(p => {
            const st = document.getElementById(`ci_start_time_${p}`)?.value;
            return st ? `${pairLabels[p]}: ${st}` : null;
        }).filter(Boolean);
        document.getElementById('sum-time').textContent = times.join(' | ') || '—';
    }
    triggerPreview();
    updateSummary();
    checkRoomAvailability();
}

// ── Recalculate ───────────────────────────────────────────────────────────
function recalculate() {
    const totalHours = parseFloat(document.getElementById('ci_total_hours').value) || 0;
    const sessionDur = parseFloat(document.getElementById('ci_session_duration').value) || 0;

    if (totalHours && sessionDur) {
        const sessions = Math.ceil(totalHours / sessionDur);
        document.getElementById('sessionsCount').textContent   = sessions;
        document.getElementById('sessionsInfo').style.display  = 'block';
        document.getElementById('sum-sessions').textContent    = sessions + ' sessions';
        document.getElementById('sum-hours').textContent       = totalHours + ' hrs';
        evaluateContractAlert();

        const pairs     = getCheckedPairs();
        const startDate = document.getElementById('ci_start_date').value;
        if (pairs.length && startDate) {
            const endDate = calcEndDate(startDate, pairs, sessions);
            document.getElementById('ci_end_date').value   = endDate;
            document.getElementById('sum-end').textContent = formatDate(endDate);
            const pe = document.getElementById('prev-end');
            if (pe) pe.textContent = formatDate(endDate);
        }
        pairs.forEach(pair => renderTimeSlots(pair));
    } else {
        document.getElementById('sessionsInfo').style.display = 'none';
    }
    checkScheduleReady();
    triggerPreview();
    updateSummary();
}

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
    return last ? toLocalDateStr(last) : '';
}

function onStartDateChange() {
    adjustStartDateForPairs();
    updateSummary();
    recalculate();
    checkRoomAvailability();
    getCheckedPairs().forEach(pair => renderTimeSlots(pair));
}

// ── Preview ───────────────────────────────────────────────────────────────
function triggerPreview() {
    clearTimeout(_previewTimer);
    _previewTimer = setTimeout(fetchPreview, 600);
}

async function fetchPreview() {
    const pairs      = getCheckedPairs();
    const startDate  = document.getElementById('ci_start_date').value;
    const totalHours = parseFloat(document.getElementById('ci_total_hours').value) || 0;
    const sessionDur = parseFloat(document.getElementById('ci_session_duration').value) || 0;
    const firstPair  = pairs.find(p => document.getElementById(`ci_start_time_${p}`)?.value);
    const startTime  = firstPair ? document.getElementById(`ci_start_time_${firstPair}`).value : null;

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
    if (teacherId && startDate && pairs.length && startTime) {
        try {
            const res = await fetch('/student-care/check-conflicts', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ teacher_id:teacherId, start_date:startDate, end_date:endDate, day_of_week:pairs, start_time:startTime, session_duration:sessionDur }),
            });
            if (res.ok) {
                const data = await res.json();
                pairs.forEach(p => {
                    const alertEl  = document.getElementById(`conflictAlert_${p}`);
                    const detailEl = document.getElementById(`conflictDetails_${p}`);
                    if (!alertEl || !detailEl) return;
                    if (data.conflicts?.length) {
                        detailEl.innerHTML = data.conflicts.map(c => `<div>• ${c}</div>`).join('');
                        alertEl.classList.add('show');
                    } else {
                        alertEl.classList.remove('show');
                    }
                });
            }
        } catch {}
    }
}

// ── Summary ───────────────────────────────────────────────────────────────
function updateSummary() {
    const course   = document.getElementById('ci_course');
    const type     = document.querySelector('[name="type"]');
    const mode     = document.querySelector('[name="delivery_mood"]');
    const capacity = document.getElementById('ci_capacity')?.value;
    if (course) document.getElementById('sum-course').textContent   = course.options[course.selectedIndex]?.text?.trim() || '—';
    if (type)   document.getElementById('sum-type').textContent     = type.value || '—';
    if (mode)   document.getElementById('sum-mode').textContent     = mode.value || '—';
    document.getElementById('sum-capacity').textContent = capacity ? `${capacity} students` : '—';
    document.getElementById('sum-start').textContent    = formatDate(document.getElementById('ci_start_date')?.value);
    document.getElementById('sum-end').textContent      = formatDate(document.getElementById('ci_end_date')?.value);
    const hours = document.getElementById('ci_total_hours')?.value;
    if (hours) document.getElementById('sum-hours').textContent = hours + ' hrs';
}

// ── Contract check ────────────────────────────────────────────────────────
async function checkTeacherContract() {
    const teacherId = document.getElementById('ci_teacher').value;
    const patchId   = document.getElementById('ci_patch').value;
    if (!teacherId || !patchId) { hideContractAlert(); return; }
    try {
        const res  = await fetch(`/student-care/teacher-contract-info?teacher_id=${teacherId}&patch_id=${patchId}`);
        const data = await res.json();
        _contractInfo = (data && typeof data.max_sessions !== 'undefined') ? data : null;
        evaluateContractAlert();
    } catch { _contractInfo = null; }
}

function evaluateContractAlert() {
    if (!_contractInfo) { hideContractAlert(); return; }
    const newSessions = parseInt(document.getElementById('sessionsCount')?.textContent) || 0;
    if (newSessions === 0) { hideContractAlert(); return; }

    const after = _contractInfo.current_sessions + newSessions;
    let el = document.getElementById('contractAlert');
    if (!el) {
        el = document.createElement('div');
        el.id = 'contractAlert';
        el.style.cssText = 'padding:12px 16px;border-radius:6px;margin-top:12px;font-size:12px;border-left:3px solid;';
        document.getElementById('sessionsInfo')?.after(el);
    }
    if (after > _contractInfo.max_sessions) {
        const over = after - _contractInfo.max_sessions;
        el.style.cssText = 'padding:12px 16px;border-radius:6px;margin-top:12px;font-size:12px;border-left:3px solid;background:rgba(220,38,38,0.06);border-color:#DC2626;color:#DC2626;display:block;';
        el.innerHTML = `⚠ <strong>Contract Limit Exceeded</strong> — ${_contractInfo.contract_name}: max <strong>${_contractInfo.max_sessions}</strong> sessions. Current: <strong>${_contractInfo.current_sessions}</strong> + new: <strong>${newSessions}</strong> = <strong>${after}</strong> (over by ${over}). Will require teacher approval.`;
    } else {
        el.style.cssText = 'padding:12px 16px;border-radius:6px;margin-top:12px;font-size:12px;border-left:3px solid;background:rgba(5,150,105,0.06);border-color:#059669;color:#059669;display:block;';
        el.innerHTML = `✓ <strong>${_contractInfo.contract_name}</strong> — ${_contractInfo.current_sessions} existing + ${newSessions} new = ${after}/${_contractInfo.max_sessions} sessions.`;
    }
}

function hideContractAlert() {
    const el = document.getElementById('contractAlert');
    if (el) el.style.display = 'none';
}

// ── Room availability ─────────────────────────────────────────────────────
async function checkRoomAvailability() {
    const roomId    = document.getElementById('ci_room')?.value;
    const startDate = document.getElementById('ci_start_date').value;
    const endDate   = document.getElementById('ci_end_date').value;
    const pairs     = getCheckedPairs();
    const dur       = document.getElementById('ci_session_duration').value;
    const firstPair = pairs.find(p => document.getElementById(`ci_start_time_${p}`)?.value);
    const startTime = firstPair ? document.getElementById(`ci_start_time_${firstPair}`).value : '';

    let el = document.getElementById('roomAlert');
    if (!el) {
        el = document.createElement('div');
        el.id = 'roomAlert';
        el.style.cssText = 'padding:10px 14px;border-radius:4px;margin-top:8px;font-size:12px;border-left:3px solid;display:none;';
        document.getElementById('ci_room')?.closest('.field')?.appendChild(el);
    }
    if (!roomId || !startDate || !startTime || !pairs.length) { el.style.display='none'; return; }
    try {
        const res  = await fetch(`/student-care/check-room-availability?room_id=${roomId}&start_date=${startDate}&end_date=${endDate}&pairs=${pairs.join(',')}&start_time=${startTime}&duration=${dur}`);
        const data = await res.json();
        el.style.cssText = data.available
            ? 'padding:10px 14px;border-radius:4px;margin-top:8px;font-size:12px;border-left:3px solid;background:rgba(5,150,105,0.06);border-color:#059669;color:#059669;display:block;'
            : 'padding:10px 14px;border-radius:4px;margin-top:8px;font-size:12px;border-left:3px solid;background:rgba(220,38,38,0.06);border-color:#DC2626;color:#DC2626;display:block;';
        el.innerHTML = data.available ? '✓ Room is available for the selected schedule' : `⚠ ${data.message}`;
    } catch { el.style.display='none'; }
}

// ── Mode change ───────────────────────────────────────────────────────────
function onRoomChange() {
    const sel = document.getElementById('ci_room');
    const opt = sel.options[sel.selectedIndex];
    const cap = opt?.dataset.capacity;
    const capInput = document.getElementById('ci_capacity');
    if (cap && sel.value) {
        capInput.value = cap; capInput.readOnly = true;
        document.getElementById('capacityHint').style.display = 'flex';
        document.getElementById('capacityText').textContent   = `Room capacity: ${cap} students`;
    } else {
        capInput.readOnly = false; capInput.value = '';
        document.getElementById('capacityHint').style.display = 'none';
    }
    updateSummary(); checkRoomAvailability();
}

function onModeChange() {
    const mode    = document.querySelector('[name="delivery_mood"]')?.value;
    const roomSel = document.getElementById('ci_room');
    if (!mode || !roomSel) return;
    [...roomSel.options].forEach(opt => {
        if (!opt.value) return;
        const roomType = opt.dataset.type?.toLowerCase() || '';
        const match    = mode === 'Online' ? roomType === 'online' : roomType !== 'online';
        opt.hidden = !match; opt.disabled = !match;
    });
    const selected = roomSel.options[roomSel.selectedIndex];
    if (selected?.value && selected?.disabled) { roomSel.value = ''; onRoomChange(); }
    updateSummary();
}

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    onModeChange();
    renderPairGrid([]);
    checkScheduleReady();
});
</script>
@endsection