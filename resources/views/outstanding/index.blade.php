@extends('layouts.leads')
@section('title', 'Outstanding Balances')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
/* ─── Base ───────────────────────────────────────────────── */
*{box-sizing:border-box}
.os-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}

/* ─── Header ─────────────────────────────────────────────── */
.os-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.os-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:#1B4FA8;margin:0 0 28px}

/* ─── Alerts ─────────────────────────────────────────────── */
.alert{padding:12px 16px;border-radius:4px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px}
.alert-success{background:rgba(5,150,105,0.07);border:1px solid rgba(5,150,105,0.2);color:#059669}
.alert-error  {background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.18);color:#DC2626}

/* ─── KPI Grid ───────────────────────────────────────────── */
.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:28px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.09);border-radius:6px;padding:18px 16px;position:relative;overflow:hidden;transition:box-shadow 0.2s}
.kpi-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.1)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:8px;font-weight:500}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:10px;color:#C4CDD6;margin-top:5px}

/* ─── Toolbar ────────────────────────────────────────────── */
.os-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.os-search-wrap{position:relative;flex:1;min-width:220px}
.os-search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;color:#AAB8C8}
.os-search{width:100%;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:5px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none}
.os-search:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.pills{display:flex;gap:6px;flex-wrap:wrap}
.pill{padding:6px 14px;border:1px solid rgba(27,79,168,0.15);border-radius:20px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A;cursor:pointer;transition:all 0.2s;background:#fff;font-family:'DM Sans',sans-serif;font-weight:500}
.pill:hover{border-color:#1B4FA8;color:#1B4FA8}
.pill.active{background:#1B4FA8;color:#fff;border-color:#1B4FA8}
.pill.p-red.active   {background:#DC2626;border-color:#DC2626}
.pill.p-orange.active{background:#F5911E;border-color:#F5911E}
.pill.p-green.active {background:#059669;border-color:#059669}

/* ─── Section Label ──────────────────────────────────────── */
.sec-lbl{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(245,145,30,0.15);display:flex;align-items:center;justify-content:space-between}
.sec-lbl-count{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#AAB8C8}

/* ─── Table Card ─────────────────────────────────────────── */
.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.09);border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(27,79,168,0.05)}
.tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.tbl{width:100%;border-collapse:collapse;min-width:860px}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;text-align:left;font-weight:600;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr.main-row{border-bottom:1px solid rgba(27,79,168,0.05);cursor:pointer;transition:background 0.15s}
.tbl tbody tr.main-row:hover{background:rgba(27,79,168,0.025)}
.tbl td{padding:13px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

/* ─── Expand Row ─────────────────────────────────────────── */
.expand-row{display:none}
.expand-row.open{display:table-row}
.expand-inner{padding:18px 20px 20px;background:rgba(248,246,242,0.7);border-top:1px solid rgba(27,79,168,0.07)}
.expand-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}

/* ─── Mini Table ─────────────────────────────────────────── */
.mini-tbl{width:100%;border-collapse:collapse}
.mini-tbl th{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;padding:5px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.07);font-weight:600}
.mini-tbl td{font-size:12px;color:#4A5A7A;padding:7px 10px;border-bottom:1px solid rgba(27,79,168,0.04)}
.mini-tbl tr:last-child td{border-bottom:none}
.mini-section-lbl{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:8px;margin-top:4px}

/* ─── Badges ─────────────────────────────────────────────── */
.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:600;white-space:nowrap}
.b-restricted{background:rgba(220,38,38,0.07);color:#DC2626;border:1px solid rgba(220,38,38,0.15)}
.b-overdue   {background:rgba(245,145,30,0.08);color:#C47010;border:1px solid rgba(245,145,30,0.2)}
.b-ontrack   {background:rgba(5,150,105,0.07);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.b-paid      {background:rgba(5,150,105,0.07);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.b-pending   {background:rgba(27,79,168,0.06);color:#1B4FA8;border:1px solid rgba(27,79,168,0.12)}
.b-finished  {background:rgba(5,150,105,0.07);color:#059669;border:1px solid rgba(5,150,105,0.2)}

/* ─── Progress bar ───────────────────────────────────────── */
.prog-wrap{display:flex;align-items:center;gap:8px}
.prog-track{flex:1;max-width:72px;background:#EEF0F4;border-radius:3px;height:4px;overflow:hidden}
.prog-fill{height:4px;border-radius:3px;transition:width 0.6s ease}

/* ─── Pay Button ─────────────────────────────────────────── */
.btn-pay{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:transparent;border:1.5px solid rgba(5,150,105,0.4);border-radius:4px;color:#059669;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all 0.25s;font-weight:500}
.btn-pay:hover{background:#059669;color:#fff;border-color:#059669}

/* ─── Chevron ────────────────────────────────────────────── */
.chev{transition:transform 0.25s;display:inline-block;margin-left:5px;opacity:0.35;vertical-align:middle}
.chev.open{transform:rotate(180deg);opacity:0.7}

/* ─── Fully Paid section ─────────────────────────────────── */
.fin-section{margin-top:32px}
.fin-banner{display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fff;border:1px solid rgba(5,150,105,0.15);border-left:4px solid #059669;border-radius:6px;margin-bottom:14px}
.fin-banner-icon{width:32px;height:32px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.fin-banner-text{font-size:12px;color:#7A8A9A;line-height:1.5}
.fin-banner-text strong{color:#1A2A4A}

/* ─── Modal ──────────────────────────────────────────────── */
.modal-backdrop{display:none;position:fixed;inset:0;z-index:1050;background:rgba(10,20,40,0.45);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:24px}
.modal-backdrop.show{display:flex;animation:fadein 0.2s ease both}
@keyframes fadein{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:440px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:10px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(27,79,168,0.2);animation:slidein 0.3s cubic-bezier(0.16,1,0.3,1) both}
@keyframes slidein{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.modal-header{padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08)}
.modal-eyebrow{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#1B4FA8}
.modal-body{padding:20px 24px}
.modal-footer{padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.form-lbl{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#7A8A9A;margin-bottom:6px;display:block;font-weight:500}
.form-ctrl{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;margin-bottom:14px}
.form-ctrl:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.remaining-hint{background:rgba(220,38,38,0.04);border:1px solid rgba(220,38,38,0.12);border-radius:4px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#DC2626;display:flex;align-items:center;gap:8px}
.next-inst-box{background:rgba(27,79,168,0.04);border:1px solid rgba(27,79,168,0.1);border-radius:5px;padding:14px 16px;margin-bottom:16px}
.next-inst-lbl{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:6px}
.next-inst-amt{font-family:'Bebas Neue',sans-serif;font-size:30px;color:#1B4FA8;letter-spacing:2px;line-height:1}
.next-inst-date{font-size:10px;color:#AAB8C8;margin-top:4px}
.btn-cancel-modal{padding:9px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer;transition:all 0.2s}
.btn-cancel-modal:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8}
.btn-confirm{padding:10px 24px;background:#059669;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;transition:background 0.2s}
.btn-confirm:hover{background:#047857}

/* ─── Responsive ─────────────────────────────────────────── */
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.kpi-grid{grid-template-columns:1fr 1fr};.os-page{padding:18px 14px};.expand-grid{grid-template-columns:1fr}}
</style>

<div class="os-page">

    <div class="os-eyebrow">Customer Service</div>
    <h1 class="os-title">Outstanding Balances</h1>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Total Outstanding</div>
            <div class="kpi-val">{{ number_format($summary['total_outstanding']) }}</div>
            <div class="kpi-sub">LE unpaid</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Cases</div>
            <div class="kpi-val">{{ $summary['total_students'] }}</div>
            <div class="kpi-sub">with balance</div>
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
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Fully Paid</div>
            <div class="kpi-val">{{ $summary['finished_count'] }}</div>
            <div class="kpi-sub">this cycle</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="os-toolbar">
        <div class="os-search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="searchInput" class="os-search" placeholder="Search by student or course...">
        </div>
        <div class="pills">
            <button class="pill active"     onclick="setFilter('',           this)">All</button>
            <button class="pill p-red"      onclick="setFilter('restricted', this)">Restricted</button>
            <button class="pill p-orange"   onclick="setFilter('overdue',    this)">Overdue</button>
            <button class="pill p-green"    onclick="setFilter('ok',         this)">On Track</button>
            <button class="pill p-green"    onclick="setFilter('finished',   this)">Finished</button>
        </div>
    </div>

    {{-- ══ OUTSTANDING TABLE ══ --}}
    @php $activeRows = $rows->where('is_finished', false); @endphp
<div id="mainTableSection">
    <div class="sec-lbl">
        <span>Outstanding Balances</span>
        <span class="sec-lbl-count">{{ $activeRows->count() }}</span>
    </div>

    <div class="tbl-card">
        <div class="tbl-scroll">
            <table class="tbl" id="outTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Plan</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Next Due</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows->where('is_finished', false) as $row)
                @php
                    $pct      = $row['total'] > 0 ? min(100, round(($row['paid']/$row['total'])*100)) : 0;
                    $barColor = $pct >= 80 ? '#059669' : ($pct >= 40 ? '#1B4FA8' : '#F5911E');
                    $statusKey = $row['is_restricted'] ? 'restricted' : ($row['days_overdue'] ? 'overdue' : 'ok');
                @endphp
                <tr class="main-row"
                    data-status="{{ $statusKey }}"
                    data-search="{{ strtolower($row['student_name'].' '.$row['course']) }}"
                    onclick="toggleExpand({{ $row['enrollment_id'] }})">

                    <td>
                        <div style="font-weight:600;color:#1A2A4A;font-size:13px">
                            {{ $row['student_name'] }}
                            <svg class="chev" id="chev-{{ $row['enrollment_id'] }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div style="font-size:10px;color:#AAB8C8;margin-top:2px;text-transform:uppercase;letter-spacing:1px">{{ $row['enrollment_type'] }}</div>
                    </td>

                    <td style="font-size:12px;color:#4A5A7A">{{ $row['course'] }}</td>
                    <td style="font-size:11px;color:#AAB8C8">{{ $row['payment_plan'] }}</td>

                    <td style="font-family:monospace;font-size:12px;color:#1A2A4A">
                        {{ number_format($row['total']) }} <span style="color:#C4CDD6;font-size:10px">LE</span>
                    </td>

                    <td>
                        <div class="prog-wrap">
                            <span style="font-family:monospace;font-size:12px;color:#059669">{{ number_format($row['paid']) }}</span>
                            <div class="prog-track">
                                <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                            </div>
                            <span style="font-size:10px;color:#AAB8C8">{{ $pct }}%</span>
                        </div>
                    </td>

                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:{{ $row['remaining'] > 3000 ? '#DC2626' : '#C47010' }}">
                            {{ number_format($row['remaining']) }}
                        </span>
                        <span style="font-size:10px;color:#C4CDD6"> LE</span>
                    </td>

                    <td onclick="event.stopPropagation()">
                        @if($row['next_due_date'])
                            <div style="font-size:12px;color:#1A2A4A;font-weight:500">{{ $row['next_due_date'] }}</div>
                            @if($row['next_due_amount'])
                            <div style="font-size:10px;color:#7A8A9A;margin-top:2px">{{ number_format($row['next_due_amount']) }} LE</div>
                            @endif
                            @if($row['days_overdue'])
                            <div style="font-size:10px;color:#DC2626;margin-top:2px;font-weight:500">{{ $row['days_overdue'] }}d overdue</div>
                            @endif
                        @else
                            <span style="color:#C4CDD6;font-size:11px">—</span>
                        @endif
                    </td>

                    <td onclick="event.stopPropagation()">
                        @if($row['is_restricted'])
                            <span class="badge b-restricted">Restricted</span>
                            @if($row['restriction_reason'])
                            <div style="font-size:9px;color:#AAB8C8;margin-top:3px">{{ str_replace('_',' ',$row['restriction_reason']) }}</div>
                            @endif
                        @elseif($row['days_overdue'])
                            <span class="badge b-overdue">Overdue</span>
                        @else
                            <span class="badge b-ontrack">On Track</span>
                        @endif
                    </td>

                    <td onclick="event.stopPropagation()">
                        <button class="btn-pay" onclick="openPayModal(
                            {{ $row['enrollment_id'] }},
                            '{{ addslashes($row['student_name']) }}',
                            {{ $row['remaining'] }},
                            {{ $row['next_due_amount'] ?? 0 }},
                            '{{ $row['next_due_date'] ?? '—' }}'
                        )">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                            Record
                        </button>
                    </td>
                </tr>

                {{-- Expand --}}
                <tr class="expand-row" id="expand-{{ $row['enrollment_id'] }}">
                    <td colspan="9" style="padding:0">
                        <div class="expand-inner">

                                {{-- Installments --}}
                                <div>
                                    <div class="mini-section-lbl">Installment Schedule</div>
                                    @if(!empty($row['installments']))
                                    <table class="mini-tbl">
                                        <thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Paid At</th></tr></thead>
                                        <tbody>
                                        @foreach($row['installments'] as $inst)
                                        <tr>
                                            <td style="color:#AAB8C8">{{ $inst['number'] }}</td>
                                            <td style="font-family:monospace">{{ number_format($inst['amount']) }} LE</td>
                                            <td>{{ $inst['due_date'] ?? '—' }}</td>
                                            <td>
                                                @if($inst['status']==='Paid')     <span class="badge b-paid">Paid</span>
                                                @elseif($inst['status']==='Overdue') <span class="badge b-overdue">Overdue</span>
                                                @else <span class="badge b-pending">Pending</span>
                                                @endif
                                            </td>
                                            <td style="font-size:11px;color:#AAB8C8">{{ $inst['paid_at'] ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <div style="font-size:11px;color:#AAB8C8;padding:8px 0">No installments scheduled.</div>
                                    @endif
                                </div>

                                {{-- Payment History --}}
                                <div>
                                    <div class="mini-section-lbl">Payment History</div>
                                    @if(!empty($row['transactions']))
                                    <table class="mini-tbl">
                                        <thead><tr><th>Type</th><th>Amount</th><th>Method</th><th>Notes</th><th>Date</th></tr></thead>
                                        <tbody>
                                        @foreach($row['transactions'] as $tx)
                                        <tr>
                                            <td>
                                                <span style="text-transform:capitalize">{{ $tx['type'] }}</span>
                                                <span style="font-size:9px;color:#AAB8C8;letter-spacing:1px">({{ $tx['category'] }})</span>
                                            </td>
                                            <td style="font-family:monospace;color:{{ $tx['type']==='Refund'?'#DC2626':'#059669' }}">
                                                {{ $tx['type']==='Refund'?'-':'+' }}{{ number_format($tx['amount']) }} LE
                                            </td>
                                            <td style="color:#AAB8C8">{{ $tx['method'] }}</td>
                                            <td style="font-size:11px;color:#7A8A9A">{{ $tx['notes'] ?? '—' }}</td>
                                            <td style="font-size:11px;color:#AAB8C8">{{ $tx['date'] }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <div style="font-size:11px;color:#AAB8C8;padding:8px 0">No payment history.</div>
                                    @endif
                                </div>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div style="text-align:center;padding:56px;color:#AAB8C8">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C4CDD6" stroke-width="1.2" style="display:block;margin:0 auto 12px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;margin-bottom:4px">All Clear</div>
                            <div style="font-size:12px">No outstanding balances in your portfolio.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    {{-- ══ FULLY PAID SECTION ══ --}}
    @php $finishedRows = $rows->where('is_finished', true); @endphp
    @if($finishedRows->count())
    <div class="fin-section" id="finishedSection" style="display:none">

        <div class="fin-banner">
            <div class="fin-banner-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="fin-banner-text">
                <strong>{{ $finishedRows->count() }} enrollment{{ $finishedRows->count() > 1 ? 's' : '' }}</strong>
                fully settled — all payments received and reconciled.
            </div>
        </div>

        <div class="sec-lbl">
            <span>Fully Paid</span>
            <span class="sec-lbl-count">{{ $finishedRows->count() }}</span>
        </div>

        <div class="tbl-card">
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Plan</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($finishedRows as $row)
                    <tr class="main-row" onclick="toggleExpand('fin_{{ $row['enrollment_id'] }}')">
                        <td>
                            <div style="font-weight:600;color:#1A2A4A;font-size:13px">
                                {{ $row['student_name'] }}
                                <svg class="chev" id="chev-fin_{{ $row['enrollment_id'] }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <div style="font-size:10px;color:#AAB8C8;margin-top:2px;text-transform:uppercase;letter-spacing:1px">{{ $row['enrollment_type'] }}</div>
                        </td>
                        <td style="font-size:12px;color:#4A5A7A">{{ $row['course'] }}</td>
                        <td style="font-size:11px;color:#AAB8C8">{{ $row['payment_plan'] }}</td>
                        <td style="font-family:monospace;font-size:12px;color:#1A2A4A">{{ number_format($row['total']) }} LE</td>
                        <td style="font-family:monospace;font-size:12px;color:#059669;font-weight:600">{{ number_format($row['paid']) }} LE</td>
                        <td><span class="badge b-finished">✓ Settled</span></td>
                    </tr>

                    <tr class="expand-row" id="expand-fin_{{ $row['enrollment_id'] }}">
                        <td colspan="6" style="padding:0">
                            <div class="expand-inner">
                                @if(!empty($row['transactions']))
                                <div class="mini-section-lbl">Payment History</div>
                                <table class="mini-tbl">
                                    <thead><tr><th>Type</th><th>Amount</th><th>Method</th><th>Notes</th><th>Date</th></tr></thead>
                                    <tbody>
                                    @foreach($row['transactions'] as $tx)
                                    <tr>
                                        <td>
                                            <span style="text-transform:capitalize">{{ $tx['type'] }}</span>
                                            <span style="font-size:9px;color:#AAB8C8">({{ $tx['category'] }})</span>
                                        </td>
                                        <td style="font-family:monospace;color:#059669">+{{ number_format($tx['amount']) }} LE</td>
                                        <td style="color:#AAB8C8">{{ $tx['method'] }}</td>
                                        <td style="font-size:11px;color:#7A8A9A">{{ $tx['notes'] ?? '—' }}</td>
                                        <td style="font-size:11px;color:#AAB8C8">{{ $tx['date'] }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @else
                                <div style="font-size:11px;color:#AAB8C8">No history available.</div>
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

{{-- ══ PAY MODAL ══ --}}
<div class="modal-backdrop" id="payModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-eyebrow">Record Payment</div>
            <div class="modal-title" id="modal-student-name">Student Name</div>
        </div>
        <div class="modal-body">
            <div class="remaining-hint">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Remaining balance: <strong id="modal-remaining">0</strong> LE
            </div>
            <div class="next-inst-box">
                <div class="next-inst-lbl">Next Installment Due</div>
                <div class="next-inst-amt" id="modal-installment-amount">—</div>
                <div class="next-inst-date" id="modal-installment-date">—</div>
            </div>
            <form id="payForm" method="POST">
                @csrf
                <input type="hidden" name="amount" id="modal-amount">
                <label class="form-lbl">Payment Method <span style="color:#F5911E">*</span></label>
                <select name="payment_method" class="form-ctrl" required>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Transfer">InstaPay</option>
                    <option value="Online">Vodafone Cash</option>
                </select>
                <label class="form-lbl">Notes (optional)</label>
                <input type="text" name="notes" class="form-ctrl" placeholder="Any notes...">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel-modal" onclick="closePayModal()">Cancel</button>
            <button class="btn-confirm" onclick="submitPayment()">Confirm Payment</button>
        </div>
    </div>
</div>

<script>
let currentFilter = '';
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', applyFilters);

function setFilter(status, btn) {
    currentFilter = status;
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');

    const fin     = document.getElementById('finishedSection');
    const mainTbl = document.getElementById('mainTableSection'); 

    if (status === 'finished') {
        if (fin)     fin.style.display     = 'block';
        if (mainTbl) mainTbl.style.display = 'none';
    } else {
        if (fin)     fin.style.display     = 'none';
        if (mainTbl) mainTbl.style.display = 'block';
    }

    applyFilters();
}

function applyFilters() {
    const q = searchInput.value.toLowerCase();
    document.querySelectorAll('#outTable tbody tr.main-row').forEach(row => {
        const matchQ = !q || row.dataset.search.includes(q);
        const matchF = !currentFilter || currentFilter === 'finished' || row.dataset.status === currentFilter;
        const show   = matchQ && matchF;
        row.style.display = show ? '' : 'none';
        const id = row.querySelector('[id^="chev-"]')?.id?.replace('chev-','');
        if (id) {
            const exp = document.getElementById('expand-' + id);
            if (exp && !show) exp.classList.remove('open');
        }
    });
}

function toggleExpand(id) {
    const row  = document.getElementById('expand-' + id);
    const chev = document.getElementById('chev-' + id);
    if (!row) return;
    const isOpen = row.classList.contains('open');
    document.querySelectorAll('.expand-row').forEach(r => r.classList.remove('open'));
    document.querySelectorAll('.chev').forEach(c => c.classList.remove('open'));
    if (!isOpen) { row.classList.add('open'); chev?.classList.add('open'); }
}

function openPayModal(enrollmentId, studentName, remaining, nextAmount, nextDate) {
    document.getElementById('modal-student-name').textContent      = studentName;
    document.getElementById('modal-remaining').textContent         = remaining;
    document.getElementById('modal-installment-amount').textContent = nextAmount + ' LE';
    document.getElementById('modal-installment-date').textContent  = 'Due: ' + nextDate;
    document.getElementById('modal-amount').value                  = nextAmount;
    document.getElementById('payForm').action                      = `/outstanding/${enrollmentId}/pay`;
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