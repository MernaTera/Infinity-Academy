@extends(auth()->user()?->isCsLeader() ? 'layouts.leads' : 'admin.layouts.app')
@section('title', 'Sales Revenue')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

@php
    $salesRoute = auth()->user()?->isCsLeader() ? 'team.sales' : 'admin.sales.index';
    $csRevRoute = auth()->user()?->isCsLeader() ? 'team.sales.cs-revenue' : 'admin.sales.cs-revenue';
@endphp

<style>
:root {
    --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-light:rgba(27,79,168,0.08);
    --orange:#4E86D6; --orange-dk:#1B4FA8; --orange-light:rgba(78,134,214,0.1);
    --green:#2D6FDB; --green-dk:#1B4FA8; --green-light:rgba(45,111,219,0.1);
    --red:#5A6A85; --red-light:rgba(90,106,133,0.08);
    --purple:#3E6FC4; --purple-light:rgba(62,111,196,0.1);
    --border:rgba(27,79,168,0.1);
    --bg:#F8F6F2; --card:rgba(255,255,255,0.5);
    --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
    --glass-bd:rgba(255,255,255,0.65); --glass-sh:0 12px 36px -12px rgba(23,45,90,0.18);
    --track:rgba(27,79,168,0.08);
}

* { box-sizing: border-box; }

.sales-page {
    min-height:100vh; padding:40px 34px 52px; font-family:'DM Sans',sans-serif; color:var(--text);
    background:#F8F6F2;
}
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

.glass { background:var(--card); -webkit-backdrop-filter:blur(24px) saturate(180%); backdrop-filter:blur(24px) saturate(180%); border:1px solid var(--glass-bd); box-shadow:var(--glass-sh); }

.page-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--orange-dk); margin-bottom:7px; font-weight:600; }
.page-title { font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:2px; color:var(--text); margin:0 0 24px; line-height:0.95; }

.filter-bar { display:flex; align-items:center; gap:10px; margin-bottom:26px; flex-wrap:wrap; }
.filter-tab { padding:9px 20px; border-radius:12px; font-size:10px; letter-spacing:2px; text-transform:uppercase; text-decoration:none; border:1px solid var(--glass-bd); transition:all 0.2s; font-family:'DM Sans',sans-serif; white-space:nowrap; font-weight:600; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); color:var(--muted); }
.filter-tab.active { background:linear-gradient(120deg,var(--blue),var(--blue-2)); color:#fff; border-color:transparent; box-shadow:0 8px 20px rgba(27,79,168,0.28); }
.filter-tab:not(.active):hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }
.filter-input { font-family:'DM Sans',sans-serif; font-size:12px; padding:9px 13px; border:1px solid var(--glass-bd); border-radius:12px; background:rgba(255,255,255,0.55); color:var(--text); outline:none; transition:border-color 0.2s, box-shadow 0.2s; }
.filter-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }
.filter-sep { width:1px; height:24px; background:var(--border); }

.kpi-strip { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:30px; }
@media(max-width:1000px) { .kpi-strip { grid-template-columns:repeat(3,1fr); } }
.kpi-card { border-radius:18px; padding:20px; position:relative; overflow:hidden; }
/* .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kc, var(--blue)); } */
.kpi-eyebrow { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); margin-bottom:8px; font-weight:600; }
.kpi-value { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:1px; color:var(--kc, var(--blue)); line-height:1; }
.kpi-sub { font-size:10px; color:var(--faint); margin-top:4px; }
.prog { background:var(--track); border-radius:3px; height:4px; margin-top:10px; overflow:hidden; }
.prog-fill { height:4px; border-radius:3px; background:var(--kc, var(--blue)); transition:width .6s ease; }

.sec-label { display:flex; align-items:center; gap:10px; font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--text); font-weight:700; margin:6px 0 16px; }
.sec-label::before { content:''; width:20px; height:3px; border-radius:3px; background:linear-gradient(90deg, var(--orange), var(--blue)); flex-shrink:0; }

.cs-table-card { border-radius:20px; overflow:hidden; margin-bottom:30px; }
.cs-tbl { width:100%; border-collapse:collapse; }
.cs-tbl thead th { padding:14px 16px; font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); text-align:left; font-weight:700; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); border-bottom:1px solid var(--glass-bd); white-space:nowrap; }
.cs-tbl thead th:last-child { text-align:center; }
.cs-tbl tbody tr { border-bottom:1px solid rgba(27,79,168,0.06); transition:background 0.15s; }
.cs-tbl tbody tr:last-child { border-bottom:none; }
.cs-tbl tbody tr:hover { background:rgba(27,79,168,0.04); }
.cs-tbl td { padding:14px 16px; font-size:13px; color:var(--muted); vertical-align:middle; }

.cs-avatar { width:34px; height:34px; border-radius:11px; background:var(--blue-light); color:var(--blue); display:inline-flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:15px; flex-shrink:0; }
.cs-name { font-weight:600; color:var(--text); font-size:13px; }
.cs-branch { font-size:10px; color:var(--faint); margin-top:2px; }

.rank-badge { width:26px; height:26px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:13px; flex-shrink:0; }
.rank-1 { background:rgba(45,111,219,0.18); color:#1B4FA8; }
.rank-2 { background:rgba(122,138,154,0.16); color:#5A6A7A; }
.rank-3 { background:rgba(90,106,133,0.14); color:#7A8A9A; }
.rank-n { background:rgba(27,79,168,0.08); color:var(--faint); }

.money { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:1px; color:var(--text); }
.money-green { color:var(--green); }
.money-orange { color:var(--orange-dk); }
.money-red { color:var(--red); }

.mini-prog-wrap { display:flex; align-items:center; gap:8px; }
.mini-prog { flex:1; background:var(--track); border-radius:3px; height:6px; overflow:hidden; min-width:60px; }
.mini-prog-fill { height:6px; border-radius:3px; transition:width .5s ease; }
.mini-prog-pct { font-size:11px; font-family:'Bebas Neue',sans-serif; letter-spacing:1px; white-space:nowrap; }

.stat-pill { display:inline-flex; align-items:center; gap:4px; font-size:10px; letter-spacing:0.5px; padding:3px 9px; border-radius:20px; font-weight:600; }
.pill-blue { background:var(--blue-light); color:var(--blue); }
.pill-orange { background:var(--orange-light); color:var(--orange-dk); }

.top-badge { display:inline-flex; align-items:center; gap:4px; font-size:8px; letter-spacing:2px; text-transform:uppercase; padding:3px 9px; border-radius:20px; background:var(--orange-light); color:var(--orange-dk); border:1px solid rgba(45,111,219,0.2); font-weight:600; }

.chart-card { border-radius:20px; padding:24px 26px; margin-bottom:30px; }
.chart-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
.chart-title { font-size:11px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); }
.chart-wrap { position:relative; height:220px; }

.cs-detail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:28px; }
@media(max-width:900px) { .cs-detail-grid { grid-template-columns:1fr; } }
.cs-detail-card { border-radius:18px; overflow:hidden; position:relative; }
/* .cs-detail-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, var(--orange), var(--blue)); } */
.cs-detail-header { padding:18px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--glass-bd); }
.cs-detail-stats { display:grid; grid-template-columns:repeat(3,1fr); }
.cs-detail-stat { padding:14px 16px; border-right:1px solid var(--border); text-align:center; }
.cs-detail-stat:last-child { border-right:none; }
.cs-detail-stat-label { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); margin-bottom:5px; }
.cs-detail-stat-val { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px; color:var(--text); line-height:1; }
.cs-detail-prog { padding:12px 16px 16px; }
.cs-detail-prog-label { font-size:9px; letter-spacing:1px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; display:flex; justify-content:space-between; }

@media(max-width:768px) { .sales-page { padding:20px 14px 36px; } .kpi-strip { grid-template-columns:1fr 1fr; } .page-title { font-size:34px; } }

.cs-detail-card { cursor:pointer; transition:transform .18s, box-shadow .18s; }
.cs-detail-card:hover { transform:translateY(-3px); box-shadow:0 18px 42px -14px rgba(23,45,90,0.3); }
.cs-detail-viewmore { padding:11px 20px; border-top:1px solid var(--border); font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--blue); font-weight:700; display:flex; align-items:center; justify-content:space-between; }
.cs-detail-viewmore span { opacity:.7; }

.rev-modal { display:none; position:fixed; inset:0; z-index:1200; background:rgba(15,31,61,0.55); -webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px); align-items:flex-start; justify-content:center; padding:40px 20px; overflow-y:auto; }
.rev-modal.show { display:flex; }
.rev-modal-box { background:rgba(255,255,255,0.9); -webkit-backdrop-filter:blur(24px) saturate(180%); backdrop-filter:blur(24px) saturate(180%); border:1px solid var(--glass-bd); border-radius:22px; width:100%; max-width:920px; box-shadow:0 30px 80px -20px rgba(15,31,61,0.5); overflow:hidden; margin:auto; }
.rev-modal-head { display:flex; align-items:center; gap:14px; padding:20px 24px; border-bottom:1px solid var(--border); background:rgba(255,255,255,0.5); }
.rev-modal-emp-avatar { width:42px; height:42px; border-radius:13px; background:var(--blue-light); color:var(--blue); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:19px; flex-shrink:0; }
.rev-modal-emp { flex:1; }
.rev-modal-name { font-weight:600; color:var(--text); font-size:15px; }
.rev-modal-title { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-top:3px; }
.rev-modal-close { background:rgba(27,79,168,0.06); border:1px solid var(--border); color:var(--muted); width:34px; height:34px; border-radius:50%; cursor:pointer; font-size:20px; line-height:1; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .2s; }
.rev-modal-close:hover { background:var(--blue); color:#fff; border-color:transparent; }
.rev-modal-body { max-height:64vh; overflow-y:auto; }
.rev-tbl { width:100%; border-collapse:collapse; min-width:720px; }
.rev-tbl thead th { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); padding:13px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:700; background:rgba(255,255,255,0.6); white-space:nowrap; }
.rev-tbl thead th.num { text-align:right; }
.rev-tbl tbody td { padding:13px 16px; border-bottom:1px solid rgba(27,79,168,0.06); font-size:12px; color:var(--text); vertical-align:middle; }
.rev-tbl tbody tr:last-child td { border-bottom:none; }
.rev-tbl tbody tr:hover { background:rgba(27,79,168,0.04); }
.rev-tbl .num { text-align:right; font-variant-numeric:tabular-nums; }
.rev-tbl .money { color:var(--muted); }
.rev-tbl .total { font-weight:700; color:var(--blue); }
.rev-std { display:flex; align-items:center; gap:10px; }
.rev-std-avatar { width:32px; height:32px; border-radius:10px; background:rgba(27,79,168,0.1); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:13px; color:var(--blue); flex-shrink:0; }
.rev-std-name { font-weight:600; color:var(--text); }
.rev-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:10px; font-weight:600; white-space:nowrap; }
.rev-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
.rev-badge-direct { background:rgba(27,79,168,0.08); color:var(--blue); }
.rev-badge-shared { background:rgba(45,111,219,0.1); color:var(--blue-2); }
.rev-tbl tfoot td { padding:14px 16px; border-top:2px solid var(--border); background:rgba(255,255,255,0.55); font-variant-numeric:tabular-nums; }
.rev-tfoot-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; }
.rev-tfoot-num { text-align:right; font-weight:600; color:var(--text); }
.rev-tfoot-total { text-align:right; font-family:'Bebas Neue',sans-serif; font-size:17px; color:var(--blue); letter-spacing:1px; }
.rev-empty { text-align:center; padding:46px 20px; color:var(--faint); font-size:13px; }
.rev-filter { display:flex; align-items:center; gap:12px; flex-wrap:wrap; padding:14px 24px; border-bottom:1px solid var(--border); background:rgba(255,255,255,0.35); }
.rev-tabs { display:inline-flex; gap:4px; background:rgba(27,79,168,0.05); border:1px solid var(--border); border-radius:12px; padding:4px; }
.rev-tab { padding:7px 15px; border:none; background:transparent; border-radius:9px; font-family:'DM Sans',sans-serif; font-size:10px; letter-spacing:1.5px; text-transform:uppercase; font-weight:600; color:var(--muted); cursor:pointer; transition:all .2s; }
.rev-tab.active { background:linear-gradient(120deg,var(--blue),var(--blue-2)); color:#fff; box-shadow:0 4px 12px rgba(27,79,168,0.28); }
.rev-tab:not(.active):hover { color:var(--blue); }
.rev-pickers { display:flex; gap:8px; align-items:center; }
.rev-sel { font-family:'DM Sans',sans-serif; font-size:12px; padding:7px 11px; border:1px solid rgba(27,79,168,0.15); border-radius:10px; background:rgba(255,255,255,0.7); color:var(--text); outline:none; color-scheme:light; cursor:pointer; }
.rev-sel:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }
</style>

<div class="sales-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="page-eyebrow">Admin Panel</div>
    <h1 class="page-title">Sales Revenue</h1>

    <div class="filter-bar">
        <a href="{{ route($salesRoute, ['filter'=>'month','month'=>$month]) }}"
           class="filter-tab {{ $filterType==='month'?'active':'' }}">By Month</a>
        <a href="{{ route($salesRoute, ['filter'=>'week','day'=>$day]) }}"
           class="filter-tab {{ $filterType==='week'?'active':'' }}">By Week</a>
        <a href="{{ route($salesRoute, ['filter'=>'day','day'=>$day]) }}"
           class="filter-tab {{ $filterType==='day'?'active':'' }}">By Day</a>

        <div class="filter-sep"></div>

        @if($filterType === 'month')
        <input type="month" value="{{ $month }}" class="filter-input"
               onchange="window.location.href='{{ route($salesRoute) }}?filter=month&month='+this.value">
        @elseif($filterType === 'week')
        <input type="week" value="{{ \Carbon\Carbon::parse($day)->format('Y-\WW') }}" class="filter-input"
               onchange="
                   const[y,w]=this.value.split('-W');
                   const d=new Date(y,0,1+(w-1)*7);
                   window.location.href='{{ route($salesRoute) }}?filter=week&day='+d.toISOString().split('T')[0]">
        @else
        <input type="date" value="{{ $day }}" class="filter-input" style="color-scheme:light;"
               onchange="window.location.href='{{ route($salesRoute) }}?filter=day&day='+this.value">
        @endif

        <span style="font-size:11px;color:var(--faint);margin-left:4px;">
            @if($filterType==='month') {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
            @elseif($filterType==='week') Week of {{ \Carbon\Carbon::parse($day)->startOfWeek()->format('d M') }} – {{ \Carbon\Carbon::parse($day)->endOfWeek()->format('d M Y') }}
            @else {{ \Carbon\Carbon::parse($day)->format('l, d M Y') }}
            @endif
        </span>
    </div>

    <span class="sec-label">Overall Performance</span>
    <div class="kpi-strip">
        <div class="kpi-card glass" style="--kc:var(--blue)">
            <div class="kpi-eyebrow">Total Target</div>
            <div class="kpi-value">{{ number_format($overallKpis['total_target']) }}</div>
            <div class="kpi-sub">LE across all CS</div>
        </div>
        <div class="kpi-card glass" style="--kc:var(--green)">
            <div class="kpi-eyebrow">Total Achieved</div>
            <div class="kpi-value">{{ number_format($overallKpis['total_achieved']) }}</div>
            <div class="kpi-sub">LE collected</div>
            @if($overallKpis['total_target'] > 0)
            <div class="prog">
                <div class="prog-fill" style="width:{{ min(100,round($overallKpis['total_achieved']/$overallKpis['total_target']*100)) }}%"></div>
            </div>
            @endif
        </div>
        <div class="kpi-card glass" style="--kc:var(--orange)">
            <div class="kpi-eyebrow">Avg Achievement</div>
            <div class="kpi-value">{{ round($overallKpis['avg_achievement'],1) }}%</div>
            <div class="kpi-sub">across CS team</div>
        </div>
        <div class="kpi-card glass" style="--kc:var(--purple)">
            <div class="kpi-eyebrow">Total Registrations</div>
            <div class="kpi-value">{{ $overallKpis['total_registrations'] }}</div>
            <div class="kpi-sub">students enrolled</div>
        </div>
        <div class="kpi-card glass" style="--kc:var(--orange)">
            <div class="kpi-eyebrow">Top Performer</div>
            @if($overallKpis['top_cs'])
            <div class="kpi-value" style="font-size:18px;font-family:'DM Sans',sans-serif;font-weight:600;letter-spacing:0">
                {{ $overallKpis['top_cs']['employee']->full_name }}
            </div>
            <div class="kpi-sub">{{ number_format($overallKpis['top_cs']['achieved']) }} LE achieved</div>
            @else
            <div class="kpi-value">—</div>
            @endif
        </div>
    </div>

    <span class="sec-label">Revenue Over Time — All CS Combined</span>
    <div class="chart-card glass">
        <div class="chart-header">
            <div class="chart-title">
                @if($filterType==='month') Daily revenue — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
                @elseif($filterType==='week') Revenue — {{ \Carbon\Carbon::parse($day)->startOfWeek()->format('d M') }} to {{ \Carbon\Carbon::parse($day)->endOfWeek()->format('d M Y') }}
                @else Revenue — {{ \Carbon\Carbon::parse($day)->format('d M Y') }}
                @endif
            </div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--green);">
                {{ number_format($overallKpis['total_achieved']) }} LE
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    <span class="sec-label">CS Leaderboard</span>
    <div class="cs-table-card glass">
        <div style="overflow-x:auto;">
            <table class="cs-tbl">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>CS Employee</th>
                        <th>Monthly Target</th>
                        <th>Achieved</th>
                        <th>Remaining</th>
                        <th>Achievement</th>
                        <th>Registrations</th>
                        <th>Total Leads</th>
                        <th>Active Leads</th>
                        <th>Calls Made</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                    @php $rank = $i + 1; @endphp
                    <tr>
                        <td>
                            <div class="rank-badge {{ $rank===1?'rank-1':($rank===2?'rank-2':($rank===3?'rank-3':'rank-n')) }}">
                                {{ $rank===1?'🥇':($rank===2?'🥈':($rank===3?'🥉':$rank)) }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="cs-avatar">{{ strtoupper(substr($row['employee']->full_name,0,1)) }}</div>
                                <div>
                                    <div class="cs-name">{{ $row['employee']->full_name }}</div>
                                    <div class="cs-branch">{{ $row['employee']->branch?->name ?? '—' }}</div>
                                </div>
                                @if($rank === 1)
                                <span class="top-badge">Top</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($row['target'] > 0)
                            <span class="money">{{ number_format($row['target']) }}</span>
                            <span style="font-size:10px;color:var(--faint);margin-left:2px;">LE</span>
                            @else
                            <span style="color:var(--faint);font-size:11px;">Not set</span>
                            @endif
                        </td>
                        <td>
                            <span class="money money-green">{{ number_format($row['achieved']) }}</span>
                            <span style="font-size:10px;color:var(--faint);margin-left:2px;">LE</span>
                        </td>
                        <td>
                            @if($row['remaining'] !== null)
                            <span class="money {{ $row['remaining'] > 0 ? 'money-orange' : 'money-green' }}">
                                {{ number_format($row['remaining']) }}
                            </span>
                            <span style="font-size:10px;color:var(--faint);margin-left:2px;">LE</span>
                            @else
                            <span style="color:var(--faint);font-size:11px;">N/A</span>
                            @endif
                        </td>
                        <td style="min-width:140px;">
                            @php $pct = $row['percentage']; @endphp
                            <div class="mini-prog-wrap">
                                <div class="mini-prog">
                                    <div class="mini-prog-fill" style="width:{{ min(100,$pct) }}%;background:{{ $pct>=100?'var(--green)':($pct>=60?'var(--blue)':'var(--orange)') }}"></div>
                                </div>
                                <span class="mini-prog-pct" style="color:{{ $pct>=100?'var(--green)':($pct>=60?'var(--blue)':'var(--orange)') }}">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span class="stat-pill pill-blue">{{ $row['registrations'] }}</span>
                        </td>
                        <td style="text-align:center;">{{ $row['total_leads'] }}</td>
                        <td style="text-align:center;">
                            <span class="stat-pill pill-orange">{{ $row['active_leads'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="stat-pill pill-blue">{{ $row['calls_made'] ?? 0 }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:40px;color:var(--faint);font-size:13px;">
                            No CS employees found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($rows->count() > 0)
                <tfoot>
                    <tr style="border-top:2px solid var(--border);background:rgba(27,79,168,0.03);">
                        <td colspan="2" style="padding:12px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);">Team Total</td>
                        <td>
                            <span class="money">{{ number_format($rows->sum('target')) }}</span>
                            <span style="font-size:10px;color:var(--faint);margin-left:2px;">LE</span>
                        </td>
                        <td>
                            <span class="money money-green">{{ number_format($rows->sum('achieved')) }}</span>
                            <span style="font-size:10px;color:var(--faint);margin-left:2px;">LE</span>
                        </td>
                        <td colspan="3"></td>
                        <td style="text-align:center;font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--text);">{{ $rows->sum('total_leads') }}</td>
                        <td></td>
                        <td style="text-align:center;font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--text);">{{ $rows->sum('calls_made') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <span class="sec-label">Individual Breakdown</span>
    <div class="cs-detail-grid">
        @foreach($rows as $i => $row)
        @php $pct = $row['percentage']; @endphp
        <div class="cs-detail-card glass" onclick="openRev({{ $row['employee']->employee_id }})">
            <div class="cs-detail-header">
                <div class="cs-avatar" style="width:40px;height:40px;font-size:18px;">
                    {{ strtoupper(substr($row['employee']->full_name,0,1)) }}
                </div>
                <div style="flex:1">
                    <div style="font-weight:600;color:var(--text);font-size:14px;">{{ $row['employee']->full_name }}</div>
                    <div style="font-size:10px;color:var(--faint);margin-top:2px;">{{ $row['employee']->branch?->name ?? '—' }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;color:var(--green);">
                        {{ number_format($row['achieved']) }} LE
                    </div>
                    <div style="font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;">Achieved</div>
                </div>
            </div>
            <div class="cs-detail-stats">
                <div class="cs-detail-stat">
                    <div class="cs-detail-stat-label">Target</div>
                    <div class="cs-detail-stat-val" style="color:var(--blue);">
                        {{ $row['target'] > 0 ? number_format($row['target']) : '—' }}
                    </div>
                </div>
                <div class="cs-detail-stat">
                    <div class="cs-detail-stat-label">Registrations</div>
                    <div class="cs-detail-stat-val" style="color:var(--purple);">{{ $row['registrations'] }}</div>
                </div>
                <div class="cs-detail-stat">
                    <div class="cs-detail-stat-label">Active Leads</div>
                    <div class="cs-detail-stat-val" style="color:var(--orange);">{{ $row['active_leads'] }}</div>
                </div>
                <div class="cs-detail-stat">
                    <div class="cs-detail-stat-label">Calls Made</div>
                    <div class="cs-detail-stat-val" style="color:var(--blue);">{{ $row['calls_made'] ?? 0 }}</div>
                </div>
            </div>
            <div class="cs-detail-prog">
                <div class="cs-detail-prog-label">
                    <span>Achievement Progress</span>
                    <span style="color:{{ $pct>=100?'var(--green)':($pct>=60?'var(--blue)':'var(--orange)') }};font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;">
                        {{ $pct }}%
                    </span>
                </div>
                <div style="background:var(--track);border-radius:4px;height:6px;overflow:hidden;">
                    <div style="width:{{ min(100,$pct) }}%;height:6px;border-radius:4px;transition:width .6s;background:{{ $pct>=100?'var(--green)':($pct>=60?'var(--blue)':'var(--orange)') }}"></div>
                </div>
                @if($row['remaining'] !== null && $row['remaining'] > 0)
                <div style="font-size:10px;color:var(--faint);margin-top:6px;">
                    {{ number_format($row['remaining']) }} LE remaining to target
                </div>
                @elseif($pct >= 100)
                <div style="font-size:10px;color:var(--green);margin-top:6px;">✓ Target reached!</div>
                @endif
            </div>
            <div class="cs-detail-viewmore">Per-student revenue <span>View &rarr;</span></div>
        </div>
        @endforeach
    </div>

    @foreach($rows as $row)
    <div class="rev-modal" id="revModal-{{ $row['employee']->employee_id }}" onclick="if(event.target===this)closeRev({{ $row['employee']->employee_id }})">
        <div class="rev-modal-box">
            <div class="rev-modal-head">
                <div class="rev-modal-emp-avatar">{{ strtoupper(substr($row['employee']->full_name,0,1)) }}</div>
                <div class="rev-modal-emp">
                    <div class="rev-modal-name">{{ $row['employee']->full_name }}</div>
                    <div class="rev-modal-title">Revenue Breakdown &mdash; Per Student</div>
                </div>
                <button type="button" class="rev-modal-close" onclick="closeRev({{ $row['employee']->employee_id }})">&times;</button>
            </div>
            <div class="rev-filter" data-emp="{{ $row['employee']->employee_id }}">
                <div class="rev-tabs">
                    <button type="button" class="rev-tab {{ $filterType==='patch'?'active':'' }}" data-f="patch">Patch</button>
                    <button type="button" class="rev-tab {{ $filterType==='month'?'active':'' }}" data-f="month">Month</button>
                    <button type="button" class="rev-tab {{ $filterType==='week'?'active':'' }}" data-f="week">Week</button>
                    <button type="button" class="rev-tab {{ $filterType==='day'?'active':'' }}" data-f="day">Day</button>
                </div>
                <div class="rev-pickers">
                    <select class="rev-sel rev-p-patch" style="{{ $filterType==='patch'?'':'display:none;' }}">
                        <option value="">&mdash; Select Patch &mdash;</option>
                        @foreach($patches as $p)
                        <option value="{{ $p->patch_id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <input type="month" class="rev-sel rev-p-month" value="{{ $month }}" style="{{ $filterType==='month'?'':'display:none;' }}">
                    <input type="date" class="rev-sel rev-p-day" value="{{ $day }}" style="{{ in_array($filterType,['week','day'])?'':'display:none;' }}">
                </div>
            </div>
            <div class="rev-modal-body">
                <table class="rev-tbl">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th class="num">Deposit</th>
                            <th class="num">Test</th>
                            <th class="num">Material</th>
                            <th>Material Type</th>
                            <th class="num">Total Revenue</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody class="rev-tbody">
                        @forelse($row['revenue_rows'] as $rev)
                        <tr>
                            <td>
                                <div class="rev-std">
                                    <div class="rev-std-avatar">{{ strtoupper(substr($rev['student_name'],0,1)) }}</div>
                                    <span class="rev-std-name">{{ $rev['student_name'] }}</span>
                                </div>
                            </td>
                            <td class="money" style="color:var(--text);">{{ $rev['course'] }}</td>
                            <td class="num money">{{ number_format($rev['deposit']) }} LE</td>
                            <td class="num money">
                                @if(($rev['test_fee'] ?? 0) > 0)
                                    {{ number_format($rev['test_fee']) }} LE
                                @else
                                    <span style="color:var(--faint);">&mdash;</span>
                                @endif
                            </td>
                            <td class="num money">{{ number_format($rev['material']) }} LE</td>
                            <td>
                                <span class="rev-badge {{ $rev['material'] > 0 ? 'rev-badge-shared' : 'rev-badge-direct' }}">
                                    {{ $rev['material'] > 0 ? 'Shared' : 'Direct' }}
                                </span>
                            </td>
                            <td class="num total">{{ number_format($rev['total']) }} LE</td>
                            <td style="color:var(--faint);font-size:11px;white-space:nowrap;">{{ $rev['date'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8"><div class="rev-empty">No revenue recorded for this period.</div></td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="rev-tfoot">
                        @if($row['revenue_rows']->count() > 0)
                        <tr>
                            <td colspan="2" class="rev-tfoot-label">Total ({{ $row['revenue_rows']->count() }} {{ \Illuminate\Support\Str::plural('student', $row['revenue_rows']->count()) }})</td>
                            <td class="num rev-tfoot-num">{{ number_format($row['revenue_rows']->sum('deposit')) }} LE</td>
                            <td class="num rev-tfoot-num">{{ number_format($row['revenue_rows']->sum('test_fee')) }} LE</td>
                            <td class="num rev-tfoot-num">{{ number_format($row['revenue_rows']->sum('material')) }} LE</td>
                            <td></td>
                            <td class="num rev-tfoot-total">{{ number_format($row['revenue_rows']->sum('total')) }} LE</td>
                            <td></td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>
var REV_URL = "{{ route($csRevRoute) }}";
function openRev(id){var m=document.getElementById('revModal-'+id);if(m){m.classList.add('show');document.body.style.overflow='hidden';}}
function closeRev(id){var m=document.getElementById('revModal-'+id);if(m){m.classList.remove('show');document.body.style.overflow='';}}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){document.querySelectorAll('.rev-modal.show').forEach(function(m){m.classList.remove('show');});document.body.style.overflow='';}});

function revEsc(s){return String(s==null?'':s).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];});}
function revFmt(n){return Number(n||0).toLocaleString('en-US',{maximumFractionDigits:0});}
function revRenderRows(rows){
    if(!rows||!rows.length){return '<tr><td colspan="8"><div class="rev-empty">No revenue recorded for this period.</div></td></tr>';}
    return rows.map(function(r){
        var testCell = (Number(r.test_fee)>0) ? (revFmt(r.test_fee)+' LE') : '<span style="color:var(--faint);">&mdash;</span>';
        var shared = Number(r.material)>0;
        var badge = '<span class="rev-badge '+(shared?'rev-badge-shared':'rev-badge-direct')+'">'+(shared?'Shared':'Direct')+'</span>';
        var initial = revEsc(String(r.student_name||'-').charAt(0).toUpperCase());
        return '<tr>'+
            '<td><div class="rev-std"><div class="rev-std-avatar">'+initial+'</div><span class="rev-std-name">'+revEsc(r.student_name)+'</span></div></td>'+
            '<td class="money" style="color:var(--text);">'+revEsc(r.course)+'</td>'+
            '<td class="num money">'+revFmt(r.deposit)+' LE</td>'+
            '<td class="num money">'+testCell+'</td>'+
            '<td class="num money">'+revFmt(r.material)+' LE</td>'+
            '<td>'+badge+'</td>'+
            '<td class="num total">'+revFmt(r.total)+' LE</td>'+
            '<td style="color:var(--faint);font-size:11px;white-space:nowrap;">'+revEsc(r.date)+'</td>'+
        '</tr>';
    }).join('');
}
function revRenderFoot(t){
    if(!t||!t.count){return '';}
    var plural = (Number(t.count)===1)?'student':'students';
    return '<tr>'+
        '<td colspan="2" class="rev-tfoot-label">Total ('+t.count+' '+plural+')</td>'+
        '<td class="num rev-tfoot-num">'+revFmt(t.deposit)+' LE</td>'+
        '<td class="num rev-tfoot-num">'+revFmt(t.test_fee)+' LE</td>'+
        '<td class="num rev-tfoot-num">'+revFmt(t.material)+' LE</td>'+
        '<td></td>'+
        '<td class="num rev-tfoot-total">'+revFmt(t.total)+' LE</td>'+
        '<td></td>'+
    '</tr>';
}
function revApply(box){
    var emp = box.getAttribute('data-emp');
    var modal = document.getElementById('revModal-'+emp);
    if(!modal){return;}
    var tbody = modal.querySelector('.rev-tbody');
    var tfoot = modal.querySelector('.rev-tfoot');
    var active = box.querySelector('.rev-tab.active');
    var f = active ? active.getAttribute('data-f') : 'month';
    var params = 'employee_id='+encodeURIComponent(emp)+'&filter='+encodeURIComponent(f);
    if(f==='month'){params += '&month='+encodeURIComponent(box.querySelector('.rev-p-month').value);}
    else if(f==='week'||f==='day'){params += '&day='+encodeURIComponent(box.querySelector('.rev-p-day').value);}
    else if(f==='patch'){
        var pv = box.querySelector('.rev-p-patch').value;
        if(!pv){
            tbody.innerHTML = '<tr><td colspan="8"><div class="rev-empty">Select a patch to view its revenue.</div></td></tr>';
            tfoot.innerHTML = '';
            return;
        }
        params += '&patch='+encodeURIComponent(pv);
    }
    tbody.innerHTML = '<tr><td colspan="8"><div class="rev-empty">Loading&hellip;</div></td></tr>';
    tfoot.innerHTML = '';
    fetch(REV_URL+'?'+params, {headers:{'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
        .then(function(r){return r.json();})
        .then(function(d){
            tbody.innerHTML = revRenderRows(d.rows);
            tfoot.innerHTML = revRenderFoot(d.totals);
        })
        .catch(function(){
            tbody.innerHTML = '<tr><td colspan="8"><div class="rev-empty">Could not load revenue. Please try again.</div></td></tr>';
            tfoot.innerHTML = '';
        });
}
document.addEventListener('click',function(e){
    var tab = e.target.closest('.rev-tab');
    if(!tab){return;}
    var box = tab.closest('.rev-filter');
    box.querySelectorAll('.rev-tab').forEach(function(t){t.classList.remove('active');});
    tab.classList.add('active');
    var f = tab.getAttribute('data-f');
    box.querySelector('.rev-p-patch').style.display = (f==='patch')?'':'none';
    box.querySelector('.rev-p-month').style.display = (f==='month')?'':'none';
    box.querySelector('.rev-p-day').style.display = (f==='week'||f==='day')?'':'none';
    revApply(box);
});
document.addEventListener('change',function(e){
    var box = e.target.closest('.rev-filter');
    if(box && e.target.classList.contains('rev-sel')){revApply(box);}
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartLabels = @json($dailyData->pluck('day'));
const chartValues = @json($dailyData->pluck('total'));

new Chart(document.getElementById('mainChart'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Revenue (LE)',
            data: chartValues,
            backgroundColor: 'rgba(27,79,168,0.6)',
            borderRadius: 4,
            borderSkipped: false,
            hoverBackgroundColor: 'rgba(27,79,168,0.85)',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + Number(ctx.raw).toLocaleString('en-EG') + ' LE'
                }
            }
        },
        scales: {
            y: {
                grid: { color: 'rgba(27,79,168,0.05)' },
                ticks: { font: { size: 11 }, callback: v => Number(v).toLocaleString('en-EG') }
            },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});
</script>
@endsection
