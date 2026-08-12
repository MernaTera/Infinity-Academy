@extends('admin.layouts.app')
@section('title', 'Manual Transactions')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --purple:#7C3AED; --purple-dk:#6D28D9; --purple-l:rgba(124,58,237,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }
    .mtx-page { background:var(--bg); min-height:100vh; padding:32px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .mtx-wrap { max-width:1300px; margin:0 auto; }

    .mtx-header { margin-bottom:26px; }
    .mtx-eyebrow { font-size:10px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:4px; font-weight:600; }
    .mtx-title { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:3px; color:var(--blue); margin:0; }
    .mtx-sub { font-size:12px; color:var(--muted); margin-top:4px; }

    /* Success flash */
    .flash-ok { background:var(--green-l); border:1px solid rgba(5,150,105,0.25); border-left:3px solid var(--green); color:var(--green-dk); padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .flash-err { background:var(--red-l); border:1px solid rgba(220,38,38,0.25); border-left:3px solid var(--red); color:var(--red); padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .flash-err ul { margin:6px 0 0; padding-left:18px; }

    .mtx-grid { display:grid; grid-template-columns:380px 1fr; gap:22px; align-items:start; }
    @media (max-width:1000px){ .mtx-grid{ grid-template-columns:1fr; } }

    /* FORM CARD */
    .form-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); position:sticky; top:20px; }
    .form-card-head { background:linear-gradient(135deg, var(--dark), #243B69); padding:18px 22px; }
    .form-card-eyebrow { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--orange); font-weight:600; margin-bottom:3px; }
    .form-card-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; color:#fff; }
    .form-card-body { padding:22px; }

    .fld { margin-bottom:16px; }
    .fld-label { display:block; font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .fld-label .req { color:var(--red); }
    .fld-input, .fld-select, .fld-textarea {
        width:100%; padding:11px 13px; border:1px solid rgba(27,79,168,0.15); border-radius:8px;
        font-size:13px; color:var(--text); background:#fff; font-family:'DM Sans',sans-serif;
    }
    .fld-input:focus, .fld-select:focus, .fld-textarea:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08); }
    .fld-textarea { resize:vertical; min-height:70px; }
    .amount-wrap { position:relative; }
    .amount-wrap .cur { position:absolute; right:14px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--faint); font-weight:600; pointer-events:none; }

    .btn-submit { width:100%; padding:13px; border:none; background:var(--orange); color:#fff; border-radius:8px; font-size:12px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; cursor:pointer; box-shadow:0 4px 14px rgba(245,145,30,0.3); transition:all 0.2s; }
    .btn-submit:hover { background:var(--orange-dk); transform:translateY(-1px); }

    /* TABLE */
    .tbl-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .tbl-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .tbl-head-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; color:var(--blue); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:820px; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:12px 14px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:var(--bg); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:12px 14px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:var(--blue-l); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }

    .amt { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:0.5px; color:var(--green-dk); }
    .amt.neg { color:var(--red); }

    .tag { display:inline-block; padding:3px 9px; border-radius:6px; font-size:10px; font-weight:600; white-space:nowrap; }
    .tag-course { background:var(--blue-l); color:var(--blue); }
    .tag-material { background:var(--orange-l); color:var(--orange-dk); }
    .tag-test { background:var(--purple-l); color:var(--purple-dk); }
    .tag-other { background:rgba(122,138,154,0.1); color:var(--muted); }

    .method-chip { display:inline-block; padding:3px 9px; border-radius:12px; font-size:10px; font-weight:600; background:var(--green-l); color:var(--green-dk); white-space:nowrap; }

    .notes-cell { max-width:200px; font-size:11px; color:#4A5A7A; white-space:normal; word-break:break-word; line-height:1.4; }

    .tbl-empty { text-align:center; padding:44px 20px; color:var(--faint); }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-top:10px; }

    .pager { padding:14px 20px; border-top:1px solid var(--border); }

    @media (max-width:600px){ .mtx-page{ padding:16px; } }
</style>

<div class="mtx-page">
    <div class="mtx-wrap">

        {{-- HEADER --}}
        <div class="mtx-header">
            <div class="mtx-eyebrow">Finance</div>
            <h1 class="mtx-title">Manual Transactions</h1>
            <div class="mtx-sub">Record an ad-hoc payment or adjustment and review the full transaction log</div>
        </div>

        {{-- FLASH --}}
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

            {{-- ADD FORM --}}
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
                            <label class="fld-label">Reference No. <span style="color:var(--faint);font-weight:400;">(optional)</span></label>
                            <input type="text" name="reference_number" class="fld-input"
                                   placeholder="e.g. receipt / transfer ref" value="{{ old('reference_number') }}">
                        </div>

                        <div class="fld">
                            <label class="fld-label">Notes <span style="color:var(--faint);font-weight:400;">(optional)</span></label>
                            <textarea name="notes" class="fld-textarea" placeholder="Reason or details for this transaction…">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn-submit">Record Transaction</button>
                    </form>
                </div>
            </div>

            {{-- TRANSACTION LOG --}}
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
                <div class="pager">{{ $transactions->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>

@endsection