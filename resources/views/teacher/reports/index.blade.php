@extends('teacher.layouts.app')

@section('title', 'My Reports')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.trp-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0 0 24px;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);display:block;}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:16px 18px;position:relative;overflow:hidden;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:5px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

/* Overdue alert */
.overdue-alert{display:flex;align-items:center;gap:14px;background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.2);border-left:3px solid var(--red);border-radius:6px;padding:14px 18px;margin-bottom:20px;}
.overdue-alert-text{font-size:13px;color:var(--red);}

/* Pending cards */
.pending-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:28px;}
@media(max-width:800px){.pending-grid{grid-template-columns:1fr;}}
.pending-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.pending-card.overdue{border-color:rgba(220,38,38,0.3);}
.pending-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--orange),var(--blue));}
.pending-card.overdue::before{background:linear-gradient(90deg,var(--red),var(--orange));}
.pending-card-body{padding:18px 20px;}
.pending-student{font-size:15px;font-weight:600;color:var(--text);}
.pending-course{font-size:11px;color:var(--faint);margin-top:3px;}
.pending-deadline{font-size:10px;margin-top:8px;padding:4px 10px;border-radius:3px;display:inline-flex;align-items:center;gap:4px;}
.deadline-ok{background:var(--blue-l);color:var(--blue);}
.deadline-warn{background:var(--orange-l);color:#C47010;}
.deadline-over{background:var(--red-l);color:var(--red);}
.pending-card-footer{padding:12px 20px;border-top:1px solid var(--border);}

/* Reports table */
.tbl-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:14px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

/* Status */
.status-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;}
.status-badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-draft{color:var(--faint);background:rgba(170,184,200,0.1);border:1px solid rgba(170,184,200,0.2);}
.badge-submitted{color:#C47010;background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.badge-approved{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-rejected{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.badge-sent{color:var(--purple);background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}

/* Score bar */
.score-bar{display:flex;align-items:center;gap:8px;}
.score-track{flex:1;background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;min-width:50px;}
.score-fill{height:5px;border-radius:3px;}

/* Rejection note */
.rejection-box{background:var(--red-l);border:1px solid rgba(220,38,38,0.2);border-radius:4px;padding:8px 12px;font-size:11px;color:var(--red);margin-top:6px;}

/* Buttons */
.btn-primary{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:transparent;border:1.5px solid var(--blue);border-radius:4px;color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.3s;text-decoration:none;}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);}
.btn-primary:hover::before{transform:scaleX(1);}
.btn-primary:hover{color:#fff;text-decoration:none;}
.btn-primary span,.btn-primary svg{position:relative;z-index:1;}
.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;font-family:'DM Sans',sans-serif;border:1px solid;background:transparent;cursor:pointer;transition:all 0.2s;text-decoration:none;}
.btn-edit{color:var(--blue);border-color:rgba(27,79,168,0.25);}
.btn-edit:hover{background:var(--blue-l);text-decoration:none;}
.btn-send{color:var(--green);border-color:rgba(5,150,105,0.3);}
.btn-send:hover{background:var(--green-l);}
.btn-new{color:var(--orange);border-color:rgba(245,145,30,0.3);}
.btn-new:hover{background:var(--orange-l);text-decoration:none;}

@media(max-width:768px){.trp-page{padding:18px 14px;}.kpi-grid{grid-template-columns:1fr 1fr;}}
</style>

<div class="trp-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Teacher Dashboard</div>
            <h1 class="page-title">Student Reports</h1>
        </div>
        <a href="{{ route('teacher.reports.create') }}" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Report</span>
        </a>
    </div>

    @if(session('success'))
    <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('error') }}</div>
    @endif

    {{-- Overdue Warning --}}
    @if($stats['overdue'] > 0)
    <div class="overdue-alert">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:var(--red);">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
            <div class="overdue-alert-text">
                <strong>{{ $stats['overdue'] }} report{{ $stats['overdue'] > 1 ? 's' : '' }} overdue!</strong>
                The 3-day deadline has passed for these courses. Please submit them immediately.
            </div>
        </div>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--orange)"><div class="kpi-label">Pending Reports</div><div class="kpi-val">{{ $stats['pending'] }}</div></div>
        <div class="kpi-card" style="--kc:#C47010"><div class="kpi-label">Awaiting Approval</div><div class="kpi-val">{{ $stats['submitted'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Approved</div><div class="kpi-val">{{ $stats['approved'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--red)"><div class="kpi-label">Rejected</div><div class="kpi-val">{{ $stats['rejected'] }}</div></div>
    </div>

    {{-- Pending Reports --}}
    @if($pendingEnrollments->count())
    <span class="sec-label">⚠ Courses Awaiting Report</span>
    <div class="pending-grid">
        @foreach($pendingEnrollments as $e)
        @php
            $endDate = $e->courseInstance?->end_date;
            $deadline = $endDate ? \Carbon\Carbon::parse($endDate)->addDays(3) : null;
            $daysLeft = $deadline ? now()->diffInDays($deadline, false) : null;
            $isOver   = $daysLeft !== null && $daysLeft < 0;
            $isWarn   = $daysLeft !== null && $daysLeft <= 1 && !$isOver;
        @endphp
        <div class="pending-card {{ $isOver ? 'overdue' : '' }}">
            <div class="pending-card-body">
                <div class="pending-student">{{ $e->student?->full_name ?? '—' }}</div>
                <div class="pending-course">
                    {{ $e->courseTemplate?->name ?? '—' }}
                    @if($e->level) · {{ $e->level->name }} @endif
                    @if($e->sublevel) › {{ $e->sublevel->name }} @endif
                </div>
                <div style="margin-top:8px;">
                    @if($deadline)
                    <span class="pending-deadline {{ $isOver ? 'deadline-over' : ($isWarn ? 'deadline-warn' : 'deadline-ok') }}">
                        @if($isOver)
                            ⚠ Overdue — {{ abs($daysLeft) }} day{{ abs($daysLeft) != 1 ? 's' : '' }} past deadline
                        @elseif($isWarn)
                            ⚡ {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} left
                        @else
                            Deadline: {{ $deadline->format('d M Y') }}
                        @endif
                    </span>
                    @endif
                </div>
            </div>
            <div class="pending-card-footer">
                <a href="{{ route('teacher.reports.create', ['enrollment_id' => $e->enrollment_id]) }}"
                   class="btn-sm btn-new">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Write Report
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Reports Table --}}
    <span class="sec-label">All Reports</span>
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course / Level</th>
                        <th>Total Score</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    @php
                        $pct = $report->total_score;
                        $scoreColor = $pct >= 80 ? 'var(--green)' : ($pct >= 60 ? 'var(--blue)' : 'var(--red)');
                        $comments = null;
                        if ($report->rejection_note && str_starts_with($report->rejection_note, '__COMMENTS__')) {
                            $comments = substr($report->rejection_note, 12);
                        }
                        $rejectionNote = (!str_starts_with($report->rejection_note ?? '', '__COMMENTS__'))
                            ? $report->rejection_note
                            : null;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $report->enrollment?->student?->full_name ?? '—' }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:500;color:var(--text);">{{ $report->enrollment?->courseTemplate?->name ?? '—' }}</div>
                            <div style="font-size:10px;color:var(--faint);">
                                {{ $report->enrollment?->level?->name ?? '' }}
                                @if($report->enrollment?->sublevel) › {{ $report->enrollment->sublevel->name }} @endif
                            </div>
                        </td>
                        <td>
                            @if($report->total_score > 0)
                            <div class="score-bar">
                                <div class="score-track">
                                    <div class="score-fill" style="width:{{ $pct }}%;background:{{ $scoreColor }};"></div>
                                </div>
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px;color:{{ $scoreColor }};white-space:nowrap;">
                                    {{ $report->total_score }}/100
                                </span>
                            </div>
                            @else
                            <span style="color:var(--faint);font-size:11px;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge badge-{{ strtolower($report->status) }}">{{ $report->status }}</span>
                            @if($rejectionNote)
                            <div class="rejection-box">⚠ {{ $rejectionNote }}</div>
                            @endif
                        </td>
                        <td style="font-size:11px;color:var(--faint);">{{ $report->updated_at?->format('d M Y') }}</td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @if(in_array($report->status, ['Draft', 'Rejected']))
                                <a href="{{ route('teacher.reports.edit', $report->report_id) }}" class="btn-sm btn-edit">
                                    {{ $report->status === 'Rejected' ? '✏ Revise' : '✏ Edit' }}
                                </a>
                                @endif
                                @if($report->status === 'Approved')
                                <form method="POST" action="{{ route('teacher.reports.mark-sent', $report->report_id) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-sm btn-send">✓ Mark as Sent</button>
                                </form>
                                @endif
                                @if($report->status === 'Sent')
                                <span style="font-size:10px;color:var(--green);display:flex;align-items:center;gap:3px;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    Sent to student
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--faint);font-size:13px;">
                            No reports yet. Start by writing a report for a completed course.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection