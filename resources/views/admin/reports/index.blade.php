@extends('admin.layouts.app')
@section('title', 'Student Reports')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.rp-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0 0 24px;}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:16px 18px;position:relative;overflow:hidden;cursor:pointer;text-decoration:none;display:block;transition:all 0.2s;}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(27,79,168,0.1);text-decoration:none;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-card.active-filter{box-shadow:0 0 0 2px var(--kc);}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:5px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

/* Table */
.tbl-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:14px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

/* Status badges */
.status-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:500;}
.status-badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-submitted{color:#C47010;background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.badge-approved{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-rejected{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.badge-sent{color:var(--purple);background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}
.badge-draft{color:var(--faint);background:rgba(170,184,200,0.1);border:1px solid rgba(170,184,200,0.2);}

/* Score bar */
.score-bar{display:flex;align-items:center;gap:8px;}
.score-track{flex:1;background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;min-width:60px;}
.score-fill{height:5px;border-radius:3px;}

/* Buttons */
.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;font-family:'DM Sans',sans-serif;border:1px solid;background:transparent;cursor:pointer;transition:all 0.2s;}
.btn-approve{color:var(--green);border-color:rgba(5,150,105,0.3);}
.btn-approve:hover{background:var(--green-l);}
.btn-reject{color:var(--red);border-color:rgba(220,38,38,0.2);}
.btn-reject:hover{background:var(--red-l);}
.btn-view{color:var(--blue);border-color:rgba(27,79,168,0.25);}
.btn-view:hover{background:var(--blue-l);}

/* Modal */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.5);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:440px;background:var(--bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 24px 60px rgba(27,79,168,0.15);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--red),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--red);}
.modal-body{padding:20px 22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}
.form-control{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;box-sizing:border-box;resize:vertical;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}

/* Score preview panel */
.score-panel{background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:14px 16px;margin-top:8px;}
.score-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid rgba(27,79,168,0.04);font-size:12px;}
.score-row:last-child{border-bottom:none;font-weight:600;color:var(--text);}
.score-component{color:var(--muted);}
.score-val{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;}

@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(3,1fr);}.rp-page{padding:18px 14px;}}
</style>

<div class="rp-page">
    <div class="page-eyebrow">Admin Panel</div>
    <h1 class="page-title">Student Reports</h1>

    @if(session('success'))
    <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('error') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <a href="{{ route('admin.reports.index', ['status'=>'Submitted']) }}"
           class="kpi-card {{ $filterStatus==='Submitted'?'active-filter':'' }}" style="--kc:#C47010">
            <div class="kpi-label">Pending Review</div>
            <div class="kpi-val">{{ $stats['submitted'] }}</div>
        </a>
        <a href="{{ route('admin.reports.index', ['status'=>'Approved']) }}"
           class="kpi-card {{ $filterStatus==='Approved'?'active-filter':'' }}" style="--kc:var(--green)">
            <div class="kpi-label">Approved</div>
            <div class="kpi-val">{{ $stats['approved'] }}</div>
        </a>
        <a href="{{ route('admin.reports.index', ['status'=>'Rejected']) }}"
           class="kpi-card {{ $filterStatus==='Rejected'?'active-filter':'' }}" style="--kc:var(--red)">
            <div class="kpi-label">Rejected</div>
            <div class="kpi-val">{{ $stats['rejected'] }}</div>
        </a>
        <a href="{{ route('admin.reports.index', ['status'=>'Sent']) }}"
           class="kpi-card {{ $filterStatus==='Sent'?'active-filter':'' }}" style="--kc:var(--purple)">
            <div class="kpi-label">Sent to Students</div>
            <div class="kpi-val">{{ $stats['sent'] }}</div>
        </a>
        <a href="{{ route('admin.reports.index', ['status'=>'all']) }}"
           class="kpi-card {{ $filterStatus==='all'?'active-filter':'' }}" style="--kc:var(--blue)">
            <div class="kpi-label">All Reports</div>
            <div class="kpi-val">{{ array_sum([$stats['submitted'],$stats['approved'],$stats['rejected'],$stats['sent']]) }}</div>
        </a>
    </div>

    {{-- Table --}}
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course / Level</th>
                        <th>Teacher</th>
                        <th>Total Score</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Deadline</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    @php
                        $pct = $report->total_score;
                        $scoreColor = $pct >= 80 ? 'var(--green)' : ($pct >= 60 ? 'var(--blue)' : 'var(--red)');
                        $enrollment = $report->enrollment;
                        $instance = $enrollment?->courseInstance;
                        $deadline = $instance?->end_date
                            ? \Carbon\Carbon::parse($instance->end_date)->addDays(3)
                            : null;
                        $isOverdue = $deadline && now()->gt($deadline) && $report->status === 'Submitted';
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $enrollment?->student?->full_name ?? '—' }}</div>
                            <div style="font-size:10px;color:var(--faint);margin-top:2px;">#{{ $enrollment?->enrollment_id }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:var(--text);font-weight:500;">{{ $enrollment?->courseTemplate?->name ?? '—' }}</div>
                            <div style="font-size:10px;color:var(--faint);margin-top:2px;">
                                {{ $enrollment?->level?->name ?? '' }}
                                @if($enrollment?->sublevel) › {{ $enrollment->sublevel->name }} @endif
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $report->teacher?->employee?->full_name ?? '—' }}</td>
                        <td style="min-width:120px;">
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
                            <span style="color:var(--faint);font-size:11px;">Not scored yet</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge badge-{{ strtolower($report->status) }}">{{ $report->status }}</span>
                            @if($isOverdue)
                            <div style="display:inline-flex;align-items:center;gap:3px;margin-top:4px;font-size:9px;color:var(--red);letter-spacing:1px;text-transform:uppercase;">
                                ⚠ Overdue
                            </div>
                            @endif
                        </td>
                        <td style="font-size:11px;color:var(--faint);">
                            {{ $report->submitted_at ? \Carbon\Carbon::parse($report->submitted_at)->format('d M Y') : '—' }}
                        </td>
                        <td style="font-size:11px;">
                            @if($deadline)
                            <span style="color:{{ $isOverdue ? 'var(--red)' : 'var(--faint)' }};">
                                {{ $deadline->format('d M Y') }}
                            </span>
                            @else
                            <span style="color:var(--faint);">—</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <a href="{{ route('admin.reports.show', $report->report_id) }}" class="btn-sm btn-view">
                                    View
                                </a>
                                @if($report->status === 'Submitted')
                                <form method="POST" action="{{ route('admin.reports.approve', $report->report_id) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-sm btn-approve">✓ Approve</button>
                                </form>
                                <button class="btn-sm btn-reject" onclick="openReject({{ $report->report_id }})">✕ Reject</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--faint);font-size:13px;">
                            No reports found for this filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal-backdrop" id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Reject Report</div>
            <button onclick="document.getElementById('rejectModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);">Rejection Reason <span style="color:var(--red);">*</span></label>
                    <textarea name="reason" rows="4" class="form-control" placeholder="Explain what needs to be corrected..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('rejectModal').classList.remove('open')"
                        style="padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:10px 22px;background:var(--red);border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;">
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReject(id) {
    document.getElementById('rejectForm').action = `/admin/reports/${id}/reject`;
    document.getElementById('rejectModal').classList.add('open');
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('rejectModal').classList.remove('open');
});
</script>
@endsection