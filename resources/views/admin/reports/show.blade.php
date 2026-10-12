@extends('admin.layouts.app')
@section('title', 'Report Details')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--blue-l:rgba(27,79,168,0.10);--orange:#F5911E;--orange-dk:#C47010;--orange-l:rgba(245,145,30,0.12);--green:#15803D;--green-l:rgba(5,150,105,0.10);--red:#DC2626;--red-l:rgba(220,38,38,0.08);--purple:#7C3AED;--purple-l:rgba(124,58,237,0.10);--border:rgba(27,79,168,0.08);--bg:#F8F6F2;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;}
*{box-sizing:border-box;}
.rp-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none;}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%;}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border:1px solid var(--glass-bd);border-radius:11px;color:var(--muted);font-size:10px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;font-weight:600;box-shadow:var(--glass-sh);}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}

.layout{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;}
@media(max-width:1000px){.layout{grid-template-columns:1fr;}}

.card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;margin-bottom:20px;box-shadow:var(--glass-sh);position:relative;}
/* .card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent);} */
.card-body{padding:22px 26px;}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:16px;padding-bottom:9px;border-bottom:1px solid rgba(27,79,168,0.1);font-weight:600;}

.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;}
.info-item{}
.info-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:var(--faint);margin-bottom:4px;font-weight:600;}
.info-val{font-size:13px;color:var(--text);font-weight:500;}

.status-badge{display:inline-flex;align-items:center;gap:5px;font-size:9px;letter-spacing:1.2px;text-transform:uppercase;padding:4px 10px;border-radius:20px;font-weight:600;}
.status-badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-submitted{color:var(--orange-dk);background:var(--orange-l);border:1px solid rgba(245,145,30,0.25);}
.badge-approved{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-rejected{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.18);}
.badge-sent{color:var(--purple);background:var(--purple-l);border:1px solid rgba(124,58,237,0.2);}
.badge-draft{color:var(--muted);background:rgba(122,138,154,0.10);border:1px solid rgba(122,138,154,0.2);}

.score-table{width:100%;border-collapse:collapse;}
.score-table tr{border-bottom:1px solid rgba(27,79,168,0.06);}
.score-table tr:last-child{border-bottom:none;font-weight:700;color:var(--text);}
.score-table td{padding:10px 4px;font-size:13px;color:var(--muted);}
.score-table td:first-child{color:var(--text);}
.score-table td:last-child{text-align:right;}

.score-bar-wrap{display:flex;align-items:center;gap:8px;}
.score-track{flex:1;background:rgba(27,79,168,0.08);border-radius:6px;height:6px;overflow:hidden;}
.score-fill{height:6px;border-radius:6px;}

.total-card{background:linear-gradient(135deg,var(--blue),var(--blue-2));border-radius:16px;padding:20px;margin-top:16px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 10px 30px rgba(27,79,168,0.22);}
.total-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.65);margin-bottom:4px;}
.total-val{font-family:'Bebas Neue',sans-serif;font-size:42px;letter-spacing:3px;color:#fff;line-height:1;}
.total-grade{font-family:'Bebas Neue',sans-serif;font-size:64px;letter-spacing:2px;color:rgba(255,255,255,0.18);}

.comments-box{background:rgba(255,255,255,0.55);border:1px solid rgba(27,79,168,0.10);border-radius:12px;padding:16px;font-size:13px;color:var(--muted);line-height:1.7;white-space:pre-wrap;}

.rejection-box{background:var(--red-l);border:1px solid rgba(220,38,38,0.22);border-radius:14px;padding:14px 16px;font-size:13px;color:var(--red);margin-bottom:16px;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}

.sidebar-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;position:sticky;top:24px;box-shadow:var(--glass-sh);}
.sidebar-header{padding:16px 20px;border-bottom:1px solid var(--border);}
.sidebar-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;color:var(--text);}
.sidebar-body{padding:20px;}
.meta-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(27,79,168,0.05);font-size:12px;}
.meta-row:last-child{border-bottom:none;}
.meta-key{color:var(--faint);}
.meta-val{color:var(--text);font-weight:500;text-align:right;}

.action-area{padding:16px 20px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:10px;}
.btn-approve{width:100%;padding:12px;background:linear-gradient(120deg,#15803D,#059669);border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 10px 24px rgba(21,128,61,0.26);}
.btn-approve::before{display:none;}
.btn-approve:hover::before{transform:none;}
.btn-approve:hover{color:#fff;transform:translateY(-2px);box-shadow:0 14px 30px rgba(21,128,61,0.34);}
.btn-approve span{position:relative;z-index:1;}
.btn-reject{width:100%;padding:11px;background:rgba(255,255,255,0.55);border:1px solid rgba(220,38,38,0.3);border-radius:12px;color:var(--red);font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;font-weight:600;}
.btn-reject:hover{background:var(--red-l);}

.flash{padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.flash-success{background:var(--green-l);border-color:rgba(5,150,105,0.2);color:var(--green);}
.flash-error{background:var(--red-l);border-color:rgba(220,38,38,0.2);color:var(--red);}

.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:420px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(23,45,90,0.18);position:relative;animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
/* .modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--red),transparent);} */
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--red);}
.modal-body{padding:20px 22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;resize:vertical;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08);}
.btn-modal-cancel{padding:9px 18px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;}
.btn-modal-reject{padding:10px 22px;background:linear-gradient(120deg,#DC2626,#EF4444);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(220,38,38,0.28);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;}
@media(max-width:768px){.rp-page{padding:18px 14px;}}
</style>

<div class="rp-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin — Reports</div>
            <h1 class="page-title">Report Detail</h1>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Reports
        </a>
    </div>

    @if(session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    @php
        $enrollment = $report->enrollment;
        $comments = $report->comments;
        $rejectionNote = $report->rejection_note;
        $pct = $report->total_score;
        $grade = match(true) {
            $pct >= 90 => 'A',
            $pct >= 80 => 'B',
            $pct >= 70 => 'C',
            $pct >= 60 => 'D',
            $pct > 0   => 'F',
            default    => '—',
        };
        $instance = $enrollment?->courseInstance;
        $deadline = $instance?->end_date
            ? \Carbon\Carbon::parse($instance->end_date)->addDays(3)
            : null;
        $isOverdue = $deadline && now()->gt($deadline) && $report->status === 'Submitted';
    @endphp

    <div class="layout">

        <div>


            <div class="card">
                <div class="card-body">
                    <span class="sec-label">Student & Course</span>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Student</div>
                            <div class="info-val" style="font-size:16px;color:var(--blue);">
                                {{ $enrollment?->student?->full_name ?? '—' }}
                            </div>
                            <div style="font-size:10px;color:var(--faint);margin-top:3px;">
                                Enrollment #{{ $enrollment?->enrollment_id }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Teacher</div>
                            <div class="info-val">{{ $report->teacher?->employee?->full_name ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Course</div>
                            <div class="info-val">{{ $enrollment?->courseTemplate?->name ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Level</div>
                            <div class="info-val">
                                {{ $enrollment?->level?->name ?? '—' }}
                                @if($enrollment?->sublevel) › {{ $enrollment->sublevel->name }} @endif
                            </div>
                        </div>
                        @if($deadline)
                        <div class="info-item">
                            <div class="info-label">Deadline</div>
                            <div class="info-val" style="color:{{ $isOverdue ? 'var(--red)' : 'var(--text)' }};">
                                {{ $deadline->format('d M Y') }}
                                @if($isOverdue) <span style="font-size:9px;">⚠ Overdue</span> @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <span class="sec-label">Score Sheet</span>

                    <table class="score-table">
                        @foreach($report->reportScores as $score)
                        @php
                            $spct = $score->max_score > 0 ? ($score->student_score / $score->max_score) * 100 : 0;
                            $scolor = $spct >= 80 ? 'var(--green)' : ($spct >= 60 ? 'var(--blue)' : 'var(--red)');
                        @endphp
                        <tr>
                            <td>{{ $score->component_name }}</td>
                            <td style="min-width:120px;">
                                <div class="score-bar-wrap">
                                    <div class="score-track">
                                        <div class="score-fill" style="width:{{ $spct }}%;background:{{ $scolor }};"></div>
                                    </div>
                                </div>
                            </td>
                            <td style="white-space:nowrap;color:{{ $scolor }};font-weight:600;font-size:14px;">
                                {{ $score->student_score }} / {{ $score->max_score }}
                            </td>
                        </tr>
                        @endforeach
                        <tr style="border-top:2px solid rgba(27,79,168,0.12) !important;">
                            <td style="font-size:14px;">Total</td>
                            <td></td>
                            <td style="font-size:16px;color:var(--blue);font-weight:700;">
                                {{ $report->total_score }} / 100
                            </td>
                        </tr>
                    </table>

                    <div class="total-card">
                        <div>
                            <div class="total-label">Total Score</div>
                            <div class="total-val">{{ $report->total_score }}</div>
                            <div style="font-size:10px;color:rgba(255,255,255,0.4);margin-top:4px;">out of 100</div>
                        </div>
                        <div class="total-grade">{{ $grade }}</div>
                    </div>
                </div>
            </div>

            @if($comments)
            <div class="card">
                <div class="card-body">
                    <span class="sec-label">Instructor's Comments</span>
                    <div class="comments-box">{{ $comments }}</div>
                </div>
            </div>
            @endif

        </div>

        <div>
            <div class="sidebar-card">
                <div class="sidebar-header">
                    <div class="sidebar-title">Report Info</div>
                </div>
                <div class="sidebar-body">
                    <div class="meta-row">
                        <span class="meta-key">Status</span>
                        <span class="status-badge badge-{{ strtolower($report->status) }}">{{ $report->status }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Report ID</span>
                        <span class="meta-val">#{{ $report->report_id }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Submitted</span>
                        <span class="meta-val">{{ $report->submitted_at ? \Carbon\Carbon::parse($report->submitted_at)->format('d M Y H:i') : '—' }}</span>
                    </div>
                    @if($report->approved_at)
                    <div class="meta-row">
                        <span class="meta-key">Reviewed</span>
                        <span class="meta-val">{{ \Carbon\Carbon::parse($report->approved_at)->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    @if($report->approvedBy)
                    <div class="meta-row">
                        <span class="meta-key">Reviewed by</span>
                        <span class="meta-val">{{ $report->approvedBy->full_name }}</span>
                    </div>
                    @endif
                    @if($report->sent_at)
                    <div class="meta-row">
                        <span class="meta-key">Sent to student</span>
                        <span class="meta-val">{{ \Carbon\Carbon::parse($report->sent_at)->format('d M Y') }}</span>
                    </div>
                    @endif
                    <div class="meta-row">
                        <span class="meta-key">Score</span>
                        <span class="meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--blue);">{{ $report->total_score }}/100</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Grade</span>
                        <span class="meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--orange);">{{ $grade }}</span>
                    </div>
                </div>

                @if($report->status === 'Submitted')
                <div class="action-area">
                    <form method="POST" action="{{ route('admin.reports.approve', $report->report_id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-approve">
                            <span>✓ Approve Report</span>
                        </button>
                    </form>
                    <button class="btn-reject" onclick="document.getElementById('rejectModal').classList.add('open')">
                        ✕ Reject Report
                    </button>
                </div>
                @elseif($report->status === 'Approved')
                <div class="action-area">
                    <div style="text-align:center;padding:9px;background:var(--green-l);border:1px solid rgba(5,150,105,0.2);border-radius:12px;color:var(--green);font-weight:600;font-size:12px;letter-spacing:1px;text-transform:uppercase;">
                        ✓ Approved — awaiting teacher to send
                    </div>
                </div>
                @elseif($report->status === 'Sent')
                <div class="action-area">
                    <div style="text-align:center;padding:9px;background:var(--purple-l);border:1px solid rgba(124,58,237,0.2);border-radius:12px;color:var(--purple);font-weight:600;font-size:12px;letter-spacing:1px;text-transform:uppercase;">
                        ✓ Sent to student
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<div class="modal-backdrop" id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Reject Report</div>
            <button onclick="document.getElementById('rejectModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.reports.reject', $report->report_id) }}">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600;">Rejection Reason <span style="color:var(--red);">*</span></label>
                    <textarea name="reason" rows="4" class="form-control" placeholder="What needs to be corrected..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('rejectModal').classList.remove('open')"
                        class="btn-modal-cancel">
                    Cancel
                </button>
                <button type="submit"
                        class="btn-modal-reject">
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
</script>
@endsection