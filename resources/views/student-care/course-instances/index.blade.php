@extends('student-care.layouts.app')

@section('title', 'Course Instances')

@include('student-care.course-instances.partials.schedule-modal')
@include('student-care.course-instances.partials.create-modal')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    body, .ci-page * { font-family: 'DM Sans', sans-serif; }
    .ci-page { min-height: 100vh; color: #1A2A4A; }

    /* ── HEADER ── */
    .page-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:22px; padding-bottom:18px; border-bottom:1px solid rgba(27,79,168,0.1); flex-wrap:wrap; gap:16px; }
    .page-eyebrow  { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:#F5911E; margin-bottom:4px; }
    .page-title    { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:4px; color:#1B4FA8; line-height:1; }
    .page-subtitle { font-size:12px; color:#7A8A9A; margin-top:4px; }
    .btn-primary   { padding:10px 16px; background:#1B4FA8; color:#fff; border:none; border-radius:6px; text-decoration:none; font-size:13px; letter-spacing:1px; transition:background .2s; }
    .btn-primary:hover { background:#153e85; }

    /* ── LIVE ALERTS ── */
    #liveAlerts { display:flex; flex-direction:column; gap:10px; margin-bottom:18px; }
    #liveAlerts:empty { display:none; }
    .live-alert { display:flex; align-items:center; gap:14px; padding:13px 18px; border-radius:8px; border:1px solid; animation:alertIn .4s cubic-bezier(.16,1,.3,1) both; box-shadow:0 4px 18px rgba(27,79,168,0.06); }
    .live-alert.is-soon { background:linear-gradient(90deg,rgba(245,145,30,0.10),rgba(245,145,30,0.02)); border-color:rgba(245,145,30,0.35); }
    .live-alert.is-live { background:linear-gradient(90deg,rgba(21,128,61,0.10),rgba(21,128,61,0.02)); border-color:rgba(21,128,61,0.35); }
    .la-dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
    .live-alert.is-soon .la-dot { background:#F5911E; box-shadow:0 0 0 4px rgba(245,145,30,0.18); }
    .live-alert.is-live .la-dot { background:#15803D; animation:pulse 1.4s infinite; }
    .la-body { flex:1; min-width:0; }
    .la-title { font-size:13px; font-weight:600; color:#1A2A4A; }
    .la-title .la-when-soon { color:#C47010; }
    .la-title .la-when-live { color:#15803D; }
    .la-meta { font-size:11px; color:#7A8A9A; margin-top:2px; display:flex; gap:12px; flex-wrap:wrap; }
    .la-meta b { color:#4A5A7A; font-weight:600; }
    .la-meta svg { display:inline-block; vertical-align:-2px; }
    .la-go { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; padding:6px 12px; border-radius:4px; text-decoration:none; border:1px solid rgba(27,79,168,0.25); color:#1B4FA8; white-space:nowrap; }
    .la-go:hover { background:rgba(27,79,168,0.07); }

    @keyframes alertIn { from{opacity:0;transform:translateY(-6px);} to{opacity:1;transform:translateY(0);} }
    @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(21,128,61,0.5);} 70%{box-shadow:0 0 0 7px rgba(21,128,61,0);} 100%{box-shadow:0 0 0 0 rgba(21,128,61,0);} }

    /* ── TABS ── */
    .ci-tabs { display:flex; gap:6px; margin-bottom:16px; border-bottom:1px solid rgba(27,79,168,0.1); flex-wrap:wrap; }
    .ci-tab { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; background:transparent; border:none; border-bottom:2px solid transparent; margin-bottom:-1px; font-family:'DM Sans',sans-serif; font-size:12px; letter-spacing:1.5px; text-transform:uppercase; color:#7A8A9A; cursor:pointer; transition:all .2s; }
    .ci-tab:hover { color:#1B4FA8; }
    .ci-tab.active { color:#1B4FA8; border-bottom-color:#1B4FA8; font-weight:600; }
    .ci-tab .tab-count { font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:1px; background:rgba(27,79,168,0.08); color:#1B4FA8; border-radius:10px; padding:1px 9px; }
    .ci-tab[data-tab="active"] .tab-count { background:rgba(21,128,61,0.1); color:#15803D; }

    /* ── TOOLBAR ── */
   .toolbar {display: flex; align-items: center; gap: 10px;margin-bottom: 16px; flex-wrap: wrap;}
    .toolbar .app-select,
    .toolbar > .filter-select {flex: 1 1 150px;min-width: 140px;max-width: 260px;}
    .search-wrap { position:relative; flex:1; min-width:220px; max-width:360px; }
    .search-wrap svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input { width:100%; padding:10px 14px 10px 40px; background:rgba(255,255,255,0.8); border:1px solid rgba(27,79,168,0.12); border-radius:6px; font-size:13px; color:#1A2A4A; outline:none; transition:border-color .3s,box-shadow .3s; }
    .search-input:focus { border-color:#1B4FA8; box-shadow:0 0 0 3px rgba(27,79,168,0.08); }
    .filter-select { padding:9px 32px 9px 12px; background:rgba(255,255,255,0.8); border:1px solid rgba(27,79,168,0.12); border-radius:6px; font-size:12px; color:#4A5A7A; outline:none; cursor:pointer; appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; transition:border-color .3s; }
    .filter-select:focus { border-color:#1B4FA8; }

    /* ── ACTIVE LAYOUT (cards + now panel) ── */
    .active-layout { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }
    @media (max-width:1000px){ .active-layout { grid-template-columns:1fr; } }

    .ci-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:14px; }

    /* ── CARD ── */
    .ci-card { position:relative; background:rgba(255,255,255,0.85); border:1px solid rgba(27,79,168,0.1); border-radius:10px; padding:16px 16px 14px; box-shadow:0 2px 12px rgba(27,79,168,0.05); transition:transform .2s, box-shadow .2s, border-color .3s; overflow:hidden; }
    .ci-card:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(27,79,168,0.1); }
    .ci-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,rgba(27,79,168,0.4),transparent); }
    .ci-card.ci-soon { border-color:rgba(245,145,30,0.55); box-shadow:0 0 0 2px rgba(245,145,30,0.25),0 8px 22px rgba(245,145,30,0.1); }
    .ci-card.ci-soon::before { background:#F5911E; height:3px; }
    .ci-card.ci-live { border-color:rgba(21,128,61,0.55); box-shadow:0 0 0 2px rgba(21,128,61,0.3),0 8px 22px rgba(21,128,61,0.12); }
    .ci-card.ci-live::before { background:#15803D; height:3px; }

    .ci-head-badges { display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0; }
    .ci-live-badge { display:inline-flex; align-items:center; gap:5px; font-size:8.5px; letter-spacing:1px; text-transform:uppercase; font-weight:600; padding:3px 8px; border-radius:20px; white-space:nowrap; }
    .ci-live-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
    .ci-live-badge.is-soon { color:#C47010; background:rgba(245,145,30,0.12); border:1px solid rgba(245,145,30,0.3); }
    .ci-live-badge.is-live { color:#15803D; background:rgba(21,128,61,0.12); border:1px solid rgba(21,128,61,0.3); }
    .ci-live-badge.is-live::before { animation:pulse 1.4s infinite; }

    .ci-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:10px;}
    .ci-card-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:1px; color:#1A2A4A; line-height:1.05; }
    .ci-card-sub { font-size:11px; color:#7A8A9A; margin-top:3px; }
    .ci-card-tags { display:flex; gap:5px; flex-wrap:wrap; margin-bottom:12px; }

    .ci-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:9px 14px; padding:12px 0; border-top:1px solid rgba(27,79,168,0.06); border-bottom:1px solid rgba(27,79,168,0.06); margin-bottom:12px; }
    .ci-meta { display:flex; flex-direction:column; gap:2px; min-width:0; }
    .ci-meta-today { grid-column:1 / -1; background:rgba(27,79,168,0.03); border-radius:5px; padding:6px 8px; margin-top:2px; }
    .ci-meta-k { font-size:8.5px; letter-spacing:1.5px; text-transform:uppercase; color:#AAB8C8; }
    .ci-meta-v { font-size:12px; color:#1A2A4A; font-weight:500; }

    .ci-cap { margin-bottom:12px; }

    .ci-card-actions { display:flex; gap:8px; }
    .btn-action { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; border-radius:4px; font-weight:500; border:1px solid; background:transparent; cursor:pointer; transition:all .2s; text-decoration:none; }
    .ci-btn-view { color:#1B4FA8; border-color:rgba(27,79,168,0.25); }
    .ci-btn-view:hover { background:rgba(27,79,168,0.07); border-color:#1B4FA8; }
    .ci-btn-edit { color:#C47010; border-color:rgba(245,145,30,0.3); }
    .ci-btn-edit:hover { background:rgba(245,145,30,0.08); border-color:#F5911E; }

    /* Tags / status / capacity (shared) */
    .tag { display:inline-block; font-size:9px; letter-spacing:1px; padding:2px 8px; border-radius:3px; white-space:nowrap; text-transform:uppercase; font-weight:500; }
    .tag-course  { background:rgba(27,79,168,0.07);  border:1px solid rgba(27,79,168,0.15); color:#1B4FA8; }
    .tag-group   { background:rgba(27,79,168,0.05);  border:1px solid rgba(27,79,168,0.12); color:#2D6FDB; }
    .tag-private { background:rgba(245,145,30,0.05); border:1px solid rgba(245,145,30,0.15); color:#C47010; }
    .tag-online  { background:rgba(21,128,61,0.05);  border:1px solid rgba(21,128,61,0.15); color:#15803D; }
    .tag-offline { background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15);color:#7A8A9A; }
    .status-badge { display:inline-flex; align-items:center; gap:5px; font-size:9px; letter-spacing:1.2px; text-transform:uppercase; padding:4px 9px; border-radius:3px; white-space:nowrap; font-weight:500; }
    .status-badge::before { content:''; width:4px; height:4px; border-radius:50%; background:currentColor; flex-shrink:0; }
    .status-upcoming  { color:#1B6FA8; background:rgba(27,111,168,0.08); border:1px solid rgba(27,111,168,0.2); }
    .status-active-ci { color:#15803D; background:rgba(21,128,61,0.08);  border:1px solid rgba(21,128,61,0.2); }
    .status-completed { color:#7A8A9A; background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.2); }
    .status-cancelled { color:#DC2626; background:rgba(220,38,38,0.06);  border:1px solid rgba(220,38,38,0.2); }
    .cap-wrap { display:flex; align-items:center; gap:8px; }
    .cap-text { font-size:12px; color:#1A2A4A; font-weight:500; white-space:nowrap; }
    .cap-track { flex:1; min-width:50px; height:5px; background:rgba(27,79,168,0.08); border-radius:3px; overflow:hidden; }
    .cap-fill { height:100%; border-radius:3px; transition:width .6s cubic-bezier(.16,1,.3,1); }
    .cap-full { color:#DC2626; font-size:9px; letter-spacing:1px; text-transform:uppercase; }

    /* ── NOW PANEL ── */
    .now-panel { position:sticky; top:80px; background:linear-gradient(160deg,#1A2A4A,#1B4FA8); border-radius:12px; padding:18px; color:#fff; box-shadow:0 8px 28px rgba(27,79,168,0.18); }
    .now-panel-head { display:flex; align-items:center; gap:8px; font-size:10px; letter-spacing:2.5px; text-transform:uppercase; color:rgba(255,255,255,0.6); margin-bottom:14px; }
    .now-panel-head .np-live-dot { width:8px; height:8px; border-radius:50%; background:#4ADE80; animation:pulse 1.4s infinite; }
    .now-item { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); border-radius:8px; padding:12px; margin-bottom:10px; }
    .now-item:last-child { margin-bottom:0; }
    .now-item-when { font-size:9px; letter-spacing:1px; text-transform:uppercase; font-weight:600; margin-bottom:6px; display:inline-block; padding:2px 8px; border-radius:10px; }
    .now-item-when.is-soon { background:rgba(245,145,30,0.25); color:#FFD9A8; }
    .now-item-when.is-live { background:rgba(74,222,128,0.2); color:#BBF7D0; }
    .now-item-course { font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:1px; margin-bottom:6px; }
    .now-item-meta { font-size:11px; color:rgba(255,255,255,0.75); line-height:1.7; }
    .now-item-meta svg { display:inline-block; vertical-align:-2px; margin-right:5px; opacity:0.7; }
    .now-empty { text-align:center; padding:16px 6px; color:rgba(255,255,255,0.55); font-size:12px; line-height:1.6; }
    .now-empty svg { opacity:0.4; margin-bottom:8px; }

    /* ── EMPTY STATE ── */
    .empty-state { padding:56px 24px; text-align:center; grid-column:1/-1; }
    .empty-state svg { margin:0 auto 14px; opacity:0.2; }
    .empty-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:4px; color:#7A8A9A; margin-bottom:6px; }
    .empty-sub { font-size:12px; color:#AAB8C8; }

    .tab-panel { display:none; }
    .tab-panel.active { display:block; animation:panelIn .3s ease both; }
    @keyframes panelIn { from{opacity:0;transform:translateY(6px);} to{opacity:1;transform:translateY(0);} }
    .ci-card.filtered-out { display:none !important; }

    @media (max-width:768px){ .ci-page { padding:20px 14px; } }
    @media (max-width:480px){ .page-header { flex-direction:column; align-items:flex-start; } }
</style>

<div class="ci-page">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care</div>
            <h1 class="page-title">Course Instances</h1>
            <p class="page-subtitle">Active, upcoming and completed course groups</p>
        </div>
        <a href="{{ route('student-care.instances.create') }}" class="btn-primary">+ New Course</a>
    </div>

    {{-- LIVE ALERTS (filled by JS from active courses) --}}
    <div id="liveAlerts"></div>

    {{-- TABS --}}
    <div class="ci-tabs">
        <button class="ci-tab active" data-tab="active" onclick="switchTab('active')">
            Active <span class="tab-count">{{ $active->count() }}</span>
        </button>
        <button class="ci-tab" data-tab="next" onclick="switchTab('next')">
            Next Patch <span class="tab-count">{{ $nextPatch->count() }}</span>
        </button>
        <button class="ci-tab" data-tab="completed" onclick="switchTab('completed')">
            Completed <span class="tab-count">{{ $completed->count() }}</span>
        </button>
    </div>

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="ciSearch" class="search-input" placeholder="Search by course or teacher..." oninput="applyFilters()">
        </div>
        <select class="filter-select" id="ciTypeFilter" onchange="applyFilters()">
            <option value="">All Types</option>
            <option value="Group">Group</option>
            <option value="Private">Private</option>
        </select>
        <select class="filter-select" id="ciModeFilter" onchange="applyFilters()">
            <option value="">All Modes</option>
            <option value="Online">Online</option>
            <option value="Offline">Offline</option>
        </select>
    </div>

    {{-- ── ACTIVE TAB ── --}}
    <div class="tab-panel active" id="panel-active">
        <div class="active-layout">
            <div>
                <div class="ci-cards">
                    @forelse($active as $instance)
                        @include('student-care.course-instances.partials.instance-card', ['instance'=>$instance,'context'=>'active'])
                    @empty
                        <div class="empty-state">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <div class="empty-title">No Active Courses</div>
                            <div class="empty-sub">No courses are currently running</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- NOW / UP NEXT panel --}}
            <div class="now-panel">
                <div class="now-panel-head"><span class="np-live-dot"></span> Now &amp; Up Next</div>
                <div id="nowPanel">
                    <div class="now-empty">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <div>No sessions in the next 30 minutes.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── NEXT PATCH TAB ── --}}
    <div class="tab-panel" id="panel-next">
        <div class="ci-cards">
            @forelse($nextPatch as $instance)
                @include('student-care.course-instances.partials.instance-card', ['instance'=>$instance,'context'=>'next'])
            @empty
                <div class="empty-state">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <div class="empty-title">No Upcoming Courses</div>
                    <div class="empty-sub">No courses scheduled for the next patch yet</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── COMPLETED TAB ── --}}
    <div class="tab-panel" id="panel-completed">
        <div class="ci-cards">
            @forelse($completed as $instance)
                @include('student-care.course-instances.partials.instance-card', ['instance'=>$instance,'context'=>'completed'])
            @empty
                <div class="empty-state">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div class="empty-title">No Completed Courses</div>
                    <div class="empty-sub">Finished courses will appear here</div>
                </div>
            @endforelse
        </div>
    </div>

    @if(request()->query('create') == '1')
    <script>document.addEventListener('DOMContentLoaded', function(){ if (typeof openCreateInstanceModal === 'function') openCreateInstanceModal(); });</script>
    @endif
</div>

<script>
// ── TABS ──────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.ci-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
    try { localStorage.setItem('ci_active_tab', tab); } catch(e){}
    applyFilters();
}

// ── FILTERS (search / type / mode) over cards ─────────────────────────
function applyFilters() {
    const q    = (document.getElementById('ciSearch').value || '').toLowerCase().trim();
    const type = document.getElementById('ciTypeFilter').value;
    const mode = document.getElementById('ciModeFilter').value;
    document.querySelectorAll('.ci-card').forEach(card => {
        const okType = !type || card.dataset.type === type;
        const okMode = !mode || card.dataset.mode === mode;
        const okText = !q || (card.dataset.course || '').includes(q) || (card.dataset.teacher || '').includes(q);
        card.classList.toggle('filtered-out', !(okType && okMode && okText));
    });
}

// ── LIVE LAYER (starting-soon / in-progress) ──────────────────────────
function refreshLive() {
    const now  = new Date();
    const soonEdge = new Date(now.getTime() + 30 * 60000);
    const items = [];

    document.querySelectorAll('.ci-card[data-context="active"]').forEach(card => {
        let sessions = [];
        try { sessions = JSON.parse(card.dataset.sessions || '[]'); } catch(e){}
        let state = null, mins = 0;
        for (const sess of sessions) {
            const s = new Date(sess.s), e = new Date(sess.e);
            if (now >= s && now <= e) { state = 'live'; mins = Math.max(0, Math.round((e - now) / 60000)); break; }
            if (now < s && s <= soonEdge) { state = 'soon'; mins = Math.max(0, Math.round((s - now) / 60000)); }
        }
        card.classList.toggle('ci-live', state === 'live');
        card.classList.toggle('ci-soon', state === 'soon');

        const badge = card.querySelector('.ci-live-badge');
        if (badge) {
            if (state) {
                badge.style.display = 'inline-flex';
                badge.className = 'ci-live-badge ' + (state === 'live' ? 'is-live' : 'is-soon');
                badge.textContent = state === 'live' ? ('Ends in ' + mins + 'm') : ('Starts in ' + mins + 'm');
            } else {
                badge.style.display = 'none';
            }
        }
        if (state) {
            items.push({
                state, mins,
                course:  card.dataset.courseName,
                teacher: card.dataset.teacherName,
                room:    card.dataset.roomName,
                time:    card.dataset.timeLabel,
                id:      card.dataset.instance,
            });
        }
    });

    // Order: live first, then soonest.
    items.sort((a, b) => (a.state === b.state) ? a.mins - b.mins : (a.state === 'live' ? -1 : 1));
    renderNowPanel(items);
    renderAlerts(items);
}

function roomSvg(){ return '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>'; }
function teacherSvg(){ return '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'; }

function esc(s){ return (s == null ? '' : String(s)).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

function renderNowPanel(items) {
    const box = document.getElementById('nowPanel');
    if (!box) return;
    if (!items.length) {
        box.innerHTML = '<div class="now-empty"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><div>No sessions in the next 30 minutes.</div></div>';
        return;
    }
    box.innerHTML = items.map(it => {
        const when = it.state === 'live' ? ('In progress · ends in ' + it.mins + ' min') : ('Starts in ' + it.mins + ' min');
        return '<div class="now-item">'
            + '<span class="now-item-when ' + (it.state === 'live' ? 'is-live' : 'is-soon') + '">' + when + '</span>'
            + '<div class="now-item-course">' + esc(it.course) + '</div>'
            + '<div class="now-item-meta">'
            +   teacherSvg() + esc(it.teacher) + '<br>'
            +   roomSvg() + 'Room ' + esc(it.room) + (it.time ? ' · ' + esc(it.time) : '')
            + '</div>'
            + '</div>';
    }).join('');
}

function renderAlerts(items) {
    const box = document.getElementById('liveAlerts');
    if (!box) return;
    box.innerHTML = items.map(it => {
        const isLive = it.state === 'live';
        const whenHtml = isLive
            ? '<span class="la-when-live">in progress — ends in ' + it.mins + ' min</span>'
            : '<span class="la-when-soon">starts in ' + it.mins + ' min</span>';
        return '<div class="live-alert ' + (isLive ? 'is-live' : 'is-soon') + '">'
            + '<span class="la-dot"></span>'
            + '<div class="la-body">'
            +   '<div class="la-title">' + esc(it.course) + ' — ' + whenHtml + '</div>'
            +   '<div class="la-meta"><span>' + teacherSvg() + ' <b>' + esc(it.teacher) + '</b></span>'
            +       '<span>' + roomSvg() + ' Room <b>' + esc(it.room) + '</b></span>'
            +       (it.time ? '<span>🕒 ' + esc(it.time) + '</span>' : '')
            +   '</div>'
            + '</div>'
            + '<a class="la-go" href="/student-care/course-instances/' + encodeURIComponent(it.id) + '">Open</a>'
            + '</div>';
    }).join('');
}

// ── INIT ──────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    let saved = 'active';
    try { saved = localStorage.getItem('ci_active_tab') || 'active'; } catch(e){}
    if (document.getElementById('panel-' + saved)) switchTab(saved); else applyFilters();
    refreshLive();
    setInterval(refreshLive, 30000); // keep highlight + alerts fresh
});
</script>

@endsection
