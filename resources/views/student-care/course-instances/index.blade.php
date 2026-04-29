@extends('student-care.layouts.app')

@section('title', 'Course Instances')

@include('student-care.course-instances.partials.schedule-modal')

@include('student-care.course-instances.partials.create-modal')


@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    body, .ci-page * { font-family: 'DM Sans', sans-serif; }
    body { min-width: fit-content; }

    .ci-page {
        background: #F8F6F2;
        min-height: 100vh;
        padding: 36px 32px;
        color: #1A2A4A;
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

    /* ── STATS ── */
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

    /* ── TOOLBAR ── */
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

    /* ── TABLE ── */
    .table-card {
        min-height: 400px; background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px); border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px; overflow: visible;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
    }
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-card table { width: 100%; border-collapse: collapse; min-width: 1100px; }
    .table-card thead tr { border-bottom: 1px solid rgba(27,79,168,0.08); }
    .table-card thead th {
        padding: 12px 14px; font-size: 9px; letter-spacing: 2.5px;
        text-transform: uppercase; color: #7A8A9A; font-weight: 500;
        white-space: nowrap; background: rgba(27,79,168,0.02); text-align: left;
    }
    .table-card tbody tr {
        border-bottom: 1px solid rgba(27,79,168,0.04);
        transition: background 0.2s, opacity 0.3s, transform 0.3s;
        animation: rowFadeIn 0.35s ease both;
    }
    .table-card tbody tr:hover { background: rgba(27,79,168,0.025); }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody td { padding: 12px 14px; font-size: 13px; color: #4A5A7A; vertical-align: middle; }

    @keyframes rowFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .ci-name   { font-weight: 500; color: #1A2A4A; font-size: 13px; }
    .ci-sub    { font-size: 11px; color: #7A8A9A; margin-top: 2px; }

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

    /* ── STATUS BADGES ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 9px; letter-spacing: 1.2px; text-transform: uppercase;
        padding: 4px 9px; border-radius: 3px; white-space: nowrap; font-weight: 500;
    }
    .status-badge::before {
        content: ''; width: 4px; height: 4px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .status-upcoming  { color: #1B6FA8; background: rgba(27,111,168,0.08); border: 1px solid rgba(27,111,168,0.2); }
    .status-active-ci { color: #15803D; background: rgba(21,128,61,0.08);  border: 1px solid rgba(21,128,61,0.2); }
    .status-completed { color: #7A8A9A; background: rgba(122,138,154,0.08);border: 1px solid rgba(122,138,154,0.2); }
    .status-cancelled { color: #DC2626; background: rgba(220,38,38,0.06);  border: 1px solid rgba(220,38,38,0.2); }

    /* ── CAPACITY BAR ── */
    .cap-wrap { display: flex; align-items: center; gap: 8px; }
    .cap-text  { font-size: 12px; color: #1A2A4A; font-weight: 500; white-space: nowrap; }
    .cap-track { flex: 1; min-width: 60px; height: 5px; background: rgba(27,79,168,0.08); border-radius: 3px; overflow: hidden; }
    .cap-fill  { height: 100%; border-radius: 3px; transition: width 0.6s cubic-bezier(0.16,1,0.3,1); }
    .cap-full  { color: #DC2626; font-size: 9px; letter-spacing: 1px; text-transform: uppercase; margin-top: 3px; }

    /* ── DATES ── */
    .date-main { font-size: 12px; color: #1A2A4A; font-weight: 500; }
    .date-sub  { font-size: 10px; color: #AAB8C8; margin-top: 2px; }

    /* ── HOURS ── */
    .hours-val { font-family: 'Bebas Neue', sans-serif; font-size: 18px; color: #1B4FA8; letter-spacing: 1px; }
    .hours-sub { font-size: 10px; color: #AAB8C8; letter-spacing: 1px; }

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
    .btn-action {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 11px; font-size: 9px; letter-spacing: 1.5px;
        text-transform: uppercase; border-radius: 3px;
        font-family: 'DM Sans', sans-serif; font-weight: 500;
        border: 1px solid; background: transparent; cursor: pointer;
        transition: all 0.25s; white-space: nowrap;
    }

    @media (max-width: 768px) { .ci-page { padding: 20px 14px; } }
    @media (max-width: 480px) { .page-header { flex-direction: column; align-items: flex-start; } }
</style>

<div class="ci-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care</div>
            <h1 class="page-title">Course Instances</h1>
            <p class="page-subtitle">All active and upcoming course groups</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;padding:10px 18px;
                    background:rgba(255,255,255,0.7);border:1px solid rgba(27,79,168,0.1);
                    border-radius:6px;box-shadow:0 2px 8px rgba(27,79,168,0.04);">
            <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;">Total</span>
            <span style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:2px;line-height:1;">
                {{ $instances->total() }}
            </span>
        </div>
        <a href="{{ route('student-care.instances.create') }}" class="btn-primary" style="padding:10px 16px;background:#1B4FA8;color:#fff;border:none;border-radius:6px;">
            + New Course
        </a>

    </div>

    {{-- ── STATS ── --}}
    @php
        $col = $instances->getCollection();
        $countUpcoming  = $col->where('status','Upcoming')->count();
        $countActive    = $col->where('status','Active')->count();
        $countCompleted = $col->where('status','Completed')->count();
        $countCancelled = $col->where('status','Cancelled')->count();
        $countGroup     = $col->where('type','Group')->count();
        $countPrivate   = $col->where('type','Private')->count();
        $countOnline    = $col->where('delivery_mood','Online')->count();
        $countOffline   = $col->where('delivery_mood','Offline')->count();
    @endphp

    <div class="stats-row">
        <div class="stat-card" style="--accent:#1B4FA8;" onclick="filterByCiStatus('all')" data-filter="all">
            <div class="stat-label">All</div>
            <div class="stat-value">{{ $instances->total() }}</div>
        </div>
        <div class="stat-card" style="--accent:#1B6FA8;" onclick="filterByCiStatus('Upcoming')" data-filter="Upcoming">
            <div class="stat-label">Upcoming</div>
            <div class="stat-value">{{ $countUpcoming }}</div>
        </div>
        <div class="stat-card" style="--accent:#15803D;" onclick="filterByCiStatus('Active')" data-filter="Active">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ $countActive }}</div>
        </div>
        <div class="stat-card" style="--accent:#7A8A9A;" onclick="filterByCiStatus('Completed')" data-filter="Completed">
            <div class="stat-label">Completed</div>
            <div class="stat-value">{{ $countCompleted }}</div>
        </div>
        <div class="stat-card" style="--accent:#DC2626;" onclick="filterByCiStatus('Cancelled')" data-filter="Cancelled">
            <div class="stat-label">Cancelled</div>
            <div class="stat-value">{{ $countCancelled }}</div>
        </div>
        <div class="stat-card" style="--accent:#2D6FDB;" onclick="filterByCiType('Group')" data-filter-type="Group">
            <div class="stat-label">Group</div>
            <div class="stat-value">{{ $countGroup }}</div>
        </div>
        <div class="stat-card" style="--accent:#C47010;" onclick="filterByCiType('Private')" data-filter-type="Private">
            <div class="stat-label">Private</div>
            <div class="stat-value">{{ $countPrivate }}</div>
        </div>
        <div class="stat-card" style="--accent:#15803D;" onclick="filterByCiMode('Online')" data-filter-mode="Online">
            <div class="stat-label">Online</div>
            <div class="stat-value">{{ $countOnline }}</div>
        </div>
        <div class="stat-card" style="--accent:#7A8A9A;" onclick="filterByCiMode('Offline')" data-filter-mode="Offline">
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
            <input type="text" id="ciSearch" class="search-input"
                   placeholder="Search by course or teacher..."
                   oninput="searchInstances(this.value)">
        </div>

        <select class="filter-select" id="ciStatusFilter" onchange="filterByCiStatusSelect(this.value)">
            <option value="">All Statuses</option>
            <option value="Upcoming">Upcoming</option>
            <option value="Active">Active</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <select class="filter-select" id="ciTypeFilter" onchange="filterByCiTypeSelect(this.value)">
            <option value="">All Types</option>
            <option value="Group">Group</option>
            <option value="Private">Private</option>
        </select>

        <select class="filter-select" id="ciModeFilter" onchange="filterByCiModeSelect(this.value)">
            <option value="">All Modes</option>
            <option value="Online">Online</option>
            <option value="Offline">Offline</option>
        </select>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Course & Level</th>
                        <th>Teacher</th>
                        <th>Patch</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Capacity</th>
                        <th>Schedule</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ciTableBody">
                    @forelse($instances as $instance)
                    @php
                        $count    = $instance->enrollments->count();
                        $capacity = $instance->capacity;
                        $pct      = $capacity > 0 ? round(($count / $capacity) * 100) : 0;
                        $isFull   = $count >= $capacity;

                        $statusClass = match($instance->status) {
                            'Upcoming'  => 'status-upcoming',
                            'Active'    => 'status-active-ci',
                            'Completed' => 'status-completed',
                            'Cancelled' => 'status-cancelled',
                            default     => 'status-upcoming',
                        };
                        $typeClass = match($instance->type) {
                            'Group'   => 'tag-group',
                            'Private' => 'tag-private',
                            default   => 'tag-group',
                        };
                        $modeClass = match($instance->delivery_mood) {
                            'Online'  => 'tag-online',
                            'Offline' => 'tag-offline',
                            default   => 'tag-offline',
                        };
                        $capColor = $isFull ? '#DC2626' : ($pct >= 80 ? '#C47010' : '#1B4FA8');
                    @endphp
                    <tr data-status="{{ $instance->status }}"
                        data-type="{{ $instance->type }}"
                        data-mode="{{ $instance->delivery_mood }}"
                        data-course="{{ strtolower($instance->courseTemplate->name ?? '') }}"
                        data-teacher="{{ strtolower($instance->teacher->name ?? '') }}">

                        {{-- Course & Level --}}
                        <td>
                            <div class="ci-name">
                                {{ $instance->courseTemplate->name ?? '—' }}
                            </div>
                            @if($instance->level ?? null)
                                <div class="ci-sub">{{ $instance->level->name }}</div>
                            @endif
                            @if($instance->sublevel ?? null)
                                <div style="font-size:10px;color:#AAB8C8;">{{ $instance->sublevel->name }}</div>
                            @endif
                        </td>

                        {{-- Teacher --}}
                        <td>
                            @if($instance->teacher ?? null)
                                <div class="ci-name">{{ $instance->teacher->name }}</div>
                            @else
                                <span style="font-size:11px;color:#DC2626;letter-spacing:0.5px;">Not Assigned</span>
                            @endif
                        </td>

                        {{-- Patch --}}
                        <td>
                            @if($instance->patch ?? null)
                                <div class="ci-name">{{ $instance->patch->name }}</div>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="tag {{ $typeClass }}">{{ $instance->type ?? 'Group' }}</span>
                        </td>

                        {{-- Mode --}}
                        <td>
                            <span class="tag {{ $modeClass }}">{{ $instance->delivery_mood }}</span>
                        </td>

                        {{-- Capacity --}}
                        <td style="min-width:120px;">
                            <div class="cap-wrap">
                                <span class="cap-text">{{ $count }} / {{ $capacity }}</span>
                                <div class="cap-track">
                                    <div class="cap-fill"
                                         style="width:{{ $pct }}%;background:{{ $capColor }};"></div>
                                </div>
                            </div>
                            @if($isFull)
                                <div class="cap-full">Full</div>
                            @endif
                        </td>

                        {{-- Schedule --}}
                        <td>
                            <div class="date-main">{{ \Carbon\Carbon::parse($instance->start_date)->format('d M Y') }}</div>
                            <div class="date-sub">→ {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}</div>
                        </td>

                        {{-- Hours --}}
                        <td>
                            <div class="hours-val">{{ $instance->total_hours }}</div>
                            <div class="hours-sub">{{ $instance->session_duration }} hr/session</div>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-badge {{ $statusClass }}">{{ $instance->status }}</span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <a href="{{ route('student-care.instances.show', $instance->course_instance_id) }}"
                            class="btn-action"
                            style="color:#1B4FA8;border-color:rgba(27,79,168,0.25);text-decoration:none;"
                            onmouseover="this.style.background='rgba(27,79,168,0.07)';this.style.borderColor='#1B4FA8'"
                            onmouseout="this.style.background='';this.style.borderColor='rgba(27,79,168,0.25)'">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </a>
                            @php
                                $hasSchedule  = $instance->instanceSchedules->isNotEmpty();
                                $hasSessions  = $instance->sessions->isNotEmpty();
                                $sessionCount = $instance->sessions->count();
                            @endphp

                            @if($hasSessions)
                                {{-- ✅ Scheduled --}}
                                <button
                                    onclick="openScheduleModal({{ $instance->course_instance_id }})"
                                    style="display:inline-flex;align-items:center;gap:6px;
                                        padding:5px 12px;font-size:9px;letter-spacing:1.5px;
                                        text-transform:uppercase;border-radius:3px;
                                        font-family:'DM Sans',sans-serif;font-weight:500;
                                        border:1px solid rgba(5,150,105,0.3);
                                        background:rgba(5,150,105,0.06);
                                        color:#059669;cursor:pointer;transition:all 0.2s;"
                                    onmouseover="this.style.background='rgba(5,150,105,0.12)'"
                                    onmouseout="this.style.background='rgba(5,150,105,0.06)'">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    {{ $sessionCount }} Sessions
                                </button>

                            @elseif($hasSchedule)
                                {{-- ⚠️ Schedule set but no sessions --}}
                                <button
                                    onclick="openScheduleModal({{ $instance->course_instance_id }})"
                                    style="display:inline-flex;align-items:center;gap:6px;
                                        padding:5px 12px;font-size:9px;letter-spacing:1.5px;
                                        text-transform:uppercase;border-radius:3px;
                                        font-family:'DM Sans',sans-serif;font-weight:500;
                                        border:1px solid rgba(245,145,30,0.3);
                                        background:rgba(245,145,30,0.06);
                                        color:#C47010;cursor:pointer;transition:all 0.2s;"
                                    onmouseover="this.style.background='rgba(245,145,30,0.12)'"
                                    onmouseout="this.style.background='rgba(245,145,30,0.06)'">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    Regenerate
                                </button>

                            @else
                                {{-- ➕ No schedule yet --}}
                                <button
                                    onclick="openScheduleModal({{ $instance->course_instance_id }})"
                                    style="display:inline-flex;align-items:center;gap:6px;
                                        padding:5px 12px;font-size:9px;letter-spacing:1.5px;
                                        text-transform:uppercase;border-radius:3px;
                                        font-family:'DM Sans',sans-serif;font-weight:500;
                                        border:1px solid rgba(27,79,168,0.25);
                                        background:transparent;
                                        color:#1B4FA8;cursor:pointer;transition:all 0.2s;"
                                    onmouseover="this.style.background='rgba(27,79,168,0.07)'"
                                    onmouseout="this.style.background='transparent'">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    Set Schedule
                                </button>
                            @endif

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <div class="empty-title">No Course Instances</div>
                                <div class="empty-sub">No instances have been created yet</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($instances->hasPages())
    <div class="pagination-wrap">{{ $instances->links() }}</div>
    @endif
        @if(request()->query('create') == '1')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            openCreateInstanceModal();
        });
    </script>
    @endif
</div>

<script>
let ciActiveStatus = '';
let ciActiveType   = '';
let ciActiveMode   = '';
let ciSearchQuery  = '';

function applyCiFilters() {
    document.querySelectorAll('#ciTableBody tr[data-status]').forEach(row => {
        const matchStatus  = !ciActiveStatus || row.dataset.status  === ciActiveStatus;
        const matchType    = !ciActiveType   || row.dataset.type    === ciActiveType;
        const matchMode    = !ciActiveMode   || row.dataset.mode    === ciActiveMode;
        const course       = row.dataset.course  || '';
        const teacher      = row.dataset.teacher || '';
        const matchSearch  = !ciSearchQuery || course.includes(ciSearchQuery) || teacher.includes(ciSearchQuery);
        row.style.display  = (matchStatus && matchType && matchMode && matchSearch) ? '' : 'none';
    });
}

function searchInstances(q) {
    ciSearchQuery = q.toLowerCase().trim();
    applyCiFilters();
}

function filterByCiStatus(status) {
    ciActiveStatus = status === 'all' ? '' : status;
    document.getElementById('ciStatusFilter').value = ciActiveStatus;
    document.querySelectorAll('.stat-card[data-filter]').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filter === status);
    });
    applyCiFilters();
}

function filterByCiType(type) {
    ciActiveType = type;
    document.getElementById('ciTypeFilter').value = type;
    document.querySelectorAll('.stat-card[data-filter-type]').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filterType === type);
    });
    applyCiFilters();
}

function filterByCiMode(mode) {
    ciActiveMode = mode;
    document.getElementById('ciModeFilter').value = mode;
    document.querySelectorAll('.stat-card[data-filter-mode]').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filterMode === mode);
    });
    applyCiFilters();
}

function filterByCiStatusSelect(val) { ciActiveStatus = val; applyCiFilters(); }
function filterByCiTypeSelect(val)   { ciActiveType   = val; applyCiFilters(); }
function filterByCiModeSelect(val)   { ciActiveMode   = val; applyCiFilters(); }

</script>

@endsection