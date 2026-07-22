@extends('teacher.layouts.app')
@section('title', 'Reports')

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
.tr-page{background:var(--bg);min-height:100vh;padding:36px 32px;font-family:'DM Sans',sans-serif;color:var(--ink);}

/* ═══════════════════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════════════════ */
.tr-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:26px;flex-wrap:wrap;gap:16px;}
.tr-header-left{}
.tr-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--green);margin-bottom:6px;font-weight:500;}
.tr-title{font-family:'Bebas Neue',sans-serif;font-size:38px;letter-spacing:5px;color:var(--blue);margin:0;line-height:1;}
.tr-subtitle{font-size:12px;color:var(--muted);margin-top:8px;letter-spacing:0.3px;}

/* ═══════════════════════════════════════════════════════════════
   KPI CARDS
═══════════════════════════════════════════════════════════════ */
.tr-kpi-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:26px;}
.tr-kpi-card{background:#fff;border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;}
.tr-kpi-card:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(27,79,168,0.06);}
.tr-kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kpi-c,var(--blue));}
.tr-kpi-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:500;}
.tr-kpi-val{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:2px;color:var(--kpi-c,var(--ink));line-height:1;}
.tr-kpi-hint{font-size:9px;color:var(--faint);margin-top:4px;letter-spacing:0.5px;}

.tr-kpi-card.pending  {--kpi-c:var(--orange);}
.tr-kpi-card.overdue  {--kpi-c:var(--red);}
.tr-kpi-card.submitted{--kpi-c:var(--purple);}
.tr-kpi-card.approved {--kpi-c:var(--green);}
.tr-kpi-card.rejected {--kpi-c:var(--red);}
.tr-kpi-card.sent     {--kpi-c:var(--blue);}

/* ═══════════════════════════════════════════════════════════════
   FILTER PILLS + SEARCH
═══════════════════════════════════════════════════════════════ */
.tr-toolbar{display:flex;gap:10px;align-items:center;margin-bottom:22px;flex-wrap:wrap;}
.tr-search{flex:1;max-width:340px;min-width:200px;position:relative;}
.tr-search input{width:100%;padding:10px 14px 10px 38px;background:#fff;border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink);outline:none;transition:border-color 0.2s;}
.tr-search input:focus{border-color:var(--blue);}
.tr-search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--faint);}
.tr-pills{display:flex;gap:6px;flex-wrap:wrap;}
.tr-pill{padding:8px 16px;background:#fff;border:1px solid var(--border-2);border-radius:6px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:6px;}
.tr-pill:hover{border-color:var(--blue);color:var(--blue);}
.tr-pill.active{background:var(--blue);color:#fff;border-color:var(--blue);}
.tr-pill-count{background:rgba(255,255,255,0.15);padding:1px 6px;border-radius:10px;font-size:9px;}
.tr-pill:not(.active) .tr-pill-count{background:var(--blue-l);color:var(--blue);}

/* ═══════════════════════════════════════════════════════════════
   COURSE CARDS
═══════════════════════════════════════════════════════════════ */
.tr-course-card{background:#fff;border:1px solid var(--border);border-radius:10px;margin-bottom:18px;overflow:hidden;transition:box-shadow 0.2s;}
.tr-course-card:hover{box-shadow:0 2px 16px rgba(27,79,168,0.04);}

/* Course header */
.tr-course-head{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;}
.tr-course-info{flex:1;min-width:260px;}
.tr-course-meta-top{display:flex;align-items:center;gap:10px;margin-bottom:6px;}
.tr-course-patch{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);padding:3px 9px;background:var(--bg);border-radius:3px;}
.tr-course-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--ink);line-height:1.1;margin-bottom:4px;}
.tr-course-level{font-size:11px;color:var(--muted);letter-spacing:0.5px;}
.tr-course-meta-row{display:flex;gap:22px;margin-top:12px;flex-wrap:wrap;}
.tr-course-meta-item{display:flex;flex-direction:column;gap:2px;}
.tr-course-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);}
.tr-course-meta-val{font-size:12px;color:var(--ink-2);font-weight:500;}

/* Deadline badge */
.tr-deadline{padding:10px 14px;border-radius:6px;text-align:right;min-width:150px;}
.tr-deadline.ok{background:var(--green-l);border:1px solid rgba(5,150,105,0.15);}
.tr-deadline.warn{background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.tr-deadline.overdue{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.tr-deadline-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;margin-bottom:3px;font-weight:500;}
.tr-deadline.ok .tr-deadline-label{color:var(--green);}
.tr-deadline.warn .tr-deadline-label{color:#C47010;}
.tr-deadline.overdue .tr-deadline-label{color:var(--red);}
.tr-deadline-val{font-size:13px;font-weight:600;letter-spacing:0.5px;}
.tr-deadline.ok .tr-deadline-val{color:var(--green);}
.tr-deadline.warn .tr-deadline-val{color:#C47010;}
.tr-deadline.overdue .tr-deadline-val{color:var(--red);}

/* Progress bar */
.tr-progress-strip{padding:12px 24px;background:var(--bg);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;}
.tr-progress-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);white-space:nowrap;font-weight:500;}
.tr-progress-track{flex:1;height:5px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;}
.tr-progress-fill{height:100%;background:linear-gradient(90deg,var(--green),#0BA870);border-radius:3px;transition:width 0.4s;}
.tr-progress-text{font-size:11px;color:var(--ink);font-weight:600;font-family:'DM Sans',sans-serif;letter-spacing:0.5px;}
.tr-progress-pct{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;color:var(--green);margin-left:4px;}

/* Student rows */
.tr-students-table{width:100%;border-collapse:collapse;}
.tr-students-table thead{background:var(--bg);}
.tr-students-table th{padding:11px 24px;text-align:left;font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:500;border-bottom:1px solid var(--border);}
.tr-students-table td{padding:14px 24px;border-bottom:1px solid var(--border);font-size:12.5px;color:var(--ink);}
.tr-students-table tbody tr:last-child td{border-bottom:none;}
.tr-students-table tbody tr:hover{background:var(--blue-l);}

.tr-student-num{color:var(--faint);font-size:10px;font-family:'DM Sans',sans-serif;letter-spacing:0.5px;width:32px;}
.tr-student-name{font-weight:500;color:var(--ink);}
.tr-student-phone{font-size:10px;color:var(--muted);margin-top:2px;letter-spacing:0.3px;}

.tr-att-cell{font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink-2);}
.tr-att-present{color:var(--green);font-weight:600;}
.tr-att-absent{color:var(--red);font-weight:600;}

/* Report status badge */
.tr-status-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;font-size:9.5px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;font-family:'DM Sans',sans-serif;}
.tr-status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
.tr-status-pending  {color:var(--orange);background:var(--orange-l);}
.tr-status-draft    {color:var(--muted);background:rgba(122,138,154,0.08);}
.tr-status-submitted{color:var(--purple);background:var(--purple-l);}
.tr-status-approved {color:var(--green);background:var(--green-l);}
.tr-status-rejected {color:var(--red);background:var(--red-l);}
.tr-status-sent     {color:var(--blue);background:var(--blue-l);}

/* Action buttons */
.tr-action-cell{text-align:right;}
.tr-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:4px;font-family:'DM Sans',sans-serif;font-size:9.5px;letter-spacing:1.5px;text-transform:uppercase;text-decoration:none;font-weight:600;border:1px solid;background:#fff;cursor:pointer;transition:all 0.2s;white-space:nowrap;}
.tr-btn:hover{transform:translateY(-1px);text-decoration:none;}
.tr-btn-primary  {color:var(--green);border-color:rgba(5,150,105,0.25);}
.tr-btn-primary:hover  {background:var(--green);color:#fff;border-color:var(--green);}
.tr-btn-warning  {color:var(--orange);border-color:rgba(245,145,30,0.3);}
.tr-btn-warning:hover  {background:var(--orange);color:#fff;border-color:var(--orange);}
.tr-btn-neutral  {color:var(--blue);border-color:rgba(27,79,168,0.2);}
.tr-btn-neutral:hover  {background:var(--blue);color:#fff;border-color:var(--blue);}
.tr-btn-success  {color:#fff;background:var(--blue);border-color:var(--blue);}
.tr-btn-success:hover  {background:#0F3D8A;border-color:#0F3D8A;}
.tr-btn svg{opacity:0.9;}

/* Overdue row indicator */
.tr-row-overdue td{background:linear-gradient(90deg,rgba(220,38,38,0.03),transparent);}
.tr-row-overdue .tr-student-name::before{content:'●';color:var(--red);margin-right:6px;font-size:8px;}

/* ═══════════════════════════════════════════════════════════════
   EMPTY STATE
═══════════════════════════════════════════════════════════════ */
.tr-empty{background:#fff;border:1px dashed var(--border-2);border-radius:10px;padding:60px 32px;text-align:center;}
.tr-empty-icon{width:56px;height:56px;background:var(--bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
.tr-empty-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:var(--muted);margin-bottom:8px;}
.tr-empty-sub{font-size:12px;color:var(--faint);max-width:340px;margin:0 auto;line-height:1.5;}

/* ═══════════════════════════════════════════════════════════════
   FLASH MESSAGES
═══════════════════════════════════════════════════════════════ */
.tr-flash{padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:12.5px;display:flex;align-items:center;gap:10px;}
.tr-flash.success{background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);}
.tr-flash.error{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);color:var(--red);}

/* ═══════════════════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════════════════ */
@media(max-width:1200px){.tr-kpi-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:900px){
    .tr-page{padding:22px 16px;}
    .tr-title{font-size:30px;}
    .tr-kpi-grid{grid-template-columns:repeat(2,1fr);}
    .tr-course-head{flex-direction:column;}
    .tr-deadline{width:100%;text-align:left;}
    .tr-students-table{display:block;overflow-x:auto;}
    .tr-students-table th,.tr-students-table td{padding:11px 16px;}
}
@media(max-width:600px){
    .tr-kpi-grid{grid-template-columns:1fr 1fr;gap:8px;}
    .tr-kpi-val{font-size:26px;}
    .tr-progress-strip{flex-wrap:wrap;}
}
</style>

<div class="tr-page">

    {{-- ══════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════ --}}
    <div class="tr-header">
        <div class="tr-header-left">
            <div class="tr-eyebrow">Instructor Panel</div>
            <h1 class="tr-title">Student Reports</h1>
            <div class="tr-subtitle">Grade, submit, and track student evaluations. Deadline is 3 days after course end date.</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════ --}}
    @if(session('success'))
    <div class="tr-flash success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="tr-flash error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         KPI STATS
    ══════════════════════════════════════════ --}}
    <div class="tr-kpi-grid">
        <div class="tr-kpi-card pending">
            <div class="tr-kpi-label">Pending</div>
            <div class="tr-kpi-val">{{ $stats['pending'] }}</div>
            <div class="tr-kpi-hint">Not started</div>
        </div>
        <div class="tr-kpi-card overdue">
            <div class="tr-kpi-label">Overdue</div>
            <div class="tr-kpi-val">{{ $stats['overdue'] }}</div>
            <div class="tr-kpi-hint">Past 3-day deadline</div>
        </div>
        <div class="tr-kpi-card submitted">
            <div class="tr-kpi-label">Submitted</div>
            <div class="tr-kpi-val">{{ $stats['submitted'] }}</div>
            <div class="tr-kpi-hint">Awaiting admin</div>
        </div>
        <div class="tr-kpi-card approved">
            <div class="tr-kpi-label">Approved</div>
            <div class="tr-kpi-val">{{ $stats['approved'] }}</div>
            <div class="tr-kpi-hint">Ready to send</div>
        </div>
        <div class="tr-kpi-card rejected">
            <div class="tr-kpi-label">Rejected</div>
            <div class="tr-kpi-val">{{ $stats['rejected'] }}</div>
            <div class="tr-kpi-hint">Needs revision</div>
        </div>
        <div class="tr-kpi-card sent">
            <div class="tr-kpi-label">Sent</div>
            <div class="tr-kpi-val">{{ $stats['sent'] }}</div>
            <div class="tr-kpi-hint">Delivered</div>
        </div>
        @if($stats['send_overdue'] > 0)
        <div class="tr-kpi-card" style="--kpi-c:var(--red);">
            <div class="tr-kpi-label">Send Overdue</div>
            <div class="tr-kpi-val">{{ $stats['send_overdue'] }}</div>
            <div class="tr-kpi-hint">Past 24h deadline</div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         FILTER PILLS + SEARCH
    ══════════════════════════════════════════ --}}
    <div class="tr-toolbar">
        <div class="tr-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="tr-search-input" placeholder="Search by student, course, or level..." oninput="filterReports()">
        </div>
        <div class="tr-pills">
            <button class="tr-pill active" data-filter="all" onclick="setFilter('all', this)">
                All
            </button>
            <button class="tr-pill" data-filter="pending" onclick="setFilter('pending', this)">
                Pending <span class="tr-pill-count">{{ $stats['pending'] }}</span>
            </button>
            @if($stats['overdue'] > 0)
            <button class="tr-pill" data-filter="overdue" onclick="setFilter('overdue', this)" style="border-color:rgba(220,38,38,0.25);color:var(--red);">
                Overdue <span class="tr-pill-count" style="background:var(--red-l);color:var(--red);">{{ $stats['overdue'] }}</span>
            </button>
            @endif
            <button class="tr-pill" data-filter="rejected" onclick="setFilter('rejected', this)">
                Rejected <span class="tr-pill-count">{{ $stats['rejected'] }}</span>
            </button>
            <button class="tr-pill" data-filter="approved" onclick="setFilter('approved', this)">
                Approved <span class="tr-pill-count">{{ $stats['approved'] }}</span>
            </button>
            <button class="tr-pill" data-filter="submitted" onclick="setFilter('submitted', this)">
                Submitted <span class="tr-pill-count">{{ $stats['submitted'] }}</span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         COURSE CARDS (oldest → newest)
    ══════════════════════════════════════════ --}}
    <div id="tr-courses-container">

        @forelse($completedInstances as $inst)
            @php
                $enrollments = $inst->enrollments;
                $totalStudents = $enrollments->count();

                // Report counts
                $reportsWithStatus = $enrollments->map(fn($e) => $e->report?->status ?? 'Pending');
                $completedReports = $reportsWithStatus->whereIn(null, ['Approved','Sent'])->count();
                $doneReports = $enrollments->filter(fn($e) => in_array($e->report?->status, ['Submitted','Approved','Sent']))->count();
                $progressPct = $totalStudents > 0 ? round(($doneReports / $totalStudents) * 100) : 0;

                // Deadline
                $endDate = $inst->end_date ? \Carbon\Carbon::parse($inst->end_date) : null;
                $deadline = $endDate?->copy()->addDays(3);
                $now = now();

                if (!$deadline) {
                    $deadlineClass = 'ok';
                    $deadlineLabel = 'Deadline';
                    $deadlineVal = 'N/A';
                } elseif ($deadline->isPast()) {
                    $deadlineClass = 'overdue';
                    $deadlineLabel = 'Overdue';
                    $deadlineVal = 'By ' . (int)abs($now->diffInDays($deadline)) . ' day' . ((int)abs($now->diffInDays($deadline)) !== 1 ? 's' : '');
                } elseif ($deadline->diffInDays($now) < 2) {
                    $deadlineClass = 'warn';
                    $deadlineLabel = 'Due Soon';
                    $deadlineVal = $deadline->format('d M Y');
                } else {
                    $deadlineClass = 'ok';
                    $deadlineLabel = 'Deadline';
                    $deadlineVal = $deadline->format('d M Y');
                }

                // Total sessions
                $totalSessions = $inst->sessions->count();
            @endphp

            <div class="tr-course-card"
                 data-course-name="{{ strtolower($inst->courseTemplate?->name ?? '') }}"
                 data-course-level="{{ strtolower($inst->level?->name ?? '') }}">

                {{-- Course Header --}}
                <div class="tr-course-head">
                    <div class="tr-course-info">
                        <div class="tr-course-meta-top">
                            @if($inst->patch)
                            <span class="tr-course-patch">{{ $inst->patch->name }}</span>
                            @endif
                            <span class="tr-course-patch" style="background:var(--blue-l);color:var(--blue);">{{ $inst->type ?? 'Group' }}</span>
                        </div>
                        <div class="tr-course-name">{{ $inst->courseTemplate?->name ?? 'Untitled Course' }}</div>
                        <div class="tr-course-level">
                            {{ $inst->level?->name ?? '—' }}
                            @if($inst->sublevel) · {{ $inst->sublevel->name }} @endif
                        </div>

                        <div class="tr-course-meta-row">
                            <div class="tr-course-meta-item">
                                <span class="tr-course-meta-label">Period</span>
                                <span class="tr-course-meta-val">
                                    {{ $inst->start_date ? \Carbon\Carbon::parse($inst->start_date)->format('d M') : '—' }}
                                    →
                                    {{ $endDate ? $endDate->format('d M Y') : '—' }}
                                </span>
                            </div>
                            <div class="tr-course-meta-item">
                                <span class="tr-course-meta-label">Sessions</span>
                                <span class="tr-course-meta-val">{{ $totalSessions }}</span>
                            </div>
                            <div class="tr-course-meta-item">
                                <span class="tr-course-meta-label">Students</span>
                                <span class="tr-course-meta-val">{{ $totalStudents }}</span>
                            </div>
                            <div class="tr-course-meta-item">
                                <span class="tr-course-meta-label">Delivery</span>
                                <span class="tr-course-meta-val">{{ $inst->delivery_mood ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="tr-deadline {{ $deadlineClass }}">
                        <div class="tr-deadline-label">{{ $deadlineLabel }}</div>
                        <div class="tr-deadline-val">{{ $deadlineVal }}</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="tr-progress-strip">
                    <span class="tr-progress-label">Reports Progress</span>
                    <div class="tr-progress-track">
                        <div class="tr-progress-fill" style="width:{{ $progressPct }}%"></div>
                    </div>
                    <span class="tr-progress-text">{{ $doneReports }}/{{ $totalStudents }}</span>
                    <span class="tr-progress-pct">{{ $progressPct }}%</span>
                </div>

                {{-- Students table --}}
                <table class="tr-students-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Attendance</th>
                            <th>Report Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $index => $enr)
                            @php
                                $student = $enr->student;
                                $report  = $enr->report;
                                $status  = $report?->status ?? 'Pending';

                                $isOverdue = !$report && $deadline && $deadline->isPast();

                                $presentCount = $enr->attendances?->where('status', 'Present')->count() ?? 0;
                                $totalAtt = $enr->attendances?->count() ?? 0;

                                // Status label + class
                                $statusMap = [
                                    'Pending'   => ['tr-status-pending',   'Not Started'],
                                    'Draft'     => ['tr-status-draft',     'Draft'],
                                    'Submitted' => ['tr-status-submitted', 'Submitted'],
                                    'Approved'  => ['tr-status-approved',  'Approved'],
                                    'Rejected'  => ['tr-status-rejected',  'Rejected'],
                                    'Sent'      => ['tr-status-sent',      'Sent to Student'],
                                ];
                                [$statusClass, $statusLabel] = $statusMap[$status] ?? ['tr-status-pending','Not Started'];
                            @endphp
                            <tr class="{{ $isOverdue ? 'tr-row-overdue' : '' }}"
                                data-student-name="{{ strtolower($student?->full_name ?? '') }}"
                                data-status="{{ strtolower($status) }}"
                                data-overdue="{{ $isOverdue ? '1' : '0' }}">

                                <td class="tr-student-num">{{ $index + 1 }}</td>

                                <td>
                                    <div class="tr-student-name">{{ $student?->full_name ?? '—' }}</div>
                                    @if($student?->phones?->first())
                                    <div class="tr-student-phone">{{ $student->phones->first()->phone_number }}</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="tr-att-cell">
                                        <span class="tr-att-present">{{ $presentCount }}</span>
                                        <span style="color:var(--faint);">/</span>
                                        <span>{{ $totalAtt }}</span>
                                    </div>
                                </td>

                                <td>
                                    <span class="tr-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>

                                <td class="tr-action-cell">
                                    @if(!$report)
                                        {{-- No report → Create --}}
                                        <a href="{{ route('teacher.reports.create', ['enrollment_id' => $enr->enrollment_id]) }}"
                                           class="tr-btn tr-btn-primary">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                            Start Report
                                        </a>
                                    @elseif($status === 'Draft')
                                        {{-- Draft → Continue --}}
                                        <a href="{{ route('teacher.reports.edit', $report->report_id) }}"
                                           class="tr-btn tr-btn-primary">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                            Continue
                                        </a>
                                    @elseif($status === 'Rejected')
                                        {{-- Rejected → Revise --}}
                                        <a href="{{ route('teacher.reports.edit', $report->report_id) }}"
                                           class="tr-btn tr-btn-warning">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                            Revise
                                        </a>
                                    @elseif($status === 'Submitted')
                                        <span class="tr-btn tr-btn-neutral" style="cursor:default;opacity:0.7;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            Awaiting Admin
                                        </span>
                                @elseif($status === 'Approved')
                                    @php
                                        $approvedAt   = $report->approved_at ? \Carbon\Carbon::parse($report->approved_at) : null;
                                        $sendDeadline = $approvedAt?->copy()->addDay();
                                        $sendOverdue  = $sendDeadline && $sendDeadline->isPast();
                                        $hoursLeft    = $sendDeadline ? (int) now()->diffInHours($sendDeadline, false) : null;
                                    @endphp
                                    <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-end;">
                                        <form method="POST" action="{{ route('teacher.reports.mark-sent', $report->report_id) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="tr-btn tr-btn-success">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                                Mark as Sent
                                            </button>
                                        </form>
                                        @if($sendDeadline)
                                            @if($sendOverdue)
                                                <span style="font-size:9px;color:var(--red);font-weight:700;letter-spacing:0.5px;">
                                                    ⚠ OVERDUE by {{ abs($hoursLeft) }}h
                                                </span>
                                            @elseif($hoursLeft <= 12)
                                                <span style="font-size:9px;color:var(--orange);font-weight:600;">
                                                    ⏳ {{ $hoursLeft }}h left
                                                </span>
                                            @else
                                                <span style="font-size:9px;color:var(--muted);">
                                                    Send by {{ $sendDeadline->format('d M · H:i') }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    @elseif($status === 'Sent')
                                        <span class="tr-btn tr-btn-neutral" style="cursor:default;opacity:0.7;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            Completed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @empty
            <div class="tr-empty">
                <div class="tr-empty-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div class="tr-empty-title">No Completed Courses Yet</div>
                <div class="tr-empty-sub">Reports become available once your courses are marked as completed. You'll have 3 days from the course end date to submit each student's report.</div>
            </div>
        @endforelse

    </div>
</div>

<script>
let activeFilter = 'all';
let activeSearch = '';

function setFilter(f, btn) {
    activeFilter = f;
    document.querySelectorAll('.tr-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    filterReports();
}

function filterReports() {
    activeSearch = document.getElementById('tr-search-input').value.toLowerCase().trim();

    document.querySelectorAll('.tr-course-card').forEach(card => {
        const rows = card.querySelectorAll('tbody tr');
        let visibleRows = 0;

        rows.forEach(row => {
            const studentName = row.dataset.studentName || '';
            const status      = row.dataset.status      || '';
            const isOverdue   = row.dataset.overdue     === '1';
            const courseName  = card.dataset.courseName  || '';
            const courseLevel = card.dataset.courseLevel || '';

            // Filter match
            let matchFilter = true;
            if (activeFilter === 'pending')   matchFilter = status === 'pending';
            if (activeFilter === 'overdue')   matchFilter = isOverdue;
            if (activeFilter === 'draft')     matchFilter = status === 'draft';
            if (activeFilter === 'submitted') matchFilter = status === 'submitted';
            if (activeFilter === 'approved')  matchFilter = status === 'approved';
            if (activeFilter === 'rejected')  matchFilter = status === 'rejected';
            if (activeFilter === 'sent')      matchFilter = status === 'sent';

            // Search match
            const matchSearch = !activeSearch
                || studentName.includes(activeSearch)
                || courseName.includes(activeSearch)
                || courseLevel.includes(activeSearch);

            const show = matchFilter && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) visibleRows++;
        });

        card.style.display = visibleRows > 0 ? '' : 'none';
    });
}
</script>
@endsection