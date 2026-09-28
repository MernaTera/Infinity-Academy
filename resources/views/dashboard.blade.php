@extends('layouts.leads')
@section('title', 'Dashboard')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
        --green:#059669; --green-dk:#15803D; --purple:#7C3AED; --red:#DC2626;
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --card:rgba(255,255,255,0.5); --border:rgba(255,255,255,0.6);
        --glass-sh:0 8px 30px rgba(23,45,90,0.07);
        --bg:#F8F6F2;
    }
    * { box-sizing:border-box; }

    .dash {background:var(--bg); min-height:100vh; padding:30px 34px 44px; color:var(--text); font-family:'DM Sans',sans-serif; }
    @media (max-width:600px){ .dash{ padding:18px 16px 32px; } }

    .glass { background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--border); box-shadow:var(--glass-sh); }

    .dash-head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:14px; }
    .dash-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); font-weight:600; margin-bottom:7px; }
    .dash-title { font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0; }
    .dash-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .btn-g { display:inline-flex; align-items:center; gap:8px; padding:12px 18px; border-radius:13px; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-weight:600; text-decoration:none; transition:transform .2s, box-shadow .2s, background .2s; }
    .btn-g-ghost { background:rgba(255,255,255,0.5); border:1px solid var(--border); color:var(--blue); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); }
    .btn-g-ghost:hover { background:rgba(255,255,255,0.72); text-decoration:none; color:var(--blue); }
    .btn-g-solid { background:linear-gradient(120deg,var(--blue),var(--blue-2)); border:none; color:#fff; box-shadow:0 10px 24px rgba(27,79,168,0.32); }
    .btn-g-solid:hover { transform:translateY(-2px); text-decoration:none; color:#fff; }

    .shift-chip { display:inline-flex; align-items:center; gap:9px; padding:9px 15px; border-radius:13px; margin-bottom:22px; }
    .shift-chip .sc-k { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); font-weight:600; }
    .shift-chip .sc-v { font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:1px; color:var(--text); line-height:1; margin-top:2px; }

    .sec { font-size:10px; letter-spacing:3px; text-transform:uppercase; color:var(--muted); font-weight:600; margin:26px 2px 12px; }

    .calls-banner { display:flex; align-items:center; gap:14px; padding:16px 20px; border-radius:16px; text-decoration:none; transition:transform .2s; }
    .calls-banner:hover { transform:translateY(-2px); text-decoration:none; }
    .calls-icon { width:44px; height:44px; border-radius:13px; background:linear-gradient(135deg,var(--orange),#ffb45f); display:grid; place-items:center; flex-shrink:0; box-shadow:0 8px 18px rgba(245,145,30,0.32); }
    .calls-text { flex:1; }
    .calls-title { font-size:14px; font-weight:600; color:var(--text); }
    .calls-sub { font-size:11px; color:var(--muted); margin-top:2px; }
    .calls-count { font-family:'Bebas Neue',sans-serif; font-size:36px; color:var(--orange-dk); letter-spacing:1px; }

    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
    .kpi-grid.cols-3 { grid-template-columns:repeat(3,1fr); }
    @media (max-width:900px){ .kpi-grid, .kpi-grid.cols-3 { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:520px){ .kpi-grid, .kpi-grid.cols-3 { grid-template-columns:1fr; } }

    .kpi { border-radius:18px; padding:20px; text-decoration:none; display:block; transition:transform .2s, box-shadow .2s; }
    .kpi:hover { transform:translateY(-3px); box-shadow:0 16px 38px rgba(23,45,90,0.13); text-decoration:none; }
    .kpi-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
    .kpi-chip { width:38px; height:38px; border-radius:12px; display:grid; place-items:center; background:var(--kcl,rgba(27,79,168,0.12)); }
    .kpi-chip svg { width:18px; height:18px; stroke:var(--kc,var(--blue)); }
    .kpi-arrow { color:var(--faint); opacity:0; transition:opacity .2s, transform .2s; }
    .kpi:hover .kpi-arrow { opacity:1; transform:translateX(2px); }
    .kpi-label { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; }
    .kpi-val { font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:1px; line-height:1; color:var(--kc,var(--blue)); margin-top:4px; }
    .kpi-sub { font-size:11px; color:var(--faint); margin-top:6px; }

    .panel { border-radius:20px; padding:26px; }
    .target-body { display:grid; grid-template-columns:auto 1fr; gap:34px; align-items:center; }
    @media (max-width:640px){ .target-body{ grid-template-columns:1fr; gap:22px; justify-items:center; } }
    .target-ring { position:relative; width:158px; height:158px; flex-shrink:0; }    .ring-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .ring-pct { font-family:'Bebas Neue',sans-serif; font-size:42px; color:var(--green-dk); line-height:1; letter-spacing:1px; }
    .ring-lbl { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-top:3px; }
    .t-stats { display:flex; flex-direction:column; gap:0; width:100%; }
    .t-row { display:flex; align-items:center; justify-content:space-between; padding:13px 0; border-bottom:1px solid rgba(27,79,168,0.08); }
    .t-row:last-child { border-bottom:none; }
    .t-k { font-size:12px; color:var(--muted); }
    .t-v { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:1px; }
    .target-cta { display:flex; align-items:center; justify-content:center; gap:8px; margin-top:22px; padding:13px; border-radius:13px; background:linear-gradient(120deg,var(--blue),var(--blue-2)); color:#fff; font-size:11px; letter-spacing:2px; text-transform:uppercase; font-weight:600; text-decoration:none; box-shadow:0 10px 24px rgba(27,79,168,0.30); transition:transform .2s, box-shadow .2s; }
    .target-cta:hover { transform:translateY(-2px); box-shadow:0 14px 30px rgba(27,79,168,0.4); color:#fff; text-decoration:none; }

    .two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media (max-width:900px){ .two-col{ grid-template-columns:1fr; } }
    .list-card { border-radius:20px; overflow:hidden; }
    .list-head { padding:18px 22px 14px; display:flex; align-items:center; justify-content:space-between; }
    .list-title { font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:2px; color:var(--text); }
    .list-link { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--blue); font-weight:600; text-decoration:none; }
    .list-link:hover { text-decoration:underline; }
    .list-body { padding:2px 22px 12px; }
    .row { display:flex; align-items:center; gap:13px; padding:12px 0; border-bottom:1px solid rgba(27,79,168,0.06); }
    .row:last-child { border-bottom:none; }
    .row-av { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; font-family:'Bebas Neue',sans-serif; font-size:17px; flex-shrink:0; }
    .row-name { font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .row-sub { font-size:11px; color:var(--muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .row-empty { text-align:center; padding:34px 20px; color:var(--faint); font-size:12px; }

    .pill { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:9px; font-weight:600; letter-spacing:0.3px; }
    .p-course { background:rgba(27,79,168,0.10); color:var(--blue); }
    .p-test { background:rgba(124,58,237,0.10); color:var(--purple); }
    .p-material { background:rgba(245,145,30,0.12); color:var(--orange-dk); }
    .p-install { background:rgba(5,150,105,0.10); color:var(--green-dk); }
    .p-waiting { background:rgba(122,138,154,0.14); color:var(--muted); }
    .p-call { background:rgba(245,145,30,0.12); color:var(--orange-dk); }
    .p-sched { background:rgba(27,79,168,0.10); color:var(--blue); }
    .p-reg { background:rgba(5,150,105,0.10); color:var(--green-dk); }
    .method { font-size:9px; color:var(--faint); margin-top:2px; }
</style>

<div class="dash">

    <div class="dash-head">
        <div>
            <div class="dash-eyebrow">Customer Service · {{ now()->format('l, d M Y') }}@if($currentPatch) · {{ $currentPatch->name }}@endif</div>
            <h1 class="dash-title">Welcome back{{ $employee?->full_name ? ', '.explode(' ', $employee->full_name)[0] : '' }}</h1>
        </div>
        <div class="dash-actions">
            <a href="{{ route('leads.public') }}" class="btn-g btn-g-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 11l2 2 4-4M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                Public Pool
            </a>
            <a href="{{ route('leads.create') }}" class="btn-g btn-g-solid">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Lead
            </a>
        </div>
    </div>

    @if(!empty($me) && ($me->work_start_time || $me->work_end_time))
    <div class="shift-chip glass">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <div>
            <div class="sc-k">Your Shift</div>
            <div class="sc-v">
                {{ $me->work_start_time ? \Carbon\Carbon::parse($me->work_start_time)->format('g:i A') : '—' }}
                <span style="color:var(--faint);">→</span>
                {{ $me->work_end_time ? \Carbon\Carbon::parse($me->work_end_time)->format('g:i A') : '—' }}
            </div>
        </div>
    </div>
    @endif

    @if($callsDueToday > 0)
    <a href="{{ route('leads.index') }}" class="calls-banner glass">
        <div class="calls-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div class="calls-text">
            <div class="calls-title">You have calls scheduled today</div>
            <div class="calls-sub">Follow up with your leads to keep them warm</div>
        </div>
        <div class="calls-count">{{ $callsDueToday }}</div>
    </a>
    @endif

    <div class="sec">My Leads</div>
    <div class="kpi-grid">
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#1B4FA8;--kcl:rgba(27,79,168,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Total Leads</div>
            <div class="kpi-val">{{ $leadsStats['my_total'] }}</div>
            <div class="kpi-sub">assigned to me</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#C47010;--kcl:rgba(245,145,30,0.14)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Active</div>
            <div class="kpi-val">{{ $leadsStats['my_active'] }}</div>
            <div class="kpi-sub">in follow-up</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#15803D;--kcl:rgba(5,150,105,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Registered</div>
            <div class="kpi-val">{{ $leadsStats['my_registered'] }}</div>
            <div class="kpi-sub">converted</div>
        </a>
        <a href="{{ route('leads.archived') }}" class="kpi glass" style="--kc:#DC2626;--kcl:rgba(220,38,38,0.10)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Archived</div>
            <div class="kpi-val">{{ $leadsStats['archived'] }}</div>
            <div class="kpi-sub">not currently active</div>
        </a>
    </div>

    <div class="sec">Outstanding &amp; Collections</div>
    <div class="kpi-grid">
        <a href="{{ route('outstanding.index') }}" class="kpi glass" style="--kc:#DC2626;--kcl:rgba(220,38,38,0.10)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="12"/><line x1="19" y1="16" x2="19.01" y2="16"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Outstanding Students</div>
            <div class="kpi-val">{{ $outstandingStats['count'] }}</div>
            <div class="kpi-sub">with balance due</div>
        </a>
        <a href="{{ route('outstanding.index') }}" class="kpi glass" style="--kc:#C47010;--kcl:rgba(245,145,30,0.14)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M16 12h.01"/><path d="M2 10h20"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Total Outstanding</div>
            <div class="kpi-val">{{ number_format($outstandingStats['total_le']) }}</div>
            <div class="kpi-sub">LE to collect</div>
        </a>
        <a href="{{ route('outstanding.index') }}" class="kpi glass" style="--kc:#DC2626;--kcl:rgba(220,38,38,0.10)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $outstandingStats['restricted'] }}</div>
            <div class="kpi-sub">access restricted</div>
        </a>
        <a href="{{ route('cs.postponed') }}" class="kpi glass" style="--kc:#7C3AED;--kcl:rgba(124,58,237,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Postponed</div>
            <div class="kpi-val">{{ $postponedCount }}</div>
            <div class="kpi-sub">on hold</div>
        </a>
    </div>

    <div class="sec">Monthly Target</div>
    <div class="panel glass">
        <div class="target-body">
            @php
                $pct = min($salesStats['percentage'], 100);
                $circumference = 2 * 3.14159 * 66;
                $offset = $circumference - ($pct / 100) * $circumference;
            @endphp
            <div class="target-ring">
                <svg width="158" height="158">
                    <circle cx="79" cy="79" r="66" fill="none" stroke="rgba(27,79,168,0.10)" stroke-width="12"/>
                    <circle cx="79" cy="79" r="66" fill="none" stroke="var(--green)" stroke-width="12"
                            stroke-linecap="round"
                            transform="rotate(-90 79 79)"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="ring-center">
                    <div class="ring-pct">{{ $salesStats['percentage'] }}%</div>
                    <div class="ring-lbl">Achieved</div>
                </div>
            </div>
            <div class="t-stats">
                <div class="t-row"><span class="t-k">Target</span><span class="t-v" style="color:var(--text);">{{ number_format($salesStats['target']) }} LE</span></div>
                <div class="t-row"><span class="t-k">Achieved</span><span class="t-v" style="color:var(--green-dk);">{{ number_format($salesStats['achieved']) }} LE</span></div>
                <div class="t-row"><span class="t-k">Remaining</span><span class="t-v" style="color:var(--orange-dk);">{{ number_format($salesStats['remaining']) }} LE</span></div>
                <div class="t-row"><span class="t-k">Registrations this month</span><span class="t-v" style="color:var(--blue);">{{ $salesStats['registrations'] }}</span></div>
            </div>
        </div>
        <a href="{{ route('sales.index') }}" class="target-cta">
            Go to Sales Table
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    <div class="sec">Recent Activity</div>
    <div class="two-col">
        <div class="list-card glass">
            <div class="list-head">
                <div class="list-title">Recent Leads</div>
                <a href="{{ route('leads.index') }}" class="list-link">View all</a>
            </div>
            <div class="list-body">
                @forelse($recentLeads as $lead)
                    @php
                        $lmb = match($lead->status) {
                            'Waiting'        => ['p-waiting','Waiting'],
                            'Call_Again'     => ['p-call','Call Again'],
                            'Scheduled_Call' => ['p-sched','Scheduled'],
                            'Registered'     => ['p-reg','Registered'],
                            default          => ['p-waiting', str_replace('_',' ',$lead->status)],
                        };
                    @endphp
                    <div class="row">
                        <div class="row-av" style="background:rgba(27,79,168,0.10);color:var(--blue);">{{ strtoupper(substr($lead->full_name,0,1)) }}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="row-name">{{ $lead->full_name }}</div>
                            <div class="row-sub">{{ $lead->phone }} · {{ $lead->courseTemplate?->name ?? 'No course' }}</div>
                        </div>
                        <span class="pill {{ $lmb[0] }}">{{ $lmb[1] }}</span>
                    </div>
                @empty
                    <div class="row-empty">No leads yet. Start by adding one!</div>
                @endforelse
            </div>
        </div>

        <div class="list-card glass">
            <div class="list-head">
                <div class="list-title">Recent Payments</div>
                <a href="{{ route('sales.index') }}" class="list-link">Sales table</a>
            </div>
            <div class="list-body">
                @forelse($recentPayments as $tx)
                    @php
                        if ($tx->transaction_type === 'Installment') {
                            $catClass = 'p-install'; $catLabel = 'Installment';
                        } else {
                            $catClass = match($tx->transaction_category) {
                                'Course'   => 'p-course',
                                'Test'     => 'p-test',
                                'Material' => 'p-material',
                                default    => 'p-course',
                            };
                            $catLabel = $tx->transaction_category;
                        }
                        $methodLabel = match($tx->payment_method) {
                            'Transfer' => 'Instapay',
                            'Online'   => 'Vodafone Cash',
                            'Cash'     => 'Cash',
                            'Card'     => 'Card',
                            default    => $tx->payment_method,
                        };
                    @endphp
                    <div class="row">
                        <div class="row-av" style="background:rgba(5,150,105,0.10);color:var(--green);">
                            {{ strtoupper(substr($tx->enrollment?->student?->full_name ?? '?', 0, 1)) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="row-name">{{ $tx->enrollment?->student?->full_name ?? '—' }}</div>
                            <div class="row-sub"><span class="pill {{ $catClass }}">{{ $catLabel }}</span> · {{ $tx->created_at->diffForHumans() }}</div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:17px;color:var(--green-dk);letter-spacing:1px;">+{{ number_format($tx->amount) }}</div>
                            <div class="method">{{ $methodLabel }}</div>
                        </div>
                    </div>
                @empty
                    <div class="row-empty">No payments recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script>
document.querySelectorAll('.kpi-val').forEach(el => {
    const text = el.textContent.trim();
    const num  = parseFloat(text.replace(/[^0-9.]/g, ''));
    if (isNaN(num) || num === 0) return;
    const dur = 700, start = performance.now();
    (function tick(now) {
        const pct = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        el.textContent = Math.round(num * ease).toLocaleString();
        if (pct < 1) requestAnimationFrame(tick);
    })(start);
});
</script>

@endsection