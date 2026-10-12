@extends('admin.layouts.app')

@section('title', 'Course Instances')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    :root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
    body, .ci-page * { font-family: 'DM Sans', sans-serif; }
    .ci-page { min-height: 100vh; color: var(--text); padding: 40px 32px; background: var(--bg); }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .page-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:22px; padding-bottom:18px; border-bottom:1px solid rgba(27,79,168,0.1); flex-wrap:wrap; gap:16px; }
    .page-eyebrow  { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:var(--blue); margin-bottom:4px; font-weight:600; }
    .page-title    { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:4px; color:var(--text); line-height:1; }
    .page-subtitle { font-size:12px; color:var(--muted); margin-top:4px; }
    .btn-back { display:inline-flex; align-items:center; gap:8px; padding:9px 18px; background:rgba(255,255,255,0.6); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--glass-bd); border-radius:11px; color:var(--muted); font-size:10px; letter-spacing:2.5px; text-transform:uppercase; text-decoration:none; transition:all 0.3s; }
    .btn-back:hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }

    #liveAlerts { display:flex; flex-direction:column; gap:10px; margin-bottom:18px; }
    #liveAlerts:empty { display:none; }
    .live-alert { display:flex; align-items:center; gap:14px; padding:13px 18px; border-radius:16px; border:1px solid; animation:alertIn .4s cubic-bezier(.16,1,.3,1) both; box-shadow:var(--glass-sh); -webkit-backdrop-filter:blur(16px); backdrop-filter:blur(16px); }
    .live-alert.is-soon { background:linear-gradient(90deg,rgba(245,145,30,0.14),rgba(255,255,255,0.5)); border-color:rgba(245,145,30,0.35); }
    .live-alert.is-live { background:linear-gradient(90deg,rgba(21,128,61,0.14),rgba(255,255,255,0.5)); border-color:rgba(21,128,61,0.35); }
    .la-dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
    .live-alert.is-soon .la-dot { background:var(--orange); box-shadow:0 0 0 4px rgba(245,145,30,0.18); }
    .live-alert.is-live .la-dot { background:var(--green-dk); animation:pulse 1.4s infinite; }
    .la-body { flex:1; min-width:0; }
    .la-title { font-size:13px; font-weight:600; color:var(--text); }
    .la-title .la-when-soon { color:var(--orange-dk); }
    .la-title .la-when-live { color:var(--green-dk); }
    .la-meta { font-size:11px; color:var(--muted); margin-top:2px; display:flex; gap:12px; flex-wrap:wrap; }
    .la-meta b { color:#4A5A7A; font-weight:600; }
    .la-meta svg { display:inline-block; vertical-align:-2px; }
    .la-go { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; padding:6px 12px; border-radius:10px; text-decoration:none; border:1px solid rgba(27,79,168,0.25); color:var(--blue); white-space:nowrap; background:rgba(255,255,255,0.55); font-weight:600; }
    .la-go:hover { background:rgba(27,79,168,0.08); }

    @keyframes alertIn { from{opacity:0;transform:translateY(-6px);} to{opacity:1;transform:translateY(0);} }
    @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(21,128,61,0.5);} 70%{box-shadow:0 0 0 7px rgba(21,128,61,0);} 100%{box-shadow:0 0 0 0 rgba(21,128,61,0);} }

    .ci-tabs { display:flex; gap:6px; margin-bottom:16px; border-bottom:1px solid rgba(27,79,168,0.1); flex-wrap:wrap; }
    .ci-tab { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; background:transparent; border:none; border-bottom:2px solid transparent; margin-bottom:-1px; font-family:'DM Sans',sans-serif; font-size:12px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); cursor:pointer; transition:all .2s; }
    .ci-tab:hover { color:var(--blue); }
    .ci-tab.active { color:var(--blue); border-bottom-color:var(--blue); font-weight:600; }
    .ci-tab .tab-count { font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:1px; background:rgba(27,79,168,0.10); color:var(--blue); border-radius:10px; padding:1px 9px; }
    .ci-tab[data-tab="active"] .tab-count { background:rgba(21,128,61,0.12); color:var(--green-dk); }

    .toolbar { display:flex; align-items:center; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
    .search-wrap { position:relative; flex:1; min-width:220px; max-width:360px; }
    .search-wrap svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input { width:100%; padding:10px 14px 10px 40px; background:rgba(255,255,255,0.6); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--glass-bd); border-radius:12px; font-size:13px; color:var(--text); outline:none; transition:border-color .3s,box-shadow .3s; }
    .search-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }
    .filter-select { padding:9px 32px 9px 12px; background:rgba(255,255,255,0.6); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid var(--glass-bd); border-radius:12px; font-size:12px; color:#4A5A7A; outline:none; cursor:pointer; appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%235A6A85'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; transition:border-color .3s; }
    .filter-select:focus { border-color:var(--blue); }

    .active-layout { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }
    @media (max-width:1000px){ .active-layout { grid-template-columns:1fr; } }

    .ci-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:14px; }

    .ci-card { position:relative; background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:18px; padding:16px 16px 14px; box-shadow:var(--glass-sh); transition:transform .2s, box-shadow .2s, border-color .3s; overflow:hidden; }
    .ci-card:hover { transform:translateY(-3px); box-shadow:0 16px 38px rgba(23,45,90,0.13); }
    /* .ci-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,transparent,var(--blue),transparent); } */
    .ci-card.ci-soon { border-color:rgba(245,145,30,0.55); box-shadow:0 0 0 2px rgba(245,145,30,0.25),0 8px 22px rgba(245,145,30,0.1); }
    .ci-card.ci-soon::before { background:var(--orange); height:3px; }
    .ci-card.ci-live { border-color:rgba(21,128,61,0.55); box-shadow:0 0 0 2px rgba(21,128,61,0.3),0 8px 22px rgba(21,128,61,0.12); }
    .ci-card.ci-live::before { background:var(--green-dk); height:3px; }

    .ci-head-badges { display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0; }
    .ci-live-badge { display:inline-flex; align-items:center; gap:5px; font-size:8.5px; letter-spacing:1px; text-transform:uppercase; font-weight:600; padding:3px 8px; border-radius:20px; white-space:nowrap; }
    .ci-live-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
    .ci-live-badge.is-soon { color:var(--orange-dk); background:rgba(245,145,30,0.12); border:1px solid rgba(245,145,30,0.3); }
    .ci-live-badge.is-live { color:var(--green-dk); background:rgba(21,128,61,0.12); border:1px solid rgba(21,128,61,0.3); }
    .ci-live-badge.is-live::before { animation:pulse 1.4s infinite; }

    .ci-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:10px; }
    .ci-card-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:1px; color:var(--text); line-height:1.05; }
    .ci-card-sub { font-size:11px; color:var(--muted); margin-top:3px; }
    .ci-card-tags { display:flex; gap:5px; flex-wrap:wrap; margin-bottom:12px; }

    .ci-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:9px 14px; padding:12px 0; border-top:1px solid rgba(27,79,168,0.06); border-bottom:1px solid rgba(27,79,168,0.06); margin-bottom:12px; }
    .ci-meta { display:flex; flex-direction:column; gap:2px; min-width:0; }
    .ci-meta-today { grid-column:1 / -1; background:rgba(27,79,168,0.06); border-radius:10px; padding:6px 8px; margin-top:2px; }
    .ci-meta-k { font-size:8.5px; letter-spacing:1.5px; text-transform:uppercase; color:var(--faint); }
    .ci-meta-v { font-size:12px; color:var(--text); font-weight:500; }

    .ci-cap { margin-bottom:12px; }

    .ci-card-actions { display:flex; gap:8px; }
    .btn-action { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; border-radius:8px; font-weight:600; border:1px solid; background:rgba(255,255,255,0.5); cursor:pointer; transition:all .2s; text-decoration:none; }
    .ci-btn-view { color:var(--blue); border-color:rgba(27,79,168,0.22); }
    .ci-btn-view:hover { background:rgba(27,79,168,0.08); border-color:var(--blue); }
    .ci-btn-edit { color:var(--orange-dk); border-color:rgba(245,145,30,0.3); }
    .ci-btn-edit:hover { background:rgba(245,145,30,0.08); border-color:var(--orange); }

    .tag { display:inline-block; font-size:9px; letter-spacing:1px; padding:2px 8px; border-radius:20px; white-space:nowrap; text-transform:uppercase; font-weight:600; }
    .tag-course  { background:rgba(27,79,168,0.10);  border:1px solid rgba(27,79,168,0.18); color:var(--blue); }
    .tag-group   { background:rgba(27,79,168,0.06);  border:1px solid rgba(27,79,168,0.14); color:var(--blue-2); }
    .tag-private { background:rgba(245,145,30,0.08); border:1px solid rgba(245,145,30,0.2); color:var(--orange-dk); }
    .tag-online  { background:rgba(21,128,61,0.08);  border:1px solid rgba(21,128,61,0.18); color:var(--green-dk); }
    .tag-offline { background:rgba(122,138,154,0.10);border:1px solid rgba(122,138,154,0.18);color:var(--muted); }
    .status-badge { display:inline-flex; align-items:center; gap:5px; font-size:9px; letter-spacing:1.2px; text-transform:uppercase; padding:4px 9px; border-radius:20px; white-space:nowrap; font-weight:600; }
    .status-badge::before { content:''; width:4px; height:4px; border-radius:50%; background:currentColor; flex-shrink:0; }
    .status-upcoming  { color:var(--blue); background:rgba(27,79,168,0.10); border:1px solid rgba(27,79,168,0.2); }
    .status-active-ci { color:var(--green-dk); background:rgba(21,128,61,0.10);  border:1px solid rgba(21,128,61,0.2); }
    .status-completed { color:var(--muted); background:rgba(122,138,154,0.10);border:1px solid rgba(122,138,154,0.2); }
    .status-cancelled { color:var(--red); background:rgba(220,38,38,0.08);  border:1px solid rgba(220,38,38,0.2); }
    .cap-wrap { display:flex; align-items:center; gap:8px; }
    .cap-text { font-size:12px; color:var(--text); font-weight:500; white-space:nowrap; }
    .cap-track { flex:1; min-width:50px; height:5px; background:rgba(27,79,168,0.10); border-radius:3px; overflow:hidden; }
    .cap-fill { height:100%; border-radius:3px; transition:width .6s cubic-bezier(.16,1,.3,1); }
    .cap-full { color:var(--red); font-size:9px; letter-spacing:1px; text-transform:uppercase; }

    .now-panel { position:sticky; top:80px; background:linear-gradient(150deg,var(--blue),var(--blue-2)); border-radius:20px; padding:18px; color:#fff; box-shadow:0 14px 34px rgba(27,79,168,0.28); }
    .now-panel-head { display:flex; align-items:center; gap:8px; font-size:10px; letter-spacing:2.5px; text-transform:uppercase; color:rgba(255,255,255,0.75); margin-bottom:14px; font-weight:600; }
    .now-panel-head .np-live-dot { width:8px; height:8px; border-radius:50%; background:#4ADE80; animation:pulse 1.4s infinite; }
    .now-item { background:rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.2); border-radius:14px; padding:12px; margin-bottom:10px; }
    .now-item:last-child { margin-bottom:0; }
    .now-item-when { font-size:9px; letter-spacing:1px; text-transform:uppercase; font-weight:600; margin-bottom:6px; display:inline-block; padding:2px 8px; border-radius:10px; }
    .now-item-when.is-soon { background:rgba(245,145,30,0.3); color:#FFE3BF; }
    .now-item-when.is-live { background:rgba(74,222,128,0.25); color:#D1FADF; }
    .now-item-course { font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:1px; margin-bottom:6px; }
    .now-item-meta { font-size:11px; color:rgba(255,255,255,0.82); line-height:1.7; }
    .now-item-meta svg { display:inline-block; vertical-align:-2px; margin-right:5px; opacity:0.75; }
    .now-empty { text-align:center; padding:16px 6px; color:rgba(255,255,255,0.7); font-size:12px; line-height:1.6; }
    .now-empty svg { opacity:0.5; margin-bottom:8px; }

    .empty-state { padding:56px 24px; text-align:center; grid-column:1/-1; }
    .empty-state svg { margin:0 auto 14px; opacity:0.2; }
    .empty-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:4px; color:var(--muted); margin-bottom:6px; }
    .empty-sub { font-size:12px; color:var(--faint); }

    .tab-panel { display:none; }
    .tab-panel.active { display:block; animation:panelIn .3s ease both; }
    @keyframes panelIn { from{opacity:0;transform:translateY(6px);} to{opacity:1;transform:translateY(0);} }
    .ci-card.filtered-out { display:none !important; }

    @media (max-width:768px){ .ci-page { padding:20px 14px; } }
    @media (max-width:480px){ .page-header { flex-direction:column; align-items:flex-start; } }
</style>

<div class="ci-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Course Instances</h1>
            <p class="page-subtitle">Active, upcoming and completed course groups</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Courses
        </a>
    </div>

    <div id="liveAlerts"></div>

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

    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
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

    <div class="tab-panel active" id="panel-active">
        <div class="active-layout">
            <div>
                <div class="ci-cards">
                    @forelse($active as $instance)
                        @include('student-care.course-instances.partials.instance-card', ['instance'=>$instance,'context'=>'active','showRoute'=>'admin.course-instances.show','editRoute'=>''])
                    @empty
                        <div class="empty-state">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <div class="empty-title">No Active Courses</div>
                            <div class="empty-sub">No courses are currently running</div>
                        </div>
                    @endforelse
                </div>
            </div>

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

    <div class="tab-panel" id="panel-next">
        <div class="ci-cards">
            @forelse($nextPatch as $instance)
                @include('student-care.course-instances.partials.instance-card', ['instance'=>$instance,'context'=>'next','showRoute'=>'admin.course-instances.show','editRoute'=>''])
            @empty
                <div class="empty-state">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <div class="empty-title">No Upcoming Courses</div>
                    <div class="empty-sub">No courses scheduled for the next patch yet</div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="tab-panel" id="panel-completed">
        <div class="ci-cards">
            @forelse($completed as $instance)
                @include('student-care.course-instances.partials.instance-card', ['instance'=>$instance,'context'=>'completed','showRoute'=>'admin.course-instances.show','editRoute'=>''])
            @empty
                <div class="empty-state">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div class="empty-title">No Completed Courses</div>
                    <div class="empty-sub">Finished courses will appear here</div>
                </div>
            @endforelse
        </div>
    </div>

</div>

<script>
const CI_SHOW_BASE = @json(url('/admin/course-instances'));

function switchTab(tab) {
    document.querySelectorAll('.ci-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
    try { localStorage.setItem('ci_admin_active_tab', tab); } catch(e){}
    applyFilters();
}

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
            + '<a class="la-go" href="' + CI_SHOW_BASE + '/' + encodeURIComponent(it.id) + '">Open</a>'
            + '</div>';
    }).join('');
}

document.addEventListener('DOMContentLoaded', function () {
    let saved = 'active';
    try { saved = localStorage.getItem('ci_admin_active_tab') || 'active'; } catch(e){}
    if (document.getElementById('panel-' + saved)) switchTab(saved); else applyFilters();
    refreshLive();
    setInterval(refreshLive, 30000);
});
</script>

@endsection
