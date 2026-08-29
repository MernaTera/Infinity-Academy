<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Invoice — Enrollment #{{ $enrollment->enrollment_id }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

@php
    // ── Resolve display values (course / level / patch / teacher) ──
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
        $wl = $enrollment->waitingLists?->first();
        if ($wl) {
            if ($wl->requested_patch_id) {
                $reqPatch = \App\Models\Academic\Patch::find($wl->requested_patch_id);
                if ($reqPatch) {
                    $patchName = $reqPatch->name;
                    if ($wl->preferred_type === 'Next_Patch') $patchName .= ' · Next Patch';
                }
            } elseif ($wl->preferred_type === 'Next_Patch') {
                $patchName = 'Next Patch (TBD)';
            } elseif ($wl->preferred_type === 'Specific_Date' && $wl->preferred_start_date) {
                $patchName = 'Custom Start · ' . \Carbon\Carbon::parse($wl->preferred_start_date)->format('d M Y');
            }
        }
    }

    $teacherName  = $enrollment->teacher?->name
                  ?? $enrollment->courseInstance?->teacher?->name ?? '—';
    $studentPhone = $enrollment->student?->phones?->first()?->phone_number ?? '—';

    // ── Money ──
    $finalPrice    = (float) $enrollment->final_price;                 // course after discount
    $discountValue = (float) ($enrollment->discount_value ?? 0);
    $basePrice     = $finalPrice + $discountValue;                     // course before discount
    $isPackage     = !is_null($enrollment->package_id);

    // Materials attached to this enrollment (supports multiple).
    $invMaterials = \DB::table('enrollment_material')
        ->join('materials', 'materials.material_id', '=', 'enrollment_material.material_id')
        ->where('enrollment_material.enrollment_id', $enrollment->enrollment_id)
        ->select('materials.name', 'enrollment_material.price')
        ->get();
    $materialTotal = (float) $invMaterials->sum('price');

    // Test fee (Test payment transaction).
    $testFee = (float) ($enrollment->financialTransactions
        ->where('transaction_category', 'Test')
        ->whereIn('transaction_type', ['Payment'])
        ->sum('amount'));

    $grandTotal = $finalPrice + $materialTotal + $testFee;

    // Deposit payments made at registration.
    $depositPayments = \DB::table('deposit_payment')
        ->where('enrollment_id', $enrollment->enrollment_id)
        ->get();
    $paidAmount = (float) $depositPayments->sum('amount');
    $remaining  = max(0, $grandTotal - $paidAmount);

    // Payment plan figures.
    $plan          = $enrollment->paymentPlan;
    $depositPct    = $plan ? (float) $plan->deposit_percentage : 100.0;
    $needsApproval = (bool) ($plan?->requires_admin_approval);
    $depositOnCourse = ($finalPrice * $depositPct) / 100;
    $remainingCourse = max(0, $finalPrice - $depositOnCourse);
    $dueNow          = $depositOnCourse + $materialTotal + $testFee;

    $installments = $enrollment->installmentSchedules ?? collect();

    $csName = $enrollment->createdByCs?->full_name
            ?? \App\Models\HR\Employee::where('employee_id', $enrollment->created_by_cs_id)->value('full_name')
            ?? '—';

    $statusColor = match($enrollment->status){
        'Active'           => '#4ADE80',
        'Waiting'          => '#FFB347',
        'Pending_Approval' => '#FFB347',
        'Cancelled'        => '#F87171',
        'Completed'        => '#60A5FA',
        default            => '#60A5FA',
    };

    $fmt = fn($n) => number_format((float)$n, 2) . ' LE';
@endphp

<style>
*::before,*::after{pointer-events:none;}
*{box-sizing:border-box;}
body{margin:0;padding:20px;background:#F8F6F2;font-family:'DM Sans',sans-serif;min-height:100vh;}

.inf-modal{
    position:relative;width:min(820px,100%);margin:0 auto;
    background:#F8F6F2;display:flex;flex-direction:column;
    border-radius:16px;box-shadow:0 32px 80px rgba(0,0,0,0.18),0 8px 24px rgba(27,79,168,0.12);
    overflow:hidden;
}
.inf-modal::before{
    content:'';position:absolute;top:0;left:0;right:0;height:3px;z-index:10;border-radius:16px 16px 0 0;
    background:linear-gradient(90deg,#F5911E 0%,#1B4FA8 40%,#2D6FDB 70%,#F5911E 100%);
    background-size:200% auto;animation:infGradMove 4s linear infinite;
}
@keyframes infGradMove{to{background-position:200% center}}

.inf-modal-header{
    background:linear-gradient(135deg,#0F1D3A 0%,#1B4FA8 55%,#2D6FDB 100%);
    padding:24px 28px 20px;display:flex;align-items:flex-start;justify-content:space-between;
    flex-shrink:0;position:relative;overflow:hidden;
}
.inf-modal-eyebrow{font-size:9px;letter-spacing:5px;text-transform:uppercase;color:rgba(255,255,255,0.45);margin-bottom:6px;display:flex;align-items:center;gap:8px;}
.inf-modal-eyebrow::before{content:'';width:20px;height:1px;background:rgba(245,145,30,0.6);}
.inf-modal-title{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:6px;color:#fff;line-height:1;margin-bottom:10px;}
.inf-modal-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.inf-modal-id{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.7);background:rgba(255,255,255,0.1);padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,0.15);font-weight:500;}
.inf-modal-date{font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:0.5px;}
.inf-modal-status{font-size:9px;letter-spacing:2px;text-transform:uppercase;padding:4px 10px;border-radius:20px;}
.inf-header-actions{display:flex;align-items:center;gap:8px;position:relative;z-index:1;flex-shrink:0;}
.inf-close-btn{width:34px;height:34px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:8px;cursor:pointer;color:rgba(255,255,255,0.5);transition:all 0.2s;text-decoration:none;}
.inf-close-btn:hover{background:rgba(220,38,38,0.25);border-color:rgba(220,38,38,0.4);color:#fff;}
.inf-print-btn{display:flex;align-items:center;gap:6px;padding:8px 16px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;cursor:pointer;color:rgba(255,255,255,0.7);font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;transition:all 0.2s;}
.inf-print-btn:hover{background:rgba(255,255,255,0.15);color:#fff;}

.inf-modal-body{flex:1;background:#F8F6F2;}

.inf-section{padding:20px 28px;border-bottom:1px solid rgba(27,79,168,0.06);}
.inf-section:last-child{border-bottom:none;}
.inf-sec-label{font-size:8px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.inf-sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

.inf-two-col{display:grid;grid-template-columns:1fr 1fr;gap:0;}
.inf-col-box{padding:20px 28px;border-right:1px solid rgba(27,79,168,0.06);}
.inf-col-box:last-child{border-right:none;}

.inf-row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid rgba(27,79,168,0.04);}
.inf-row:last-child{border-bottom:none;}
.inf-key{font-size:11px;color:#7A8A9A;}
.inf-val{font-size:12px;color:#1A2A4A;font-weight:500;text-align:right;max-width:60%;}
.inf-val.blue{color:#1B4FA8;}.inf-val.orange{color:#F5911E;}.inf-val.green{color:#059669;}.inf-val.red{color:#DC2626;}

.inf-pricing-table{width:100%;border-collapse:collapse;}
.inf-pricing-table th{font-size:8px;letter-spacing:3px;text-transform:uppercase;color:#AAB8C8;padding:9px 12px;text-align:left;border-bottom:2px solid rgba(27,79,168,0.08);font-weight:400;background:rgba(27,79,168,0.02);}
.inf-pricing-table th:last-child{text-align:right;}
.inf-pricing-table td{padding:10px 12px;font-size:12px;color:#1A2A4A;border-bottom:1px solid rgba(27,79,168,0.04);vertical-align:middle;}
.inf-pricing-table td:last-child{text-align:right;font-weight:600;color:#1B4FA8;}
.inf-pricing-table tr:last-child td{border-bottom:none;}
.inf-pricing-table td small{color:#7A8A9A;font-size:11px;}

.inf-price-tag{display:inline-block;font-size:8px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;}
.inf-price-tag.course  {background:rgba(27,79,168,0.08);color:#1B4FA8;}
.inf-price-tag.material{background:rgba(245,145,30,0.08);color:#C47010;}
.inf-price-tag.test    {background:rgba(5,150,105,0.08);color:#059669;}
.inf-price-tag.discount{background:rgba(5,150,105,0.08);color:#059669;}
.inf-price-tag.package {background:rgba(127,119,221,0.1);color:#7F77DD;}

.inf-totals-strip{background:linear-gradient(135deg,#0F1D3A 0%,#1B4FA8 100%);padding:18px 28px;display:grid;grid-template-columns:repeat(3,1fr);}
.inf-total-item{padding:14px 16px;text-align:center;border-right:1px solid rgba(255,255,255,0.06);}
.inf-total-item:last-child{border-right:none;}
.inf-total-label{font-size:8px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:6px;}
.inf-total-val{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:2px;color:#fff;line-height:1;}
.inf-total-val.orange{color:#FFB347;}.inf-total-val.green{color:#4ADE80;}
.inf-total-sub{font-size:9px;color:rgba(255,255,255,0.3);margin-top:4px;}

.inf-plan-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;padding:12px 14px;background:rgba(27,79,168,0.04);border:1px solid rgba(27,79,168,0.1);border-radius:8px;}
.inf-plan-name{font-size:13px;color:#1A2A4A;font-weight:600;}
.inf-plan-badge{font-size:9px;letter-spacing:2px;text-transform:uppercase;padding:4px 10px;border-radius:20px;background:rgba(27,79,168,0.08);color:#1B4FA8;border:1px solid rgba(27,79,168,0.15);}

.inf-inst-table{width:100%;border-collapse:collapse;margin-top:10px;}
.inf-inst-table th{font-size:8px;letter-spacing:3px;text-transform:uppercase;color:#AAB8C8;padding:8px 10px;text-align:left;border-bottom:1px solid rgba(27,79,168,0.08);font-weight:400;background:rgba(27,79,168,0.02);}
.inf-inst-table td{font-size:12px;color:#1A2A4A;font-weight:300;padding:9px 10px;border-bottom:1px solid rgba(27,79,168,0.04);}
.inf-inst-table td:nth-child(2){font-weight:600;color:#1B4FA8;}
.inf-inst-table td:last-child{text-align:right;}
.inf-inst-table tr:last-child td{border-bottom:none;}
.inst-badge{font-size:8px;letter-spacing:1.5px;text-transform:uppercase;padding:3px 8px;border-radius:3px;}
.inst-badge.paid{background:rgba(5,150,105,0.1);color:#059669;}
.inst-badge.overdue{background:rgba(220,38,38,0.08);color:#DC2626;}
.inst-badge.pending{background:rgba(122,138,154,0.1);color:#AAB8C8;}

.inf-approval-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(245,145,30,0.07);border:1px solid rgba(245,145,30,0.2);border-left:3px solid #F5911E;border-radius:6px;padding:10px 14px;margin-top:14px;font-size:11px;color:#92400E;line-height:1.4;}

.inf-terms-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px;}
.inf-terms-list li{font-size:11px;color:#7A8A9A;display:flex;align-items:flex-start;gap:8px;line-height:1.5;}
.inf-terms-dot{width:4px;height:4px;border-radius:50%;background:#F5911E;flex-shrink:0;margin-top:6px;display:block;}

.inf-modal-footer{padding:16px 28px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:#fff;border-top:1px solid rgba(27,79,168,0.08);}
.inf-footer-brand{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;color:#0F1F3D;text-align:center;}
.inf-footer-sub{font-size:10px;color:#AAB8C8;margin-top:3px;text-align:center;letter-spacing:0.5px;}

/* ════════ PRINT — minimal B&W A4 ════════ */
@media print{
    @page{margin:8mm 10mm;size:A4 portrait;}
    body{padding:0;background:#fff;}
    .inf-header-actions{display:none!important;}
    .inf-modal{box-shadow:none!important;border-radius:0!important;width:100%!important;zoom:0.9;}
    .inf-modal::before{display:none!important;}
    .inf-modal-header{background:#1A2A4A!important;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;padding:12px 16px!important;}
    .inf-modal-title{font-size:22px!important;letter-spacing:4px!important;}
    .inf-totals-strip{background:#1A2A4A!important;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;padding:10px 12px!important;}
    .inf-total-val{font-size:16px!important;}
    .inf-total-val.orange,.inf-total-val.green{color:#fff!important;}
    .inf-section{padding:8px 12px!important;}
    .inf-col-box{padding:8px 12px!important;}
}

@media(max-width:640px){
    .inf-modal{width:100%;border-radius:12px;}
    .inf-modal-header,.inf-section,.inf-col-box{padding-left:16px;padding-right:16px;}
    .inf-modal-title{font-size:24px;}
    .inf-two-col{grid-template-columns:1fr;}
    .inf-col-box{border-right:none;border-bottom:1px solid rgba(27,79,168,0.06);}
    .inf-totals-strip{grid-template-columns:1fr;padding:14px 16px;}
    .inf-total-item{border-right:none;border-bottom:1px solid rgba(255,255,255,0.06);padding:10px 0;}
    .inf-print-btn span{display:none;}
}
</style>
</head>
<body>

<div class="inf-modal" id="invoicePanel">

    {{-- Header --}}
    <div class="inf-modal-header">
        <div style="position:relative;z-index:1;">
            <div class="inf-modal-eyebrow">Infinity Academy · Registration</div>
            <div class="inf-modal-title">Invoice</div>
            <div class="inf-modal-meta">
                <div class="inf-modal-id">INV-{{ str_pad($enrollment->enrollment_id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="inf-modal-date">{{ \Carbon\Carbon::parse($enrollment->created_at)->format('d M Y · H:i') }}</div>
                <div class="inf-modal-status" style="background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}33;">{{ str_replace('_',' ',$enrollment->status) }}</div>
            </div>
        </div>
        <div class="inf-header-actions">
            <button type="button" class="inf-print-btn" onclick="window.print()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Print</span>
            </button>
            <a href="javascript:window.close()" class="inf-close-btn" title="Close">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </a>
        </div>
    </div>

    {{-- Body --}}
    <div class="inf-modal-body">

        {{-- Student + Course --}}
        <div class="inf-two-col">
            <div class="inf-col-box">
                <div class="inf-sec-label">Student</div>
                <div class="inf-row"><span class="inf-key">Full Name</span><span class="inf-val">{{ $enrollment->student?->full_name ?? '—' }}</span></div>
                <div class="inf-row"><span class="inf-key">Phone</span><span class="inf-val">{{ $studentPhone }}</span></div>
                @if($enrollment->student?->email)
                <div class="inf-row"><span class="inf-key">Email</span><span class="inf-val">{{ $enrollment->student->email }}</span></div>
                @endif
                <div class="inf-row"><span class="inf-key">Registered by</span><span class="inf-val">{{ $csName }}</span></div>
            </div>
            <div class="inf-col-box">
                <div class="inf-sec-label">Course Details</div>
                <div class="inf-row"><span class="inf-key">Course</span><span class="inf-val blue">{{ $courseName }}</span></div>
                <div class="inf-row"><span class="inf-key">Level</span><span class="inf-val">{{ $levelName }}</span></div>
                @if($sublevelName !== '—')
                <div class="inf-row"><span class="inf-key">Sublevel</span><span class="inf-val">{{ $sublevelName }}</span></div>
                @endif
                <div class="inf-row"><span class="inf-key">Type</span><span class="inf-val">{{ $enrollment->enrollment_type ?? '—' }}</span></div>
                <div class="inf-row"><span class="inf-key">Mode</span><span class="inf-val">{{ $enrollment->delivery_mood ?? '—' }}</span></div>
                <div class="inf-row"><span class="inf-key">Start</span><span class="inf-val">{{ $patchName }}</span></div>
                @if($teacherName !== '—')
                <div class="inf-row"><span class="inf-key">Teacher</span><span class="inf-val blue">{{ $teacherName }}</span></div>
                @endif
            </div>
        </div>

        {{-- Pricing Breakdown --}}
        <div class="inf-section">
            <div class="inf-sec-label">Pricing Breakdown</div>
            <table class="inf-pricing-table">
                <thead><tr><th style="width:45%;">Item</th><th>Type</th><th style="text-align:right;">Amount</th></tr></thead>
                <tbody>
                    @if($isPackage)
                        <tr>
                            <td><strong>Level Package</strong><br><small>{{ $courseName }}</small></td>
                            <td><span class="inf-price-tag package">Package</span></td>
                            <td>{{ $fmt($finalPrice) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td><strong>Course Fee</strong><br><small>{{ $courseName }}{{ $levelName !== '—' ? ' · '.$levelName : '' }}</small></td>
                            <td><span class="inf-price-tag course">Course</span></td>
                            <td>{{ $fmt($basePrice) }}</td>
                        </tr>
                        @if($discountValue > 0)
                        <tr>
                            <td><strong>Discount Applied</strong></td>
                            <td><span class="inf-price-tag discount">Offer</span></td>
                            <td style="color:#059669;">− {{ $fmt($discountValue) }}</td>
                        </tr>
                        @endif
                    @endif

                    @foreach($invMaterials as $mat)
                    <tr>
                        <td><strong>{{ $mat->name }}</strong><br><small>Full payment required</small></td>
                        <td><span class="inf-price-tag material">Material</span></td>
                        <td>{{ $fmt($mat->price) }}</td>
                    </tr>
                    @endforeach

                    @if($testFee > 0)
                    <tr>
                        <td><strong>Placement Test</strong><br><small>Full payment required</small></td>
                        <td><span class="inf-price-tag test">Test</span></td>
                        <td>{{ $fmt($testFee) }}</td>
                    </tr>
                    @endif

                    <tr style="background:rgba(27,79,168,0.03);">
                        <td colspan="2" style="font-weight:600;font-size:12px;color:#1A2A4A;letter-spacing:1px;text-transform:uppercase;">Grand Total</td>
                        <td style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;">{{ $fmt($grandTotal) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Totals strip --}}
        <div class="inf-totals-strip">
            <div class="inf-total-item">
                <div class="inf-total-label">Course Price</div>
                <div class="inf-total-val">{{ $fmt($finalPrice) }}</div>
                <div class="inf-total-sub">{{ $isPackage ? 'Package price' : ($discountValue > 0 ? 'After '.$fmt($discountValue).' discount' : 'Regular price') }}</div>
            </div>
            <div class="inf-total-item">
                <div class="inf-total-label">Total Amount</div>
                <div class="inf-total-val orange">{{ $fmt($grandTotal) }}</div>
                <div class="inf-total-sub">Course + extras</div>
            </div>
            <div class="inf-total-item">
                <div class="inf-total-label">Remaining</div>
                <div class="inf-total-val green">{{ $fmt($remaining) }}</div>
                <div class="inf-total-sub">Paid {{ $fmt($paidAmount) }}</div>
            </div>
        </div>

        {{-- Payment Plan --}}
        <div class="inf-section">
            <div class="inf-sec-label">Payment Plan</div>

            <div class="inf-plan-header">
                <div class="inf-plan-name">{{ $plan?->name ?? 'Full Cash' }}</div>
                <div class="inf-plan-badge">{{ rtrim(rtrim(number_format($depositPct,2),'0'),'.') }}% Deposit</div>
            </div>

            <div class="inf-row"><span class="inf-key">Deposit on Course</span><span class="inf-val orange">{{ rtrim(rtrim(number_format($depositPct,2),'0'),'.') }}% × {{ $fmt($finalPrice) }} = {{ $fmt($depositOnCourse) }}</span></div>
            @if($materialTotal > 0)<div class="inf-row"><span class="inf-key">Material (full payment)</span><span class="inf-val">{{ $fmt($materialTotal) }}</span></div>@endif
            @if($testFee > 0)<div class="inf-row"><span class="inf-key">Test Fee (full payment)</span><span class="inf-val">{{ $fmt($testFee) }}</span></div>@endif
            <div style="height:1px;background:rgba(27,79,168,0.07);margin:8px 0;"></div>
            <div class="inf-row"><span class="inf-key">Total Due Now</span><span class="inf-val green">{{ $fmt($dueNow) }}</span></div>
            <div class="inf-row"><span class="inf-key">Remaining (installments)</span><span class="inf-val blue">{{ $fmt($remainingCourse) }}</span></div>

            @if($needsApproval)
            <div class="inf-approval-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#C47010" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                This plan requires <strong>Admin Approval</strong> before activation
            </div>
            @endif

            {{-- Installment schedule --}}
            @if($installments->isNotEmpty())
            <div style="margin-top:16px;">
                <div class="inf-sec-label" style="margin-bottom:10px;">Installment Schedule</div>
                <table class="inf-inst-table">
                    <thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th style="text-align:right;">Status</th></tr></thead>
                    <tbody>
                        @foreach($installments as $inst)
                        @php
                            $instStatus = $inst->status ?? 'Pending';
                            $instClass  = match($instStatus){ 'Paid'=>'paid','Overdue'=>'overdue', default=>'pending' };
                        @endphp
                        <tr>
                            <td style="color:#AAB8C8;">{{ $inst->installment_number ?? $loop->iteration }}</td>
                            <td>{{ $fmt($inst->amount) }}</td>
                            <td style="color:#AAB8C8;font-size:11px;">{{ $inst->due_date ? \Carbon\Carbon::parse($inst->due_date)->format('d M Y') : 'Upon course assignment' }}</td>
                            <td style="text-align:right;"><span class="inst-badge {{ $instClass }}">{{ $instStatus }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Deposit payment methods --}}
            @if($depositPayments->isNotEmpty())
            <div style="margin-top:16px;">
                <div class="inf-sec-label" style="margin-bottom:10px;">Deposit Payment Methods</div>
                @foreach($depositPayments as $dp)
                <div class="inf-row">
                    <span class="inf-key">
                        {{ str_replace('_',' ',$dp->method) }}
                        @if($dp->reference_number ?? false)<span style="color:#AAB8C8;font-size:10px;margin-left:6px;">Ref: {{ $dp->reference_number }}</span>@endif
                    </span>
                    <span class="inf-val green">{{ $fmt($dp->amount) }}</span>
                </div>
                @endforeach
                <div style="height:1px;background:rgba(27,79,168,0.07);margin:6px 0;"></div>
                <div class="inf-row"><span class="inf-key">Total Paid at Registration</span><span class="inf-val green">{{ $fmt($paidAmount) }}</span></div>
            </div>
            @endif
        </div>

        {{-- Terms & Conditions --}}
        <div class="inf-section" style="background:rgba(27,79,168,0.01);">
            <div class="inf-sec-label">Terms &amp; Conditions</div>
            <ul class="inf-terms-list">
                <li><span class="inf-terms-dot"></span>Please keep your payment receipt for future reference.</li>
                <li><span class="inf-terms-dot"></span>Refund requests are accepted only within three (3) days from the booking date.</li>
                <li><span class="inf-terms-dot"></span>In the event of a refund, an administrative fee equal to 10% of the total paid amount will be deducted.</li>
                <li><span class="inf-terms-dot"></span>No refunds will be granted for bookings made under promotional offers or discounted rates.</li>
            </ul>
        </div>

    </div>{{-- end body --}}

    {{-- Footer --}}
    <div class="inf-modal-footer">
        <div>
            <div class="inf-footer-brand">Infinity Academy</div>
            <div class="inf-footer-sub">Generated on {{ now()->format('d M Y · H:i') }} · This is a system-generated document</div>
        </div>
    </div>

</div>

</body>
</html>
