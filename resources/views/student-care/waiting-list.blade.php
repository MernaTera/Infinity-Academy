@extends('student-care.layouts.app')

@section('title', 'Waiting List')

@include('student-care.waiting-list.partials.assign-modal')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    body, .wl-page * { font-family: 'DM Sans', sans-serif; }
    body { min-width: fit-content; }

    .wl-page {

        min-height: 100vh;

        color: #1A2A4A;
        overflow-: hidden;
    }

    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 28px; padding-bottom: 20px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap; gap: 16px;
    }
    .page-eyebrow  { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title    { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; }
    .page-subtitle { font-size: 12px; color: #7A8A9A; margin-top: 4px; }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px; margin-bottom: 22px;
    }
    .stat-card {
        background: rgba(255,255,255,0.75); backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.1); border-radius: 6px;
        padding: 14px 16px; position: relative; overflow: hidden;
        box-shadow: 0 2px 10px rgba(27,79,168,0.04);
        cursor: pointer; transition: all 0.25s;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent, #1B4FA8), transparent);
    }
    .stat-card:hover         { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(27,79,168,0.1); }
    .stat-card.active-filter { box-shadow: 0 0 0 2px var(--accent); transform: translateY(-2px); }
    .stat-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: #7A8A9A; margin-bottom: 6px; }
    .stat-value { font-family: 'Bebas Neue', sans-serif; font-size: 26px; letter-spacing: 2px; color: var(--accent, #1B4FA8); line-height: 1; }

    .toolbar {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 16px; flex-wrap: wrap;
    }
    .search-wrap { position: relative; flex: 1; min-width: 220px; max-width: 360px; }
    .search-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; }
    .search-input {
        width: 100%; padding: 10px 14px 10px 40px;
        background: rgba(255,255,255,0.8);
        border: 1px solid rgba(27,79,168,0.12); border-radius: 6px;
        font-family: 'DM Sans', sans-serif; font-size: 13px;
        color: #1A2A4A; outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .search-input:focus { border-color: #1B4FA8; box-shadow: 0 0 0 3px rgba(27,79,168,0.08); }

    .filter-select {
        padding: 9px 32px 9px 12px;
        background: rgba(255,255,255,0.8);
        border: 1px solid rgba(27,79,168,0.12); border-radius: 6px;
        font-family: 'DM Sans', sans-serif; font-size: 12px;
        color: #4A5A7A; outline: none; cursor: pointer;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center;
        background-color: rgba(255,255,255,0.8);
        transition: border-color 0.3s;
    }
    .filter-select:focus { border-color: #1B4FA8; }

    .table-card {
        min-height: 400px; background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px); border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px; overflow: visible;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
    }
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-card table { width: 100%; border-collapse: collapse; min-width: 960px; }
    .table-card thead tr { border-bottom: 1px solid rgba(27,79,168,0.08); }
    .table-card thead th {
        padding: 12px 14px; font-size: 9px; letter-spacing: 2.5px;
        text-transform: uppercase; color: #7A8A9A; font-weight: 500;
        white-space: nowrap; background: rgba(27,79,168,0.02); text-align: left;
    }
    .table-card tbody tr { border-bottom: 1px solid rgba(27,79,168,0.04); transition: background 0.2s; }
    .table-card tbody tr:hover { background: rgba(27,79,168,0.025); }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody td { padding: 12px 14px; font-size: 13px; color: #4A5A7A; vertical-align: middle; }

    .lead-name  { font-weight: 500; color: #1A2A4A; font-size: 13px; }
    .lead-sub   { font-size: 11px; color: #7A8A9A; margin-top: 2px; }

    .tag {
        display: inline-block; font-size: 9px; letter-spacing: 1px;
        padding: 2px 8px; border-radius: 3px; white-space: nowrap;
        text-transform: uppercase; font-weight: 500; margin-bottom: 3px;
    }
    .tag-course  { background: rgba(27,79,168,0.07);  border: 1px solid rgba(27,79,168,0.15);  color: #1B4FA8; }
    .tag-level   { background: rgba(245,145,30,0.07); border: 1px solid rgba(245,145,30,0.2);  color: #C47010; }
    .tag-group   { background: rgba(27,79,168,0.05);  border: 1px solid rgba(27,79,168,0.12);  color: #2D6FDB; }
    .tag-private { background: rgba(245,145,30,0.05); border: 1px solid rgba(245,145,30,0.15); color: #C47010; }
    .tag-online  { background: rgba(21,128,61,0.05);  border: 1px solid rgba(21,128,61,0.15);  color: #15803D; }
    .tag-offline { background: rgba(122,138,154,0.06);border: 1px solid rgba(122,138,154,0.15);color: #7A8A9A; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 9px; letter-spacing: 1.2px; text-transform: uppercase;
        padding: 4px 9px; border-radius: 3px; white-space: nowrap; font-weight: 500;
    }
    .status-badge::before {
        content: ''; width: 4px; height: 4px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .status-active    { color: #C47010; background: rgba(245,145,30,0.08); border: 1px solid rgba(245,145,30,0.25); }
    .status-assigned  { color: #15803D; background: rgba(21,128,61,0.08);  border: 1px solid rgba(21,128,61,0.2); }
    .status-cancelled { color: #DC2626; background: rgba(220,38,38,0.06);  border: 1px solid rgba(220,38,38,0.2); }
    .status-wl-default{ color: #7A8A9A; background: rgba(122,138,154,0.08);border: 1px solid rgba(122,138,154,0.2); }

    .action-group { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
    .btn-action {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 11px; font-size: 9px; letter-spacing: 1.5px;
        text-transform: uppercase; border-radius: 3px;
        font-family: 'DM Sans', sans-serif; font-weight: 500;
        border: 1px solid; background: transparent; cursor: pointer;
        transition: all 0.25s; white-space: nowrap;
    }
    .btn-assign    { color: #1B4FA8; border-color: rgba(27,79,168,0.25); }
    .btn-assign:hover { background: rgba(27,79,168,0.07); border-color: #1B4FA8; }
    .btn-cancel-wl { color: #DC2626; border-color: rgba(220,38,38,0.2); }
    .btn-cancel-wl:hover { background: rgba(220,38,38,0.06); border-color: rgba(220,38,38,0.5); }

    .empty-state { padding: 60px 24px; text-align: center; }
    .empty-state svg { margin: 0 auto 14px; opacity: 0.2; }
    .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 18px; letter-spacing: 4px; color: #7A8A9A; margin-bottom: 6px; }
    .empty-sub   { font-size: 12px; color: #AAB8C8; }

    .pagination-wrap { margin-top: 20px; }
    .pagination-wrap .page-link {
        background: rgba(255,255,255,0.8) !important;
        border: 1px solid rgba(27,79,168,0.12) !important;
        color: #7A8A9A !important; font-size: 11px; letter-spacing: 1px;
        border-radius: 4px !important; padding: 6px 12px; transition: all 0.2s;
    }
    .pagination-wrap .page-link:hover {
        background: rgba(27,79,168,0.06) !important;
        color: #1B4FA8 !important; border-color: rgba(27,79,168,0.3) !important;
    }
    .pagination-wrap .page-item.active .page-link {
        background: transparent !important; border-color: #1B4FA8 !important;
        color: #1B4FA8 !important; font-weight: 600 !important;
    }

    @media (max-width: 768px) { .wl-page { padding: 20px 14px; } }
    @media (max-width: 480px) { .page-header { flex-direction: column; align-items: flex-start; } }
</style>

<div class="wl-page">

    {{-- ── HEADER ── --}}
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

    {{-- ── STATS ── --}}
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
        <div class="stat-card" style="--accent:#DC2626;" onclick="filterByStatus('Cancelled')" data-filter="Cancelled">
            <div class="stat-label">Cancelled</div>
            <div class="stat-value">{{ $countCancelled }}</div>
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

    {{-- ── TOOLBAR ── --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="wlSearch" class="search-input"
                   placeholder="Search by student name or ID..."
                   oninput="searchWaiting(this.value)">
        </div>

        <select class="filter-select" id="statusFilter" onchange="filterByStatusSelect(this.value)">
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Assigned">Assigned</option>
            <option value="Cancelled">Cancelled</option>
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

    {{-- ── TABLE ── --}}
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
                    @endphp
                    <tr data-status="{{ $item->status }}"
                        data-dtype="{{ $item->preferred_delivery_type }}"
                        data-mode="{{ $item->preferred_delivery_mood }}"
                        data-ptype="{{ $item->preferred_type }}"
                        data-name="{{ strtolower($item->enrollment->student->full_name ?? '') }}"
                        data-sid="{{ $item->enrollment->student_id ?? '' }}">

                        {{-- Student --}}
                        <td>
                            <div class="lead-name">{{ $item->enrollment->student->full_name ?? '—' }}</div>
                            <div class="lead-sub">ID: {{ $item->enrollment->student_id ?? '—' }}</div>
                        </td>

                        {{-- Course & Level --}}
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

                        {{-- Delivery Type --}}
                        <td>
                            <span class="tag {{ $dtypeClass }}">{{ $item->preferred_delivery_type }}</span>
                        </td>

                        {{-- Mode --}}
                        <td>
                            <span class="tag {{ $modeClass }}">{{ $item->preferred_delivery_mood }}</span>
                        </td>

                        {{-- Patch Preference --}}
                        <td>
                            <span style="font-size:11px;color:#4A5A7A;letter-spacing:0.5px;">
                                {{ str_replace('_',' ',$item->preferred_type) }}
                            </span>
                        </td>

                        {{-- Requested Patch --}}
                        <td>
                            @if($item->patch ?? null)
                                <span style="font-size:12px;color:#1A2A4A;font-weight:500;">{{ $item->patch->name }}</span>
                            @else
                                <span style="font-size:11px;color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Preferred Date --}}
                        <td>
                            @if($item->preferred_start_date)
                                <span style="font-size:12px;color:#1A2A4A;font-weight:500;">
                                    {{ \Carbon\Carbon::parse($item->preferred_start_date)->format('d M Y') }}
                                </span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-badge {{ $statusClass }}">{{ $item->status }}</span>
                        </td>

                        {{-- Notes --}}
                        <td>
                            @if($item->notes)
                                <span style="font-size:11px;color:#4A5A7A;max-width:120px;display:block;
                                             overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                      title="{{ $item->notes }}">
                                    {{ $item->notes }}
                                </span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="action-group">
                                @if($item->status !== 'Assigned')
                                <button class="btn-action btn-assign"
                                        onclick="openAssignModal({{ $item->waiting_id }})">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    Assign
                                </button>
                                @endif

                                @if($item->status !== 'Cancelled')
                                <button class="btn-action btn-cancel-wl"
                                        onclick="cancelWaiting({{ $item->waiting_id }})">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    Cancel
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
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
        const name        = row.dataset.name || '';
        const sid         = row.dataset.sid  || '';
        const matchSearch = !searchQuery || name.includes(searchQuery) || sid.includes(searchQuery);
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

// function assignStudent(id) {
//     if (confirm('Assign this student?')) {
//         fetch(`/student-care/waiting-list/${id}/assign`, {
//             method: 'POST',
//             headers: {
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
//                 'Accept': 'application/json'
//             }
//         }).then(res => { if (res.ok) location.reload(); });
//     }
// }
function openAssignModal(id) {
    document.getElementById('assign_waiting_id').value = id;
    document.getElementById('assignModal').style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}

function cancelWaiting(id) {
    if (confirm('Cancel this student from the waiting list?')) {
        fetch(`/student-care/waiting-list/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(res => { if (res.ok) location.reload(); });
    }
}
</script>

@endsection