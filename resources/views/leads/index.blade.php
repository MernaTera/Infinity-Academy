@extends('layouts.app')

@section('title', 'My Leads')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    body, .leads-page * { font-family: 'DM Sans', sans-serif; }

    .leads-page {
        background: #F8F6F2;
        min-height: 100vh;
        padding: 36px 32px;
        color: #1A2A4A;
    }

    /* ── PAGE HEADER ── */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-eyebrow {
        font-size: 10px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #F5911E;
        margin-bottom: 4px;
    }

    .page-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 34px;
        letter-spacing: 4px;
        color: #1B4FA8;
        line-height: 1;
    }

    .page-subtitle {
        font-size: 12px;
        color: #7A8A9A;
        margin-top: 4px;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        background: transparent;
        border: 1.5px solid #1B4FA8;
        border-radius: 4px;
        color: #1B4FA8;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 13px;
        letter-spacing: 4px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        transition: color 0.4s;
        white-space: nowrap;
    }

    .btn-add::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }

    .btn-add:hover::before { transform: scaleX(1); }
    .btn-add:hover { color: #fff; text-decoration: none; }
    .btn-add span, .btn-add svg { position: relative; z-index: 1; }

    /* ── STATS ROW ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px;
        padding: 14px 16px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(27,79,168,0.04);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent, #1B4FA8), transparent);
    }

    .stat-label {
        font-size: 9px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #7A8A9A;
        margin-bottom: 6px;
    }

    .stat-value {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 26px;
        letter-spacing: 2px;
        color: var(--accent, #1B4FA8);
        line-height: 1;
    }

    /* ── TABLE CARD ── */
    .table-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
    }

    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-card table {
        width: 100%;
        border-collapse: collapse;
        min-width: 960px;
    }

    .table-card thead tr {
        border-bottom: 1px solid rgba(27,79,168,0.08);
    }

    .table-card thead th {
        padding: 12px 14px;
        font-size: 9px;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: #7A8A9A;
        font-weight: 500;
        white-space: nowrap;
        background: rgba(27,79,168,0.02);
        text-align: left;
    }

    .table-card tbody tr {
        border-bottom: 1px solid rgba(27,79,168,0.04);
        transition: background 0.2s;
    }

    .table-card tbody tr:hover { background: rgba(27,79,168,0.025); }
    .table-card tbody tr:last-child { border-bottom: none; }

    .table-card tbody td {
        padding: 12px 14px;
        font-size: 13px;
        color: #4A5A7A;
        vertical-align: middle;
    }

    .lead-name   { font-weight: 500; color: #1A2A4A; font-size: 13px; }
    .lead-phone  { font-size: 11px; color: #7A8A9A; font-family: monospace; letter-spacing: 0.5px; margin-top: 2px; }
    .lead-loc    { font-size: 10px; color: #9AAABB; margin-top: 2px; }

    .tag {
        display: inline-block;
        font-size: 9px;
        letter-spacing: 1px;
        padding: 2px 8px;
        border-radius: 3px;
        white-space: nowrap;
        text-transform: uppercase;
        font-weight: 500;
        margin-bottom: 3px;
    }

    .tag-course  { background: rgba(27,79,168,0.07);  border: 1px solid rgba(27,79,168,0.15);  color: #1B4FA8; }
    .tag-level   { background: rgba(245,145,30,0.07); border: 1px solid rgba(245,145,30,0.2);  color: #C47010; }
    .tag-sub     { background: rgba(245,145,30,0.04); border: 1px solid rgba(245,145,30,0.1);  color: #C47010; font-size: 8px; }
    .tag-degree  { background: rgba(27,79,168,0.05);  border: 1px solid rgba(27,79,168,0.12);  color: #2D6FDB; }
    .tag-source  { background: rgba(245,145,30,0.05); border: 1px solid rgba(245,145,30,0.15); color: #C47010; }

    /* ── STATUS BADGES ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 9px;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 4px 9px;
        border-radius: 3px;
        white-space: nowrap;
        font-weight: 500;
    }

    .status-badge::before {
        content: '';
        width: 4px; height: 4px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }

    .status-waiting      { color: #7A8A9A; background: rgba(122,138,154,0.08); border: 1px solid rgba(122,138,154,0.2); }
    .status-call_again   { color: #C47010; background: rgba(245,145,30,0.08);  border: 1px solid rgba(245,145,30,0.25); }
    .status-scheduled    { color: #1B6FA8; background: rgba(27,111,168,0.08);  border: 1px solid rgba(27,111,168,0.2); }
    .status-registered   { color: #15803D; background: rgba(21,128,61,0.08);   border: 1px solid rgba(21,128,61,0.2); }
    .status-not_interest { color: #DC2626; background: rgba(220,38,38,0.06);   border: 1px solid rgba(220,38,38,0.2); }
    .status-archived     { color: #9A8A7A; background: rgba(154,138,122,0.08); border: 1px solid rgba(154,138,122,0.2); }
    .status-default      { color: #7A8A9A; background: rgba(122,138,154,0.06); border: 1px solid rgba(122,138,154,0.15); }

    .pref-text    { font-size: 12px; color: #7A8A9A; }
    .call-date    { font-size: 12px; color: #1A2A4A; font-weight: 500; }
    .call-time    { font-size: 10px; color: #7A8A9A; }
    .days-lbl     { font-size: 10px; color: #7A8A9A; letter-spacing: 1px; }

    /* ── ACTIONS ── */
    .action-group { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 11px;
        font-size: 9px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        border-radius: 3px;
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        border: 1px solid;
        background: transparent;
        cursor: pointer;
        transition: all 0.25s;
        white-space: nowrap;
    }

    .btn-edit   { color: #1B4FA8; border-color: rgba(27,79,168,0.25); }
    .btn-edit:hover { background: rgba(27,79,168,0.07); border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    .btn-delete { color: #DC2626; border-color: rgba(220,38,38,0.2); }
    .btn-delete:hover { background: rgba(220,38,38,0.06); border-color: rgba(220,38,38,0.5); color: #DC2626; }

    /* ── EMPTY ── */
    .empty-state { padding: 60px 24px; text-align: center; }
    .empty-state svg { margin: 0 auto 14px; opacity: 0.2; }
    .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 18px; letter-spacing: 4px; color: #7A8A9A; margin-bottom: 6px; }
    .empty-sub   { font-size: 12px; color: #AAB8C8; }

    /* ── PAGINATION ── */
    .pagination-wrap { margin-top: 20px; }
    .pagination-wrap .page-link {
        background: rgba(255,255,255,0.8) !important;
        border: 1px solid rgba(27,79,168,0.12) !important;
        color: #7A8A9A !important;
        font-size: 11px; letter-spacing: 1px;
        border-radius: 4px !important;
        padding: 6px 12px;
        transition: all 0.2s;
    }
    .pagination-wrap .page-link:hover {
        background: rgba(27,79,168,0.06) !important;
        color: #1B4FA8 !important;
        border-color: rgba(27,79,168,0.3) !important;
    }
    .pagination-wrap .page-item.active .page-link {
        background: transparent !important;
        border-color: #1B4FA8 !important;
        color: #1B4FA8 !important;
        font-weight: 600 !important;
    }
    .status-select {
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid rgba(27,79,168,0.2);
        font-size: 11px;
        background: rgba(255,255,255,0.8);
        color: #1A2A4A;
        cursor: pointer;
    }
    .days-num {
        font-family: 'Bebas Neue';
        font-size: 16px;
        color: #1B4FA8;
    }

    .days-num.danger {
        color: #DC2626; /* 🔥 أحمر */
    }

.call-modal {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    backdrop-filter: blur(6px);
    z-index:999;
    align-items:center;
    justify-content:center;
}

.call-box {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(20px);
    padding: 32px;
    border-radius: 12px;
    width: 380px;
    box-shadow: 0 24px 60px rgba(27,79,168,0.15), 0 4px 16px rgba(0,0,0,0.08);
    border: 1px solid rgba(27,79,168,0.1);
    border-top: 2px solid #F5911E;
    animation: fadeIn 0.3s ease;
}

.call-header {
    font-family: 'Bebas Neue';
    letter-spacing: 4px;
    font-size: 20px;
    color: #1B4FA8;
    margin-bottom: 6px;
}

/* ضيفي ده تحت الـ header */
.call-subtext {
    font-size: 11px;
    color: #AAB8C8;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

.call-label {
    font-size: 9px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #7A8A9A;
    margin-bottom: 7px;
    display: block;
}

.call-input {
    width: 100%;
    padding: 11px 13px;
    border: 1px solid rgba(27,79,168,0.15);
    border-radius: 6px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: #1A2A4A;
    background: rgba(248,246,242,0.8);
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
    color-scheme: light;
}

.call-input:focus {
    border-color: #1B4FA8;
    box-shadow: 0 0 0 3px rgba(27,79,168,0.08);
}

.call-actions {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.btn-cancel {
    padding: 9px 20px;
    background: transparent;
    border: 1px solid rgba(27,79,168,0.15);
    border-radius: 6px;
    color: #7A8A9A;
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: all 0.2s;
}

.btn-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; }

.btn-save {
    padding: 9px 24px;
    background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 14px;
    letter-spacing: 4px;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-save:hover { opacity: 0.9; }

@keyframes fadeIn {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}

    /* ── MOBILE ── */
    @media (max-width: 768px) {
        .leads-page { padding: 20px 14px; }
        .page-title { font-size: 26px; letter-spacing: 2px; }
        .stats-row  { grid-template-columns: 1fr 1fr; gap: 10px; }
        .stat-value { font-size: 22px; }
        .btn-add    { padding: 9px 16px; font-size: 12px; letter-spacing: 3px; }
    }

    @media (max-width: 480px) {
        .page-header { flex-direction: column; align-items: flex-start; }
        .stats-row   { grid-template-columns: 1fr 1fr; }
    }
</style>
<script>
let currentLeadId = null;
let currentSelect = null;

function updateStatus(select, leadId) {

    if (select.value === 'Call_Again') {
        currentLeadId = leadId;
        currentSelect = select;

        document.getElementById('callModal').style.display = 'flex';
        return;
    }

    sendUpdate(select, leadId, { status: select.value });
}
function sendUpdate(select, leadId, data) {

    fetch(`/leads/${leadId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _method: 'PUT',
            ...data
        })
    })
    .then(res => res.json())
    .then(() => {
        select.style.borderColor = "#22C55E";

        setTimeout(() => location.reload(), 500);
    })
    .catch(err => console.error(err));
}
function closeModal() {
    document.getElementById('callModal').style.display = 'none';
}

function confirmCall() {
    let input = document.getElementById('callDate').value;

    if (!input) {
        alert("Please select date & time");
        return;
    }

    let formatted = input;

    sendUpdate(currentSelect, currentLeadId, {
        status: 'Call_Again',
        next_call_at: formatted
    });

    closeModal();
}
</script>

<div class="leads-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Leads</div>
            <h1 class="page-title">My Follow-Up Leads</h1>
            <p class="page-subtitle">Track and manage your active leads pipeline</p>
        </div>
        <a href="{{ route('leads.create') }}" class="btn-add">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span>Add Lead</span>
        </a>
    </div>

    {{-- ── STATS ── --}}
    <div class="stats-row">
        @php
            $total         = $leads->total();
            $registered    = $leads->getCollection()->where('status','Registered')->count();
            $callAgain     = $leads->getCollection()->where('status','Call_Again')->count();
            $scheduled     = $leads->getCollection()->where('status','Scheduled_Call')->count();
            $waiting       = $leads->getCollection()->where('status','Waiting')->count();
            $notInterested = $leads->getCollection()->where('status','Not_Interested')->count();
            $archived      = $leads->getcollection()->where('status','Archived')->count();
        @endphp

        <div class="stat-card" style="--accent:#1B4FA8"><div class="stat-label">Total</div><div class="stat-value">{{ $total }}</div></div>
        <div class="stat-card" style="--accent:#15803D"><div class="stat-label">Registered</div><div class="stat-value">{{ $registered }}</div></div>
        <div class="stat-card" style="--accent:#C47010"><div class="stat-label">Call Again</div><div class="stat-value">{{ $callAgain }}</div></div>
        <div class="stat-card" style="--accent:#7A8A9A"><div class="stat-label">Waiting</div><div class="stat-value">{{ $waiting }}</div></div>
        <div class="stat-card" style="--accent:#DC2626"><div class="stat-label">Not Interested</div><div class="stat-value">{{ $notInterested }}</div></div>
        <div class="stat-card" style="--accent:#9A8A7A"><div class="stat-label">Archived</div><div class="stat-value">{{ $archived }}</div></div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Name & Contact</th>
                        <th>Source</th>
                        <th>Degree</th>
                        <th>Course & Level</th>
                        <th>Status</th>
                        <th>Start Pref.</th>
                        <th>Next Call</th>
                        <th>Lead Age</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        {{-- Name & Contact --}}
                        <td>
                            <div class="lead-name">{{ $lead->full_name }}</div>
                            <div class="lead-phone">{{ $lead->phone }}</div>
                            @if($lead->location)
                                <div class="lead-loc">📍 {{ $lead->location }}</div>
                            @endif
                        </td>

                        {{-- Source --}}
                        <td>
                            <span class="tag tag-source">{{ str_replace('_',' ',$lead->source) }}</span><br>
                        </td>

                        {{-- Degree --}}
                        <td>
                            <span class="tag tag-degree">{{ $lead->degree }}</span>
                        </td>

                        {{-- Course & Level --}}
                        <td>
                            @if($lead->courseTemplate)
                                <span class="tag tag-course">{{ $lead->courseTemplate->name }}</span>
                            @else
                                <span style="color:#AAB8C8;font-size:11px;">—</span>
                            @endif
                            @if($lead->level)
                                <br><span class="tag tag-level">{{ $lead->level->name ?? '' }}</span>
                            @endif
                            @if($lead->sublevel)
                                <br><span class="tag tag-sub">{{ $lead->sublevel->name ?? '' }}</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <select class="status-select"
                                    onchange="updateStatus(this, {{ $lead->lead_id }})">
                                @foreach(['Waiting','Call_Again','Registered','Not_Interested','Archived'] as $status)
                                    <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>
                                    <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>
                                        {{ str_replace('_',' ',$status) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- Start Preference --}}
                        <td>
                            <span class="pref-text">{{ $lead->start_preference_type ?? '—' }}</span>
                        </td>

                        {{-- Next Call --}}
                        <td>
                            @if($lead->next_call_at)
                                <div class="call-date">{{ $lead->next_call_at->format('d M Y') }}</div>
                                <div class="call-time">{{ $lead->next_call_at->format('H:i') }}</div>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Age --}}
                        @php
                            $totalHours = abs($lead->created_at->diffInHours(now()));
                            $days  = intval($totalHours / 24);
                            $hours = $totalHours % 24;
                        @endphp

                        <td>
                            <div class="days-num">{{ $days }} days</div>
                            <div class="days-lbl">{{ $hours }} h</div>
                        </td>

                        {{-- Notes --}}
                        <td>
                            @if($lead->notes)
                                <span style="font-size:11px;color:#4A5A7A;max-width:150px;display:block;
                                            overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                    title="{{ $lead->notes }}">
                                    {{ $lead->notes }}
                                </span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="action-group">
                                <a href="{{ route('leads.edit', $lead->lead_id) }}" class="btn-action btn-edit">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST"
                                      style="display:inline;" onsubmit="return confirm('Delete this lead?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
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

    @if($leads->hasPages())
    <div class="pagination-wrap">
        {{ $leads->links() }}
    </div>
    @endif
    <div id="callModal" class="call-modal">
        <div class="call-box">
            <div class="call-header">Schedule Next Call</div>
            <div class="call-subtext">Select date & time for the follow-up call</div>

            <label class="call-label">Date & Time</label>
            <input type="datetime-local" id="callDate" class="call-input">

            <div class="call-actions">
                <button onclick="closeModal()" class="btn-cancel">Cancel</button>
                <button onclick="confirmCall()" class="btn-save">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection