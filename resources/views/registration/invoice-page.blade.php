
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Invoice — Enrollment #{{ $enrollment->enrollment_id }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    *,*::before,*::after{box-sizing:border-box;}
    body{
        margin:0;padding:24px;background:#F8F6F2;
        font-family:'DM Sans',sans-serif;color:#1A2A4A;
        min-height:100vh;
    }

    .inv-wrap{max-width:820px;margin:0 auto;background:#fff;
        border-radius:16px;box-shadow:0 12px 40px rgba(15,31,61,0.12);overflow:hidden;
        position:relative;}
    .inv-wrap::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;
        background:linear-gradient(90deg,#F5911E,#1B4FA8);}

    .inv-header{padding:26px 32px;background:linear-gradient(135deg,#0F1F3D,#1A2A4A);
        color:#fff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
    .inv-brand{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:4px;}
    .inv-brand-sub{font-size:9px;letter-spacing:3px;text-transform:uppercase;
        color:#F5911E;margin-top:2px;font-weight:600;}
    .inv-ref{text-align:right;}
    .inv-ref-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;
        color:rgba(255,255,255,0.5);}
    .inv-ref-val{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:#fff;margin-top:2px;}

    .inv-actions{padding:14px 32px;background:#FAFAF7;border-bottom:1px solid rgba(27,79,168,0.08);
        display:flex;justify-content:flex-end;gap:8px;}
    .btn{padding:9px 20px;border:none;border-radius:6px;
        font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2.5px;cursor:pointer;
        display:inline-flex;align-items:center;gap:6px;text-decoration:none;transition:all 0.2s;}
    .btn-print{background:#0F1F3D;color:#fff;}
    .btn-print:hover{background:#1A2A4A;transform:translateY(-1px);}
    .btn-close{background:transparent;color:#7A8A9A;border:1px solid rgba(122,138,154,0.3);}
    .btn-close:hover{background:rgba(122,138,154,0.06);color:#1A2A4A;}

    .inv-body{padding:28px 32px;}
    .inv-section{margin-bottom:22px;}
    .inv-section-title{font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:3px;
        text-transform:uppercase;color:#F5911E;margin-bottom:10px;padding-bottom:6px;
        border-bottom:2px solid rgba(245,145,30,0.15);display:flex;align-items:center;gap:8px;}
    .inv-num{width:20px;height:20px;background:#F5911E;color:#fff;border-radius:4px;
        display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0;}

    .inv-row{display:flex;justify-content:space-between;align-items:center;
        padding:8px 0;border-bottom:1px solid rgba(27,79,168,0.04);font-size:12px;}
    .inv-row:last-child{border-bottom:none;}
    .inv-row-label{color:#7A8A9A;font-weight:500;letter-spacing:0.3px;}
    .inv-row-value{color:#1A2A4A;font-weight:600;text-align:right;}
    .inv-row-value.big{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;color:#1B4FA8;}
    .inv-row-value.money{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px;}
    .inv-row-value.green{color:#059669;}
    .inv-row-value.red{color:#DC2626;}
    .inv-row-value.orange{color:#F5911E;}
    .inv-row-value.blue{color:#1B4FA8;}

    .inv-total-box{background:linear-gradient(135deg,#F8F6F2,#fff);border:1px solid rgba(27,79,168,0.1);
        border-radius:10px;padding:16px 20px;margin-top:14px;}
    .inv-total-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:12px;}
    .inv-total-row.big{border-top:2px solid #F5911E;padding-top:12px;margin-top:8px;}
    .inv-total-row.big .inv-row-label{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;
        text-transform:uppercase;color:#0F1F3D;}
    .inv-total-row.big .inv-row-value{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:2px;color:#F5911E;}

    .badge{display:inline-flex;align-items:center;font-size:9px;font-weight:700;
        padding:3px 8px;border-radius:3px;letter-spacing:0.5px;text-transform:uppercase;}
    .badge-green{background:rgba(5,150,105,0.1);color:#059669;}
    .badge-orange{background:rgba(245,145,30,0.1);color:#C47010;}
    .badge-red{background:rgba(220,38,38,0.08);color:#DC2626;}
    .badge-blue{background:rgba(27,79,168,0.08);color:#1B4FA8;}

    .inf-section{padding:20px 28px;border-bottom:1px solid rgba(27,79,168,0.06);}
    .inf-section:last-child{border-bottom:none;}
    .inf-sec-label{font-size:8px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
    .inf-sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

    .inf-terms-list{list-style:none;padding:0;margin:0;}
    .inf-terms-list li{position:relative;padding-left:16px;margin-bottom:8px;font-size:11px;color:#1A2A4A;line-height:1.4;}
    .inf-terms-dot{position:absolute;top:6px;left:0;width:6px;height:6px;background:#F5911E;border-radius:50%;}
    .inv-footer{padding:20px 32px;background:#F8F6F2;text-align:center;
        border-top:1px solid rgba(27,79,168,0.08);}
    .inv-footer-brand{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;color:#0F1F3D;}
    .inv-footer-sub{font-size:10px;color:#7A8A9A;margin-top:4px;letter-spacing:0.5px;}

    @media print{
        body{padding:0;background:#fff;}
        .inv-wrap{box-shadow:none;border-radius:0;}
        .inv-actions{display:none !important;}
        .inv-header{background:#0F1F3D !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
        .inv-wrap::before{-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    }
</style>
</head>
<body>

@php
    $courseTemplate = $enrollment->courseTemplate ?? $enrollment->courseInstance?->courseTemplate;
    $courseName     = $courseTemplate?->name ?? '—';
    $levelName      = $enrollment->level?->name ?? $enrollment->courseInstance?->level?->name ?? '—';
    $sublevelName   = $enrollment->sublevel?->name ?? '—';
    $patchName = '—';
    if ($enrollment->patch?->name) {
        $patchName = $enrollment->patch->name;
    } elseif ($enrollment->courseInstance?->patch?->name) {
        $patchName = $enrollment->courseInstance->patch->name;
    } else {
        // Case: Next Patch / Specific Date → info lives on waiting_list
        $wl = $enrollment->waitingLists?->first();
        if ($wl) {
            if ($wl->requested_patch_id) {
                $reqPatch = \App\Models\Academic\Patch::find($wl->requested_patch_id);
                if ($reqPatch) {
                    $patchName = $reqPatch->name;
                    if ($wl->preferred_type === 'Next_Patch') {
                        $patchName .= ' · Next Patch';
                    }
                }
            } elseif ($wl->preferred_type === 'Next_Patch') {
                $patchName = 'Next Patch (TBD)';
            } elseif ($wl->preferred_type === 'Specific_Date' && $wl->preferred_start_date) {
                $patchName = 'Custom Start · ' . \Carbon\Carbon::parse($wl->preferred_start_date)->format('d M Y');
            }
        }
    }    
    $teacherName    = $enrollment->teacher?->employee?->full_name 
                    ?? $enrollment->courseInstance?->teacher?->employee?->full_name ?? '—';
    $studentPhone   = $enrollment->student?->phones?->first()?->phone_number ?? '—';

    $finalPrice     = (float) $enrollment->final_price;
    $discountValue  = (float) ($enrollment->discount_value ?? 0);
    $basePrice      = $finalPrice + $discountValue;

    $paidAmount = \DB::table('deposit_payment')
        ->where('enrollment_id', $enrollment->enrollment_id)
        ->sum('amount');
    $remaining  = max(0, $finalPrice - $paidAmount);

    $depositPayments = \DB::table('deposit_payment')
        ->where('enrollment_id', $enrollment->enrollment_id)
        ->get();

    $installments = $enrollment->installmentSchedules ?? collect();

    $statusColor = match($enrollment->status){
        'Active'           => 'green',
        'Waiting'          => 'orange',
        'Pending_Approval' => 'orange',
        'Cancelled'        => 'red',
        'Completed'        => 'blue',
        default            => 'blue',
    };
@endphp

<div class="inv-wrap">

    {{-- Header --}}
    <div class="inv-header">
        <div>
            <div class="inv-brand">Infinity Academy</div>
            <div class="inv-brand-sub">Enrollment Invoice</div>
        </div>
        <div class="inv-ref">
            <div class="inv-ref-label">Reference</div>
            <div class="inv-ref-val">INV-{{ str_pad($enrollment->enrollment_id, 6, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size:10px;color:rgba(255,255,255,0.5);margin-top:4px;letter-spacing:0.5px;">
                {{ \Carbon\Carbon::parse($enrollment->created_at)->format('d M Y · H:i') }}
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="inv-actions">
        <button onclick="window.close()" class="btn btn-close">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Close
        </button>
        <button onclick="window.print()" class="btn btn-print">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
        </button>
    </div>

    <div class="inv-body">

        {{-- Section 1: Student --}}
        <div class="inv-section">
            <div class="inv-section-title"><span class="inv-num">1</span> Student Information</div>
            <div class="inv-row">
                <span class="inv-row-label">Full Name</span>
                <span class="inv-row-value">{{ $enrollment->student?->full_name ?? '—' }}</span>
            </div>
            <div class="inv-row">
                <span class="inv-row-label">Phone</span>
                <span class="inv-row-value">{{ $studentPhone }}</span>
            </div>
            @if($enrollment->student?->email)
            <div class="inv-row">
                <span class="inv-row-label">Email</span>
                <span class="inv-row-value">{{ $enrollment->student->email }}</span>
            </div>
            @endif
            <div class="inv-row">
                <span class="inv-row-label">Status</span>
                <span class="inv-row-value">
                    <span class="badge badge-{{ $statusColor }}">{{ str_replace('_', ' ', $enrollment->status) }}</span>
                </span>
            </div>
        </div>

        {{-- Section 2: Course --}}
        <div class="inv-section">
            <div class="inv-section-title"><span class="inv-num">2</span> Course Details</div>
            <div class="inv-row">
                <span class="inv-row-label">Course</span>
                <span class="inv-row-value big">{{ $courseName }}</span>
            </div>
            <div class="inv-row">
                <span class="inv-row-label">Level</span>
                <span class="inv-row-value">{{ $levelName }}</span>
            </div>
            @if($sublevelName !== '—')
            <div class="inv-row">
                <span class="inv-row-label">Sublevel</span>
                <span class="inv-row-value">{{ $sublevelName }}</span>
            </div>
            @endif
            <div class="inv-row">
                <span class="inv-row-label">Type</span>
                <span class="inv-row-value">{{ $enrollment->enrollment_type ?? '—' }}</span>
            </div>
            <div class="inv-row">
                <span class="inv-row-label">Mode</span>
                <span class="inv-row-value">{{ $enrollment->delivery_mood ?? '—' }}</span>
            </div>
            <div class="inv-row">
                <span class="inv-row-label">Patch</span>
                <span class="inv-row-value">{{ $patchName }}</span>
            </div>
            @if($teacherName !== '—')
            <div class="inv-row">
                <span class="inv-row-label">Teacher</span>
                <span class="inv-row-value">{{ $teacherName }}</span>
            </div>
            @endif
        </div>

        {{-- Section 3: Financials --}}
        <div class="inv-section">
            <div class="inv-section-title"><span class="inv-num">3</span> Financial Summary</div>
            <div class="inv-row">
                <span class="inv-row-label">Base Price</span>
                <span class="inv-row-value money">{{ number_format($basePrice) }} LE</span>
            </div>
            @if($discountValue > 0)
            <div class="inv-row">
                <span class="inv-row-label">Discount</span>
                <span class="inv-row-value money orange">− {{ number_format($discountValue) }} LE</span>
            </div>
            @endif
            <div class="inv-row">
                <span class="inv-row-label">Payment Plan</span>
                <span class="inv-row-value">
                    {{ $enrollment->paymentPlan?->name ?? 'Full Cash' }}
                    @if($enrollment->paymentPlan?->requires_admin_approval)
                        <span class="badge badge-orange" style="margin-left:6px;">Approval Required</span>
                    @endif
                </span>
            </div>

            <div class="inv-total-box">
                <div class="inv-total-row">
                    <span class="inv-row-label">Total Price</span>
                    <span class="inv-row-value money blue">{{ number_format($finalPrice) }} LE</span>
                </div>
                <div class="inv-total-row">
                    <span class="inv-row-label">Amount Paid</span>
                    <span class="inv-row-value money green">{{ number_format($paidAmount) }} LE</span>
                </div>
                <div class="inv-total-row big">
                    <span class="inv-row-label">Remaining</span>
                    <span class="inv-row-value">{{ number_format($remaining) }} LE</span>
                </div>
            </div>
        </div>

        {{-- Section 4: Deposit Payments --}}
        @if($depositPayments->isNotEmpty())
        <div class="inv-section">
            <div class="inv-section-title"><span class="inv-num">4</span> Deposit Payments</div>
            @foreach($depositPayments as $dp)
            <div class="inv-row">
                <span class="inv-row-label">
                    {{ str_replace('_', ' ', $dp->method) }}
                    @if($dp->reference_number)
                        <span style="color:#AAB8C8;font-size:10px;margin-left:6px;">Ref: {{ $dp->reference_number }}</span>
                    @endif
                </span>
                <span class="inv-row-value money green">{{ number_format($dp->amount) }} LE</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Section 5: Installments Schedule --}}
        @if($installments->isNotEmpty())
        <div class="inv-section">
            <div class="inv-section-title"><span class="inv-num">5</span> Installment Schedule</div>
            @foreach($installments as $inst)
            @php
                $instStatus = $inst->status ?? 'Pending';
                $instColor = match($instStatus){
                    'Paid'    => 'green',
                    'Overdue' => 'red',
                    default   => 'orange',
                };
            @endphp
            <div class="inv-row">
                <span class="inv-row-label">
                    Installment #{{ $inst->installment_number ?? $loop->iteration }}
                    @if($inst->due_date)
                        <span style="color:#AAB8C8;font-size:10px;margin-left:6px;">
                            Due: {{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}
                        </span>
                    @endif
                </span>
                <span class="inv-row-value" style="display:flex;align-items:center;gap:8px;">
                    <span class="badge badge-{{ $instColor }}">{{ $instStatus }}</span>
                    <span class="money">{{ number_format($inst->amount) }} LE</span>
                </span>
            </div>
            @endforeach
        </div>
        @endif

    </div>
            {{-- Terms & Conditions (inside body so it prints) --}}
            <div class="inf-section" style="background:rgba(27,79,168,0.01);">
                <div class="inf-sec-label">Terms &amp; Conditions</div>
                <ul class="inf-terms-list">
                    <li><span class="inf-terms-dot"></span>Please keep your payment receipt for future reference.</li>
                    <li><span class="inf-terms-dot"></span>Refund requests are accepted only within three (3) days from the booking date.</li>
                    <li><span class="inf-terms-dot"></span>In the event of a refund, an administrative fee equal to 10% of the total paid amount will be deducted.</li>
                    <li><span class="inf-terms-dot"></span>No refunds will be granted for bookings made under promotional offers or discounted rates.</li>
                </ul>
            </div>
    {{-- Footer --}}
    <div class="inv-footer">
        <div class="inv-footer-brand">Infinity Academy</div>
        <div class="inv-footer-sub">Generated on {{ now()->format('d M Y · H:i') }} · This is a system-generated document</div>
    </div>

</div>

</body>
</html>