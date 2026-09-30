@php
    $user = auth()->user();
    $__layout = $user?->isAdmin() ? 'admin.layouts.app'
        : ($user?->isSC() ? 'student-care.layouts.app'
        : 'layouts.leads');
@endphp
@extends($__layout)
@section('title', 'Level Packages')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        /* --blue:#F5911E; --blue-dk:#C47010; --blue-l:rgba(245,145,30,0.07); */
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        /* --blue-l:rgba(124,58,237,0.07); */
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:rgba(255,255,255,0.72); --border:rgba(255,255,255,0.6);
        --line:rgba(27,79,168,0.08); --glass-sh:0 12px 34px -14px rgba(23,45,90,0.2);
    }
    * { box-sizing:border-box; }

    .pk-page {
        min-height:100vh; padding:40px 34px 52px; color:var(--text); font-family:'DM Sans',sans-serif;
        background:#F8F6F2;
    }
    .pk-wrap { max-width:1300px; margin:0 auto; }
   .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .pk-header { margin:0 auto 4px; }
    .pk-header-inner { position:relative; z-index:1; display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; }
    .pk-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:7px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .pk-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--blue); box-shadow:0 0 8px var(--blue); }
    .pk-title { font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0; }
    .pk-sub { font-size:12px; color:var(--muted); margin-top:6px; letter-spacing:0.3px; }
    .btn-new { display:inline-flex; align-items:center; gap:7px; padding:12px 22px; background:linear-gradient(135deg,var(--blue),var(--blue-2)); border:none; border-radius:12px; color:#fff; font-size:11px; letter-spacing:1px; text-transform:uppercase; text-decoration:none; font-weight:700; transition:box-shadow 0.2s, filter 0.2s; box-shadow:0 10px 24px -6px rgba(58, 121, 237, 0.5); margin-bottom:10px; }
    .btn-new:hover { filter:brightness(1.06); color:#fff; text-decoration:none; box-shadow:0 14px 30px -6px rgba(58, 85, 237, 0.6); }

    .sec-label { display:flex; align-items:center; gap:10px; font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--text); margin:26px 0 14px; font-weight:700; }
    .sec-label::before { content:''; width:20px; height:3px; border-radius:3px; background:linear-gradient(90deg, var(--blue), var(--blue)); flex-shrink:0; }

    .pk-stats { display:grid; grid-template-columns:repeat(6,1fr); gap:14px; }
    @media (max-width:1100px){ .pk-stats{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:560px){ .pk-stats{ grid-template-columns:1fr 1fr; } }
    .stat { background:var(--card); -webkit-backdrop-filter:blur(20px) saturate(150%); backdrop-filter:blur(20px) saturate(150%); border:1px solid var(--border); border-radius:18px; padding:18px 20px; position:relative; overflow:hidden; box-shadow:var(--glass-sh); transition:transform 0.2s, box-shadow 0.2s; }
    .stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--sc,var(--blue)); }
    .stat:hover { transform:translateY(-3px); box-shadow:0 18px 42px -14px rgba(23,45,90,0.28); }
    .stat-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .stat-val { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }

    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; }
    .filter-pill { padding:10px 20px; border-radius:12px; font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:all 0.2s; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--border); color:var(--muted); text-decoration:none; box-shadow:var(--glass-sh); }
    .filter-pill:hover { color:var(--blue); text-decoration:none; }
    .filter-pill.active { background:linear-gradient(120deg,var(--blue),var(--blue-2)); border-color:transparent; color:#fff; box-shadow:0 6px 16px rgba(27,79,168,0.28); }

    .tbl-card { background:rgba(255,255,255,0.72); border:1px solid var(--border); border-radius:20px; overflow:hidden; box-shadow:var(--glass-sh); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:950px; }
    .tbl thead th { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); padding:15px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:700; background:rgba(255,255,255,0.55); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.06); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:rgba(124,58,237,0.05); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }

    .std-cell { display:flex; align-items:center; gap:11px; }
    .std-avatar { width:36px; height:36px; border-radius:11px; background:rgba(124,58,237,0.12); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:15px; color:var(--blue); flex-shrink:0; }
    .std-name { font-weight:700; color:var(--text); }
    .std-course { font-size:10px; color:var(--muted); margin-top:1px; }

    .pkg-name-chip { display:inline-block; padding:4px 11px; border-radius:8px; font-size:10px; font-weight:600; background:var(--blue-l); color:var(--blue-dk); border:1px solid rgba(124,58,237,0.18); }

    .units-cell { min-width:150px; }
    .units-top { display:flex; align-items:baseline; gap:5px; margin-bottom:5px; justify-content:flex-end; }
    .units-remain { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:0.5px; }
    .units-total { font-size:10px; color:var(--faint); }
    .units-bar { height:6px; background:rgba(124,58,237,0.1); border-radius:3px; overflow:hidden; }
    .units-fill { height:100%; border-radius:3px; }

    .state-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:10px; font-weight:600; white-space:nowrap; }
    .state-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .st-active { background:var(--green-l); color:var(--green-dk); }
    .st-available { background:var(--blue-l); color:var(--blue-dk); }
    .st-done { background:var(--blue-l); color:var(--blue); }
    .st-postponed { background:rgba(100,116,139,0.14); color:#475569; }

    .btn-enroll { display:inline-flex; align-items:center; gap:5px; padding:8px 14px; border-radius:9px; font-size:10px; font-weight:600; letter-spacing:0.3px; text-decoration:none; background:var(--blue-l); color:var(--blue-dk); border:1px solid rgba(124,58,237,0.3); transition:all 0.2s; white-space:nowrap; }
    .btn-enroll:hover { background:var(--blue); color:#fff; text-decoration:none; }

    .tbl-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .tbl-empty svg { opacity:0.35; margin-bottom:12px; }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-bottom:4px; }

    @media (max-width:600px){ .pk-page{ padding:20px 14px 36px; } .pk-title{ font-size:34px; } }
</style>

<div class="pk-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="pk-wrap">

        
        <div class="pk-header">
            <div class="pk-header-inner">
                <div>
                    <div class="pk-eyebrow">Level Program</div>
                    <h1 class="pk-title">Level Packages</h1>
                    <div class="pk-sub">Package progress & remaining prepaid levels for group students</div>
                </div>
                @if($canAct)
                <a href="{{ route('leads.index') }}" class="btn-new">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Registration
                </a>
                @endif
            </div>
        </div>

        
        <div class="pk-stats">
            <div class="stat" style="--sc:var(--dark)">
                <div class="stat-label">Total Packages</div>
                <div class="stat-val">{{ $stats['total'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--green)">
                <div class="stat-label">Active</div>
                <div class="stat-val">{{ $stats['active'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--blue)">
                <div class="stat-label">Ready to Continue</div>
                <div class="stat-val">{{ $stats['available'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--blue)">
                <div class="stat-label">Completed</div>
                <div class="stat-val">{{ $stats['done'] }}</div>
            </div>
            <div class="stat" style="--sc:#64748B">
                <div class="stat-label">Postponed</div>
                <div class="stat-val">{{ $stats['postponed'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--blue-dk)">
                <div class="stat-label">Levels Remaining</div>
                <div class="stat-val">{{ $stats['units_left'] }}</div>
            </div>
        </div>

        
        <span class="sec-label">Filter by State</span>
        <div class="filter-bar">
            <a href="{{ route('packages-tracking.index') }}" class="filter-pill {{ $stateFilter === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'active']) }}" class="filter-pill {{ $stateFilter === 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'available']) }}" class="filter-pill {{ $stateFilter === 'available' ? 'active' : '' }}">Ready to Continue</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'done']) }}" class="filter-pill {{ $stateFilter === 'done' ? 'active' : '' }}">Completed</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'postponed']) }}" class="filter-pill {{ $stateFilter === 'postponed' ? 'active' : '' }}">Postponed</a>
        </div>

        
        <span class="sec-label">{{ $rows->count() }} {{ Str::plural('Student', $rows->count()) }}</span>
        <div class="tbl-card">
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Package</th>
                            <th>Current Course / Level</th>
                            <th class="num">Levels Left</th>
                            <th>State</th>
                            @if($canAct)<th>Action</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $e)
                        @php
                            $stateMeta = match($e->v_state) {
                                'active'    => ['st-active','Active','var(--green)'],
                                'available' => ['st-available','Level Done · Continue','var(--blue)'],
                                'done'      => ['st-done','Package Complete','var(--blue)'],
                                'postponed' => ['st-postponed','Postponed','#64748B'],
                                default     => ['st-active','—','var(--green)'],
                            };
                            $fillColor   = $e->v_state === 'done' ? 'var(--blue)' : 'var(--blue)';
                            $remainColor = $e->v_state === 'available' ? 'var(--blue-dk)' : ($e->v_state === 'done' ? 'var(--blue)' : 'var(--green-dk)');
                        @endphp
                        <tr>
                            <td>
                                <div class="std-cell">
                                    <div class="std-avatar">{{ strtoupper(substr($e->student?->full_name ?? '?', 0, 1)) }}</div>
                                    <div>
                                        <div class="std-name">{{ $e->student?->full_name ?? 'Student #'.$e->enrollment_id }}</div>
                                        <div class="std-course">Enrollment #{{ $e->enrollment_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="pkg-name-chip">{{ $e->v_package_name }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $e->v_course }}</div>
                                <div style="font-size:10px;color:var(--muted);margin-top:1px;">{{ $e->v_level }}</div>
                            </td>
                            <td class="num">
                                <div class="units-cell">
                                    <div class="units-top">
                                        <span class="units-remain" style="color:{{ $remainColor }};">{{ $e->v_remaining }}</span>
                                        @if($e->v_total_units)<span class="units-total">/ {{ $e->v_total_units }}</span>@endif
                                    </div>
                                    <div class="units-bar"><div class="units-fill" style="width:{{ 100 - $e->v_done_pct }}%;background:{{ $fillColor }};"></div></div>
                                </div>
                            </td>
                            <td><span class="state-badge {{ $stateMeta[0] }}">{{ $stateMeta[1] }}</span></td>
                            @if($canAct)
                            <td>
                                @if($e->v_state === 'available')
                                    
                                    @if($e->v_lead_id)
                                    <a href="{{ route('registration.from.lead', $e->v_lead_id) }}?renew=1" class="btn-enroll">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        New Course
                                    </a>
                                    @else
                                    <a href="{{ route('leads.index') }}" class="btn-enroll" title="No lead linked — open leads">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        New Course
                                    </a>
                                    @endif
                                @else
                                <span style="color:var(--faint);font-size:11px;">—</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $canAct ? 6 : 5 }}">
                                <div class="tbl-empty">
                                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="1"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                                    <div class="tbl-empty-title">No Package Students</div>
                                    <div style="font-size:12px;">No level-package enrollments match this filter.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection