@extends('layouts.leads')
@section('title', 'Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);
    --orange:#F5911E;--orange-l:rgba(245,145,30,0.08);
    --green:#059669;--green-l:rgba(5,150,105,0.08);
    --red:#DC2626;--red-l:rgba(220,38,38,0.06);
    --purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);
    --border:rgba(27,79,168,0.09);
    --bg:#F8F6F2;--card:#fff;
    --text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;
}
*{box-sizing:border-box;}

.dash-page{background:var(--bg);min-height:100vh;padding:36px 28px;font-family:'DM Sans',sans-serif;color:var(--text);}

/* ── HEADER ── */
.dash-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--blue);line-height:1;}
.page-sub{font-size:12px;color:var(--faint);margin-top:4px;}
.btn-new-lead{display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border:1.5px solid var(--blue);border-radius:4px;color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;transition:color 0.4s;position:relative;overflow:hidden;}
.btn-new-lead::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);}
.btn-new-lead:hover::before{transform:scaleX(1);}
.btn-new-lead:hover{color:#fff;text-decoration:none;}
.btn-new-lead span,.btn-new-lead svg{position:relative;z-index:1;}

/* ── HERO STRIP (Target) ── */
.hero-strip{background:linear-gradient(135deg,#1A2A4A 0%,var(--blue) 60%,#2D6FDB 100%);border-radius:10px;padding:22px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:18px;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(27,79,168,0.2);}
.hero-strip::before{content:'';position:absolute;top:-50px;right:-50px;width:200px;height:200px;border-radius:50%;background:rgba(245,145,30,0.07);}
.hero-strip::after{content:'';position:absolute;bottom:-30px;left:140px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.04);}
.hero-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:rgba(255,255,255,0.45);margin-bottom:5px;}
.hero-name{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:4px;color:#fff;line-height:1;}
.hero-sub{font-size:11px;color:rgba(255,255,255,0.5);margin-top:4px;}
.hero-prog-track{background:rgba(255,255,255,0.12);border-radius:4px;height:6px;overflow:hidden;margin:8px 0 4px;}
.hero-prog-fill{height:6px;border-radius:4px;background:linear-gradient(90deg,var(--orange),#FFB347);transition:width 1s ease;}
.hero-stat-val{font-family:'Bebas Neue',sans-serif;font-size:28px;color:#fff;letter-spacing:1px;line-height:1;}
.hero-stat-label{font-size:9px;color:rgba(255,255,255,0.4);letter-spacing:2px;text-transform:uppercase;margin-top:3px;}

/* ── ALERT ── */
.alert-card{display:flex;align-items:center;gap:14px;background:var(--card);border:1px solid rgba(245,145,30,0.2);border-left:3px solid var(--orange);border-radius:6px;padding:14px 18px;margin-bottom:20px;text-decoration:none;transition:all 0.2s;animation:fadeIn 0.4s ease both;}
.alert-card:hover{box-shadow:0 4px 16px rgba(245,145,30,0.12);border-left-color:var(--blue);text-decoration:none;}
.alert-card-text{font-size:13px;color:var(--text);}
.alert-card-sub{font-size:11px;color:var(--faint);margin-top:2px;}

/* ── SECTION LABEL ── */
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);display:flex;align-items:center;gap:8px;margin-bottom:14px;margin-top:28px;}
.sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

/* ── KPI CARDS ── */
.kpi-grid{display:grid;gap:12px;margin-bottom:4px;}
.kpi-grid-5{grid-template-columns:repeat(5,1fr);}
.kpi-grid-4{grid-template-columns:repeat(4,1fr);}
.kpi-grid-3{grid-template-columns:repeat(3,1fr);}
.kpi-grid-2{grid-template-columns:repeat(2,1fr);}

.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;text-decoration:none;display:block;transition:all 0.25s;animation:fadeIn 0.4s ease both;}
.kpi-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(27,79,168,0.1);text-decoration:none;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:7px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}
.kpi-sub{font-size:10px;color:var(--faint);margin-top:5px;}
.kpi-link{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--kc,var(--blue));margin-top:10px;display:block;opacity:0.6;transition:opacity 0.2s;}
.kpi-card:hover .kpi-link{opacity:1;}

/* Progress in kpi */
.kpi-prog{background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;margin-top:10px;}
.kpi-prog-fill{height:4px;border-radius:3px;background:var(--kc,var(--blue));transition:width 1s ease;}

/* ── TASK CARDS ── */
.task-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:4px;}
.task-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:16px 18px;display:flex;align-items:center;gap:14px;text-decoration:none;transition:all 0.25s;animation:fadeIn 0.4s ease both;}
.task-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(27,79,168,0.1);text-decoration:none;}
.task-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.2s;}
.task-num{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:1px;line-height:1;}
.task-label{font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--faint);margin-top:2px;}

/* ── TWO-COL ── */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}

/* ── MINI CARD ── */
.mini-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;animation:fadeIn 0.4s ease both;}
.mini-card-header{padding:13px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(27,79,168,0.01);}
.mini-card-title{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;color:var(--text);}
.mini-card-link{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--blue);text-decoration:none;transition:opacity 0.2s;}
.mini-card-link:hover{text-decoration:none;opacity:0.7;}
.mini-row{display:flex;align-items:center;gap:10px;padding:11px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.mini-row:last-child{border-bottom:none;}
.mini-row:hover{background:rgba(27,79,168,0.02);}
.mini-avatar{width:30px;height:30px;border-radius:50%;background:var(--blue-l);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:12px;color:var(--blue);flex-shrink:0;}
.mini-row-name{font-size:13px;color:var(--text);font-weight:500;}
.mini-row-sub{font-size:10px;color:var(--faint);margin-top:1px;}
.mini-empty{text-align:center;padding:28px;color:var(--faint);font-size:12px;}

/* ── BADGES ── */
.badge{display:inline-block;font-size:8px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:600;}
.badge-waiting{background:rgba(122,138,154,0.1);color:var(--muted);}
.badge-call{background:var(--orange-l);color:#C47010;}
.badge-registered{background:var(--green-l);color:var(--green);}
.badge-archived{background:rgba(155,155,155,0.1);color:#9A8A7A;}
.badge-overdue{background:var(--red-l);color:var(--red);}

/* ── OUTSTANDING BAR ── */
.os-row{display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid rgba(27,79,168,0.04);}
.os-row:last-child{border-bottom:none;}
.os-bar-track{flex:1;background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;}
.os-bar-fill{height:4px;border-radius:3px;background:var(--red);transition:width 0.8s ease;}

/* ── ANIMATE ── */
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}

/* ── RESPONSIVE ── */
@media(max-width:1100px){
    .kpi-grid-5,.task-grid{grid-template-columns:repeat(3,1fr);}
    .kpi-grid-4{grid-template-columns:repeat(2,1fr);}
    .three-col{grid-template-columns:1fr 1fr;}
}
@media(max-width:768px){
    .dash-page{padding:18px 14px;}
    .kpi-grid-5,.kpi-grid-4,.kpi-grid-3,.task-grid{grid-template-columns:1fr 1fr;}
    .two-col,.three-col{grid-template-columns:1fr;}
    .hero-strip{flex-direction:column;}
}
@media(max-width:480px){
    .kpi-grid-5,.kpi-grid-4,.kpi-grid-3,.kpi-grid-2,.task-grid{grid-template-columns:1fr;}
}
</style>

<div class="dash-page">

    {{-- ── HEADER ── --}}
    <div class="dash-header">
        <div>
            <div class="page-eyebrow">Customer Service</div>
            <h1 class="page-title">Dashboard</h1>
            <div class="page-sub">
                {{ now()->format('l, d M Y') }}
                @if(isset($currentPatch) && $currentPatch)
                — <span style="color:var(--blue);font-weight:500;">{{ $currentPatch->name }}</span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('leads.index') }}" class="btn-new-lead" style="border-color:rgba(27,79,168,0.2);color:var(--muted);">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <span>My Leads</span>
            </a>
            <a href="{{ route('leads.create') }}" class="btn-new-lead">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>New Lead</span>
            </a>
        </div>
    </div>

    {{-- ── ALERT: Calls Due Today ── --}}
    @if($callsDueToday > 0)
    <a href="{{ route('leads.index') }}" class="alert-card">
        <div style="width:38px;height:38px;border-radius:10px;background:var(--orange-l);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div>
            <div class="alert-card-text">You have <strong>{{ $callsDueToday }} follow-up call{{ $callsDueToday > 1 ? 's' : '' }}</strong> scheduled for today</div>
            <div class="alert-card-sub">Tap to open your leads list and start calling →</div>
        </div>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--faint)" stroke-width="2" style="margin-left:auto;flex-shrink:0;"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    @endif

    {{-- ── HERO: Sales Target ── --}}
    <div class="hero-strip">
        <div style="position:relative;z-index:1;">
            <div class="hero-label">Monthly Target — {{ now()->format('F Y') }}</div>
            <div class="hero-name">{{ $employee?->full_name ?? Auth::user()->name }}</div>
            <div class="hero-sub">Customer Service · Infinity Academy</div>
        </div>
        <div style="flex:1;max-width:300px;position:relative;z-index:1;">
            @php $pct = min(100, $salesStats['percentage']); @endphp
            <div style="display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,0.5);margin-bottom:6px;">
                <span>Target Progress</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="hero-prog-track">
                <div class="hero-prog-fill" style="width:{{ $pct }}%"></div>
            </div>
            <div style="font-size:10px;color:rgba(255,255,255,0.35);margin-top:4px;">
                {{ number_format($salesStats['achieved']) }} / {{ number_format($salesStats['target']) }} LE
            </div>
        </div>
        <div style="display:flex;gap:28px;position:relative;z-index:1;flex-wrap:wrap;">
            <div>
                <div class="hero-stat-val">{{ number_format($salesStats['achieved']) }}</div>
                <div class="hero-stat-label">Achieved LE</div>
            </div>
            <div>
                <div class="hero-stat-val" style="color:#FFB347;">{{ number_format($salesStats['remaining']) }}</div>
                <div class="hero-stat-label">Remaining LE</div>
            </div>
            <div>
                <div class="hero-stat-val">{{ $salesStats['registrations'] }}</div>
                <div class="hero-stat-label">Registrations</div>
            </div>
        </div>
    </div>

    {{-- ── TASK QUICK VIEW ── --}}
    <span class="sec-label">Your Tasks Right Now</span>
    <div class="task-grid">
        <a href="{{ route('leads.index') }}" class="task-card" style="border-color:rgba(220,38,38,0.2);">
            <div class="task-icon" style="background:var(--red-l);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <div>
                <div class="task-num" style="color:var(--red);">{{ $callsDueToday }}</div>
                <div class="task-label">Calls Today</div>
            </div>
        </a>
        <a href="{{ route('leads.index') }}" class="task-card" style="border-color:rgba(245,145,30,0.2);">
            <div class="task-icon" style="background:var(--orange-l);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="task-num" style="color:var(--orange);">{{ $leadsStats['my_overdue'] }}</div>
                <div class="task-label">Overdue Leads</div>
            </div>
        </a>
        <a href="{{ route('outstanding.index') }}" class="task-card" style="border-color:rgba(220,38,38,0.15);">
            <div class="task-icon" style="background:var(--red-l);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <div class="task-num" style="color:var(--red);">{{ $outstandingStats['restricted'] }}</div>
                <div class="task-label">Restricted Students</div>
            </div>
        </a>
        <a href="{{ route('leads.public') }}" class="task-card" style="border-color:rgba(27,79,168,0.15);">
            <div class="task-icon" style="background:var(--blue-l);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div>
                <div class="task-num" style="color:var(--blue);">{{ $leadsStats['public'] }}</div>
                <div class="task-label">Public Leads</div>
            </div>
        </a>
    </div>

    {{-- ── SALES KPIs ── --}}
    <span class="sec-label">Sales Performance — {{ now()->format('F Y') }}</span>
    <div class="kpi-grid kpi-grid-4">
        <div class="kpi-card" style="--kc:var(--blue)">
            <div class="kpi-label">Monthly Target</div>
            <div class="kpi-val">{{ number_format($salesStats['target']) }}</div>
            <div class="kpi-sub">LE — set by admin</div>
        </div>
        <div class="kpi-card" style="--kc:var(--green)">
            <div class="kpi-label">Achieved</div>
            <div class="kpi-val">{{ number_format($salesStats['achieved']) }}</div>
            <div class="kpi-sub">LE this month</div>
            <div class="kpi-prog"><div class="kpi-prog-fill" style="width:{{ min(100,$salesStats['percentage']) }}%;background:var(--green)"></div></div>
        </div>
        <div class="kpi-card" style="--kc:var(--orange)">
            <div class="kpi-label">Remaining</div>
            <div class="kpi-val">{{ number_format($salesStats['remaining']) }}</div>
            <div class="kpi-sub">LE to hit target</div>
        </div>
        <div class="kpi-card" style="--kc:var(--purple)">
            <div class="kpi-label">Achievement Rate</div>
            <div class="kpi-val">{{ $salesStats['percentage'] }}<span style="font-size:16px">%</span></div>
            <div class="kpi-prog"><div class="kpi-prog-fill" style="width:{{ min(100,$salesStats['percentage']) }}%;background:var(--purple)"></div></div>
        </div>
    </div>

    {{-- ── LEADS KPIs ── --}}
    <span class="sec-label">Follow-up Pipeline</span>
    <div class="kpi-grid kpi-grid-4">
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--blue)">
            <div class="kpi-label">My Total Leads</div>
            <div class="kpi-val">{{ $leadsStats['my_total'] }}</div>
            <div class="kpi-link">View List →</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--orange)">
            <div class="kpi-label">Active Follow-ups</div>
            <div class="kpi-val">{{ $leadsStats['my_active'] }}</div>
            <div class="kpi-sub">waiting / call again</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--green)">
            <div class="kpi-label">Registered</div>
            <div class="kpi-val">{{ $leadsStats['my_registered'] }}</div>
            <div class="kpi-sub">converted to students</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--red)">
            <div class="kpi-label">Overdue Calls</div>
            <div class="kpi-val">{{ $leadsStats['my_overdue'] }}</div>
            <div class="kpi-sub">past 4-day deadline</div>
            <div class="kpi-link">Follow Up →</div>
        </a>
    </div>

    {{-- ── OUTSTANDING ── --}}
    <span class="sec-label">Outstanding Balances</span>
    <div class="kpi-grid kpi-grid-3">
        <a href="{{ route('outstanding.index') }}" class="kpi-card" style="--kc:var(--red)">
            <div class="kpi-label">Outstanding Students</div>
            <div class="kpi-val">{{ $outstandingStats['count'] }}</div>
            <div class="kpi-sub">with unpaid balance</div>
            <div class="kpi-link">View All →</div>
        </a>
        <a href="{{ route('outstanding.index') }}" class="kpi-card" style="--kc:var(--red)">
            <div class="kpi-label">Restricted Students</div>
            <div class="kpi-val">{{ $outstandingStats['restricted'] }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </a>
        <div class="kpi-card" style="--kc:var(--orange)">
            <div class="kpi-label">Total Unpaid Balance</div>
            <div class="kpi-val" style="font-size:22px;">{{ number_format($outstandingStats['total_le']) }}</div>
            <div class="kpi-sub">LE outstanding</div>
        </div>
    </div>

    {{-- ── RECENT ACTIVITY ── --}}
    <span class="sec-label">Recent Activity</span>
    <div class="two-col">

        {{-- Recent Leads --}}
        <div class="mini-card">
            <div class="mini-card-header">
                <div class="mini-card-title">Recent Leads</div>
                <a href="{{ route('leads.index') }}" class="mini-card-link">View All →</a>
            </div>
            @forelse($recentLeads as $lead)
            <div class="mini-row">
                <div class="mini-avatar">{{ strtoupper(substr($lead->full_name, 0, 1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="mini-row-name">{{ $lead->full_name }}</div>
                    <div class="mini-row-sub">
                        {{ $lead->courseTemplate?->name ?? '—' }}
                        · {{ $lead->created_at->diffForHumans() }}
                    </div>
                </div>
                @php
                    $bMap = ['Waiting'=>'badge-waiting','Call_Again'=>'badge-call','Registered'=>'badge-registered','Archived'=>'badge-archived'];
                    $isOverdue = in_array($lead->status,['Waiting','Call_Again']) && $lead->updated_at->diffInDays(now()) >= 4;
                @endphp
                <span class="badge {{ $isOverdue ? 'badge-overdue' : ($bMap[$lead->status] ?? 'badge-waiting') }}">
                    {{ $isOverdue ? 'Overdue' : str_replace('_',' ',$lead->status) }}
                </span>
            </div>
            @empty
            <div class="mini-empty">No leads yet — add your first lead!</div>
            @endforelse
        </div>

        {{-- Recent Payments --}}
        <div class="mini-card">
            <div class="mini-card-header">
                <div class="mini-card-title">Recent Payments</div>
                <a href="{{ route('sales.index') }}" class="mini-card-link">Sales Table →</a>
            </div>
            @forelse($recentPayments as $tx)
            <div class="mini-row">
                <div class="mini-avatar" style="background:var(--green-l);color:var(--green);">
                    {{ strtoupper(substr($tx->enrollment?->student?->full_name ?? '?', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="mini-row-name">{{ $tx->enrollment?->student?->full_name ?? '—' }}</div>
                    <div class="mini-row-sub">{{ $tx->enrollment?->courseTemplate?->name ?? '—' }} · {{ $tx->created_at->diffForHumans() }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:var(--green);letter-spacing:1px;">+{{ number_format($tx->amount) }}</div>
                    <div style="font-size:9px;color:var(--faint);margin-top:1px;">{{ $tx->payment_method }}</div>
                </div>
            </div>
            @empty
            <div class="mini-empty">No payments recorded yet.</div>
            @endforelse
        </div>

    </div>

</div>

<script>
// Animate KPI numbers
document.querySelectorAll('.kpi-val').forEach(el => {
    const text = el.textContent.trim();
    const num  = parseFloat(text.replace(/[^0-9.]/g, ''));
    if (isNaN(num) || num === 0) return;
    const suffix = text.replace(/[\d,. ]/g, '').trim();
    const dur = 700, start = performance.now();
    const isFloat = text.includes('.');
    (function tick(now) {
        const pct = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        const v = num * ease;
        el.textContent = (isFloat ? v.toFixed(1) : Math.round(v).toLocaleString()) + (suffix ? '' + suffix : '');
        if (pct < 1) requestAnimationFrame(tick);
    })(start);
});
</script>

@endsection