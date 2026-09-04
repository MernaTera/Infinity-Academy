@extends('teacher.layouts.app')
@section('title', 'Report Details')

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
}
*{box-sizing:border-box}
.rp-page{background:var(--bg);min-height:100vh;padding:36px 32px;font-family:'DM Sans',sans-serif;color:var(--ink);}

/* Toolbar (hidden in print) */
.rp-toolbar{max-width:850px;margin:0 auto 20px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;}
.rp-back{display:inline-flex;align-items:center;gap:6px;padding:9px 14px;background:#fff;border:1px solid var(--border-2);border-radius:6px;font-size:10px;letter-spacing:1.8px;text-transform:uppercase;color:var(--muted);text-decoration:none;font-weight:600;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.rp-back:hover{color:var(--green);border-color:var(--green);text-decoration:none;}
.rp-actions{display:flex;gap:8px;}
.rp-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:6px;font-size:10px;letter-spacing:1.8px;text-transform:uppercase;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;border:1px solid;}
.rp-btn-print{background:var(--green);color:#fff;border-color:var(--green);}
.rp-btn-print:hover{background:#047857;border-color:#047857;}

/* Report Sheet */
.rp-sheet{max-width:850px;margin:0 auto;background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(27,79,168,0.05);}

/* Header banner */
.rp-header{background:linear-gradient(135deg,#059669 0%,#10B981 100%);color:#fff;padding:32px 40px;position:relative;overflow:hidden;}
.rp-header::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06);}
.rp-header::after{content:'';position:absolute;bottom:-30px;left:-30px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04);}
.rp-header-logo{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:5px;opacity:0.6;margin-bottom:4px;}
.rp-header-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:6px;line-height:1;margin-bottom:8px;position:relative;z-index:1;}
.rp-header-sub{font-size:12px;opacity:0.85;letter-spacing:0.5px;position:relative;z-index:1;}

/* Info blocks */
.rp-info{padding:28px 40px;border-bottom:1px solid var(--border);display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
.rp-info-item{display:flex;flex-direction:column;gap:4px;}
.rp-info-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:500;}
.rp-info-val{font-size:14px;color:var(--ink);font-weight:500;}

/* Sections */
.rp-section{padding:28px 40px;border-bottom:1px solid var(--border);}
.rp-section:last-child{border-bottom:none;}
.rp-section-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;color:var(--green);margin-bottom:18px;display:flex;align-items:center;gap:10px;}
.rp-section-title::after{content:'';flex:1;height:1px;background:linear-gradient(to right,rgba(5,150,105,0.2),transparent);}

/* Scores table */
.rp-scores{width:100%;border-collapse:collapse;}
.rp-scores th{padding:10px 14px;text-align:left;font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:500;background:var(--bg);border-bottom:1px solid var(--border-2);}
.rp-scores td{padding:14px;border-bottom:1px solid var(--border);font-size:13px;color:var(--ink-2);}
.rp-scores tbody tr:last-child td{border-bottom:none;}
.rp-scores .component{font-weight:500;color:var(--ink);}
.rp-scores .max-col{color:var(--faint);font-size:11px;}
.rp-scores .score-col{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:var(--ink);text-align:center;width:80px;}
.rp-scores .pct-bar-wrap{width:120px;}
.rp-scores .pct-bar-track{height:5px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;}
.rp-scores .pct-bar-fill{height:100%;border-radius:3px;transition:width 0.4s;}
.rp-scores .pct-val{font-size:10px;color:var(--muted);text-align:right;margin-top:3px;letter-spacing:0.5px;}

/* Total */
.rp-total-row{background:linear-gradient(90deg,var(--green-l),transparent);border-top:2px solid var(--green);}
.rp-total-row td{padding:18px 14px;font-weight:600;color:var(--green);}
.rp-total-row .component{color:var(--green);font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;}
.rp-total-row .score-col{color:var(--green);font-size:26px;}

/* Score circle */
.rp-score-circle-wrap{display:flex;justify-content:center;align-items:center;padding:20px 0;}
.rp-score-circle{width:160px;height:160px;position:relative;}
.rp-score-circle svg{transform:rotate(-90deg);}
.rp-score-circle-bg{fill:none;stroke:rgba(5,150,105,0.1);stroke-width:8;}
.rp-score-circle-fg{fill:none;stroke:var(--green);stroke-width:8;stroke-linecap:round;transition:stroke-dashoffset 1s ease;}
.rp-score-circle-text{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
.rp-score-num{font-family:'Bebas Neue',sans-serif;font-size:44px;letter-spacing:2px;color:var(--green);line-height:1;}
.rp-score-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-top:4px;}

/* Grade badge */
.rp-grade{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:20px;font-size:11px;letter-spacing:2px;text-transform:uppercase;font-weight:600;}
.rp-grade.excellent{background:var(--green-l);color:var(--green);}
.rp-grade.good{background:var(--blue-l);color:var(--blue);}
.rp-grade.average{background:var(--orange-l);color:#C47010;}
.rp-grade.poor{background:var(--red-l);color:var(--red);}

/* Comments */
.rp-comments{background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:16px 18px;font-size:13px;color:var(--ink-2);line-height:1.6;font-style:italic;}
.rp-comments.empty{color:var(--faint);font-style:italic;text-align:center;padding:24px;}

/* Status bar */
.rp-status-bar{padding:16px 40px;background:var(--bg);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
.rp-status-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;}
.rp-status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;}
.rp-status-draft{background:rgba(122,138,154,0.1);color:var(--muted);}
.rp-status-submitted{background:rgba(124,58,237,0.1);color:#7C3AED;}
.rp-status-approved{background:var(--green-l);color:var(--green);}
.rp-status-rejected{background:var(--red-l);color:var(--red);}
.rp-status-sent{background:var(--blue-l);color:var(--blue);}
.rp-status-meta{font-size:11px;color:var(--muted);}

/* Footer signature */
.rp-footer{padding:24px 40px;background:var(--bg);border-top:1px solid var(--border);text-align:center;font-size:10px;color:var(--faint);letter-spacing:1px;}
.rp-footer strong{color:var(--muted);font-weight:600;}

/* PRINT STYLES */
@media print {
    body{background:#fff !important;}
    .rp-toolbar,.tnav-container,#teacherSidebar,.tsb-overlay,.tsb-toggle,nav#teachNav,.rp-status-bar{display:none !important;}
    .rp-page{padding:0 !important;background:#fff !important;}
    .rp-sheet{max-width:100% !important;box-shadow:none !important;border:none !important;border-radius:0 !important;}
    .rp-header{color:#000 !important;background:#f5f5f5 !important;}
    .rp-header::before,.rp-header::after{display:none;}
    .rp-header-logo,.rp-header-title,.rp-header-sub{color:#059669 !important;}
    .rp-section-title{color:#059669 !important;}
    @page{margin:12mm;}
}

@media(max-width:700px){
    .rp-page{padding:18px 12px;}
    .rp-header{padding:24px 22px;}
    .rp-info,.rp-section{padding:20px 22px;}
    .rp-info{grid-template-columns:1fr;}
    .rp-scores th,.rp-scores td{padding:10px 8px;font-size:11px;}
    .rp-scores .pct-bar-wrap{display:none;}
}
</style>

<div class="rp-page">

    {{-- Toolbar (hidden when printing) --}}
    <div class="rp-toolbar">
        <a href="{{ route('teacher.reports.index') }}" class="rp-back">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Reports
        </a>
        <div class="rp-actions">
            <button class="rp-btn rp-btn-print" onclick="window.print()">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Download PDF / Print
            </button>
        </div>
    </div>

    {{-- Report Sheet --}}
    <div class="rp-sheet">

        {{-- Header --}}
        <div class="rp-header">
            <div class="rp-header-logo">INFINITY ACADEMY</div>
            <div class="rp-header-title">Student Report</div>
            <div class="rp-header-sub">
                {{ $report->enrollment?->courseTemplate?->name ?? 'Course' }} · {{ $report->enrollment?->level?->name ?? '' }}
                @if($report->enrollment?->sublevel) · {{ $report->enrollment->sublevel->name }} @endif
            </div>
        </div>

        {{-- Status Bar --}}
        <div class="rp-status-bar">
            @php
                $statusMap = [
                    'Draft'     => ['rp-status-draft',     'Draft'],
                    'Submitted' => ['rp-status-submitted', 'Submitted for review'],
                    'Approved'  => ['rp-status-approved',  'Approved by Admin'],
                    'Rejected'  => ['rp-status-rejected',  'Rejected — needs revision'],
                    'Sent'      => ['rp-status-sent',      'Sent to student'],
                ];
                [$stC, $stL] = $statusMap[$report->status] ?? ['rp-status-draft', $report->status];
            @endphp
            <span class="rp-status-badge {{ $stC }}">{{ $stL }}</span>

            <div class="rp-status-meta">
                @if($report->submitted_at)
                    Submitted {{ \Carbon\Carbon::parse($report->submitted_at)->format('d M Y, H:i') }}
                @endif
                @if($report->approved_at)
                    · Approved {{ \Carbon\Carbon::parse($report->approved_at)->format('d M Y, H:i') }}
                    @if($report->approvedBy)
                        by {{ $report->approvedBy->full_name ?? '' }}
                    @endif
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="rp-info">
            <div class="rp-info-item">
                <span class="rp-info-label">Student</span>
                <span class="rp-info-val">{{ $report->enrollment?->student?->full_name ?? '—' }}</span>
            </div>
            <div class="rp-info-item">
                <span class="rp-info-label">Instructor</span>
                <span class="rp-info-val">{{ $report->teacher?->employee?->full_name ?? '—' }}</span>
            </div>
            <div class="rp-info-item">
                <span class="rp-info-label">Course</span>
                <span class="rp-info-val">
                    {{ $report->enrollment?->courseTemplate?->name ?? '—' }}
                    @if($report->enrollment?->level) — {{ $report->enrollment->level->name }} @endif
                </span>
            </div>
            <div class="rp-info-item">
                <span class="rp-info-label">Patch</span>
                <span class="rp-info-val">{{ $report->enrollment?->courseInstance?->patch?->name ?? '—' }}</span>
            </div>
            <div class="rp-info-item">
                <span class="rp-info-label">Course Period</span>
                <span class="rp-info-val">
                    @php
                        $inst = $report->enrollment?->courseInstance;
                    @endphp
                    @if($inst?->start_date && $inst?->end_date)
                        {{ \Carbon\Carbon::parse($inst->start_date)->format('d M') }}
                        →
                        {{ \Carbon\Carbon::parse($inst->end_date)->format('d M Y') }}
                    @else — @endif
                </span>
            </div>
            <div class="rp-info-item">
                <span class="rp-info-label">Report Date</span>
                <span class="rp-info-val">{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y') }}</span>
            </div>
        </div>

        {{-- Scores Section --}}
        <div class="rp-section">
            <div class="rp-section-title">Score Breakdown</div>
            <table class="rp-scores">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th style="text-align:center;">Score</th>
                        <th class="max-col">Max</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalMax = 0; $totalScore = 0; @endphp
                    @foreach($report->reportScores as $sc)
                        @php
                            $pct = $sc->max_score > 0 ? round(($sc->student_score / $sc->max_score) * 100) : 0;
                            $barColor = $pct >= 75 ? 'var(--green)' : ($pct >= 50 ? 'var(--orange)' : 'var(--red)');
                            $totalMax += $sc->max_score;
                            $totalScore += $sc->student_score;
                        @endphp
                        <tr>
                            <td class="component">{{ $sc->component_name }}</td>
                            <td class="score-col">{{ $sc->student_score }}</td>
                            <td class="max-col">/ {{ $sc->max_score }}</td>
                            <td>
                                <div class="pct-bar-wrap">
                                    <div class="pct-bar-track">
                                        <div class="pct-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                                    </div>
                                    <div class="pct-val">{{ $pct }}%</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="rp-total-row">
                        <td class="component">Total Score</td>
                        <td class="score-col">{{ (int) $report->total_score }}</td>
                        <td class="max-col">/ {{ $totalMax ?: 100 }}</td>
                        <td>
                            @php
                                $totalPct = $totalMax > 0 ? round(($totalScore / $totalMax) * 100) : $report->total_score;
                                if ($totalPct >= 85)      $gradeC = 'excellent'; $gradeL = 'Excellent';
                                if ($totalPct >= 85)      { $gradeC = 'excellent'; $gradeL = 'Excellent'; }
                                elseif ($totalPct >= 70)  { $gradeC = 'good';      $gradeL = 'Good'; }
                                elseif ($totalPct >= 50)  { $gradeC = 'average';   $gradeL = 'Average'; }
                                else                       { $gradeC = 'poor';      $gradeL = 'Needs Work'; }
                            @endphp
                            <span class="rp-grade {{ $gradeC }}">{{ $gradeL }} · {{ $totalPct }}%</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Overall Score Circle --}}
        <div class="rp-section">
            <div class="rp-section-title">Overall Performance</div>
            <div class="rp-score-circle-wrap">
                <div class="rp-score-circle">
                    @php
                        $r = 70;
                        $c = 2 * pi() * $r;
                        $offset = $c - ($c * ($totalPct / 100));
                    @endphp
                    <svg width="160" height="160">
                        <circle cx="80" cy="80" r="{{ $r }}" class="rp-score-circle-bg"/>
                        <circle cx="80" cy="80" r="{{ $r }}" class="rp-score-circle-fg"
                                stroke-dasharray="{{ $c }}" stroke-dashoffset="{{ $offset }}"/>
                    </svg>
                    <div class="rp-score-circle-text">
                        <div class="rp-score-num">{{ (int) $report->total_score }}</div>
                        <div class="rp-score-label">out of {{ $totalMax ?: 100 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instructor Comments --}}
        <div class="rp-section">
            <div class="rp-section-title">Instructor Comments</div>
            @php
                $comments = null;
                if ($report->rejection_note && str_starts_with($report->rejection_note, '__COMMENTS__')) {
                    $comments = substr($report->rejection_note, 12);
                }
            @endphp
            @if($comments)
                <div class="rp-comments">{{ $comments }}</div>
            @else
                <div class="rp-comments empty">No additional comments were provided.</div>
            @endif
        </div>

        {{-- Rejection Note (if any) --}}
        @if($report->status === 'Rejected' && $report->rejection_note && !str_starts_with($report->rejection_note, '__COMMENTS__'))
        <div class="rp-section" style="background:var(--red-l);">
            <div class="rp-section-title" style="color:var(--red);">Admin's Rejection Reason</div>
            <div class="rp-comments" style="background:#fff;border-color:rgba(220,38,38,0.15);color:var(--red);font-style:normal;">
                {{ $report->rejection_note }}
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="rp-footer">
            <strong>Infinity Academy</strong> · Student Evaluation Report<br>
            Report ID: #{{ str_pad($report->report_id, 6, '0', STR_PAD_LEFT) }} · Generated {{ now()->format('d M Y, H:i') }}
        </div>

    </div>
</div>
@endsection