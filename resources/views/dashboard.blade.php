@extends('layouts.app')
@section('title', 'Leads Dashboard')
@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    * { box-sizing: border-box; }
    body { background: #F8F6F2; }

    .dash-wrap {
        display: flex;
        min-height: calc(100vh - 62px);
        font-family: 'DM Sans', sans-serif;
        background: #F8F6F2;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: 220px;
        flex-shrink: 0;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border-right: 1px solid rgba(27,79,168,0.08);
        padding: 28px 0;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 62px;
        height: calc(100vh - 62px);
        overflow-y: auto;
    }

    .sidebar-section-label {
        font-size: 8px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #F5911E;
        padding: 0 20px;
        margin-bottom: 8px;
        margin-top: 20px;
    }
    .sidebar-section-label:first-child { margin-top: 0; }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #7A8A9A;
        text-decoration: none;
        transition: all 0.2s;
        border-left: 2px solid transparent;
        position: relative;
    }
    .sidebar-link:hover {
        color: #1B4FA8;
        background: rgba(27,79,168,0.03);
        border-left-color: rgba(27,79,168,0.2);
        text-decoration: none;
    }
    .sidebar-link.active {
        color: #1B4FA8;
        background: rgba(27,79,168,0.05);
        border-left-color: #1B4FA8;
        font-weight: 500;
    }
    .sidebar-link svg { flex-shrink: 0; opacity: 0.6; }
    .sidebar-link.active svg { opacity: 1; }

    .sidebar-divider {
        height: 1px;
        background: rgba(27,79,168,0.06);
        margin: 12px 20px;
    }

    /* ── MAIN ── */
    .dash-main {
        flex: 1;
        padding: 36px 32px;
        overflow-x: hidden;
    }

    .page-eyebrow { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title   { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; margin-bottom: 4px; }
    .page-subtitle { font-size: 12px; color: #7A8A9A; }

    /* ── STAT CARDS ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.08);
        border-radius: 6px;
        padding: 16px 18px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(27,79,168,0.04);
    }
    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent, #1B4FA8), transparent);
    }
    .stat-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: #7A8A9A; margin-bottom: 8px; }
    .stat-value { font-family: 'Bebas Neue', sans-serif; font-size: 32px; letter-spacing: 2px; color: var(--accent, #1B4FA8); line-height: 1; }
    .stat-sub   { font-size: 10px; color: #AAB8C8; margin-top: 4px; }

    /* ── SECTION TITLE ── */
    .section-title {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 14px; padding-bottom: 8px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
    }

    /* ── PERIOD CARDS ── */
    .period-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 28px;
    }

    .period-card {
        background: rgba(255,255,255,0.8);
        border: 1px solid rgba(27,79,168,0.08);
        border-radius: 6px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(27,79,168,0.03);
    }
    .period-label { font-size: 9px; letter-spacing: 3px; text-transform: uppercase; color: #AAB8C8; margin-bottom: 12px; }
    .period-row   { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid rgba(27,79,168,0.04); }
    .period-row:last-child { border-bottom: none; }
    .period-name  { font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: #7A8A9A; }
    .period-num   { font-family: 'Bebas Neue', sans-serif; font-size: 18px; color: #1B4FA8; letter-spacing: 1px; }

    /* ── BAR CHART ── */
    .bar-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 28px;
    }

    .bar-card {
        background: rgba(255,255,255,0.8);
        border: 1px solid rgba(27,79,168,0.08);
        border-radius: 6px;
        padding: 20px 22px;
        box-shadow: 0 2px 10px rgba(27,79,168,0.03);
    }

    .bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .bar-row:last-child { margin-bottom: 0; }
    .bar-name { font-size: 9px; letter-spacing: 1px; text-transform: uppercase; color: #7A8A9A; width: 90px; flex-shrink: 0; }
    .bar-track { flex: 1; height: 6px; background: rgba(27,79,168,0.06); border-radius: 3px; overflow: hidden; }
    .bar-fill  { height: 100%; border-radius: 3px; transition: width 0.8s cubic-bezier(0.16,1,0.3,1); }
    .bar-num   { font-family: 'Bebas Neue', sans-serif; font-size: 14px; color: #1B4FA8; letter-spacing: 1px; width: 28px; text-align: right; flex-shrink: 0; }

    /* ── RECENT TABLE ── */
    .recent-card {
        background: rgba(255,255,255,0.8);
        border: 1px solid rgba(27,79,168,0.08);
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(27,79,168,0.03);
        margin-bottom: 28px;
    }
    .recent-card table { width: 100%; border-collapse: collapse; }
    .recent-card thead th {
        padding: 10px 14px; font-size: 9px; letter-spacing: 2px;
        text-transform: uppercase; color: #7A8A9A; font-weight: 500;
        background: rgba(27,79,168,0.02); text-align: left;
    }
    .recent-card tbody tr { border-top: 1px solid rgba(27,79,168,0.04); transition: background 0.2s; }
    .recent-card tbody tr:hover { background: rgba(27,79,168,0.02); }
    .recent-card tbody td { padding: 10px 14px; font-size: 12px; color: #4A5A7A; }

    .tag-sm {
        display: inline-block; font-size: 8px; letter-spacing: 1px;
        padding: 2px 7px; border-radius: 3px; text-transform: uppercase; font-weight: 500;
    }
    .tag-waiting      { color: #7A8A9A; background: rgba(122,138,154,0.08); border: 1px solid rgba(122,138,154,0.2); }
    .tag-call_again   { color: #C47010; background: rgba(245,145,30,0.08); border: 1px solid rgba(245,145,30,0.25); }
    .tag-registered   { color: #15803D; background: rgba(21,128,61,0.08); border: 1px solid rgba(21,128,61,0.2); }
    .tag-not_interested { color: #DC2626; background: rgba(220,38,38,0.06); border: 1px solid rgba(220,38,38,0.2); }
    .tag-archived     { color: #9A8A7A; background: rgba(154,138,122,0.08); border: 1px solid rgba(154,138,122,0.2); }
    .tag-scheduled    { color: #1B6FA8; background: rgba(27,111,168,0.08); border: 1px solid rgba(27,111,168,0.2); }

    @media (max-width: 900px) {
        .sidebar { display: none; }
        .period-grid { grid-template-columns: 1fr; }
        .bar-section { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .dash-main { padding: 20px 16px; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="dash-wrap">

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">

        <div class="sidebar-section-label">Overview</div>
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section-label">Leads</div>

        <a href="{{ route('leads.index') }}"
           class="sidebar-link {{ request()->routeIs('leads.index') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            My Leads
        </a>

        <a href="{{ route('leads.public') }}"
           class="sidebar-link {{ request()->routeIs('leads.public') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
            Public Leads
        </a>

        <a href="{{ route('leads.archived') }}"
           class="sidebar-link {{ request()->routeIs('leads.archived') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="21 8 21 21 3 21 3 8"/>
                <rect x="1" y="3" width="22" height="5"/>
                <line x1="10" y1="12" x2="14" y2="12"/>
            </svg>
            Archived
        </a>

        <div class="sidebar-divider"></div>
        <a href="{{ route('leads.create') }}"
           class="sidebar-link {{ request()->routeIs('leads.create') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Lead
        </a>

    </aside>


</div>

@endsection