@extends('layouts.app')

@section('title', 'My Leads')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    body, .leads-page * { font-family: 'DM Sans', sans-serif; }

    .leads-page {
        background: #060606;
        min-height: 100vh;
        padding: 40px 32px;
        color: #F0EDE6;
    }

    /* ── PAGE HEADER ── */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 36px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(201,168,76,0.1);
    }

    .page-header-left {}

    .page-eyebrow {
        font-size: 10px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #C9A84C;
        margin-bottom: 6px;
    }

    .page-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 36px;
        letter-spacing: 4px;
        color: #F0EDE6;
        line-height: 1;
    }

    .page-subtitle {
        font-size: 12px;
        color: #5A5550;
        margin-top: 4px;
        letter-spacing: 0.3px;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 28px;
        background: transparent;
        border: 1px solid #C9A84C;
        border-radius: 2px;
        color: #C9A84C;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 13px;
        letter-spacing: 4px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        transition: color 0.4s;
    }

    .btn-add::before {
        content: '';
        position: absolute;
        inset: 0;
        background: #C9A84C;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }

    .btn-add:hover::before { transform: scaleX(1); }
    .btn-add:hover { color: #060606; text-decoration: none; }
    .btn-add span, .btn-add svg { position: relative; z-index: 1; }

    /* ── STATS ROW ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #0F0F0F;
        border: 1px solid rgba(201,168,76,0.1);
        border-radius: 2px;
        padding: 18px 20px;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--accent, #C9A84C), transparent);
    }

    .stat-label {
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #5A5550;
        margin-bottom: 8px;
    }

    .stat-value {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 28px;
        letter-spacing: 2px;
        color: var(--accent, #C9A84C);
        line-height: 1;
    }

    /* ── TABLE CARD ── */
    .table-card {
        background: #0F0F0F;
        border: 1px solid rgba(201,168,76,0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .table-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-card thead tr {
        border-bottom: 1px solid rgba(201,168,76,0.12);
    }

    .table-card thead th {
        padding: 14px 18px;
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #5A5550;
        font-weight: 500;
        white-space: nowrap;
        background: rgba(201,168,76,0.02);
    }

    .table-card tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.03);
        transition: background 0.2s;
    }

    .table-card tbody tr:hover {
        background: rgba(201,168,76,0.03);
    }

    .table-card tbody tr:last-child { border-bottom: none; }

    .table-card tbody td {
        padding: 14px 18px;
        font-size: 13px;
        color: #9A9590;
        vertical-align: middle;
    }

    .lead-name {
        font-weight: 500;
        color: #F0EDE6;
        font-size: 14px;
    }

    .lead-phone {
        font-size: 12px;
        color: #6B6560;
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    .course-tag {
        display: inline-block;
        font-size: 10px;
        letter-spacing: 1px;
        padding: 3px 10px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 2px;
        color: #8A8580;
        white-space: nowrap;
    }

    /* ── STATUS BADGES ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 2px;
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

    .status-waiting      { color: #8A8580; background: rgba(138,133,128,0.1); border: 1px solid rgba(138,133,128,0.2); }
    .status-call_again   { color: #E8C97A; background: rgba(232,201,122,0.08); border: 1px solid rgba(232,201,122,0.2); }
    .status-scheduled    { color: #67C6E3; background: rgba(103,198,227,0.08); border: 1px solid rgba(103,198,227,0.2); }
    .status-registered   { color: #4ADE80; background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.2); }
    .status-not_interest { color: #F87171; background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.2); }
    .status-default      { color: #6B6560; background: rgba(107,101,96,0.1);  border: 1px solid rgba(107,101,96,0.15); }

    .next-call {
        font-size: 11px;
        color: #6B6560;
        letter-spacing: 0.3px;
    }

    .days-badge {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 16px;
        letter-spacing: 1px;
        color: #C9A84C;
    }

    /* ── ACTION BUTTONS ── */
    .action-group { display: flex; gap: 8px; align-items: center; }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        font-size: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
        border-radius: 2px;
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        border: 1px solid;
        background: transparent;
        cursor: pointer;
        transition: all 0.25s;
    }

    .btn-edit {
        color: #C9A84C;
        border-color: rgba(201,168,76,0.3);
    }
    .btn-edit:hover {
        background: rgba(201,168,76,0.08);
        border-color: #C9A84C;
        color: #C9A84C;
        text-decoration: none;
    }

    .btn-delete {
        color: #F87171;
        border-color: rgba(248,113,113,0.2);
    }
    .btn-delete:hover {
        background: rgba(248,113,113,0.07);
        border-color: rgba(248,113,113,0.5);
        color: #F87171;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        padding: 64px 24px;
        text-align: center;
    }

    .empty-state svg { margin: 0 auto 16px; opacity: 0.2; }
    .empty-icon { color: #C9A84C; }
    .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 20px; letter-spacing: 4px; color: #5A5550; margin-bottom: 6px; }
    .empty-sub   { font-size: 12px; color: #3A3530; letter-spacing: 0.5px; }

    /* ── PAGINATION ── */
    .pagination-wrap { margin-top: 24px; }
    .pagination-wrap .pagination { gap: 4px; }
    .pagination-wrap .page-link {
        background: #0F0F0F !important;
        border: 1px solid rgba(201,168,76,0.15) !important;
        color: #5A5550 !important;
        font-size: 11px;
        letter-spacing: 1px;
        border-radius: 2px !important;
        padding: 6px 12px;
        transition: all 0.2s;
    }
    .pagination-wrap .page-link:hover {
        background: rgba(201,168,76,0.08) !important;
        color: #C9A84C !important;
        border-color: rgba(201,168,76,0.4) !important;
    }
    .pagination-wrap .page-item.active .page-link {
        background: transparent !important;
        border-color: #C9A84C !important;
        color: #C9A84C !important;
    }
</style>

<div class="leads-page">

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-eyebrow">CRM Module</div>
            <h1 class="page-title">My Follow-Up Leads</h1>
            <p class="page-subtitle">Track and manage your active sales pipeline</p>
        </div>

        <a href="{{ route('leads.create') }}" class="btn-add">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span>Add Lead</span>
        </a>
    </div>

    {{-- ── STATS ── --}}
    <div class="stats-row">
        @php
            $total      = $leads->total();
            $registered = $leads->getCollection()->where('status','Registered')->count();
            $callAgain  = $leads->getCollection()->where('status','Call_Again')->count();
            $scheduled  = $leads->getCollection()->where('status','Scheduled_Call')->count();
        @endphp

        <div class="stat-card" style="--accent:#C9A84C">
            <div class="stat-label">Total Leads</div>
            <div class="stat-value">{{ $total }}</div>
        </div>
        <div class="stat-card" style="--accent:#4ADE80">
            <div class="stat-label">Registered</div>
            <div class="stat-value">{{ $registered }}</div>
        </div>
        <div class="stat-card" style="--accent:#E8C97A">
            <div class="stat-label">Call Again</div>
            <div class="stat-value">{{ $callAgain }}</div>
        </div>
        <div class="stat-card" style="--accent:#67C6E3">
            <div class="stat-label">Scheduled</div>
            <div class="stat-value">{{ $scheduled }}</div>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Next Call</th>
                    <th>Days</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td>
                        <div class="lead-name">{{ $lead->full_name }}</div>
                    </td>

                    <td>
                        <span class="lead-phone">{{ $lead->phone }}</span>
                    </td>

                    <td>
                        @if($lead->courseTemplate)
                            <span class="course-tag">{{ $lead->courseTemplate->course_name }}</span>
                        @else
                            <span style="color:#3A3530;">—</span>
                        @endif
                    </td>

                    <td>
                        @php
                            $statusClass = match($lead->status) {
                                'Waiting'          => 'status-waiting',
                                'Call_Again'       => 'status-call_again',
                                'Scheduled_Call'   => 'status-scheduled',
                                'Registered'       => 'status-registered',
                                'Not_Interested'   => 'status-not_interest',
                                default            => 'status-default',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ str_replace('_', ' ', $lead->status) }}
                        </span>
                    </td>

                    <td>
                        @if($lead->next_call_at)
                            <span class="next-call">{{ $lead->next_call_at->format('d M Y') }}</span>
                            <br>
                            <span style="font-size:10px;color:#3A3530;">{{ $lead->next_call_at->format('H:i') }}</span>
                        @else
                            <span style="color:#3A3530;">—</span>
                        @endif
                    </td>

                    <td>
                        <span class="days-badge">{{ $lead->created_at->diffInDays(now()) }}</span>
                        <span style="font-size:10px;color:#3A3530;margin-left:2px;">d</span>
                    </td>

                    <td>
                        <div class="action-group">
                            <a href="{{ route('leads.edit', $lead->lead_id) }}" class="btn-action btn-edit">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>

                            <form action="{{ route('leads.destroy', $lead->lead_id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this lead?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="1">
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

    {{-- ── PAGINATION ── --}}
    @if($leads->hasPages())
    <div class="pagination-wrap">
        {{ $leads->links() }}
    </div>
    @endif

</div>

@endsection