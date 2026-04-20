@extends('layouts.leads')
@section('title', 'Sales Table')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.sales-page { background:#F8F6F2; min-height:100vh; padding:40px 32px; font-family:'DM Sans',sans-serif; color:#1A2A4A; }
.page-eyebrow { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:4px; }
.page-title { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:4px; color:#1B4FA8; }

.patch-bar { display:flex; align-items:center; gap:12px; margin:16px 0 28px; }
.patch-sel { font-family:'DM Sans',sans-serif; font-size:12px; padding:8px 14px; border:1px solid rgba(27,79,168,0.15); border-radius:4px; background:#fff; color:#1A2A4A; cursor:pointer; }

/* KPI */
.kpi-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:28px; }
.kpi-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:16px; position:relative; overflow:hidden; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--kc,#1B4FA8); }
.kpi-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7A8A9A; margin-bottom:6px; }
.kpi-val { font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:2px; color:var(--kc,#1B4FA8); line-height:1; }
.kpi-sub { font-size:10px; color:#AAB8C8; margin-top:4px; }
.prog { background:#F0F0F0; border-radius:4px; height:4px; margin-top:10px; overflow:hidden; }
.prog-fill { height:4px; border-radius:4px; background:var(--kc); transition:width .5s; }

/* Section label */
.sec-label { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:14px; padding-bottom:9px; border-bottom:1px solid rgba(245,145,30,0.15); }

/* Followup */
.fu-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:28px; }
.fu-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:14px; text-align:center; }
.fu-val { font-family:'Bebas Neue',sans-serif; font-size:24px; color:#1A2A4A; }
.fu-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#7A8A9A; margin-top:4px; }

/* Table */
.tbl-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; overflow:hidden; margin-bottom:28px; }
.tbl { width:100%; border-collapse:collapse; }
.tbl thead th { padding:11px 14px; font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7A8A9A; text-align:left; font-weight:500; background:rgba(27,79,168,0.02); border-bottom:1px solid rgba(27,79,168,0.07); }
.tbl tbody tr { border-bottom:1px solid rgba(27,79,168,0.05); }
.tbl tbody tr:last-child { border-bottom:none; }
.tbl tbody tr:hover { background:rgba(27,79,168,0.02); }
.tbl td { padding:12px 14px; font-size:13px; color:#4A5A7A; }
.tbl td.money { font-family:monospace; color:#1A2A4A; }
.tbl td.total { font-family:monospace; color:#059669; font-weight:500; }
.badge { font-size:9px; letter-spacing:1px; text-transform:uppercase; padding:2px 7px; border-radius:3px; }
.badge-direct { background:rgba(5,150,105,0.1); color:#059669; }
.badge-shared { background:rgba(27,79,168,0.08); color:#1B4FA8; }

/* Chart */
.chart-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:20px 22px; }
.chart-wrap { position:relative; height:180px; margin-top:14px; }
</style>

<div class="sales-page">

    {{-- HEADER --}}
    <div class="page-eyebrow">Customer Service</div>
    <h1 class="page-title">Sales Table</h1>

    {{-- PATCH FILTER --}}
    <div class="patch-bar">
        <span style="font-size:11px;color:#7A8A9A">Viewing patch:</span>
        <select class="patch-sel" onchange="switchPatch(this.value)">
            @foreach($allPatches as $p)
                <option value="{{ $p->patch_id }}"
                    {{ $p->patch_id == $currentPatch?->patch_id ? 'selected' : '' }}>
                    {{ $p->name ?? 'Patch ' . \Carbon\Carbon::parse($p->start_date)->format('M Y') }}
                    {{ $p->is_active ? '(Current)' : '' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- KPI CARDS --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Monthly Target</div>
            <div class="kpi-val">{{ number_format($kpis['target']) }}</div>
            <div class="kpi-sub">LE — set by admin</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Achieved</div>
            <div class="kpi-val">{{ number_format($kpis['achieved']) }}</div>
            <div class="kpi-sub">LE this patch</div>
            <div class="prog"><div class="prog-fill" style="width:{{ min($kpis['percentage'],100) }}%"></div></div>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Remaining</div>
            <div class="kpi-val">{{ number_format($kpis['remaining']) }}</div>
            <div class="kpi-sub">LE to target</div>
        </div>
        <div class="kpi-card" style="--kc:#7F77DD">
            <div class="kpi-label">Achievement</div>
            <div class="kpi-val">{{ $kpis['percentage'] }}%</div>
            <div class="kpi-sub">of target reached</div>
        </div>
        <div class="kpi-card" style="--kc:#1D9E75">
            <div class="kpi-label">Registrations</div>
            <div class="kpi-val">{{ $kpis['registrations'] }}</div>
            <div class="kpi-sub">students this patch</div>
        </div>
    </div>

    {{-- FOLLOWUP STATS --}}
    <div class="sec-label">Follow-up Statistics</div>
    <div class="fu-grid">
        @foreach([
            ['val' => $followupStats['total_leads'],  'label' => 'Total Leads'],
            ['val' => $followupStats['total_calls'],  'label' => 'Total Calls'],
            ['val' => $followupStats['answered'],     'label' => 'Answered'],
            ['val' => $followupStats['unanswered'],   'label' => 'Unanswered'],
            ['val' => $followupStats['registered'],   'label' => 'Registered'],
            ['val' => $followupStats['conversion'].'%','label'=> 'Conversion Rate'],
        ] as $stat)
        <div class="fu-card">
            <div class="fu-val">{{ $stat['val'] }}</div>
            <div class="fu-label">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- REVENUE TABLE --}}
    <div class="sec-label">Revenue Breakdown — Per Student</div>
    <div class="tbl-card">
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
                    <td>{{ $row['student_name'] }}</td>
                    <td>{{ $row['course'] }}</td>
                    <td class="money">{{ number_format($row['deposit']) }} LE</td>
                    <td class="money">{{ number_format($row['material']) }} LE</td>
                    <td class="total">{{ number_format($row['total']) }} LE</td>
                    <td>
                        <span class="badge {{ $row['material'] > 0 ? 'badge-shared' : 'badge-direct' }}">
                            {{ $row['material'] > 0 ? 'Shared' : 'Direct' }}
                        </span>
                    </td>
                    <td style="color:#7A8A9A;font-size:11px">{{ $row['date'] }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#AAB8C8;padding:24px">No revenue recorded for this patch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- DAILY CHART --}}
    <div class="sec-label">Revenue per Day</div>
    <div class="chart-card">
        <div style="font-size:11px;color:#7A8A9A">Daily revenue (LE) — {{ $currentPatch?->name ?? 'Current Patch' }}</div>
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

function switchPatch(patchId) {
    window.location.href = `/sales?patch_id=${patchId}`;
}
</script>
@endsection