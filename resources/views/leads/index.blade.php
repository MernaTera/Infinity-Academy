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
        --dark:#0F1F3D; --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
        --glass:rgba(255,255,255,0.5); --glass-bd:rgba(255,255,255,0.6); --glass-sh:0 8px 30px rgba(23,45,90,0.07);
    }
    * { box-sizing:border-box; }

    .leads-page { background:var(--bg); min-height:100vh; padding:30px 34px 44px; color:var(--text); font-family:'DM Sans',sans-serif; }

    .page-header {
        margin:0 auto 16px; display:flex; align-items:flex-end; justify-content:space-between;
        flex-wrap:wrap; gap:16px;
    }
    .page-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:7px; font-weight:600; }
    .page-title { font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0; }
    .page-subtitle { font-size:12px; color:var(--muted); margin-top:6px; letter-spacing:0.3px; }
    .btn-add {
        display:inline-flex; align-items:center; gap:8px; padding:13px 20px; border-radius:13px;
        background:linear-gradient(120deg,var(--blue),var(--blue-2)); border:none; color:#fff;
        font-family:'DM Sans',sans-serif; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-weight:600;
        text-decoration:none; transition:transform 0.2s, box-shadow 0.2s; box-shadow:0 10px 24px rgba(27,79,168,0.32);
    }
    .btn-add:hover { transform:translateY(-2px); text-decoration:none; color:#fff; box-shadow:0 14px 30px rgba(27,79,168,0.4); }

    .leads-wrap { margin:0 auto; }

    .stats-row { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:20px; }
    @media (max-width:900px){ .stats-row{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:520px){ .stats-row{ grid-template-columns:1fr 1fr; } }
    .stat-card {
        background:var(--glass); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%);
        border:1px solid var(--glass-bd); border-radius:18px; padding:18px 20px; position:relative; overflow:hidden;
        transition:transform 0.2s, box-shadow 0.2s; box-shadow:var(--glass-sh);
    }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent,var(--blue)); }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 16px 38px rgba(23,45,90,0.13); }
    .stat-card.active-filter { box-shadow:0 0 0 2px var(--accent), 0 12px 30px rgba(23,45,90,0.12); }
    .stat-label { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:8px; }
    .stat-value { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:1px; line-height:0.9; color:var(--accent,var(--blue)); }

    .search-wrap { margin-bottom:18px; position:relative; max-width:400px; }
    .search-wrap svg { position:absolute; left:15px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input {
        width:100%; padding:12px 16px 12px 42px;
        background:var(--glass); backdrop-filter:blur(12px) saturate(150%); -webkit-backdrop-filter:blur(12px) saturate(150%);
        border:1px solid var(--glass-bd); border-radius:13px;
        font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text);
        outline:none; transition:border-color 0.25s, box-shadow 0.25s;
    }
    .search-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }

    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; }
    .filter-pill {
        padding:10px 16px; border-radius:13px; font-size:11px; font-weight:600;
        letter-spacing:0.4px; cursor:pointer; transition:all 0.2s;
        background:var(--glass); backdrop-filter:blur(12px) saturate(150%); -webkit-backdrop-filter:blur(12px) saturate(150%);
        border:1px solid var(--glass-bd); color:var(--muted); white-space:nowrap;
    }
    .filter-pill:hover { color:var(--blue); border-color:rgba(27,79,168,0.3); }
    .filter-pill.active { background:linear-gradient(120deg,var(--blue),var(--blue-2)); border-color:transparent; color:#fff; box-shadow:0 8px 20px rgba(27,79,168,0.28); }
    .filter-pill-green.active { background:linear-gradient(120deg,var(--green),#0E9F6E); border-color:transparent; box-shadow:0 8px 20px rgba(5,150,105,0.28); }

    .reg-section-head { display:flex; align-items:center; gap:10px; margin:0 0 14px; }
    .reg-section-head .rsh-dot { width:8px; height:8px; border-radius:50%; background:var(--green); box-shadow:0 0 8px var(--green); }
    .reg-section-head .rsh-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; color:var(--green-dk); }
    .reg-section-head .rsh-count { font-size:11px; color:var(--muted); background:var(--green-l); padding:3px 10px; border-radius:20px; font-weight:600; }

    .table-card {
        background:var(--glass); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%);
        border:1px solid var(--glass-bd); border-radius:20px; overflow:hidden; box-shadow:var(--glass-sh);
    }
    .table-scroll { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; }
    thead th {
        font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted);
        padding:15px 16px; text-align:left; border-bottom:1px solid var(--glass-bd);
        font-weight:700; background:rgba(255,255,255,0.55); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
        white-space:nowrap; position:sticky; top:0; z-index:2;
    }
    tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.06); font-size:12px; color:var(--text); vertical-align:middle; }
    tbody tr { transition:background 0.15s; position:relative; }
    tbody tr:hover { background:rgba(27,79,168,0.04); }
    tbody tr td:first-child { position:relative; }
    tbody tr:hover td:first-child::before {
        content:''; position:absolute; left:0; top:0; bottom:0; width:3px;
        background:linear-gradient(180deg,#F5911E,#1B4FA8); border-radius:0 2px 2px 0;
    }
    tbody tr:last-child td { border-bottom:none; }

    .lead-name { font-weight:600; color:var(--text); font-size:13px; }
    .lead-phone { font-size:11px; color:var(--muted); margin-top:2px; }
    .lead-loc { font-size:10px; color:var(--faint); margin-top:3px; }

    .src-chip { display:inline-block; padding:4px 11px; border-radius:8px; font-size:10px; font-weight:500; background:rgba(27,79,168,0.06); color:var(--muted); border:1px solid var(--glass-bd); white-space:nowrap; }
    .degree-txt { font-size:11px; color:var(--muted); }
    .course-name { font-weight:500; color:var(--text); }
    .course-lvl { font-size:10px; color:var(--faint); margin-top:2px; }
    .pref-text { font-size:11px; color:var(--text); }
    .call-date { font-size:11px; font-weight:600; color:var(--text); }
    .call-time { font-size:10px; color:var(--muted); margin-top:1px; }
    .days-num { font-size:12px; font-weight:600; color:var(--text); }
    .days-num.danger { color:var(--red); }
    .days-lbl { font-size:10px; color:var(--faint); margin-top:1px; }

    .status-badge {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 13px; border-radius:20px; font-size:11px; font-weight:600;
        letter-spacing:0.3px; white-space:nowrap;
        transition:filter 0.2s, box-shadow 0.2s, transform 0.15s;
        border:1px solid transparent;
    }
    .status-badge:hover { filter:brightness(0.97); box-shadow:0 2px 8px rgba(15,31,61,0.08); }
    .status-badge:active { transform:scale(0.97); }
    .status-badge svg { transition:transform 0.2s; opacity:0.7; }
    .status-badge.badge-open svg { transform:rotate(180deg); }
    .status-waiting     { background:rgba(122,138,154,0.14); color:var(--muted); }
    .status-call_again  { background:rgba(245,145,30,0.12); color:var(--orange-dk); }
    .status-scheduled   { background:rgba(27,79,168,0.10); color:var(--blue); }
    .status-registered  { background:rgba(5,150,105,0.12); color:var(--green-dk); }
    .status-not_interest{ background:rgba(220,38,38,0.10); color:var(--red); }
    .status-archived    { background:rgba(154,138,122,0.16); color:#9A8A7A; }
    .status-pending-approval { background:rgba(245,145,30,0.12); color:#C47010; border:1px dashed rgba(245,145,30,0.5); }
    .status-default     { background:rgba(122,138,154,0.14); color:var(--muted); }

    .status-dropdown {
        display:none;
        position:absolute; top:calc(100% + 6px); left:0; z-index:9999;
        background:rgba(255,255,255,0.72); backdrop-filter:blur(22px) saturate(160%); -webkit-backdrop-filter:blur(22px) saturate(160%);
        border:1px solid var(--glass-bd); border-radius:13px;
        box-shadow:0 16px 38px rgba(15,31,61,0.18);
        padding:6px; min-width:160px;
        transform-origin:top left;
        animation:statusDropIn 0.16s cubic-bezier(0.16,1,0.3,1);
    }
    .status-dropdown.opens-up { transform-origin:bottom left; animation-name:statusDropInUp; }
    @keyframes statusDropIn {
        from { opacity:0; transform:translateY(-6px) scale(0.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    @keyframes statusDropInUp {
        from { opacity:0; transform:translateY(6px) scale(0.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .status-dropdown-item {
        display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:9px;
        font-size:12px; color:var(--text); cursor:pointer; transition:background 0.15s, padding-left 0.15s; font-weight:500;
    }
    .status-dropdown-item:hover { background:rgba(27,79,168,0.06); padding-left:15px; }
    .status-dropdown-item::before { content:''; width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .status-dropdown-item[data-status="Waiting"]::before       { background:#7A8A9A; }
    .status-dropdown-item[data-status="Call_Again"]::before    { background:#C47010; }
    .status-dropdown-item[data-status="Registered"]::before    { background:#15803D; }
    .status-dropdown-item[data-status="Not_Interested"]::before{ background:#DC2626; }
    .status-dropdown-item[data-status="Archived"]::before      { background:#9A8A7A; }

    .notes-cell { font-size:11px; color:#4A5A7A; max-width:150px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .dash-muted { color:var(--faint); }

    .action-group { display:flex; gap:7px; flex-wrap:wrap; }
    .btn-action {
        display:inline-flex; align-items:center; gap:5px; padding:7px 13px;
        border-radius:9px; font-size:10px; letter-spacing:0.4px; font-weight:600;
        text-decoration:none; cursor:pointer; transition:all 0.2s;
        background:rgba(255,255,255,0.4); border:1px solid var(--glass-bd);
    }
    .btn-edit { color:var(--blue); border-color:rgba(27,79,168,0.25); }
    .btn-edit:hover { background:rgba(27,79,168,0.08); border-color:var(--blue); text-decoration:none; color:var(--blue); }
    .btn-log { color:var(--muted); border-color:rgba(122,138,154,0.25); }
    .btn-log:hover { background:rgba(122,138,154,0.1); border-color:#4e5e6e; }
    .btn-invoice { color:var(--green); border-color:rgba(5,150,105,0.25); }
    .btn-invoice:hover { background:rgba(5,150,105,0.08); border-color:var(--green); text-decoration:none; color:var(--green); }

    .empty-state { text-align:center; padding:60px 20px; }
    .empty-state svg { opacity:0.35; margin-bottom:14px; }
    .empty-title { font-size:16px; font-weight:600; color:var(--muted); margin-bottom:5px; }
    .empty-sub { font-size:12px; color:var(--faint); }

    .call-modal { display:none; position:fixed; inset:0; background:rgba(15,31,61,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .call-modal-box {
        background:rgba(255,255,255,0.75); backdrop-filter:blur(30px) saturate(160%); -webkit-backdrop-filter:blur(30px) saturate(160%);
        border:1px solid var(--glass-bd); border-radius:20px; padding:26px; width:90%; max-width:420px; box-shadow:0 20px 60px rgba(15,31,61,0.3);
    }
    .call-modal-title { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:2px; color:var(--text); margin-bottom:16px; }
    .call-input { width:100%; padding:11px 14px; border:1px solid var(--glass-bd); border-radius:11px; background:rgba(255,255,255,0.5); font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); outline:none; margin-bottom:18px; }
    .call-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; }
    .btn-cancel { padding:10px 20px; background:rgba(255,255,255,0.5); border:1px solid var(--glass-bd); border-radius:11px; color:var(--muted); font-size:11px; letter-spacing:1px; text-transform:uppercase; font-weight:600; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { border-color:var(--blue); color:var(--blue); }
    .btn-save { padding:10px 22px; background:linear-gradient(120deg,var(--blue),var(--blue-2)); border:none; border-radius:11px; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:13px; letter-spacing:2px; cursor:pointer; transition:transform 0.2s, box-shadow 0.2s; }
    .btn-save:hover { transform:translateY(-1px); box-shadow:0 10px 24px rgba(27,79,168,0.32); }

    @media (max-width:600px){ .leads-page{ padding:18px 16px 32px; } }
</style>

<script src="{{ asset('js/leads/history-modal.js') }}"></script>
<script src="{{ asset('js/leads/create-modal.js') }}"></script>
<script src="{{ asset('js/register/register-modal.js') }}"></script>

<div class="leads-page">

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

    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px;">
        <div class="search-wrap" style="margin-bottom:0;flex:1;min-width:240px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="leadSearch" class="search-input" placeholder="Search by name or phone..." oninput="searchLeads(this.value)">
        </div>
        <div class="filter-bar" id="filterBar">
            <button class="filter-pill active" data-lead-filter="all" onclick="applyLeadFilter('all', this)">All</button>
            <button class="filter-pill" data-lead-filter="Waiting" onclick="applyLeadFilter('Waiting', this)">Waiting</button>
            <button class="filter-pill" data-lead-filter="Call_Again" onclick="applyLeadFilter('Call_Again', this)">Call Again</button>
            <button class="filter-pill filter-pill-green" data-lead-filter="Registered" onclick="applyLeadFilter('Registered', this)">Registered</button>
        </div>
    </div>

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
                        @include('leads.lead-row', ['lead' => $lead, 'isPendingApproval' => in_array($lead->student_id, $pendingApprovalStudentIds ?? [])])
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

    <div id="registeredSection" style="display:none;">
        <div class="reg-section-head" style="margin-top:24px;">
            <span class="rsh-dot"></span>
            <span class="rsh-title">Registered Students</span>
            <span class="rsh-count">{{ $registeredRows->count() }} enrollment{{ $registeredRows->count() === 1 ? '' : 's' }}</span>
        </div>
        <div class="table-card">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Name &amp; Contact</th>
                            <th>Course &amp; Level</th>
                            <th>Type</th>
                            <th>Enrollment</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="registeredTableBody">
                        @forelse($registeredRows as $row)
                            @php $lead = $row['lead']; $enr = $row['enrollment']; @endphp
                            <tr data-status="Registered">
                                <td>
                                    <div class="lead-name">{{ $lead->full_name }}</div>
                                    <div class="lead-phone">{{ $lead->phone }}</div>
                                </td>

                                <td>
                                    @if($enr && $enr->courseTemplate)
                                        <div class="course-name">{{ $enr->courseTemplate->name }}</div>
                                        @if($enr->level)
                                            <div class="course-lvl">{{ $enr->level->name }}@if($enr->sublevel) · {{ $enr->sublevel->name }}@endif</div>
                                        @endif
                                    @elseif($lead->courseTemplate)
                                        <div class="course-name">{{ $lead->courseTemplate->name }}</div>
                                    @else
                                        <span class="dash-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($enr)
                                        <span class="src-chip">{{ $enr->enrollment_type }}</span>
                                        @if($enr->package_id)
                                            <div style="font-size:9px;color:#7C3AED;margin-top:3px;">Package</div>
                                        @elseif($enr->enrollment_type === 'Private' && $enr->hours_remaining !== null)
                                            <div style="font-size:9px;color:#C47010;margin-top:3px;">{{ rtrim(rtrim(number_format($enr->hours_remaining,2),'0'),'.') }}h left</div>
                                        @endif
                                    @else
                                        <span class="dash-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($enr)
                                        <span style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:#1B4FA8;">#{{ $enr->enrollment_id }}</span>
                                    @else
                                        <span class="dash-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($enr)
                                        @php
                                            $enrStatusColor = match($enr->status) {
                                                'Active' => '#15803D', 'Completed' => '#1B4FA8',
                                                'Waiting' => '#C47010', 'Restricted' => '#DC2626',
                                                'Pending_Approval' => '#7C3AED', default => '#7A8A9A',
                                            };
                                        @endphp
                                        <span class="status-badge" style="background:{{ $enrStatusColor }}1a;color:{{ $enrStatusColor }};pointer-events:none;">
                                            {{ str_replace('_',' ',$enr->status) }}
                                        </span>
                                    @else
                                        <span class="status-badge status-registered" style="pointer-events:none;">Registered</span>
                                    @endif
                                </td>

                                <td>
                                    @if($enr)
                                        @if((float)$enr->final_price == 0)
                                            <span style="color:#15803D;font-size:11px;font-weight:600;">FREE</span>
                                        @else
                                            <span style="font-variant-numeric:tabular-nums;">{{ number_format($enr->final_price) }} LE</span>
                                        @endif
                                    @else
                                        <span class="dash-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-group">
                                        @if($enr)
                                            <a href="{{ route('cs.enrollment.invoice', $enr->enrollment_id) }}" target="_blank" class="btn-action btn-invoice" title="View / Print Invoice">
                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                                                Invoice
                                            </a>
                                        @endif
                                        <button class="btn-action btn-log" onclick="openHistoryModal({{ $lead->lead_id }})">
                                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                            Log
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <div class="empty-title">No Registered Students</div>
                                    <div class="empty-sub">Registered enrollments will appear here</div>
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

    </div>

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

    <div id="historyModal" class="call-modal">
        <div class="call-modal-box" style="max-width:520px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div class="call-modal-title" style="margin-bottom:0;">Lead History</div>
                <button onclick="closeHistoryModal()" style="background:transparent;border:none;color:var(--muted);cursor:pointer;font-size:20px;line-height:1;">&times;</button>
            </div>
            <div id="historyContent" style="max-height:400px;overflow-y:auto;font-size:13px;color:var(--text);">
            </div>
            <div class="modal-actions" style="margin-top:18px;">
                <button onclick="closeHistoryModal()" class="btn-cancel">Close</button>
            </div>
        </div>
    </div>

</div>

@if(session('success'))
<div id="successToast" style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--green);border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(15,31,61,0.18);font-size:13px;color:var(--text);animation:toastIn 0.35s ease;">
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
function applyLeadFilter(filter, btn) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    if (btn) {
        btn.classList.add('active');
    } else {
        const pill = document.querySelector('.filter-pill[data-lead-filter="' + filter + '"]');
        if (pill) pill.classList.add('active');
    }

    document.querySelectorAll('.stat-card').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filter === filter);
    });

    const mainCard   = document.getElementById('mainTableCard');
    const regSection = document.getElementById('registeredSection');

    if (filter === 'Registered') {
        if (mainCard)   mainCard.style.display   = 'none';
        if (regSection) regSection.style.display = 'block';
    } else {
        if (mainCard)   mainCard.style.display   = '';
        if (regSection) regSection.style.display = 'none';

        document.querySelectorAll('#mainTableBody tr[data-status]').forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.dataset.status === filter ? '' : 'none';
            }
        });
    }

    const searchBox = document.getElementById('leadSearch');
    if (searchBox && searchBox.value) {
        searchBox.value = '';
    }
}

function filterByStatus(status) {
    applyLeadFilter(status, null);
}

function searchLeads(query) {
    const q = query.toLowerCase().trim();
    const activePill = document.querySelector('.filter-pill.active');
    const currentFilter = activePill ? activePill.dataset.leadFilter : 'all';

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
