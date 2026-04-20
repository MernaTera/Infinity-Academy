@extends('student-care.layouts.app')

@section('title', $instance->courseTemplate->name ?? 'Instance')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    body, .ci-show * { font-family: 'DM Sans', sans-serif; }
    body { min-width: fit-content; }

    .ci-show {
        background: #F8F6F2;
        min-height: 100vh;
        padding: 36px 32px;
        color: #1A2A4A;
    }

    /* ── HEADER ── */
    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 28px; padding-bottom: 20px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap; gap: 16px;
    }
    .page-eyebrow  { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title    { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; }
    .page-subtitle { font-size: 12px; color: #7A8A9A; margin-top: 4px; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 18px; background: transparent;
        border: 1px solid rgba(27,79,168,0.2); border-radius: 4px;
        color: #7A8A9A; font-size: 10px; letter-spacing: 2.5px;
        text-transform: uppercase; text-decoration: none;
        transition: all 0.3s; font-family: 'DM Sans', sans-serif;
    }
    .btn-back:hover { border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    /* ── OVERVIEW ── */
    .overview-card {
        background: rgba(255,255,255,0.85); backdrop-filter: blur(12px);
        border: 1px solid rgba(27,79,168,0.1); border-radius: 6px;
        padding: 22px 26px; margin-bottom: 22px;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
        position: relative; overflow: hidden;
    }
    .overview-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #F5911E, #1B4FA8, transparent);
    }
    .overview-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 18px;
    }
    .ov-label { font-size: 8px; letter-spacing: 2.5px; text-transform: uppercase; color: #AAB8C8; margin-bottom: 5px; }
    .ov-value { font-size: 13px; color: #1A2A4A; font-weight: 500; }
    .ov-divider { height: 1px; background: rgba(27,79,168,0.06); margin: 18px 0; }

    /* ── TAGS ── */
    .tag {
        display: inline-block; font-size: 9px; letter-spacing: 1px;
        padding: 2px 8px; border-radius: 3px; white-space: nowrap;
        text-transform: uppercase; font-weight: 500;
    }
    .tag-group   { background: rgba(27,79,168,0.05);  border: 1px solid rgba(27,79,168,0.12);  color: #2D6FDB; }
    .tag-private { background: rgba(245,145,30,0.05); border: 1px solid rgba(245,145,30,0.15); color: #C47010; }
    .tag-online  { background: rgba(21,128,61,0.05);  border: 1px solid rgba(21,128,61,0.15);  color: #15803D; }
    .tag-offline { background: rgba(122,138,154,0.06);border: 1px solid rgba(122,138,154,0.15);color: #7A8A9A; }

    /* ── STATUS ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 9px; letter-spacing: 1.2px; text-transform: uppercase;
        padding: 4px 9px; border-radius: 3px; white-space: nowrap; font-weight: 500;
    }
    .status-badge::before {
        content: ''; width: 4px; height: 4px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .s-active      { color: #15803D; background: rgba(21,128,61,0.08);   border: 1px solid rgba(21,128,61,0.2); }
    .s-upcoming    { color: #1B6FA8; background: rgba(27,111,168,0.08);  border: 1px solid rgba(27,111,168,0.2); }
    .s-completed   { color: #7A8A9A; background: rgba(122,138,154,0.08); border: 1px solid rgba(122,138,154,0.2); }
    .s-cancelled   { color: #DC2626; background: rgba(220,38,38,0.06);   border: 1px solid rgba(220,38,38,0.2); }
    .s-waiting     { color: #C47010; background: rgba(245,145,30,0.08);  border: 1px solid rgba(245,145,30,0.25); }
    .s-restricted  { color: #DC2626; background: rgba(220,38,38,0.06);   border: 1px solid rgba(220,38,38,0.2); }
    .s-scheduled   { color: #1B6FA8; background: rgba(27,111,168,0.08);  border: 1px solid rgba(27,111,168,0.2); }
    .s-default     { color: #7A8A9A; background: rgba(122,138,154,0.08); border: 1px solid rgba(122,138,154,0.2); }

    /* ── CAPACITY ── */
    .cap-wrap  { display: flex; align-items: center; gap: 8px; }
    .cap-track { width: 70px; height: 5px; background: rgba(27,79,168,0.08); border-radius: 3px; overflow: hidden; }
    .cap-fill  { height: 100%; border-radius: 3px; }

    /* ── TABS ── */
    .tab-nav {
        display: flex; gap: 2px; margin-bottom: 20px;
        border-bottom: 1px solid rgba(27,79,168,0.08);
    }
    .tab-btn {
        padding: 10px 20px; font-size: 10px; letter-spacing: 2px;
        text-transform: uppercase; background: transparent;
        border: none; cursor: pointer; color: #7A8A9A;
        font-family: 'DM Sans', sans-serif; font-weight: 500;
        position: relative; transition: color 0.2s;
        border-radius: 4px 4px 0 0;
    }
    .tab-btn::after {
        content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px;
        background: #1B4FA8; transform: scaleX(0);
        transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
    }
    .tab-btn:hover { color: #1B4FA8; }
    .tab-btn.active { color: #1B4FA8; }
    .tab-btn.active::after { transform: scaleX(1); }

    /* ── TABLE ── */
    .table-card {
        background: rgba(255,255,255,0.75); backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.1); border-radius: 6px;
        overflow: hidden; box-shadow: 0 4px 24px rgba(27,79,168,0.06);
    }
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-card table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .table-card thead tr { border-bottom: 1px solid rgba(27,79,168,0.08); }
    .table-card thead th {
        padding: 12px 14px; font-size: 9px; letter-spacing: 2.5px;
        text-transform: uppercase; color: #7A8A9A; font-weight: 500;
        background: rgba(27,79,168,0.02); text-align: left; white-space: nowrap;
    }
    .table-card tbody tr {
        border-bottom: 1px solid rgba(27,79,168,0.04);
        transition: background 0.2s;
        animation: rowFadeIn 0.3s ease both;
    }
    .table-card tbody tr:hover { background: rgba(27,79,168,0.025); }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody td { padding: 12px 14px; font-size: 13px; color: #4A5A7A; vertical-align: middle; }

    @keyframes rowFadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .st-name  { font-weight: 500; color: #1A2A4A; font-size: 13px; }
    .st-phone { font-size: 11px; color: #7A8A9A; font-family: monospace; margin-top: 2px; }

    /* ── ATTENDANCE LINK ── */
    .att-link {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; font-size: 9px; letter-spacing: 1.5px;
        text-transform: uppercase; border-radius: 3px;
        font-family: 'DM Sans', sans-serif; font-weight: 500;
        border: 1px solid rgba(27,79,168,0.25); color: #1B4FA8;
        background: transparent; text-decoration: none;
        transition: all 0.25s; white-space: nowrap;
    }
    .att-link:hover { background: rgba(27,79,168,0.07); border-color: #1B4FA8; text-decoration: none; }
    .att-link.done  { color: #15803D; border-color: rgba(21,128,61,0.25); }
    .att-link.done:hover { background: rgba(21,128,61,0.07); border-color: #15803D; }

    /* ── EMPTY ── */
    .empty-state { padding: 50px 24px; text-align: center; }
    .empty-state svg { margin: 0 auto 12px; opacity: 0.15; }
    .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 3px; color: #7A8A9A; }
    .empty-sub   { font-size: 12px; color: #AAB8C8; margin-top: 4px; }

    /* ── PLACEHOLDER ── */
    .tab-placeholder {
        padding: 60px 24px; text-align: center;
        background: rgba(255,255,255,0.75);
        border: 1px solid rgba(27,79,168,0.08); border-radius: 6px;
    }
    .tab-placeholder svg { margin: 0 auto 12px; opacity: 0.15; }
    .tab-placeholder-title { font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 3px; color: #7A8A9A; }
    .tab-placeholder-sub   { font-size: 12px; color: #AAB8C8; margin-top: 4px; }

    @media (max-width: 768px) { .ci-show { padding: 20px 14px; } }
    @media (max-width: 480px) { .page-header { flex-direction: column; align-items: flex-start; } }
</style>

<div class="ci-show">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Course Instance</div>
            <h1 class="page-title">{{ $instance->courseTemplate->name ?? '—' }}</h1>
            <p class="page-subtitle">
                @if($instance->level) {{ $instance->level->name }} @endif
                @if($instance->sublevel) — {{ $instance->sublevel->name }} @endif
            </p>
        </div>
        <a href="{{ route('student-care.instances') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    {{-- ── OVERVIEW ── --}}
    @php
        $count    = $instance->enrollments->count();
        $capacity = $instance->capacity;
        $pct      = $capacity > 0 ? round(($count / $capacity) * 100) : 0;
        $capColor = $count >= $capacity ? '#DC2626' : ($pct >= 80 ? '#C47010' : '#1B4FA8');
        $statusClass = match($instance->status) {
            'Active'    => 's-active',
            'Upcoming'  => 's-upcoming',
            'Completed' => 's-completed',
            'Cancelled' => 's-cancelled',
            default     => 's-default',
        };
        $typeClass = match($instance->type) {
            'Group'   => 'tag-group',
            'Private' => 'tag-private',
            default   => 'tag-group',
        };
        $modeClass = match($instance->delivery_mood) {
            'Online'  => 'tag-online',
            'Offline' => 'tag-offline',
            default   => 'tag-offline',
        };
    @endphp

    <div class="overview-card">
        <div class="overview-grid">
            <div>
                <div class="ov-label">Teacher</div>
                <div class="ov-value">{{ $instance->teacher->employee->full_name ?? $instance->teacher->name ?? '—' }}</div>
            </div>
            <div>
                <div class="ov-label">Branch</div>
                <div class="ov-value">{{ $instance->branch->name ?? '—' }}</div>
            </div>
            <div>
                <div class="ov-label">Room</div>
                <div class="ov-value">{{ $instance->room->name ?? '—' }}</div>
            </div>
            <div>
                <div class="ov-label">Patch</div>
                <div class="ov-value">{{ $instance->patch->name ?? '—' }}</div>
            </div>
            <div>
                <div class="ov-label">Type</div>
                <div class="ov-value"><span class="tag {{ $typeClass }}">{{ $instance->type ?? '—' }}</span></div>
            </div>
            <div>
                <div class="ov-label">Mode</div>
                <div class="ov-value"><span class="tag {{ $modeClass }}">{{ $instance->delivery_mood }}</span></div>
            </div>
            <div>
                <div class="ov-label">Capacity</div>
                <div class="ov-value">
                    <div class="cap-wrap">
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:{{ $capColor }};letter-spacing:1px;">
                            {{ $count }}/{{ $capacity }}
                        </span>
                        <div class="cap-track">
                            <div class="cap-fill" style="width:{{ $pct }}%;background:{{ $capColor }};"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="ov-label">Schedule</div>
                <div class="ov-value">
                    {{ \Carbon\Carbon::parse($instance->start_date)->format('d M Y') }}
                    <span style="color:#AAB8C8;"> → </span>
                    {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}
                </div>
            </div>
            <div>
                <div class="ov-label">Total Hours</div>
                <div class="ov-value">
                    <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;">
                        {{ $instance->total_hours }}
                    </span>
                    <span style="font-size:10px;color:#AAB8C8;"> hrs</span>
                </div>
            </div>
            <div>
                <div class="ov-label">Session Duration</div>
                <div class="ov-value">{{ $instance->session_duration }} hr/session</div>
            </div>
            <div>
                <div class="ov-label">Status</div>
                <div class="ov-value">
                    <span class="status-badge {{ $statusClass }}">{{ $instance->status }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABS ── --}}
    <div class="tab-nav">
        <button onclick="showTab('students')" class="tab-btn active">
            Students ({{ $count }})
        </button>
        <button onclick="showTab('attendance')" class="tab-btn">Attendance</button>
        <button onclick="showTab('schedule')" class="tab-btn">Schedule</button>
    </div>

    {{-- ══ STUDENTS TAB ══ --}}
    <div id="studentsTab">
        <div class="table-card">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Hours Remaining</th>
                            <th>Start Date</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($instance->enrollments as $i => $enrollment)
                    @php
                        $eClass = match($enrollment->status) {
                            'Active'           => 's-active',
                            'Waiting'          => 's-waiting',
                            'Restricted'       => 's-restricted',
                            'Cancelled'        => 's-cancelled',
                            'Postponed'        => 's-waiting',
                            'Pending_Approval' => 's-scheduled',
                            default            => 's-default',
                        };
                    @endphp
                    <tr>
                        <td style="color:#AAB8C8;font-size:11px;">{{ $i + 1 }}</td>
                        <td>
                            <div class="st-name">{{ $enrollment->student->full_name ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="st-phone">
                                {{ $enrollment->student->phones->first()->phone_number ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $eClass }}">{{ $enrollment->status }}</span>
                        </td>
                        <td>
                            @if($enrollment->hours_remaining !== null)
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:#1B4FA8;letter-spacing:1px;">
                                    {{ $enrollment->hours_remaining }}
                                </span>
                                <span style="font-size:10px;color:#AAB8C8;"> hrs</span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12px;color:#1A2A4A;">
                                {{ $enrollment->actual_start_date ? \Carbon\Carbon::parse($enrollment->actual_start_date)->format('d M Y') : '—' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#C47010;">Pending</span>
                        </td>

                        {{-- ✅ Actions Column --}}
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                @if($enrollment->status === 'Active')
                                <button onclick="openPostponeModal({{ $enrollment->enrollment_id }}, '{{ addslashes($enrollment->student?->full_name) }}')"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(245,145,30,0.25);background:transparent;color:#C47010;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s"
                                    onmouseover="this.style.background='rgba(245,145,30,0.07)'"
                                    onmouseout="this.style.background='transparent'">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Postpone
                                </button>
                                @endif

                                @if($enrollment->status === 'Postponed')
                                <form method="POST" action="{{ route('student-care.postponed.resume', $enrollment->activePostponement?->postponement_id) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(5,150,105,0.25);background:transparent;color:#059669;cursor:pointer;font-family:'DM Sans',sans-serif">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                        Resume
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <div class="empty-title">No Students Yet</div>
                                <div class="empty-sub">No active enrollments in this instance</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══ ATTENDANCE TAB ══ --}}
    <div id="attendanceTab" style="display:none;">
        <div class="table-card">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($instance->sessions as $session)
                        @php
                            $sClass = match($session->status) {
                                'Scheduled' => 's-scheduled',
                                'Completed' => 's-completed',
                                'Cancelled' => 's-cancelled',
                                default     => 's-default',
                            };
                        @endphp
                        <tr>
                            <td style="color:#AAB8C8;font-size:11px;">{{ $session->session_number ?? '—' }}</td>
                            <td>
                                <span style="font-size:12px;color:#1A2A4A;font-weight:500;">
                                    {{ $session->session_date ? \Carbon\Carbon::parse($session->session_date)->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td>
                                @if($session->start_time && $session->end_time)
                                    <span style="font-size:11px;color:#7A8A9A;font-family:monospace;">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                        – {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </span>
                                @else
                                    <span style="color:#AAB8C8;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $sClass }}">{{ $session->status }}</span>
                            </td>
                            <td>
                                @if($session->status !== 'Cancelled')
                                    <a href="{{ route('student-care.attendance.show', $session->course_session_id) }}"
                                       class="att-link {{ $session->status === 'Completed' ? 'done' : '' }}">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        {{ $session->status === 'Completed' ? 'Edit' : 'Take' }} Attendance
                                    </a>
                                @else
                                    <span style="font-size:10px;color:#AAB8C8;letter-spacing:1px;">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <div class="empty-title">No Sessions Yet</div>
                                    <div class="empty-sub">Sessions will appear once generated</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- ══ SCHEDULE TAB ══ --}}
<div id="scheduleTab" style="display:none;">

    @php
        $schedule = $instance->instanceSchedules->first();
        $sessions = $instance->sessions->sortBy('session_number');

        $pairLabels = [
            'sun_wed' => 'Sunday & Wednesday',
            'sat_tue' => 'Saturday & Tuesday',
            'mon_thu' => 'Monday & Thursday',
        ];

        $completedCount  = $sessions->where('status','Completed')->count();
        $scheduledCount  = $sessions->where('status','Scheduled')->count();
        $cancelledCount  = $sessions->where('status','Cancelled')->count();
        $totalCount      = $sessions->count();
        $progressPct     = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
    @endphp

    @if(!$schedule)
        {{-- No schedule yet --}}
        <div style="text-align:center;padding:60px 24px;
                    background:rgba(255,255,255,0.75);
                    border:1px solid rgba(27,79,168,0.08);
                    border-radius:6px;">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1" style="margin:0 auto 14px;display:block">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:#7A8A9A;margin-bottom:6px">
                No Schedule Set
            </div>
            <div style="font-size:12px;color:#AAB8C8">
                Go to Course Instances list and click "Set Schedule" to generate sessions.
            </div>
        </div>

    @else
        {{-- ── Schedule Info Card ── --}}
        <div style="background:rgba(255,255,255,0.85);border:1px solid rgba(27,79,168,0.1);
                    border-radius:6px;padding:20px 24px;margin-bottom:20px;
                    position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:2px;
                        background:linear-gradient(90deg,transparent,#1B4FA8,transparent)"></div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:18px;margin-bottom:18px">
                <div>
                    <div style="font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px">Day Pair</div>
                    <div style="font-size:13px;color:#1A2A4A;font-weight:500">
                        {{ $pairLabels[$schedule->day_of_week] ?? $schedule->day_of_week }}
                    </div>
                </div>
                <div>
                    <div style="font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px">Time Slot</div>
                    <div style="font-size:13px;color:#1A2A4A;font-weight:500">
                        {{ $schedule->timeSlot?->name ?? '—' }}
                    </div>
                    <div style="font-size:10px;color:#7A8A9A;margin-top:2px">
                        {{ $schedule->timeSlot ? \Carbon\Carbon::parse($schedule->timeSlot->start_time)->format('H:i').' – '.\Carbon\Carbon::parse($schedule->timeSlot->end_time)->format('H:i') : '' }}
                    </div>
                </div>
                <div>
                    <div style="font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px">Session Start</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:2px;line-height:1">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                    </div>
                </div>
                <div>
                    <div style="font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px">Session End</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:2px;line-height:1">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->addHours((float)$instance->session_duration)->format('H:i') }}
                    </div>
                </div>
                <div>
                    <div style="font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px">Total Sessions</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:2px;line-height:1">
                        {{ $totalCount }}
                    </div>
                </div>
                <div>
                    <div style="font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px">Duration</div>
                    <div style="font-size:13px;color:#1A2A4A;font-weight:500">
                        {{ $instance->session_duration }} hr / session
                    </div>
                </div>
            </div>

            {{-- Progress --}}
            <div style="border-top:1px solid rgba(27,79,168,0.06);padding-top:16px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Course Progress</span>
                    <div style="display:flex;gap:14px">
                        <span style="font-size:10px;color:#059669">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:16px">{{ $completedCount }}</span> Completed
                        </span>
                        <span style="font-size:10px;color:#1B4FA8">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:16px">{{ $scheduledCount }}</span> Upcoming
                        </span>
                        @if($cancelledCount > 0)
                        <span style="font-size:10px;color:#DC2626">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:16px">{{ $cancelledCount }}</span> Cancelled
                        </span>
                        @endif
                    </div>
                </div>
                <div style="background:#F0F0F0;border-radius:4px;height:6px;overflow:hidden">
                    <div style="height:6px;border-radius:4px;background:linear-gradient(90deg,#1B4FA8,#059669);
                                width:{{ $progressPct }}%;transition:width 0.6s ease"></div>
                </div>
                <div style="font-size:10px;color:#AAB8C8;margin-top:6px;text-align:right">
                    {{ $progressPct }}% complete
                </div>
            </div>
        </div>

        {{-- ── Sessions List ── --}}
        @if($sessions->isNotEmpty())
        <div class="table-card">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        @php
                            $isToday    = \Carbon\Carbon::parse($session->session_date)->isToday();
                            $isPast     = \Carbon\Carbon::parse($session->session_date)->isPast();
                            $sClass     = match($session->status) {
                                'Scheduled' => 's-scheduled',
                                'Completed' => 's-completed',
                                'Cancelled' => 's-cancelled',
                                default     => 's-default',
                            };
                        @endphp
                        <tr style="{{ $isToday ? 'background:rgba(27,79,168,0.03)' : '' }}">

                            {{-- Number --}}
                            <td>
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;
                                             color:{{ $session->status === 'Completed' ? '#059669' : ($isToday ? '#1B4FA8' : '#AAB8C8') }};
                                             letter-spacing:1px;line-height:1">
                                    {{ $session->session_number }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td>
                                <div style="font-size:12px;color:#1A2A4A;font-weight:500">
                                    {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}
                                </div>
                                @if($isToday)
                                <div style="font-size:9px;color:#1B4FA8;letter-spacing:1px;text-transform:uppercase;margin-top:2px">
                                    Today
                                </div>
                                @endif
                            </td>

                            {{-- Day --}}
                            <td style="font-size:12px;color:#7A8A9A">
                                {{ \Carbon\Carbon::parse($session->session_date)->format('l') }}
                            </td>

                            {{-- Time --}}
                            <td>
                                @if($session->start_time && $session->end_time)
                                <span style="font-size:12px;color:#1A2A4A;font-family:monospace">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                    <span style="color:#AAB8C8"> → </span>
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </span>
                                @else
                                <span style="color:#AAB8C8">—</span>
                                @endif
                            </td>

                            {{-- Duration --}}
                            <td style="font-size:12px;color:#7A8A9A">
                                {{ $instance->session_duration }} hr
                            </td>

                            {{-- Status --}}
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">

                                    {{-- Status badge --}}
                                    <span class="status-badge {{ $eClass }}">{{ $enrollment->status }}</span>

                                    {{-- Postpone button -- only if Active and no outstanding --}}
                                    @if($enrollment->status === 'Active')
                                    <button onclick="openPostponeModal({{ $enrollment->enrollment_id }}, '{{ addslashes($enrollment->student?->full_name) }}')"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(245,145,30,0.25);background:transparent;color:#C47010;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s"
                                        onmouseover="this.style.background='rgba(245,145,30,0.07)'"
                                        onmouseout="this.style.background='transparent'">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Postpone
                                    </button>
                                    @endif

                                    {{-- Resume button -- if Postponed --}}
                                    @if($enrollment->status === 'Postponed')
                                    <form method="POST" action="{{ route('student-care.postponed.resume', $enrollment->activePostponement?->postponement_id) }}" style="display:inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(5,150,105,0.25);background:transparent;color:#059669;cursor:pointer;font-family:'DM Sans',sans-serif">
                                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                            Resume
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                
                </table>
            </div>
        </div>
        @endif

    @endif
</div>

</div>

<script>
function showTab(tab) {
    document.getElementById('studentsTab').style.display   = 'none';
    document.getElementById('attendanceTab').style.display = 'none';
    document.getElementById('scheduleTab').style.display   = 'none';
    document.getElementById(tab + 'Tab').style.display     = 'block';
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function openAttendance(sessionId) {
    document.getElementById('session_id').value = sessionId;
    document.getElementById('attendanceTab').style.display = 'block';
}
</script>
{{-- Postpone Modal --}}
<div id="postponeModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:460px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18)">
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)"></div>

        <div style="padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.07)">
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px">Student Care</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:#1B4FA8">Postpone Student</div>
            <div style="font-size:12px;color:#7A8A9A;margin-top:3px" id="postponeStudentName">—</div>
        </div>

        <form id="postponeForm" method="POST">
            @csrf
            <div style="padding:18px 22px">

                {{-- Eligibility Warning --}}
                <div style="background:rgba(245,145,30,0.04);border:1px solid rgba(245,145,30,0.15);border-radius:4px;padding:10px 14px;font-size:11px;color:#C47010;margin-bottom:16px;line-height:1.5">
                    ⚠ Postponement allowed only for students with <strong>100% payment completed</strong> and no outstanding balance. Max duration: <strong>3 months</strong>.
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Start Date *</label>
                        <input type="date" name="start_date" id="postponeStart"
                            style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;width:100%"
                            value="{{ now()->toDateString() }}"
                            onchange="updateMaxReturn()" required>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Expected Return *</label>
                        <input type="date" name="expected_return_date" id="postponeReturn"
                            style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;width:100%"
                            onchange="checkDuration()" required>
                    </div>
                </div>

                <div id="durationWarning" style="display:none;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);border-radius:4px;padding:8px 12px;font-size:11px;color:#DC2626;margin-bottom:12px">
                    ⚠ Duration exceeds 3 months maximum. Enrollment will automatically expire.
                </div>

                <div style="display:flex;flex-direction:column;gap:5px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Reason (optional)</label>
                    <textarea name="reason" rows="3"
                        style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;resize:none;box-sizing:border-box;width:100%"
                        placeholder="Travel, health, work..."></textarea>
                </div>

            </div>

            <div style="padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="closePostponeModal()"
                    style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 22px;background:#C47010;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Confirm Postpone
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPostponeModal(enrollmentId, studentName) {
    document.getElementById('postponeStudentName').textContent = studentName;
    document.getElementById('postponeForm').action = `/student-care/enrollments/${enrollmentId}/postpone`;
    // Set min return date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('postponeReturn').min = tomorrow.toISOString().split('T')[0];
    updateMaxReturn();
    document.getElementById('postponeModal').style.display = 'flex';
}

function closePostponeModal() {
    document.getElementById('postponeModal').style.display = 'none';
}

function updateMaxReturn() {
    const start = document.getElementById('postponeStart').value;
    if (!start) return;
    const maxDate = new Date(start);
    maxDate.setMonth(maxDate.getMonth() + 3);
    // No hard max — just warn
    document.getElementById('postponeReturn').min = start;
    checkDuration();
}

function checkDuration() {
    const start  = new Date(document.getElementById('postponeStart').value);
    const ret    = new Date(document.getElementById('postponeReturn').value);
    if (!start || !ret) return;
    const diffDays = (ret - start) / (1000 * 60 * 60 * 24);
    document.getElementById('durationWarning').style.display = diffDays > 90 ? 'block' : 'none';
}

document.getElementById('postponeModal').addEventListener('click', function(e) {
    if (e.target === this) closePostponeModal();
});
</script>
@endsection