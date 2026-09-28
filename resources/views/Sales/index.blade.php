@extends('layouts.leads')
@section('title', 'Sales Table')

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
        --dark:#0F1F3D; --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:rgba(255,255,255,0.72); --border:rgba(255,255,255,0.6);
        --line:rgba(27,79,168,0.08); --glass-sh:0 12px 34px -14px rgba(23,45,90,0.2);
    }
    * { box-sizing:border-box; }

    .sl-page {
        min-height:100vh; padding:40px 34px 52px; color:var(--text); font-family:'DM Sans',sans-serif;
        background:#F8F6F2;
    }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .sl-header {
        margin:0 auto 20px;
        display:flex; align-items:flex-end; justify-content:space-between;
        flex-wrap:wrap; gap:16px;
    }
    .sl-header-left { position:relative; z-index:1; }
    .sl-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:7px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .sl-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--blue); box-shadow:0 0 8px var(--orange); }
    .sl-title { font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0; }
    .sl-sub { font-size:12px; color:var(--muted); margin-top:6px; letter-spacing:0.3px; }
    .sl-emp-badge { position:relative; z-index:1; display:flex; align-items:center; gap:10px; background:var(--card); border:1px solid var(--border); border-radius:14px; padding:10px 16px; box-shadow:var(--glass-sh); }
    .sl-emp-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,var(--orange),#ffb45f); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:16px; color:#fff; }
    .sl-emp-name { font-size:13px; font-weight:600; color:var(--text); }
    .sl-emp-role { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); }

    .sl-wrap { margin:0 auto; }
    .sec-label { display:flex; align-items:center; gap:10px; font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--text); margin:26px 0 14px; font-weight:700; }
    .sec-label::before { content:''; width:20px; height:3px; border-radius:3px; background:linear-gradient(90deg, var(--orange), var(--blue)); flex-shrink:0; }

    .filter-row { display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:6px; }
    .filter-tabs { display:inline-flex; gap:4px; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--border); border-radius:14px; padding:4px; box-shadow:var(--glass-sh); }
    .filter-tab { padding:9px 20px; border-radius:11px; font-size:10px; letter-spacing:1.5px; text-transform:uppercase; text-decoration:none; transition:all 0.2s; font-weight:600; color:var(--muted); }
    .filter-tab.active { background:linear-gradient(120deg,var(--blue),var(--blue-2)); color:#fff; box-shadow:0 6px 16px rgba(27,79,168,0.28); }
    .filter-tab:not(.active):hover { color:var(--blue); text-decoration:none; }
    .filter-picker { display:inline-flex; align-items:center; gap:10px; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--border); border-radius:14px; padding:8px 16px; box-shadow:var(--glass-sh); }
    .filter-picker label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; }
    .filter-sel { font-family:'DM Sans',sans-serif; font-size:13px; padding:7px 11px; border:1px solid rgba(27,79,168,0.1); border-radius:10px; background:rgba(255,255,255,0.6); color:var(--text); cursor:pointer; outline:none; color-scheme:light; }
    .filter-sel:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }

    .kpi-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; }
    @media (max-width:1000px){ .kpi-grid{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:600px){ .kpi-grid{ grid-template-columns:1fr 1fr; } }
    .kpi-card { background:var(--card); border:1px solid var(--border); border-radius:18px; padding:20px; position:relative; overflow:hidden; box-shadow:var(--glass-sh); transition:transform 0.2s, box-shadow 0.2s; }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kc,var(--blue)); }
    .kpi-card:hover { transform:translateY(-3px); box-shadow:0 18px 42px -14px rgba(23,45,90,0.28); }
    .kpi-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:8px; }
    .kpi-val { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:1px; line-height:0.9; color:var(--kc,var(--blue)); }
    .kpi-na { font-family:'Bebas Neue',sans-serif; font-size:24px; letter-spacing:1px; color:var(--faint); line-height:1; padding:5px 0; }
    .kpi-sub { font-size:10px; color:var(--faint); margin-top:5px; }
    .prog { height:5px; background:var(--line); border-radius:3px; overflow:hidden; margin-top:10px; }
    .prog-fill { height:100%; background:var(--green); border-radius:3px; transition:width 0.7s cubic-bezier(0.16,1,0.3,1); }

    .fu-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; }
    @media (max-width:900px){ .fu-grid{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:520px){ .fu-grid{ grid-template-columns:1fr 1fr; } }
    .fu-card { background:var(--card); border:1px solid var(--border); border-radius:16px; padding:16px; text-align:center; box-shadow:var(--glass-sh); }
    .fu-val { font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:1px; color:var(--blue); line-height:1; }
    .fu-label { font-size:9px; letter-spacing:1px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-top:6px; }

    .tbl-card { background:rgba(255,255,255,0.72); border:1px solid var(--border); border-radius:20px; overflow:hidden; box-shadow:var(--glass-sh); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:820px; }
    .tbl thead th { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); padding:15px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:700; background:rgba(255,255,255,0.55); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.06); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:rgba(27,79,168,0.04); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }
    .money { color:var(--muted); font-variant-numeric:tabular-nums; }
    .total { font-weight:700; color:var(--blue); font-variant-numeric:tabular-nums; }

    .std-cell { display:flex; align-items:center; gap:11px; }
    .std-avatar { width:34px; height:34px; border-radius:11px; background:rgba(27,79,168,0.1); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:14px; color:var(--blue); flex-shrink:0; }
    .std-name { font-weight:600; color:var(--text); }

    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:10px; font-weight:600; letter-spacing:0.3px; white-space:nowrap; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .badge-direct { background:var(--blue-l); color:var(--blue); }
    .badge-shared { background:var(--orange-l); color:var(--orange-dk); }

    .tbl tfoot td { padding:15px 16px; border-top:2px solid var(--border); background:rgba(255,255,255,0.5); font-variant-numeric:tabular-nums; }
    .tfoot-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; }
    .tfoot-num { text-align:right; font-weight:600; color:var(--text); }
    .tfoot-total { text-align:right; font-family:'Bebas Neue',sans-serif; font-size:18px; color:var(--blue); letter-spacing:1px; }

    .tbl-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .tbl-empty svg { opacity:0.35; margin-bottom:12px; }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-bottom:4px; }

    .chart-card { background:var(--card); border:1px solid var(--border); border-radius:20px; padding:24px; box-shadow:var(--glass-sh); }
    .chart-wrap { height:300px; }

    @media (max-width:600px){ .sl-page{ padding:20px 14px 36px; } .sl-title{ font-size:34px; } }
</style>

<div class="sl-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="sl-header">
        <div class="sl-header-left">
            <div class="sl-eyebrow">Sales Performance</div>
            <h1 class="sl-title">My Sales Table</h1>
            <div class="sl-sub">Track your target, revenue, and conversions</div>
        </div>
        <!-- @if($employee)
        <div class="sl-emp-badge">
            <div class="sl-emp-avatar">{{ strtoupper(substr($employee->full_name, 0, 1)) }}</div>
            <div>
                <div class="sl-emp-name">{{ $employee->full_name }}</div>
                <div class="sl-emp-role">Customer Service</div>
            </div>
        </div>
        @endif -->
    </div>

    <div class="sl-wrap">

        <span class="sec-label">Filter Period</span>
        <div class="filter-row">
            <div class="filter-tabs">
                <a href="{{ route('sales.index', ['filter' => 'month', 'month' => $month]) }}"
                   class="filter-tab {{ $filterType === 'month' ? 'active' : '' }}">By Month</a>
                <a href="{{ route('sales.index', ['filter' => 'week', 'day' => $day]) }}"
                   class="filter-tab {{ $filterType === 'week' ? 'active' : '' }}">By Week</a>
                <a href="{{ route('sales.index', ['filter' => 'day', 'day' => $day]) }}"
                   class="filter-tab {{ $filterType === 'day' ? 'active' : '' }}">By Day</a>
            </div>

            @if($filterType === 'week')
            <div class="filter-picker">
                <label>Week</label>
                <input type="week" value="{{ \Carbon\Carbon::parse($day)->format('Y-\WW') }}" class="filter-sel"
                    onchange="
                        var y=this.value.split('-W')[0];
                        var w=this.value.split('-W')[1];
                        var d=new Date(y,0,(w-1)*7+1);
                        window.location.href='{{ route('sales.index') }}?filter=week&day='+d.toISOString().slice(0,10);">
            </div>
            @elseif($filterType === 'day')
            <div class="filter-picker">
                <label>Date</label>
                <input type="date" value="{{ $day }}" class="filter-sel"
                       onchange="window.location.href='{{ route('sales.index') }}?filter=day&day='+this.value">
            </div>
            @else
            <div class="filter-picker">
                <label>Month</label>
                <input type="month" value="{{ $month }}" class="filter-sel"
                       onchange="window.location.href='{{ route('sales.index') }}?filter=month&month='+this.value">
            </div>
            @endif
        </div>

        <span class="sec-label">Target & Revenue</span>
        <div class="kpi-grid">
            <div class="kpi-card" style="--kc:var(--blue)">
                <div class="kpi-label">Monthly Target</div>
                @if($kpis['target'] > 0)
                    <div class="kpi-val">{{ number_format($kpis['target']) }}</div>
                    <div class="kpi-sub">LE — {{ \Carbon\Carbon::parse($kpis['target_month'].'-01')->format('M Y') }}</div>
                @else
                    <div class="kpi-na">No Target</div>
                    <div class="kpi-sub">Not set for this month</div>
                @endif
            </div>

            <div class="kpi-card" style="--kc:var(--green)">
                <div class="kpi-label">Achieved</div>
                <div class="kpi-val">{{ number_format($kpis['achieved']) }}</div>
                <div class="kpi-sub">LE
                    @if($filterType === 'day') today
                    @elseif($filterType === 'week') this week
                    @else this month
                    @endif
                </div>
                @if($kpis['percentage'] !== null)
                <div class="prog"><div class="prog-fill" style="width:{{ min($kpis['percentage'],100) }}%"></div></div>
                @endif
            </div>

            <div class="kpi-card" style="--kc:var(--orange)">
                <div class="kpi-label">Remaining</div>
                @if($kpis['remaining'] !== null)
                    <div class="kpi-val">{{ number_format($kpis['remaining']) }}</div>
                    <div class="kpi-sub">LE to monthly target</div>
                @else
                    <div class="kpi-na">N/A</div>
                    <div class="kpi-sub">No target set</div>
                @endif
            </div>

            <div class="kpi-card" style="--kc:var(--purple)">
                <div class="kpi-label">Achievement</div>
                @if($kpis['percentage'] !== null)
                    <div class="kpi-val">{{ $kpis['percentage'] }}%</div>
                    <div class="kpi-sub">of monthly target</div>
                @else
                    <div class="kpi-na">N/A</div>
                    <div class="kpi-sub">No target set</div>
                @endif
            </div>

            <div class="kpi-card" style="--kc:var(--green-dk)">
                <div class="kpi-label">Registrations</div>
                <div class="kpi-val">{{ $kpis['registrations'] }}</div>
                <div class="kpi-sub">students
                    @if($filterType === 'day') today
                    @elseif($filterType === 'week') this week
                    @else this month
                    @endif
                </div>
            </div>

            <div class="kpi-card" style="--kc:var(--orange)">
                <div class="kpi-label">Calls Made</div>
                <div class="kpi-val">{{ $kpis['calls_made'] ?? 0 }}</div>
                <div class="kpi-sub">call-again
                    @if($filterType === 'day') today
                    @elseif($filterType === 'week') this week
                    @else this month
                    @endif
                </div>
            </div>
        </div>

        

        <span class="sec-label">Revenue Breakdown — Per Student</span>
        <div class="tbl-card">
            <div class="tbl-scroll">
                <table class="tbl">
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
                    <tbody>
                        @forelse($revenueRows as $row)
                        <tr>
                            <td>
                                <div class="std-cell">
                                    <div class="std-avatar">{{ strtoupper(substr($row['student_name'], 0, 1)) }}</div>
                                    <span class="std-name">{{ $row['student_name'] }}</span>
                                </div>
                            </td>
                            <td class="money" style="color:var(--text);">{{ $row['course'] }}</td>
                            <td class="num money">{{ number_format($row['deposit']) }} LE</td>
                            <td class="num money">
                                @if(($row['test_fee'] ?? 0) > 0)
                                    {{ number_format($row['test_fee']) }} LE
                                @else
                                    <span style="color:var(--faint);">—</span>
                                @endif
                            </td>
                            <td class="num money">{{ number_format($row['material']) }} LE</td>
                                                        <td>
                                <span class="badge {{ $row['material'] > 0 ? 'badge-shared' : 'badge-direct' }}">
                                    {{ $row['material'] > 0 ? 'Shared' : 'Direct' }}
                                </span>
                            </td>
                            <td class="num total">{{ number_format($row['total']) }} LE</td>
                            <td style="color:var(--faint);font-size:11px;white-space:nowrap;">{{ $row['date'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="tbl-empty">
                                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                                    <div class="tbl-empty-title">No Revenue This Period</div>
                                    <div style="font-size:12px;">Registrations you make will appear here.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($revenueRows->count() > 0)
                    <tfoot>
                        <tr>
                            <td colspan="2" class="tfoot-label">Total ({{ $revenueRows->count() }} {{ Str::plural('student', $revenueRows->count()) }})</td>
                            <td class="num tfoot-num">{{ number_format($revenueRows->sum('deposit')) }} LE</td>
                            <td class="num tfoot-num">{{ number_format($revenueRows->sum('test_fee')) }} LE</td>
                            <td class="num tfoot-num">{{ number_format($revenueRows->sum('material')) }} LE</td>
                            <td class="num tfoot-num"></td>
                            <td class="num tfoot-total">{{ number_format($revenueRows->sum('total')) }} LE</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <span class="sec-label">
            @if($filterType === 'day')
                Revenue — {{ \Carbon\Carbon::parse($day)->format('d M Y') }}
            @elseif($filterType === 'week')
                Revenue — Week of {{ \Carbon\Carbon::parse($day)->startOfWeek()->format('d M Y') }}
            @else
                Daily Revenue — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
            @endif
        </span>
        <div class="chart-card">
            <div class="chart-wrap">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const rawLabels = @json($dailyRevenue['labels']);
    const rawValues = @json($dailyRevenue['values']);

    // Format labels as short dates (d M) for readability
    const labels = rawLabels.map(function(d){
        const dt = new Date(d);
        return isNaN(dt) ? d : dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
    });
    const values = rawValues.map(Number);

    const ctx = document.getElementById('dailyChart');
    const grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(27,79,168,0.85)');
    grad.addColorStop(1, 'rgba(45,111,219,0.45)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (LE)',
                data: values,
                backgroundColor: grad,
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 46,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F1F3D',
                    padding: 12,
                    titleFont: { size: 12 },
                    bodyFont: { size: 13, weight: '600' },
                    callbacks: {
                        label: function(c){ return Number(c.parsed.y).toLocaleString() + ' LE'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(27,79,168,0.05)' },
                    ticks: {
                        font: { size: 11 },
                        color: '#7A8A9A',
                        callback: function(v){ return Number(v).toLocaleString(); }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#7A8A9A' }
                }
            }
        }
    });
})();
</script>

@endsection