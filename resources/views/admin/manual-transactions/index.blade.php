@extends('admin.layouts.app')
@section('title', 'Manual Transactions')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.08);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.10);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.10);
        --purple:#7C3AED; --purple-dk:#6D28D9; --purple-l:rgba(124,58,237,0.10);
        --red:#DC2626; --red-l:rgba(220,38,38,0.08);
        --dark:#0F1F3D; --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:rgba(255,255,255,0.5); --border:rgba(27,79,168,0.08);
        --glass-bd:rgba(255,255,255,0.6); --glass-sh:0 8px 30px rgba(23,45,90,0.07);
    }
    * { box-sizing:border-box; }
    .mtx-page { background:var(--bg); min-height:100vh; padding:32px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }
    .mtx-wrap { max-width:1300px; margin:0 auto; }

    .mtx-header { margin-bottom:26px; }
    .mtx-eyebrow { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:var(--blue); margin-bottom:4px; font-weight:600; }
    .mtx-title { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:3px; color:var(--text); margin:0; }
    .mtx-sub { font-size:12px; color:var(--muted); margin-top:4px; }

    .flash-ok { background:var(--green-l); border:1px solid rgba(5,150,105,0.22); color:var(--green-dk); padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:13px; -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); }
    .flash-err { background:var(--red-l); border:1px solid rgba(220,38,38,0.22); color:var(--red); padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:13px; -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); }
    .flash-err ul { margin:6px 0 0; padding-left:18px; }

    .mtx-grid { display:grid; grid-template-columns:380px 1fr; gap:22px; align-items:start; }
    @media (max-width:1000px){ .mtx-grid{ grid-template-columns:1fr; } }

    .form-card { background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:18px; overflow:hidden; box-shadow:var(--glass-sh); position:sticky; top:20px; }
    .form-card-head { position:relative; background:rgba(255,255,255,0.35); border-bottom:1px solid var(--border); padding:18px 22px; }
    /* .form-card-head::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,transparent,var(--blue),transparent); } */
    .form-card-eyebrow { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); font-weight:600; margin-bottom:3px; }
    .form-card-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; color:var(--text); }
    .form-card-body { padding:22px; }

    .fld { margin-bottom:16px; }
    .fld-label { display:block; font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .fld-label .req { color:var(--red); }
    .fld-input, .fld-select, .fld-textarea {
        width:100%; padding:11px 13px; border:1px solid rgba(27,79,168,0.14); border-radius:10px;
        font-size:13px; color:var(--text); background:rgba(255,255,255,0.7); font-family:'DM Sans',sans-serif;
    }
    .fld-input:focus, .fld-select:focus, .fld-textarea:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }
    .fld-textarea { resize:vertical; min-height:70px; }
    .amount-wrap { position:relative; }
    .amount-wrap .cur { position:absolute; right:14px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--faint); font-weight:600; pointer-events:none; }

    .btn-submit { width:100%; padding:13px; border:none; background:linear-gradient(120deg,var(--blue),var(--blue-2)); color:#fff; border-radius:12px; font-size:12px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; cursor:pointer; box-shadow:0 10px 24px rgba(27,79,168,0.28); transition:all 0.2s; }
    .btn-submit:hover { transform:translateY(-2px); box-shadow:0 14px 30px rgba(27,79,168,0.36); }

    .tbl-card { background:var(--card); -webkit-backdrop-filter:blur(22px) saturate(165%); backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--glass-bd); border-radius:18px; overflow:hidden; box-shadow:var(--glass-sh); }
    .tbl-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .tbl-head-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; color:var(--text); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:820px; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:12px 14px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:rgba(255,255,255,0.35); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:12px 14px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:rgba(27,79,168,0.03); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }

    .amt { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:0.5px; color:var(--green-dk); }
    .amt.neg { color:var(--red); }

    .tag { display:inline-block; padding:3px 9px; border-radius:6px; font-size:10px; font-weight:600; white-space:nowrap; border:1px solid transparent; }
    .tag-course { background:var(--blue-l); color:var(--blue); border-color:rgba(27,79,168,0.15); }
    .tag-material { background:var(--orange-l); color:var(--orange-dk); border-color:rgba(245,145,30,0.22); }
    .tag-test { background:var(--purple-l); color:var(--purple-dk); border-color:rgba(124,58,237,0.18); }
    .tag-other { background:rgba(122,138,154,0.10); color:var(--muted); border-color:rgba(122,138,154,0.18); }

    .method-chip { display:inline-block; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:600; background:var(--green-l); color:var(--green-dk); border:1px solid rgba(5,150,105,0.2); white-space:nowrap; }

    .notes-cell { max-width:200px; font-size:11px; color:var(--muted); white-space:normal; word-break:break-word; line-height:1.4; }

    .tbl-empty { text-align:center; padding:44px 20px; color:var(--faint); }
    .tbl-empty svg { opacity:0.3; }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-top:10px; }

    .pager { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:12px 20px; border-top:1px solid var(--border); background:rgba(255,255,255,0.3); }
    .pager-info { font-size:11px; color:var(--faint); }
    .pager-links { display:flex; gap:4px; flex-wrap:wrap; }
    .pg { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 9px; border-radius:9px; font-size:11px; font-weight:600; color:var(--muted); text-decoration:none; border:1px solid rgba(27,79,168,0.12); background:rgba(255,255,255,0.6); transition:all 0.2s; }
    .pg:hover { color:var(--blue); border-color:var(--blue); text-decoration:none; }
    .pg.active { color:#fff; border-color:transparent; background:linear-gradient(120deg,var(--blue),var(--blue-2)); box-shadow:0 6px 14px rgba(27,79,168,0.25); }
    .pg.disabled { opacity:0.4; pointer-events:none; }

    @media (max-width:600px){ .mtx-page{ padding:16px; } }
</style>

<div class="mtx-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="mtx-wrap">

        <div class="mtx-header">
            <div class="mtx-eyebrow">Finance</div>
            <h1 class="mtx-title">Manual Transactions</h1>
            <div class="mtx-sub">Record an ad-hoc payment or adjustment and review the full transaction log</div>
        </div>

        @if(session('success'))
        <div class="flash-ok">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="flash-err">
            Please fix the following:
            <ul>
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="mtx-grid">

            <div class="form-card">
                <div class="form-card-head">
                    <div class="form-card-eyebrow">New Entry</div>
                    <div class="form-card-title">Add Transaction</div>
                </div>
                <div class="form-card-body">
                    <form method="POST" action="{{ route('admin.manual-transactions.store') }}">
                        @csrf

                        <div class="fld">
                            <label class="fld-label">Enrollment <span class="req">*</span></label>
                            <select name="enrollment_id" class="fld-select" required>
                                <option value="">— Select enrollment —</option>
                                @foreach($enrollments as $en)
                                <option value="{{ $en['id'] }}" {{ old('enrollment_id') == $en['id'] ? 'selected' : '' }}>
                                    {{ $en['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="fld">
                            <label class="fld-label">Amount <span class="req">*</span></label>
                            <div class="amount-wrap">
                                <input type="number" name="amount" step="0.01" min="0.01" class="fld-input"
                                       placeholder="0.00" value="{{ old('amount') }}" required>
                                <span class="cur">LE</span>
                            </div>
                        </div>

                        <div class="fld">
                            <label class="fld-label">Payment Method <span class="req">*</span></label>
                            <select name="payment_method" class="fld-select" required>
                                <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Instapay" {{ old('payment_method') === 'Instapay' ? 'selected' : '' }}>Instapay</option>
                                <option value="Vodafone_Cash" {{ old('payment_method') === 'Vodafone_Cash' ? 'selected' : '' }}>Vodafone Cash</option>
                            </select>
                        </div>

                        <div class="fld">
                            <label class="fld-label">Category <span class="req">*</span></label>
                            <select name="transaction_category" class="fld-select" required>
                                <option value="Course" {{ old('transaction_category') === 'Course' ? 'selected' : '' }}>Course</option>
                                <option value="Material" {{ old('transaction_category') === 'Material' ? 'selected' : '' }}>Material</option>
                                <option value="Test" {{ old('transaction_category') === 'Test' ? 'selected' : '' }}>Test</option>
                                <option value="Other" {{ old('transaction_category') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="fld">
                            <label class="fld-label">Notes <span style="color:var(--faint);font-weight:400;">(optional)</span></label>
                            <textarea name="notes" class="fld-textarea" placeholder="Reason or details for this transaction…">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn-submit">Record Transaction</button>
                    </form>
                </div>
            </div>

            <div class="tbl-card">
                <div class="tbl-head">
                    <span class="tbl-head-title">Transaction Log</span>
                    <span style="font-size:11px;color:var(--muted);">{{ $transactions->total() }} total</span>
                </div>
                <div class="tbl-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Category</th>
                                <th>Method</th>
                                <th class="num">Amount</th>
                                <th>Added By</th>
                                <th>Notes</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                            @php
                                $catClass = match($tx->transaction_category) {
                                    'Course'   => 'tag-course',
                                    'Material' => 'tag-material',
                                    'Test'     => 'tag-test',
                                    default    => 'tag-other',
                                };
                                $methodLabel = match($tx->payment_method) {
                                    'Transfer' => 'Instapay',
                                    'Online'   => 'Vodafone Cash',
                                    default    => $tx->payment_method,
                                };
                                $isNeg = $tx->transaction_type === 'Refund';
                            @endphp
                            <tr>
                                <td style="color:var(--faint);font-variant-numeric:tabular-nums;">{{ $tx->transaction_id }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $tx->enrollment?->student?->full_name ?? 'Student #'.($tx->enrollment?->student_id ?? '?') }}</div>
                                    <div style="font-size:10px;color:var(--muted);margin-top:1px;">Enrollment #{{ $tx->enrollment_id }}</div>
                                </td>
                                <td><span class="tag {{ $catClass }}">{{ $tx->transaction_category }}</span></td>
                                <td><span class="method-chip">{{ $methodLabel }}</span></td>
                                <td class="num"><span class="amt {{ $isNeg ? 'neg' : '' }}">{{ $isNeg ? '−' : '' }}{{ number_format($tx->amount, 2) }}</span></td>
                                <td>
                                    @if($tx->createdBy)
                                        <span style="font-size:11px;color:var(--text);">{{ $tx->createdBy->full_name ?? ('Employee #'.$tx->created_by_employee_id) }}</span>
                                    @else
                                        <span style="color:var(--faint);">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->notes)
                                        <div class="notes-cell">{{ $tx->notes }}</div>
                                    @else
                                        <span style="color:var(--faint);">—</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;font-size:11px;color:var(--muted);">
                                    {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y') }}
                                    <div style="font-size:10px;color:var(--faint);">{{ \Carbon\Carbon::parse($tx->created_at)->format('h:i A') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    <div class="tbl-empty">
                                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        <div class="tbl-empty-title">No Transactions Yet</div>
                                        <div style="font-size:12px;">Recorded transactions will appear here.</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                @php
                    $tCur  = $transactions->currentPage();
                    $tLast = $transactions->lastPage();
                    $tFrom = max(1, $tCur - 2);
                    $tTo   = min($tLast, $tCur + 2);
                @endphp
                <div class="pager">
                    <div class="pager-info">Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}</div>
                    <div class="pager-links">
                        <a href="{{ $transactions->previousPageUrl() ?? '#' }}" class="pg {{ $transactions->onFirstPage() ? 'disabled' : '' }}">‹</a>
                        @if($tFrom > 1)
                            <a href="{{ $transactions->url(1) }}" class="pg">1</a>
                            @if($tFrom > 2)<span class="pg disabled">…</span>@endif
                        @endif
                        @for($i = $tFrom; $i <= $tTo; $i++)
                            <a href="{{ $transactions->url($i) }}" class="pg {{ $i === $tCur ? 'active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if($tTo < $tLast)
                            @if($tTo < $tLast - 1)<span class="pg disabled">…</span>@endif
                            <a href="{{ $transactions->url($tLast) }}" class="pg">{{ $tLast }}</a>
                        @endif
                        <a href="{{ $transactions->nextPageUrl() ?? '#' }}" class="pg {{ $transactions->hasMorePages() ? '' : 'disabled' }}">›</a>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

@endsection