@extends('layouts.leads')
@section('title', 'Outstanding Balances')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
:root {
    --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
    --green:#059669; --green-dk:#15803D; --red:#DC2626; --purple:#7C3AED;
    --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
    --bg:#F8F6F2; --card:rgba(255,255,255,0.5); --glass-bd:rgba(255,255,255,0.65);
    --glass-sh:0 12px 36px -12px rgba(23,45,90,0.18); --track:rgba(27,79,168,0.08);
}
*{box-sizing:border-box}

.os-page{
    min-height:100vh; padding:40px 34px 52px; font-family:'DM Sans',sans-serif; color:var(--text);
    background: #F8F6F2;
}
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

.glass{background:var(--card);-webkit-backdrop-filter:blur(24px) saturate(180%);backdrop-filter:blur(24px) saturate(180%);border:1px solid var(--glass-bd);box-shadow:var(--glass-sh)}

.os-eyebrow{font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:7px;font-weight:600}
.os-title{font-family:'Bebas Neue',sans-serif;font-size:42px;letter-spacing:2px;color:var(--text);margin:0 0 26px;line-height:0.95}

.alert{padding:13px 18px;border-radius:14px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px}
.alert-success{background:rgba(5,150,105,0.1);border:1px solid rgba(5,150,105,0.2);color:var(--green-dk)}
.alert-error{background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);color:var(--red)}

.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:28px}
.kpi-card{border-radius:18px;padding:20px 18px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.kpi-card:hover{transform:translateY(-3px);box-shadow:0 18px 42px -14px rgba(23,45,90,0.28)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--kc,var(--blue))}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:8px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:1px;color:var(--kc,var(--blue));line-height:1}
.kpi-sub{font-size:10px;color:var(--faint);margin-top:5px}

.os-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:22px;flex-wrap:wrap}
.os-search-wrap{position:relative;flex:1;min-width:220px;max-width:420px}
.os-search-wrap svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--faint)}
.os-search{width:100%;padding:12px 14px 12px 40px;border:1px solid var(--glass-bd);border-radius:13px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.5);-webkit-backdrop-filter:blur(12px);backdrop-filter:blur(12px);outline:none;transition:border-color .25s,box-shadow .25s}
.os-search:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.1)}
.pills{display:flex;gap:7px;flex-wrap:wrap}
.pill{padding:8px 15px;border:1px solid var(--glass-bd);border-radius:13px;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);cursor:pointer;transition:all .2s;background:rgba(255,255,255,0.5);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);font-family:'DM Sans',sans-serif;font-weight:600}
.pill:hover{border-color:var(--blue);color:var(--blue)}
.pill.active{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;border-color:transparent;box-shadow:0 8px 20px rgba(27,79,168,0.28)}
.pill.p-red.active{background:var(--red);box-shadow:0 8px 20px rgba(220,38,38,0.28)}
.pill.p-orange.active{background:var(--orange);box-shadow:0 8px 20px rgba(245,145,30,0.28)}
.pill.p-green.active{background:var(--green);box-shadow:0 8px 20px rgba(5,150,105,0.28)}

.sec-lbl{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--orange-dk);font-weight:700;margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.18);display:flex;align-items:center;justify-content:space-between}
.sec-lbl-count{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:var(--faint)}

.tbl-card{background:rgba(255,255,255,0.72);border:1px solid var(--glass-bd);border-radius:20px;overflow:hidden;box-shadow:var(--glass-sh)}
.tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.tbl{width:100%;border-collapse:collapse;min-width:860px}
.tbl thead th{padding:14px;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:700;background:rgba(255,255,255,0.55);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);border-bottom:1px solid var(--glass-bd);white-space:nowrap}
.tbl tbody tr.main-row{border-bottom:1px solid rgba(27,79,168,0.06);cursor:pointer;transition:background .15s}
.tbl tbody tr.main-row:hover{background:rgba(27,79,168,0.04)}
.tbl td{padding:14px;font-size:13px;color:var(--muted);vertical-align:middle}

.expand-row{display:none}
.expand-row.open{display:table-row}
.expand-inner{padding:18px 20px 20px;background:rgba(255,255,255,0.45);border-top:1px solid var(--glass-bd)}
.expand-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}

.mini-tbl{width:100%;border-collapse:collapse}
.mini-tbl th{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);padding:6px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.08);font-weight:700}
.mini-tbl td{font-size:12px;color:var(--muted);padding:8px 10px;border-bottom:1px solid rgba(27,79,168,0.05)}
.mini-tbl tr:last-child td{border-bottom:none}
.mini-section-lbl{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--orange-dk);margin-bottom:8px;margin-top:4px;font-weight:600}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:0.5px;text-transform:uppercase;padding:4px 10px;border-radius:20px;font-weight:600;white-space:nowrap}
.b-restricted{background:rgba(220,38,38,0.1);color:var(--red);border:1px solid rgba(220,38,38,0.18)}
.b-overdue{background:rgba(245,145,30,0.12);color:var(--orange-dk);border:1px solid rgba(245,145,30,0.2)}
.b-ontrack{background:rgba(5,150,105,0.1);color:var(--green-dk);border:1px solid rgba(5,150,105,0.18)}
.b-paid{background:rgba(5,150,105,0.1);color:var(--green-dk);border:1px solid rgba(5,150,105,0.18)}
.b-pending{background:rgba(27,79,168,0.08);color:var(--blue);border:1px solid rgba(27,79,168,0.15)}
.b-finished{background:rgba(5,150,105,0.1);color:var(--green-dk);border:1px solid rgba(5,150,105,0.2)}

.prog-wrap{display:flex;align-items:center;gap:8px}
.prog-track{flex:1;max-width:72px;background:var(--track);border-radius:3px;height:5px;overflow:hidden}
.prog-fill{height:5px;border-radius:3px;transition:width .6s ease}

.btn-pay{display:inline-flex;align-items:center;gap:5px;padding:8px 15px;background:rgba(5,150,105,0.06);border:1.5px solid rgba(5,150,105,0.4);border-radius:11px;color:var(--green-dk);font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .25s;font-weight:600}
.btn-pay:hover{background:var(--green);color:#fff;border-color:var(--green);box-shadow:0 8px 18px rgba(5,150,105,0.3)}

.chev{transition:transform .25s;display:inline-block;margin-left:5px;opacity:0.35;vertical-align:middle}
.chev.open{transform:rotate(180deg);opacity:0.7}

.fin-section{margin-top:34px}
.fin-banner{display:flex;align-items:center;gap:12px;padding:15px 18px;border-radius:16px;border-left:4px solid var(--green);margin-bottom:16px}
.fin-banner-icon{width:34px;height:34px;border-radius:50%;background:rgba(5,150,105,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.fin-banner-text{font-size:12px;color:var(--muted);line-height:1.5}
.fin-banner-text strong{color:var(--text)}

.modal-backdrop{display:none;position:fixed;inset:0;z-index:1050;background:rgba(15,31,61,0.5);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:24px}
.modal-backdrop.show{display:flex;animation:fadein .2s ease both}
@keyframes fadein{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:440px;background:rgba(255,255,255,0.92);-webkit-backdrop-filter:blur(28px) saturate(160%);backdrop-filter:blur(28px) saturate(160%);border:1px solid var(--glass-bd);border-radius:22px;overflow:hidden;position:relative;box-shadow:0 30px 70px -18px rgba(23,45,90,0.4);animation:slidein .3s cubic-bezier(0.16,1,0.3,1) both}
@keyframes slidein{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange),var(--blue),var(--purple))}
.modal-header{padding:22px 24px 16px;border-bottom:1px solid var(--glass-bd)}
.modal-eyebrow{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--orange-dk);margin-bottom:4px;font-weight:600}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:2px;color:var(--text)}
.modal-body{padding:20px 24px}
.modal-footer{padding:14px 24px 20px;border-top:1px solid var(--glass-bd);display:flex;gap:10px;justify-content:flex-end}
.form-lbl{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:7px;display:block;font-weight:600}
.form-ctrl{width:100%;padding:11px 13px;border:1px solid rgba(27,79,168,0.12);border-radius:12px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.6);outline:none;box-sizing:border-box;margin-bottom:14px;transition:border-color .25s,box-shadow .25s}
.form-ctrl:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.1)}
.remaining-hint{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.14);border-radius:12px;padding:11px 14px;margin-bottom:16px;font-size:12px;color:var(--red);display:flex;align-items:center;gap:8px}
.next-inst-box{background:rgba(27,79,168,0.05);border:1px solid rgba(27,79,168,0.12);border-radius:14px;padding:14px 16px;margin-bottom:16px}
.next-inst-lbl{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:6px;font-weight:600}
.next-inst-amt{font-family:'Bebas Neue',sans-serif;font-size:30px;color:var(--blue);letter-spacing:2px;line-height:1}
.next-inst-date{font-size:10px;color:var(--faint);margin-top:4px}
.btn-cancel-modal{padding:10px 20px;background:rgba(255,255,255,0.5);border:1px solid var(--glass-bd);border-radius:12px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all .2s;font-weight:600}
.btn-cancel-modal:hover{border-color:var(--blue);color:var(--blue)}
.btn-confirm{padding:11px 26px;background:linear-gradient(120deg,var(--green),#0E9F6E);border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2px;cursor:pointer;transition:box-shadow .25s,filter .25s;box-shadow:0 10px 24px rgba(5,150,105,0.32)}
.btn-confirm:hover{filter:brightness(1.06);box-shadow:0 14px 30px rgba(5,150,105,0.42)}

@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.kpi-grid{grid-template-columns:1fr 1fr};.os-page{padding:20px 14px 36px};.expand-grid{grid-template-columns:1fr};.os-title{font-size:34px}}
</style>

<div class="os-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="os-eyebrow">Customer Service</div>
    <h1 class="os-title">Outstanding Balances</h1>

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

    <div class="kpi-grid">
        <div class="kpi-card glass" style="--kc:#DC2626">
            <div class="kpi-label">Total Outstanding</div>
            <div class="kpi-val">{{ number_format($summary['total_outstanding']) }}</div>
            <div class="kpi-sub">LE unpaid</div>
        </div>
        <div class="kpi-card glass" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Cases</div>
            <div class="kpi-val">{{ $summary['total_students'] }}</div>
            <div class="kpi-sub">with balance</div>
        </div>
        <div class="kpi-card glass" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $summary['restricted_count'] }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </div>
        <div class="kpi-card glass" style="--kc:#F5911E">
            <div class="kpi-label">Overdue</div>
            <div class="kpi-val">{{ $summary['overdue_count'] }}</div>
            <div class="kpi-sub">past due date</div>
        </div>
        <div class="kpi-card glass" style="--kc:#059669">
            <div class="kpi-label">Fully Paid</div>
            <div class="kpi-val">{{ $summary['finished_count'] }}</div>
            <div class="kpi-sub">this cycle</div>
        </div>
    </div>

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
                        <div style="font-weight:600;color:#16233F;font-size:13px">
                            {{ $row['student_name'] }}
                            <svg class="chev" id="chev-{{ $row['enrollment_id'] }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div style="font-size:10px;color:#93A3BC;margin-top:2px;text-transform:uppercase;letter-spacing:1px">{{ $row['enrollment_type'] }}</div>
                    </td>

                    <td style="font-size:12px;color:#5A6A85">{{ $row['course'] }}</td>
                    <td style="font-size:11px;color:#93A3BC">{{ $row['payment_plan'] }}</td>

                    <td style="font-family:monospace;font-size:12px;color:#16233F">
                        {{ number_format($row['total']) }} <span style="color:#93A3BC;font-size:10px">LE</span>
                    </td>

                    <td>
                        <div class="prog-wrap">
                            <span style="font-family:monospace;font-size:12px;color:#059669">{{ number_format($row['paid']) }}</span>
                            <div class="prog-track">
                                <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                            </div>
                            <span style="font-size:10px;color:#93A3BC">{{ $pct }}%</span>
                        </div>
                    </td>

                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:{{ $row['remaining'] > 3000 ? '#DC2626' : '#C47010' }}">
                            {{ number_format($row['remaining']) }}
                        </span>
                        <span style="font-size:10px;color:#93A3BC"> LE</span>
                    </td>

                    <td onclick="event.stopPropagation()">
                        @if(!empty($row['has_pending_installment']) && empty($row['next_due_date']))
                            <div style="font-size:11px;color:#93A3BC;font-style:italic;line-height:1.4">
                                Upon course<br>assignment
                            </div>
                        @elseif($row['next_due_date'])
                            <div style="font-size:12px;color:#16233F;font-weight:500">{{ $row['next_due_date'] }}</div>
                            @if($row['next_due_amount'])
                            <div style="font-size:10px;color:#5A6A85;margin-top:2px">{{ number_format($row['next_due_amount']) }} LE</div>
                            @endif
                            @if($row['days_overdue'])
                            <div style="font-size:10px;color:#DC2626;margin-top:2px;font-weight:500">{{ $row['days_overdue'] }}d overdue</div>
                            @endif
                        @else
                            <span style="color:#93A3BC;font-size:11px">—</span>
                        @endif
                    </td>

                    <td onclick="event.stopPropagation()">
                        @if($row['is_restricted'])
                            <span class="badge b-restricted">Restricted</span>
                            @if($row['restriction_reason'])
                            <div style="font-size:9px;color:#93A3BC;margin-top:3px">{{ str_replace('_',' ',$row['restriction_reason']) }}</div>
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

                <tr class="expand-row" id="expand-{{ $row['enrollment_id'] }}">
                    <td colspan="9" style="padding:0">
                        <div class="expand-inner">

                                <div>
                                    <div class="mini-section-lbl">Installment Schedule</div>
                                    @if(!empty($row['installments']))
                                    <table class="mini-tbl">
                                        <thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Paid At</th></tr></thead>
                                        <tbody>
                                        @foreach($row['installments'] as $inst)
                                        <tr>
                                            <td style="color:#93A3BC">{{ $inst['number'] }}</td>
                                            <td style="font-family:monospace">{{ number_format($inst['amount']) }} LE</td>
                                            <td>
                                                @if(!empty($inst['due_date']))
                                                    {{ $inst['due_date'] }}
                                                @else
                                                    <span style="color:#93A3BC;font-style:italic;font-size:10px">Upon course assignment</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inst['status']==='Paid')     <span class="badge b-paid">Paid</span>
                                                @elseif($inst['status']==='Overdue') <span class="badge b-overdue">Overdue</span>
                                                @else <span class="badge b-pending">Pending</span>
                                                @endif
                                            </td>
                                            <td style="font-size:11px;color:#93A3BC">{{ $inst['paid_at'] ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <div style="font-size:11px;color:#93A3BC;padding:8px 0">No installments scheduled.</div>
                                    @endif
                                </div>

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
                                                <span style="font-size:9px;color:#93A3BC;letter-spacing:1px">({{ $tx['category'] }})</span>
                                            </td>
                                            <td style="font-family:monospace;color:{{ $tx['type']==='Refund'?'#DC2626':'#059669' }}">
                                                {{ $tx['type']==='Refund'?'-':'+' }}{{ number_format($tx['amount']) }} LE
                                            </td>
                                            <td style="color:#93A3BC">
                                                @if(($tx['method_count'] ?? 1) > 1 && !empty($tx['methods_breakdown']))
                                                    <div style="font-size:11px;color:#5A6A85;font-weight:600;">Multi-method</div>
                                                    @foreach($tx['methods_breakdown'] as $mb)
                                                        <div style="font-size:10px;color:#93A3BC;">
                                                            {{ str_replace('_', ' ', $mb['method']) }}: <span style="font-family:monospace;">{{ number_format($mb['amount']) }} LE</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    {{ str_replace('_', ' ', $tx['method']) }}
                                                @endif
                                            </td>
                                            <td style="font-size:11px;color:#5A6A85">{{ $tx['notes'] ?? '—' }}</td>
                                            <td style="font-size:11px;color:#93A3BC">{{ $tx['date'] }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <div style="font-size:11px;color:#93A3BC">No history available.</div>
                                    @endif
                                </div>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div style="text-align:center;padding:56px;color:#93A3BC">
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

    @php $finishedRows = $rows->where('is_finished', true); @endphp
    @if($finishedRows->count())
    <div class="fin-section" id="finishedSection" style="display:none">

        <div class="fin-banner glass">
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
                            <div style="font-weight:600;color:#16233F;font-size:13px">
                                {{ $row['student_name'] }}
                                <svg class="chev" id="chev-fin_{{ $row['enrollment_id'] }}" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                            <div style="font-size:10px;color:#93A3BC;margin-top:2px;text-transform:uppercase;letter-spacing:1px">{{ $row['enrollment_type'] }}</div>
                        </td>
                        <td style="font-size:12px;color:#5A6A85">{{ $row['course'] }}</td>
                        <td style="font-size:11px;color:#93A3BC">{{ $row['payment_plan'] }}</td>
                        <td style="font-family:monospace;font-size:12px;color:#16233F">{{ number_format($row['total']) }} LE</td>
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
                                            <span style="font-size:9px;color:#93A3BC">({{ $tx['category'] }})</span>
                                        </td>
                                        <td style="font-family:monospace;color:#059669">+{{ number_format($tx['amount']) }} LE</td>
                                        <td style="color:#93A3BC">{{ $tx['method'] }}</td>
                                        <td style="font-size:11px;color:#5A6A85">{{ $tx['notes'] ?? '—' }}</td>
                                        <td style="font-size:11px;color:#93A3BC">{{ $tx['date'] }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @else
                                <div style="font-size:11px;color:#93A3BC">No history available.</div>
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
