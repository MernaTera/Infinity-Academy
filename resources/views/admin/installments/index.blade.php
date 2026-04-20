@extends('admin.layouts.app')
@section('title', 'Installment Approvals')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.inst-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:12px;display:block}

.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:24px}
.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:13px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-pending{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}
.badge-approved{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-rejected{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.btn-approve{color:#059669;border-color:rgba(5,150,105,0.25)}
.btn-approve:hover{background:rgba(5,150,105,0.07)}
.btn-reject{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-reject:hover{background:rgba(220,38,38,0.06)}

/* Reject Modal */
#rejectModal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
#rejectModal.show{display:flex}
.modal-box{width:100%;max-width:440px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18)}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#DC2626,transparent)}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.07)}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#DC2626}
.modal-body{padding:18px 22px}
.modal-footer{padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}

@media(max-width:768px){.inst-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(3,1fr)}}
</style>

<div class="inst-page">

    <div class="page-header">
        <div class="page-eyebrow">Admin Panel</div>
        <h1 class="page-title">Installment Approvals</h1>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#C47010"><div class="kpi-label">Pending</div><div class="kpi-val">{{ $stats['pending'] }}</div></div>
        <div class="kpi-card" style="--kc:#059669"><div class="kpi-label">Approved</div><div class="kpi-val">{{ $stats['approved'] }}</div></div>
        <div class="kpi-card" style="--kc:#DC2626"><div class="kpi-label">Rejected</div><div class="kpi-val">{{ $stats['rejected'] }}</div></div>
    </div>

    {{-- Pending --}}
    <span class="sec-label">Pending Requests</span>
    <div class="tbl-card">
        <div style="overflow-x:auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Patch</th>
                        <th>Current Plan</th>
                        <th>Requested Plan</th>
                        <th>CS Reason</th>
                        <th>Requested</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $log)
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#1A2A4A">{{ $log->enrollment?->student?->full_name ?? '—' }}</div>
                        </td>
                        <td style="font-size:12px">{{ $log->enrollment?->courseInstance?->courseTemplate?->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#7A8A9A">{{ $log->enrollment?->courseInstance?->patch?->name ?? '—' }}</td>
                        <td>
                            <span style="font-size:11px;color:#7A8A9A">{{ $log->old_plan ?? '—' }}</span>
                        </td>
                        <td>
                            <span style="font-size:11px;color:#1B4FA8;font-weight:500">{{ $log->new_plan ?? '—' }}</span>
                        </td>
                        <td style="font-size:11px;color:#7A8A9A;max-width:180px">
                            {{ Str::limit($log->cs_reason ?? '—', 50) }}
                        </td>
                        <td style="font-size:10px;color:#AAB8C8">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <form method="POST" action="{{ route('admin.installments.approve', $log->approval_id) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-sm btn-approve">✓ Approve</button>
                                </form>
                                <button class="btn-sm btn-reject" onclick="openReject({{ $log->approval_id }})">✕ Reject</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#AAB8C8;font-size:13px">
                            No pending requests
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- History --}}
    <span class="sec-label">Recent History</span>
    <div class="tbl-card">
        <div style="overflow-x:auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $log)
                    <tr>
                        <td style="font-weight:500;color:#1A2A4A">{{ $log->enrollment?->student?->full_name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $log->status === 'Approved' ? 'badge-approved' : 'badge-rejected' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#7A8A9A">{{ $log->approvedBy?->full_name ?? '—' }}</td>
                        <td style="font-size:10px;color:#AAB8C8">{{ \Carbon\Carbon::parse($log->approved_at)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:30px;color:#AAB8C8;font-size:12px">No history yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Reject Modal --}}
<div id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Reject Request</div>
        </div>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:5px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Rejection Reason</label>
                    <textarea name="reason" rows="3"
                        style="width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;resize:none;box-sizing:border-box"
                        placeholder="Explain the reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeReject()"
                    style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 22px;background:#DC2626;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReject(id) {
    document.getElementById('rejectForm').action = `/admin/installments/${id}/reject`;
    document.getElementById('rejectModal').classList.add('show');
}
function closeReject() {
    document.getElementById('rejectModal').classList.remove('show');
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeReject();
});
</script>
@endsection