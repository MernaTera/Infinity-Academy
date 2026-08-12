@extends('layouts.leads')
@section('title', 'Leads Dashboard')
@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-l:rgba(5,150,105,0.07);
        --teal:#0EA5A5; --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }

    .dash-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }

    .dash-header {
        margin:0 auto 24px;
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:26px 32px;
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:16px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .dash-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .dash-header::after { content:''; position:absolute; bottom:-60px; left:22%; width:160px; height:160px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .dash-header-left { position:relative; z-index:1; }
    .dash-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:6px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .dash-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--orange); box-shadow:0 0 8px var(--orange); }
    .dash-title { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:4px; color:#fff; line-height:1; margin:0; }
    .dash-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:6px; letter-spacing:0.5px; }
    .dash-actions { display:flex; gap:10px; position:relative; z-index:1; flex-wrap:wrap; }
    .btn-h { display:inline-flex; align-items:center; gap:7px; padding:11px 20px; border-radius:7px; font-size:10px; letter-spacing:2px; text-transform:uppercase; text-decoration:none; transition:all 0.25s; font-weight:600; cursor:pointer; }
    .btn-h-ghost { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:rgba(255,255,255,0.85); }
    .btn-h-ghost:hover { background:rgba(255,255,255,0.14); color:#fff; text-decoration:none; }
    .btn-h-solid { background:var(--orange); border:none; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:3px; }
    .btn-h-solid:hover { background:#E8850F; text-decoration:none; color:#fff; }

    .dash-wrap { margin:0 auto; }
    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin:26px 0 14px; font-weight:600; }
    .sec-label:first-child { margin-top:0; }

    .kpi-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:14px; }
    @media (max-width:1100px){ .kpi-grid{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:600px){ .kpi-grid{ grid-template-columns:repeat(2,1fr); } }
    .kpi-card {
        background:var(--card); border:1px solid var(--border); border-radius:12px;
        padding:18px 18px 16px; text-decoration:none; position:relative; overflow:hidden;
        transition:transform 0.2s, box-shadow 0.2s; display:block;
        box-shadow:0 2px 10px rgba(27,79,168,0.04);
    }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kc,var(--blue)); }
    .kpi-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(27,79,168,0.1); text-decoration:none; }
    .kpi-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:12px; background:var(--bg); border:1px solid var(--border); }
    .kpi-icon svg { width:18px; height:18px; stroke:var(--kc,var(--blue)); }
    .kpi-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:6px; }
    .kpi-val { font-family:'Bebas Neue',sans-serif; font-size:38px; letter-spacing:1px; line-height:0.9; color:var(--kc,var(--blue)); }
    .kpi-sub { font-size:10px; color:var(--faint); margin-top:5px; letter-spacing:0.3px; }
    .kpi-link { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--kc,var(--blue)); font-weight:600; margin-top:8px; opacity:0.85; }

    .two-col { display:grid; grid-template-columns:1.35fr 1fr; gap:20px; }
    @media (max-width:960px){ .two-col{ grid-template-columns:1fr; } }

    .panel { background:var(--card); border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .panel-head { padding:16px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; background:linear-gradient(135deg, rgba(27,79,168,0.02), transparent); }
    .panel-title { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:2px; color:var(--text); }
    .panel-hint { font-size:10px; color:var(--muted); margin-left:auto; letter-spacing:0.4px; }
    .panel-body { padding:20px 22px; }

    .funnel-row { display:flex; align-items:center; gap:14px; margin-bottom:14px; }
    .funnel-row:last-child { margin-bottom:0; }
    .funnel-bar-wrap { flex:1; }
    .funnel-bar-top { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:6px; }
    .funnel-name { font-size:11px; font-weight:600; color:var(--text); letter-spacing:0.3px; }
    .funnel-count { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:1px; color:var(--fc,var(--blue)); }
    .funnel-track { height:8px; background:var(--bg); border-radius:6px; overflow:hidden; }
    .funnel-fill { height:100%; background:var(--fc,var(--blue)); border-radius:6px; transition:width 0.6s cubic-bezier(0.16,1,0.3,1); }
    .funnel-pct { font-size:10px; color:var(--muted); min-width:38px; text-align:right; font-weight:600; }

    .dist-item { display:flex; align-items:center; gap:12px; margin-bottom:13px; }
    .dist-item:last-child { margin-bottom:0; }
    .dist-name { font-size:11px; color:var(--text); font-weight:500; min-width:88px; letter-spacing:0.2px; }
    .dist-track { flex:1; height:7px; background:var(--bg); border-radius:5px; overflow:hidden; }
    .dist-fill { height:100%; border-radius:5px; }
    .dist-val { font-size:11px; font-weight:600; color:var(--muted); min-width:24px; text-align:right; }

    .period-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .period-box { background:var(--bg); border:1px solid var(--border); border-radius:10px; padding:14px 16px; text-align:center; }
    .period-box-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:8px; }
    .period-box-val { font-family:'Bebas Neue',sans-serif; font-size:30px; color:var(--blue); line-height:0.9; }
    .period-box-sub { font-size:9px; color:var(--faint); margin-top:5px; }

    .followup-item { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid var(--border); }
    .followup-item:last-child { border-bottom:none; }
    .followup-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg, var(--blue-l), var(--orange-l)); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:16px; color:var(--blue); flex-shrink:0; }
    .followup-info { flex:1; min-width:0; }
    .followup-name { font-size:12px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .followup-meta { font-size:10px; color:var(--muted); margin-top:2px; }
    .followup-when { font-size:10px; font-weight:600; padding:4px 10px; border-radius:20px; white-space:nowrap; }
    .fw-today { background:var(--orange-l); color:var(--orange-dk); }
    .fw-soon { background:var(--blue-l); color:var(--blue); }
    .fw-past { background:var(--red-l); color:var(--red); }
    .empty-mini { text-align:center; padding:24px; color:var(--faint); font-size:12px; }

    .table-panel { background:var(--card); border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .table-scroll { overflow-x:auto; }
    .lead-table { width:100%; border-collapse:collapse; min-width:900px; }
    .lead-table thead th {
        font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted);
        padding:13px 16px; text-align:left; border-bottom:1px solid var(--border);
        font-weight:600; background:var(--bg); white-space:nowrap;
    }
    .lead-table tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .lead-table tbody tr:last-child td { border-bottom:none; }
    .lead-table tbody tr { transition:background 0.15s; }
    .lead-table tbody tr:hover { background:var(--blue-l); }

    .lt-name-cell { display:flex; align-items:center; gap:11px; }
    .lt-avatar { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg, var(--blue-l), var(--orange-l)); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:14px; color:var(--blue); flex-shrink:0; }
    .lt-name { font-weight:600; color:var(--text); }
    .lt-phone { font-size:10px; color:var(--muted); margin-top:1px; }
    .lt-course { font-weight:500; }
    .lt-course-sub { font-size:10px; color:var(--faint); margin-top:1px; }
    .lt-muted { color:var(--muted); }

    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:10px; font-weight:600; letter-spacing:0.3px; white-space:nowrap; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .b-waiting { background:rgba(122,138,154,0.1); color:var(--muted); }
    .b-call { background:var(--orange-l); color:var(--orange-dk); }
    .b-sched { background:var(--blue-l); color:var(--blue); }
    .b-reg { background:var(--green-l); color:var(--green); }
    .b-noint { background:var(--red-l); color:var(--red); }
    .b-arch { background:rgba(154,138,122,0.12); color:#9A8A7A; }

    .src-chip { display:inline-block; padding:3px 9px; border-radius:6px; font-size:10px; font-weight:500; background:var(--bg); color:var(--muted); border:1px solid var(--border); }

    .lt-action { display:inline-flex; align-items:center; gap:5px; padding:6px 13px; background:var(--blue); color:#fff; border-radius:6px; font-size:10px; letter-spacing:0.5px; text-decoration:none; font-weight:600; transition:background 0.2s; }
    .lt-action:hover { background:var(--blue-2); text-decoration:none; color:#fff; }

    .table-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .table-empty svg { margin-bottom:12px; opacity:0.4; }
</style>

<div class="dash-page">

    {{-- ═══ HEADER ═══ --}}
    <div class="dash-header">
        <div class="dash-header-left">
            <div class="dash-eyebrow">My Pipeline</div>
            <h1 class="dash-title">Leads Dashboard</h1>
            <div class="dash-sub">{{ now()->format('l, d M Y') }} · Track and convert your leads</div>
        </div>
        <div class="dash-actions">
            <a href="{{ route('leads.public') }}" class="btn-h btn-h-ghost">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 11l2 2 4-4M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                Public Pool
            </a>
            <a href="{{ route('leads.index') }}" class="btn-h btn-h-ghost">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                All Leads
            </a>
            <a href="{{ route('leads.create') }}" class="btn-h btn-h-solid">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Lead
            </a>
        </div>
    </div>

    <div class="dash-wrap">

        {{-- ═══ KPI OVERVIEW ═══ --}}
        <span class="sec-label">Overview</span>
        <div class="kpi-grid">
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--blue)">
                <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <div class="kpi-label">Total Leads</div>
                <div class="kpi-val">{{ $stats['total'] }}</div>
                <div class="kpi-link">View all →</div>
            </a>
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--green)">
                <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                <div class="kpi-label">Registered</div>
                <div class="kpi-val">{{ $stats['registered'] }}</div>
                <div class="kpi-sub">converted</div>
            </a>
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--orange-dk)">
                <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
                <div class="kpi-label">Call Again</div>
                <div class="kpi-val">{{ $stats['call_again'] }}</div>
                <div class="kpi-sub">needs follow-up</div>
            </a>
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--muted)">
                <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                <div class="kpi-label">Waiting</div>
                <div class="kpi-val">{{ $stats['waiting'] }}</div>
                <div class="kpi-sub">new / untouched</div>
            </a>
            <a href="{{ route('leads.archived') }}" class="kpi-card" style="--kc:#9A8A7A">
                <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg></div>
                <div class="kpi-label">Archived</div>
                <div class="kpi-val">{{ $stats['archived'] }}</div>
                <div class="kpi-sub">closed out</div>
            </a>
            <a href="{{ route('leads.public') }}" class="kpi-card" style="--kc:var(--blue-2)">
                <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                <div class="kpi-label">Public Pool</div>
                <div class="kpi-val">{{ $stats['public'] }}</div>
                <div class="kpi-link">Claim →</div>
            </a>
        </div>

        {{-- ═══ FUNNEL + PERIOD ═══ --}}
        <span class="sec-label">Performance</span>
        <div class="two-col">
            {{-- Conversion Funnel --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">Conversion Funnel</div>
                    <div class="panel-hint">Your pipeline breakdown</div>
                </div>
                <div class="panel-body">
                    @php
                        $tot = max($stats['total'], 1);
                        $funnelStages = [
                            ['Total Leads',   $stats['total'],      'var(--blue)'],
                            ['Waiting',       $stats['waiting'],    'var(--muted)'],
                            ['Call Again',    $stats['call_again'], 'var(--orange-dk)'],
                            ['Registered',    $stats['registered'], 'var(--green)'],
                        ];
                    @endphp
                    @foreach($funnelStages as [$name, $count, $color])
                        <div class="funnel-row">
                            <div class="funnel-bar-wrap" style="--fc:{{ $color }}">
                                <div class="funnel-bar-top">
                                    <span class="funnel-name">{{ $name }}</span>
                                    <span class="funnel-count">{{ $count }}</span>
                                </div>
                                <div class="funnel-track">
                                    <div class="funnel-fill" style="width:{{ round(($count / $tot) * 100) }}%"></div>
                                </div>
                            </div>
                            <span class="funnel-pct">{{ round(($count / $tot) * 100) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Period Activity --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">Activity</div>
                    <div class="panel-hint">Leads updated</div>
                </div>
                <div class="panel-body">
                    @php
                        $todayTotal = array_sum($today);
                        $weekTotal  = array_sum($week);
                        $monthTotal = array_sum($month);
                    @endphp
                    <div class="period-grid">
                        <div class="period-box">
                            <div class="period-box-label">Today</div>
                            <div class="period-box-val">{{ $todayTotal }}</div>
                            <div class="period-box-sub">touched</div>
                        </div>
                        <div class="period-box">
                            <div class="period-box-label">This Week</div>
                            <div class="period-box-val" style="color:var(--orange)">{{ $weekTotal }}</div>
                            <div class="period-box-sub">touched</div>
                        </div>
                        <div class="period-box">
                            <div class="period-box-label">This Month</div>
                            <div class="period-box-val" style="color:var(--green)">{{ $monthTotal }}</div>
                            <div class="period-box-sub">touched</div>
                        </div>
                    </div>

                    @php
                        $registeredThisMonth = $month['Registered'] ?? 0;
                        $convRate = $monthTotal > 0 ? round(($registeredThisMonth / $monthTotal) * 100) : 0;
                    @endphp
                    <div style="margin-top:18px; padding:14px 16px; background:var(--green-l); border:1px solid rgba(5,150,105,0.15); border-radius:10px; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <div style="font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--green); font-weight:600;">Month Conversion</div>
                            <div style="font-size:10px; color:var(--muted); margin-top:3px;">{{ $registeredThisMonth }} registered of {{ $monthTotal }} touched</div>
                        </div>
                        <div style="font-family:'Bebas Neue',sans-serif; font-size:32px; color:var(--green); line-height:1;">{{ $convRate }}%</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ DISTRIBUTION + FOLLOWUP ═══ --}}
        <span class="sec-label">Insights</span>
        <div class="two-col">
            {{-- Distributions --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">Lead Sources & Courses</div>
                    <div class="panel-hint">Where they come from</div>
                </div>
                <div class="panel-body">
                    @php $maxSrc = max(array_values($bySource) ?: [1]); @endphp
                    <div style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:12px;">By Source</div>
                    @forelse($bySource as $src => $cnt)
                        <div class="dist-item">
                            <span class="dist-name">{{ str_replace('_',' ',$src) }}</span>
                            <div class="dist-track"><div class="dist-fill" style="width:{{ round(($cnt/$maxSrc)*100) }}%; background:var(--blue);"></div></div>
                            <span class="dist-val">{{ $cnt }}</span>
                        </div>
                    @empty
                        <div class="empty-mini">No source data yet</div>
                    @endforelse

                    @if(!empty($byCourse))
                        @php $maxCrs = max(array_values($byCourse) ?: [1]); @endphp
                        <div style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; margin:20px 0 12px;">By Course Interest</div>
                        @foreach($byCourse as $crs => $cnt)
                            <div class="dist-item">
                                <span class="dist-name">{{ $crs }}</span>
                                <div class="dist-track"><div class="dist-fill" style="width:{{ round(($cnt/$maxCrs)*100) }}%; background:var(--orange);"></div></div>
                                <span class="dist-val">{{ $cnt }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Upcoming Follow-ups --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">Upcoming Follow-ups</div>
                    <div class="panel-hint">Scheduled calls</div>
                </div>
                <div class="panel-body" style="padding-top:8px; padding-bottom:8px;">
                    @php
                        $followUps = $recentLeads->filter(fn($l) => $l->next_call_at !== null)
                            ->sortBy('next_call_at')->take(6);
                    @endphp
                    @forelse($followUps as $lead)
                        @php
                            $callDate = \Carbon\Carbon::parse($lead->next_call_at);
                            $isToday = $callDate->isToday();
                            $isPast = $callDate->isPast() && !$isToday;
                            $whenClass = $isToday ? 'fw-today' : ($isPast ? 'fw-past' : 'fw-soon');
                            $whenText = $isToday ? 'Today ' . $callDate->format('H:i') : ($isPast ? $callDate->format('d M') . ' (past)' : $callDate->format('d M · H:i'));
                        @endphp
                        <div class="followup-item">
                            <div class="followup-avatar">{{ strtoupper(substr($lead->full_name,0,1)) }}</div>
                            <div class="followup-info">
                                <div class="followup-name">{{ $lead->full_name }}</div>
                                <div class="followup-meta">{{ $lead->phone }} · {{ $lead->courseTemplate?->name ?? 'No course' }}</div>
                            </div>
                            <span class="followup-when {{ $whenClass }}">{{ $whenText }}</span>
                        </div>
                    @empty
                        <div class="empty-mini">
                            No scheduled follow-ups.<br>
                            <span style="font-size:11px;">Set call times on your leads to see them here.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══ DETAILED RECENT LEADS TABLE ═══ --}}
        <span class="sec-label">Recent Leads · Detailed</span>
        <div class="table-panel">
            <div class="table-scroll">
                <table class="lead-table">
                    <thead>
                        <tr>
                            <th>Lead</th>
                            <th>Status</th>
                            <th>Course Interest</th>
                            <th>Source</th>
                            <th>Degree</th>
                            <th>Start Pref.</th>
                            <th>Next Call</th>
                            <th>Added</th>
                            <!-- <th style="text-align:right;">Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeads as $lead)
                            @php
                                $badgeMap = [
                                    'Waiting'        => ['b-waiting','Waiting'],
                                    'Call_Again'     => ['b-call','Call Again'],
                                    'Scheduled_Call' => ['b-sched','Scheduled'],
                                    'Registered'     => ['b-reg','Registered'],
                                    'Not_Interested' => ['b-noint','Not Interested'],
                                    'Archived'       => ['b-arch','Archived'],
                                ];
                                [$bClass,$bLabel] = $badgeMap[$lead->status] ?? ['b-waiting',$lead->status];
                            @endphp
                            <tr>
                                <td>
                                    <div class="lt-name-cell">
                                        <div class="lt-avatar">{{ strtoupper(substr($lead->full_name,0,1)) }}</div>
                                        <div>
                                            <div class="lt-name">{{ $lead->full_name }}</div>
                                            <div class="lt-phone">{{ $lead->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge {{ $bClass }}">{{ $bLabel }}</span></td>
                                <td>
                                    @if($lead->courseTemplate)
                                        <div class="lt-course">{{ $lead->courseTemplate->name }}</div>
                                        @if($lead->level || $lead->sublevel)
                                            <div class="lt-course-sub">{{ $lead->level?->name }}{{ $lead->sublevel ? ' · '.$lead->sublevel->name : '' }}</div>
                                        @endif
                                    @else
                                        <span class="lt-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="src-chip">{{ str_replace('_',' ',$lead->source) }}</span></td>
                                <td><span class="lt-muted">{{ $lead->degree }}</span></td>
                                <td>
                                    @if($lead->start_preference_type)
                                        <span style="font-size:11px;">{{ $lead->start_preference_type }}</span>
                                        @if($lead->start_preference_date)
                                            <div class="lt-course-sub">{{ \Carbon\Carbon::parse($lead->start_preference_date)->format('d M Y') }}</div>
                                        @endif
                                    @else
                                        <span class="lt-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lead->next_call_at)
                                        @php $nc = \Carbon\Carbon::parse($lead->next_call_at); @endphp
                                        <div style="font-size:11px; font-weight:600; color:{{ $nc->isToday() ? 'var(--orange-dk)' : ($nc->isPast() ? 'var(--red)' : 'var(--text)') }};">{{ $nc->format('d M') }}</div>
                                        <div class="lt-course-sub">{{ $nc->format('H:i') }}</div>
                                    @else
                                        <span class="lt-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="lt-muted">{{ $lead->created_at?->format('d M Y') }}</span></td>
                                <!-- <td style="text-align:right;">
                                    <a href="{{ route('leads.edit', $lead->lead_id) }}" class="lt-action">
                                        Open
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                                    </a>
                                </td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="table-empty">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                        <div style="font-size:14px; font-weight:600; color:var(--muted); margin-bottom:4px;">No leads yet</div>
                                        <div style="font-size:12px;">Create your first lead or claim from the public pool.</div>
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