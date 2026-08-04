@extends('layouts.leads')

@section('title', 'My Leads')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --teal:#0EA5A5; --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }

    .leads-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }

    /* ═══ HEADER ═══ */
    .page-header {
        margin:0 auto 22px;
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px;
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:16px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .page-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .page-header::after { content:''; position:absolute; bottom:-60px; left:24%; width:150px; height:150px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .page-header > div:first-child { position:relative; z-index:1; }
    .page-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .page-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--orange); box-shadow:0 0 8px var(--orange); }
    .page-title { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:4px; color:#fff; line-height:1; margin:0; }
    .page-subtitle { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }
    .btn-add { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; background:var(--orange); border:none; border-radius:8px; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:3px; text-decoration:none; transition:all 0.25s; position:relative; z-index:1; box-shadow:0 4px 16px rgba(245,145,30,0.3); }
    .btn-add:hover { background:#E8850F; transform:translateY(-2px); text-decoration:none; color:#fff; }

    .leads-wrap { margin:0 auto; }

    /* ═══ STATS ═══ */
    .stats-row { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:20px; }
    @media (max-width:900px){ .stats-row{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:520px){ .stats-row{ grid-template-columns:1fr 1fr; } }
    .stat-card {
        background:var(--card); border:1px solid var(--border); border-radius:12px;
        padding:16px 18px; position:relative; overflow:hidden;
        transition:transform 0.2s, box-shadow 0.2s;
        box-shadow:0 2px 10px rgba(27,79,168,0.04);
    }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent,var(--blue)); }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(27,79,168,0.1); }
    .stat-card.active-filter { box-shadow:0 0 0 2px var(--accent), 0 8px 24px rgba(27,79,168,0.12); }
    .stat-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:8px; }
    .stat-value { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:1px; line-height:0.9; color:var(--accent,var(--blue)); }

    /* ═══ SEARCH ═══ */
    .search-wrap { margin-bottom:18px; position:relative; max-width:400px; }
    .search-wrap svg { position:absolute; left:15px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input {
        width:100%; padding:12px 16px 12px 42px;
        background:var(--card); border:1px solid var(--border); border-radius:9px;
        font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text);
        outline:none; transition:border-color 0.25s, box-shadow 0.25s;
        box-shadow:0 2px 8px rgba(27,79,168,0.03);
    }
    .search-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }

    /* ═══ FILTER PILLS ═══ */
    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; }
    .filter-pill {
        padding:9px 16px; border-radius:8px; font-size:11px; font-weight:600;
        letter-spacing:0.4px; cursor:pointer; transition:all 0.2s;
        background:var(--card); border:1px solid var(--border); color:var(--muted);
        white-space:nowrap;
    }
    .filter-pill:hover { border-color:var(--blue); color:var(--blue); }
    .filter-pill.active { background:var(--blue); border-color:var(--blue); color:#fff; box-shadow:0 4px 12px rgba(27,79,168,0.2); }
    .filter-pill-green.active { background:var(--green); border-color:var(--green); box-shadow:0 4px 12px rgba(5,150,105,0.2); }

    /* ═══ SECTION HEADING (registered table) ═══ */
    .reg-section-head { display:flex; align-items:center; gap:10px; margin:0 0 14px; }
    .reg-section-head .rsh-dot { width:8px; height:8px; border-radius:50%; background:var(--green); box-shadow:0 0 8px var(--green); }
    .reg-section-head .rsh-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; color:var(--green-dk); }
    .reg-section-head .rsh-count { font-size:11px; color:var(--muted); background:var(--green-l); padding:3px 10px; border-radius:20px; font-weight:600; }

    /* ═══ TABLE ═══ */
    .table-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 14px rgba(27,79,168,0.05); }
    .table-scroll { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; }
    thead th {
        font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted);
        padding:14px 16px; text-align:left; border-bottom:1px solid var(--border);
        font-weight:600; background:var(--bg); white-space:nowrap; position:sticky; top:0;
    }
    tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:top; }
    tbody tr { transition:background 0.15s; }
    tbody tr:hover { background:var(--blue-l); }
    tbody tr:last-child td { border-bottom:none; }

    .lead-name { font-weight:600; color:var(--text); font-size:13px; }
    .lead-phone { font-size:11px; color:var(--muted); margin-top:2px; }
    .lead-loc { font-size:10px; color:var(--faint); margin-top:3px; }

    .src-chip { display:inline-block; padding:4px 11px; border-radius:6px; font-size:10px; font-weight:500; background:var(--bg); color:var(--muted); border:1px solid var(--border); white-space:nowrap; }
    .degree-txt { font-size:11px; color:var(--muted); }
    .course-name { font-weight:500; color:var(--text); }
    .course-lvl { font-size:10px; color:var(--faint); margin-top:2px; }
    .pref-text { font-size:11px; color:var(--text); }
    .call-date { font-size:11px; font-weight:600; color:var(--text); }
    .call-time { font-size:10px; color:var(--muted); margin-top:1px; }
    .days-num { font-size:12px; font-weight:600; color:var(--text); }
    .days-num.danger { color:var(--red); }
    .days-lbl { font-size:10px; color:var(--faint); margin-top:1px; }

    /* ═══ STATUS BADGE + DROPDOWN ═══ */
    .status-badge {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 13px; border-radius:20px; font-size:11px; font-weight:600;
        letter-spacing:0.3px; white-space:nowrap; transition:filter 0.2s;
    }
    .status-badge:hover { filter:brightness(0.96); }
    .status-waiting     { background:rgba(122,138,154,0.12); color:var(--muted); }
    .status-call_again  { background:var(--orange-l); color:var(--orange-dk); }
    .status-scheduled   { background:var(--blue-l); color:var(--blue); }
    .status-registered  { background:var(--green-l); color:var(--green-dk); }
    .status-not_interest{ background:var(--red-l); color:var(--red); }
    .status-archived    { background:rgba(154,138,122,0.14); color:#9A8A7A; }
    .status-default     { background:var(--bg); color:var(--muted); }

    .status-dropdown {
        display:none;
        position:absolute; top:calc(100% + 6px); left:0; z-index:50;
        background:var(--card); border:1px solid var(--border); border-radius:9px;
        box-shadow:0 8px 28px rgba(15,31,61,0.14); padding:5px; min-width:150px;
    }
    .status-dropdown-item {
        display:flex; align-items:center; gap:9px; padding:9px 12px; border-radius:6px;
        font-size:12px; color:var(--text); cursor:pointer; transition:background 0.15s; font-weight:500;
    }
    .status-dropdown-item:hover { background:var(--blue-l); }
    .status-dropdown-item::before { content:''; width:8px; height:8px; border-radius:50%; }
    .status-dropdown-item[data-status="Waiting"]::before       { background:#7A8A9A; }
    .status-dropdown-item[data-status="Call_Again"]::before    { background:#C47010; }
    .status-dropdown-item[data-status="Registered"]::before    { background:#15803D; }
    .status-dropdown-item[data-status="Not_Interested"]::before{ background:#DC2626; }
    .status-dropdown-item[data-status="Archived"]::before      { background:#9A8A7A; }

    .notes-cell { font-size:11px; color:#4A5A7A; max-width:150px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .dash-muted { color:var(--faint); }

    /* ═══ ACTIONS ═══ */
    .action-group { display:flex; gap:7px; flex-wrap:wrap; }
    .btn-action {
        display:inline-flex; align-items:center; gap:5px; padding:7px 13px;
        border-radius:7px; font-size:10px; letter-spacing:0.4px; font-weight:600;
        text-decoration:none; cursor:pointer; transition:all 0.2s;
        background:transparent; border:1px solid var(--border);
    }
    .btn-edit { color:var(--blue); border-color:rgba(27,79,168,0.25); }
    .btn-edit:hover { background:var(--blue-l); border-color:var(--blue); text-decoration:none; color:var(--blue); }
    .btn-log { color:var(--muted); border-color:rgba(122,138,154,0.25); }
    .btn-log:hover { background:rgba(122,138,154,0.07); border-color:#4e5e6e; }
    .btn-invoice { color:var(--green); border-color:rgba(5,150,105,0.25); }
    .btn-invoice:hover { background:var(--green-l); border-color:var(--green); text-decoration:none; color:var(--green); }

    /* ═══ EMPTY ═══ */
    .empty-state { text-align:center; padding:60px 20px; }
    .empty-state svg { opacity:0.35; margin-bottom:14px; }
    .empty-title { font-size:16px; font-weight:600; color:var(--muted); margin-bottom:5px; }
    .empty-sub { font-size:12px; color:var(--faint); }

    /* ═══ MODALS ═══ */
    .call-modal { display:none; position:fixed; inset:0; background:rgba(15,31,61,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .call-modal-box { background:var(--card); border-radius:14px; padding:26px; width:90%; max-width:420px; box-shadow:0 20px 60px rgba(15,31,61,0.3); }
    .call-modal-title { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:2px; color:var(--text); margin-bottom:16px; }
    .call-input { width:100%; padding:11px 14px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); outline:none; margin-bottom:18px; }
    .call-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; }
    .btn-cancel { padding:10px 20px; background:transparent; border:1px solid var(--border); border-radius:7px; color:var(--muted); font-size:11px; letter-spacing:1px; text-transform:uppercase; font-weight:600; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { border-color:var(--blue); color:var(--blue); }
    .btn-save { padding:10px 22px; background:var(--blue); border:none; border-radius:7px; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:13px; letter-spacing:2px; cursor:pointer; transition:background 0.2s; }
    .btn-save:hover { background:var(--blue-2); }

    @media (max-width:600px){ .leads-page{ padding:16px; } }
</style>

<script src="{{ asset('js/leads/history-modal.js') }}"></script>
<script src="{{ asset('js/leads/create-modal.js') }}"></script>
<script src="{{ asset('js/register/register-modal.js') }}"></script>

<div class="leads-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Leads</div>
            <h1 class="page-title">My Follow-Up Leads</h1>
            <p class="page-subtitle">Track and manage your active leads pipeline</p>
        </div>
        <a href="{{ route('leads.create') }}" class="btn-add">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            <span>Add Lead</span>
        </a>
    </div>

    <div class="leads-wrap">

    {{-- ── STATS ── --}}
    <div class="stats-row">
        <div class="stat-card" style="--accent:#1B4FA8;cursor:pointer;" onclick="filterByStatus('all')" data-filter="all">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card" style="--accent:#15803D;cursor:pointer;" onclick="filterByStatus('Registered')" data-filter="Registered">
            <div class="stat-label">Registered</div>
            <div class="stat-value">{{ $stats['registered'] }}</div>
        </div>
        <div class="stat-card" style="--accent:#C47010;cursor:pointer;" onclick="filterByStatus('Call_Again')" data-filter="Call_Again">
            <div class="stat-label">Call Again</div>
            <div class="stat-value">{{ $stats['call_again'] }}</div>
        </div>
        <div class="stat-card" style="--accent:#7A8A9A;cursor:pointer;" onclick="filterByStatus('Waiting')" data-filter="Waiting">
            <div class="stat-label">Waiting</div>
            <div class="stat-value">{{ $stats['waiting'] }}</div>
        </div>
        <div class="stat-card" style="--accent:#9A8A7A;cursor:pointer;" onclick="window.location='{{ route('leads.archived') }}'">
            <div class="stat-label">Archived</div>
            <div class="stat-value">{{ $stats['archived'] }}</div>
        </div>
    </div>

    {{-- ── SEARCH + FILTER BAR ── --}}
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px;">
        <div class="search-wrap" style="margin-bottom:0;flex:1;min-width:240px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="leadSearch" class="search-input" placeholder="Search by name or phone..." oninput="searchLeads(this.value)">
        </div>
        <div class="filter-bar" id="filterBar">
            <button class="filter-pill active" data-lead-filter="all" onclick="applyLeadFilter('all', this)">All</button>
            <button class="filter-pill" data-lead-filter="Waiting" onclick="applyLeadFilter('Waiting', this)">Waiting</button>
            <button class="filter-pill" data-lead-filter="Call_Again" onclick="applyLeadFilter('Call_Again', this)">Call Again</button>
            <!-- <button class="filter-pill" data-lead-filter="Not_Interested" onclick="applyLeadFilter('Not_Interested', this)">Not Interested</button> -->
            <button class="filter-pill filter-pill-green" data-lead-filter="Registered" onclick="applyLeadFilter('Registered', this)">Registered</button>
        </div>
    </div>

    {{-- ══ MAIN TABLE (non-registered leads) ══ --}}
    <div class="table-card" id="mainTableCard">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Name &amp; Contact</th>
                        <th>Source</th>
                        <th>Degree</th>
                        <th>Course &amp; Level</th>
                        <th>Status</th>
                        <th>Start Pref.</th>
                        <th>Start Pref. Date</th>
                        <th>Next Call</th>
                        <th>Lead Age</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="mainTableBody">
                    @forelse($leads->where('status', '!=', 'Registered') as $lead)
                        @include('leads.lead-row', ['lead' => $lead])
                    @empty
                    <tr data-empty-main>
                        <td colspan="11">
                            <div class="empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <div class="empty-title">No Leads Found</div>
                                <div class="empty-sub">Start by adding your first follow-up lead</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ REGISTERED TABLE (hidden until "Registered" filter is clicked) ══ --}}
    @php $registeredLeads = $leads->where('status', 'Registered'); @endphp
    <div id="registeredSection" style="display:none;">
        <div class="reg-section-head" style="margin-top:24px;">
            <span class="rsh-dot"></span>
            <span class="rsh-title">Registered Students</span>
            <span class="rsh-count">{{ $registeredLeads->count() }} registered</span>
        </div>
        <div class="table-card">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Name &amp; Contact</th>
                            <th>Source</th>
                            <th>Degree</th>
                            <th>Course &amp; Level</th>
                            <th>Status</th>
                            <th>Start Pref.</th>
                            <th>Start Pref. Date</th>
                            <th>Next Call</th>
                            <th>Lead Age</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="registeredTableBody">
                        @forelse($registeredLeads as $lead)
                            @include('leads.lead-row', ['lead' => $lead])
                        @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <div class="empty-title">No Registered Students</div>
                                    <div class="empty-sub">Registered leads will appear here</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($leads->hasPages())
        <div style="margin-top:20px;">
            {{ $leads->links() }}
        </div>
    @endif

    </div>{{-- /leads-wrap --}}

    {{-- ── CALL MODAL ── --}}
    <div id="callModal" class="call-modal">
        <div class="call-modal-box">
            <div class="call-modal-title">Schedule Next Call</div>
            <input type="datetime-local" id="callDate" class="call-input">
            <div class="modal-actions">
                <button onclick="closeModal()" class="btn-cancel">Cancel</button>
                <button onclick="confirmCall()" class="btn-save">Confirm</button>
            </div>
        </div>
    </div>

    {{-- ── HISTORY MODAL ── --}}
    <div id="historyModal" class="call-modal">
        <div class="call-modal-box" style="max-width:520px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div class="call-modal-title" style="margin-bottom:0;">Lead History</div>
                <button onclick="closeHistoryModal()" style="background:transparent;border:none;color:var(--muted);cursor:pointer;font-size:20px;line-height:1;">&times;</button>
            </div>
            <div id="historyContent" style="max-height:400px;overflow-y:auto;font-size:13px;color:var(--text);">
                {{-- filled by JS --}}
            </div>
            <div class="modal-actions" style="margin-top:18px;">
                <button onclick="closeHistoryModal()" class="btn-cancel">Close</button>
            </div>
        </div>
    </div>

</div>

@if(session('success'))
<div id="successToast" style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--green);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(15,31,61,0.18);font-size:13px;color:var(--text);animation:toastIn 0.35s ease;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" style="flex-shrink:0;"><path d="M20 6L9 17l-5-5"/></svg>
    {{ session('success') }}
    <button onclick="document.getElementById('successToast').style.display='none'" style="background:transparent;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;margin-left:8px;">&times;</button>
</div>

<style>
@keyframes toastIn { from { opacity:0; transform:translateX(20px) scale(0.96); } to { opacity:1; transform:none; } }
@keyframes toastOut { from { opacity:1; transform:none; } to { opacity:0; transform:translateX(20px) scale(0.96); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-lead-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const confirmed = await infConfirm.show({
                label:   'Delete Lead',
                title:   'Delete This Lead?',
                message: 'This will permanently remove the lead and all its history. This action cannot be undone.',
                okText:  'Delete',
            });
            if (confirmed) form.submit();
        });
    });
});
setTimeout(function() {
    const toast = document.getElementById('successToast');
    if (toast) {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }
}, 4000);
</script>
@endif

<script>
/**
 * Master lead filter — controls the main table + the separate Registered table.
 * - 'Registered'  → hide main table, show the Registered section only
 * - anything else → show main table (filtered), hide Registered section
 * - 'all'         → show main table (all non-registered), hide Registered section
 */
function applyLeadFilter(filter, btn) {
    // Sync the filter-pill active state
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    if (btn) {
        btn.classList.add('active');
    } else {
        const pill = document.querySelector('.filter-pill[data-lead-filter="' + filter + '"]');
        if (pill) pill.classList.add('active');
    }

    // Sync the stat-card active state (so the two stay visually in sync)
    document.querySelectorAll('.stat-card').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filter === filter);
    });

    const mainCard   = document.getElementById('mainTableCard');
    const regSection = document.getElementById('registeredSection');

    if (filter === 'Registered') {
        // Show only the registered table
        if (mainCard)   mainCard.style.display   = 'none';
        if (regSection) regSection.style.display = 'block';
    } else {
        // Show the main table, hide registered section
        if (mainCard)   mainCard.style.display   = '';
        if (regSection) regSection.style.display = 'none';

        // Filter rows inside the main table
        document.querySelectorAll('#mainTableBody tr[data-status]').forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.dataset.status === filter ? '' : 'none';
            }
        });
    }

    // Clear any active search when switching filters
    const searchBox = document.getElementById('leadSearch');
    if (searchBox && searchBox.value) {
        searchBox.value = '';
    }
}

// Redirect the old stat-card filter calls to the new master filter,
// so the top stat cards and the filter pills behave identically.
function filterByStatus(status) {
    applyLeadFilter(status, null);
}

// Search that respects the currently-active filter (main table + registered table)
function searchLeads(query) {
    const q = query.toLowerCase().trim();
    const activePill = document.querySelector('.filter-pill.active');
    const currentFilter = activePill ? activePill.dataset.leadFilter : 'all';

    // Which tbody are we searching in?
    const scope = (currentFilter === 'Registered')
        ? '#registeredTableBody tr[data-status]'
        : '#mainTableBody tr[data-status]';

    document.querySelectorAll(scope).forEach(row => {
        const name  = row.querySelector('.lead-name')?.textContent.toLowerCase() ?? '';
        const phone = row.querySelector('.lead-phone')?.textContent.toLowerCase() ?? '';
        const matchesSearch = q === '' || name.includes(q) || phone.includes(q);

        let matchesFilter = true;
        if (currentFilter !== 'all' && currentFilter !== 'Registered') {
            matchesFilter = row.dataset.status === currentFilter;
        }
        row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
}
</script>

@endsection