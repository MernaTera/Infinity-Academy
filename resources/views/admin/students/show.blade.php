@extends('admin.layouts.app')
@section('title', 'Student Profile')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.sp-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-size:10px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}

.layout{display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;}
@media(max-width:1000px){.layout{grid-template-columns:1fr;}}

/* Cards */
.card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:16px;position:relative;}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.card-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:3px;color:var(--blue);}
.card-body{padding:18px 20px;}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(245,145,30,0.15);display:block;}

/* Profile card */
.profile-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--blue-l),rgba(27,79,168,0.15));display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--blue);margin:0 auto 14px;letter-spacing:2px;border:2px solid rgba(27,79,168,0.1);}
.profile-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--text);text-align:center;margin-bottom:4px;}
.profile-email{font-size:12px;color:var(--faint);text-align:center;margin-bottom:16px;}

/* Meta rows */
.meta-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(27,79,168,0.04);font-size:12px;}
.meta-row:last-child{border-bottom:none;}
.meta-key{color:var(--faint);font-size:10px;letter-spacing:1px;text-transform:uppercase;}
.meta-val{color:var(--text);font-weight:500;text-align:right;}

/* Status badges */
.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-active{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-restricted{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.badge-archived{color:var(--faint);background:rgba(170,184,200,0.1);border:1px solid rgba(170,184,200,0.2);}
.badge-pending_approval{color:var(--orange);background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.badge-completed{color:var(--purple);background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}
.badge-waiting{color:#7F77DD;background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}
.badge-cancelled{color:var(--faint);background:rgba(170,184,200,0.08);border:1px solid rgba(170,184,200,0.15);}
.badge-postponed{color:var(--orange);background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}

/* Enrollment card */
.enrollment-card{background:rgba(27,79,168,0.02);border:1px solid rgba(27,79,168,0.08);border-radius:8px;padding:16px;margin-bottom:12px;position:relative;}
.enrollment-card.active-enroll{border-color:rgba(5,150,105,0.2);background:rgba(5,150,105,0.02);}
.enrollment-card.active-enroll::before{content:'Active';position:absolute;top:10px;right:12px;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);padding:2px 7px;border-radius:3px;}
.enroll-course{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--blue);margin-bottom:4px;}
.enroll-meta{font-size:11px;color:var(--faint);margin-bottom:12px;}

/* Payment breakdown */
.pay-row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.04);font-size:12px;}
.pay-row:last-child{border-bottom:none;font-weight:600;color:var(--text);}
.pay-key{color:var(--muted);}
.pay-val{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;}

/* Installment table */
.inst-tbl{width:100%;border-collapse:collapse;margin-top:10px;}
.inst-tbl th{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);padding:6px 8px;text-align:left;border-bottom:1px solid var(--border);}
.inst-tbl td{font-size:11px;color:var(--muted);padding:7px 8px;border-bottom:1px solid rgba(27,79,168,0.04);}
.inst-tbl tr:last-child td{border-bottom:none;}

/* Lead history */
.history-item{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid rgba(27,79,168,0.04);}
.history-item:last-child{border-bottom:none;}
.history-dot{width:8px;height:8px;border-radius:50%;background:var(--blue-l);border:2px solid var(--blue);flex-shrink:0;margin-top:4px;}
.history-text{font-size:12px;color:var(--text);}
.history-meta{font-size:10px;color:var(--faint);margin-top:2px;}

/* Total summary */
.total-banner{background:linear-gradient(135deg,#1A2A4A,var(--blue));border-radius:8px;padding:18px 22px;display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.total-banner-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:4px;}
.total-banner-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:#fff;}
</style>

<div class="sp-page">
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel — Students</div>
            <h1 class="page-title">{{ $student->full_name }}</h1>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Students
        </a>
    </div>
    @php
        $allTx = $student->enrollments->flatMap->financialTransactions;

        $totalFees = $student->enrollments->sum(function($e) {
            return $e->final_price
                + $e->financialTransactions->where('transaction_category','Material')->sum('amount')
                + $e->financialTransactions->where('transaction_category','Test')->sum('amount');
        });

        $totalPaid = $allTx
            ->whereIn('transaction_type', ['Payment','Installment'])
            ->sum('amount');

        $remaining = $student->enrollments->sum(function($e) {
            $depositAmt = $e->paymentPlan
                ? ($e->final_price * $e->paymentPlan->deposit_percentage / 100)
                : $e->final_price;
            $instPaid = $e->financialTransactions
                ->where('transaction_type','Installment')->sum('amount');
            return max(0, $e->final_price - $depositAmt - $instPaid);
        });
    @endphp

    <div class="layout">

        {{-- ── LEFT SIDEBAR ── --}}
        <div>

            {{-- Profile Card --}}
            <div class="card">
                <div class="card-body" style="text-align:center;padding-top:24px;">
                    <div class="profile-avatar">{{ strtoupper(substr($student->full_name ?? 'S', 0, 2)) }}</div>
                    <div class="profile-name">{{ $student->full_name }}</div>
                    <div class="profile-email">{{ $student->email ?? 'No email' }}</div>
                    <span class="badge badge-{{ strtolower($student->status) }}">{{ $student->status }}</span>
                </div>
                <div class="card-body" style="border-top:1px solid var(--border);padding-top:14px;">
                    <div class="meta-row">
                        <span class="meta-key">Phone</span>
                        <span class="meta-val" style="font-family:monospace;font-size:12px;">
                            {{ $student->phones->where('is_primary',true)->first()?->phone_number ?? '—' }}
                        </span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Degree</span>
                        <span class="meta-val">{{ $student->degree ?? '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Location</span>
                        <span class="meta-val">{{ $student->location ?? '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Enrolled</span>
                        <span class="meta-val">{{ $student->created_at?->format('d M Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Enrollments</span>
                        <span class="meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--blue);">{{ $student->enrollments->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="card">
                <div class="card-header"><div class="card-title">Financial Summary</div></div>
                <div class="card-body">
                    <div class="meta-row">
                        <span class="meta-key">Total Fees</span>
                        <span class="meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--blue);">{{ number_format($totalFees, 0) }} LE</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Total Paid</span>
                        <span class="meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--green);">{{ number_format($totalPaid, 0) }} LE</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Outstanding</span>
                        <span class="meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:{{ $remaining > 0 ? 'var(--orange)' : 'var(--green)' }};">
                            {{ $remaining > 0 ? number_format($remaining, 0) . ' LE' : '✓ Paid' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Lead Info --}}
            @if($student->lead)
            <div class="card">
                <div class="card-header"><div class="card-title">Lead Info</div></div>
                <div class="card-body">
                    <div class="meta-row">
                        <span class="meta-key">Source</span>
                        <span class="meta-val">{{ $student->lead->source ?? '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">CS Owner</span>
                        <span class="meta-val">{{ $student->lead->owner?->full_name ?? '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Lead Status</span>
                        <span class="meta-val">{{ $student->lead->status }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Lead Created</span>
                        <span class="meta-val">{{ $student->lead->created_at?->format('d M Y') }}</span>
                    </div>
                    @if($student->lead->notes)
                    <div style="margin-top:10px;padding:10px;background:var(--bg);border-radius:4px;font-size:12px;color:var(--muted);line-height:1.6;">
                        {{ $student->lead->notes }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- ── RIGHT MAIN ── --}}
        <div>

            {{-- Enrollments --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Enrollments ({{ $student->enrollments->count() }})</div>
                </div>
                <div class="card-body">
                    @php
                        $allDepositPayments = \DB::table('deposit_payment')
                            ->whereIn('enrollment_id', $student->enrollments->pluck('enrollment_id'))
                            ->get();
                    @endphp
                    @forelse($student->enrollments as $e)
                    @php
                        $ePaid = $e->financialTransactions
                            ->where('transaction_type', 'Payment')->sum('amount');
                        $eRemaining = max(0, $e->final_price - $ePaid);
                    @endphp
                    <div class="enrollment-card {{ $e->status === 'Active' ? 'active-enroll' : '' }}">
                        <div class="enroll-course">
                            {{ $e->courseTemplate?->name ?? '—' }}
                            @if($e->level) — {{ $e->level->name }} @endif
                            @if($e->sublevel) › {{ $e->sublevel->name }} @endif
                        </div>
                        <div class="enroll-meta">
                            {{ ucfirst($e->enrollment_type) }} · {{ $e->delivery_mood }}
                            @if($e->teacher?->employee) · {{ $e->teacher->full_name }} @endif
                            · Registered by {{ $e->createdByCs?->full_name ?? '—' }}
                        </div>

                        <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                            <span class="badge badge-{{ strtolower(str_replace('_','-',$e->status)) }}">{{ $e->status }}</span>
                            @if($e->paymentPlan)
                            <span style="font-size:10px;color:var(--muted);background:rgba(27,79,168,0.04);border:1px solid var(--border);padding:2px 8px;border-radius:3px;">{{ $e->paymentPlan->name }}</span>
                            @endif
                        </div>

                        {{-- Payment breakdown --}}
                        @php
                            $materialTotal = $e->financialTransactions
                                ->where('transaction_category', 'Material')->sum('amount');
                            $testTotal = $e->financialTransactions
                                ->where('transaction_category', 'Test')->sum('amount');
                            $totalEnrollmentFees = $e->final_price + $materialTotal + $testTotal;
                        @endphp

                        {{-- Final Price --}}
                        <div class="pay-row">
                            <span class="pay-key">Course Price</span>
                            <span class="pay-val" style="color:var(--blue);">{{ number_format($e->final_price, 0) }} LE</span>
                        </div>
                        @if($materialTotal > 0)
                        <div class="pay-row">
                            <span class="pay-key">Material</span>
                            <span class="pay-val" style="color:var(--muted);">{{ number_format($materialTotal, 0) }} LE</span>
                        </div>
                        @endif
                        @if($testTotal > 0)
                        <div class="pay-row">
                            <span class="pay-key">Test Fee</span>
                            <span class="pay-val" style="color:var(--muted);">{{ number_format($testTotal, 0) }} LE</span>
                        </div>
                        @endif
                        <div class="pay-row">
                            <span class="pay-key">Total Fees</span>
                            <span class="pay-val" style="color:var(--blue);">{{ number_format($totalEnrollmentFees, 0) }} LE</span>
                        </div>

                        {{-- Payment Plan Details --}}
                        @if($e->paymentPlan)
                        <div style="background:#fff;border:1px solid var(--border);border-radius:6px;padding:12px 14px;margin-bottom:10px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                                <span style="font-size:12px;font-weight:600;color:var(--text);">{{ $e->paymentPlan->name }}</span>
                                <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;padding:3px 8px;border-radius:10px;background:var(--blue-l);color:var(--blue);border:1px solid rgba(27,79,168,0.15);">
                                    {{ $e->paymentPlan->deposit_percentage }}% Deposit
                                </span>
                            </div>
                            @php
                                $depositAmt = ($e->final_price * $e->paymentPlan->deposit_percentage) / 100;
                                $instCount  = $e->paymentPlan->installment_count ?? 0;
                                $instAmt    = $instCount > 0 ? ($e->final_price - $depositAmt) / $instCount : 0;
                                $courseRemaining = $e->installmentSchedules
                                    ->whereIn('status', ['Pending', 'Overdue'])
                                    ->sum('amount');
                            @endphp
                            <div class="pay-row">
                                <span class="pay-key">Deposit ({{ $e->paymentPlan->deposit_percentage }}%)</span>
                                <span class="pay-val" style="color:var(--orange);">{{ number_format($depositAmt, 0) }} LE</span>
                            </div>
                            <div class="pay-row">
                                <span class="pay-key">Remaining Balance</span>
                                <span class="pay-val" style="color:var(--{{ $courseRemaining > 0 ? 'orange' : 'green' }});">
                                    {{ $courseRemaining > 0 ? number_format($courseRemaining, 0) . ' LE' : '✓ Fully Paid' }}
                                </span>
                            </div>
                            @if($instCount > 0)
                            <div class="pay-row">
                                <span class="pay-key">Installments</span>
                                <span class="pay-val">{{ $instCount }} × {{ number_format($instAmt, 0) }} LE</span>
                            </div>
                            @endif
                            @if($e->paymentPlan->requires_admin_approval)
                            <div style="margin-top:8px;padding:7px 10px;background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);border-left:3px solid var(--orange);border-radius:4px;font-size:11px;color:#92400E;">
                                ⚠ Requires Admin Approval
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Deposit Methods --}}
                        @php
                            $depositPayments = \DB::table('deposit_payment')
                                ->where('enrollment_id', $e->enrollment_id)
                                ->get();
                        @endphp
                        @if($depositPayments->count() > 0)
                        <div style="background:#fff;border:1px solid var(--border);border-radius:6px;padding:12px 14px;margin-bottom:10px;">
                            <div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--orange);margin-bottom:10px;">Deposit Payment Methods</div>
                            @foreach($depositPayments as $dp)
                            <div class="pay-row">
                                <span class="pay-key">{{ str_replace('_',' ',$dp->method) }}</span>
                                <span class="pay-val" style="color:var(--green);">{{ number_format($dp->amount, 0) }} LE</span>
                            </div>
                            @endforeach
                            <div style="height:1px;background:rgba(27,79,168,0.06);margin:6px 0;"></div>
                            <div class="pay-row">
                                <span class="pay-key">Total Paid at Registration</span>
                                <span class="pay-val" style="color:var(--green);">{{ number_format($depositPayments->sum('amount'), 0) }} LE</span>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Installment Schedule --}}
                        @if($e->installmentSchedules->count() > 0)
                        <details style="margin-top:6px;">
                            <summary style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--blue);cursor:pointer;padding:4px 0;">
                                Installment Schedule ({{ $e->installmentSchedules->count() }})
                            </summary>
                            <table class="inst-tbl">
                                <thead>
                                    <tr><th>#</th><th>Amount</th><th>Due Date</th><th>Method</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                @foreach($e->installmentSchedules->sortBy('due_date') as $inst)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td style="font-family:'Bebas Neue',sans-serif;font-size:13px;color:var(--blue);">{{ number_format($inst->amount, 0) }} LE</td>
                                    <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}</td>
                                    <td style="font-size:10px;color:var(--muted);">
                                        @php
                                            $dp = $depositPayments->where('enrollment_id', $e->enrollment_id)->first();
                                        @endphp
                                        {{ $dp ? str_replace('_',' ', $dp->method) : '—' }}
                                    </td>
                                    <td>
                                        <span style="font-size:9px;color:{{ $inst->status === 'Paid' ? 'var(--green)' : ($inst->status === 'Overdue' ? 'var(--red)' : 'var(--muted)') }};">
                                            {{ $inst->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </details>
                        @endif

                        {{-- Placement Test --}}
                        @if($e->placementTest)
                        <div style="margin-top:10px;padding:8px 12px;background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);border-radius:4px;font-size:11px;color:var(--purple);display:flex;gap:16px;">
                            <span>Test Score: <strong>{{ $e->placementTest->score }}</strong></span>
                            <span>Test Fee: <strong>{{ $e->placementTest->test_fee }} LE</strong></span>
                        </div>
                        @endif

                        <div style="font-size:10px;color:var(--faint);margin-top:8px;">
                            Registered {{ $e->created_at?->format('d M Y') }}
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:32px;color:var(--faint);font-size:13px;">No enrollments found.</div>
                    @endforelse
                </div>
            </div>

            {{-- Lead History --}}
            @if($student->lead && $student->lead->leadHistories->count() > 0)
            <div class="card">
                <div class="card-header"><div class="card-title">Lead History</div></div>
                <div class="card-body">
                    @foreach($student->lead->leadHistories->sortByDesc('created_at') as $h)
                    <div class="history-item">
                        <div class="history-dot"></div>
                        <div>
                            <div class="history-text">
                                Status changed:
                                <span style="color:var(--muted);">{{ $h->old_status ?? '—' }}</span>
                                →
                                <span style="color:var(--blue);font-weight:500;">{{ $h->new_status }}</span>
                                @if($h->notes) — {{ $h->notes }} @endif
                            </div>
                            <div class="history-meta">
                                {{ $h->changedBy?->full_name ?? 'System' }} · {{ $h->created_at?->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection