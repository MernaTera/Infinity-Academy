@extends('layouts.leads')
@section('title', 'Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.dash-page { background:#F8F6F2; min-height:100vh; padding:40px 32px; font-family:'DM Sans',sans-serif; color:#1A2A4A; }

/* Header */
.dash-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
.page-eyebrow { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:4px; }
.page-title { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:4px; color:#1B4FA8; line-height:1; }
.page-patch { font-size:11px; color:#AAB8C8; margin-top:4px; }
.btn-new-lead { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border:1.5px solid #1B4FA8; border-radius:4px; color:#1B4FA8; font-family:'Bebas Neue',sans-serif; font-size:13px; letter-spacing:3px; text-decoration:none; transition:all 0.3s; position:relative; overflow:hidden; }
.btn-new-lead::before { content:''; position:absolute; inset:0; background:#1B4FA8; transform:scaleX(0); transform-origin:left; transition:transform 0.4s cubic-bezier(0.16,1,0.3,1); }
.btn-new-lead:hover::before { transform:scaleX(1); }
.btn-new-lead:hover { color:#fff; text-decoration:none; }
.btn-new-lead span { position:relative; z-index:1; }

/* Section label */
.sec-label { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:14px; padding-bottom:9px; border-bottom:1px solid rgba(245,145,30,0.15); margin-top:28px; }

/* KPI Grid */
.kpi-grid { display:grid; gap:14px; margin-bottom:4px; }
.kpi-grid-5 { grid-template-columns: repeat(5,1fr); }
.kpi-grid-3 { grid-template-columns: repeat(3,1fr); }
.kpi-grid-4 { grid-template-columns: repeat(4,1fr); }

.kpi-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; padding:16px 18px; position:relative; overflow:hidden; text-decoration:none; display:block; transition:transform 0.2s, box-shadow 0.2s; }
.kpi-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(27,79,168,0.1); text-decoration:none; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--kc,#1B4FA8); }
.kpi-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7A8A9A; margin-bottom:6px; }
.kpi-val { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:2px; color:var(--kc,#1B4FA8); line-height:1; }
.kpi-sub { font-size:10px; color:#AAB8C8; margin-top:4px; }
.kpi-link { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--kc,#1B4FA8); margin-top:8px; opacity:0.7; }

/* Progress bar */
.prog { background:#F0F0F0; border-radius:3px; height:5px; margin-top:10px; overflow:hidden; }
.prog-fill { height:5px; border-radius:3px; transition:width 0.8s ease; }

/* Alert card */
.alert-card { display:flex; align-items:center; gap:14px; background:#fff; border:1px solid rgba(245,145,30,0.2); border-left:3px solid #F5911E; border-radius:6px; padding:14px 18px; margin-bottom:16px; }
.alert-card svg { flex-shrink:0; color:#F5911E; }
.alert-card-text { font-size:13px; color:#1A2A4A; }
.alert-card-sub { font-size:11px; color:#7A8A9A; margin-top:2px; }

/* Two-col layout */
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }

/* Mini table */
.mini-card { background:#fff; border:1px solid rgba(27,79,168,0.1); border-radius:6px; overflow:hidden; }
.mini-card-header { padding:12px 16px; border-bottom:1px solid rgba(27,79,168,0.06); display:flex; align-items:center; justify-content:space-between; }
.mini-card-title { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:#7A8A9A; }
.mini-card-link { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#1B4FA8; text-decoration:none; }
.mini-card-link:hover { text-decoration:underline; }
.mini-row { display:flex; align-items:center; justify-content:space-between; padding:11px 16px; border-bottom:1px solid rgba(27,79,168,0.04); }
.mini-row:last-child { border-bottom:none; }
.mini-row-name { font-size:13px; color:#1A2A4A; font-weight:500; }
.mini-row-sub { font-size:10px; color:#AAB8C8; margin-top:2px; }
.mini-row-right { text-align:right; }
.mini-row-val { font-size:12px; font-family:monospace; color:#1A2A4A; }
.mini-empty { text-align:center; padding:24px; color:#AAB8C8; font-size:12px; }

/* Status badge */
.badge { display:inline-block; font-size:8px; letter-spacing:1px; text-transform:uppercase; padding:2px 7px; border-radius:3px; }
.badge-waiting   { background:rgba(122,138,154,0.1); color:#7A8A9A; }
.badge-call      { background:rgba(196,112,16,0.1);  color:#C47010; }
.badge-registered{ background:rgba(5,150,105,0.1);   color:#059669; }
.badge-archived  { background:rgba(155,155,155,0.1); color:#9A8A7A; }

@media(max-width:768px){
    .kpi-grid-5,.kpi-grid-4,.kpi-grid-3 { grid-template-columns:1fr 1fr; }
    .two-col { grid-template-columns:1fr; }
    .dash-page { padding:18px 14px; }
}
</style>

<div class="dash-page">

    {{-- HEADER --}}
    <div class="dash-header">
        <div>
            <div class="page-eyebrow">Customer Service</div>
            <h1 class="page-title">Dashboard</h1>
            <div class="page-patch">
                {{ now()->format('l, d M Y') }}
                @if($currentPatch)
                — <span style="color:#1B4FA8">{{ $currentPatch->name }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('leads.create') }}" class="btn-new-lead">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Lead</span>
        </a>
    </div>

    {{-- ALERT: Calls Due Today --}}
    @if($callsDueToday > 0)
    <a href="{{ route('leads.index') }}" style="text-decoration:none">
        <div class="alert-card">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <div>
                <div class="alert-card-text">You have <strong>{{ $callsDueToday }} follow-up call{{ $callsDueToday > 1 ? 's' : '' }}</strong> scheduled for today</div>
                <div class="alert-card-sub">Click to view your leads list</div>
            </div>
        </div>
    </a>
    @endif

    {{-- SALES KPIs --}}
    <div class="sec-label">Sales Performance — {{ now()->format('F Y') }}</div>
    <div class="kpi-grid kpi-grid-5">
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Monthly Target</div>
            <div class="kpi-val">{{ number_format($salesStats['target']) }}</div>
            <div class="kpi-sub">LE — set by admin</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Achieved</div>
            <div class="kpi-val">{{ number_format($salesStats['achieved']) }}</div>
            <div class="kpi-sub">LE this month</div>
            <div class="prog"><div class="prog-fill" style="width:{{ min(100,$salesStats['percentage']) }}%;background:#059669"></div></div>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Remaining</div>
            <div class="kpi-val">{{ number_format($salesStats['remaining']) }}</div>
            <div class="kpi-sub">LE to target</div>
        </div>
        <div class="kpi-card" style="--kc:#7F77DD">
            <div class="kpi-label">Achievement</div>
            <div class="kpi-val">{{ $salesStats['percentage'] }}%</div>
            <div class="kpi-sub">of target reached</div>
        </div>
        <a href="{{ route('sales.index') }}" class="kpi-card" style="--kc:#1D9E75">
            <div class="kpi-label">Registrations</div>
            <div class="kpi-val">{{ $salesStats['registrations'] }}</div>
            <div class="kpi-sub">students this patch</div>
            <div class="kpi-link">View Sales →</div>
        </a>
    </div>

    {{-- LEADS KPIs --}}
    <div class="sec-label">Follow-up Overview</div>
    <div class="kpi-grid kpi-grid-5">
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">My Total Leads</div>
            <div class="kpi-val">{{ $leadsStats['my_total'] }}</div>
            <div class="kpi-link">View List →</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Active Follow-ups</div>
            <div class="kpi-val">{{ $leadsStats['my_active'] }}</div>
            <div class="kpi-sub">waiting / call again</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Overdue Calls</div>
            <div class="kpi-val">{{ $leadsStats['my_overdue'] }}</div>
            <div class="kpi-sub">past 4-day deadline</div>
        </a>
        <a href="{{ route('leads.public') }}" class="kpi-card" style="--kc:#2D6FDB">
            <div class="kpi-label">Public Leads</div>
            <div class="kpi-val">{{ $leadsStats['public'] }}</div>
            <div class="kpi-sub">available to claim</div>
            <div class="kpi-link">Claim Now →</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Registered</div>
            <div class="kpi-val">{{ $leadsStats['my_registered'] }}</div>
            <div class="kpi-sub">converted students</div>
        </a>
    </div>

    {{-- OUTSTANDING KPIs --}}
    <div class="sec-label">Outstanding Balances</div>
    <div class="kpi-grid kpi-grid-3">
        <a href="{{ route('outstanding.index') }}" class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Outstanding Students</div>
            <div class="kpi-val">{{ $outstandingStats['count'] }}</div>
            <div class="kpi-sub">with unpaid balance</div>
            <div class="kpi-link">View All →</div>
        </a>
        <a href="{{ route('outstanding.index') }}?filter=restricted" class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $outstandingStats['restricted'] }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </a>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Total Unpaid</div>
            <div class="kpi-val">{{ number_format($outstandingStats['total_le']) }}</div>
            <div class="kpi-sub">LE outstanding balance</div>
        </div>
    </div>

    {{-- RECENT: Leads + Payments --}}
    <div class="sec-label">Recent Activity</div>
    <div class="two-col">

        {{-- Recent Leads --}}
        <div class="mini-card">
            <div class="mini-card-header">
                <span class="mini-card-title">Recent Leads</span>
                <a href="{{ route('leads.index') }}" class="mini-card-link">View All →</a>
            </div>
            @forelse($recentLeads as $lead)
            <div class="mini-row">
                <div>
                    <div class="mini-row-name">{{ $lead->full_name }}</div>
                    <div class="mini-row-sub">{{ $lead->courseTemplate?->name ?? '—' }} · {{ $lead->created_at->diffForHumans() }}</div>
                </div>
                <div class="mini-row-right">
                    @php
                        $badgeMap = [
                            'Waiting'      => 'badge-waiting',
                            'Call_Again'   => 'badge-call',
                            'Registered'   => 'badge-registered',
                            'Archived'     => 'badge-archived',
                        ];
                    @endphp
                    <span class="badge {{ $badgeMap[$lead->status] ?? 'badge-waiting' }}">
                        {{ str_replace('_',' ',$lead->status) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="mini-empty">No leads yet.</div>
            @endforelse
        </div>

        {{-- Recent Payments --}}
        <div class="mini-card">
            <div class="mini-card-header">
                <span class="mini-card-title">Recent Payments</span>
                <a href="{{ route('sales.index') }}" class="mini-card-link">Sales Table →</a>
            </div>
            @forelse($recentPayments as $tx)
            <div class="mini-row">
                <div>
                    <div class="mini-row-name">{{ $tx->enrollment?->student?->full_name ?? '—' }}</div>
                    <div class="mini-row-sub">{{ $tx->enrollment?->courseTemplate?->name ?? '—' }} · {{ $tx->created_at->diffForHumans() }}</div>
                </div>
                <div class="mini-row-right">
                    <div class="mini-row-val" style="color:#059669">+{{ number_format($tx->amount) }} LE</div>
                    <div style="font-size:10px;color:#AAB8C8;margin-top:2px">{{ $tx->payment_method }}</div>
                </div>
            </div>
            @empty
            <div class="mini-empty">No payments recorded yet.</div>
            @endforelse
        </div>

    </div>

</div>
@endsection