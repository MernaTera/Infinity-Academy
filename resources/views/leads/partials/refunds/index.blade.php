@extends('layouts.leads')

@section('title', 'Refund Requests')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }

    .rf-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }

    /* ═══ HEADER ═══ */
    .rf-header {
        max-width:1120px; margin:0 auto 22px;
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px;
        position:relative; overflow:hidden; box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .rf-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .rf-header::after { content:''; position:absolute; bottom:-60px; left:26%; width:150px; height:150px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .rf-header-inner { position:relative; z-index:1; }
    .rf-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .rf-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--orange); box-shadow:0 0 8px var(--orange); }
    .rf-title { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:4px; color:#fff; line-height:1; margin:0; }
    .rf-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }

    .rf-wrap { max-width:1120px; margin:0 auto; }

    /* ═══ POLICY BANNER ═══ */
    .policy-banner {
        background:var(--blue-l); border:1px solid var(--border); border-left:3px solid var(--blue);
        border-radius:10px; padding:14px 18px; margin-bottom:22px;
        display:flex; align-items:flex-start; gap:12px; font-size:13px; color:var(--text); line-height:1.5;
    }
    .policy-banner svg { flex-shrink:0; margin-top:2px; }
    .policy-banner strong { color:var(--blue); font-weight:600; }

    /* ═══ STATS ═══ */
    .rf-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:26px; }
    @media (max-width:600px){ .rf-stats{ grid-template-columns:1fr; } }
    .rf-stat { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:18px 20px; position:relative; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .rf-stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--sc,var(--blue)); }
    .rf-stat-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:8px; }
    .rf-stat-val { font-family:'Bebas Neue',sans-serif; font-size:38px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }

    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin:0 0 16px; font-weight:600; }

    /* ═══ ELIGIBLE CARDS ═══ */
    .elig-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:16px; margin-bottom:32px; }
    .elig-card {
        background:var(--card); border:1px solid var(--border); border-radius:14px;
        overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05);
        transition:transform 0.2s, box-shadow 0.2s;
    }
    .elig-card:hover { transform:translateY(-3px); box-shadow:0 10px 28px rgba(27,79,168,0.1); }
    .elig-card-top { padding:18px 20px 16px; border-bottom:1px solid var(--border); }
    .elig-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:14px; }
    .elig-name { font-size:16px; font-weight:700; color:var(--text); line-height:1.2; }
    .elig-course { font-size:11px; color:var(--muted); margin-top:3px; }
    .elig-timer {
        display:flex; align-items:center; gap:6px; padding:7px 12px; border-radius:9px;
        font-size:11px; font-weight:600; white-space:nowrap; flex-shrink:0;
    }
    .timer-ok   { background:var(--green-l); color:var(--green); }
    .timer-warn { background:var(--orange-l); color:var(--orange-dk); }
    .timer-crit { background:var(--red-l); color:var(--red); }

    .elig-meta { display:flex; flex-direction:column; gap:8px; }
    .elig-meta-row { display:flex; align-items:center; justify-content:space-between; font-size:11px; }
    .elig-meta-key { color:var(--muted); letter-spacing:0.3px; }
    .elig-meta-val { color:var(--text); font-weight:600; }
    .day-pill { display:inline-block; padding:2px 9px; border-radius:12px; font-size:10px; font-weight:600; background:var(--blue-l); color:var(--blue); }

    /* Payment methods breakdown */
    .method-breakdown { margin-top:12px; padding-top:12px; border-top:1px dashed var(--border); }
    .method-breakdown-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--faint); font-weight:600; margin-bottom:8px; }
    .method-chips { display:flex; flex-wrap:wrap; gap:6px; }
    .method-chip {
        display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:7px;
        font-size:11px; font-weight:500; background:var(--bg); border:1px solid var(--border);
    }
    .method-chip .mc-method { color:var(--muted); }
    .method-chip .mc-amount { color:var(--text); font-weight:700; }
    .method-chip .mc-dot { width:7px; height:7px; border-radius:50%; }
    .mc-cash    { background:#15803D; }
    .mc-transfer{ background:#1B4FA8; }
    .mc-online  { background:#F5911E; }
    .mc-card    { background:#7A8A9A; }

    .elig-card-bottom { padding:16px 20px; display:flex; align-items:center; justify-content:space-between; gap:14px; }
    .elig-total { }
    .elig-total-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--faint); font-weight:600; margin-bottom:3px; }
    .elig-total-val { font-family:'Bebas Neue',sans-serif; font-size:26px; letter-spacing:1px; color:var(--blue); line-height:1; }
    .btn-refund {
        display:inline-flex; align-items:center; gap:7px; padding:11px 20px;
        background:transparent; border:1.5px solid var(--red); border-radius:8px;
        color:var(--red); font-family:'DM Sans',sans-serif; font-size:11px; font-weight:600;
        letter-spacing:0.5px; cursor:pointer; transition:all 0.2s; white-space:nowrap;
    }
    .btn-refund:hover { background:var(--red); color:#fff; }
    .btn-refund:disabled { opacity:0.4; cursor:not-allowed; }

    .elig-empty {
        grid-column:1/-1; text-align:center; padding:50px 20px;
        background:var(--card); border:1px dashed var(--border); border-radius:14px; color:var(--faint);
    }
    .elig-empty svg { opacity:0.35; margin-bottom:12px; }
    .elig-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-bottom:4px; }
    .elig-empty-sub { font-size:12px; }

    /* ═══ REQUESTS TABLE ═══ */
    .rf-table-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .rf-table-scroll { overflow-x:auto; }
    .rf-table { width:100%; border-collapse:collapse; min-width:760px; }
    .rf-table thead th { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); padding:14px 18px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:var(--bg); white-space:nowrap; }
    .rf-table tbody td { padding:14px 18px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .rf-table tbody tr:last-child td { border-bottom:none; }
    .rf-table tbody tr:hover { background:var(--blue-l); }
    .rf-amount { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:1px; color:var(--blue); }

    .rf-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:10px; font-weight:600; letter-spacing:0.3px; white-space:nowrap; }
    .rf-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .rfb-pending  { background:var(--orange-l); color:var(--orange-dk); }
    .rfb-approved { background:var(--blue-l); color:var(--blue); }
    .rfb-processed{ background:var(--green-l); color:var(--green-dk); }
    .rfb-rejected { background:var(--red-l); color:var(--red); }

    .rf-table-empty { text-align:center; padding:44px 20px; color:var(--faint); font-size:13px; }

    /* ═══ MODAL ═══ */
    .rf-modal { display:none; position:fixed; inset:0; background:rgba(15,31,61,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .rf-modal.open { display:flex; }
    .rf-modal-box { background:var(--card); border-radius:14px; width:90%; max-width:460px; box-shadow:0 20px 60px rgba(15,31,61,0.3); overflow:hidden; }
    .rf-modal-head { padding:20px 24px; background:linear-gradient(135deg, rgba(220,38,38,0.04), transparent); border-bottom:1px solid var(--border); }
    .rf-modal-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; color:var(--text); }
    .rf-modal-body { padding:22px 24px; }
    .rf-warn { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; background:var(--red-l); border:1px solid rgba(220,38,38,0.15); border-radius:9px; font-size:12px; color:var(--red); margin-bottom:18px; line-height:1.5; }
    .rf-warn svg { flex-shrink:0; margin-top:1px; }
    .rf-field { margin-bottom:16px; }
    .rf-field-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; display:block; }
    .form-control { width:100%; padding:11px 14px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); outline:none; transition:border-color 0.2s, box-shadow 0.2s; }
    .form-control:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }
    .form-control[readonly] { background:var(--bg); color:var(--muted); }
    textarea.form-control { resize:vertical; min-height:80px; }
    .rf-modal-actions { display:flex; justify-content:flex-end; gap:10px; padding:16px 24px; border-top:1px solid var(--border); background:var(--bg); }
    .btn-cancel { padding:10px 20px; background:transparent; border:1px solid var(--border); border-radius:7px; color:var(--muted); font-size:11px; letter-spacing:1px; text-transform:uppercase; font-weight:600; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { border-color:var(--blue); color:var(--blue); }
    .btn-submit-refund { padding:10px 22px; background:var(--red); border:none; border-radius:7px; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:2px; cursor:pointer; transition:background 0.2s; }
    .btn-submit-refund:hover { background:#B91C1C; }

    @media (max-width:600px){ .rf-page{ padding:16px; } }
</style>

<div class="rf-page">

    {{-- ── HEADER ── --}}
    <div class="rf-header">
        <div class="rf-header-inner">
            <div class="rf-eyebrow">Finance</div>
            <h1 class="rf-title">Refund Requests</h1>
            <div class="rf-sub">Request deposit refunds for eligible students</div>
        </div>
    </div>

    <div class="rf-wrap">

        {{-- ── POLICY ── --}}
        <div class="policy-banner">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div><strong>Refund Policy:</strong> Students are eligible for a <strong>full deposit refund</strong> within <strong>3 days</strong> of payment. After 3 days — or once any installment is paid — no refund is applicable. Refund requests require admin approval before processing.</div>
        </div>

        {{-- ── STATS ── --}}
        <div class="rf-stats">
            <div class="rf-stat" style="--sc:var(--green)">
                <div class="rf-stat-label">Eligible Now</div>
                <div class="rf-stat-val">{{ $stats['eligible'] }}</div>
            </div>
            <div class="rf-stat" style="--sc:var(--orange)">
                <div class="rf-stat-label">Pending</div>
                <div class="rf-stat-val">{{ $stats['pending'] }}</div>
            </div>
            <div class="rf-stat" style="--sc:var(--blue)">
                <div class="rf-stat-label">Approved</div>
                <div class="rf-stat-val">{{ $stats['approved'] }}</div>
            </div>
        </div>

        {{-- ── ELIGIBLE ── --}}
        <span class="sec-label">Eligible for Refund</span>
        <div class="elig-grid">
            @forelse($eligibleEnrollments as $enrollment)
                @php
                    $deposits    = $enrollment->financialTransactions;
                    $firstPaid   = $deposits->sortBy('created_at')->first();
                    $depositTotal= $deposits->sum('amount');
                    $daysAgo     = $firstPaid ? (int) $firstPaid->created_at->diffInDays(now()) : 0;
                    $hoursLeft   = $firstPaid ? max(0, 72 - (int) $firstPaid->created_at->diffInHours(now())) : 0;

                    // Merge deposits by payment_method for the breakdown
                    $byMethod = $deposits->groupBy('payment_method')->map(fn($g) => $g->sum('amount'));

                    $timerClass = $hoursLeft > 48 ? 'timer-ok' : ($hoursLeft > 12 ? 'timer-warn' : 'timer-crit');

                    $methodMeta = [
                        'Cash'     => ['label' => 'Cash',      'dot' => 'mc-cash'],
                        'Transfer' => ['label' => 'Instapay',  'dot' => 'mc-transfer'],
                        'Online'   => ['label' => 'Vodafone',  'dot' => 'mc-online'],
                        'Card'     => ['label' => 'Card',      'dot' => 'mc-card'],
                    ];

                    $hasPending = $enrollment->refundRequests
                        ->whereIn('status', ['Pending','Approved'])->isNotEmpty();
                @endphp
                <div class="elig-card">
                    <div class="elig-card-top">
                        <div class="elig-head">
                            <div>
                                <div class="elig-name">{{ $enrollment->student?->full_name ?? 'Student #'.$enrollment->enrollment_id }}</div>
                                <div class="elig-course">{{ $enrollment->courseTemplate?->name }}@if($enrollment->level) · {{ $enrollment->level->name }}@endif</div>
                            </div>
                            <div class="elig-timer {{ $timerClass }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $hoursLeft }}h left
                            </div>
                        </div>

                        <div class="elig-meta">
                            <div class="elig-meta-row">
                                <span class="elig-meta-key">Paid on</span>
                                <span class="elig-meta-val">{{ $firstPaid ? $firstPaid->created_at->format('d M Y · H:i') : '—' }}</span>
                            </div>
                            <div class="elig-meta-row">
                                <span class="elig-meta-key">Window</span>
                                <span class="elig-meta-val"><span class="day-pill">Day {{ min($daysAgo + 1, 3) }} of 3</span></span>
                            </div>
                        </div>

                        {{-- Payment methods breakdown (deposit split) --}}
                        <div class="method-breakdown">
                            <div class="method-breakdown-label">Deposit paid via</div>
                            <div class="method-chips">
                                @foreach($byMethod as $method => $amt)
                                    @php $meta = $methodMeta[$method] ?? ['label' => $method, 'dot' => 'mc-card']; @endphp
                                    <span class="method-chip">
                                        <span class="mc-dot {{ $meta['dot'] }}"></span>
                                        <span class="mc-method">{{ $meta['label'] }}</span>
                                        <span class="mc-amount">{{ number_format($amt, 0) }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="elig-card-bottom">
                        <div class="elig-total">
                            <div class="elig-total-label">Refund Amount</div>
                            <div class="elig-total-val">{{ number_format($depositTotal, 0) }} LE</div>
                        </div>
                        @if($hasPending)
                            <button class="btn-refund" disabled title="Refund already requested">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                Requested
                            </button>
                        @else
                            <button class="btn-refund"
                                onclick="openRefundModal({{ $enrollment->enrollment_id }}, '{{ addslashes($enrollment->student?->full_name ?? 'Student') }}', '{{ addslashes($enrollment->courseTemplate?->name ?? '') }}', {{ $depositTotal }})">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
                                Request Refund
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="elig-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
                    <div class="elig-empty-title">No Eligible Refunds</div>
                    <div class="elig-empty-sub">Enrollments within the 3-day window (with no paid installments) will appear here.</div>
                </div>
            @endforelse
        </div>

        {{-- ── MY REQUESTS ── --}}
        <span class="sec-label">My Refund Requests</span>
        <div class="rf-table-card">
            <div class="rf-table-scroll">
                <table class="rf-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myRequests as $req)
                            @php
                                $rfBadge = match($req->status) {
                                    'Pending'   => ['rfb-pending','Pending'],
                                    'Approved'  => ['rfb-approved','Approved'],
                                    'Processed' => ['rfb-processed','Processed'],
                                    'Rejected'  => ['rfb-rejected','Rejected'],
                                    default     => ['rfb-pending', $req->status],
                                };
                            @endphp
                            <tr>
                                <td style="font-weight:600;">{{ $req->enrollment?->student?->full_name ?? '—' }}</td>
                                <td style="color:var(--muted);">{{ $req->enrollment?->courseTemplate?->name ?? '—' }}</td>
                                <td><span class="rf-amount">{{ number_format($req->amount, 0) }} LE</span></td>
                                <td><span class="rf-badge {{ $rfBadge[0] }}">{{ $rfBadge[1] }}</span></td>
                                <td style="color:var(--faint);">{{ $req->created_at?->format('d M Y · H:i') }}</td>
                                <td style="color:var(--muted);max-width:180px;">
                                    @if($req->rejection_note)
                                        <span title="{{ $req->rejection_note }}" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $req->rejection_note }}</span>
                                    @else
                                        <span style="color:var(--faint);">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"><div class="rf-table-empty">No refund requests yet.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /rf-wrap --}}

    {{-- ── MODAL ── --}}
    <div id="refundModal" class="rf-modal">
        <form method="POST" action="{{ route('refunds.store') }}">
            @csrf
            <div class="rf-modal-box">
                <div class="rf-modal-head">
                    <div class="rf-modal-title">Request Refund</div>
                </div>
                <div class="rf-modal-body">
                    <div class="rf-warn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>This will request a <strong>full deposit refund</strong>. Once submitted, it requires admin approval before processing.</div>
                    </div>

                    <input type="hidden" name="enrollment_id" id="modal_enrollment_id">

                    <div class="rf-field">
                        <label class="rf-field-label">Student</label>
                        <input type="text" id="modal_student" class="form-control" readonly>
                    </div>
                    <div class="rf-field">
                        <label class="rf-field-label">Refund Amount</label>
                        <input type="text" id="modal_amount" class="form-control" readonly>
                    </div>
                    <div class="rf-field" style="margin-bottom:0;">
                        <label class="rf-field-label">Reason <span style="color:var(--red);">*</span></label>
                        <textarea name="reason" id="modal_reason" class="form-control" placeholder="Why is this refund being requested?" required minlength="5"></textarea>
                    </div>
                </div>
                <div class="rf-modal-actions">
                    <button type="button" onclick="closeRefundModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit-refund">Submit Request</button>
                </div>
            </div>
        </form>
    </div>

</div>

@if(session('success'))
<div id="rfToast" style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--green);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(15,31,61,0.18);font-size:13px;color:var(--text);">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
    {{ session('success') }}
</div>
<script>setTimeout(()=>document.getElementById('rfToast')?.remove(),4000);</script>
@endif

@if(session('error'))
<div id="rfErrToast" style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--red);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(15,31,61,0.18);font-size:13px;color:var(--text);max-width:380px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ session('error') }}
</div>
<script>setTimeout(()=>document.getElementById('rfErrToast')?.remove(),5000);</script>
@endif

<script>
function openRefundModal(enrollmentId, student, course, amount) {
    document.getElementById('modal_enrollment_id').value = enrollmentId;
    document.getElementById('modal_student').value       = student + (course ? ' — ' + course : '');
    document.getElementById('modal_amount').value        = Number(amount).toLocaleString() + ' LE';
    document.getElementById('modal_reason').value        = '';
    document.getElementById('refundModal').classList.add('open');
}
function closeRefundModal() {
    document.getElementById('refundModal').classList.remove('open');
}
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) closeRefundModal();
});
</script>

@endsection