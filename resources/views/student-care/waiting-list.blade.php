@extends('student-care.layouts.app')

@section('title', 'Waiting List')

@include('student-care.waiting-list.partials.assign-modal')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    body, .wl-page * { font-family: 'DM Sans', sans-serif; }

    .wl-page {
        position:relative; overflow:hidden;
        margin:-30px; padding:30px 34px 44px;
        min-height:calc(100vh - 62px);
        background:var(--bg); color:var(--text);
    }
    @media (max-width:600px){ .wl-page{ margin:-30px; padding:18px 16px 32px; } }

    .orb{ position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2{ width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3{ width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }
    .wl-page > *:not(.orb){ position:relative; z-index:1; }

    .page-header {
        display:flex; align-items:flex-end; justify-content:space-between;
        margin-bottom:22px; padding-bottom:18px;
        border-bottom:1px solid rgba(27,79,168,0.1);
        flex-wrap:wrap; gap:16px;
    }
    .page-eyebrow  { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); font-weight:600; margin-bottom:7px; }
    .page-title    { font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:2px; color:var(--text); line-height:0.95; }
    .page-subtitle { font-size:12px; color:var(--muted); margin-top:7px; }

    .stats-row {
        display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr));
        gap:12px; margin-bottom:22px;
    }
    .stat-card {
        background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%);
        border:1px solid var(--border); border-radius:16px;
        padding:16px 18px; position:relative; overflow:hidden;
        box-shadow:var(--glass-sh); cursor:pointer; transition:transform .2s, box-shadow .2s;
    }

    .stat-card:hover         { transform:translateY(-3px); box-shadow:0 16px 38px rgba(23,45,90,0.13); }
    .stat-card.active-filter { box-shadow:0 0 0 2px var(--accent); transform:translateY(-3px); }
    .stat-label { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:6px; }
    .stat-value { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:1px; color:var(--accent,#1B4FA8); line-height:1; }

    .toolbar { display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
    .search-wrap { position:relative; flex:1; min-width:220px; max-width:360px; }
    .search-wrap svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input {
        width:100%; padding:11px 14px 11px 40px;
        background:var(--card); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
        border:1px solid var(--border); border-radius:12px;
        font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); outline:none;
        transition:border-color .3s, box-shadow .3s;
    }
    .search-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }

    .filter-select {
        padding:10px 32px 10px 12px;
        background-color:var(--card); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
        border:1px solid var(--border); border-radius:12px;
        font-family:'DM Sans',sans-serif; font-size:12px; color:var(--muted); outline:none; cursor:pointer;
        appearance:none; -webkit-appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 10px center;
        transition:border-color .3s;
    }
    .filter-select:focus { border-color:var(--blue); }

    .toolbar .app-select,
    .toolbar > .filter-select { flex:1 1 150px; min-width:140px; max-width:260px; }

    .table-card {
        min-height:400px;
        background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%);
        border:1px solid var(--border); border-radius:20px; overflow:visible;
        box-shadow:var(--glass-sh);
    }
    .table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; border-radius:20px; }
    .table-card table { width:100%; border-collapse:collapse; min-width:960px; }
    .table-card thead tr { border-bottom:1px solid var(--border); }
    .table-card thead th {
        padding:13px 16px; font-size:9px; letter-spacing:2px;
        text-transform:uppercase; color:var(--muted); font-weight:600;
        white-space:nowrap; background:rgba(255,255,255,0.4); text-align:left;
    }
    .table-card tbody tr { border-bottom:1px solid rgba(27,79,168,0.06); transition:background .15s; }
    .table-card tbody tr:hover { background:rgba(27,79,168,0.04); }
    .table-card tbody tr:last-child { border-bottom:none; }
    .table-card tbody td { padding:13px 16px; font-size:13px; color:var(--muted); vertical-align:middle; }

    .lead-name  { font-weight:600; color:var(--text); font-size:13px; }
    .lead-sub   { font-size:11px; color:var(--muted); margin-top:2px; }

    .tag {
        display:inline-block; font-size:9px; letter-spacing:1px;
        padding:2px 8px; border-radius:6px; white-space:nowrap;
        text-transform:uppercase; font-weight:600; margin-bottom:3px;
    }
    .tag-course  { background:rgba(27,79,168,0.07);  border:1px solid rgba(27,79,168,0.15);  color:var(--blue); }
    .tag-level   { background:rgba(245,145,30,0.07); border:1px solid rgba(245,145,30,0.2);  color:var(--orange-dk); }
    .tag-group   { background:rgba(27,79,168,0.05);  border:1px solid rgba(27,79,168,0.12);  color:var(--blue-2); }
    .tag-private { background:rgba(245,145,30,0.05); border:1px solid rgba(245,145,30,0.15); color:var(--orange-dk); }
    .tag-online  { background:rgba(5,150,105,0.06);  border:1px solid rgba(5,150,105,0.15);  color:var(--green-dk); }
    .tag-offline { background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15);color:var(--muted); }

    .status-badge {
        display:inline-flex; align-items:center; gap:5px;
        font-size:9px; letter-spacing:1.2px; text-transform:uppercase;
        padding:4px 10px; border-radius:20px; white-space:nowrap; font-weight:600;
    }
    .status-badge::before { content:''; width:4px; height:4px; border-radius:50%; background:currentColor; flex-shrink:0; }
    .status-active    { color:var(--orange-dk); background:rgba(245,145,30,0.1); }
    .status-assigned  { color:var(--green-dk); background:rgba(5,150,105,0.1); }
    .status-cancelled { color:var(--red); background:rgba(220,38,38,0.08); }
    .status-wl-default{ color:var(--muted); background:rgba(122,138,154,0.1); }

    .action-group { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
    .btn-action {
        display:inline-flex; align-items:center; gap:4px;
        padding:6px 12px; font-size:9px; letter-spacing:1.5px;
        text-transform:uppercase; border-radius:8px;
        font-family:'DM Sans',sans-serif; font-weight:600;
        border:1px solid; background:transparent; cursor:pointer;
        transition:all .2s; white-space:nowrap;
    }
    .btn-assign    { color:var(--blue); border-color:rgba(27,79,168,0.25); }
    .btn-assign:hover { background:rgba(27,79,168,0.07); border-color:var(--blue); }
    .btn-cancel-wl { color:var(--red); border-color:rgba(220,38,38,0.2); }
    .btn-cancel-wl:hover { background:rgba(220,38,38,0.06); border-color:rgba(220,38,38,0.5); }

    .empty-state { padding:60px 24px; text-align:center; }
    .empty-state svg { margin:0 auto 14px; opacity:0.2; }
    .empty-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:4px; color:var(--muted); margin-bottom:6px; }
    .empty-sub   { font-size:12px; color:var(--faint); }

    .pagination-wrap { margin-top:20px; }
    .pagination-wrap .page-link {
        background:var(--card) !important; border:1px solid var(--border) !important;
        color:var(--muted) !important; font-size:11px; letter-spacing:1px;
        border-radius:8px !important; padding:6px 12px; transition:all .2s;
    }
    .pagination-wrap .page-link:hover {
        background:rgba(27,79,168,0.06) !important; color:var(--blue) !important; border-color:rgba(27,79,168,0.3) !important;
    }
    .pagination-wrap .page-item.active .page-link {
        background:transparent !important; border-color:var(--blue) !important; color:var(--blue) !important; font-weight:600 !important;
    }

    @media (max-width:480px) { .page-header { flex-direction:column; align-items:flex-start; } }
</style>

<div class="wl-page">

    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care</div>
            <h1 class="page-title">Waiting List</h1>
            <p class="page-subtitle">Students pending course assignment</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;padding:10px 18px;
                    background:rgba(255,255,255,0.7);border:1px solid rgba(27,79,168,0.1);
                    border-radius:6px;box-shadow:0 2px 8px rgba(27,79,168,0.04);">
            <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;">Total</span>
            <span style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:2px;line-height:1;">
                {{ $waiting->count() }}
            </span>
        </div>
    </div>

    @php
        $countActive    = $waiting->where('status', 'Active')->count();
        $countAssigned  = $waiting->where('status', 'Assigned')->count();
        $countCancelled = $waiting->where('status', 'Cancelled')->count();
        $countGroup     = $waiting->where('preferred_delivery_type', 'Group')->count();
        $countPrivate   = $waiting->where('preferred_delivery_type', 'Private')->count();
        $countOnline    = $waiting->where('preferred_delivery_mood', 'Online')->count();
        $countOffline   = $waiting->where('preferred_delivery_mood', 'Offline')->count();
    @endphp

    <div class="stats-row">
        <div class="stat-card" style="--accent:#1B4FA8;" onclick="filterByStatus('all')" data-filter="all">
            <div class="stat-label">All</div>
            <div class="stat-value">{{ $waiting->count() }}</div>
        </div>
        <div class="stat-card" style="--accent:#C47010;" onclick="filterByStatus('Active')" data-filter="Active">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ $countActive }}</div>
        </div>
        <div class="stat-card" style="--accent:#15803D;" onclick="filterByStatus('Assigned')" data-filter="Assigned">
            <div class="stat-label">Assigned</div>
            <div class="stat-value">{{ $countAssigned }}</div>
        </div>

        <div class="stat-card" style="--accent:#2D6FDB;" onclick="filterByDeliveryType('Group')" data-filter-dtype="Group">
            <div class="stat-label">Group</div>
            <div class="stat-value">{{ $countGroup }}</div>
        </div>
        <div class="stat-card" style="--accent:#C47010;" onclick="filterByDeliveryType('Private')" data-filter-dtype="Private">
            <div class="stat-label">Private</div>
            <div class="stat-value">{{ $countPrivate }}</div>
        </div>
        <div class="stat-card" style="--accent:#15803D;" onclick="filterByMode('Online')" data-filter-mode="Online">
            <div class="stat-label">Online</div>
            <div class="stat-value">{{ $countOnline }}</div>
        </div>
        <div class="stat-card" style="--accent:#7A8A9A;" onclick="filterByMode('Offline')" data-filter-mode="Offline">
            <div class="stat-label">Offline</div>
            <div class="stat-value">{{ $countOffline }}</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="wlSearch" class="search-input"
                   placeholder="Search by name, ID, phone, or days..."
                   oninput="searchWaiting(this.value)">
        </div>

        <select class="filter-select" id="statusFilter" onchange="filterByStatusSelect(this.value)">
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Assigned">Assigned</option>

        </select>

        <select class="filter-select" id="dtypeFilter" onchange="filterByDeliveryTypeSelect(this.value)">
            <option value="">All Types</option>
            <option value="Group">Group</option>
            <option value="Private">Private</option>
        </select>

        <select class="filter-select" id="modeFilter" onchange="filterByModeSelect(this.value)">
            <option value="">All Modes</option>
            <option value="Online">Online</option>
            <option value="Offline">Offline</option>
        </select>

        <select class="filter-select" id="ptypeFilter" onchange="filterByPrefTypeSelect(this.value)">
            <option value="">All Patch Prefs</option>
            <option value="Current_Patch">Current Patch</option>
            <option value="Next_Patch">Next Patch</option>
            <option value="Specific_Date">Specific Date</option>
        </select>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course & Level</th>
                        <th>Delivery Type</th>
                        <th>Mode</th>
                        <th>Patch Preference</th>
                        <th>Requested Patch</th>
                        <th>Preferred Date</th>
                        <th>Preferred Days</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="wlTableBody">
                    @forelse($waiting as $item)
                    @php
                        $statusClass = match($item->status) {
                            'Active'    => 'status-active',
                            'Assigned'  => 'status-assigned',
                            'Cancelled' => 'status-cancelled',
                            default     => 'status-wl-default',
                        };
                        $dtypeClass = match($item->preferred_delivery_type) {
                            'Group'   => 'tag-group',
                            'Private' => 'tag-private',
                            default   => 'tag-group',
                        };
                        $modeClass = match($item->preferred_delivery_mood) {
                            'Online'  => 'tag-online',
                            'Offline' => 'tag-offline',
                            default   => 'tag-offline',
                        };
                        $phones      = ($item->enrollment->student->phones ?? collect())->sortByDesc(fn($p) => $p->is_primary)->values();
                        $allPhones   = $phones->pluck('phone_number')->filter()->implode(' ');
                    @endphp
                    <tr data-status="{{ $item->status }}"
                        data-dtype="{{ $item->preferred_delivery_type }}"
                        data-mode="{{ $item->preferred_delivery_mood }}"
                        data-ptype="{{ $item->preferred_type }}"
                        data-name="{{ strtolower($item->enrollment->student->full_name ?? '') }}"
                        data-sid="{{ $item->enrollment->student_id ?? '' }}"
                        data-phone="{{ $allPhones }}"
                        data-days="{{ str_replace('_',' ',$item->preferred_days ?? '') }}">

                        <td>
                            <div class="lead-name">{{ $item->enrollment->student->full_name ?? '—' }}</div>
                            <div class="lead-sub">ID: {{ $item->enrollment->student_id ?? '—' }}</div>
                            @forelse($phones as $ph)
                            <div class="lead-sub" style="display:inline-flex;align-items:center;gap:4px;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                {{ $ph->phone_number }}
                            </div>
                            @empty
                            <div class="lead-sub" style="display:inline-flex;align-items:center;gap:4px;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                —
                            </div>
                            @endforelse
                        </td>

                        <td>
                            @if($item->enrollment->courseTemplate ?? null)
                                <span class="tag tag-course">{{ $item->enrollment->courseTemplate->name }}</span>
                            @endif
                            @if($item->enrollment->level ?? null)
                                <br><span class="tag tag-level">{{ $item->enrollment->level->name }}</span>
                            @endif
                            @if($item->enrollment->sublevel ?? null)
                                <br><span class="tag tag-level" style="font-size:8px;">{{ $item->enrollment->sublevel->name }}</span>
                            @endif
                        </td>

                        <td>
                            <span class="tag {{ $dtypeClass }}">{{ $item->preferred_delivery_type }}</span>
                        </td>

                        <td>
                            <span class="tag {{ $modeClass }}">{{ $item->preferred_delivery_mood }}</span>
                        </td>

                        <td>
                            <span style="font-size:11px;color:#4A5A7A;letter-spacing:0.5px;">
                                {{ str_replace('_',' ',$item->preferred_type) }}
                            </span>
                        </td>

                        <td>
                            @if($item->patch ?? null)
                                <span style="font-size:12px;color:#1A2A4A;font-weight:500;">{{ $item->patch->name }}</span>
                            @else
                                <span style="font-size:11px;color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        <td>
                            @if($item->preferred_start_date)
                                <span style="font-size:12px;color:#1A2A4A;font-weight:500;">
                                    {{ \Carbon\Carbon::parse($item->preferred_start_date)->format('d M Y') }}
                                </span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        <td>
                            @php
                                $dayLabel = match($item->preferred_days ?? '') {
                                    'sat_tue' => 'Sat · Tue',
                                    'sun_wed' => 'Sun · Wed',
                                    'mon_thu' => 'Mon · Thu',
                                    default   => null,
                                };
                            @endphp
                            @if($dayLabel)
                                <span style="display:inline-block;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;background:rgba(245,145,30,0.08);color:#C47010;white-space:nowrap;">{{ $dayLabel }}</span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        <td>
                            <span class="status-badge {{ $statusClass }}">{{ $item->status }}</span>
                        </td>

                        <td>
                            @if($item->notes)
                                <button type="button"
                                        class="notes-pill"
                                        onclick="openNotes(this)"
                                        data-note="{{ e($item->notes) }}"
                                        data-student="{{ e($item->enrollment->student->full_name ?? 'Student') }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    <span class="notes-pill-text">{{ \Illuminate\Support\Str::limit($item->notes, 28) }}</span>
                                </button>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="action-group">
                                @if($item->status !== 'Assigned' && $item->status !== 'Cancelled')
                                <button class="btn-action btn-assign"
                                        onclick="openAssignModal({{ $item->waiting_id }}, '{{ $item->enrollment->enrollment_type ?? '' }}', '{{ $item->enrollment->delivery_mood ?? '' }}')">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    Assign
                                </button>
                                @endif

                                @if($item->status !== 'Cancelled' && $item->status !== 'Assigned')

                                @endif

                                @if($item->status === 'Assigned')
                                    <span style="font-size:10px;color:#15803D;font-weight:600;letter-spacing:0.3px;">✓ Assigned</span>
                                @elseif($item->status === 'Cancelled')
                                    <span style="font-size:10px;color:#DC2626;font-weight:600;letter-spacing:0.3px;">Cancelled</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <div class="empty-title">No Students Waiting</div>
                                <div class="empty-sub">The waiting list is currently empty</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($waiting, 'hasPages') && $waiting->hasPages())
    <div class="pagination-wrap">{{ $waiting->links() }}</div>
    @endif

</div>

<style>
    .notes-pill {
        display:inline-flex; align-items:center; gap:6px; max-width:200px;
        padding:5px 11px; border-radius:16px; cursor:pointer;
        background:rgba(27,79,168,0.06); border:1px solid rgba(27,79,168,0.15);
        color:#1B4FA8; font-size:11px; font-family:'DM Sans',sans-serif;
        transition:all 0.18s; text-align:left;
    }
    .notes-pill:hover { background:rgba(27,79,168,0.1); border-color:rgba(27,79,168,0.3); }
    .notes-pill-text { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .notes-overlay {
        display:none; position:fixed; inset:0; z-index:1000;
        background:rgba(10,20,40,0.5); backdrop-filter:blur(3px);
        align-items:center; justify-content:center; padding:20px;
    }
    .notes-overlay.open { display:flex; animation:notesFade 0.2s ease both; }
    @keyframes notesFade { from{opacity:0} to{opacity:1} }

    .notes-modal {
        background:#fff; border-radius:14px; max-width:520px; width:100%;
        max-height:80vh; display:flex; flex-direction:column; overflow:hidden;
        box-shadow:0 20px 60px rgba(15,31,61,0.3); animation:notesPop 0.25s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes notesPop { from{opacity:0;transform:scale(0.96) translateY(10px)} to{opacity:1;transform:none} }

    .notes-modal-head {
        background:linear-gradient(135deg,#0F1F3D,#243B69); padding:18px 22px;
        display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .notes-modal-head-title { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:2px; color:#fff; }
    .notes-modal-head-sub { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:#F5911E; margin-top:2px; }
    .notes-modal-close {
        background:rgba(255,255,255,0.1); border:none; width:30px; height:30px; border-radius:8px;
        color:#fff; cursor:pointer; font-size:18px; line-height:1; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; transition:background 0.2s;
    }
    .notes-modal-close:hover { background:rgba(255,255,255,0.2); }
    .notes-modal-body {
        padding:22px; overflow-y:auto; font-size:14px; line-height:1.65; color:#1A2A4A;
        white-space:pre-wrap; word-break:break-word;
    }
</style>

<div class="notes-overlay" id="notesOverlay" onclick="if(event.target===this)closeNotes()">
    <div class="notes-modal">
        <div class="notes-modal-head">
            <div>
                <div class="notes-modal-head-sub">Waiting List Note</div>
                <div class="notes-modal-head-title" id="notesModalStudent">Note</div>
            </div>
            <button type="button" class="notes-modal-close" onclick="closeNotes()">&times;</button>
        </div>
        <div class="notes-modal-body" id="notesModalBody"></div>
    </div>
</div>

<script>
let activeStatus = '';
let activeDtype  = '';
let activeMode   = '';
let activePtype  = '';
let searchQuery  = '';

function applyFilters() {
    document.querySelectorAll('#wlTableBody tr[data-status]').forEach(row => {
        const matchStatus = !activeStatus || row.dataset.status === activeStatus;
        const matchDtype  = !activeDtype  || row.dataset.dtype  === activeDtype;
        const matchMode   = !activeMode   || row.dataset.mode   === activeMode;
        const matchPtype  = !activePtype  || row.dataset.ptype  === activePtype;
        const name        = row.dataset.name  || '';
        const sid         = row.dataset.sid   || '';
        const phone       = row.dataset.phone || '';
        const days        = row.dataset.days  || '';
        const matchSearch = !searchQuery || name.includes(searchQuery) || sid.includes(searchQuery) || phone.includes(searchQuery) || days.includes(searchQuery);
        row.style.display = (matchStatus && matchDtype && matchMode && matchPtype && matchSearch) ? '' : 'none';
    });
}

function searchWaiting(q) {
    searchQuery = q.toLowerCase().trim();
    applyFilters();
}

function filterByStatus(status) {
    activeStatus = status === 'all' ? '' : status;
    document.getElementById('statusFilter').value = activeStatus;
    document.querySelectorAll('.stat-card[data-filter]').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filter === status);
    });
    applyFilters();
}

function filterByDeliveryType(dtype) {
    activeDtype = dtype;
    document.getElementById('dtypeFilter').value = dtype;
    document.querySelectorAll('.stat-card[data-filter-dtype]').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filterDtype === dtype);
    });
    applyFilters();
}

function filterByMode(mode) {
    activeMode = mode;
    document.getElementById('modeFilter').value = mode;
    document.querySelectorAll('.stat-card[data-filter-mode]').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filterMode === mode);
    });
    applyFilters();
}

function filterByStatusSelect(val)       { activeStatus = val; applyFilters(); }
function filterByDeliveryTypeSelect(val) { activeDtype  = val; applyFilters(); }
function filterByModeSelect(val)         { activeMode   = val; applyFilters(); }
function filterByPrefTypeSelect(val)     { activePtype  = val; applyFilters(); }

function openAssignModal(id, studentType, studentMode) {
    document.getElementById('assign_waiting_id').value = id;

    document.getElementById('assign_instance_hidden').value = '';
    document.querySelectorAll('.assign-instance-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.assign-card-radio').forEach(r => r.checked = false);

    let visibleCount = 0;
    document.querySelectorAll('.assign-instance-card').forEach(card => {
        const courseType = card.dataset.courseType || '';
        const courseMood = card.dataset.deliveryMood || '';
        const typeMismatch = studentType && courseType && courseType !== studentType;
        const moodMismatch = studentMode && courseMood && courseMood !== studentMode;
        if (typeMismatch || moodMismatch) {
            card.style.display = 'none';
        } else {
            card.style.display = '';
            if (!card.classList.contains('is-full')) visibleCount++;
        }
    });

    const banner = document.getElementById('assign_type_banner');
    if (banner) {
        const parts = [];
        if (studentType) parts.push(studentType);
        if (studentMode) parts.push(studentMode);
        banner.textContent = parts.length
            ? `Showing ${parts.join(' · ')} course instances only`
            : 'Showing all course instances';
        banner.style.display = 'block';
    }

    const noMatch = document.getElementById('assign_no_type_match');
    if (noMatch) {
        noMatch.style.display = ((studentType || studentMode) && visibleCount === 0) ? 'block' : 'none';
    }

    document.getElementById('assignModal').style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wl-cancel-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Cancel this student from the waiting list? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});

function openNotes(btn){
    const note    = btn.getAttribute('data-note') || '';
    const student = btn.getAttribute('data-student') || 'Note';
    document.getElementById('notesModalBody').textContent = note;
    document.getElementById('notesModalStudent').textContent = student;
    document.getElementById('notesOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeNotes(){
    document.getElementById('notesOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeNotes(); });
</script>

@endsection