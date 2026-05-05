@extends('layouts.leads')
@section('title', 'Outstanding Balances')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.out-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:6px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:10px;color:#AAB8C8;margin-top:4px}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15)}

.search-bar{display:flex;gap:12px;margin-bottom:20px;align-items:center;flex-wrap:wrap}
.search-input{flex:1;min-width:200px;padding:10px 14px;border:1px solid rgba(27,79,168,0.15);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

/* Filter pills */
.filter-pills{display:flex;gap:8px;flex-wrap:wrap}
.pill{padding:7px 16px;border:1px solid rgba(27,79,168,0.15);border-radius:20px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A;cursor:pointer;transition:all 0.2s;background:#fff;font-family:'DM Sans',sans-serif}
.pill:hover{border-color:#1B4FA8;color:#1B4FA8}
.pill.active{background:#1B4FA8;color:#fff;border-color:#1B4FA8}
.pill.pill-red.active{background:#DC2626;border-color:#DC2626}
.pill.pill-orange.active{background:#F5911E;border-color:#F5911E}
.pill.pill-green.active{background:#059669;border-color:#059669}

.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;overflow:hidden}
.tbl-scroll{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse;min-width:900px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr.main-row{border-bottom:1px solid rgba(27,79,168,0.05);cursor:pointer;transition:background 0.2s}
.tbl tbody tr.main-row:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:13px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}
.tbl td.money{font-family:monospace;font-size:12px;color:#1A2A4A}
.tbl td.remaining{font-family:monospace;font-size:13px;color:#DC2626;font-weight:500}
.tbl td.paid-td{font-family:monospace;font-size:13px;color:#059669}

/* Expand row */
.expand-row{display:none;background:rgba(27,79,168,0.015)}
.expand-row.open{display:table-row}
.expand-inner{padding:16px 20px;border-bottom:2px solid rgba(27,79,168,0.08)}

/* Installments mini table */
.inst-table{width:100%;border-collapse:collapse;margin-bottom:14px}
.inst-table th{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;padding:5px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.06)}
.inst-table td{font-size:12px;color:#4A5A7A;padding:7px 10px;border-bottom:1px solid rgba(27,79,168,0.04)}
.inst-table tr:last-child td{border-bottom:none}

/* Badges */
.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;white-space:nowrap}
.badge-restricted{background:rgba(220,38,38,0.08);color:#DC2626;border:1px solid rgba(220,38,38,0.15)}
.badge-ok{background:rgba(5,150,105,0.08);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.badge-overdue{background:rgba(245,145,30,0.1);color:#C47010;border:1px solid rgba(245,145,30,0.2)}
.badge-paid{background:rgba(5,150,105,0.08);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.badge-pending{background:rgba(27,79,168,0.06);color:#1B4FA8;border:1px solid rgba(27,79,168,0.12)}

/* Pay button */
.btn-pay{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:transparent;border:1.5px solid #059669;border-radius:4px;color:#059669;font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.3s;position:relative;overflow:hidden}
.btn-pay::before{content:'';position:absolute;inset:0;background:#059669;transform:scaleX(0);transform-origin:left;transition:transform 0.3s cubic-bezier(0.16,1,0.3,1)}
.btn-pay:hover::before{transform:scaleX(1)}
.btn-pay:hover{color:#fff}
.btn-pay span{position:relative;z-index:1}

/* Progress */
.pay-prog{background:#F0F0F0;border-radius:3px;height:4px;margin-top:6px;overflow:hidden;min-width:80px}
.pay-prog-fill{height:4px;border-radius:3px}

/* Payment Modal */
.pay-modal-backdrop{display:none;position:fixed;inset:0;z-index:1050;background:rgba(209,216,231,0.55);align-items:center;justify-content:center;padding:24px}
.pay-modal-backdrop.show{display:flex}
.pay-modal{width:100%;max-width:440px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18)}
.pay-modal::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent);z-index:1}
.pay-modal-header{padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08)}
.pay-modal-eyebrow{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px}
.pay-modal-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#1B4FA8}
.pay-modal-body{padding:20px 24px}
.pay-form-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#7A8A9A;margin-bottom:6px;display:block}
.pay-form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;margin-bottom:14px}
.pay-form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.pay-modal-footer{padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-cancel-modal{padding:9px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer}
.btn-confirm-pay{padding:10px 24px;background:#059669;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer}

/* Remaining hint in modal */
.remaining-hint{background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.1);border-radius:4px;padding:10px 12px;margin-bottom:14px;font-size:12px;color:#DC2626}

/* Alerts */
.alert-success{background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:16px;font-size:13px}
.alert-error{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:16px;font-size:13px}

.chevron{transition:transform 0.25s;display:inline-block;margin-left:6px;opacity:0.4}
.chevron.open{transform:rotate(180deg)}
@media(max-width:680px){.kpi-grid{grid-template-columns:1fr 1fr}.out-page{padding:18px 14px}}
</style>

<div class="out-page">

    <div class="page-eyebrow">Customer Service</div>
    <h1 class="page-title">Outstanding Balances</h1>

    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- KPI CARDS --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Total Outstanding</div>
            <div class="kpi-val">{{ number_format($summary['total_outstanding']) }}</div>
            <div class="kpi-sub">LE unpaid balance</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Students</div>
            <div class="kpi-val">{{ $summary['total_students'] }}</div>
            <div class="kpi-sub">with pending payments</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $summary['restricted_count'] }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Overdue</div>
            <div class="kpi-val">{{ $summary['overdue_count'] }}</div>
            <div class="kpi-sub">past due date</div>
        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <div class="search-bar">
        <input type="text" id="searchInput" class="search-input" placeholder="Search student or course...">
        <div class="filter-pills">
            <button class="pill active" onclick="setFilter('', this)">All</button>
            <button class="pill pill-red" onclick="setFilter('restricted', this)">Restricted</button>
            <button class="pill pill-orange" onclick="setFilter('overdue', this)">Overdue</button>
            <button class="pill pill-green" onclick="setFilter('ok', this)">On Track</button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="sec-label">Outstanding Balances</div>
    <div class="tbl-card">
        <div class="tbl-scroll">
            <table class="tbl" id="outTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Payment Plan</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Next Due</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)

                    {{-- MAIN ROW --}}
                    <tr class="main-row"
                        data-status="{{ $row['is_restricted'] ? 'restricted' : ($row['days_overdue'] ? 'overdue' : 'ok') }}"
                        data-search="{{ strtolower($row['student_name'] . ' ' . $row['course']) }}"
                        onclick="toggleExpand({{ $row['enrollment_id'] }})">
                        <td>
                            <div style="font-weight:500;color:#1A2A4A">
                                {{ $row['student_name'] }}
                                <svg class="chevron" id="chev-{{ $row['enrollment_id'] }}" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <div style="font-size:10px;color:#AAB8C8;margin-top:2px;text-transform:uppercase;letter-spacing:1px">{{ $row['enrollment_type'] }}</div>
                        </td>
                        <td>{{ $row['course'] }}</td>
                        <td style="font-size:11px;color:#7A8A9A">{{ $row['payment_plan'] }}</td>
                        <td class="money">{{ number_format($row['total']) }} LE</td>
                        <td class="paid-td">
                            {{ number_format($row['paid']) }} LE
                            <div class="pay-prog">
                                <div class="pay-prog-fill" style="width:{{ $row['total'] > 0 ? min(100, round(($row['paid']/$row['total'])*100)) : 0 }}%;background:#059669"></div>
                            </div>
                        </td>
                        <td class="remaining">{{ number_format($row['remaining']) }} LE</td>
                        <td onclick="event.stopPropagation()">
                            @if($row['next_due_date'])
                                <div style="font-size:12px;color:#1A2A4A">{{ $row['next_due_date'] }}</div>
                                @if($row['next_due_amount'])
                                <div style="font-size:10px;color:#7A8A9A;margin-top:2px">{{ number_format($row['next_due_amount']) }} LE</div>
                                @endif
                                @if($row['days_overdue'])
                                <div style="font-size:10px;color:#DC2626;margin-top:2px">{{ $row['days_overdue'] }}d overdue</div>
                                @endif
                            @else
                                <span style="color:#AAB8C8;font-size:11px">—</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            @if($row['is_restricted'])
                                <span class="badge badge-restricted">
                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                    Restricted
                                </span>
                                @if($row['restriction_reason'])
                                <div style="font-size:9px;color:#AAB8C8;margin-top:3px">{{ str_replace('_',' ',$row['restriction_reason']) }}</div>
                                @endif
                            @elseif($row['days_overdue'])
                                <span class="badge badge-overdue">Overdue</span>
                            @else
                                <span class="badge badge-ok">On Track</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            <button class="btn-pay"
                                onclick="openPayModal({{ $row['enrollment_id'] }}, '{{ addslashes($row['student_name']) }}', {{ $row['remaining'] }})">
                                <span>Record Payment</span>
                            </button>
                        </td>
                    </tr>

                    {{-- EXPAND ROW — Installment details --}}
                    <tr class="expand-row" id="expand-{{ $row['enrollment_id'] }}">
                        <td colspan="9" style="padding:0">
                            <div class="expand-inner">
                                <div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:10px">Installment Schedule</div>
                                <table class="inst-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Paid At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($row['installments'] as $inst)
                                        <tr>
                                            <td style="color:#AAB8C8">{{ $inst['number'] }}</td>
                                            <td style="font-family:monospace">{{ number_format($inst['amount']) }} LE</td>
                                            <td>{{ $inst['due_date'] ?? '—' }}</td>
                                            <td>
                                                @if($inst['status'] === 'Paid')
                                                    <span class="badge badge-paid">Paid</span>
                                                @elseif($inst['status'] === 'Overdue')
                                                    <span class="badge badge-overdue">Overdue</span>
                                                @else
                                                    <span class="badge badge-pending">Pending</span>
                                                @endif
                                            </td>
                                            <td style="font-size:11px;color:#AAB8C8">{{ $inst['paid_at'] ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                        @if(empty($row['installments']))
                                        <tr><td colspan="5" style="color:#AAB8C8;text-align:center;padding:12px">No installments scheduled.</td></tr>
                                        @endif
                                    </tbody>
                                </table>

                                {{-- Payment History --}}
                                @if(!empty($row['transactions']))
                                <div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#7A8A9A;margin:14px 0 8px">Payment History</div>
                                <table class="inst-table">
                                    <thead>
                                        <tr><th>Type</th><th>Amount</th><th>Method</th><th>Date</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($row['transactions'] as $tx)
                                        <tr>
                                            <td style="text-transform:capitalize">{{ $tx['type'] }}</td>
                                            <td style="font-family:monospace;color:{{ $tx['type']==='Refund' ? '#DC2626' : '#059669' }}">
                                                {{ $tx['type']==='Refund' ? '-' : '+' }}{{ number_format($tx['amount']) }} LE
                                            </td>
                                            <td style="color:#AAB8C8">{{ $tx['method'] }}</td>
                                            <td style="font-size:11px;color:#AAB8C8">{{ $tx['date'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9">
                            <div style="text-align:center;padding:48px;color:#AAB8C8">
                                <div style="font-size:13px">No outstanding balances — all students are up to date.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- PAY MODAL --}}
<div class="pay-modal-backdrop" id="payModal">
    <div class="pay-modal">
        <div class="pay-modal-header">
            <div class="pay-modal-eyebrow">Record Payment</div>
            <div class="pay-modal-title" id="modal-student-name">Student Name</div>
        </div>
        <div class="pay-modal-body">
            <div class="remaining-hint">
                Remaining balance: <strong id="modal-remaining">0</strong> LE
            </div>
            <form id="payForm" method="POST">
                @csrf
                <label class="pay-form-label">Amount Paid (LE) <span style="color:#F5911E">*</span></label>
                <input type="number" name="amount" id="modal-amount" class="pay-form-control"
                    placeholder="Enter amount..." min="1" step="0.01" required>

                <label class="pay-form-label">Payment Method <span style="color:#F5911E">*</span></label>
                <select name="payment_method" class="pay-form-control" required>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Transfer">InstaPay</option>
                    <option value="Online">Vodafove Cash</option>
                </select>

                <label class="pay-form-label">Notes (optional)</label>
                <input type="text" name="notes" class="pay-form-control" placeholder="Any notes...">
            </form>
        </div>
        <div class="pay-modal-footer">
            <button class="btn-cancel-modal" onclick="closePayModal()">Cancel</button>
            <button class="btn-confirm-pay" onclick="submitPayment()">Confirm Payment</button>
        </div>
    </div>
</div>

<script>
// ── Filter ──────────────────────────────
let currentFilter = '';
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', applyFilters);

function setFilter(status, btn) {
    currentFilter = status;
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
}

function applyFilters() {
    const search = searchInput.value.toLowerCase();
    document.querySelectorAll('#outTable tbody tr.main-row').forEach(row => {
        const matchSearch = !search || row.dataset.search.includes(search);
        const matchFilter = !currentFilter || row.dataset.status === currentFilter;
        const show = matchSearch && matchFilter;
        row.style.display = show ? '' : 'none';
        // hide expand row too
        const expandId = row.querySelector('[id^="chev-"]')?.id?.replace('chev-','');
        if (expandId) {
            const expandRow = document.getElementById('expand-' + expandId);
            if (expandRow && !show) expandRow.classList.remove('open');
        }
    });
}

// ── Expand ──────────────────────────────
function toggleExpand(id) {
    const row    = document.getElementById('expand-' + id);
    const chev   = document.getElementById('chev-' + id);
    const isOpen = row.classList.contains('open');
    // close all
    document.querySelectorAll('.expand-row').forEach(r => r.classList.remove('open'));
    document.querySelectorAll('.chevron').forEach(c => c.classList.remove('open'));
    if (!isOpen) {
        row.classList.add('open');
        chev.classList.add('open');
    }
}

// ── Pay Modal ───────────────────────────
function openPayModal(enrollmentId, studentName, remaining) {
    document.getElementById('modal-student-name').textContent = studentName;
    document.getElementById('modal-remaining').textContent    = remaining.toLocaleString('en-EG');
    document.getElementById('modal-amount').max               = remaining;
    document.getElementById('modal-amount').value             = '';
    document.getElementById('payForm').action = '/outstanding/' + enrollmentId + '/pay';
    document.getElementById('payModal').classList.add('show');
}

function closePayModal() {
    document.getElementById('payModal').classList.remove('show');
}

function submitPayment() {
    document.getElementById('payForm').submit();
}

document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>

@endsection