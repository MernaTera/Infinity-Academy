@extends('student-care.layouts.app')
@section('title', 'Outstanding Monitoring')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
*{box-sizing:border-box}
.out-page{min-height:100vh;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0 0 24px}

.view-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(122,138,154,0.07);border:1px solid rgba(122,138,154,0.18);border-radius:4px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:20px}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.09);border-radius:6px;padding:16px 18px;position:relative;overflow:hidden;transition:box-shadow 0.2s}
.kpi-card:hover{box-shadow:0 4px 16px rgba(27,79,168,0.1)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:6px;font-weight:500}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:9px;color:#C4CDD6;margin-top:4px}

/* Pills */
.pills{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px}
.pill{padding:6px 14px;border:1px solid rgba(27,79,168,0.15);border-radius:20px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A;cursor:pointer;transition:all 0.2s;background:#fff;font-family:'DM Sans',sans-serif;font-weight:500}
.pill:hover{border-color:#1B4FA8;color:#1B4FA8}
.pill.active{background:#1B4FA8;color:#fff;border-color:#1B4FA8}
.pill.p-red.active{background:#DC2626;border-color:#DC2626}
.pill.p-orange.active{background:#F5911E;border-color:#F5911E}
.pill.p-green.active{background:#059669;border-color:#059669}

/* Toolbar */
.toolbar{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

/* Sec label */
.sec-lbl{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(245,145,30,0.15);display:flex;align-items:center;justify-content:space-between}
.sec-lbl-count{font-family:'Bebas Neue',sans-serif;font-size:18px;color:#AAB8C8;letter-spacing:2px}

/* Table */
.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.09);border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(27,79,168,0.05)}
.tbl-scroll{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse;min-width:900px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;text-align:left;font-weight:600;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr.main-row{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s;cursor:pointer}
.tbl tbody tr.main-row:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:12px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

/* Expand */
.expand-row{display:none}
.expand-row.open{display:table-row}
.expand-inner{padding:18px 20px;background:rgba(248,246,242,0.6);border-top:1px solid rgba(27,79,168,0.06)}
.expand-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}

/* Mini table */
.mini-lbl{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:8px}
.mini-tbl{width:100%;border-collapse:collapse}
.mini-tbl th{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;padding:5px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.07);font-weight:600}
.mini-tbl td{font-size:12px;color:#4A5A7A;padding:7px 10px;border-bottom:1px solid rgba(27,79,168,0.04)}
.mini-tbl tr:last-child td{border-bottom:none}

/* Badges */
.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:600;white-space:nowrap}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.b-restricted{color:#DC2626;background:rgba(220,38,38,0.07);border:1px solid rgba(220,38,38,0.15)}
.b-active    {color:#059669;background:rgba(5,150,105,0.07);border:1px solid rgba(5,150,105,0.15)}
.b-overdue   {color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}
.b-waiting   {color:#1B4FA8;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.12)}
.b-paid      {color:#059669;background:rgba(5,150,105,0.07);border:1px solid rgba(5,150,105,0.15)}
.b-pending   {color:#1B4FA8;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.12)}
.b-finished  {color:#059669;background:rgba(5,150,105,0.07);border:1px solid rgba(5,150,105,0.2)}

.overdue-tag{display:inline-block;padding:2px 8px;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);border-radius:3px;font-size:10px;color:#DC2626;font-weight:500;margin-top:3px}

/* Progress */
.pay-prog-wrap{display:flex;align-items:center;gap:8px}
.pay-prog-track{flex:1;max-width:70px;background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden}
.pay-prog-fill{height:4px;border-radius:3px}

/* Buttons */
.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap;font-weight:500}
.btn-expand{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-expand:hover{background:rgba(27,79,168,0.07)}

/* Chevron */
.chev{transition:transform 0.25s;display:inline-block;margin-left:4px;opacity:0.3;vertical-align:middle}
.chev.open{transform:rotate(180deg);opacity:0.7}

/* Notice */
.notice{display:flex;align-items:center;gap:8px;padding:10px 14px;background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15);border-radius:4px;font-size:11px;color:#7A8A9A;margin-top:12px}

/* Finished */
.fin-banner{display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fff;border:1px solid rgba(5,150,105,0.15);border-left:4px solid #059669;border-radius:6px;margin-bottom:14px}
.fin-icon{width:32px;height:32px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0}

@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.kpi-grid{grid-template-columns:repeat(2,1fr)}.expand-grid{grid-template-columns:1fr}}
</style>

<div class="out-page">

    <div class="page-eyebrow">Student Care</div>
    <h1 class="page-title">Outstanding Monitoring</h1>

    <div class="view-badge">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        View Only — Contact Admin to override restrictions or modify due dates
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Total Outstanding</div>
            <div class="kpi-val">{{ number_format($stats['total_outstanding']) }}</div>
            <div class="kpi-sub">LE unpaid</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Cases</div>
            <div class="kpi-val">{{ $stats['count'] }}</div>
            <div class="kpi-sub">with balance</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $stats['restricted'] }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Overdue</div>
            <div class="kpi-val">{{ $stats['overdue'] }}</div>
            <div class="kpi-sub">past due date</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Finished</div>
            <div class="kpi-val">{{ $stats['finished_count'] }}</div>
            <div class="kpi-sub">fully settled</div>
        </div>
    </div>

    {{-- Filter Pills --}}
    <div class="pills">
        <button class="pill active"    onclick="setFilter('', this)">All</button>
        <button class="pill p-red"     onclick="setFilter('Restricted', this)">Restricted</button>
        <button class="pill p-orange"  onclick="setFilter('overdue', this)">Overdue</button>
        <button class="pill p-green"   onclick="setFilter('Active', this)">Active</button>
        <button class="pill p-green"   onclick="setFilter('finished', this)">Finished</button>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="outSearch" class="search-input" placeholder="Search by student or course...">
        </div>
    </div>

    {{-- ══ OUTSTANDING TABLE ══ --}}
    <div id="mainSection">
        <div class="sec-lbl">
            <span>Outstanding Balances</span>
            <span class="sec-lbl-count">{{ $withBalance->count() }}</span>
        </div>

        <div class="tbl-card">
            <div class="tbl-scroll">
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
                    @forelse($withBalance as $enrollment)
                    @php
                        $paid       = $enrollment->total_paid;
                        $remaining  = $enrollment->remaining_balance;
                        $total      = $enrollment->total_fees;
                        $pct        = $total > 0 ? min(100, round($paid / $total * 100)) : 0;
                        $barColor   = $pct >= 100 ? '#059669' : ($pct >= 50 ? '#1B4FA8' : '#C47010');
                        $nextDue    = $enrollment->installmentSchedules->whereIn('status',['Pending','Overdue'])->sortBy('due_date')->first();
                        $overdueSch = $enrollment->installmentSchedules->where('status','Overdue')->first();
                        $daysOverdue = $overdueSch ? (int)\Carbon\Carbon::parse($overdueSch->due_date)->diffInDays(now()) : 0;
                        $isRestricted = $enrollment->status === 'Restricted';
                        $rowStatus = $isRestricted ? 'Restricted' : ($daysOverdue > 0 ? 'overdue' : 'Active');
                        $courseName = $enrollment->courseTemplate?->name
                            ?? $enrollment->courseInstance?->courseTemplate?->name ?? '—';
                        $patchName = $enrollment->patch?->name
                            ?? $enrollment->courseInstance?->patch?->name ?? '—';
                    @endphp
                    <tr class="main-row"
                        data-name="{{ strtolower($enrollment->student?->full_name ?? '') }}"
                        data-course="{{ strtolower($courseName) }}"
                        data-status="{{ $rowStatus }}"
                        onclick="toggleExpand({{ $enrollment->enrollment_id }})">

                        <td>
                            <div style="font-weight:600;color:#1A2A4A">
                                {{ $enrollment->student?->full_name ?? '—' }}
                                <svg class="chev" id="chev-{{ $enrollment->enrollment_id }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </td>
                        <td style="font-size:12px">{{ $courseName }}</td>
                        <td style="font-size:11px;color:#7A8A9A">{{ $patchName }}</td>
                        <td style="font-size:11px;color:#7A8A9A">{{ $enrollment->paymentPlan?->name ?? '—' }}</td>
                        <td style="font-family:monospace;font-size:12px">
                            {{ number_format($total) }} <span style="font-size:10px;color:#C4CDD6">LE</span>
                        </td>
                        <td>
                            <div class="pay-prog-wrap">
                                <span style="font-family:monospace;font-size:12px;color:#059669">{{ number_format($paid) }}</span>
                                <div class="pay-prog-track">
                                    <div class="pay-prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:{{ $remaining > 5000 ? '#DC2626' : '#C47010' }}">
                                {{ number_format($remaining) }}
                            </div>
                            <div style="font-size:9px;color:#C4CDD6">LE</div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            @if($nextDue)
                                <div style="font-size:12px;color:#1A2A4A;font-weight:500">
                                    {{ $nextDue->due_date ? \Carbon\Carbon::parse($nextDue->due_date)->format('d M Y') : '—' }}
                                </div>
                                @if($daysOverdue > 0)
                                    <div class="overdue-tag">{{ $daysOverdue }}d overdue</div>
                                @endif
                            @else
                                <span style="color:#AAB8C8;font-size:11px">—</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            @if($isRestricted)
                                <span class="badge b-restricted">Restricted</span>
                            @elseif($enrollment->status === 'Waiting')
                                <span class="badge b-waiting">Waiting</span>
                            @elseif($daysOverdue > 0)
                                <span class="badge b-overdue">Overdue</span>
                            @else
                                <span class="badge b-active">Active</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            <button class="btn-sm btn-expand"
                                onclick="toggleExpand({{ $enrollment->enrollment_id }})">
                                ↓ Details
                            </button>
                        </td>
                    </tr>

                    {{-- Expand --}}
                    <tr class="expand-row" id="expand-{{ $enrollment->enrollment_id }}">
                        <td colspan="10" style="padding:0">
                            <div class="expand-inner">
                                <div class="expand-grid">

                                    {{-- Installment Schedule --}}
                                    <div>
                                        <div class="mini-lbl">Installment Schedule</div>
                                        @if($enrollment->installmentSchedules->isNotEmpty())
                                        <table class="mini-tbl">
                                            <thead>
                                                <tr><th>#</th><th>Due Date</th><th>Amount</th><th>Status</th><th>Paid At</th></tr>
                                            </thead>
                                            <tbody>
                                            @foreach($enrollment->installmentSchedules as $inst)
                                            <tr>
                                                <td style="color:#AAB8C8">{{ $inst->installment_number }}</td>
                                                <td>{{ $inst->due_date ? \Carbon\Carbon::parse($inst->due_date)->format('d M Y') : '—' }}</td>
                                                <td style="font-family:monospace">{{ number_format($inst->amount) }} LE</td>
                                                <td>
                                                    @if($inst->status === 'Paid')
                                                        <span class="badge b-paid">Paid</span>
                                                    @elseif($inst->status === 'Overdue')
                                                        <span class="badge b-overdue">Overdue</span>
                                                    @else
                                                        <span class="badge b-pending">Pending</span>
                                                    @endif
                                                </td>
                                                <td style="font-size:11px;color:#AAB8C8">
                                                    {{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('d M Y') : '—' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <div style="font-size:11px;color:#AAB8C8">No installment schedule.</div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div>
                                        <div class="mini-lbl">Enrollment Info</div>
                                        <div style="display:flex;flex-direction:column;gap:6px">
                                            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                                <span style="color:#7A8A9A">Total Fees</span>
                                                <span style="font-weight:500;color:#1A2A4A">{{ number_format($total) }} LE</span>
                                            </div>
                                            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                                <span style="color:#7A8A9A">Total Paid</span>
                                                <span style="font-weight:500;color:#059669">{{ number_format($paid) }} LE</span>
                                            </div>
                                            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                                <span style="color:#7A8A9A">Remaining</span>
                                                <span style="font-weight:500;color:#DC2626">{{ number_format($remaining) }} LE</span>
                                            </div>
                                            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.05)">
                                                <span style="color:#7A8A9A">Payment Plan</span>
                                                <span style="font-weight:500;color:#1A2A4A">{{ $enrollment->paymentPlan?->name ?? '—' }}</span>
                                            </div>
                                            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0">
                                                <span style="color:#7A8A9A">Responsible CS</span>
                                                <span style="font-weight:500;color:#1A2A4A">
                                                    {{ $enrollment->createdByCs?->full_name ?? '—' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="notice">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                            To override restrictions or extend due dates, contact Admin.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:56px;color:#AAB8C8">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#C4CDD6" stroke-width="1" style="display:block;margin:0 auto 14px">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;margin-bottom:4px">All Clear</div>
                            <div style="font-size:12px">No outstanding balances found</div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══ FINISHED SECTION ══ --}}
    @if($finishedEnrollments->count())
    <div id="finishedSection" style="display:none;margin-top:28px">
        <div class="fin-banner">
            <div class="fin-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="font-size:12px;color:#7A8A9A;line-height:1.5">
                <strong style="color:#1A2A4A">{{ $finishedEnrollments->count() }} enrollment{{ $finishedEnrollments->count() > 1 ? 's' : '' }}</strong>
                fully settled — all payments received.
            </div>
        </div>

        <div class="sec-lbl">
            <span>Fully Paid</span>
            <span class="sec-lbl-count">{{ $finishedEnrollments->count() }}</span>
        </div>

        <div class="tbl-card">
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr><th>Student</th><th>Course</th><th>Patch</th><th>Payment Plan</th><th>Total</th><th>Paid</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @foreach($finishedEnrollments as $enrollment)
                    @php
                        $courseName = $enrollment->courseTemplate?->name ?? $enrollment->courseInstance?->courseTemplate?->name ?? '—';
                        $patchName  = $enrollment->patch?->name ?? $enrollment->courseInstance?->patch?->name ?? '—';
                    @endphp
                    <tr class="main-row">
                        <td style="font-weight:600;color:#1A2A4A">{{ $enrollment->student?->full_name ?? '—' }}</td>
                        <td style="font-size:12px">{{ $courseName }}</td>
                        <td style="font-size:11px;color:#7A8A9A">{{ $patchName }}</td>
                        <td style="font-size:11px;color:#7A8A9A">{{ $enrollment->paymentPlan?->name ?? '—' }}</td>
                        <td style="font-family:monospace;font-size:12px">{{ number_format($enrollment->total_fees) }} LE</td>
                        <td style="font-family:monospace;font-size:12px;color:#059669;font-weight:600">{{ number_format($enrollment->total_paid) }} LE</td>
                        <td><span class="badge b-finished">✓ Settled</span></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
let currentFilter = '';

function setFilter(status, btn) {
    currentFilter = status;
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');

    const main = document.getElementById('mainSection');
    const fin  = document.getElementById('finishedSection');

    if (status === 'finished') {
        if (main) main.style.display = 'none';
        if (fin)  fin.style.display  = 'block';
    } else {
        if (main) main.style.display = 'block';
        if (fin)  fin.style.display  = 'none';
        applyFilters();
    }
}

function applyFilters() {
    const q      = document.getElementById('outSearch').value.toLowerCase();
    const status = currentFilter;
    document.querySelectorAll('#outTable tbody tr.main-row').forEach(row => {
        const matchQ = !q || row.dataset.name?.includes(q) || row.dataset.course?.includes(q);
        const matchS = !status || status === 'finished' || row.dataset.status === status;
        const show   = matchQ && matchS;
        row.style.display = show ? '' : 'none';
        const id = row.querySelector('[id^="chev-"]')?.id?.replace('chev-', '');
        if (id && !show) {
            document.getElementById('expand-' + id)?.classList.remove('open');
        }
    });
}

document.getElementById('outSearch').addEventListener('input', applyFilters);

function toggleExpand(id) {
    const row  = document.getElementById('expand-' + id);
    const chev = document.getElementById('chev-' + id);
    if (!row) return;
    const isOpen = row.classList.contains('open');
    document.querySelectorAll('.expand-row').forEach(r => r.classList.remove('open'));
    document.querySelectorAll('.chev').forEach(c => c.classList.remove('open'));
    if (!isOpen) { row.classList.add('open'); chev?.classList.add('open'); }
}
</script>
@endsection