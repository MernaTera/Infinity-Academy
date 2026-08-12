@php
    // Pick the layout that matches the viewer's role, so each role keeps its
    // own sidebar (Admin / Student Care / CS-Leads).
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
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --purple:#7C3AED; --purple-dk:#6D28D9; --purple-l:rgba(124,58,237,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }
    .pk-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .pk-wrap { max-width:1300px; margin:0 auto; }

    /* HEADER */
    .pk-header {
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px; margin-bottom:22px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .pk-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(124,58,237,0.08); }
    .pk-header::after { content:''; position:absolute; bottom:-60px; left:30%; width:150px; height:150px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .pk-header-inner { position:relative; z-index:1; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; }
    .pk-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--purple); margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .pk-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--purple); box-shadow:0 0 8px var(--purple); }
    .pk-title { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; color:#fff; line-height:1; margin:0; }
    .pk-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }
    .btn-new { display:inline-flex; align-items:center; gap:7px; padding:11px 20px; background:var(--purple); border:none; border-radius:8px; color:#fff; font-size:11px; letter-spacing:1px; text-transform:uppercase; text-decoration:none; font-weight:700; transition:all 0.2s; box-shadow:0 4px 14px rgba(124,58,237,0.3); }
    .btn-new:hover { background:var(--purple-dk); color:#fff; text-decoration:none; transform:translateY(-1px); }

    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--purple); margin:24px 0 14px; font-weight:600; }

    /* STATS */
    .pk-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
    @media (max-width:1100px){ .pk-stats{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:560px){ .pk-stats{ grid-template-columns:1fr 1fr; } }
    .stat { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px 18px; position:relative; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--sc,var(--blue)); }
    .stat-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .stat-val { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }

    /* FILTER */
    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; }
    .filter-pill { padding:9px 18px; border-radius:8px; font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:all 0.2s; background:var(--card); border:1px solid var(--border); color:var(--muted); text-decoration:none; }
    .filter-pill:hover { border-color:var(--purple); color:var(--purple); text-decoration:none; }
    .filter-pill.active { background:var(--purple); border-color:var(--purple); color:#fff; box-shadow:0 4px 12px rgba(124,58,237,0.2); }

    /* TABLE */
    .tbl-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:950px; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:14px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:var(--bg); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:var(--blue-l); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }

    .std-cell { display:flex; align-items:center; gap:11px; }
    .std-avatar { width:36px; height:36px; border-radius:50%; background:var(--purple-l); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:15px; color:var(--purple); flex-shrink:0; }
    .std-name { font-weight:700; color:var(--text); }
    .std-course { font-size:10px; color:var(--muted); margin-top:1px; }

    .pkg-name-chip { display:inline-block; padding:3px 10px; border-radius:6px; font-size:10px; font-weight:600; background:var(--purple-l); color:var(--purple-dk); }

    .units-cell { min-width:150px; }
    .units-top { display:flex; align-items:baseline; gap:5px; margin-bottom:5px; justify-content:flex-end; }
    .units-remain { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:0.5px; }
    .units-total { font-size:10px; color:var(--faint); }
    .units-bar { height:6px; background:var(--bg); border-radius:3px; overflow:hidden; }
    .units-fill { height:100%; border-radius:3px; }

    .state-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:10px; font-weight:600; white-space:nowrap; }
    .state-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .st-active { background:var(--green-l); color:var(--green-dk); }
    .st-available { background:var(--purple-l); color:var(--purple-dk); }
    .st-done { background:var(--blue-l); color:var(--blue); }

    .btn-enroll { display:inline-flex; align-items:center; gap:5px; padding:7px 13px; border-radius:7px; font-size:10px; font-weight:600; letter-spacing:0.3px; text-decoration:none; background:var(--purple-l); color:var(--purple-dk); border:1px solid rgba(124,58,237,0.3); transition:all 0.2s; white-space:nowrap; }
    .btn-enroll:hover { background:var(--purple); color:#fff; text-decoration:none; }

    .tbl-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .tbl-empty svg { opacity:0.35; margin-bottom:12px; }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-bottom:4px; }

    @media (max-width:600px){ .pk-page{ padding:16px; } }
</style>

<div class="pk-page">
    <div class="pk-wrap">

        {{-- HEADER --}}
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

        {{-- STATS --}}
        <div class="pk-stats">
            <div class="stat" style="--sc:var(--dark)">
                <div class="stat-label">Total Packages</div>
                <div class="stat-val">{{ $stats['total'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--green)">
                <div class="stat-label">Active</div>
                <div class="stat-val">{{ $stats['active'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--purple)">
                <div class="stat-label">Ready to Continue</div>
                <div class="stat-val">{{ $stats['available'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--blue)">
                <div class="stat-label">Completed</div>
                <div class="stat-val">{{ $stats['done'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--purple-dk)">
                <div class="stat-label">Levels Remaining</div>
                <div class="stat-val">{{ $stats['units_left'] }}</div>
            </div>
        </div>

        {{-- FILTER --}}
        <span class="sec-label">Filter by State</span>
        <div class="filter-bar">
            <a href="{{ route('packages-tracking.index') }}" class="filter-pill {{ $stateFilter === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'active']) }}" class="filter-pill {{ $stateFilter === 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'available']) }}" class="filter-pill {{ $stateFilter === 'available' ? 'active' : '' }}">Ready to Continue</a>
            <a href="{{ route('packages-tracking.index', ['state' => 'done']) }}" class="filter-pill {{ $stateFilter === 'done' ? 'active' : '' }}">Completed</a>
        </div>

        {{-- TABLE --}}
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
                                'available' => ['st-available','Level Done · Continue','var(--purple)'],
                                'done'      => ['st-done','Package Complete','var(--blue)'],
                                default     => ['st-active','—','var(--green)'],
                            };
                            $fillColor   = $e->v_state === 'done' ? 'var(--blue)' : 'var(--purple)';
                            $remainColor = $e->v_state === 'available' ? 'var(--purple-dk)' : ($e->v_state === 'done' ? 'var(--blue)' : 'var(--green-dk)');
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
                                    {{-- Current level finished with prepaid levels left → open a new
                                         (free) course registration for the next level. --}}
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