@extends('admin.layouts.app')
@section('title', 'Course Details')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.10);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.12);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.10);
        --purple:#7C3AED; --purple-l:rgba(124,58,237,0.10);
        --red:#DC2626; --red-l:rgba(220,38,38,0.08);
        --dark:#16233F; --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:rgba(255,255,255,0.5); --border:rgba(27,79,168,0.08);
        --glass-bd:rgba(255,255,255,0.6); --glass-sh:0 8px 30px rgba(23,45,90,0.07);
    }
    * { box-sizing:border-box; }
    .acs-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }
    .acs-wrap { margin:0 auto; }

    .acs-header {
        background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%);
        border:1px solid var(--glass-bd); border-radius:20px; padding:24px 30px; margin-bottom:20px; position:relative; overflow:hidden;
        box-shadow:var(--glass-sh);
    }
    /* .acs-header::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:0; background:linear-gradient(90deg,transparent,var(--blue),transparent); } */
    .acs-header-inner { position:relative; z-index:1; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:16px; }
    .acs-eyebrow { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:6px; font-weight:600; }
    .acs-title { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:2px; color:var(--text); line-height:1; margin:0; }
    .acs-meta-line { font-size:12px; color:var(--muted); margin-top:8px; display:flex; gap:14px; flex-wrap:wrap; }
    .acs-meta-line span { display:flex; align-items:center; gap:5px; }
    .acs-status-badge { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:20px; font-size:11px; font-weight:600; background:rgba(27,79,168,0.10); color:var(--blue); }
    .acs-status-badge::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }

    .btn-back { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; background:rgba(255,255,255,0.6); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--glass-bd); border-radius:11px; color:var(--muted); font-size:10px; letter-spacing:1.5px; text-transform:uppercase; text-decoration:none; font-weight:600; transition:all 0.2s; }
    .btn-back:hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }

    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--muted); margin:24px 0 14px; font-weight:600; }

    .info-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
    @media (max-width:900px){ .info-grid{ grid-template-columns:1fr 1fr; } }
    .info-card { background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:16px; padding:16px 18px; box-shadow:var(--glass-sh); }
    .info-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:6px; }
    .info-val { font-size:14px; font-weight:600; color:var(--text); }
    .info-val.sm { font-size:12px; }

    .stats-row { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
    @media (max-width:900px){ .stats-row{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:560px){ .stats-row{ grid-template-columns:1fr 1fr; } }
    .stat { background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:16px; padding:16px; position:relative; overflow:hidden; box-shadow:var(--glass-sh); }
    .stat::before { display:none; }
    .stat-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .stat-val { font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }
    .stat-val.money { font-size:20px; }

    .prog-panel { background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:20px; padding:20px; box-shadow:var(--glass-sh); }
    .prog-panel-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .prog-panel-title { font-size:12px; font-weight:600; color:var(--text); }
    .prog-panel-pct { font-family:'Bebas Neue',sans-serif; font-size:22px; color:var(--blue); letter-spacing:1px; }
    .prog-track { height:10px; background:rgba(27,79,168,0.10); border-radius:5px; overflow:hidden; }
    .prog-track-fill { height:100%; background:linear-gradient(90deg, var(--blue), var(--blue-2)); border-radius:5px; transition:width 0.6s; }
    .prog-legend { display:flex; gap:18px; margin-top:12px; font-size:11px; color:var(--muted); flex-wrap:wrap; }
    .prog-legend span { display:flex; align-items:center; gap:6px; }
    .lg-dot { width:8px; height:8px; border-radius:50%; }

    .two-col { display:grid; grid-template-columns:1.15fr 0.85fr; gap:20px; align-items:start; }
    @media (max-width:1000px){ .two-col{ grid-template-columns:1fr; } }

    .card { background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:20px; overflow:hidden; box-shadow:var(--glass-sh); }
    .card-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:rgba(255,255,255,0.35); }
    .card-title { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:2px; color:var(--text); }
    .card-count { font-size:10px; color:var(--blue); background:rgba(27,79,168,0.10); padding:3px 10px; border-radius:12px; font-weight:600; }

    .tbl { width:100%; border-collapse:collapse; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:11px 16px; text-align:left; border-bottom:1px solid var(--glass-bd); font-weight:600; background:rgba(255,255,255,0.4); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:11px 16px; border-bottom:1px solid rgba(27,79,168,0.06); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:rgba(27,79,168,0.04); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }
    .tbl-scroll { overflow-x:auto; max-height:520px; overflow-y:auto; }

    .std-cell { display:flex; align-items:center; gap:10px; }
    .std-avatar { width:30px; height:30px; border-radius:10px; background:var(--blue-l); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:13px; color:var(--blue); flex-shrink:0; }

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

    .rev-list { padding:16px 20px; }
    .rev-row { display:flex; align-items:center; justify-content:space-between; padding:11px 0; border-bottom:1px solid rgba(27,79,168,0.06); }
    .rev-row:last-child { border-bottom:none; }
    .rev-cat { display:flex; align-items:center; gap:9px; font-size:12px; color:var(--text); }
    .rev-cat-dot { width:9px; height:9px; border-radius:50%; }
    .rev-cat-amt { font-weight:700; font-variant-numeric:tabular-nums; }
    .rev-total-row { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; background:rgba(255,255,255,0.4); border-top:1px solid var(--glass-bd); }
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
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="acs-wrap">

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

        <div class="two-col" style="margin-top:24px;">

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

            <div class="card">
                <div class="card-head">
                    <div class="card-title">Revenue Breakdown</div>
                </div>
                <div class="rev-list">
                    @foreach($revenueByCategory as $cat => $amount)
                    @php $meta = $catMeta[$cat] ?? [$cat, '#5A6A85']; @endphp
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