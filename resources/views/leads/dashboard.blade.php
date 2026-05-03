@extends('layouts.leads')
@section('title', 'Leads Dashboard')
@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
:root{
    --blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);
    --orange:#F5911E;--orange-l:rgba(245,145,30,0.08);
    --green:#059669;--green-l:rgba(5,150,105,0.08);
    --red:#DC2626;--red-l:rgba(220,38,38,0.06);
    --purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);
    --teal:#0891B2;--teal-l:rgba(8,145,178,0.08);
    --border:rgba(27,79,168,0.09);
    --bg:#F8F6F2;--card:#fff;
    --text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;
}
*{box-sizing:border-box;}

.dash-page{background:var(--bg);min-height:100vh;padding:36px 28px;font-family:'DM Sans',sans-serif;color:var(--text);}

/* ── HEADER ── */
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--blue);line-height:1;margin:0 0 4px;}
.page-sub{font-size:12px;color:var(--faint);}
.dash-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px;}

/* ── SECTION LABEL ── */
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);display:flex;align-items:center;gap:8px;margin-bottom:14px;margin-top:28px;}
.sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

/* ── KPI CARDS ── */
.kpi-grid{display:grid;gap:12px;}
.kpi-grid-6{grid-template-columns:repeat(6,1fr);}
.kpi-grid-5{grid-template-columns:repeat(5,1fr);}
.kpi-grid-4{grid-template-columns:repeat(4,1fr);}
.kpi-grid-3{grid-template-columns:repeat(3,1fr);}
.kpi-grid-2{grid-template-columns:1fr 1fr;}

.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;text-decoration:none;display:block;transition:all 0.25s;animation:fadeIn 0.4s ease both;}
.kpi-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(27,79,168,0.1);text-decoration:none;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:7px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}
.kpi-sub{font-size:10px;color:var(--faint);margin-top:5px;}
.kpi-link{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--kc,var(--blue));margin-top:10px;display:block;opacity:0.6;}
.kpi-card:hover .kpi-link{opacity:1;}

/* ── PERIOD TABLE CARDS ── */
.period-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
.period-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;animation:fadeIn 0.4s ease both;}
.period-card-header{padding:12px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(27,79,168,0.01);}
.period-card-title{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;color:var(--text);}
.period-total{font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--blue);letter-spacing:1px;}
.period-row{display:flex;justify-content:space-between;align-items:center;padding:9px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.period-row:last-child{border-bottom:none;}
.period-row:hover{background:rgba(27,79,168,0.02);}
.period-name{font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);}
.period-num{font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--blue);letter-spacing:1px;line-height:1;}

/* ── BAR CHARTS ── */
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.chart-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;animation:fadeIn 0.4s ease both;}
.chart-card-header{padding:13px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(27,79,168,0.01);}
.chart-card-title{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;color:var(--text);}
.chart-body{padding:16px 18px;}

.bar-row{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.bar-row:last-child{margin-bottom:0;}
.bar-name{font-size:9px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);width:90px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.bar-track{flex:1;height:6px;background:rgba(27,79,168,0.06);border-radius:3px;overflow:hidden;}
.bar-fill{height:100%;border-radius:3px;transition:width 0.9s cubic-bezier(0.16,1,0.3,1);}
.bar-num{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px;width:28px;text-align:right;flex-shrink:0;}

/* ── CONVERSION FUNNEL ── */
.funnel-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;}
.funnel-step{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:14px 12px;text-align:center;position:relative;overflow:hidden;transition:all 0.25s;}
.funnel-step:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(27,79,168,0.1);}
.funnel-step::before{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:var(--fc,var(--blue));}
.funnel-step-val{font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--fc,var(--blue));letter-spacing:1px;line-height:1;}
.funnel-step-label{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--faint);margin-top:5px;}
.funnel-pct{font-size:11px;color:var(--fc,var(--blue));margin-top:4px;font-weight:600;}

/* ── RECENT TABLE ── */
.tbl-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;animation:fadeIn 0.4s ease both;}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:10px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);font-weight:500;background:rgba(27,79,168,0.02);text-align:left;border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl tbody td{padding:10px 14px;font-size:12px;color:var(--muted);}

/* ── BADGES ── */
.tag-sm{display:inline-block;font-size:8px;letter-spacing:1px;padding:3px 8px;border-radius:3px;text-transform:uppercase;font-weight:600;}
.tag-waiting{color:var(--muted);background:rgba(122,138,154,0.1);border:1px solid rgba(122,138,154,0.2);}
.tag-call_again{color:#C47010;background:var(--orange-l);border:1px solid rgba(245,145,30,0.25);}
.tag-registered{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.tag-not_interested{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.2);}
.tag-archived{color:#9A8A7A;background:rgba(154,138,122,0.08);border:1px solid rgba(154,138,122,0.2);}
.tag-scheduled{color:var(--teal);background:var(--teal-l);border:1px solid rgba(8,145,178,0.2);}

/* ── ANIMATE ── */
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}

/* ── RESPONSIVE ── */
@media(max-width:1100px){
    .kpi-grid-6{grid-template-columns:repeat(3,1fr);}
    .funnel-grid{grid-template-columns:repeat(3,1fr);}
}
@media(max-width:900px){
    .period-grid,.charts-grid{grid-template-columns:1fr;}
    .kpi-grid-6,.kpi-grid-5,.kpi-grid-4{grid-template-columns:repeat(2,1fr);}
    .funnel-grid{grid-template-columns:1fr 1fr;}
}
@media(max-width:600px){
    .dash-page{padding:18px 14px;}
    .kpi-grid-6,.kpi-grid-5,.kpi-grid-4,.kpi-grid-3{grid-template-columns:1fr 1fr;}
    .funnel-grid{grid-template-columns:1fr 1fr;}
}
</style>

<div class="dash-page">

    {{-- ── HEADER ── --}}
    <div class="dash-header">
        <div>
            <div class="page-eyebrow">Leads</div>
            <h1 class="page-title">Leads Dashboard</h1>
            <p class="page-sub">{{ now()->format('l, d M Y') }} · Your pipeline at a glance</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('leads.public') }}" style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-size:10px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.borderColor='var(--blue)';this.style.color='var(--blue)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>
                Public Leads
            </a>
            <a href="{{ route('leads.create') }}" style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;background:var(--blue);border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;transition:background 0.2s;"
               onmouseover="this.style.background='#2D6FDB'"
               onmouseout="this.style.background='var(--blue)'">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Lead
            </a>
        </div>
    </div>

    {{-- ── OVERVIEW KPIs ── --}}
    <span class="sec-label">Overview</span>
    <div class="kpi-grid kpi-grid-6" style="margin-bottom:4px;">
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--blue)">
            <div class="kpi-label">Total My Leads</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
            <div class="kpi-link">View All →</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--green)">
            <div class="kpi-label">Registered</div>
            <div class="kpi-val">{{ $stats['registered'] }}</div>
            <div class="kpi-sub">converted</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Call Again</div>
            <div class="kpi-val">{{ $stats['call_again'] }}</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--muted)">
            <div class="kpi-label">Waiting</div>
            <div class="kpi-val">{{ $stats['waiting'] }}</div>
        </a>
        <a href="{{ route('leads.archived') }}" class="kpi-card" style="--kc:#9A8A7A">
            <div class="kpi-label">Archived</div>
            <div class="kpi-val">{{ $stats['archived'] }}</div>
        </a>
        <a href="{{ route('leads.public') }}" class="kpi-card" style="--kc:#2D6FDB">
            <div class="kpi-label">Public Pool</div>
            <div class="kpi-val">{{ $stats['public'] }}</div>
            <div class="kpi-sub">unassigned</div>
            <div class="kpi-link">Claim →</div>
        </a>
    </div>

    {{-- ── CONVERSION FUNNEL ── --}}
    <span class="sec-label">Conversion Funnel</span>
    @php
        $total = max($stats['total'], 1);
        $convRate = round(($stats['registered'] / $total) * 100, 1);
    @endphp
    <div class="funnel-grid" style="margin-bottom:4px;">
        <div class="funnel-step" style="--fc:var(--blue)">
            <div class="funnel-step-val">{{ $stats['total'] }}</div>
            <div class="funnel-step-label">Total Leads</div>
            <div class="funnel-pct">100%</div>
        </div>
        <div class="funnel-step" style="--fc:#C47010">
            <div class="funnel-step-val">{{ $stats['call_again'] + $stats['waiting'] }}</div>
            <div class="funnel-step-label">In Pipeline</div>
            <div class="funnel-pct">{{ round((($stats['call_again'] + $stats['waiting']) / $total) * 100) }}%</div>
        </div>
        <div class="funnel-step" style="--fc:var(--teal)">
            <div class="funnel-step-val">{{ $stats['call_again'] }}</div>
            <div class="funnel-step-label">Call Again</div>
            <div class="funnel-pct">{{ round(($stats['call_again'] / $total) * 100) }}%</div>
        </div>
        <div class="funnel-step" style="--fc:var(--green)">
            <div class="funnel-step-val">{{ $stats['registered'] }}</div>
            <div class="funnel-step-label">Registered</div>
            <div class="funnel-pct">{{ $convRate }}%</div>
        </div>
        <div class="funnel-step" style="--fc:var(--red)">
            <div class="funnel-step-val">{{ $stats['archived'] }}</div>
            <div class="funnel-step-label">Archived</div>
            <div class="funnel-pct">{{ round(($stats['archived'] / $total) * 100) }}%</div>
        </div>
    </div>

    {{-- ── PERIOD STATS ── --}}
    <span class="sec-label">New Leads by Period</span>
    <div class="period-grid">
        @foreach(['Today' => $today, 'This Week' => $week, 'This Month' => $month] as $label => $data)
        <div class="period-card">
            <div class="period-card-header">
                <div class="period-card-title">{{ $label }}</div>
                <div class="period-total">{{ array_sum($data) }}</div>
            </div>
            @foreach(['Waiting' => ['label'=>'Waiting','color'=>'var(--muted)'], 'Call_Again' => ['label'=>'Call Again','color'=>'#C47010'], 'Registered' => ['label'=>'Registered','color'=>'var(--green)'], 'Archived' => ['label'=>'Archived','color'=>'#9A8A7A']] as $key => $cfg)
            <div class="period-row">
                <span class="period-name" style="color:{{ $cfg['color'] }};">{{ $cfg['label'] }}</span>
                <span class="period-num" style="color:{{ $cfg['color'] }};">{{ $data[$key] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    {{-- ── DISTRIBUTION CHARTS ── --}}
    <span class="sec-label">Distribution</span>
    <div class="charts-grid">

        {{-- By Source --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title">By Source</div>
                <span style="font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;">All Time</span>
            </div>
            <div class="chart-body">
                @php $maxSource = max(array_values($bySource) ?: [1]); @endphp
                @foreach($bySource as $source => $count)
                <div class="bar-row">
                    <span class="bar-name" title="{{ str_replace('_',' ',$source) }}">{{ Str::limit(str_replace('_',' ',$source), 11) }}</span>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ $maxSource > 0 ? round(($count/$maxSource)*100) : 0 }}%;background:linear-gradient(90deg,var(--blue),#2D6FDB);"></div>
                    </div>
                    <span class="bar-num" style="color:var(--blue);">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- By Course --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title">By Course</div>
                <span style="font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;">All Time</span>
            </div>
            <div class="chart-body">
                @php $maxCourse = max(array_values($byCourse) ?: [1]); @endphp
                @forelse($byCourse as $course => $count)
                <div class="bar-row">
                    <span class="bar-name" title="{{ $course }}">{{ Str::limit($course, 11) }}</span>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ $maxCourse > 0 ? round(($count/$maxCourse)*100) : 0 }}%;background:linear-gradient(90deg,var(--orange),#FFB347);"></div>
                    </div>
                    <span class="bar-num" style="color:#C47010;">{{ $count }}</span>
                </div>
                @empty
                <div style="font-size:11px;color:var(--faint);padding:8px 0;">No data</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── BY CS ── --}}
    @if(count($byCs) > 1)
    <span class="sec-label">By CS Employee</span>
    <div class="chart-card" style="margin-bottom:4px;">
        <div class="chart-card-header">
            <div class="chart-card-title">Leads Assigned Per CS</div>
        </div>
        <div class="chart-body">
            @php $maxCs = max(array_values($byCs) ?: [1]); @endphp
            @foreach($byCs as $name => $count)
            <div class="bar-row">
                <span class="bar-name">{{ Str::limit($name, 11) }}</span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ $maxCs > 0 ? round(($count/$maxCs)*100) : 0 }}%;background:linear-gradient(90deg,var(--green),#34D399);"></div>
                </div>
                <span class="bar-num" style="color:var(--green);">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── RECENT LEADS ── --}}
    <span class="sec-label">Recent Leads</span>
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
                    <tr>
                        <td style="font-weight:600;color:var(--text);">{{ $lead->full_name }}</td>
                        <td style="font-family:monospace;font-size:11px;">{{ $lead->phone }}</td>
                        <td>
                            @if($lead->courseTemplate)
                            <span style="font-size:11px;color:var(--blue);font-weight:500;">{{ $lead->courseTemplate->name }}</span>
                            @else<span style="color:var(--faint);">—</span>@endif
                        </td>
                        <td>
                            <span class="tag-sm" style="background:var(--orange-l);border:1px solid rgba(245,145,30,0.15);color:#C47010;">
                                {{ str_replace('_',' ',$lead->source) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $tc = match($lead->status) {
                                    'Waiting'        => 'tag-waiting',
                                    'Call_Again'     => 'tag-call_again',
                                    'Registered'     => 'tag-registered',
                                    'Not_Interested' => 'tag-not_interested',
                                    'Archived'       => 'tag-archived',
                                    'Scheduled_Call' => 'tag-scheduled',
                                    default          => 'tag-waiting',
                                };
                            @endphp
                            <span class="tag-sm {{ $tc }}">{{ str_replace('_',' ',$lead->status) }}</span>
                        </td>
                        <td style="font-size:11px;color:var(--faint);white-space:nowrap;">{{ $lead->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('leads.edit', $lead->lead_id) }}"
                               style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:var(--blue);text-decoration:none;opacity:0.6;transition:opacity 0.2s;"
                               onmouseover="this.style.opacity='1'"
                               onmouseout="this.style.opacity='0.6'">Edit →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--faint);font-size:12px;">No leads yet — add your first one!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// Animate KPI numbers
document.querySelectorAll('.kpi-val, .funnel-step-val, .period-num').forEach(el => {
    const text = el.textContent.trim();
    const num  = parseInt(text.replace(/[^0-9]/g, ''));
    if (isNaN(num) || num === 0) return;
    const dur = 600, start = performance.now();
    (function tick(now) {
        const pct  = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        el.textContent = Math.round(num * ease).toLocaleString();
        if (pct < 1) requestAnimationFrame(tick);
    })(start);
});

// Animate bars on load
setTimeout(() => {
    document.querySelectorAll('.bar-fill').forEach(bar => {
        const w = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => bar.style.width = w, 100);
    });
}, 100);
</script>

@endsection