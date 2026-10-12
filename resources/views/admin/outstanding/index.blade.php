@extends('admin.layouts.app')
@section('title', 'Outstanding Risk')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box}
.os-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.os-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.os-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--text);margin:0 0 28px}

.alert{padding:12px 16px;border-radius:12px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.alert-success{background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:var(--green-dk)}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:18px 16px;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;box-shadow:var(--glass-sh)}
.kpi-card:hover{transform:translateY(-3px);box-shadow:0 16px 38px rgba(23,45,90,0.13)}
.kpi-card::before{display:none}
.kpi-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}
.kpi-sub{font-size:10px;color:var(--faint);margin-top:5px}

.os-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.os-search-wrap{position:relative;flex:1;min-width:220px}
.os-search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.os-search{width:100%;padding:10px 14px 10px 38px;border:1px solid var(--glass-bd);border-radius:12px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);outline:none;transition:border-color .3s,box-shadow .3s}
.os-search:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.filter-sel{padding:10px 14px;border:1px solid var(--glass-bd);border-radius:12px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--muted);background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);cursor:pointer;outline:none}

.pills{display:flex;gap:6px;flex-wrap:wrap}
.pill{padding:6px 14px;border:1px solid var(--glass-bd);border-radius:20px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);cursor:pointer;transition:all 0.2s;background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);font-family:'DM Sans',sans-serif;font-weight:600}
.pill:hover{border-color:var(--blue);color:var(--blue)}
.pill.active{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;border-color:transparent;box-shadow:0 6px 14px rgba(27,79,168,0.25)}
.pill.p-red.active   {background:var(--red);border-color:var(--red);box-shadow:0 6px 14px rgba(220,38,38,0.22)}
.pill.p-orange.active{background:var(--orange);border-color:var(--orange);box-shadow:0 6px 14px rgba(245,145,30,0.25)}
.pill.p-green.active {background:var(--green-dk);border-color:var(--green-dk);box-shadow:0 6px 14px rgba(21,128,61,0.22)}

.sec-lbl{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(27,79,168,0.1);display:flex;align-items:center;justify-content:space-between;font-weight:600}
.sec-lbl-count{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:var(--blue);background:rgba(27,79,168,0.08);border-radius:10px;padding:0 10px}

.tbl-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh)}
.tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.tbl{width:100%;border-collapse:collapse;min-width:1000px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid rgba(27,79,168,0.08);white-space:nowrap}
.tbl tbody tr.main-row{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s;cursor:pointer}
.tbl tbody tr.main-row:hover{background:rgba(27,79,168,0.03)}
.tbl td{padding:12px 14px;font-size:13px;color:var(--muted);vertical-align:middle}

.expand-row{display:none}
.expand-row.open{display:table-row}
.expand-inner{padding:18px 20px 20px;background:rgba(255,255,255,0.35);border-top:1px solid rgba(27,79,168,0.08)}
.expand-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}

.mini-lbl{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:8px;font-weight:600}
.mini-tbl{width:100%;border-collapse:collapse;background:rgba(255,255,255,0.5);border-radius:12px;overflow:hidden}
.mini-tbl th{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);padding:5px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.08);font-weight:600}
.mini-tbl td{font-size:12px;color:var(--muted);padding:7px 10px;border-bottom:1px solid rgba(27,79,168,0.05)}
.mini-tbl tr:last-child td{border-bottom:none}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600;white-space:nowrap}
.b-restricted{color:var(--red);background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.18)}
.b-active    {color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.2)}
.b-overdue   {color:var(--orange-dk);background:rgba(245,145,30,0.10);border:1px solid rgba(245,145,30,0.25)}
.b-paid      {color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.2)}
.b-pending   {color:var(--blue);background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.18)}
.b-finished  {color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.22)}

.overdue-tag{display:inline-block;padding:2px 8px;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);border-radius:20px;font-size:10px;color:var(--red);font-weight:600}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap;font-weight:600}
.btn-expand  {color:var(--blue);border-color:rgba(27,79,168,0.22)}
.btn-expand:hover{background:rgba(27,79,168,0.08)}
.btn-override{color:var(--orange-dk);border-color:rgba(245,145,30,0.3)}
.btn-override:hover{background:rgba(245,145,30,0.08)}

.chev{transition:transform 0.25s;display:inline-block;margin-left:5px;opacity:0.35;vertical-align:middle}
.chev.open{transform:rotate(180deg);opacity:0.7}

.fin-section{margin-top:32px}
.fin-banner{display:flex;align-items:center;gap:12px;padding:14px 18px;background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid rgba(5,150,105,0.22);border-radius:14px;margin-bottom:14px;box-shadow:var(--glass-sh)}
.fin-banner-icon{width:32px;height:32px;border-radius:10px;background:rgba(5,150,105,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.fin-banner-text{font-size:12px;color:var(--muted);line-height:1.5}
.fin-banner-text strong{color:var(--text)}

#overrideModal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
#overrideModal.show{display:flex;animation:fadein 0.2s ease both}
@keyframes fadein{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:460px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18);animation:slidein 0.3s cubic-bezier(0.16,1,0.3,1) both}
@keyframes slidein{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
/* .modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)} */
.modal-header{padding:20px 24px 14px;border-bottom:1px solid rgba(27,79,168,0.08)}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:var(--text)}
.modal-subtitle{font-size:11px;color:var(--muted);margin-top:3px}
.modal-body{padding:20px 24px}
.modal-footer{padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.08);display:flex;gap:10px;justify-content:flex-end}
.form-field{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.action-cards{display:flex;flex-direction:column;gap:8px;margin-bottom:14px}
.action-card{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1.5px solid rgba(27,79,168,0.12);border-radius:14px;cursor:pointer;transition:all 0.2s;background:rgba(255,255,255,0.6)}
.action-card:hover,.action-card.selected{border-color:var(--blue);background:rgba(27,79,168,0.06)}
.action-card input{margin-top:2px;flex-shrink:0;accent-color:var(--blue)}
.action-card-label{font-size:12px;color:var(--text);font-weight:500}
.action-card-sub{font-size:10px;color:var(--muted);margin-top:2px}
#extendDateField{display:none}
.btn-modal-cancel{padding:9px 18px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer}
.btn-modal-submit{padding:10px 22px;background:linear-gradient(120deg,#1B4FA8,#2D6FDB);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(27,79,168,0.28);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer}

@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.os-page{padding:18px 14px}.expand-grid{grid-template-columns:1fr}}
</style>

<div class="os-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="os-eyebrow">Admin Panel</div>
    <h1 class="os-title">Outstanding Risk</h1>

    @if(session('success'))
    <div class="alert alert-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8">
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
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Overdue</div>
            <div class="kpi-val">{{ $stats['overdue'] }}</div>
            <div class="kpi-sub">past due date</div>
        </div>
    </div>

    <div class="os-toolbar">
        <div class="os-search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="outSearch" class="os-search" placeholder="Search by student or course...">
        </div>
        <div class="pills">
            <button class="pill active"   onclick="setFilter('',           this)">All</button>
            <button class="pill p-red"    onclick="setFilter('Restricted', this)">Restricted</button>
            <button class="pill p-orange" onclick="setFilter('overdue',    this)">Overdue</button>
            <button class="pill p-green"  onclick="setFilter('Active',     this)">Active</button>
            <button class="pill p-green"  onclick="setFilter('finished',   this)">Finished</button>
        </div>
    </div>

    @php
        $activeEnrollments   = $enrollments->where('remaining_balance', '>', 0.01);
        $finishedEnrollments = $enrollments->where('remaining_balance', '<=', 0.01);
    @endphp

    <div id="mainTableSection">
        <div class="sec-lbl">
            <span>Outstanding Balances</span>
            <span class="sec-lbl-count">{{ $activeEnrollments->count() }}</span>
        </div>

        <div class="tbl-card">
            <div class="tbl-scroll">
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
                    @forelse($activeEnrollments as $enrollment)
                    @php
                        $paid        = $enrollment->total_paid;
                        $remaining   = $enrollment->remaining_balance;
                        $nextDue     = $enrollment->installmentSchedules->whereIn('status', ['Pending', 'Overdue'])->sortBy('due_date')->first();
                        $daysOverdue = 0;
                        if ($nextDue && $nextDue->due_date) {
                            $grace     = (int) ($enrollment->paymentPlan?->grace_period_days ?? 0);
                            $graceEnds = \Carbon\Carbon::parse($nextDue->due_date)->startOfDay()->addDays($grace);
                            $today     = now()->startOfDay();
                            if ($today->gt($graceEnds)) {
                                $daysOverdue = (int) abs($today->diffInDays($graceEnds));
                            }
                        }
                        $hasPendingInstallment = $enrollment->installmentSchedules->whereIn('status', ['Pending', 'Overdue'])->isNotEmpty();
                        $isRestricted = $enrollment->status === 'Restricted';
                        $rowStatus    = $isRestricted ? 'Restricted' : ($daysOverdue > 0 ? 'overdue' : 'Active');
                    @endphp
                    <tr class="main-row"
                        data-name="{{ strtolower($enrollment->student?->full_name ?? '') }}"
                        data-course="{{ strtolower($enrollment->courseInstance?->courseTemplate?->name ?? '') }}"
                        data-status="{{ $rowStatus }}"
                        onclick="toggleExpand({{ $enrollment->enrollment_id }})">

                        <td>
                            <div style="font-weight:600;color:#16233F;font-size:13px">
                                {{ $enrollment->student?->full_name ?? '—' }}
                                <svg class="chev" id="chev-{{ $enrollment->enrollment_id }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </td>
                        <td style="font-size:12px">{{ $enrollment->courseTemplate?->name ?? $enrollment->courseInstance?->courseTemplate?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#93A3BC">{{ $enrollment->patch?->name ?? $enrollment->courseInstance?->patch?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#5A6A85">
                            {{ $enrollment->createdByCs?->employee?->full_name ?? $enrollment->createdByCs?->full_name ?? '—' }}
                        </td>
                        <td style="font-family:monospace;font-size:12px;color:#16233F">
                            {{ number_format($enrollment->total_fees) }} <span style="font-size:10px;color:#B4C0D0">LE</span>
                        </td>
                        <td style="font-family:monospace;font-size:12px;color:#15803D">
                            {{ number_format($paid) }} <span style="font-size:10px;color:#B4C0D0">LE</span>
                        </td>
                        <td>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:{{ $remaining > 5000 ? '#DC2626' : '#C47010' }}">
                                {{ number_format($remaining) }}
                            </span>
                            <span style="font-size:10px;color:#B4C0D0"> LE</span>
                        </td>
                        <td onclick="event.stopPropagation()">
                            @if($nextDue)
                                @if($nextDue->due_date)
                                    <div style="font-size:12px;color:#16233F;font-weight:500">{{ \Carbon\Carbon::parse($nextDue->due_date)->format('d M Y') }}</div>
                                    @if($nextDue->amount)
                                        <div style="font-size:10px;color:#5A6A85;margin-top:2px">{{ number_format($nextDue->amount) }} LE</div>
                                    @endif
                                    @if($daysOverdue > 0)
                                        <div style="font-size:10px;color:#DC2626;margin-top:2px;font-weight:500">{{ $daysOverdue }}d overdue</div>
                                    @endif
                                @else
                                    <div style="font-size:11px;color:#93A3BC;font-style:italic;line-height:1.4">
                                        Upon course<br>assignment
                                    </div>
                                @endif
                            @else
                                <span style="color:#93A3BC;font-size:11px">—</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            @if($isRestricted)
                                <span class="badge b-restricted">Restricted</span>
                                @if($enrollment->restriction_reason)
                                    <div style="font-size:9px;color:#93A3BC;margin-top:3px">{{ str_replace('_',' ',strtolower($enrollment->restriction_reason)) }}</div>
                                @endif
                            @elseif($daysOverdue > 0)
                                <span class="badge b-overdue">Overdue</span>
                            @else
                                <span class="badge b-active">On Track</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div style="display:flex;gap:6px">
                                <button class="btn-sm btn-expand"
                                    onclick="toggleExpand({{ $enrollment->enrollment_id }});event.stopPropagation()">
                                    ↓ Details
                                </button>
                                <button class="btn-sm btn-override"
                                    onclick="openOverride({{ $enrollment->enrollment_id }}, '{{ addslashes($enrollment->student?->full_name) }}', '{{ $enrollment->status }}');event.stopPropagation()">
                                    Override
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr class="expand-row" id="expand-{{ $enrollment->enrollment_id }}">
                        <td colspan="10" style="padding:0">
                            <div class="expand-inner">
                                <div class="expand-grid">
                                    <div>
                                        <div class="mini-lbl">Installment Schedule</div>
                                        @if($enrollment->installmentSchedules->isNotEmpty())
                                        <table class="mini-tbl">
                                            <thead><tr><th>#</th><th>Due Date</th><th>Amount</th><th>Status</th><th>Paid At</th><th>Notes</th></tr></thead>
                                            <tbody>
                                            @foreach($enrollment->installmentSchedules as $inst)
                                            <tr>
                                                <td style="color:#93A3BC">{{ $inst->installment_number }}</td>
                                                <td>
                                                    @if($inst->due_date)
                                                        {{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}
                                                    @else
                                                        <span style="color:#93A3BC;font-style:italic;font-size:10px">Upon course assignment</span>
                                                    @endif
                                                </td>
                                                <td style="font-family:monospace">{{ number_format($inst->amount) }} LE</td>
                                                <td>
                                                    @if($inst->status === 'Paid')     <span class="badge b-paid">Paid</span>
                                                    @elseif($inst->status === 'Overdue') <span class="badge b-overdue">Overdue</span>
                                                    @else <span class="badge b-pending">Pending</span>
                                                    @endif
                                                </td>
                                                <td style="font-size:11px;color:#93A3BC">{{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('d M Y') : '—' }}</td>
                                                <td style="font-size:11px;color:#5A6A85">{{ $inst->transaction?->notes ?? '—' }}</td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <div style="font-size:11px;color:#93A3BC">No installments scheduled.</div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="mini-lbl">Restriction History</div>
                                        @forelse($enrollment->restrictionLogs as $rlog)
                                        <div style="padding:10px 12px;background:rgba(255,255,255,0.6);border:1px solid rgba(220,38,38,0.15);border-radius:12px;margin-bottom:6px">
                                            <div style="font-size:11px;color:#DC2626;font-weight:500">{{ ucfirst(str_replace('_',' ',$rlog->reason)) }}</div>
                                            <div style="font-size:10px;color:#5A6A85;margin-top:3px">
                                                By {{ $rlog->triggered_by }} — {{ \Carbon\Carbon::parse($rlog->triggered_at)->format('d M Y H:i') }}
                                            </div>
                                            @if($rlog->notes)
                                            <div style="font-size:10px;color:#93A3BC;margin-top:2px">{{ $rlog->notes }}</div>
                                            @endif
                                        </div>
                                        @empty
                                        <div style="font-size:11px;color:#93A3BC">No active restrictions.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10">
                            <div style="text-align:center;padding:56px;color:#93A3BC">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#B4C0D0" stroke-width="1.2" style="display:block;margin:0 auto 12px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;margin-bottom:4px">All Clear</div>
                                <div style="font-size:12px">No outstanding balances found.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($finishedEnrollments->count())
    <div class="fin-section" id="finishedSection" style="display:none">

        <div class="fin-banner">
            <div class="fin-banner-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="fin-banner-text">
                <strong>{{ $finishedEnrollments->count() }} enrollment{{ $finishedEnrollments->count() > 1 ? 's' : '' }}</strong>
                fully settled — all payments received and reconciled.
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
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Patch</th>
                            <th>Payment Plan</th>
                            <th>Responsible CS</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($finishedEnrollments as $enrollment)
                    <tr class="main-row" onclick="toggleExpand('fin_{{ $enrollment->enrollment_id }}')">
                        <td>
                            <div style="font-weight:600;color:#16233F;font-size:13px">
                                {{ $enrollment->student?->full_name ?? '—' }}
                                <svg class="chev" id="chev-fin_{{ $enrollment->enrollment_id }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </td>
                        <td style="font-size:12px">{{ $enrollment->courseTemplate?->name ?? $enrollment->courseInstance?->courseTemplate?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#93A3BC">{{ $enrollment->patch?->name ?? $enrollment->courseInstance?->patch?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#5A6A85">{{ $enrollment->paymentPlan?->name ?? '—' }}</td>
                        <td style="font-size:11px;color:#5A6A85">
                            {{ $enrollment->createdByCs?->employee?->full_name ?? $enrollment->createdByCs?->full_name ?? '—' }}
                        </td>
                        <td style="font-family:monospace;font-size:12px;color:#16233F">{{ number_format($enrollment->total_fees) }} LE</td>
                        <td style="font-family:monospace;font-size:12px;color:#15803D;font-weight:600">{{ number_format($enrollment->total_paid) }} LE</td>
                        <td><span class="badge b-finished">✓ Settled</span></td>
                    </tr>
                    <tr class="expand-row" id="expand-fin_{{ $enrollment->enrollment_id }}">
                        <td colspan="7" style="padding:0">
                            <div class="expand-inner">
                                <div class="mini-lbl">Installment Schedule</div>
                                @if($enrollment->installmentSchedules->isNotEmpty())
                                <table class="mini-tbl">
                                    <thead><tr><th>#</th><th>Due Date</th><th>Amount</th><th>Status</th><th>Notes</th><th>Paid At</th></tr></thead>
                                    <tbody>
                                    @foreach($enrollment->installmentSchedules as $inst)
                                    <tr>
                                        <td style="color:#93A3BC">{{ $inst->installment_number }}</td>
                                        <td>
                                            @if($inst->due_date)
                                                {{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}
                                            @else
                                                <span style="color:#93A3BC;font-style:italic;font-size:10px">Upon course assignment</span>
                                            @endif
                                        </td>
                                        <td style="font-family:monospace">{{ number_format($inst->amount) }} LE</td>
                                        <td><span class="badge b-paid">Paid</span></td>
                                        <td style="font-size:11px;color:#5A6A85">{{ $inst->transaction?->notes ?? '—' }}</td>
                                        <td style="font-size:11px;color:#93A3BC">{{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('d M Y') : '—' }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @else
                                <div style="font-size:11px;color:#93A3BC">No installment records.</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endif

</div>

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
                    <textarea name="notes" class="form-control" rows="2" placeholder="Reason for override..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeOverride()"
                    class="btn-modal-cancel">
                    Cancel
                </button>
                <button type="submit"
                    class="btn-modal-submit">
                    Apply Override
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentFilter = '';

function setFilter(status, btn) {
    currentFilter = status;
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');

    const mainSection = document.getElementById('mainTableSection');
    const finSection  = document.getElementById('finishedSection');

    if (status === 'finished') {
        if (mainSection) mainSection.style.display = 'none';
        if (finSection)  finSection.style.display  = 'block';
    } else {
        if (mainSection) mainSection.style.display = 'block';
        if (finSection)  finSection.style.display  = 'none';
        applyFilters();
    }
}

function applyFilters() {
    const q      = document.getElementById('outSearch').value.toLowerCase();
    const status = currentFilter;
    document.querySelectorAll('#outTable tbody tr.main-row').forEach(row => {
        const matchQ = !q || row.dataset.name.includes(q) || row.dataset.course.includes(q);
        const matchS = !status || status === 'finished' || row.dataset.status === status;
        const show   = matchQ && matchS;
        row.style.display = show ? '' : 'none';
        const id = row.querySelector('[id^="chev-"]')?.id?.replace('chev-','');
        if (id) {
            const exp = document.getElementById('expand-' + id);
            if (exp && !show) exp.classList.remove('open');
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
    document.getElementById('extendDateField').style.display = action === 'extend_due' ? 'flex' : 'none';
}

document.getElementById('overrideModal').addEventListener('click', function(e) {
    if (e.target === this) closeOverride();
});
</script>
@endsection