@extends('admin.layouts.app')
@section('title', 'Outstanding Risk')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.out-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:9px;color:#AAB8C8;letter-spacing:1px;margin-top:3px}

.toolbar{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.filter-sel{padding:10px 14px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;cursor:pointer;outline:none}

.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden}
.tbl{width:100%;border-collapse:collapse;min-width:1000px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:12px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

/* Expand row */
.tbl tbody tr.detail-row{background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.08)}
.tbl tbody tr.detail-row td{padding:14px 20px}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-restricted{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-overdue{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}

.balance-val{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;line-height:1}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap}
.btn-expand{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-expand:hover{background:rgba(27,79,168,0.07)}
.btn-lift{color:#059669;border-color:rgba(5,150,105,0.25)}
.btn-lift:hover{background:rgba(5,150,105,0.07)}
.btn-restrict{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-restrict:hover{background:rgba(220,38,38,0.06)}
.btn-extend{color:#C47010;border-color:rgba(245,145,30,0.2)}
.btn-extend:hover{background:rgba(245,145,30,0.06)}

/* Days overdue badge */
.overdue-days{display:inline-block;padding:2px 8px;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);border-radius:3px;font-size:10px;color:#DC2626;font-weight:500}

/* Installment mini table */
.inst-mini{width:100%;border-collapse:collapse;font-size:11px}
.inst-mini th{padding:5px 10px;text-align:left;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;font-weight:500}
.inst-mini td{padding:6px 10px;border-top:1px solid rgba(27,79,168,0.05);color:#4A5A7A}

/* Override modal */
#overrideModal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
#overrideModal.show{display:flex}
.modal-box{width:100%;max-width:460px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18)}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.07)}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1B4FA8}
.modal-subtitle{font-size:11px;color:#7A8A9A;margin-top:3px}
.modal-body{padding:18px 22px}
.modal-footer{padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.form-field{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.action-cards{display:flex;flex-direction:column;gap:8px;margin-bottom:14px}
.action-card{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1.5px solid rgba(27,79,168,0.12);border-radius:5px;cursor:pointer;transition:all 0.2s;background:#fff}
.action-card:hover,.action-card.selected{border-color:#1B4FA8;background:rgba(27,79,168,0.03)}
.action-card input{margin-top:2px;flex-shrink:0;accent-color:#1B4FA8}
.action-card-label{font-size:12px;color:#1A2A4A;font-weight:500}
.action-card-sub{font-size:10px;color:#7A8A9A;margin-top:2px}
#extendDateField{display:none}

@media(max-width:768px){.out-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="out-page">

    <div class="page-header">
        <div class="page-eyebrow">Admin Panel</div>
        <h1 class="page-title">Outstanding Risk</h1>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Total Outstanding</div>
            <div class="kpi-val">{{ number_format($stats['total_outstanding']) }}</div>
            <div class="kpi-sub">LE</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Students</div>
            <div class="kpi-val">{{ $stats['count'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $stats['restricted'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Overdue</div>
            <div class="kpi-val">{{ $stats['overdue'] }}</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="outSearch" class="search-input" placeholder="Search by student or course...">
        </div>
        <select class="filter-sel" id="statusFilter" onchange="applyFilters()">
            <option value="">All Status</option>
            <option value="Restricted">Restricted</option>
            <option value="Active">Active</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="tbl-card">
        <div style="overflow-x:auto">
            <table class="tbl" id="outTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Patch</th>
                        <th>Responsible CS</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Next Due</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                    @php
                        $paid     = $enrollment->financialTransactions()
                            ->whereIn('transaction_type', ['Payment','Installment'])->sum('amount');
                        $refunded = $enrollment->financialTransactions()
                            ->where('transaction_type','Refund')->sum('amount');
                        $remaining= $enrollment->final_price - ($paid - $refunded);
                        $nextDue  = $enrollment->installmentSchedules
                            ->where('status','Pending')->sortBy('due_date')->first();
                        $overdueDue = $enrollment->installmentSchedules
                            ->where('status','Overdue')->first();
                        $daysOverdue = $overdueDue
                            ? \Carbon\Carbon::parse($overdueDue->due_date)->diffInDays(now())
                            : 0;
                        $isRestricted = $enrollment->status === 'Restricted';
                    @endphp
                    <tr data-name="{{ strtolower($enrollment->student?->full_name ?? '') }}"
                        data-course="{{ strtolower($enrollment->courseInstance?->courseTemplate?->name ?? '') }}"
                        data-status="{{ $enrollment->status }}">
                        <td>
                            <div style="font-weight:500;color:#1A2A4A">{{ $enrollment->student?->full_name ?? '—' }}</div>
                        </td>
                        <td style="font-size:12px">{{ $enrollment->courseInstance?->courseTemplate?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#7A8A9A">{{ $enrollment->courseInstance?->patch?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#7A8A9A">
                            {{ $enrollment->createdByCs?->employee?->full_name ?? $enrollment->createdByCs?->full_name ?? '—' }}
                        </td>
                        <td style="font-family:monospace;font-size:12px">{{ number_format($enrollment->final_price) }}</td>
                        <td style="font-family:monospace;font-size:12px;color:#059669">{{ number_format($paid - $refunded) }}</td>
                        <td>
                            <div class="balance-val" style="color:{{ $remaining > 5000 ? '#DC2626' : '#C47010' }}">
                                {{ number_format($remaining) }}
                            </div>
                            <div style="font-size:9px;color:#AAB8C8">LE</div>
                        </td>
                        <td>
                            @if($nextDue)
                                <div style="font-size:12px;color:#1A2A4A">{{ \Carbon\Carbon::parse($nextDue->due_date)->format('d M Y') }}</div>
                                @if($daysOverdue > 0)
                                    <div class="overdue-days">{{ $daysOverdue }}d overdue</div>
                                @endif
                            @else
                                <span style="color:#AAB8C8;font-size:11px">—</span>
                            @endif
                        </td>
                        <td>
                            @if($isRestricted)
                                <span class="badge badge-restricted">Restricted</span>
                            @else
                                <span class="badge badge-active">Active</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button class="btn-sm btn-expand"
                                    onclick="toggleRow('detail_{{ $enrollment->enrollment_id }}', this)">
                                    ↓ Details
                                </button>
                                <button class="btn-sm btn-lift"
                                    onclick="openOverride({{ $enrollment->enrollment_id }}, '{{ addslashes($enrollment->student?->full_name) }}', '{{ $enrollment->status }}')">
                                    Override
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Expand Row --}}
                    <tr id="detail_{{ $enrollment->enrollment_id }}" class="detail-row" style="display:none">
                        <td colspan="10">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

                                {{-- Installment Schedule --}}
                                <div>
                                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#F5911E;margin-bottom:8px">Installment Schedule</div>
                                    @if($enrollment->installmentSchedules->isNotEmpty())
                                    <table class="inst-mini">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Due Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($enrollment->installmentSchedules as $inst)
                                            <tr>
                                                <td>{{ $inst->installment_number }}</td>
                                                <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}</td>
                                                <td style="font-family:monospace">{{ number_format($inst->amount) }} LE</td>
                                                <td>
                                                    <span style="font-size:9px;text-transform:uppercase;letter-spacing:1px;
                                                        color:{{ $inst->status === 'Paid' ? '#059669' : ($inst->status === 'Overdue' ? '#DC2626' : '#C47010') }}">
                                                        {{ $inst->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                        <div style="font-size:11px;color:#AAB8C8">No installment schedule</div>
                                    @endif
                                </div>

                                {{-- Restriction History --}}
                                <div>
                                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#F5911E;margin-bottom:8px">Restriction History</div>
                                    @foreach($enrollment->restrictionLogs as $rlog)
                                    <div style="padding:8px 10px;background:#fff;border:1px solid rgba(220,38,38,0.1);border-radius:4px;margin-bottom:6px">
                                        <div style="font-size:11px;color:#DC2626;font-weight:500">{{ ucfirst(str_replace('_',' ',$rlog->reason)) }}</div>
                                        <div style="font-size:10px;color:#7A8A9A;margin-top:2px">
                                            By {{ $rlog->triggered_by }} — {{ \Carbon\Carbon::parse($rlog->triggered_at)->format('d M Y H:i') }}
                                        </div>
                                        @if($rlog->notes)
                                        <div style="font-size:10px;color:#AAB8C8;margin-top:2px">{{ $rlog->notes }}</div>
                                        @endif
                                    </div>
                                    @endforeach
                                    @if($enrollment->restrictionLogs->isEmpty())
                                        <div style="font-size:11px;color:#AAB8C8">No active restrictions</div>
                                    @endif
                                </div>

                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:48px;color:#AAB8C8">
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">All Clear</div>
                            <div style="font-size:12px">No outstanding balances found</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Override Modal --}}
<div id="overrideModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Admin Override</div>
            <div class="modal-subtitle" id="overrideStudentName">—</div>
        </div>
        <form id="overrideForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body">

                <div class="action-cards">
                    <label class="action-card" onclick="selectAction(this, 'lift')">
                        <input type="radio" name="action" value="lift">
                        <div>
                            <div class="action-card-label">🔓 Lift Restriction</div>
                            <div class="action-card-sub">Remove current restriction — student regains access</div>
                        </div>
                    </label>
                    <label class="action-card" onclick="selectAction(this, 'restrict')">
                        <input type="radio" name="action" value="restrict">
                        <div>
                            <div class="action-card-label">🔒 Manual Restrict</div>
                            <div class="action-card-sub">Force restriction — won't auto-release on payment</div>
                        </div>
                    </label>
                    <label class="action-card" onclick="selectAction(this, 'extend_due')">
                        <input type="radio" name="action" value="extend_due">
                        <div>
                            <div class="action-card-label">📅 Extend Due Date</div>
                            <div class="action-card-sub">Push next installment due date forward</div>
                        </div>
                    </label>
                </div>

                <div id="extendDateField" class="form-field">
                    <label class="form-label">New Due Date</label>
                    <input type="date" name="new_due_date" class="form-control">
                </div>

                <div class="form-field">
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2"
                        placeholder="Reason for override..."></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeOverride()"
                    style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 22px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Apply Override
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openOverride(id, name, status) {
    document.getElementById('overrideStudentName').textContent = name;
    document.getElementById('overrideForm').action = `/admin/outstanding/${id}/override`;
    document.querySelectorAll('.action-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('[name="action"]').forEach(r => r.checked = false);
    document.getElementById('extendDateField').style.display = 'none';
    document.getElementById('overrideModal').classList.add('show');
}

function closeOverride() {
    document.getElementById('overrideModal').classList.remove('show');
}

function selectAction(card, action) {
    document.querySelectorAll('.action-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('extendDateField').style.display =
        action === 'extend_due' ? 'flex' : 'none';
}

function toggleRow(id, btn) {
    const row = document.getElementById(id);
    const show = row.style.display === 'none';
    row.style.display = show ? '' : 'none';
    btn.textContent = show ? '↑ Hide' : '↓ Details';
}

// Search + Filter
function applyFilters() {
    const q      = document.getElementById('outSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    document.querySelectorAll('#outTable tbody tr[data-name]').forEach(row => {
        const matchQ = !q || row.dataset.name.includes(q) || row.dataset.course.includes(q);
        const matchS = !status || row.dataset.status === status;
        row.style.display = (matchQ && matchS) ? '' : 'none';
    });
}
document.getElementById('outSearch').addEventListener('input', applyFilters);

document.getElementById('overrideModal').addEventListener('click', function(e) {
    if (e.target === this) closeOverride();
});
</script>
@endsection