@extends('layouts.leads')
@section('title', 'Sales Table')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.sales-page { background:#F8F6F2; min-height:100vh; padding:40px 32px; font-family:'DM Sans',sans-serif; color:#1A2A4A; }
.page-eyebrow { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:4px; }
.page-title { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:4px; color:#1B4FA8; margin-bottom:20px; }

/* Filter tabs */
.filter-tabs { display:flex; gap:8px; margin-bottom:16px; }
.filter-tab {
    padding:7px 20px; border-radius:4px; font-size:10px; letter-spacing:2px;
    text-transform:uppercase; text-decoration:none; border:1px solid;
    transition:all 0.2s; font-family:'DM Sans',sans-serif;
}
.filter-tab.active { background:#1B4FA8; color:#fff; border-color:#1B4FA8; }
.filter-tab:not(.active) { color:#7A8A9A; border-color:rgba(27,79,168,0.2); background:#fff; }
.filter-tab:not(.active):hover { border-color:#1B4FA8; color:#1B4FA8; text-decoration:none; }

/* Filter bar */
.filter-bar { display:flex; align-items:center; gap:12px; margin-bottom:24px; flex-wrap:wrap; }
.filter-bar label { font-size:11px; color:#7A8A9A; }
.filter-sel {
    font-family:'DM Sans',sans-serif; font-size:12px; padding:8px 14px;
    border:1px solid rgba(27,79,168,0.15); border-radius:4px;
    background:#fff; color:#1A2A4A; cursor:pointer; outline:none;
}
.filter-sel:focus { border-color:#1B4FA8; }

/* KPI */
.kpi-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:28px; }
@media(max-width:900px) { .kpi-grid { grid-template-columns:repeat(3,1fr); } }
@media(max-width:600px) { .kpi-grid { grid-template-columns:1fr 1fr; } }
.kpi-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:16px; position:relative; overflow:hidden; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--kc,#1B4FA8); }
.kpi-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7A8A9A; margin-bottom:6px; }
.kpi-val { font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:2px; color:var(--kc,#1B4FA8); line-height:1; }
.kpi-sub { font-size:10px; color:#AAB8C8; margin-top:4px; }
.kpi-na { font-family:'Bebas Neue',sans-serif; font-size:20px; color:#D0D8E4; letter-spacing:2px; }
.prog { background:#F0F0F0; border-radius:4px; height:4px; margin-top:10px; overflow:hidden; }
.prog-fill { height:4px; border-radius:4px; background:var(--kc); transition:width .5s; }

/* Section label */
.sec-label { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:14px; margin-top:8px; padding-bottom:9px; border-bottom:1px solid rgba(245,145,30,0.15); display:block; }

/* Followup */
.fu-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:28px; }
@media(max-width:900px) { .fu-grid { grid-template-columns:repeat(3,1fr); } }
.fu-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:14px; text-align:center; }
.fu-val { font-family:'Bebas Neue',sans-serif; font-size:24px; color:#1A2A4A; }
.fu-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#7A8A9A; margin-top:4px; }

/* Table */
.tbl-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; overflow:hidden; margin-bottom:28px; }
.tbl { width:100%; border-collapse:collapse; }
.tbl thead th { padding:11px 14px; font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7A8A9A; text-align:left; font-weight:500; background:rgba(27,79,168,0.02); border-bottom:1px solid rgba(27,79,168,0.07); white-space:nowrap; }
.tbl tbody tr { border-bottom:1px solid rgba(27,79,168,0.05); transition:background 0.15s; }
.tbl tbody tr:last-child { border-bottom:none; }
.tbl tbody tr:hover { background:rgba(27,79,168,0.02); }
.tbl td { padding:12px 14px; font-size:13px; color:#4A5A7A; }
.tbl td.money { font-family:monospace; color:#1A2A4A; }
.tbl td.total { font-family:monospace; color:#059669; font-weight:500; }
.badge { font-size:9px; letter-spacing:1px; text-transform:uppercase; padding:2px 7px; border-radius:3px; }
.badge-direct { background:rgba(5,150,105,0.1); color:#059669; }
.badge-shared { background:rgba(27,79,168,0.08); color:#1B4FA8; }

/* Chart */
.chart-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:20px 22px; margin-bottom:28px; }
.chart-wrap { position:relative; height:180px; margin-top:14px; }
</style>

<div class="sales-page">

    <div class="page-eyebrow">Customer Service</div>
    <h1 class="page-title">Sales Table</h1>

    {{-- ── FILTER TABS ── --}}
    <div class="filter-tabs">
        <a href="{{ route('sales.index', ['filter' => 'patch']) }}"
           class="filter-tab {{ $filterType === 'patch' ? 'active' : '' }}">By Patch</a>
        <a href="{{ route('sales.index', ['filter' => 'month', 'month' => $month]) }}"
           class="filter-tab {{ $filterType === 'month' ? 'active' : '' }}">By Month</a>
        <a href="{{ route('sales.index', ['filter' => 'day', 'day' => $day]) }}"
           class="filter-tab {{ $filterType === 'day' ? 'active' : '' }}">By Day</a>
    </div>

    {{-- ── FILTER INPUT ── --}}
    @if($filterType === 'patch')
    <div class="filter-bar">
        <label>Viewing patch:</label>
        <select class="filter-sel" onchange="window.location.href='{{ route('sales.index') }}?filter=patch&patch_id='+this.value">
            @foreach($allPatches as $p)
                <option value="{{ $p->patch_id }}" {{ $p->patch_id == $currentPatch?->patch_id ? 'selected' : '' }}>
                    {{ $p->name ?? 'Patch ' . \Carbon\Carbon::parse($p->start_date)->format('M Y') }}
                    @if($p->status === 'Active') (Current) @endif
                </option>
            @endforeach
        </select>
    </div>

    @elseif($filterType === 'month')
    <div class="filter-bar">
        <label>Select Month:</label>
        <input type="month" value="{{ $month }}" class="filter-sel"
               onchange="window.location.href='{{ route('sales.index') }}?filter=month&month='+this.value">
    </div>

    @elseif($filterType === 'day')
    <div class="filter-bar">
        <label>Select Day:</label>
        <input type="date" value="{{ $day }}" class="filter-sel" style="color-scheme:light;"
               onchange="window.location.href='{{ route('sales.index') }}?filter=day&day='+this.value">
    </div>
    @endif

    {{-- ── KPI CARDS ── --}}
    <div class="kpi-grid">

        {{-- Target — patch only --}}
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">{{ $filterType === 'patch' ? 'Patch Target' : 'Target' }}</div>
            @if($filterType === 'patch')
                <div class="kpi-val">{{ number_format($kpis['target']) }}</div>
                <div class="kpi-sub">LE — set by admin</div>
            @else
                <div class="kpi-na">N/A</div>
                <div class="kpi-sub">Patch view only</div>
            @endif
        </div>

        {{-- Achieved --}}
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Achieved</div>
            <div class="kpi-val">{{ number_format($kpis['achieved']) }}</div>
            <div class="kpi-sub">LE
                @if($filterType === 'patch') this patch
                @elseif($filterType === 'month') this month
                @else today
                @endif
            </div>
            @if($filterType === 'patch' && $kpis['percentage'] !== null)
            <div class="prog"><div class="prog-fill" style="width:{{ min($kpis['percentage'],100) }}%"></div></div>
            @endif
        </div>

        {{-- Remaining — patch only --}}
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Remaining</div>
            @if($filterType === 'patch' && $kpis['remaining'] !== null)
                <div class="kpi-val">{{ number_format($kpis['remaining']) }}</div>
                <div class="kpi-sub">LE to target</div>
            @else
                <div class="kpi-na">N/A</div>
                <div class="kpi-sub">Patch view only</div>
            @endif
        </div>

        {{-- Achievement % — patch only --}}
        <div class="kpi-card" style="--kc:#7F77DD">
            <div class="kpi-label">Achievement</div>
            @if($filterType === 'patch' && $kpis['percentage'] !== null)
                <div class="kpi-val">{{ $kpis['percentage'] }}%</div>
                <div class="kpi-sub">of target reached</div>
            @else
                <div class="kpi-na">N/A</div>
                <div class="kpi-sub">Patch view only</div>
            @endif
        </div>

        {{-- Registrations --}}
        <div class="kpi-card" style="--kc:#1D9E75">
            <div class="kpi-label">Registrations</div>
            <div class="kpi-val">{{ $kpis['registrations'] }}</div>
            <div class="kpi-sub">students
                @if($filterType === 'patch') this patch
                @elseif($filterType === 'month') this month
                @else today
                @endif
            </div>
        </div>

    </div>

    {{-- ── FOLLOWUP STATS ── --}}
    <span class="sec-label">Follow-up Statistics</span>
    <div class="fu-grid">
        @foreach([
            ['val' => $followupStats['total_leads'],   'label' => 'Total Leads'],
            ['val' => $followupStats['total_calls'],   'label' => 'Total Calls'],
            ['val' => $followupStats['answered'],      'label' => 'Answered'],
            ['val' => $followupStats['unanswered'],    'label' => 'Unanswered'],
            ['val' => $followupStats['registered'],    'label' => 'Registered'],
            ['val' => $followupStats['conversion'].'%','label' => 'Conversion Rate'],
        ] as $stat)
        <div class="fu-card">
            <div class="fu-val">{{ $stat['val'] }}</div>
            <div class="fu-label">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── REVENUE TABLE ── --}}
    <span class="sec-label">Revenue Breakdown — Per Student</span>
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Deposit</th>
                        <th>Material</th>
                        <th>Total Revenue</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenueRows as $row)
                    <tr>
                        <td style="font-weight:500;color:#1A2A4A;">{{ $row['student_name'] }}</td>
                        <td>{{ $row['course'] }}</td>
                        <td class="money">{{ number_format($row['deposit']) }} LE</td>
                        <td class="money">{{ number_format($row['material']) }} LE</td>
                        <td class="total">{{ number_format($row['total']) }} LE</td>
                        <td>
                            <span class="badge {{ $row['material'] > 0 ? 'badge-shared' : 'badge-direct' }}">
                                {{ $row['material'] > 0 ? 'Shared' : 'Direct' }}
                            </span>
                        </td>
                        <td style="color:#7A8A9A;font-size:11px;">{{ $row['date'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#AAB8C8;padding:32px;font-size:12px;">
                            No revenue recorded for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($revenueRows->count() > 0)
                <tfoot>
                    <tr style="border-top:2px solid rgba(27,79,168,0.1);">
                        <td colspan="2" style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;padding:10px 14px;">Total</td>
                        <td class="money" style="font-weight:500;">{{ number_format($revenueRows->sum('deposit')) }} LE</td>
                        <td class="money" style="font-weight:500;">{{ number_format($revenueRows->sum('material')) }} LE</td>
                        <td class="total" style="font-size:15px;">{{ number_format($revenueRows->sum('total')) }} LE</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── DAILY CHART ── --}}
    <span class="sec-label">Revenue Over Time</span>
    <div class="chart-card">
        <div style="font-size:11px;color:#7A8A9A;">
            @if($filterType === 'patch') Daily revenue — {{ $currentPatch?->name ?? 'Current Patch' }}
            @elseif($filterType === 'month') Daily revenue — {{ \Carbon\Carbon::parse($month)->format('F Y') }}
            @else Revenue — {{ \Carbon\Carbon::parse($day)->format('d M Y') }}
            @endif
        </div>
        <div class="chart-wrap">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = @json($dailyRevenue['labels']);
const values = @json($dailyRevenue['values']);

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Revenue (LE)',
            data: values,
            backgroundColor: 'rgba(27,79,168,0.65)',
            borderRadius: 3,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(27,79,168,0.05)' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});
</script>

@endsection