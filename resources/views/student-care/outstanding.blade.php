@extends('student-care.layouts.app')
@section('title', 'Outstanding Monitoring')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.out-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{margin-bottom:28px}

/* View Only Badge */
.view-only-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.2);border-radius:4px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:20px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:9px;color:#AAB8C8;margin-top:3px}

.toolbar{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.filter-sel{padding:10px 14px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;cursor:pointer;outline:none}

.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden}
.tbl{width:100%;border-collapse:collapse;min-width:900px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:12px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

/* Expand */
.tbl tbody tr.detail-row{background:rgba(27,79,168,0.02)}
.tbl tbody tr.detail-row td{padding:14px 20px}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-restricted{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}

.balance-val{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;line-height:1}
.overdue-days{display:inline-block;padding:2px 8px;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);border-radius:3px;font-size:10px;color:#DC2626;font-weight:500;margin-top:3px}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.btn-expand{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-expand:hover{background:rgba(27,79,168,0.07)}

/* Installment mini */
.inst-mini{width:100%;border-collapse:collapse;font-size:11px}
.inst-mini th{padding:5px 10px;text-align:left;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;font-weight:500}
.inst-mini td{padding:6px 10px;border-top:1px solid rgba(27,79,168,0.05);color:#4A5A7A}

/* Payment progress */
.pay-prog-wrap{display:flex;align-items:center;gap:8px}
.pay-prog-track{flex:1;max-width:80px;background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden}
.pay-prog-fill{height:4px;border-radius:3px}

/* No actions notice */
.no-actions-notice{display:flex;align-items:center;gap:8px;padding:10px 14px;background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15);border-radius:4px;font-size:11px;color:#7A8A9A;margin-top:14px}

@media(max-width:768px){.out-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="out-page">

    <div class="page-header">
        <div class="page-eyebrow">Student Care</div>
        <h1 class="page-title">Outstanding Monitoring</h1>
    </div>

    {{-- View Only Notice --}}
    <div class="view-only-badge">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        View Only — Contact Admin to override restrictions or modify due dates
    </div>

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
            <div class="kpi-sub">with balance</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $stats['restricted'] }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </div>
        <div class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Overdue</div>
            <div class="kpi-val">{{ $stats['overdue'] }}</div>
            <div class="kpi-sub">past due date</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
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
                    @forelse($enrollments as $enrollment)
                    @php
                        $paid       = $enrollment->total_paid;
                        $remaining  = $enrollment->remaining_balance;
                        $pct        = $enrollment->final_price > 0
                            ? min(100, round($paid / $enrollment->final_price * 100)) : 0;
                        $nextDue    = $enrollment->installmentSchedules
                            ->where('status', 'Pending')->sortBy('due_date')->first();
                        $overdueDue = $enrollment->installmentSchedules
                            ->where('status', 'Overdue')->first();
                        $daysOverdue = $overdueDue
                            ? (int)\Carbon\Carbon::parse($overdueDue->due_date)->diffInDays(now())
                            : 0;
                        $isRestricted = $enrollment->status === 'Restricted';
                        $barColor = $pct >= 100 ? '#059669' : ($pct >= 50 ? '#1B4FA8' : '#C47010');
                    @endphp
                    <tr data-name="{{ strtolower($enrollment->student?->full_name ?? '') }}"
                        data-course="{{ strtolower($enrollment->courseInstance?->courseTemplate?->name ?? '') }}"
                        data-status="{{ $enrollment->status }}">

                        <td>
                            <div style="font-weight:500;color:#1A2A4A">
                                {{ $enrollment->student?->full_name ?? '—' }}
                            </div>
                        </td>

                        <td style="font-size:12px">
                            {{ $enrollment->courseInstance?->courseTemplate?->name ?? '—' }}
                        </td>

                        <td style="font-size:11px;color:#7A8A9A">
                            {{ $enrollment->courseInstance?->patch?->name ?? '—' }}
                        </td>

                        <td style="font-size:11px;color:#7A8A9A">
                            {{ $enrollment->paymentPlan?->name ?? '—' }}
                        </td>

                        <td style="font-family:monospace;font-size:12px">
                            {{ number_format($enrollment->final_price) }}
                        </td>

                        <td>
                            <div class="pay-prog-wrap">
                                <span style="font-family:monospace;font-size:12px;color:#059669">
                                    {{ number_format($paid) }}
                                </span>
                                <div class="pay-prog-track">
                                    <div class="pay-prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="balance-val"
                                style="color:{{ $remaining > 5000 ? '#DC2626' : '#C47010' }}">
                                {{ number_format($remaining) }}
                            </div>
                            <div style="font-size:9px;color:#AAB8C8">LE</div>
                        </td>

                        <td>
                            @if($nextDue)
                                <div style="font-size:12px;color:#1A2A4A">
                                    {{ \Carbon\Carbon::parse($nextDue->due_date)->format('d M Y') }}
                                </div>
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
                            <button class="btn-sm btn-expand"
                                onclick="toggleRow('detail_{{ $enrollment->enrollment_id }}', this)">
                                ↓ Details
                            </button>
                        </td>
                    </tr>

                    {{-- Expand Row --}}
                    <tr id="detail_{{ $enrollment->enrollment_id }}"
                        class="detail-row" style="display:none">
                        <td colspan="10">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

                                {{-- Installment Schedule --}}
                                <div>
                                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#F5911E;margin-bottom:8px">
                                        Installment Schedule
                                    </div>
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

                                {{-- Info + No Actions Notice --}}
                                <div>
                                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#F5911E;margin-bottom:8px">
                                        Enrollment Info
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:8px">
                                        <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                            <span style="color:#7A8A9A">Final Price</span>
                                            <span style="font-weight:500;color:#1A2A4A">{{ number_format($enrollment->final_price) }} LE</span>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                            <span style="color:#7A8A9A">Total Paid</span>
                                            <span style="font-weight:500;color:#059669">{{ number_format($paid) }} LE</span>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                            <span style="color:#7A8A9A">Remaining</span>
                                            <span style="font-weight:500;color:#DC2626">{{ number_format($remaining) }} LE</span>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0">
                                            <span style="color:#7A8A9A">Responsible CS</span>
                                            <span style="font-weight:500;color:#1A2A4A">
                                                {{ $enrollment->createdByCs?->full_name ?? '—' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="no-actions-notice">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        To override restrictions or extend due dates, contact Admin.
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:60px;color:#AAB8C8">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1" style="margin:0 auto 14px;display:block">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
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

<script>
function toggleRow(id, btn) {
    const row  = document.getElementById(id);
    const show = row.style.display === 'none';
    row.style.display = show ? '' : 'none';
    btn.textContent   = show ? '↑ Hide' : '↓ Details';
}

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
</script>
@endsection