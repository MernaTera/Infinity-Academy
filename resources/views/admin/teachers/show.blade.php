@extends('admin.layouts.app')
@section('title', 'Teacher Profile')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --bg:#F8F6F2;
    --ink:#1A2A4A;
    --ink-2:#3A4A6A;
    --muted:#7A8A9A;
    --faint:#AAB8C8;
    --border:rgba(27,79,168,0.08);
    --border-2:rgba(27,79,168,0.15);
    --green:#059669;
    --green-l:rgba(5,150,105,0.08);
    --blue:#1B4FA8;
    --blue-l:rgba(27,79,168,0.06);
    --orange:#F5911E;
    --orange-l:rgba(245,145,30,0.08);
    --red:#DC2626;
    --red-l:rgba(220,38,38,0.06);
    --purple:#7C3AED;
    --purple-l:rgba(124,58,237,0.07);
}
*{box-sizing:border-box}
.tp-page{background:var(--bg);min-height:100vh;padding:32px;font-family:'DM Sans',sans-serif;color:var(--ink);}

/* HEADER */
.tp-back{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;border:1px solid var(--border-2);border-radius:6px;font-size:10px;letter-spacing:1.8px;text-transform:uppercase;color:var(--muted);text-decoration:none;font-weight:600;transition:all 0.2s;margin-bottom:20px;}
.tp-back:hover{color:var(--blue);border-color:var(--blue);text-decoration:none;}

/* PROFILE BANNER */
.tp-banner{background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px 32px;margin-bottom:22px;display:flex;gap:26px;align-items:center;position:relative;overflow:hidden;flex-wrap:wrap;}
.tp-banner::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange),var(--blue));}
.tp-avatar{width:96px;height:96px;border-radius:50%;background:linear-gradient(135deg,var(--orange-l),var(--blue-l));display:flex;align-items:center;justify-content:center;flex-shrink:0;border:3px solid #fff;box-shadow:0 4px 16px rgba(27,79,168,0.08);position:relative;}
.tp-avatar span{font-family:'Bebas Neue',sans-serif;font-size:36px;color:var(--blue);letter-spacing:2px;}
.tp-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.tp-info{flex:1;min-width:220px;}
.tp-name{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:4px;color:var(--ink);line-height:1;margin-bottom:6px;}
.tp-role{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;background:var(--blue-l);color:var(--blue);border-radius:20px;font-size:10px;letter-spacing:2px;text-transform:uppercase;font-weight:600;margin-bottom:10px;}
.tp-meta{display:flex;gap:20px;flex-wrap:wrap;font-size:12px;color:var(--muted);}
.tp-meta-item{display:flex;align-items:center;gap:5px;}
.tp-meta-item svg{opacity:0.6;}
.tp-status-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:10px;letter-spacing:2px;text-transform:uppercase;font-weight:600;}
.tp-status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;}
.tp-status-active{background:var(--green-l);color:var(--green);}
.tp-status-inactive{background:rgba(122,138,154,0.1);color:var(--muted);}

/* KPI STATS */
.tp-kpi-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:22px;}
.tp-kpi{background:#fff;border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;transition:transform 0.2s;}
.tp-kpi:hover{transform:translateY(-1px);}
.tp-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kpi-c,var(--blue));}
.tp-kpi-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:500;}
.tp-kpi-val{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:2px;color:var(--kpi-c,var(--ink));line-height:1;}
.tp-kpi-hint{font-size:9px;color:var(--faint);margin-top:4px;letter-spacing:0.5px;}
.tp-kpi.warn{--kpi-c:var(--orange);}
.tp-kpi.danger{--kpi-c:var(--red);}
.tp-kpi.success{--kpi-c:var(--green);}
.tp-kpi.info{--kpi-c:var(--blue);}
.tp-kpi.purple{--kpi-c:var(--purple);}

/* OVERLOAD ALERT */
.tp-alert{background:var(--red-l);border:1px solid rgba(220,38,38,0.2);border-left:3px solid var(--red);border-radius:8px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:12px;font-size:13px;color:var(--red);font-weight:500;}

/* SECTIONS */
.tp-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:22px;}
.tp-card{background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden;}
.tp-card-head{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.tp-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;color:var(--blue);display:flex;align-items:center;gap:8px;}
.tp-card-title svg{opacity:0.7;}
.tp-card-body{padding:20px 22px;}

/* Personal info list */
.tp-info-list{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;}
.tp-info-row{display:flex;flex-direction:column;gap:3px;}
.tp-info-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:500;}
.tp-info-val{font-size:13px;color:var(--ink);font-weight:500;word-break:break-word;}
.tp-info-val.na{color:var(--faint);font-style:italic;font-weight:400;}

/* Contract card */
.tp-contract-main{background:linear-gradient(135deg,var(--blue-l),transparent);border:1px solid var(--border-2);border-radius:8px;padding:16px 18px;margin-bottom:14px;}
.tp-contract-type{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:var(--blue);margin-bottom:4px;}
.tp-contract-max{font-size:11px;color:var(--muted);letter-spacing:0.3px;}
.tp-contract-usage{margin-top:12px;}
.tp-usage-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;display:flex;justify-content:space-between;}
.tp-usage-bar{height:6px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;}
.tp-usage-fill{height:100%;transition:width 0.4s;border-radius:3px;}
.tp-usage-fill.ok{background:var(--green);}
.tp-usage-fill.warn{background:var(--orange);}
.tp-usage-fill.over{background:var(--red);}
.tp-history{max-height:180px;overflow-y:auto;}
.tp-history-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px dashed var(--border);font-size:11px;}
.tp-history-item:last-child{border:none;}
.tp-history-name{color:var(--ink);font-weight:500;}
.tp-history-patch{color:var(--muted);font-size:10px;}
.tp-history-active{color:var(--green);font-weight:600;font-size:9px;letter-spacing:1px;text-transform:uppercase;background:var(--green-l);padding:2px 8px;border-radius:10px;}

/* Availability slots */
.tp-slots-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;}
.tp-slot-day{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px;}
.tp-slot-day.active{border-color:rgba(5,150,105,0.3);background:var(--green-l);}
.tp-slot-day-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;font-weight:600;}
.tp-slot-day.active .tp-slot-day-label{color:var(--green);}
.tp-slot-list{display:flex;flex-direction:column;gap:5px;}
.tp-slot-item{font-size:11px;color:var(--ink);background:#fff;padding:5px 8px;border-radius:4px;font-family:monospace;}
.tp-slot-empty{font-size:10px;color:var(--faint);font-style:italic;}

/* Courses list */
.tp-course-item{padding:14px 0;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;}
.tp-course-item:last-child{border:none;}
.tp-course-name{font-weight:600;color:var(--ink);font-size:13px;margin-bottom:3px;}
.tp-course-meta{font-size:10px;color:var(--muted);letter-spacing:0.3px;}
.tp-course-progress{width:120px;flex-shrink:0;}
.tp-course-progress-track{height:5px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;}
.tp-course-progress-fill{height:100%;background:linear-gradient(90deg,var(--green),#0BA870);border-radius:3px;}
.tp-course-progress-text{font-size:9px;color:var(--muted);text-align:right;margin-top:3px;letter-spacing:0.5px;}
.tp-course-status{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;padding:3px 8px;border-radius:20px;}
.tp-course-status.active{background:var(--green-l);color:var(--green);}
.tp-course-status.upcoming{background:var(--orange-l);color:#C47010;}
.tp-course-status.completed{background:var(--blue-l);color:var(--blue);}
.tp-course-students{font-size:10px;color:var(--muted);}
.tp-course-actions{}
.tp-course-link{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--blue);text-decoration:none;font-weight:600;padding:5px 10px;border:1px solid rgba(27,79,168,0.2);border-radius:4px;transition:all 0.2s;}
.tp-course-link:hover{background:var(--blue);color:#fff;text-decoration:none;border-color:var(--blue);}

/* Reports table */
.tp-reports-mini{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
.tp-report-stat{padding:10px 12px;border:1px solid var(--border);border-radius:6px;text-align:center;}
.tp-report-stat-val{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;color:var(--stat-c,var(--ink));}
.tp-report-stat-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-top:3px;}

/* Empty state */
.tp-empty{text-align:center;padding:30px 20px;color:var(--faint);font-size:12px;font-style:italic;}

/* Full-width sections */
.tp-full{grid-column:1/-1;}

/* Responsive */
@media(max-width:1200px){
    .tp-kpi-grid{grid-template-columns:repeat(3,1fr);}
}
@media(max-width:900px){
    .tp-page{padding:20px 16px;}
    .tp-grid{grid-template-columns:1fr;}
    .tp-info-list{grid-template-columns:1fr;}
    .tp-slots-grid{grid-template-columns:1fr;}
    .tp-kpi-grid{grid-template-columns:repeat(2,1fr);}
}
</style>

<div class="tp-page">

    {{-- Back --}}
    <a href="{{ route('admin.employees.show', $employee->employee_id) }}" class="tp-back">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Employee Profile
    </a>

    {{-- Profile Banner --}}
    <div class="tp-banner">
        <div class="tp-avatar">
            <span>{{ strtoupper(substr($employee->full_name ?? 'T', 0, 1)) }}</span>
        </div>

        <div class="tp-info">
            <div class="tp-role">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                Instructor
            </div>
            <div class="tp-name">{{ $employee->full_name ?? '—' }}</div>
            <div class="tp-meta">
                <div class="tp-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>
                    {{ $employee->user?->email ?? '—' }}
                </div>
                <div class="tp-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $employee->branch?->name ?? '—' }}
                </div>
                @if($teacher->englishLevel)
                <div class="tp-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    English Level: {{ $teacher->englishLevel->level_name }}
                </div>
                @endif
            </div>
        </div>

        <span class="tp-status-badge {{ $teacher->is_active ? 'tp-status-active' : 'tp-status-inactive' }}">
            {{ $teacher->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>

    {{-- Overload Alert --}}
    @if($isOverload)
    <div class="tp-alert">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <div>
            <strong>Session Overload</strong> — Teacher has {{ $sessionsThisPatch }} sessions in current patch, exceeding contract limit of {{ $maxAllowed }} by {{ $sessionsThisPatch - $maxAllowed }} sessions.
        </div>
    </div>
    @endif

    {{-- KPI STATS (Current Patch) --}}
    <div class="tp-kpi-grid">
        <div class="tp-kpi info">
            <div class="tp-kpi-label">Active Courses</div>
            <div class="tp-kpi-val">{{ $activeInstances->count() }}</div>
            <div class="tp-kpi-hint">Current patch</div>
        </div>
        <div class="tp-kpi purple">
            <div class="tp-kpi-label">Upcoming</div>
            <div class="tp-kpi-val">{{ $upcomingInstances->count() }}</div>
            <div class="tp-kpi-hint">Starting soon</div>
        </div>
        <div class="tp-kpi success">
            <div class="tp-kpi-label">Completed</div>
            <div class="tp-kpi-val">{{ $completedInstances->count() }}</div>
            <div class="tp-kpi-hint">This patch</div>
        </div>
        <div class="tp-kpi {{ $isOverload ? 'danger' : ($maxAllowed && $sessionsThisPatch / max($maxAllowed, 1) > 0.8 ? 'warn' : 'info') }}">
            <div class="tp-kpi-label">Sessions</div>
            <div class="tp-kpi-val">{{ $sessionsThisPatch }}{{ $maxAllowed ? '/' . $maxAllowed : '' }}</div>
            <div class="tp-kpi-hint">{{ $completedSessions }} done</div>
        </div>
        <div class="tp-kpi info">
            <div class="tp-kpi-label">Students</div>
            <div class="tp-kpi-val">{{ $studentsThisPatch }}</div>
            <div class="tp-kpi-hint">Currently teaching</div>
        </div>
        <div class="tp-kpi {{ $reportStats['overdue'] > 0 ? 'danger' : 'success' }}">
            <div class="tp-kpi-label">Reports Due</div>
            <div class="tp-kpi-val">{{ $reportStats['pending'] + $reportStats['submitted'] }}</div>
            <div class="tp-kpi-hint">{{ $reportStats['overdue'] }} overdue</div>
        </div>
    </div>

    {{-- Grid: Personal Info + Contract --}}
    <div class="tp-grid">

        {{-- Personal Info --}}
        <div class="tp-card">
            <div class="tp-card-head">
                <div class="tp-card-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Personal Details
                </div>
            </div>
            <div class="tp-card-body">
            <div class="tp-info-list">
                <div class="tp-info-row">
                    <span class="tp-info-label">Full Name</span>
                    <span class="tp-info-val">{{ $employee->full_name ?? '—' }}</span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Email</span>
                    <span class="tp-info-val {{ $employee->user?->email ? '' : 'na' }}">
                        {{ $employee->user?->email ?? 'Not set' }}
                    </span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Branch</span>
                    <span class="tp-info-val {{ $employee->branch?->name ? '' : 'na' }}">
                        {{ $employee->branch?->name ?? 'Not set' }}
                    </span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Employee Status</span>
                    <span class="tp-info-val">
                        {{ $employee->status ?? '—' }}
                    </span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Salary</span>
                    <span class="tp-info-val {{ $employee->salary ? '' : 'na' }}">
                        @if($employee->salary){{ number_format($employee->salary, 2) }} LE @else Not set @endif
                    </span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Hired Since</span>
                    <span class="tp-info-val {{ $employee->hired_at ? '' : 'na' }}">
                        {{ $employee->hired_at ? \Carbon\Carbon::parse($employee->hired_at)->format('d M Y') : 'Not set' }}
                    </span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Employee ID</span>
                    <span class="tp-info-val">#{{ str_pad($employee->employee_id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="tp-info-row">
                    <span class="tp-info-label">Max English Level</span>
                    <span class="tp-info-val {{ $teacher->englishLevel ? '' : 'na' }}">
                        {{ $teacher->englishLevel?->level_name ?? 'Not set' }}
                    </span>
                </div>
            </div>
                </div>
            </div>
        </div>

        {{-- Contract --}}
        <div class="tp-card">
            <div class="tp-card-head">
                <div class="tp-card-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                    Contract
                </div>
            </div>
            <div class="tp-card-body">
                @if($contract && $contract->contractType)
                    <div class="tp-contract-main">
                        <div class="tp-contract-type">{{ $contract->contractType->name }}</div>
                        <div class="tp-contract-max">Max <strong style="color:var(--ink);">{{ $contract->contractType->max_sessions_allowed }}</strong> sessions per patch</div>

                        <div class="tp-contract-usage">
                            @php
                                $pct = $contract->contractType->max_sessions_allowed > 0
                                    ? min(100, round(($sessionsThisPatch / $contract->contractType->max_sessions_allowed) * 100))
                                    : 0;
                                $barClass = $pct >= 100 ? 'over' : ($pct >= 80 ? 'warn' : 'ok');
                            @endphp
                            <div class="tp-usage-label">
                                <span>Current patch usage</span>
                                <span>{{ $sessionsThisPatch }} / {{ $contract->contractType->max_sessions_allowed }}</span>
                            </div>
                            <div class="tp-usage-bar">
                                <div class="tp-usage-fill {{ $barClass }}" style="width:{{ $pct }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Contract History</div>
                    <div class="tp-history">
                        @foreach($allContracts as $c)
                        <div class="tp-history-item">
                            <div>
                                <div class="tp-history-name">{{ $c->contractType?->name ?? '—' }}</div>
                                <div class="tp-history-patch">{{ $c->patch?->name ?? 'No patch' }}</div>
                            </div>
                            @if($c->is_active)
                            <span class="tp-history-active">Active</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="tp-empty">
                        No active contract for the current patch.
                    </div>
                @endif
            </div>
        </div>

        {{-- Availability --}}
        <div class="tp-card tp-full">
            <div class="tp-card-head">
                <div class="tp-card-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Weekly Availability
                </div>
            </div>
            <div class="tp-card-body">
                <div class="tp-slots-grid">
                    @foreach($pairLabels as $pair => $label)
                    @php $slots = $availByPair->get($pair, collect()); @endphp
                    <div class="tp-slot-day {{ $slots->isNotEmpty() ? 'active' : '' }}">
                        <div class="tp-slot-day-label">{{ $label }}</div>
                        @if($slots->isNotEmpty())
                            <div class="tp-slot-list">
                                @foreach($slots as $av)
                                <div class="tp-slot-item">
                                    {{ $av->timeSlot?->name ?? '—' }}
                                    @if($av->timeSlot)
                                        · {{ \Carbon\Carbon::parse($av->timeSlot->start_time)->format('H:i') }}
                                        –
                                        {{ \Carbon\Carbon::parse($av->timeSlot->end_time)->format('H:i') }}
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="tp-slot-empty">Not available</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Active Courses --}}
        <div class="tp-card tp-full">
            <div class="tp-card-head">
                <div class="tp-card-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                    Active Courses ({{ $currentInstances->count() }})
                </div>
            </div>
            <div class="tp-card-body">
                @if($currentInstances->count() > 0)
                    @foreach($currentInstances as $inst)
                    @php
                        $completedSess  = $inst->sessions->where('status', 'Completed')->count();
                        $totalSess      = $inst->sessions->count();
                        $courseProgress = $totalSess > 0 ? round(($completedSess / $totalSess) * 100) : 0;
                        $statusClass    = strtolower($inst->status);
                    @endphp
                    <div class="tp-course-item">
                        <div style="flex:1;min-width:200px;">
                            <div class="tp-course-name">
                                {{ $inst->courseTemplate?->name ?? '—' }}
                                <span style="color:var(--muted);font-weight:400;font-size:11px;"> · {{ $inst->level?->name ?? '' }}
                                    @if($inst->sublevel) · {{ $inst->sublevel->name }} @endif
                                </span>
                            </div>
                            <div class="tp-course-meta">
                                {{ $inst->start_date ? \Carbon\Carbon::parse($inst->start_date)->format('d M') : '—' }}
                                →
                                {{ $inst->end_date ? \Carbon\Carbon::parse($inst->end_date)->format('d M Y') : '—' }}
                                · {{ $inst->type ?? 'Group' }}
                                · {{ $inst->delivery_mood ?? '—' }}
                            </div>
                        </div>

                        <div class="tp-course-students">
                            <strong style="color:var(--ink);">{{ $inst->enrollments->count() }}</strong> / {{ $inst->capacity ?? '—' }} students
                        </div>

                        <div class="tp-course-progress">
                            <div class="tp-course-progress-track">
                                <div class="tp-course-progress-fill" style="width:{{ $courseProgress }}%"></div>
                            </div>
                            <div class="tp-course-progress-text">{{ $completedSess }}/{{ $totalSess }} sessions</div>
                        </div>

                        <span class="tp-course-status {{ $statusClass }}">{{ $inst->status }}</span>
<!-- 
                        <div class="tp-course-actions">
                            <a href="#" class="tp-course-link">View</a>
                        </div> -->
                    </div>
                    @endforeach
                @else
                    <div class="tp-empty">
                        No courses assigned in the current patch.
                    </div>
                @endif
            </div>
        </div>

        {{-- Reports Status --}}
        <div class="tp-card">
            <div class="tp-card-head">
                <div class="tp-card-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Reports Overview
                </div>
            </div>
            <div class="tp-card-body">
                <div class="tp-reports-mini">
                    <div class="tp-report-stat" style="--stat-c:var(--orange);">
                        <div class="tp-report-stat-val">{{ $reportStats['pending'] }}</div>
                        <div class="tp-report-stat-label">Pending</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--red);">
                        <div class="tp-report-stat-val">{{ $reportStats['overdue'] }}</div>
                        <div class="tp-report-stat-label">Overdue</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--purple);">
                        <div class="tp-report-stat-val">{{ $reportStats['submitted'] }}</div>
                        <div class="tp-report-stat-label">Submitted</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--green);">
                        <div class="tp-report-stat-val">{{ $reportStats['approved'] }}</div>
                        <div class="tp-report-stat-label">Approved</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--red);">
                        <div class="tp-report-stat-val">{{ $reportStats['rejected'] }}</div>
                        <div class="tp-report-stat-label">Rejected</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--blue);">
                        <div class="tp-report-stat-val">{{ $reportStats['sent'] }}</div>
                        <div class="tp-report-stat-label">Sent</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- All-Time Stats --}}
        <div class="tp-card">
            <div class="tp-card-head">
                <div class="tp-card-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    All-Time Performance
                </div>
            </div>
            <div class="tp-card-body">
                <div class="tp-reports-mini" style="grid-template-columns:1fr 1fr 1fr;">
                    <div class="tp-report-stat" style="--stat-c:var(--blue);">
                        <div class="tp-report-stat-val">{{ $totalCoursesAllTime }}</div>
                        <div class="tp-report-stat-label">Total Courses</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--green);">
                        <div class="tp-report-stat-val">{{ $totalSessionsAllTime }}</div>
                        <div class="tp-report-stat-label">Total Sessions</div>
                    </div>
                    <div class="tp-report-stat" style="--stat-c:var(--purple);">
                        <div class="tp-report-stat-val">{{ $totalStudentsAllTime }}</div>
                        <div class="tp-report-stat-label">Students Taught</div>
                    </div>
                </div>

                @if($employee->hired_at || $employee->created_at)
                <div style="margin-top:14px;padding-top:14px;border-top:1px dashed var(--border);text-align:center;font-size:11px;color:var(--muted);">
                    With Infinity Academy for
                    <strong style="color:var(--ink);">
                        {{ \Carbon\Carbon::parse($employee->hired_at ?? $employee->created_at)->diffForHumans(null, true) }}
                    </strong>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection