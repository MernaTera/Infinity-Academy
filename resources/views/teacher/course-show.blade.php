@extends('teacher.layouts.app')
@section('title', $instance->courseTemplate->name ?? 'Course')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.cs-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#059669;margin:0;line-height:1}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(5,150,105,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#059669;color:#059669;text-decoration:none}

/* Overview */
.overview-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;padding:22px 26px;margin-bottom:22px;position:relative;overflow:hidden}
.overview-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.ov-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:18px}
.ov-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px}
.ov-value{font-size:13px;color:#1A2A4A;font-weight:500}

/* Today Banner */
.today-banner{background:linear-gradient(135deg,#059669 0%,#10B981 100%);border-radius:8px;padding:16px 22px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.btn-take-att{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#fff;border:none;border-radius:4px;color:#059669;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;text-decoration:none;cursor:pointer;transition:all 0.2s}
.btn-take-att:hover{background:rgba(255,255,255,0.9);text-decoration:none;color:#059669}

/* Table */
.tbl-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden}
.tbl{width:100%;border-collapse:collapse;min-width:700px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(5,150,105,0.02);border-bottom:1px solid rgba(5,150,105,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(5,150,105,0.04);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(5,150,105,0.02)}
.tbl td{padding:13px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;white-space:nowrap}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-restricted{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}
.badge-exceeded{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}
.badge-postponed{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.15)}
.badge-completed{color:#7A8A9A;background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.12)}

/* Tabs */
.tab-nav{display:flex;gap:2px;margin-bottom:20px;border-bottom:1px solid rgba(5,150,105,0.08)}
.tab-btn{padding:10px 20px;font-size:10px;letter-spacing:2px;text-transform:uppercase;background:transparent;border:none;cursor:pointer;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-weight:500;position:relative;transition:color 0.2s;border-radius:4px 4px 0 0}
.tab-btn::after{content:'';position:absolute;bottom:-1px;left:0;right:0;height:2px;background:#059669;transform:scaleX(0);transition:transform 0.3s cubic-bezier(0.16,1,0.3,1)}
.tab-btn:hover{color:#059669}
.tab-btn.active{color:#059669}
.tab-btn.active::after{transform:scaleX(1)}

/* Progress */
.prog-bar{background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;margin:8px 0 3px}
.prog-fill{height:5px;border-radius:3px;background:linear-gradient(90deg,#059669,#10B981)}

/* Attendance tab styles */
.att-session-card{background:#fff;border:1px solid rgba(5,150,105,0.08);border-radius:6px;margin-bottom:12px;overflow:hidden;transition:box-shadow 0.2s;}
.att-session-card:hover{box-shadow:0 4px 16px rgba(5,150,105,0.08);}
.att-session-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;cursor:pointer;gap:12px;}
.att-session-header:hover{background:rgba(5,150,105,0.02);}
.att-session-num{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#AAB8C8;letter-spacing:1px;line-height:1;min-width:32px;}
.att-session-info{flex:1;}
.att-session-date{font-size:13px;font-weight:500;color:#1A2A4A;}
.att-session-time{font-size:10px;color:#AAB8C8;margin-top:2px;font-family:monospace;}
.att-session-body{padding:0 18px 14px;display:none;}
.att-session-body.open{display:block;}

/* Mini attendance row */
.mini-att-row{display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(5,150,105,0.04);}
.mini-att-row:last-child{border-bottom:none;}
.mini-avatar{width:28px;height:28px;border-radius:50%;background:rgba(5,150,105,0.08);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:12px;color:#059669;flex-shrink:0;}
.mini-name{font-size:12px;font-weight:500;color:#1A2A4A;flex:1;}
.att-pill{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 10px;border-radius:3px;font-weight:500;}
.pill-present{background:rgba(5,150,105,0.08);color:#059669;border:1px solid rgba(5,150,105,0.15);}
.pill-absent{background:rgba(122,138,154,0.06);color:#7A8A9A;border:1px solid rgba(122,138,154,0.12);}
.pill-restricted{background:rgba(220,38,38,0.06);color:#DC2626;border:1px solid rgba(220,38,38,0.12);}
.pill-none{background:rgba(245,145,30,0.06);color:#C47010;border:1px solid rgba(245,145,30,0.15);}

/* Window badge */
.window-open{display:inline-flex;align-items:center;gap:5px;font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);padding:3px 10px;border-radius:3px;}
.window-open::before{content:'';width:5px;height:5px;border-radius:50%;background:#059669;animation:pulse 1.5s infinite;}
.window-closed{font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#7A8A9A;}
.window-future{font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#C47010;}

@keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.3;}}

/* Attendance summary bar */
.att-summary{display:flex;gap:6px;align-items:center;flex-wrap:wrap;}
.att-mini-bar{display:flex;gap:3px;align-items:center;}
.att-dot{width:8px;height:8px;border-radius:50%;}

.take-att-link{display:inline-flex;align-items:center;gap:5px;padding:6px 14px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(5,150,105,0.25);color:#059669;text-decoration:none;background:transparent;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.take-att-link:hover{background:rgba(5,150,105,0.07);text-decoration:none;}

@media(max-width:768px){.cs-page{padding:18px 14px}}
</style>

<div class="cs-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Course Detail</div>
            <h1 class="page-title">{{ $instance->courseTemplate?->name ?? '—' }}</h1>
            @if($instance->level)
            <div style="font-size:12px;color:#7A8A9A;margin-top:4px">
                {{ $instance->level->name }}
                @if($instance->sublevel) · {{ $instance->sublevel->name }} @endif
            </div>
            @endif
        </div>
        <a href="{{ route('teacher.courses') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    {{-- Today's Session Banner --}}
    @if($todaySession)
    <div class="today-banner">
        <div>
            <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.7);margin-bottom:4px">Today's Session #{{ $todaySession->session_number }}</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#fff;letter-spacing:2px;line-height:1;">
                {{ \Carbon\Carbon::parse($todaySession->start_time)->format('H:i') }}
                → {{ \Carbon\Carbon::parse($todaySession->end_time)->format('H:i') }}
            </div>
            <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-top:3px">Window: 20 minutes from session start</div>
        </div>
        <a href="{{ route('teacher.attendance.show', $todaySession->course_session_id) }}" class="btn-take-att">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Take Attendance
        </a>
    </div>
    @endif

    {{-- Overview --}}
    <div class="overview-card">
        <div class="ov-grid">
            <div>
                <div class="ov-label">Status</div>
                <div class="ov-value">
                    <span class="badge {{ $instance->status === 'Active' ? 'badge-active' : ($instance->status === 'Completed' ? 'badge-completed' : 'badge-postponed') }}">
                        {{ $instance->status }}
                    </span>
                </div>
            </div>
            <div>
                <div class="ov-label">Type</div>
                <div class="ov-value">{{ $instance->type }}</div>
            </div>
            <div>
                <div class="ov-label">Mode</div>
                <div class="ov-value">{{ $instance->delivery_mood }}</div>
            </div>
            <div>
                <div class="ov-label">Room</div>
                <div class="ov-value">{{ $instance->room?->name ?? '—' }}</div>
            </div>
            <div>
                <div class="ov-label">Sessions</div>
                <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#059669;letter-spacing:1px;line-height:1">
                    {{ $completedSessions }}/{{ $totalSessions }}
                </div>
                <div class="prog-bar" style="max-width:80px">
                    <div class="prog-fill" style="width:{{ $totalSessions > 0 ? round($completedSessions/$totalSessions*100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="ov-label">Duration</div>
                <div class="ov-value">{{ $instance->total_hours }}h total</div>
                <div style="font-size:10px;color:#AAB8C8;margin-top:2px">{{ $instance->session_duration }}h / session</div>
            </div>
            <div>
                <div class="ov-label">Date Range</div>
                <div class="ov-value">{{ \Carbon\Carbon::parse($instance->start_date)->format('d M Y') }}</div>
                <div style="font-size:10px;color:#AAB8C8;margin-top:2px">→ {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}</div>
            </div>
            <div>
                <div class="ov-label">Patch</div>
                <div class="ov-value">{{ $instance->patch?->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tab-nav">
        <button class="tab-btn active" onclick="showTab('studentsTab', this)">
            Students ({{ $instance->enrollments->count() }})
        </button>
        <button class="tab-btn" onclick="showTab('attendanceTab', this)">
            Attendance ({{ $totalSessions }})
        </button>
        <button class="tab-btn" onclick="showTab('sessionsTab', this)">
            Schedule ({{ $totalSessions }})
        </button>
    </div>

    {{-- ══ STUDENTS TAB ══ --}}
    <div id="studentsTab">
        <div class="tbl-card">
            <div style="overflow-x:auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Phone</th>
                            <th>Test Score</th>
                            <th>Attended</th>
                            <th>Absent</th>
                            @if($instance->type === 'Group')
                            <th>Sessions Left</th>
                            @else
                            <th>Hours Left</th>
                            @endif
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($instance->enrollments as $i => $enrollment)
                        @php
                            $attended = $enrollment->attendances->where('status','Present')->count();
                            $absent   = $enrollment->attendances->where('status','Absent')->count();
                            $remaining = $totalSessions - ($attended + $absent);
                            $isRestricted = $enrollment->status === 'Restricted';
                            $isExceeded   = $instance->type === 'Group' && $absent > 2;
                            $statusBadge  = $isRestricted ? 'badge-restricted' : ($isExceeded ? 'badge-exceeded' : 'badge-active');
                            $statusLabel  = $isRestricted ? 'Restricted' : ($isExceeded ? 'Exceeded Limit' : $enrollment->status);
                        @endphp
                        <tr>
                            <td style="color:#AAB8C8;font-size:11px">{{ $i + 1 }}</td>
                            <td><div style="font-weight:500;color:#1A2A4A">{{ $enrollment->student?->full_name ?? '—' }}</div></td>
                            <td style="font-size:11px;color:#7A8A9A;font-family:monospace">{{ $enrollment->student?->phones?->first()?->phone_number ?? '—' }}</td>
                            <td>
                                @if($enrollment->placementTest)
                                <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;line-height:1">{{ $enrollment->placementTest->score }}</div>
                                @else<span style="color:#AAB8C8;font-size:11px">—</span>@endif
                            </td>
                            <td><span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#059669;letter-spacing:1px">{{ $attended }}</span></td>
                            <td><span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:{{ $absent > 2 ? '#DC2626' : ($absent > 0 ? '#C47010' : '#AAB8C8') }};letter-spacing:1px">{{ $absent }}</span></td>
                            <td>
                                @if($instance->type === 'Group')
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1A2A4A;letter-spacing:1px">{{ max(0,$remaining) }}</span>
                                @else
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1A2A4A;letter-spacing:1px">{{ $enrollment->hours_remaining ?? '—' }}</span><span style="font-size:9px;color:#AAB8C8">h</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:#AAB8C8;font-size:12px">No students enrolled</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══ ATTENDANCE TAB ══ --}}
    <div id="attendanceTab" style="display:none">
        @php
            $sessions = $instance->sessions->sortBy('session_number');
        @endphp

        @if($sessions->isEmpty())
        <div style="text-align:center;padding:60px;color:#AAB8C8;font-size:13px;">No sessions generated yet.</div>
        @else

        {{-- Summary header --}}
        <div style="background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;padding:16px 20px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:6px;">Overall Attendance</div>
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    @foreach($instance->enrollments as $enrollment)
                    @php
                        $att = $enrollment->attendances->where('course_session_id', '!=', null);
                        $p   = $att->where('status','Present')->count();
                        $a   = $att->where('status','Absent')->count();
                        $pct = ($p + $a) > 0 ? round(($p / ($p + $a)) * 100) : 0;
                    @endphp
                    <div style="font-size:12px;">
                        <span style="font-weight:500;color:#1A2A4A;">{{ $enrollment->student?->full_name ?? '—' }}</span>
                        <span style="color:{{ $pct >= 80 ? '#059669' : ($pct >= 60 ? '#C47010' : '#DC2626') }};margin-left:6px;font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;">{{ $pct }}%</span>
                        <span style="color:#AAB8C8;font-size:10px;margin-left:4px;">({{ $p }}/{{ $p + $a }})</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="font-size:11px;color:#AAB8C8;">
                {{ $completedSessions }} of {{ $totalSessions }} sessions completed
            </div>
        </div>

        {{-- Session cards --}}
        @foreach($sessions as $session)
        @php
            $isToday   = \Carbon\Carbon::parse($session->session_date)->isToday();
            $sStart    = \Carbon\Carbon::parse($session->start_time);
            $deadline  = $sStart->copy()->addMinutes(20);
            $windowOpen = $isToday && $session->status === 'Scheduled' && now()->between($sStart, $deadline);
            $isFuture  = \Carbon\Carbon::parse($session->session_date)->isFuture();

            // Attendance for this session
            $sessionAtt = $instance->enrollments->mapWithKeys(function($e) use ($session) {
                $att = $e->attendances->where('course_session_id', $session->course_session_id)->first();
                return [$e->enrollment_id => [
                    'enrollment' => $e,
                    'status'     => $att?->status,
                ]];
            });

            $presentCount = $sessionAtt->filter(fn($a) => $a['status'] === 'Present')->count();
            $absentCount  = $sessionAtt->filter(fn($a) => $a['status'] === 'Absent')->count();
            $total        = $instance->enrollments->count();
        @endphp
        <div class="att-session-card">
            <div class="att-session-header" onclick="toggleSession({{ $session->course_session_id }})">
                <div class="att-session-num">{{ $session->session_number }}</div>
                <div class="att-session-info">
                    <div class="att-session-date">
                        {{ \Carbon\Carbon::parse($session->session_date)->format('D, d M Y') }}
                        @if($isToday)<span style="color:#059669;font-size:9px;letter-spacing:1px;text-transform:uppercase;margin-left:8px;background:rgba(5,150,105,0.08);padding:2px 6px;border-radius:2px;">Today</span>@endif
                    </div>
                    <div class="att-session-time">
                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                        → {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                    </div>
                </div>

                {{-- Status --}}
                <div style="display:flex;align-items:center;gap:10px;">
                    @if($windowOpen)
                        <span class="window-open">Live</span>
                        <a href="{{ route('teacher.attendance.show', $session->course_session_id) }}" class="take-att-link"
                           onclick="event.stopPropagation()">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Take Attendance
                        </a>
                    @elseif($session->status === 'Completed')
                        <div class="att-summary">
                            <div class="att-mini-bar">
                                @for($x = 0; $x < $total; $x++)
                                <div class="att-dot" style="background:{{ $x < $presentCount ? '#059669' : '#DC2626' }};"></div>
                                @endfor
                            </div>
                            <span style="font-size:11px;color:#059669;">{{ $presentCount }}P</span>
                            <span style="font-size:11px;color:#DC2626;">{{ $absentCount }}A</span>
                        </div>
                    @elseif($isFuture)
                        <span class="window-future">Upcoming</span>
                    @else
                        <span class="window-closed">Closed</span>
                    @endif

                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"
                         id="chevron_{{ $session->course_session_id }}" style="transition:transform 0.2s;flex-shrink:0;">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>

            <div class="att-session-body" id="body_{{ $session->course_session_id }}">
                <div style="border-top:1px solid rgba(5,150,105,0.06);padding-top:12px;">
                    @if($session->status === 'Completed' || $session->status === 'Scheduled')
                        @foreach($sessionAtt as $attData)
                        @php
                            $enrollment = $attData['enrollment'];
                            $status     = $attData['status'];
                            $isRestricted = $enrollment->status === 'Restricted';
                        @endphp
                        <div class="mini-att-row">
                            <div class="mini-avatar">{{ strtoupper(substr($enrollment->student?->full_name ?? '?', 0, 1)) }}</div>
                            <div class="mini-name">{{ $enrollment->student?->full_name ?? '—' }}</div>
                            @if($isRestricted)
                            <span class="att-pill pill-restricted">🔒 Restricted</span>
                            @elseif($status === 'Present')
                            <span class="att-pill pill-present">✓ Present</span>
                            @elseif($status === 'Absent')
                            <span class="att-pill pill-absent">✕ Absent</span>
                            @else
                            <span class="att-pill pill-none">— Not marked</span>
                            @endif
                        </div>
                        @endforeach
                        @if($windowOpen)
                        <div style="margin-top:10px;padding-top:10px;border-top:1px solid rgba(5,150,105,0.06);text-align:right;">
                            <a href="{{ route('teacher.attendance.show', $session->course_session_id) }}" class="take-att-link">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Take Attendance Now
                            </a>
                        </div>
                        @endif
                    @else
                        <div style="text-align:center;padding:16px;color:#AAB8C8;font-size:12px;">
                            {{ $isFuture ? 'Session not started yet.' : 'Attendance window has closed.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    {{-- ══ SCHEDULE TAB ══ --}}
    <div id="sessionsTab" style="display:none">
        <div class="tbl-card">
            <div style="overflow-x:auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instance->sessions->sortBy('session_number') as $session)
                        @php
                            $isToday  = \Carbon\Carbon::parse($session->session_date)->isToday();
                            $sStart   = \Carbon\Carbon::parse($session->start_time);
                            $deadline = $sStart->copy()->addMinutes(20);
                            $canTake  = $isToday && $session->status === 'Scheduled' && now()->between($sStart, $deadline);
                            $sClass   = match($session->status) {
                                'Completed' => 'color:#059669',
                                'Cancelled' => 'color:#DC2626',
                                default     => $isToday ? 'color:#1B4FA8' : 'color:#AAB8C8',
                            };
                        @endphp
                        <tr style="{{ $isToday ? 'background:rgba(5,150,105,0.03)' : '' }}">
                            <td>
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;line-height:1;{{ $sClass }}">
                                    {{ $session->session_number }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size:12px;color:#1A2A4A;font-weight:500">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</div>
                                @if($isToday)<div style="font-size:9px;color:#059669;letter-spacing:1px;text-transform:uppercase;margin-top:2px">Today</div>@endif
                            </td>
                            <td style="font-size:12px;color:#7A8A9A">{{ \Carbon\Carbon::parse($session->session_date)->format('l') }}</td>
                            <td>
                                @if($session->start_time)
                                <span style="font-size:12px;color:#1A2A4A;font-family:monospace">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                    <span style="color:#AAB8C8"> → </span>
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </span>
                                @else<span style="color:#AAB8C8">—</span>@endif
                            </td>
                            <td>
                                @if($session->status === 'Completed')
                                <span class="badge badge-active">Completed</span>
                                @elseif($session->status === 'Cancelled')
                                <span class="badge badge-restricted">Cancelled</span>
                                @elseif($isToday)
                                <span class="badge" style="color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15);">Today</span>
                                @else
                                <span style="font-size:10px;color:#AAB8C8;letter-spacing:1px;text-transform:uppercase">Scheduled</span>
                                @endif
                            </td>
                            <td>
                                @if($canTake)
                                <a href="{{ route('teacher.attendance.show', $session->course_session_id) }}" class="take-att-link">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/></svg>
                                    Take Attendance
                                </a>
                                @elseif($session->status === 'Completed')
                                <span style="font-size:10px;color:#059669;letter-spacing:1px;text-transform:uppercase">✓ Done</span>
                                @else
                                <span style="font-size:10px;color:#AAB8C8">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function showTab(tabId, btn) {
    ['studentsTab','attendanceTab','sessionsTab'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    document.getElementById(tabId).style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function toggleSession(id) {
    const body    = document.getElementById('body_' + id);
    const chevron = document.getElementById('chevron_' + id);
    const isOpen  = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}
</script>
@endsection