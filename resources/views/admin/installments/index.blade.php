@extends('admin.layouts.app')
@section('title', 'Installment Approvals')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
.inst-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0}
.page-header{margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh)}
.kpi-card::before{display:none}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;display:block;font-weight:600;padding-bottom:8px;border-bottom:1px solid rgba(27,79,168,0.1)}

.tbl-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;margin-bottom:24px;box-shadow:var(--glass-sh)}
.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid rgba(27,79,168,0.08);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.03)}
.tbl td{padding:13px 14px;font-size:13px;color:var(--muted);vertical-align:middle}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-pending{color:var(--orange-dk);background:rgba(245,145,30,0.10);border:1px solid rgba(245,145,30,0.25)}
.badge-approved{color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.2)}
.badge-rejected{color:var(--red);background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.18)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:600;transition:all 0.2s}
.btn-approve{color:var(--green-dk);border-color:rgba(5,150,105,0.25)}
.btn-approve:hover{background:rgba(5,150,105,0.08)}
.btn-reject{color:var(--red);border-color:rgba(220,38,38,0.22)}
.btn-reject:hover{background:rgba(220,38,38,0.07)}

.pager{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:12px 16px;border-top:1px solid rgba(27,79,168,0.08);background:rgba(255,255,255,0.3)}
.pager-info{font-size:11px;color:var(--faint)}
.pager-links{display:flex;gap:4px;flex-wrap:wrap}
.pg{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 9px;border-radius:9px;font-size:11px;font-weight:600;color:var(--muted);text-decoration:none;border:1px solid rgba(27,79,168,0.12);background:rgba(255,255,255,0.6);transition:all 0.2s}
.pg:hover{color:var(--blue);border-color:var(--blue);text-decoration:none}
.pg.active{color:#fff;border-color:transparent;background:linear-gradient(120deg,var(--blue),var(--blue-2));box-shadow:0 6px 14px rgba(27,79,168,0.25)}
.pg.disabled{opacity:0.4;pointer-events:none}

#rejectModal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
#rejectModal.show{display:flex}
.modal-box{width:100%;max-width:440px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18)}
/* .modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--red),transparent)} */
.modal-header{padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.08)}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:var(--red)}
.modal-body{padding:18px 22px}
.modal-footer{padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.08);display:flex;gap:10px;justify-content:flex-end}

@media(max-width:768px){.inst-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(3,1fr)}}
</style>

<div class="inst-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div class="page-eyebrow">Admin Panel</div>
        <h1 class="page-title">Installment Approvals</h1>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#15803D;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Pending</div><div class="kpi-val">{{ $stats['pending'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green-dk)"><div class="kpi-label">Approved</div><div class="kpi-val">{{ $stats['approved'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--red)"><div class="kpi-label">Rejected</div><div class="kpi-val">{{ $stats['rejected'] }}</div></div>
    </div>

    <span class="sec-label">Pending Requests</span>
    <div class="tbl-card">
        <div style="overflow-x:auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Requested Plan</th>
                        <th>Date Requested</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $log)
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#16233F">{{ $log->enrollment?->student?->full_name ?? '—' }}</div>
                        </td>
                        <td style="font-size:12px">{{ $log->enrollment?->courseTemplate?->name ?? '—' }}</td>
                        <td><span style="font-size:11px;color:#1B4FA8;font-weight:600">{{ $log->paymentPlan?->name ?? '—' }}</span></td>
                        <td style="font-size:10px;color:#93A3BC">
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
                        <td colspan="8" style="text-align:center;padding:40px;color:#93A3BC;font-size:13px">
                            No pending requests
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
                    <td style="font-weight:500;color:#16233F">
                        @php
                            $name = $log->enrollment?->student?->full_name;
                            if (!$name && $log->rejection_note && str_contains($log->rejection_note, '||')) {
                                $name = explode('||', $log->rejection_note)[0];
                            }
                        @endphp
                        {{ $name ?? '—' }}
                    </td>
                    <td>
                            <span class="badge {{ $log->status === 'Approved' ? 'badge-approved' : 'badge-rejected' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#5A6A85">{{ $log->approvedBy?->full_name ?? '—' }}</td>
                        <td style="font-size:10px;color:#93A3BC">{{ \Carbon\Carbon::parse($log->approved_at)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:30px;color:#93A3BC;font-size:12px">No history yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history->hasPages())
        @php
            $hCur  = $history->currentPage();
            $hLast = $history->lastPage();
            $hFrom = max(1, $hCur - 2);
            $hTo   = min($hLast, $hCur + 2);
        @endphp
        <div class="pager">
            <div class="pager-info">Showing {{ $history->firstItem() }}–{{ $history->lastItem() }} of {{ $history->total() }}</div>
            <div class="pager-links">
                <a href="{{ $history->previousPageUrl() ?? '#' }}" class="pg {{ $history->onFirstPage() ? 'disabled' : '' }}">‹</a>
                @if($hFrom > 1)
                    <a href="{{ $history->url(1) }}" class="pg">1</a>
                    @if($hFrom > 2)<span class="pg disabled">…</span>@endif
                @endif
                @for($i = $hFrom; $i <= $hTo; $i++)
                    <a href="{{ $history->url($i) }}" class="pg {{ $i === $hCur ? 'active' : '' }}">{{ $i }}</a>
                @endfor
                @if($hTo < $hLast)
                    @if($hTo < $hLast - 1)<span class="pg disabled">…</span>@endif
                    <a href="{{ $history->url($hLast) }}" class="pg">{{ $hLast }}</a>
                @endif
                <a href="{{ $history->nextPageUrl() ?? '#' }}" class="pg {{ $history->hasMorePages() ? '' : 'disabled' }}">›</a>
            </div>
        </div>
        @endif
    </div>

</div>

<div id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Reject Request</div>
        </div>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:5px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85;font-weight:600">Rejection Reason</label>
                    <textarea name="reason" rows="3" required minlength="2"
                        style="width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;resize:none;box-sizing:border-box"
                        placeholder="Explain the reason..."></textarea>
                        @if($errors->has('reason'))
                        <div style="font-size:11px;color:#DC2626;margin-top:4px;">{{ $errors->first('reason') }}</div>
                        @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeReject()"
                    style="padding:9px 18px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:#5A6A85;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 22px;background:linear-gradient(120deg,#DC2626,#EF4444);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(220,38,38,0.28);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
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
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('notification-received', function(e) {
        const data = e.detail || {};
        if (data.entity_type === 'installment_request') {
            setTimeout(() => window.location.reload(), 1500);
        }
    });
});
setTimeout(() => location.reload(), 30000);
</script>
@endsection