@extends('admin.layouts.app')
@section('title', 'Reports Approval')

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
.ar-page{background:var(--bg);min-height:100vh;padding:36px 32px;font-family:'DM Sans',sans-serif;color:var(--ink);}

/* HEADER */
.ar-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:26px;flex-wrap:wrap;gap:16px;}
.ar-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:6px;font-weight:500;}
.ar-title{font-family:'Bebas Neue',sans-serif;font-size:38px;letter-spacing:5px;color:var(--blue);margin:0;line-height:1;}
.ar-subtitle{font-size:12px;color:var(--muted);margin-top:8px;letter-spacing:0.3px;}

/* KPI CARDS */
.ar-kpi-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:22px;}
.ar-kpi-card{background:#fff;border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;cursor:pointer;}
.ar-kpi-card:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(27,79,168,0.06);}
.ar-kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kpi-c,var(--blue));}
.ar-kpi-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:500;}
.ar-kpi-val{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:2px;color:var(--kpi-c,var(--ink));line-height:1;}
.ar-kpi-hint{font-size:9px;color:var(--faint);margin-top:4px;letter-spacing:0.5px;}
.ar-kpi-card.pending  {--kpi-c:var(--muted);}
.ar-kpi-card.overdue  {--kpi-c:var(--red);}
.ar-kpi-card.submitted{--kpi-c:var(--purple);}
.ar-kpi-card.approved {--kpi-c:var(--green);}
.ar-kpi-card.rejected {--kpi-c:var(--red);}
.ar-kpi-card.sent     {--kpi-c:var(--blue);}

/* Highlight submitted (needs action) */
.ar-kpi-card.submitted::after{content:'ACTION';position:absolute;top:8px;right:10px;font-size:7px;letter-spacing:1.5px;background:var(--purple);color:#fff;padding:2px 6px;border-radius:2px;font-weight:600;}

/* TOOLBAR */
.ar-toolbar{display:flex;gap:10px;align-items:center;margin-bottom:22px;flex-wrap:wrap;background:#fff;padding:14px 18px;border-radius:8px;border:1px solid var(--border);}
.ar-search{flex:1;max-width:280px;min-width:180px;position:relative;}
.ar-search input{width:100%;padding:9px 12px 9px 34px;background:var(--bg);border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink);outline:none;transition:border-color 0.2s;}
.ar-search input:focus{border-color:var(--blue);background:#fff;}
.ar-search svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--faint);}
.ar-select{padding:9px 30px 9px 12px;background:var(--bg);border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:11px;color:var(--ink);cursor:pointer;outline:none;-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%237A8A9A' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;}
.ar-pills{display:flex;gap:5px;flex-wrap:wrap;}
.ar-pill{padding:7px 13px;background:transparent;border:1px solid var(--border-2);border-radius:5px;font-size:10px;letter-spacing:1.8px;text-transform:uppercase;color:var(--muted);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:6px;}
.ar-pill:hover{border-color:var(--blue);color:var(--blue);}
.ar-pill.active{background:var(--blue);color:#fff;border-color:var(--blue);}
.ar-pill-count{background:rgba(255,255,255,0.15);padding:1px 6px;border-radius:10px;font-size:9px;font-weight:600;}
.ar-pill:not(.active) .ar-pill-count{background:var(--blue-l);color:var(--blue);}

/* COURSE CARDS */
.ar-course-card{background:#fff;border:1px solid var(--border);border-radius:10px;margin-bottom:18px;overflow:hidden;transition:box-shadow 0.2s;}
.ar-course-card:hover{box-shadow:0 2px 16px rgba(27,79,168,0.04);}
.ar-course-head{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;}
.ar-course-info{flex:1;min-width:260px;}
.ar-course-meta-top{display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;}
.ar-course-patch{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);padding:3px 9px;background:var(--bg);border-radius:3px;font-weight:500;}
.ar-course-type{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--blue);padding:3px 9px;background:var(--blue-l);border-radius:3px;font-weight:500;}
.ar-course-teacher{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--orange);padding:3px 9px;background:var(--orange-l);border-radius:3px;font-weight:500;display:inline-flex;align-items:center;gap:5px;}
.ar-course-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--ink);line-height:1.1;margin-bottom:4px;}
.ar-course-level{font-size:11px;color:var(--muted);letter-spacing:0.5px;}
.ar-course-meta-row{display:flex;gap:22px;margin-top:12px;flex-wrap:wrap;}
.ar-course-meta-item{display:flex;flex-direction:column;gap:2px;}
.ar-course-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);}
.ar-course-meta-val{font-size:12px;color:var(--ink-2);font-weight:500;}

/* Deadline badge */
.ar-deadline{padding:10px 14px;border-radius:6px;text-align:right;min-width:150px;}
.ar-deadline.ok{background:var(--green-l);border:1px solid rgba(5,150,105,0.15);}
.ar-deadline.warn{background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.ar-deadline.overdue{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.ar-deadline-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;margin-bottom:3px;font-weight:500;}
.ar-deadline.ok .ar-deadline-label{color:var(--green);}
.ar-deadline.warn .ar-deadline-label{color:#C47010;}
.ar-deadline.overdue .ar-deadline-label{color:var(--red);}
.ar-deadline-val{font-size:13px;font-weight:600;letter-spacing:0.5px;}
.ar-deadline.ok .ar-deadline-val{color:var(--green);}
.ar-deadline.warn .ar-deadline-val{color:#C47010;}
.ar-deadline.overdue .ar-deadline-val{color:var(--red);}

/* Progress strip */
.ar-progress-strip{padding:12px 24px;background:var(--bg);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;}
.ar-progress-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);white-space:nowrap;font-weight:500;}
.ar-progress-track{flex:1;height:5px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;display:flex;}
.ar-progress-approved{height:100%;background:var(--green);transition:width 0.4s;}
.ar-progress-submitted{height:100%;background:var(--purple);transition:width 0.4s;}
.ar-progress-rejected{height:100%;background:var(--red);transition:width 0.4s;}
.ar-progress-text{font-size:11px;color:var(--ink);font-weight:600;font-family:'DM Sans',sans-serif;letter-spacing:0.5px;}
.ar-progress-legend{display:flex;gap:12px;font-size:9px;color:var(--muted);letter-spacing:0.5px;}
.ar-progress-legend-item{display:flex;align-items:center;gap:4px;}
.ar-progress-dot{width:6px;height:6px;border-radius:2px;}

/* Students table */
.ar-table{width:100%;border-collapse:collapse;}
.ar-table thead{background:var(--bg);}
.ar-table th{padding:11px 24px;text-align:left;font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:500;border-bottom:1px solid var(--border);}
.ar-table td{padding:14px 24px;border-bottom:1px solid var(--border);font-size:12.5px;color:var(--ink);}
.ar-table tbody tr:last-child td{border-bottom:none;}
.ar-table tbody tr:hover{background:var(--blue-l);}

.ar-num{color:var(--faint);font-size:10px;font-family:'DM Sans',sans-serif;letter-spacing:0.5px;width:32px;}
.ar-student-name{font-weight:500;color:var(--ink);}
.ar-student-phone{font-size:10px;color:var(--muted);margin-top:2px;letter-spacing:0.3px;}

.ar-att-cell{font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink-2);}
.ar-att-present{color:var(--green);font-weight:600;}

.ar-score-cell{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;}
.ar-score-cell.high{color:var(--green);}
.ar-score-cell.mid{color:var(--orange);}
.ar-score-cell.low{color:var(--red);}
.ar-score-cell.na{color:var(--faint);font-size:12px;font-family:'DM Sans',sans-serif;letter-spacing:0.5px;}

.ar-submitted-at{font-size:10px;color:var(--muted);letter-spacing:0.3px;}

/* Status badge */
.ar-status-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;font-size:9.5px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;font-family:'DM Sans',sans-serif;}
.ar-status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
.ar-status-pending  {color:var(--muted);background:rgba(122,138,154,0.08);}
.ar-status-draft    {color:var(--muted);background:rgba(122,138,154,0.08);}
.ar-status-submitted{color:var(--purple);background:var(--purple-l);}
.ar-status-approved {color:var(--green);background:var(--green-l);}
.ar-status-rejected {color:var(--red);background:var(--red-l);}
.ar-status-sent     {color:var(--blue);background:var(--blue-l);}

/* Action buttons */
.ar-action-cell{text-align:right;white-space:nowrap;}
.ar-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:4px;font-family:'DM Sans',sans-serif;font-size:9.5px;letter-spacing:1.5px;text-transform:uppercase;text-decoration:none;font-weight:600;border:1px solid;background:#fff;cursor:pointer;transition:all 0.2s;margin-left:4px;}
.ar-btn:hover{transform:translateY(-1px);text-decoration:none;}
.ar-btn-view    {color:var(--blue);border-color:rgba(27,79,168,0.2);}
.ar-btn-view:hover    {background:var(--blue);color:#fff;border-color:var(--blue);}
.ar-btn-approve {color:#fff;background:var(--green);border-color:var(--green);}
.ar-btn-approve:hover {background:#047857;border-color:#047857;}
.ar-btn-reject  {color:var(--red);border-color:rgba(220,38,38,0.25);}
.ar-btn-reject:hover  {background:var(--red);color:#fff;border-color:var(--red);}
.ar-btn-disabled {color:var(--faint);border-color:var(--border);cursor:not-allowed;}

/* Overdue row indicator */
.ar-row-overdue td{background:linear-gradient(90deg,rgba(220,38,38,0.03),transparent);}
.ar-row-overdue .ar-student-name::before{content:'●';color:var(--red);margin-right:6px;font-size:8px;}

/* EMPTY STATE */
.ar-empty{background:#fff;border:1px dashed var(--border-2);border-radius:10px;padding:60px 32px;text-align:center;}
.ar-empty-icon{width:56px;height:56px;background:var(--bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
.ar-empty-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:var(--muted);margin-bottom:8px;}
.ar-empty-sub{font-size:12px;color:var(--faint);max-width:340px;margin:0 auto;line-height:1.5;}

/* FLASH */
.ar-flash{padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:12.5px;display:flex;align-items:center;gap:10px;}
.ar-flash.success{background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);}
.ar-flash.error{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);color:var(--red);}

/* REJECT MODAL */
.ar-modal-bg{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.5);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:20px;}
.ar-modal-bg.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.ar-modal{background:#fff;border-radius:10px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 20px 60px rgba(10,20,40,0.3);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;}
@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:none;opacity:1}}
.ar-modal-head{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.ar-modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--red);}
.ar-modal-close{background:transparent;border:none;cursor:pointer;color:var(--muted);padding:4px;}
.ar-modal-body{padding:18px 22px;}
.ar-modal-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;display:block;font-weight:500;}
.ar-modal-textarea{width:100%;padding:11px 14px;background:var(--bg);border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--ink);outline:none;resize:vertical;min-height:100px;transition:border-color 0.2s;}
.ar-modal-textarea:focus{border-color:var(--red);background:#fff;}
.ar-modal-foot{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;}
.ar-modal-btn-cancel{padding:9px 18px;background:transparent;border:1px solid var(--border-2);border-radius:5px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-weight:600;}
.ar-modal-btn-confirm{padding:10px 22px;background:var(--red);border:none;border-radius:5px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;}
.ar-modal-btn-confirm:hover{background:#B91C1C;}

/* RESPONSIVE */
@media(max-width:1200px){.ar-kpi-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:900px){
    .ar-page{padding:22px 16px;}
    .ar-title{font-size:30px;}
    .ar-kpi-grid{grid-template-columns:repeat(2,1fr);}
    .ar-course-head{flex-direction:column;}
    .ar-deadline{width:100%;text-align:left;}
    .ar-table{display:block;overflow-x:auto;}
    .ar-table th,.ar-table td{padding:11px 16px;}
}
@media(max-width:600px){
    .ar-kpi-grid{grid-template-columns:1fr 1fr;gap:8px;}
    .ar-kpi-val{font-size:26px;}
}
</style>

<div class="ar-page">

    {{-- HEADER --}}
    <div class="ar-header">
        <div>
            <div class="ar-eyebrow">Admin Panel</div>
            <h1 class="ar-title">Reports Approval</h1>
            <div class="ar-subtitle">Review, approve, or reject teacher-submitted student evaluations.</div>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
    <div class="ar-flash success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="ar-flash error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- KPI STATS --}}
    <div class="ar-kpi-grid">
        <div class="ar-kpi-card pending" onclick="setFilter('pending')">
            <div class="ar-kpi-label">Pending</div>
            <div class="ar-kpi-val">{{ $stats['pending'] }}</div>
            <div class="ar-kpi-hint">No report yet</div>
        </div>
        <div class="ar-kpi-card overdue" onclick="setFilter('overdue')">
            <div class="ar-kpi-label">Overdue</div>
            <div class="ar-kpi-val">{{ $stats['overdue'] }}</div>
            <div class="ar-kpi-hint">Past 3-day deadline</div>
        </div>
        <div class="ar-kpi-card submitted" onclick="setFilter('submitted')">
            <div class="ar-kpi-label">Awaiting Review</div>
            <div class="ar-kpi-val">{{ $stats['submitted'] }}</div>
            <div class="ar-kpi-hint">Ready for approval</div>
        </div>
        <div class="ar-kpi-card approved" onclick="setFilter('approved')">
            <div class="ar-kpi-label">Approved</div>
            <div class="ar-kpi-val">{{ $stats['approved'] }}</div>
            <div class="ar-kpi-hint">Awaiting send</div>
        </div>
        <div class="ar-kpi-card rejected" onclick="setFilter('rejected')">
            <div class="ar-kpi-label">Rejected</div>
            <div class="ar-kpi-val">{{ $stats['rejected'] }}</div>
            <div class="ar-kpi-hint">Sent back</div>
        </div>
        <div class="ar-kpi-card sent" onclick="setFilter('sent')">
            <div class="ar-kpi-label">Sent</div>
            <div class="ar-kpi-val">{{ $stats['sent'] }}</div>
            <div class="ar-kpi-hint">Delivered</div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="ar-toolbar">
        <div class="ar-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="ar-search-input" placeholder="Search student, course, or teacher..." oninput="filterReports()">
        </div>

        <select class="ar-select" id="ar-teacher-select" onchange="changeTeacher(this.value)">
            <option value="all">All Teachers</option>
            @foreach($teachers as $tch)
            <option value="{{ $tch->teacher_id }}" {{ $filterTeacher == $tch->teacher_id ? 'selected' : '' }}>
                {{ $tch->employee?->full_name ?? 'Teacher #'.$tch->teacher_id }}
            </option>
            @endforeach
        </select>

        <div class="ar-pills">
            <button class="ar-pill active" data-filter="all" onclick="setFilter('all')">
                All
            </button>
            <button class="ar-pill" data-filter="submitted" onclick="setFilter('submitted')">
                Awaiting <span class="ar-pill-count">{{ $stats['submitted'] }}</span>
            </button>
            <button class="ar-pill" data-filter="approved" onclick="setFilter('approved')">
                Approved <span class="ar-pill-count">{{ $stats['approved'] }}</span>
            </button>
            <button class="ar-pill" data-filter="rejected" onclick="setFilter('rejected')">
                Rejected <span class="ar-pill-count">{{ $stats['rejected'] }}</span>
            </button>
            <button class="ar-pill" data-filter="pending" onclick="setFilter('pending')">
                Pending <span class="ar-pill-count">{{ $stats['pending'] }}</span>
            </button>
            @if($stats['overdue'] > 0)
            <button class="ar-pill" data-filter="overdue" onclick="setFilter('overdue')" style="border-color:rgba(220,38,38,0.25);color:var(--red);">
                Overdue <span class="ar-pill-count" style="background:var(--red-l);color:var(--red);">{{ $stats['overdue'] }}</span>
            </button>
            @endif
            <button class="ar-pill" data-filter="sent" onclick="setFilter('sent')">
                Sent <span class="ar-pill-count">{{ $stats['sent'] }}</span>
            </button>
        </div>
    </div>

    {{-- COURSE CARDS --}}
    <div id="ar-courses-container">

        @forelse($completedInstances as $inst)
            @php
                $enrollments = $inst->enrollments;
                $totalStudents = $enrollments->count();

                // Report status counts for progress
                $submitCount   = $enrollments->filter(fn($e) => $e->report?->status === 'Submitted')->count();
                $approveCount  = $enrollments->filter(fn($e) => in_array($e->report?->status, ['Approved','Sent']))->count();
                $rejectCount   = $enrollments->filter(fn($e) => $e->report?->status === 'Rejected')->count();

                $submitPct   = $totalStudents > 0 ? round(($submitCount / $totalStudents) * 100)   : 0;
                $approvePct  = $totalStudents > 0 ? round(($approveCount / $totalStudents) * 100)  : 0;
                $rejectPct   = $totalStudents > 0 ? round(($rejectCount / $totalStudents) * 100)   : 0;

                $endDate  = $inst->end_date ? \Carbon\Carbon::parse($inst->end_date) : null;
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

                $totalSessions   = $inst->sessions->count();
                $teacherName     = $inst->teacher?->employee?->full_name ?? 'Unknown Teacher';
            @endphp

            <div class="ar-course-card"
                 data-course-name="{{ strtolower($inst->courseTemplate?->name ?? '') }}"
                 data-course-level="{{ strtolower($inst->level?->name ?? '') }}"
                 data-teacher-name="{{ strtolower($teacherName) }}"
                 data-teacher-id="{{ $inst->teacher_id }}">

                {{-- Course Header --}}
                <div class="ar-course-head">
                    <div class="ar-course-info">
                        <div class="ar-course-meta-top">
                            @if($inst->patch)
                            <span class="ar-course-patch">{{ $inst->patch->name }}</span>
                            @endif
                            <span class="ar-course-type">{{ $inst->type ?? 'Group' }}</span>
                            <span class="ar-course-teacher">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                {{ $teacherName }}
                            </span>
                        </div>
                        <div class="ar-course-name">{{ $inst->courseTemplate?->name ?? 'Untitled Course' }}</div>
                        <div class="ar-course-level">
                            {{ $inst->level?->name ?? '—' }}
                            @if($inst->sublevel) · {{ $inst->sublevel->name }} @endif
                        </div>

                        <div class="ar-course-meta-row">
                            <div class="ar-course-meta-item">
                                <span class="ar-course-meta-label">Period</span>
                                <span class="ar-course-meta-val">
                                    {{ $inst->start_date ? \Carbon\Carbon::parse($inst->start_date)->format('d M') : '—' }}
                                    →
                                    {{ $endDate ? $endDate->format('d M Y') : '—' }}
                                </span>
                            </div>
                            <div class="ar-course-meta-item">
                                <span class="ar-course-meta-label">Sessions</span>
                                <span class="ar-course-meta-val">{{ $totalSessions }}</span>
                            </div>
                            <div class="ar-course-meta-item">
                                <span class="ar-course-meta-label">Students</span>
                                <span class="ar-course-meta-val">{{ $totalStudents }}</span>
                            </div>
                            <div class="ar-course-meta-item">
                                <span class="ar-course-meta-label">Delivery</span>
                                <span class="ar-course-meta-val">{{ $inst->delivery_mood ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="ar-deadline {{ $deadlineClass }}">
                        <div class="ar-deadline-label">{{ $deadlineLabel }}</div>
                        <div class="ar-deadline-val">{{ $deadlineVal }}</div>
                    </div>
                </div>

                {{-- Progress strip --}}
                <div class="ar-progress-strip">
                    <span class="ar-progress-label">Reports Flow</span>
                    <div class="ar-progress-track">
                        <div class="ar-progress-approved" style="width:{{ $approvePct }}%"></div>
                        <div class="ar-progress-submitted" style="width:{{ $submitPct }}%"></div>
                        <div class="ar-progress-rejected" style="width:{{ $rejectPct }}%"></div>
                    </div>
                    <div class="ar-progress-legend">
                        <span class="ar-progress-legend-item"><span class="ar-progress-dot" style="background:var(--green);"></span> {{ $approveCount }}</span>
                        <span class="ar-progress-legend-item"><span class="ar-progress-dot" style="background:var(--purple);"></span> {{ $submitCount }}</span>
                        <span class="ar-progress-legend-item"><span class="ar-progress-dot" style="background:var(--red);"></span> {{ $rejectCount }}</span>
                    </div>
                </div>

                {{-- Students table --}}
                <table class="ar-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Attendance</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th style="text-align:right;">Actions</th>
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

                                $score = $report?->total_score;
                                $scoreClass = 'na';
                                if ($score !== null) {
                                    if ($score >= 75)      $scoreClass = 'high';
                                    elseif ($score >= 50)  $scoreClass = 'mid';
                                    else                   $scoreClass = 'low';
                                }

                                $statusMap = [
                                    'Pending'   => ['ar-status-pending',   'Not Started'],
                                    'Draft'     => ['ar-status-draft',     'Draft'],
                                    'Submitted' => ['ar-status-submitted', 'Submitted'],
                                    'Approved'  => ['ar-status-approved',  'Approved'],
                                    'Rejected'  => ['ar-status-rejected',  'Rejected'],
                                    'Sent'      => ['ar-status-sent',      'Sent to Student'],
                                ];
                                [$statusClass, $statusLabel] = $statusMap[$status] ?? ['ar-status-pending','Not Started'];
                            @endphp
                            <tr class="{{ $isOverdue ? 'ar-row-overdue' : '' }}"
                                data-student-name="{{ strtolower($student?->full_name ?? '') }}"
                                data-status="{{ strtolower($status) }}"
                                data-overdue="{{ $isOverdue ? '1' : '0' }}">

                                <td class="ar-num">{{ $index + 1 }}</td>

                                <td>
                                    <div class="ar-student-name">{{ $student?->full_name ?? '—' }}</div>
                                    @if($student?->phones?->first())
                                    <div class="ar-student-phone">{{ $student->phones->first()->phone_number }}</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="ar-att-cell">
                                        <span class="ar-att-present">{{ $presentCount }}</span>
                                        <span style="color:var(--faint);">/</span>
                                        <span>{{ $totalAtt }}</span>
                                    </div>
                                </td>

                                <td>
                                    @if($score !== null)
                                        <div class="ar-score-cell {{ $scoreClass }}">{{ number_format($score, 1) }}</div>
                                        <div style="font-size:9px;color:var(--faint);letter-spacing:0.5px;">out of 100</div>
                                    @else
                                        <div class="ar-score-cell na">—</div>
                                    @endif
                                </td>

                                <td>
                                    <span class="ar-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>

                                <td>
                                    <div class="ar-submitted-at">
                                        @if($report?->submitted_at)
                                            {{ \Carbon\Carbon::parse($report->submitted_at)->format('d M Y') }}
                                            <div style="font-size:9px;color:var(--faint);margin-top:2px;">
                                                {{ \Carbon\Carbon::parse($report->submitted_at)->format('H:i') }}
                                            </div>
                                        @else
                                            <span style="color:var(--faint);">—</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="ar-action-cell">
                                    @if($report)
                                        <a href="{{ route('admin.reports.show', $report->report_id) }}" class="ar-btn ar-btn-view">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            View
                                        </a>
                                    @endif

                                    @if($status === 'Submitted')
                                        <form method="POST" action="{{ route('admin.reports.approve', $report->report_id) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="ar-btn ar-btn-approve">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                Approve
                                            </button>
                                        </form>
                                        <button type="button" class="ar-btn ar-btn-reject"
                                                onclick="openRejectModal({{ $report->report_id }}, '{{ addslashes($student?->full_name ?? '') }}')">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Reject
                                        </button>
                                    @elseif(!$report)
                                        <span class="ar-btn ar-btn-disabled" style="cursor:default;">Not started yet</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @empty
            <div class="ar-empty">
                <div class="ar-empty-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div class="ar-empty-title">No Completed Courses</div>
                <div class="ar-empty-sub">Reports become available once courses are completed. Teachers have 3 days from course end date to submit each student's report.</div>
            </div>
        @endforelse

    </div>
</div>

{{-- REJECT MODAL --}}
<div class="ar-modal-bg" id="rejectModalBg">
    <div class="ar-modal">
        <div class="ar-modal-head">
            <div class="ar-modal-title" id="rejectModalTitle">Reject Report</div>
            <button class="ar-modal-close" onclick="closeRejectModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" id="rejectForm">
            @csrf @method('PATCH')
            <div class="ar-modal-body">
                <label class="ar-modal-label">Rejection Reason <span style="color:var(--red);">*</span></label>
                <textarea name="reason" class="ar-modal-textarea" placeholder="Explain what the teacher needs to correct..." required></textarea>
            </div>
            <div class="ar-modal-foot">
                <button type="button" class="ar-modal-btn-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="ar-modal-btn-confirm">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
let activeFilter = 'all';
let activeSearch = '';

function setFilter(f) {
    activeFilter = f;
    document.querySelectorAll('.ar-pill').forEach(p => {
        p.classList.toggle('active', p.dataset.filter === f);
    });
    filterReports();
}

function filterReports() {
    activeSearch = document.getElementById('ar-search-input').value.toLowerCase().trim();

    document.querySelectorAll('.ar-course-card').forEach(card => {
        const rows = card.querySelectorAll('tbody tr');
        let visibleRows = 0;

        rows.forEach(row => {
            const studentName = row.dataset.studentName || '';
            const status      = row.dataset.status      || '';
            const isOverdue   = row.dataset.overdue     === '1';
            const courseName  = card.dataset.courseName  || '';
            const courseLevel = card.dataset.courseLevel || '';
            const teacherName = card.dataset.teacherName || '';

            let matchFilter = true;
            if (activeFilter === 'pending')   matchFilter = status === 'pending';
            if (activeFilter === 'overdue')   matchFilter = isOverdue;
            if (activeFilter === 'draft')     matchFilter = status === 'draft';
            if (activeFilter === 'submitted') matchFilter = status === 'submitted';
            if (activeFilter === 'approved')  matchFilter = status === 'approved';
            if (activeFilter === 'rejected')  matchFilter = status === 'rejected';
            if (activeFilter === 'sent')      matchFilter = status === 'sent';

            const matchSearch = !activeSearch
                || studentName.includes(activeSearch)
                || courseName.includes(activeSearch)
                || courseLevel.includes(activeSearch)
                || teacherName.includes(activeSearch);

            const show = matchFilter && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) visibleRows++;
        });

        card.style.display = visibleRows > 0 ? '' : 'none';
    });
}

function changeTeacher(id) {
    const url = new URL(window.location);
    url.searchParams.set('teacher_id', id);
    window.location = url.toString();
}

function openRejectModal(reportId, studentName) {
    document.getElementById('rejectForm').action = `/admin/reports/${reportId}/reject`;
    document.getElementById('rejectModalTitle').textContent = `Reject Report — ${studentName}`;
    document.getElementById('rejectModalBg').classList.add('open');
}

function closeRejectModal() {
    document.getElementById('rejectModalBg').classList.remove('open');
}

document.getElementById('rejectModalBg').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRejectModal();
});
</script>
@endsection