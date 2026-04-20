@extends('teacher.layouts.app')
@section('title', 'Reports')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.rep-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#059669;margin:0}
.page-header{margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px}
.kpi-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#059669)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#059669);line-height:1}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:14px;display:block;padding-bottom:8px;border-bottom:1px solid rgba(5,150,105,0.1)}

/* Course Section */
.course-section{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden;margin-bottom:16px}
.cs-header{padding:16px 20px;border-bottom:1px solid rgba(5,150,105,0.06);display:flex;align-items:center;justify-content:space-between;cursor:pointer;transition:background 0.2s}
.cs-header:hover{background:rgba(5,150,105,0.02)}
.cs-course-name{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1A2A4A}
.cs-meta{font-size:11px;color:#7A8A9A;margin-top:3px}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:500;white-space:nowrap}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-pending{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}
.badge-submitted{color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15)}
.badge-approved{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-rejected{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}

/* Student Report Row */
.student-rep-row{display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid rgba(5,150,105,0.04);transition:background 0.2s}
.student-rep-row:last-child{border-bottom:none}
.student-rep-row:hover{background:rgba(5,150,105,0.02)}
.srr-name{font-weight:500;color:#1A2A4A;font-size:13px;flex:1}
.srr-score{font-family:'Bebas Neue',sans-serif;font-size:20px;color:#1A2A4A;letter-spacing:1px;min-width:50px;text-align:center}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:6px 14px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all 0.2s;white-space:nowrap}
.btn-write{color:#059669;border-color:rgba(5,150,105,0.25)}
.btn-write:hover{background:rgba(5,150,105,0.07);text-decoration:none}
.btn-edit{color:#C47010;border-color:rgba(245,145,30,0.2)}
.btn-edit:hover{background:rgba(245,145,30,0.06);text-decoration:none}
.btn-view{color:#1B4FA8;border-color:rgba(27,79,168,0.2)}
.btn-view:hover{background:rgba(27,79,168,0.06);text-decoration:none}

/* Deadline */
.deadline-warn{font-size:10px;color:#DC2626;letter-spacing:1px;text-transform:uppercase}
.deadline-ok{font-size:10px;color:#AAB8C8;letter-spacing:1px}

.empty-state{text-align:center;padding:60px;background:#fff;border:1px solid rgba(5,150,105,0.08);border-radius:8px}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:#AAB8C8;margin-bottom:6px}

@media(max-width:768px){.rep-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="rep-page">

    <div class="page-header">
        <div class="page-eyebrow">Instructor</div>
        <h1 class="page-title">Reports</h1>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Pending</div>
            <div class="kpi-val">{{ $stats['pending'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Submitted</div>
            <div class="kpi-val">{{ $stats['submitted'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Approved</div>
            <div class="kpi-val">{{ $stats['approved'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Rejected</div>
            <div class="kpi-val">{{ $stats['rejected'] }}</div>
        </div>
    </div>

    <span class="sec-label">Completed Courses</span>

    @if($completedInstances->isEmpty())
    <div class="empty-state">
        <div class="empty-title">No Completed Courses</div>
        <div style="font-size:12px;color:#AAB8C8">Reports will appear here after course completion</div>
    </div>
    @else
    @foreach($completedInstances as $instance)
    @php
        $deadline    = \Carbon\Carbon::parse($instance->end_date)->addDays(3);
        $isLate      = now()->gt($deadline);
        $daysLeft    = (int)now()->diffInDays($deadline, false);
    @endphp
    <div class="course-section">
        <div class="cs-header" onclick="toggleSection('sec_{{ $instance->course_instance_id }}', 'chev_{{ $instance->course_instance_id }}')">
            <div>
                <div class="cs-course-name">{{ $instance->courseTemplate?->name ?? '—' }}</div>
                <div class="cs-meta">
                    @if($instance->level) {{ $instance->level->name }} · @endif
                    {{ $instance->type }} · {{ $instance->patch?->name ?? '—' }}
                    · Ended {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                @if($isLate)
                <span class="deadline-warn">⚠ Overdue</span>
                @elseif($daysLeft <= 3)
                <span class="deadline-warn">{{ $daysLeft }}d left</span>
                @else
                <span class="deadline-ok">Due {{ $deadline->format('d M') }}</span>
                @endif
                <svg id="chev_{{ $instance->course_instance_id }}" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2" style="transition:transform 0.2s"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        <div id="sec_{{ $instance->course_instance_id }}">
            @foreach($instance->enrollments as $enrollment)
            @php
                $report = $enrollment->report;
                $status = $report?->status ?? 'Draft';
                $statusBadge = match($status) {
                    'Draft'     => 'badge-pending',
                    'Submitted' => 'badge-submitted',
                    'Approved'  => 'badge-approved',
                    'Rejected'  => 'badge-rejected',
                    default     => 'badge-pending',
                };
                $statusLabel = match($status) {
                    'Draft'     => 'Not Submitted',
                    'Submitted' => 'Under Review',
                    'Approved'  => 'Approved',
                    'Rejected'  => 'Rejected',
                    default     => 'Pending',
                };
            @endphp
            <div class="student-rep-row">
                <div style="width:30px;height:30px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:12px;color:#059669;flex-shrink:0">
                    {{ strtoupper(substr($enrollment->student?->full_name ?? '?', 0, 1)) }}
                </div>
                <div style="flex:1">
                    <div class="srr-name">{{ $enrollment->student?->full_name ?? '—' }}</div>
                </div>

                @if($report)
                <div class="srr-score" style="color:{{ $report->total_score >= 60 ? '#059669' : '#DC2626' }}">
                    {{ $report->total_score }}<span style="font-size:12px;color:#AAB8C8">/100</span>
                </div>
                @endif

                <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>

                @if($report?->rejection_note)
                <div style="font-size:10px;color:#DC2626;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $report->rejection_note }}">
                    {{ Str::limit($report->rejection_note, 30) }}
                </div>
                @endif

                <div style="display:flex;gap:6px">
                    @if(!$report || $status === 'Draft')
                    <a href="{{ route('teacher.reports.create', $instance->course_instance_id) }}?enrollment={{ $enrollment->enrollment_id }}"
                       class="btn-sm btn-write">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Write Report
                    </a>
                    @elseif($status === 'Rejected')
                    <a href="{{ route('teacher.reports.edit', $report->report_id) }}" class="btn-sm btn-edit">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Resubmit
                    </a>
                    @else
                    <span class="btn-sm btn-view" style="cursor:default;opacity:0.7">
                        {{ $status === 'Submitted' ? 'Pending Admin' : 'Approved' }}
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif

</div>

<script>
function toggleSection(id, chevId) {
    const sec   = document.getElementById(id);
    const chev  = document.getElementById(chevId);
    const show  = sec.style.display === 'none';
    sec.style.display = show ? '' : 'none';
    if (chev) chev.style.transform = show ? 'rotate(180deg)' : '';
}
</script>
@endsection