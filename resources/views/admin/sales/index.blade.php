@extends('admin.layouts.app')
@section('title', 'Sales Revenue')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root {
    --blue:#1B4FA8; --blue-light:rgba(27,79,168,0.08);
    --orange:#F5911E; --orange-light:rgba(245,145,30,0.08);
    --green:#059669; --green-light:rgba(5,150,105,0.08);
    --red:#DC2626; --red-light:rgba(220,38,38,0.06);
    --purple:#7F77DD; --purple-light:rgba(127,119,221,0.08);
    --border:rgba(27,79,168,0.1);
    --bg:#F8F6F2; --card:#fff;
    --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
}

* { box-sizing: border-box; }

.sales-page { background:var(--bg); min-height:100vh; padding:40px 32px; font-family:'DM Sans',sans-serif; color:var(--text); }

/* ── Header ── */
.page-eyebrow { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:4px; }
.page-title { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:5px; color:var(--blue); margin:0 0 24px; }

/* ── Filter ── */
.filter-bar { display:flex; align-items:center; gap:10px; margin-bottom:24px; flex-wrap:wrap; }
.filter-tab { padding:7px 20px; border-radius:4px; font-size:10px; letter-spacing:2px; text-transform:uppercase; text-decoration:none; border:1px solid; transition:all 0.2s; font-family:'DM Sans',sans-serif; white-space:nowrap; }
.filter-tab.active { background:var(--blue); color:#fff; border-color:var(--blue); }
.filter-tab:not(.active) { color:var(--muted); border-color:var(--border); background:var(--card); }
.filter-tab:not(.active):hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }
.filter-input { font-family:'DM Sans',sans-serif; font-size:12px; padding:7px 12px; border:1px solid var(--border); border-radius:4px; background:var(--card); color:var(--text); outline:none; }
.filter-input:focus { border-color:var(--blue); }
.filter-sep { width:1px; height:24px; background:var(--border); }

/* ── Overall KPIs ── */
.kpi-strip { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:28px; }
@media(max-width:1000px) { .kpi-strip { grid-template-columns:repeat(3,1fr); } }
.kpi-card { background:var(--card); border:1px solid var(--border); border-radius:8px; padding:18px 20px; position:relative; overflow:hidden; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--kc, var(--blue)); }
.kpi-eyebrow { font-size:8px; letter-spacing:3px; text-transform:uppercase; color:var(--faint); margin-bottom:8px; }
.kpi-value { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:2px; color:var(--kc, var(--blue)); line-height:1; }
.kpi-sub { font-size:10px; color:var(--faint); margin-top:4px; }
.prog { background:#F0F0F0; border-radius:3px; height:4px; margin-top:10px; overflow:hidden; }
.prog-fill { height:4px; border-radius:3px; background:var(--kc, var(--blue)); transition:width .6s ease; }

/* ── Section Label ── */
.sec-label { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:14px; padding-bottom:9px; border-bottom:1px solid rgba(245,145,30,0.15); display:block; margin-top:4px; }

/* ── CS Table ── */
.cs-table-card { background:var(--card); border:1px solid var(--border); border-radius:8px; overflow:hidden; margin-bottom:28px; box-shadow:0 2px 12px rgba(27,79,168,0.04); }
.cs-tbl { width:100%; border-collapse:collapse; }
.cs-tbl thead th { padding:12px 16px; font-size:8px; letter-spacing:3px; text-transform:uppercase; color:var(--faint); text-align:left; font-weight:500; background:rgba(27,79,168,0.02); border-bottom:1px solid var(--border); white-space:nowrap; }
.cs-tbl thead th:last-child { text-align:center; }
.cs-tbl tbody tr { border-bottom:1px solid rgba(27,79,168,0.04); transition:background 0.15s; }
.cs-tbl tbody tr:last-child { border-bottom:none; }
.cs-tbl tbody tr:hover { background:rgba(27,79,168,0.02); }
.cs-tbl td { padding:14px 16px; font-size:13px; color:var(--muted); vertical-align:middle; }

.cs-avatar { width:34px; height:34px; border-radius:50%; background:var(--blue-light); color:var(--blue); display:inline-flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:15px; flex-shrink:0; }
.cs-name { font-weight:600; color:var(--text); font-size:13px; }
.cs-branch { font-size:10px; color:var(--faint); margin-top:2px; }

.rank-badge { width:24px; height:24px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:13px; flex-shrink:0; }
.rank-1 { background:rgba(245,145,30,0.15); color:#C47010; }
.rank-2 { background:rgba(122,138,154,0.12); color:#5A6A7A; }
.rank-3 { background:rgba(180,140,90,0.12); color:#8B6914; }
.rank-n { background:rgba(27,79,168,0.06); color:var(--faint); }

.money { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:1px; color:var(--text); }
.money-green { color:var(--green); }
.money-orange { color:var(--orange); }
.money-red { color:var(--red); }

.mini-prog-wrap { display:flex; align-items:center; gap:8px; }
.mini-prog { flex:1; background:#F0F0F0; border-radius:3px; height:5px; overflow:hidden; min-width:60px; }
.mini-prog-fill { height:5px; border-radius:3px; transition:width .5s ease; }
.mini-prog-pct { font-size:11px; font-family:'Bebas Neue',sans-serif; letter-spacing:1px; white-space:nowrap; }

.stat-pill { display:inline-flex; align-items:center; gap:4px; font-size:10px; letter-spacing:1px; padding:2px 8px; border-radius:20px; }
.pill-blue { background:var(--blue-light); color:var(--blue); }
.pill-orange { background:var(--orange-light); color:#C47010; }

.top-badge { display:inline-flex; align-items:center; gap:4px; font-size:8px; letter-spacing:2px; text-transform:uppercase; padding:2px 8px; border-radius:3px; background:var(--orange-light); color:#C47010; border:1px solid rgba(245,145,30,0.2); }

/* ── Chart ── */
.chart-card { background:var(--card); border:1px solid var(--border); border-radius:8px; padding:22px 24px; margin-bottom:28px; box-shadow:0 2px 12px rgba(27,79,168,0.04); }
.chart-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
.chart-title { font-size:11px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); }
.chart-wrap { position:relative; height:200px; }

/* ── Per-CS Detail Cards ── */
.cs-detail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:28px; }
@media(max-width:900px) { .cs-detail-grid { grid-template-columns:1fr; } }
.cs-detail-card { background:var(--card); border:1px solid var(--border); border-radius:8px; overflow:hidden; position:relative; box-shadow:0 2px 8px rgba(27,79,168,0.04); }
.cs-detail-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg, var(--orange), var(--blue)); }
.cs-detail-header { padding:16px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--border); }
.cs-detail-stats { display:grid; grid-template-columns:repeat(3,1fr); }
.cs-detail-stat { padding:14px 16px; border-right:1px solid var(--border); text-align:center; }
.cs-detail-stat:last-child { border-right:none; }
.cs-detail-stat-label { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); margin-bottom:5px; }
.cs-detail-stat-val { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px; color:var(--text); line-height:1; }
.cs-detail-prog { padding:12px 16px 16px; }
.cs-detail-prog-label { font-size:9px; letter-spacing:1px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; display:flex; justify-content:space-between; }

@media(max-width:768px) { .sales-page { padding:18px 14px; } .kpi-strip { grid-template-columns:1fr 1fr; } }
</style>

<div class="sales-page">

    <div class="page-eyebrow">Admin Panel</div>
    <h1 class="page-title">Sales Revenue</h1>

    {{-- ── FILTER BAR ── --}}
    <div class="filter-bar">
        <a href="{{ route('admin.sales.index', ['filter'=>'month','month'=>$month]) }}"
           class="filter-tab {{ $filterType==='month'?'active':'' }}">By Month</a>
        <a href="{{ route('admin.sales.index', ['filter'=>'week','day'=>$day]) }}"
           class="filter-tab {{ $filterType==='week'?'active':'' }}">By Week</a>
        <a href="{{ route('admin.sales.index', ['filter'=>'day','day'=>$day]) }}"
           class="filter-tab {{ $filterType==='day'?'active':'' }}">By Day</a>

        <div class="filter-sep"></div>

        @if($filterType === 'month')
        <input type="month" value="{{ $month }}" class="filter-input"
               onchange="window.location.href='{{ route('admin.sales.index') }}?filter=month&month='+this.value">
        @elseif($filterType === 'week')
        <input type="week" value="{{ \Carbon\Carbon::parse($day)->format('Y-\WW') }}" class="filter-input"
               onchange="
                   const[y,w]=this.value.split('-W');
                   const d=new Date(y,0,1+(w-1)*7);
                   window.location.href='{{ route('admin.sales.index') }}?filter=week&day='+d.toISOString().split('T')[0]">
        @else
        <input type="date" value="{{ $day }}" class="filter-input" style="color-scheme:light;"
               onchange="window.location.href='{{ route('admin.sales.index') }}?filter=day&day='+this.value">
        @endif

        <span style="font-size:11px;color:var(--faint);margin-left:4px;">
            @if($filterType==='month') {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
            @elseif($filterType==='week') Week of {{ \Carbon\Carbon::parse($day)->startOfWeek()->format('d M') }} – {{ \Carbon\Carbon::parse($day)->endOfWeek()->format('d M Y') }}
            @else {{ \Carbon\Carbon::parse($day)->format('l, d M Y') }}
            @endif
        </span>
    </div>

    {{-- ── OVERALL KPIs ── --}}
    <span class="sec-label">Overall Performance</span>
    <div class="kpi-strip">
        <div class="kpi-card" style="--kc:var(--blue)">
            <div class="kpi-eyebrow">Total Target</div>
            <div class="kpi-value">{{ number_format($overallKpis['total_target']) }}</div>
            <div class="kpi-sub">LE across all CS</div>
        </div>
        <div class="kpi-card" style="--kc:var(--green)">
            <div class="kpi-eyebrow">Total Achieved</div>
            <div class="kpi-value">{{ number_format($overallKpis['total_achieved']) }}</div>
            <div class="kpi-sub">LE collected</div>
            @if($overallKpis['total_target'] > 0)
            <div class="prog">
                <div class="prog-fill" style="width:{{ min(100,round($overallKpis['total_achieved']/$overallKpis['total_target']*100)) }}%"></div>
            </div>
            @endif
        </div>
        <div class="kpi-card" style="--kc:var(--orange)">
            <div class="kpi-eyebrow">Avg Achievement</div>
            <div class="kpi-value">{{ round($overallKpis['avg_achievement'],1) }}%</div>
            <div class="kpi-sub">across CS team</div>
        </div>
        <div class="kpi-card" style="--kc:var(--purple)">
            <div class="kpi-eyebrow">Total Registrations</div>
            <div class="kpi-value">{{ $overallKpis['total_registrations'] }}</div>
            <div class="kpi-sub">students enrolled</div>
        </div>
        <div class="kpi-card" style="--kc:var(--orange)">
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

    {{-- ── REVENUE CHART ── --}}
    <span class="sec-label">Revenue Over Time — All CS Combined</span>
    <div class="chart-card">
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

    {{-- ── CS LEADERBOARD TABLE ── --}}
    <span class="sec-label">CS Leaderboard</span>
    <div class="cs-table-card">
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:var(--faint);font-size:13px;">
                            No CS employees found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($rows->count() > 0)
                <tfoot>
                    <tr style="border-top:2px solid var(--border);background:rgba(27,79,168,0.02);">
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
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── PER-CS DETAIL CARDS ── --}}
    <span class="sec-label">Individual Breakdown</span>
    <div class="cs-detail-grid">
        @foreach($rows as $i => $row)
        @php $pct = $row['percentage']; @endphp
        <div class="cs-detail-card">
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
            </div>
            <div class="cs-detail-prog">
                <div class="cs-detail-prog-label">
                    <span>Achievement Progress</span>
                    <span style="color:{{ $pct>=100?'var(--green)':($pct>=60?'var(--blue)':'var(--orange)') }};font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;">
                        {{ $pct }}%
                    </span>
                </div>
                <div style="background:#F0F0F0;border-radius:4px;height:6px;overflow:hidden;">
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
        </div>
        @endforeach
    </div>

</div>

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