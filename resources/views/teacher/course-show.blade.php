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

/* Overview Card */
.overview-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;padding:22px 26px;margin-bottom:22px;position:relative;overflow:hidden}
.overview-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.ov-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:18px}
.ov-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:5px}
.ov-value{font-size:13px;color:#1A2A4A;font-weight:500}

/* Today Attendance Banner */
.today-banner{background:linear-gradient(135deg,#059669 0%,#10B981 100%);border-radius:8px;padding:16px 22px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.today-text{color:#fff;font-size:13px}
.today-time{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#fff;letter-spacing:2px}
.btn-take-att{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#fff;border:none;border-radius:4px;color:#059669;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;text-decoration:none;cursor:pointer;transition:all 0.2s}
.btn-take-att:hover{background:rgba(255,255,255,0.9);text-decoration:none;color:#059669}

/* Student Table */
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

.report-status{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:3px}
.rs-pending{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.15)}
.rs-submitted{color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15)}
.rs-approved{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.rs-rejected{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}

/* Sessions Tab */
.tab-nav{display:flex;gap:2px;margin-bottom:20px;border-bottom:1px solid rgba(5,150,105,0.08)}
.tab-btn{padding:10px 20px;font-size:10px;letter-spacing:2px;text-transform:uppercase;background:transparent;border:none;cursor:pointer;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-weight:500;position:relative;transition:color 0.2s;border-radius:4px 4px 0 0}
.tab-btn::after{content:'';position:absolute;bottom:-1px;left:0;right:0;height:2px;background:#059669;transform:scaleX(0);transition:transform 0.3s cubic-bezier(0.16,1,0.3,1)}
.tab-btn:hover{color:#059669}
.tab-btn.active{color:#059669}
.tab-btn.active::after{transform:scaleX(1)}

/* Progress */
.prog-bar{background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;margin:8px 0 3px}
.prog-fill{height:5px;border-radius:3px;background:linear-gradient(90deg,#059669,#10B981)}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:14px;display:block}

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

    {{-- Today's Attendance Banner --}}
    @if($todaySession)
    <div class="today-banner">
        <div>
            <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.7);margin-bottom:4px">
                Today's Session
            </div>
            <div class="today-time">
                {{ \Carbon\Carbon::parse($todaySession->start_time)->format('H:i') }}
                → {{ \Carbon\Carbon::parse($todaySession->end_time)->format('H:i') }}
            </div>
            <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-top:3px">
                Attendance window open — closes 20 minutes after start
            </div>
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
                    <span class="badge {{ $instance->status === 'Active' ? 'badge-active' : 'badge-postponed' }}">
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
        <button class="tab-btn" onclick="showTab('sessionsTab', this)">
            Sessions ({{ $totalSessions }})
        </button>
    </div>

    {{-- Students Tab --}}
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
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($instance->enrollments as $i => $enrollment)
                        @php
                            $attended  = $enrollment->attendances->where('status','Present')->count();
                            $absent    = $enrollment->attendances->where('status','Absent')->count();
                            $remaining = $totalSessions - ($attended + $absent);

                            $isRestricted = $enrollment->status === 'Restricted';
                            $isExceeded   = $instance->type === 'Group' && $absent > 2;

                            $statusBadge = match(true) {
                                $isRestricted => 'badge-restricted',
                                $isExceeded   => 'badge-exceeded',
                                default       => 'badge-active',
                            };
                            $statusLabel = match(true) {
                                $isRestricted => 'Restricted',
                                $isExceeded   => 'Exceeded Limit',
                                default       => $enrollment->status,
                            };
                        @endphp
                        <tr>
                            <td style="color:#AAB8C8;font-size:11px">{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight:500;color:#1A2A4A">
                                    {{ $enrollment->student?->full_name ?? '—' }}
                                </div>
                            </td>
                            <td style="font-size:11px;color:#7A8A9A;font-family:monospace">
                                {{ $enrollment->student?->phones?->first()?->phone_number ?? '—' }}
                            </td>
                            <td>
                                @if($enrollment->placementTest)
                                <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;line-height:1">
                                    {{ $enrollment->placementTest->score }}
                                </div>
                                <div style="font-size:9px;color:#AAB8C8;margin-top:1px">
                                    {{ $enrollment->placementTest->level_assigned ?? '—' }}
                                </div>
                                @else
                                <span style="color:#AAB8C8;font-size:11px">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#059669;letter-spacing:1px">
                                    {{ $attended }}
                                </span>
                            </td>
                            <td>
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:{{ $absent > 2 ? '#DC2626' : ($absent > 0 ? '#C47010' : '#AAB8C8') }};letter-spacing:1px">
                                    {{ $absent }}
                                </span>
                            </td>
                            <td>
                                @if($instance->type === 'Group')
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1A2A4A;letter-spacing:1px">
                                    {{ max(0, $remaining) }}
                                </span>
                                @else
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1A2A4A;letter-spacing:1px">
                                    {{ $enrollment->hours_remaining ?? '—' }}
                                </span>
                                <span style="font-size:9px;color:#AAB8C8">h</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                @if($instance->status === 'Completed')
                                    <span class="report-status rs-pending">Pending</span>
                                @else
                                    <span style="font-size:10px;color:#AAB8C8">In Progress</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:#AAB8C8;font-size:12px">
                                No students enrolled
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sessions Tab --}}
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
                        @foreach($instance->sessions as $session)
                        @php
                            $isToday   = \Carbon\Carbon::parse($session->session_date)->isToday();
                            $sStart    = \Carbon\Carbon::parse($session->start_time);
                            $deadline  = $sStart->copy()->addMinutes(20);
                            $canTake   = $isToday && $session->status === 'Scheduled' && now()->between($sStart, $deadline);
                            $sClass    = match($session->status) {
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
                                <div style="font-size:12px;color:#1A2A4A;font-weight:500">
                                    {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}
                                </div>
                                @if($isToday)
                                <div style="font-size:9px;color:#059669;letter-spacing:1px;text-transform:uppercase;margin-top:2px">Today</div>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#7A8A9A">
                                {{ \Carbon\Carbon::parse($session->session_date)->format('l') }}
                            </td>
                            <td>
                                @if($session->start_time)
                                <span style="font-size:12px;color:#1A2A4A;font-family:monospace">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                    <span style="color:#AAB8C8"> → </span>
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </span>
                                @else
                                <span style="color:#AAB8C8">—</span>
                                @endif
                            </td>
                            <td>
                                @if($session->status === 'Completed')
                                    <span class="badge badge-active">Completed</span>
                                @elseif($session->status === 'Cancelled')
                                    <span class="badge badge-restricted">Cancelled</span>
                                @elseif($isToday)
                                    <span class="badge badge-active" style="color:#1B4FA8;background:rgba(27,79,168,0.07);border-color:rgba(27,79,168,0.15)">Today</span>
                                @else
                                    <span style="font-size:10px;color:#AAB8C8;letter-spacing:1px;text-transform:uppercase">Scheduled</span>
                                @endif
                            </td>
                            <td>
                                @if($canTake)
                                <a href="{{ route('teacher.attendance.show', $session->course_session_id) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(5,150,105,0.25);color:#059669;text-decoration:none;background:transparent;transition:all 0.2s"
                                   onmouseover="this.style.background='rgba(5,150,105,0.07)'"
                                   onmouseout="this.style.background='transparent'">
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
    document.getElementById('studentsTab').style.display  = 'none';
    document.getElementById('sessionsTab').style.display  = 'none';
    document.getElementById(tabId).style.display          = 'block';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>
@endsection