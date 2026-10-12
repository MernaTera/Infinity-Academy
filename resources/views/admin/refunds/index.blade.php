@extends('admin.layouts.app')
@section('title', 'Refund Requests')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--blue-l:rgba(27,79,168,0.10);--orange:#F5911E;--orange-dk:#C47010;--orange-l:rgba(245,145,30,0.12);--green:#059669;--green-dk:#15803D;--green-l:rgba(5,150,105,0.10);--red:#DC2626;--red-l:rgba(220,38,38,0.08);--purple:#7C3AED;--purple-l:rgba(124,58,237,0.10);--border:rgba(27,79,168,0.08);--bg:#F8F6F2;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;}
*{box-sizing:border-box;}
.rf-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none;}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%;}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0 0 28px;}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px;}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 18px;position:relative;overflow:hidden;cursor:pointer;text-decoration:none;display:block;transition:all 0.2s;box-shadow:var(--glass-sh);}
.kpi-card:hover{transform:translateY(-3px);box-shadow:0 16px 38px rgba(23,45,90,0.13);text-decoration:none;}
.kpi-card.active-filter{box-shadow:0 0 0 2px var(--kc),var(--glass-sh);}
.kpi-card::before{display:none;}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

.tbl-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.03);}
.tbl td{padding:14px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

.status-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600;}
.status-badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-pending{color:var(--orange-dk);background:var(--orange-l);border:1px solid rgba(245,145,30,0.25);}
.badge-approved{color:var(--green-dk);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-rejected{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.18);}
.badge-processed{color:var(--purple);background:var(--purple-l);border:1px solid rgba(124,58,237,0.2);}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;font-family:'DM Sans',sans-serif;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;transition:all 0.2s;font-weight:600;}
.btn-approve{color:var(--green-dk);border-color:rgba(5,150,105,0.3);}
.btn-approve:hover{background:var(--green-l);}
.btn-reject{color:var(--red);border-color:rgba(220,38,38,0.22);}
.btn-reject:hover{background:var(--red-l);}

.flash{padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.flash-success{background:var(--green-l);border-color:rgba(5,150,105,0.2);color:var(--green-dk);}
.flash-error{background:var(--red-l);border-color:rgba(220,38,38,0.2);color:var(--red);}

.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:440px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(23,45,90,0.18);position:relative;animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;}
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
.btn-modal-approve{padding:10px 22px;background:linear-gradient(120deg,#15803D,#059669);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(21,128,61,0.26);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;}

.filter-tabs{display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap;}
.filter-tab{padding:6px 16px;border-radius:10px;font-size:10px;letter-spacing:2px;text-transform:uppercase;border:1px solid var(--glass-bd);background:rgba(255,255,255,0.55);color:var(--muted);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;font-weight:600;}
.filter-tab.active,.filter-tab:hover{border-color:transparent;color:#fff;background:linear-gradient(120deg,var(--blue),var(--blue-2));}

@media(max-width:900px){.kpi-grid{grid-template-columns:1fr 1fr;}.rf-page{padding:18px 14px;}}
</style>

<div class="rf-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="page-eyebrow">Admin — Finance</div>
    <h1 class="page-title">Refund Requests</h1>

    @if(session('success'))
    <div class="flash flash-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--orange-dk)" onclick="filterTable('Pending')">
            <div class="kpi-label">Pending</div>
            <div class="kpi-val">{{ $stats['pending'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--red)" onclick="filterTable('Rejected')">
            <div class="kpi-label">Rejected</div>
            <div class="kpi-val">{{ $stats['rejected'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--blue)" onclick="filterTable('all')">
            <div class="kpi-label">Total</div>
            <div class="kpi-val">{{ $requests->count() }}</div>
        </div>
    </div>

    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl" id="refundTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Requested By</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr data-status="{{ $req->status }}">
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $req->enrollment?->student?->full_name ?? '—' }}</div>
                            <div style="font-size:10px;color:var(--faint);">Enrollment #{{ $req->enrollment_id }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:var(--text);font-weight:500;">{{ $req->enrollment?->courseTemplate?->name ?? '—' }}</div>
                            @if($req->enrollment?->level)
                            <div style="font-size:10px;color:var(--faint);">{{ $req->enrollment->level->name }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--red);letter-spacing:1px;">
                                {{ number_format($req->amount, 0) }} LE
                            </span>
                        </td>
                        <td style="font-size:12px;">{{ $req->requestedBy?->full_name ?? '—' }}</td>
                        <td style="max-width:200px;font-size:12px;">
                            {{ Str::limit($req->reason, 60) }}
                            @if($req->rejection_note)
                            <div style="color:var(--red);font-size:10px;margin-top:3px;">Rejected: {{ Str::limit($req->rejection_note, 50) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge badge-{{ strtolower($req->status) }}">{{ $req->status }}</span>
                        </td>
                        <td style="font-size:11px;color:var(--faint);">
                            {{ $req->created_at?->format('d M Y H:i') }}
                            @if($req->approved_at)
                            <div style="margin-top:2px;">→ {{ $req->approved_at->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($req->status === 'Pending')
                            <div style="display:flex;gap:6px;">
                                <button class="btn-sm btn-approve"
                                        onclick="openApprove({{ $req->request_id }}, '{{ addslashes($req->enrollment?->student?->full_name) }}', {{ $req->amount }})">
                                    ✓ Approve
                                </button>
                                <button class="btn-sm btn-reject" onclick="openReject({{ $req->request_id }})">✕ Reject</button>
                            </div>
                            @elseif($req->status === 'Processed')
                            <span style="font-size:10px;color:var(--green-dk);font-weight:600;">✓ Done</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--faint);font-size:13px;">No refund requests yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Reject Refund</div>
            <button onclick="document.getElementById('rejectModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600;">Rejection Reason <span style="color:var(--red);">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Why is this refund being rejected..." required></textarea>
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

<div class="modal-backdrop" id="approveModal">
    <div class="modal-box" style="max-width:420px;">
        <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--green-dk),transparent);z-index:1;"></div>
        <div class="modal-header">
            <div class="modal-title" style="color:var(--green-dk);">Confirm Refund</div>
            <button onclick="document.getElementById('approveModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.22);border-radius:12px;padding:14px 16px;margin-bottom:16px;font-size:13px;color:var(--green-dk);">
                <strong>Full Deposit Refund</strong>
            </div>
            <div style="font-size:13px;color:var(--muted);line-height:1.7;">
                Approving refund of
                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--red);letter-spacing:1px;" id="approve_amount">—</span>
                for <strong id="approve_name" style="color:var(--text);">—</strong>.
                <div style="margin-top:10px;padding:10px 14px;background:var(--red-l);border:1px solid rgba(220,38,38,0.18);border-radius:12px;font-size:12px;color:var(--red);">
                    ⚠ This action will <strong>cancel the enrollment</strong> and cannot be undone.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="document.getElementById('approveModal').classList.remove('open')"
                    class="btn-modal-cancel">
                Cancel
            </button>
            <form id="approveForm" method="POST" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit"
                        class="btn-modal-approve">
                    ✓ Confirm Refund
                </button>
            </form>
        </div>
    </div>
</div>
<script>
function openReject(id) {
    document.getElementById('rejectForm').action = `/admin/refunds/${id}/reject`;
    document.getElementById('rejectModal').classList.add('open');
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.getElementById('rejectModal').classList.remove('open'); });

function filterTable(status, btn) {
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    document.querySelectorAll('#refundTable tbody tr').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
}
function openApprove(id, name, amount) {
    document.getElementById('approveForm').action = `/admin/refunds/${id}/approve`;
    document.getElementById('approve_name').textContent   = name;
    document.getElementById('approve_amount').textContent = Number(amount).toLocaleString() + ' LE';
    document.getElementById('approveModal').classList.add('open');
}
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});

document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('notification-received', function(e) {
        const data = e.detail || {};
        
        if (data.entity_type === 'refund_request') {
            setTimeout(() => window.location.reload(), 1500);
        }
    });
});
</script>
@endsection