@extends('student-care.layouts.app')
@section('title', 'Refund Requests')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.rf-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0 0 28px;}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);display:block;margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px;}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:16px 18px;position:relative;overflow:hidden;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:5px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

/* Policy box */
.policy-box{background:var(--blue-l);border:1px solid var(--border);border-left:3px solid var(--blue);border-radius:6px;padding:14px 18px;margin-bottom:24px;font-size:13px;color:var(--text);}
.policy-box strong{color:var(--blue);}

/* Eligible cards */
.eligible-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:28px;}
@media(max-width:900px){.eligible-grid{grid-template-columns:1fr;}}

.el-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(27,79,168,0.04);position:relative;}
.el-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--green),var(--blue));}
.el-card-body{padding:18px 20px;}
.el-student{font-size:15px;font-weight:600;color:var(--text);margin-bottom:3px;}
.el-course{font-size:12px;color:var(--faint);}
.el-meta{display:flex;gap:14px;margin-top:12px;flex-wrap:wrap;}
.el-meta-item{font-size:11px;color:var(--muted);}
.el-meta-item strong{color:var(--text);}

/* Timer badge */
.timer-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:4px;font-size:10px;font-weight:600;letter-spacing:0.5px;}
.timer-ok{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.2);}
.timer-warn{background:var(--orange-l);color:#C47010;border:1px solid rgba(245,145,30,0.2);}
.timer-urgent{background:var(--red-l);color:var(--red);border:1px solid rgba(220,38,38,0.2);}

.el-card-footer{padding:12px 20px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:10px;}
.amount-tag{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;color:var(--blue);}

/* Request button */
.btn-request{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:transparent;border:1.5px solid var(--red);border-radius:4px;color:var(--red);font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;}
.btn-request:hover{background:var(--red-l);}
.btn-requested{color:var(--faint);border-color:rgba(170,184,200,0.3);cursor:default;}
.btn-requested:hover{background:transparent;}

/* Table */
.tbl-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:20px;}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:13px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

/* Status */
.status-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:500;}
.status-badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-pending{color:#C47010;background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.badge-approved{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-rejected{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.badge-processed{color:var(--purple);background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}

/* Modal */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.45);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:460px;background:var(--bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 24px 60px rgba(27,79,168,0.15);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--red),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--red);}
.modal-body{padding:22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}
.form-field{display:flex;flex-direction:column;gap:5px;margin-bottom:14px;}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;resize:vertical;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
.form-control:read-only{background:#F9F9F9;color:var(--faint);}

.info-box{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);border-radius:4px;padding:10px 14px;font-size:12px;color:var(--red);margin-bottom:14px;}

@media(max-width:768px){.rf-page{padding:18px 14px;}.kpi-grid{grid-template-columns:1fr 1fr;}}
</style>

<div class="rf-page">
    <div class="page-eyebrow">Student Care — Finance</div>
    <h1 class="page-title">Refund Requests</h1>

    @if(session('success'))
    <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('error') }}</div>
    @endif

    {{-- Policy Notice --}}
    <div class="policy-box">
        <strong>Refund Policy:</strong> Students are eligible for a <strong>full deposit refund</strong> within <strong>3 days</strong> of payment.
        After 3 days, no refund is applicable. Refund requests require admin approval before processing.
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Eligible Now</div><div class="kpi-val">{{ $stats['eligible'] }}</div></div>
        <div class="kpi-card" style="--kc:#C47010"><div class="kpi-label">Pending</div><div class="kpi-val">{{ $stats['pending'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Approved</div><div class="kpi-val">{{ $stats['approved'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--purple)"><div class="kpi-label">Processed</div><div class="kpi-val">{{ $stats['processed'] }}</div></div>
    </div>

    {{-- Eligible Enrollments --}}
    @if($eligibleEnrollments->count())
    <span class="sec-label">Eligible for Refund</span>
    <div class="eligible-grid">
        @foreach($eligibleEnrollments as $enrollment)
        @php
            $deposit    = $enrollment->financialTransactions->first();
            $daysAgo    = $deposit ? (int) $deposit->created_at->diffInDays(now()) : 0;
            $hoursLeft = $deposit ? max(0, 72 - (int)$deposit->created_at->diffInHours(now())) : 0;
            $timerClass = $hoursLeft > 24 ? 'timer-ok' : ($hoursLeft > 6 ? 'timer-warn' : 'timer-urgent');
            $hasPending = $enrollment->refundRequests->whereIn('status', ['Pending','Approved'])->count() > 0;
        @endphp
        <div class="el-card">
            <div class="el-card-body">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:8px;">
                    <div>
                        <div class="el-student">{{ $enrollment->student?->full_name ?? '—' }}</div>
                        <div class="el-course">
                            {{ $enrollment->courseTemplate?->name ?? '—' }}
                            @if($enrollment->level) · {{ $enrollment->level->name }} @endif
                        </div>
                    </div>
                    <span class="timer-badge {{ $timerClass }}">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $hoursLeft }}h left
                    </span>
                </div>
                <div class="el-meta">
                    <div class="el-meta-item">Paid: <strong>{{ $deposit ? $deposit->created_at->format('d M Y H:i') : '—' }}</strong></div>
                    <div class="el-meta-item">Method: <strong>{{ $deposit?->payment_method ?? '—' }}</strong></div>
                    <div class="el-meta-item">Day {{ $daysAgo + 1 }} of 3</div>
                </div>
            </div>
            <div class="el-card-footer">
                <div class="amount-tag">{{ number_format($deposit?->amount ?? 0, 0) }} LE</div>
                @if($hasPending)
                <button class="btn-request btn-requested" disabled>
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Requested
                </button>
                @else
                <button class="btn-request"
                        onclick="openModal({{ $enrollment->enrollment_id }}, '{{ addslashes($enrollment->student?->full_name) }}', '{{ $enrollment->courseTemplate?->name }}', {{ $deposit?->amount ?? 0 }})">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Request Refund
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:40px;text-align:center;color:var(--faint);font-size:13px;margin-bottom:28px;">
        No enrollments currently eligible for refund.
    </div>
    @endif

    {{-- My Requests History --}}
    <span class="sec-label">My Refund Requests</span>
    <div class="tbl-card">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myRequests as $req)
                <tr>
                    <td style="font-weight:600;color:var(--text);">{{ $req->enrollment?->student?->full_name ?? '—' }}</td>
                    <td>{{ $req->enrollment?->courseTemplate?->name ?? '—' }}</td>
                    <td style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--blue);letter-spacing:1px;">
                        {{ number_format($req->amount, 0) }} LE
                    </td>
                    <td><span class="status-badge badge-{{ strtolower($req->status) }}">{{ $req->status }}</span></td>
                    <td style="font-size:11px;color:var(--faint);">{{ $req->created_at?->format('d M Y H:i') }}</td>
                    <td style="font-size:11px;color:var(--faint);">
                        @if($req->rejection_note)
                        <span style="color:var(--red);">{{ $req->rejection_note }}</span>
                        @else
                        {{ $req->reason ?? '—' }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--faint);">No refund requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Request Modal --}}
<div class="modal-backdrop" id="refundModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Request Refund</div>
            <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('student-care.refunds.store') }}">
            @csrf
            <input type="hidden" name="enrollment_id" id="modal_enrollment_id">
            <div class="modal-body">
                <div class="info-box">
                    ⚠ This will request a <strong>full deposit refund</strong>. Once submitted, it requires admin approval before processing.
                </div>
                <div class="form-field">
                    <label class="form-label">Student</label>
                    <input type="text" id="modal_student" class="form-control" readonly>
                </div>
                <div class="form-field">
                    <label class="form-label">Course</label>
                    <input type="text" id="modal_course" class="form-control" readonly>
                </div>
                <div class="form-field">
                    <label class="form-label">Refund Amount</label>
                    <input type="text" id="modal_amount" class="form-control" readonly>
                </div>
                <div class="form-field">
                    <label class="form-label">Reason <span style="color:var(--red);">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Reason for refund request..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()"
                        style="padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:10px 22px;background:var(--red);border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(enrollmentId, student, course, amount) {
    document.getElementById('modal_enrollment_id').value = enrollmentId;
    document.getElementById('modal_student').value       = student;
    document.getElementById('modal_course').value        = course;
    document.getElementById('modal_amount').value        = amount.toLocaleString() + ' LE';
    document.getElementById('refundModal').classList.add('open');
}
function closeModal() {
    document.getElementById('refundModal').classList.remove('open');
}
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endsection