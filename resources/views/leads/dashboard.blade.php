@extends('layouts.leads')
@section('title', 'Leads Dashboard')

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

    .dash { background:var(--bg); min-height:100vh; padding:30px 34px 44px; color:var(--text); font-family:'DM Sans',sans-serif; }
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

    .sec { font-size:10px; letter-spacing:3px; text-transform:uppercase; color:var(--muted); font-weight:600; margin:26px 2px 12px; }

    .calls-banner { display:flex; align-items:center; gap:14px; padding:16px 20px; border-radius:16px; text-decoration:none; transition:transform .2s; }
    .calls-banner:hover { transform:translateY(-2px); text-decoration:none; }
    .calls-icon { width:44px; height:44px; border-radius:13px; background:linear-gradient(135deg,var(--orange),#ffb45f); display:grid; place-items:center; flex-shrink:0; box-shadow:0 8px 18px rgba(245,145,30,0.32); }
    .calls-text { flex:1; }
    .calls-title { font-size:14px; font-weight:600; color:var(--text); }
    .calls-sub { font-size:11px; color:var(--muted); margin-top:2px; }
    .calls-count { font-family:'Bebas Neue',sans-serif; font-size:36px; color:var(--orange-dk); letter-spacing:1px; }

    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
    @media (max-width:900px){ .kpi-grid { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:520px){ .kpi-grid { grid-template-columns:1fr; } }

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

    .two-col { display:grid; grid-template-columns:1.3fr 1fr; gap:16px; }
    @media (max-width:960px){ .two-col{ grid-template-columns:1fr; } }
    .two-even { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media (max-width:960px){ .two-even{ grid-template-columns:1fr; } }

    .panel { border-radius:20px; padding:24px 26px; }
    .panel-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
    .panel-title { font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:2px; color:var(--text); }
    .panel-hint { font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--faint); font-weight:600; }

    .bar-row { display:flex; align-items:center; gap:13px; margin-bottom:14px; }
    .bar-row:last-child { margin-bottom:0; }
    .bar-main { flex:1; min-width:0; }
    .bar-top { display:flex; align-items:baseline; justify-content:space-between; margin-bottom:6px; }
    .bar-name { font-size:11px; font-weight:600; color:var(--text); letter-spacing:0.2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .bar-count { font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:1px; color:var(--bc,var(--blue)); flex-shrink:0; margin-left:10px; }
    .bar-track { height:8px; background:rgba(27,79,168,0.08); border-radius:6px; overflow:hidden; }
    .bar-fill { height:100%; border-radius:6px; background:var(--bc,var(--blue)); transition:width .6s cubic-bezier(0.16,1,0.3,1); }
    .bar-pct { font-size:10px; color:var(--muted); min-width:38px; text-align:right; font-weight:600; flex-shrink:0; }

    .sub-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; margin:0 0 12px; }
    .sub-label.mt { margin-top:20px; }

    .period-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .period-box { background:rgba(255,255,255,0.4); border:1px solid var(--border); border-radius:14px; padding:15px 12px; text-align:center; }
    .period-box-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:8px; }
    .period-box-val { font-family:'Bebas Neue',sans-serif; font-size:30px; line-height:0.9; }
    .period-box-sub { font-size:9px; color:var(--faint); margin-top:5px; }

    .conv-callout { margin-top:18px; padding:15px 18px; background:rgba(5,150,105,0.08); border:1px solid rgba(5,150,105,0.16); border-radius:14px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .conv-k { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--green-dk); font-weight:600; }
    .conv-s { font-size:10px; color:var(--muted); margin-top:3px; }
    .conv-v { font-family:'Bebas Neue',sans-serif; font-size:34px; color:var(--green-dk); line-height:1; letter-spacing:1px; }

    .list-card { border-radius:20px; overflow:hidden; }
    .list-head { padding:18px 22px 14px; display:flex; align-items:center; justify-content:space-between; }
    .list-title { font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:2px; color:var(--text); }
    .list-link { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--blue); font-weight:600; text-decoration:none; }
    .list-link:hover { text-decoration:underline; }
    .list-body { padding:2px 22px 14px; }
    .row { display:flex; align-items:center; gap:13px; padding:12px 0; border-bottom:1px solid rgba(27,79,168,0.06); }
    .row:last-child { border-bottom:none; }
    .row-av { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; font-family:'Bebas Neue',sans-serif; font-size:17px; flex-shrink:0; background:rgba(27,79,168,0.10); color:var(--blue); }
    .row-name { font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .row-sub { font-size:11px; color:var(--muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .row-empty { text-align:center; padding:34px 20px; color:var(--faint); font-size:12px; }
    .when { font-size:10px; font-weight:600; padding:4px 11px; border-radius:20px; white-space:nowrap; flex-shrink:0; }
    .when-today { background:rgba(245,145,30,0.12); color:var(--orange-dk); }
    .when-soon { background:rgba(27,79,168,0.10); color:var(--blue); }
    .when-past { background:rgba(220,38,38,0.10); color:var(--red); }

    .pill { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:9px; font-weight:600; letter-spacing:0.3px; }
    .p-waiting { background:rgba(122,138,154,0.14); color:var(--muted); }
    .p-call { background:rgba(245,145,30,0.12); color:var(--orange-dk); }
    .p-sched { background:rgba(27,79,168,0.10); color:var(--blue); }
    .p-reg { background:rgba(5,150,105,0.10); color:var(--green-dk); }
    .p-noint { background:rgba(220,38,38,0.10); color:var(--red); }
    .p-arch { background:rgba(154,138,122,0.14); color:#9A8A7A; }

    .tbl-scroll { overflow-x:auto; }
    .ltbl { width:100%; border-collapse:collapse; min-width:900px; }
    .ltbl thead th { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); padding:13px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:rgba(255,255,255,0.4); white-space:nowrap; }
    .ltbl tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.06); font-size:12px; color:var(--text); vertical-align:middle; }
    .ltbl tbody tr:last-child td { border-bottom:none; }
    .ltbl tbody tr { transition:background .15s; }
    .ltbl tbody tr:hover { background:rgba(27,79,168,0.04); }
    .lt-name-cell { display:flex; align-items:center; gap:11px; }
    .lt-avatar { width:34px; height:34px; border-radius:10px; background:rgba(27,79,168,0.10); display:grid; place-items:center; font-family:'Bebas Neue',sans-serif; font-size:14px; color:var(--blue); flex-shrink:0; }
    .lt-name { font-weight:600; color:var(--text); }
    .lt-phone { font-size:10px; color:var(--muted); margin-top:1px; }
    .lt-sub { font-size:10px; color:var(--faint); margin-top:1px; }
    .lt-muted { color:var(--faint); }
    .src-chip { display:inline-block; padding:3px 9px; border-radius:7px; font-size:10px; font-weight:500; background:rgba(27,79,168,0.06); color:var(--muted); border:1px solid var(--border); }
    .tbl-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .tbl-empty svg { margin-bottom:12px; opacity:0.4; }
</style>

@php
    $tot     = max($stats['total'], 1);
    $active  = $stats['waiting'] + $stats['call_again'] + $stats['scheduled'];

    $todayTotal = array_sum($today);
    $weekTotal  = array_sum($week);
    $monthTotal = array_sum($month);
    $registeredThisMonth = $month['Registered'] ?? 0;

    $statusRows = [
        ['Waiting',        $stats['waiting'],        'var(--muted)'],
        ['Call Again',     $stats['call_again'],     'var(--orange-dk)'],
        ['Scheduled',      $stats['scheduled'],      'var(--blue)'],
        ['Registered',     $stats['registered'],     'var(--green)'],
        ['Not Interested', $stats['not_interested'], 'var(--red)'],
    ];
    $maxStatus = max([$stats['waiting'], $stats['call_again'], $stats['scheduled'], $stats['registered'], $stats['not_interested'], 1]);
@endphp

<div class="dash">

    <div class="dash-head">
        <div>
            <div class="dash-eyebrow">My Pipeline · {{ now()->format('l, d M Y') }}</div>
            <h1 class="dash-title">Leads Dashboard</h1>
        </div>
        <div class="dash-actions">
            <a href="{{ route('leads.public') }}" class="btn-g btn-g-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 11l2 2 4-4M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                Public Pool
            </a>
            <a href="{{ route('leads.index') }}" class="btn-g btn-g-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                All Leads
            </a>
            <a href="{{ route('leads.create') }}" class="btn-g btn-g-solid">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Lead
            </a>
        </div>
    </div>

    @if(($stats['due_today'] + $stats['overdue']) > 0)
    <a href="{{ route('leads.index') }}" class="calls-banner glass">
        <div class="calls-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div class="calls-text">
            <div class="calls-title">{{ $stats['due_today'] }} follow-up{{ $stats['due_today'] == 1 ? '' : 's' }} due today{{ $stats['overdue'] > 0 ? ' · '.$stats['overdue'].' overdue' : '' }}</div>
            <div class="calls-sub">Call your leads back to keep the pipeline moving</div>
        </div>
        <div class="calls-count">{{ $stats['due_today'] + $stats['overdue'] }}</div>
    </a>
    @endif

    <div class="sec">Pipeline Overview</div>
    <div class="kpi-grid">
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#1B4FA8;--kcl:rgba(27,79,168,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Total Leads</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
            <div class="kpi-sub">assigned to me</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#C47010;--kcl:rgba(245,145,30,0.14)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M23 6l-9.5 9.5-5-5L1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Active Pipeline</div>
            <div class="kpi-val">{{ $active }}</div>
            <div class="kpi-sub">waiting · call again · scheduled</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#15803D;--kcl:rgba(5,150,105,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Registered</div>
            <div class="kpi-val">{{ $stats['registered'] }}</div>
            <div class="kpi-sub">converted</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#5A6A85;--kcl:rgba(90,106,133,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Not Interested</div>
            <div class="kpi-val">{{ $stats['not_interested'] }}</div>
            <div class="kpi-sub">closed / lost</div>
        </a>
    </div>

    <div class="sec">Follow-ups &amp; Status</div>
    <div class="kpi-grid">
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#C47010;--kcl:rgba(245,145,30,0.14)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Due Today</div>
            <div class="kpi-val">{{ $stats['due_today'] }}</div>
            <div class="kpi-sub">calls to make today</div>
        </a>
        <a href="{{ route('leads.archived') }}" class="kpi glass" style="--kc:#DC2626;--kcl:rgba(220,38,38,0.10)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Archived</div>
            <div class="kpi-val">{{ $stats['archived'] }}</div>
            <div class="kpi-sub">leads that are archived</div>
        </a>
        <a href="{{ route('leads.index') }}" class="kpi glass" style="--kc:#2D6FDB;--kcl:rgba(45,111,219,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Scheduled Calls</div>
            <div class="kpi-val">{{ $stats['scheduled'] }}</div>
            <div class="kpi-sub">booked follow-ups</div>
        </a>
        <a href="{{ route('leads.public') }}" class="kpi glass" style="--kc:#1B4FA8;--kcl:rgba(27,79,168,0.12)">
            <div class="kpi-top">
                <div class="kpi-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                <svg class="kpi-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="kpi-label">Public Pool</div>
            <div class="kpi-val">{{ $stats['public'] }}</div>
            <div class="kpi-sub">unclaimed · claim yours</div>
        </a>
    </div>

    <div class="sec">Performance</div>

        <div class="panel glass">
            <div class="panel-head">
                <div class="panel-title">Activity</div>
                <div class="panel-hint">Leads touched</div>
            </div>
            <div class="period-grid">
                <div class="period-box">
                    <div class="period-box-label">Today</div>
                    <div class="period-box-val" style="color:var(--blue);">{{ $todayTotal }}</div>
                    <div class="period-box-sub">updated</div>
                </div>
                <div class="period-box">
                    <div class="period-box-label">This Week</div>
                    <div class="period-box-val" style="color:var(--orange-dk);">{{ $weekTotal }}</div>
                    <div class="period-box-sub">updated</div>
                </div>
                <div class="period-box">
                    <div class="period-box-label">This Month</div>
                    <div class="period-box-val" style="color:var(--green-dk);">{{ $monthTotal }}</div>
                    <div class="period-box-sub">updated</div>
                </div>
            </div>
            <div class="conv-callout">
                <div>
                    <div class="conv-k">Registered This Month</div>
                    <div class="conv-s">new registrations added this month</div>
                </div>
                <div class="conv-v">{{ $registeredThisMonth }}</div>
            </div>
    </div>
    <div class="sec">Upcoming Follow-ups</div>
    <div class="list-card glass">
        <div class="list-body" style="padding-top:12px;">
            @forelse($upcomingFollowUps as $lead)
                @php
                    $callDate = \Carbon\Carbon::parse($lead->next_call_at);
                    $isToday  = $callDate->isToday();
                    $isPast   = $callDate->isPast() && !$isToday;
                    $whenClass = $isToday ? 'when-today' : ($isPast ? 'when-past' : 'when-soon');
                    $whenText  = $isToday ? 'Today · '.$callDate->format('H:i') : ($isPast ? $callDate->format('d M').' · past' : $callDate->format('d M · H:i'));
                @endphp
                <div class="row">
                    <div class="row-av">{{ strtoupper(substr($lead->full_name,0,1)) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="row-name">{{ $lead->full_name }}</div>
                        <div class="row-sub">{{ $lead->phone }} · {{ $lead->courseTemplate?->name ?? 'No course' }}</div>
                    </div>
                    <span class="when {{ $whenClass }}">{{ $whenText }}</span>
                </div>
            @empty
                <div class="row-empty">No scheduled follow-ups. Set call times on your leads to see them here.</div>
            @endforelse
        </div>
    </div>

    <div class="sec">Recent Leads · Detailed</div>
    <div class="list-card glass">
        <div class="tbl-scroll">
            <table class="ltbl">
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
                        @php
                            $badgeMap = [
                                'Waiting'        => ['p-waiting','Waiting'],
                                'Call_Again'     => ['p-call','Call Again'],
                                'Scheduled_Call' => ['p-sched','Scheduled'],
                                'Registered'     => ['p-reg','Registered'],
                                'Not_Interested' => ['p-noint','Not Interested'],
                                'Archived'       => ['p-arch','Archived'],
                            ];
                            [$bClass,$bLabel] = $badgeMap[$lead->status] ?? ['p-waiting', str_replace('_',' ',$lead->status)];
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
                            <td><span class="pill {{ $bClass }}">{{ $bLabel }}</span></td>
                            <td>
                                @if($lead->courseTemplate)
                                    <div style="font-weight:500;">{{ $lead->courseTemplate->name }}</div>
                                    @if($lead->level || $lead->sublevel)
                                        <div class="lt-sub">{{ $lead->level?->name }}{{ $lead->sublevel ? ' · '.$lead->sublevel->name : '' }}</div>
                                    @endif
                                @else
                                    <span class="lt-muted">—</span>
                                @endif
                            </td>
                            <td><span class="src-chip">{{ str_replace('_',' ',$lead->source) ?: '—' }}</span></td>
                            <td><span class="lt-muted">{{ $lead->degree ?: '—' }}</span></td>
                            <td>
                                @if($lead->start_preference_type)
                                    <span style="font-size:11px;">{{ $lead->start_preference_type }}</span>
                                    @if($lead->start_preference_date)
                                        <div class="lt-sub">{{ \Carbon\Carbon::parse($lead->start_preference_date)->format('d M Y') }}</div>
                                    @endif
                                @else
                                    <span class="lt-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->next_call_at)
                                    @php $nc = \Carbon\Carbon::parse($lead->next_call_at); @endphp
                                    <div style="font-size:11px; font-weight:600; color:{{ $nc->isToday() ? 'var(--orange-dk)' : ($nc->isPast() ? 'var(--red)' : 'var(--text)') }};">{{ $nc->format('d M') }}</div>
                                    <div class="lt-sub">{{ $nc->format('H:i') }}</div>
                                @else
                                    <span class="lt-muted">—</span>
                                @endif
                            </td>
                            <td><span class="lt-muted">{{ $lead->created_at?->format('d M Y') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="tbl-empty">
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
    <div class="sec">Insights</div>
    <div class="two-even">
        <div class="panel glass">
            <div class="panel-head">
                <div class="panel-title">Sources &amp; Courses</div>
                <div class="panel-hint">Where they come from</div>
            </div>
            @php $maxSrc = max(array_values($bySource) ?: [1]); @endphp
            <div class="sub-label">By Source</div>
            @forelse($bySource as $src => $cnt)
                <div class="bar-row" style="--bc:var(--blue)">
                    <div class="bar-main">
                        <div class="bar-top">
                            <span class="bar-name">{{ str_replace('_',' ',$src) ?: 'Unknown' }}</span>
                            <span class="bar-count">{{ $cnt }}</span>
                        </div>
                        <div class="bar-track"><div class="bar-fill" style="width:{{ round(($cnt / $maxSrc) * 100) }}%"></div></div>
                    </div>
                </div>
            @empty
                <div class="row-empty">No source data yet</div>
            @endforelse

            @if(!empty($byCourse))
                @php $maxCrs = max(array_values($byCourse) ?: [1]); @endphp
                <div class="sub-label mt">By Course Interest</div>
                @foreach($byCourse as $crs => $cnt)
                    <div class="bar-row" style="--bc:var(--orange)">
                        <div class="bar-main">
                            <div class="bar-top">
                                <span class="bar-name">{{ $crs }}</span>
                                <span class="bar-count">{{ $cnt }}</span>
                            </div>
                            <div class="bar-track"><div class="bar-fill" style="width:{{ round(($cnt / $maxCrs) * 100) }}%"></div></div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="panel glass">
            <div class="panel-head">
                <div class="panel-title">Leads by CS</div>
                <div class="panel-hint">Team distribution</div>
            </div>
            @php $maxCs = max(array_values($byCs) ?: [1]); @endphp
            @forelse($byCs as $csName => $cnt)
                <div class="bar-row" style="--bc:var(--purple)">
                    <div class="bar-main">
                        <div class="bar-top">
                            <span class="bar-name">{{ $csName }}</span>
                            <span class="bar-count">{{ $cnt }}</span>
                        </div>
                        <div class="bar-track"><div class="bar-fill" style="width:{{ round(($cnt / $maxCs) * 100) }}%"></div></div>
                    </div>
                </div>
            @empty
                <div class="row-empty">No CS assignment data yet</div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.kpi-val').forEach(el => {
    const raw = el.textContent.trim();
    const suffix = raw.includes('%') ? '%' : '';
    const num = parseFloat(raw.replace(/[^0-9.]/g, ''));
    if (isNaN(num)) return;
    if (num === 0) { el.textContent = '0' + suffix; return; }
    const dur = 700, start = performance.now();
    (function tick(now) {
        const pct = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        el.textContent = Math.round(num * ease).toLocaleString() + suffix;
        if (pct < 1) requestAnimationFrame(tick);
    })(start);
});
</script>

@endsection
