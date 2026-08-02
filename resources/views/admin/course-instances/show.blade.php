@extends('admin.layouts.app')
@section('title', 'Course Details')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --purple:#7C3AED; --purple-l:rgba(124,58,237,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }
    .acs-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .acs-wrap { margin:0 auto; }

    /* HEADER */
    .acs-header {
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px; margin-bottom:20px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .acs-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .acs-header-inner { position:relative; z-index:1; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:16px; }
    .acs-eyebrow { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--orange); margin-bottom:6px; font-weight:600; }
    .acs-title { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:2px; color:#fff; line-height:1; margin:0; }
    .acs-meta-line { font-size:12px; color:rgba(255,255,255,0.6); margin-top:8px; display:flex; gap:14px; flex-wrap:wrap; }
    .acs-meta-line span { display:flex; align-items:center; gap:5px; }
    .acs-status-badge { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:20px; font-size:11px; font-weight:600; background:rgba(255,255,255,0.1); color:#fff; }
    .acs-status-badge::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }

    .btn-back { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:7px; color:rgba(255,255,255,0.85); font-size:10px; letter-spacing:1.5px; text-transform:uppercase; text-decoration:none; font-weight:600; transition:all 0.2s; }
    .btn-back:hover { background:rgba(255,255,255,0.14); color:#fff; text-decoration:none; }

    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin:24px 0 14px; font-weight:600; }

    /* INFO GRID */
    .info-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
    @media (max-width:900px){ .info-grid{ grid-template-columns:1fr 1fr; } }
    .info-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px 18px; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .info-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:6px; }
    .info-val { font-size:14px; font-weight:600; color:var(--text); }
    .info-val.sm { font-size:12px; }

    /* STATS ROW */
    .stats-row { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
    @media (max-width:900px){ .stats-row{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:560px){ .stats-row{ grid-template-columns:1fr 1fr; } }
    .stat { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px; position:relative; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--sc,var(--blue)); }
    .stat-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .stat-val { font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }
    .stat-val.money { font-size:20px; }

    /* PROGRESS PANEL */
    .prog-panel { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:20px; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .prog-panel-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .prog-panel-title { font-size:12px; font-weight:600; color:var(--text); }
    .prog-panel-pct { font-family:'Bebas Neue',sans-serif; font-size:22px; color:var(--green); letter-spacing:1px; }
    .prog-track { height:10px; background:var(--bg); border-radius:5px; overflow:hidden; }
    .prog-track-fill { height:100%; background:linear-gradient(90deg, var(--green), #10B981); border-radius:5px; transition:width 0.6s; }
    .prog-legend { display:flex; gap:18px; margin-top:12px; font-size:11px; color:var(--muted); flex-wrap:wrap; }
    .prog-legend span { display:flex; align-items:center; gap:6px; }
    .lg-dot { width:8px; height:8px; border-radius:50%; }

    /* TWO COL */
    .two-col { display:grid; grid-template-columns:1.15fr 0.85fr; gap:20px; align-items:start; }
    @media (max-width:1000px){ .two-col{ grid-template-columns:1fr; } }

    /* CARD */
    .card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .card-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg, rgba(27,79,168,0.02), transparent); }
    .card-title { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:2px; color:var(--text); }
    .card-count { font-size:10px; color:var(--muted); background:var(--bg); padding:3px 10px; border-radius:12px; font-weight:600; }

    /* TABLE */
    .tbl { width:100%; border-collapse:collapse; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:11px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:var(--bg); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:11px 16px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:var(--blue-l); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }
    .tbl-scroll { overflow-x:auto; max-height:520px; overflow-y:auto; }

    .std-cell { display:flex; align-items:center; gap:10px; }
    .std-avatar { width:30px; height:30px; border-radius:50%; background:var(--blue-l); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:13px; color:var(--blue); flex-shrink:0; }

    .att-badge { display:inline-block; padding:3px 9px; border-radius:12px; font-size:10px; font-weight:600; font-variant-numeric:tabular-nums; }
    .att-good { background:var(--green-l); color:var(--green-dk); }
    .att-mid { background:var(--orange-l); color:var(--orange-dk); }
    .att-bad { background:var(--red-l); color:var(--red); }
    .att-none { background:rgba(122,138,154,0.12); color:var(--muted); }

    .sess-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:600; }
    .sess-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
    .ss-completed { background:var(--green-l); color:var(--green-dk); }
    .ss-scheduled { background:var(--blue-l); color:var(--blue); }
    .ss-cancelled { background:var(--red-l); color:var(--red); }

    /* REVENUE BREAKDOWN */
    .rev-list { padding:16px 20px; }
    .rev-row { display:flex; align-items:center; justify-content:space-between; padding:11px 0; border-bottom:1px solid rgba(27,79,168,0.05); }
    .rev-row:last-child { border-bottom:none; }
    .rev-cat { display:flex; align-items:center; gap:9px; font-size:12px; color:var(--text); }
    .rev-cat-dot { width:9px; height:9px; border-radius:50%; }
    .rev-cat-amt { font-weight:700; font-variant-numeric:tabular-nums; }
    .rev-total-row { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; background:var(--bg); border-top:2px solid var(--border); }
    .rev-total-label { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; }
    .rev-total-amt { font-family:'Bebas Neue',sans-serif; font-size:22px; color:var(--green-dk); letter-spacing:1px; }

    .empty-sm { text-align:center; padding:32px 20px; color:var(--faint); font-size:12px; }

    @media (max-width:600px){ .acs-page{ padding:16px; } }
</style>

@php
    $statusBadge = match($instance->status) {
        'Active'    => 'Active',
        'Upcoming'  => 'Upcoming',
        'Completed' => 'Completed',
        'Cancelled' => 'Cancelled',
        default     => $instance->status,
    };
    $catMeta = [
        'Course'      => ['Course Deposits', '#1B4FA8'],
        'Test'        => ['Test Fees', '#7C3AED'],
        'Material'    => ['Materials', '#F5911E'],
        'Installment' => ['Installments', '#059669'],
    ];
@endphp

<div class="acs-page">
    <div class="acs-wrap">

        {{-- HEADER --}}
        <div class="acs-header">
            <div class="acs-header-inner">
                <div>
                    <div class="acs-eyebrow">Course Instance #{{ $instance->course_instance_id }}</div>
                    <h1 class="acs-title">{{ $instance->courseTemplate?->name ?? 'Course' }}</h1>
                    <div class="acs-meta-line">
                        <span>{{ $instance->level?->name }}@if($instance->sublevel) · {{ $instance->sublevel->name }}@endif</span>
                        <span>· {{ $instance->type }}</span>
                        <span>· {{ $instance->delivery_mood }}</span>
                        <span>· {{ $instance->patch?->name }}</span>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;align-items:flex-end;">
                    <a href="{{ route('admin.course-instances.index') }}" class="btn-back">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Back
                    </a>
                    <span class="acs-status-badge">{{ $statusBadge }}</span>
                </div>
            </div>
        </div>

        {{-- KEY INFO --}}
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Teacher</div>
                <div class="info-val">{{ $instance->teacher?->employee?->full_name ?? '—' }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Room</div>
                <div class="info-val">{{ $instance->room?->name ?? ($instance->delivery_mood === 'Online' ? 'Online' : '—') }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Duration</div>
                <div class="info-val sm">{{ \Carbon\Carbon::parse($instance->start_date)->format('d M Y') }} → {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Created By</div>
                <div class="info-val sm">{{ $instance->createdBy?->full_name ?? '—' }}</div>
            </div>
        </div>

        {{-- STATS --}}
        <span class="sec-label">Overview</span>
        <div class="stats-row">
            <div class="stat" style="--sc:var(--blue)">
                <div class="stat-label">Students</div>
                <div class="stat-val">{{ $activeEnrollments->count() }}<span style="font-size:13px;color:var(--faint);"> / {{ $instance->capacity }}</span></div>
            </div>
            <div class="stat" style="--sc:var(--green)">
                <div class="stat-label">Completed</div>
                <div class="stat-val">{{ $completedSessions }}</div>
            </div>
            <div class="stat" style="--sc:var(--orange)">
                <div class="stat-label">Remaining</div>
                <div class="stat-val">{{ $remainingSessions }}</div>
            </div>
            <div class="stat" style="--sc:var(--dark)">
                <div class="stat-label">Total Sessions</div>
                <div class="stat-val">{{ $totalSessions }}</div>
            </div>
            <div class="stat" style="--sc:var(--green-dk)">
                <div class="stat-label">Revenue</div>
                <div class="stat-val money">{{ number_format($revenueTotal) }} <span style="font-size:11px;">LE</span></div>
            </div>
        </div>

        {{-- PROGRESS --}}
        <span class="sec-label">Course Progress</span>
        <div class="prog-panel">
            <div class="prog-panel-head">
                <div class="prog-panel-title">{{ $completedSessions }} of {{ $totalSessions }} sessions completed</div>
                <div class="prog-panel-pct">{{ $progressPct }}%</div>
            </div>
            <div class="prog-track"><div class="prog-track-fill" style="width:{{ $progressPct }}%"></div></div>
            <div class="prog-legend">
                <span><span class="lg-dot" style="background:var(--green);"></span> {{ $completedSessions }} Completed</span>
                <span><span class="lg-dot" style="background:var(--blue);"></span> {{ $remainingSessions }} Scheduled</span>
                @if($cancelledSessions > 0)
                <span><span class="lg-dot" style="background:var(--red);"></span> {{ $cancelledSessions }} Cancelled</span>
                @endif
            </div>
        </div>

        {{-- STUDENTS + REVENUE --}}
        <div class="two-col" style="margin-top:24px;">

            {{-- ENROLLED STUDENTS + ATTENDANCE --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Enrolled Students</div>
                    <span class="card-count">{{ $activeEnrollments->count() }} students</span>
                </div>
                <div class="tbl-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th class="num">Present</th>
                                <th class="num">Absent</th>
                                <th class="num">Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeEnrollments as $enr)
                            @php
                                $att = $studentAttendance[$enr->enrollment_id] ?? ['present'=>0,'absent'=>0,'rate'=>null];
                                $rate = $att['rate'];
                                $attClass = $rate === null ? 'att-none' : ($rate >= 75 ? 'att-good' : ($rate >= 50 ? 'att-mid' : 'att-bad'));
                            @endphp
                            <tr>
                                <td>
                                    <div class="std-cell">
                                        <div class="std-avatar">{{ strtoupper(substr($enr->student?->full_name ?? '?', 0, 1)) }}</div>
                                        <span style="font-weight:600;">{{ $enr->student?->full_name ?? 'Student #'.$enr->enrollment_id }}</span>
                                    </div>
                                </td>
                                <td class="num" style="color:var(--green-dk);font-weight:600;">{{ $att['present'] }}</td>
                                <td class="num" style="color:var(--red);font-weight:600;">{{ $att['absent'] }}</td>
                                <td class="num">
                                    <span class="att-badge {{ $attClass }}">{{ $rate === null ? '—' : $rate.'%' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4"><div class="empty-sm">No students enrolled yet.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- REVENUE BREAKDOWN --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Revenue Breakdown</div>
                </div>
                <div class="rev-list">
                    @foreach($revenueByCategory as $cat => $amount)
                    @php $meta = $catMeta[$cat] ?? [$cat, '#7A8A9A']; @endphp
                    <div class="rev-row">
                        <div class="rev-cat">
                            <span class="rev-cat-dot" style="background:{{ $meta[1] }};"></span>
                            {{ $meta[0] }}
                        </div>
                        <span class="rev-cat-amt" style="color:{{ $meta[1] }};">{{ number_format($amount) }} LE</span>
                    </div>
                    @endforeach
                </div>
                <div class="rev-total-row">
                    <span class="rev-total-label">Total Collected</span>
                    <span class="rev-total-amt">{{ number_format($revenueTotal) }} LE</span>
                </div>
            </div>
        </div>

        {{-- SESSIONS --}}
        <span class="sec-label">Sessions Schedule</span>
        <div class="card">
            <div class="card-head">
                <div class="card-title">All Sessions</div>
                <span class="card-count">{{ $instance->sessions->count() }} sessions</span>
            </div>
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($instance->sessions as $session)
                        @php
                            $ssBadge = match($session->status) {
                                'Completed' => ['ss-completed','Completed'],
                                'Scheduled' => ['ss-scheduled','Scheduled'],
                                'Cancelled' => ['ss-cancelled','Cancelled'],
                                default     => ['ss-scheduled', $session->status],
                            };
                        @endphp
                        <tr>
                            <td style="color:var(--faint);font-weight:600;">{{ $session->session_number }}</td>
                            <td style="font-weight:600;">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</td>
                            <td style="color:var(--muted);">{{ \Carbon\Carbon::parse($session->session_date)->format('D') }}</td>
                            <td style="color:var(--muted);font-variant-numeric:tabular-nums;">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} → {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                            </td>
                            <td><span class="sess-badge {{ $ssBadge[0] }}">{{ $ssBadge[1] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-sm">No sessions generated yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection