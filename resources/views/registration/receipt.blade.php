<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Receipt — INV-{{ str_pad($enrollment->enrollment_id, 6, '0', STR_PAD_LEFT) }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

@php
    // ── Same data logic as the A4 invoice page ──
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
                $patchName = 'Custom · ' . \Carbon\Carbon::parse($wl->preferred_start_date)->format('d M Y');
            }
        }
    }

    $studentPhone = $enrollment->student?->phones?->first()?->phone_number ?? '—';

    $finalPrice    = (float) $enrollment->final_price;
    $discountValue = (float) ($enrollment->discount_value ?? 0);
    $basePrice     = $finalPrice + $discountValue;
    $isPackage     = !is_null($enrollment->package_id);

    // Package / Bundle names (points 5)
    $packageName = $enrollment->levelPackage?->name ?? null;
    $bundleName  = $enrollment->privateBundle?->name
                 ?? ($enrollment->privateBundle?->hours ? ($enrollment->privateBundle->hours.' hrs bundle') : null);

    $invMaterials = \DB::table('enrollment_material')
        ->join('materials', 'materials.material_id', '=', 'enrollment_material.material_id')
        ->where('enrollment_material.enrollment_id', $enrollment->enrollment_id)
        ->select('materials.name', 'enrollment_material.price')
        ->get();
    $materialTotal = (float) $invMaterials->sum('price');

    $testFee = (float) ($enrollment->financialTransactions
        ->where('transaction_category', 'Test')
        ->whereIn('transaction_type', ['Payment'])
        ->sum('amount'));

    $grandTotal = $finalPrice + $materialTotal + $testFee;

    $depositPayments = \DB::table('deposit_payment')
        ->where('enrollment_id', $enrollment->enrollment_id)
        ->get();
    $paidAmount = (float) $depositPayments->sum('amount');
    $remaining  = max(0, $grandTotal - $paidAmount);

    $plan          = $enrollment->paymentPlan;
    $depositPct    = $plan ? (float) $plan->deposit_percentage : 100.0;
    $depositOnCourse = ($finalPrice * $depositPct) / 100;
    $remainingCourse = max(0, $finalPrice - $depositOnCourse);
    $dueNow          = $depositOnCourse + $materialTotal + $testFee;

    $installments = $enrollment->installmentSchedules ?? collect();

    $csName = $enrollment->createdByCs?->full_name
            ?? \App\Models\HR\Employee::where('employee_id', $enrollment->created_by_cs_id)->value('full_name')
            ?? '—';

    // Money formatter — no "LE" suffix inline to save width; header says currency
    $m = fn($n) => number_format((float)$n, 2);
@endphp

<style>
    /* ── 80mm thermal receipt ── */
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { background:#fff; }
    body {
        width:80mm;
        margin:0 auto;
        padding:3mm 4mm;
        font-family:'Courier New', monospace;   /* mono = crisp on thermal, aligns numbers */
        color:#000;
        font-size:11px;
        line-height:1.45;
        -webkit-font-smoothing:none;
    }

    .center { text-align:center; }
    .right  { text-align:right; }
    .bold   { font-weight:bold; }
    .big    { font-size:15px; font-weight:bold; letter-spacing:1px; }
    .sm     { font-size:10px; }
    .xs     { font-size:9px; }
    .muted  { color:#000; opacity:0.75; }

    .logo { max-width:42mm; max-height:16mm; display:block; margin:0 auto 2mm; }

    .hr    { border:none; border-top:1px dashed #000; margin:2.5mm 0; }
    .hr-solid { border:none; border-top:1px solid #000; margin:2.5mm 0; }

    /* key : value row */
    .row { display:flex; justify-content:space-between; gap:6px; margin:0.6mm 0; }
    .row .k { color:#000; opacity:0.8; white-space:nowrap; }
    .row .v { text-align:right; font-weight:bold; word-break:break-word; }

    .sec-title { font-weight:bold; font-size:10px; letter-spacing:1px; margin:1mm 0 1.2mm; }

    /* line items (name .... amount) */
    .li { display:flex; justify-content:space-between; gap:8px; margin:0.6mm 0; }
    .li .n { flex:1; }
    .li .a { font-weight:bold; white-space:nowrap; }
    .li .sub { font-size:9px; opacity:0.7; }

    .total-row { display:flex; justify-content:space-between; font-weight:bold; font-size:13px; margin:1mm 0; }

    .inst { display:flex; justify-content:space-between; gap:6px; font-size:10px; margin:0.5mm 0; }

    .footer { text-align:center; font-size:10px; margin-top:2mm; }

    /* Terms on the "back" — pushed to a second page so it prints on the reverse
       (or the next slip). Kept small. */
    .terms { page-break-before: always; font-size:9px; line-height:1.5; }
    .terms li { margin:1mm 0 1mm 4mm; }

    .print-toolbar {
        text-align:center; padding:10px; font-family:Arial, sans-serif;
    }
    .print-toolbar button {
        font-size:13px; padding:8px 18px; margin:0 4px; cursor:pointer;
        border:1px solid #333; border-radius:6px; background:#1B4FA8; color:#fff;
    }
    .print-toolbar button.sec { background:#fff; color:#333; }

    @media print {
        .print-toolbar { display:none !important; }
        body { width:80mm; padding:2mm 3mm; }
        @page { size:80mm auto; margin:0; }
    }
</style>
</head>
<body>

{{-- On-screen toolbar (hidden when printing) --}}
<div class="print-toolbar">
    <button onclick="window.print()">🖨 Print Receipt</button>
    <button class="sec" onclick="window.close()">Close</button>
</div>

{{-- ══════════ HEADER ══════════ --}}
<div class="center">
    <img src="{{ asset('images/logo.png') }}" class="logo" alt="Infinity Academy"
         onerror="this.style.display='none'">
    <div class="big">INFINITY ACADEMY</div>
    <div class="xs">Registration Receipt</div>
</div>

<hr class="hr">

{{-- Invoice meta --}}
<div class="row"><span class="k">Invoice</span><span class="v">INV-{{ str_pad($enrollment->enrollment_id, 6, '0', STR_PAD_LEFT) }}</span></div>
<div class="row"><span class="k">Date</span><span class="v">{{ \Carbon\Carbon::parse($enrollment->created_at)->format('d/m/Y') }}</span></div>
<div class="row"><span class="k">Time</span><span class="v">{{ \Carbon\Carbon::parse($enrollment->created_at)->format('h:i A') }}</span></div>
<div class="row"><span class="k">Served by</span><span class="v">{{ $csName }}</span></div>

<hr class="hr">

{{-- ══════════ STUDENT ══════════ --}}
<div class="sec-title">STUDENT</div>
<div class="row"><span class="k">Name</span><span class="v">{{ $enrollment->student?->full_name ?? '—' }}</span></div>
<div class="row"><span class="k">Phone</span><span class="v">{{ $studentPhone }}</span></div>

<hr class="hr">

{{-- ══════════ COURSE ══════════ --}}
<div class="sec-title">COURSE</div>
<div class="row"><span class="k">Course</span><span class="v">{{ $courseName }}</span></div>
@if($levelName !== '—')<div class="row"><span class="k">Level</span><span class="v">{{ $levelName }}</span></div>@endif
@if($sublevelName !== '—')<div class="row"><span class="k">Sublevel</span><span class="v">{{ $sublevelName }}</span></div>@endif
<div class="row"><span class="k">Type</span><span class="v">{{ $enrollment->enrollment_type ?? '—' }} · {{ $enrollment->delivery_mood ?? '—' }}</span></div>
<div class="row"><span class="k">Patch</span><span class="v">{{ $patchName }}</span></div>
@if($packageName)<div class="row"><span class="k">Package</span><span class="v">{{ $packageName }}</span></div>@endif
@if($bundleName)<div class="row"><span class="k">Bundle</span><span class="v">{{ $bundleName }}</span></div>@endif

<hr class="hr">

{{-- ══════════ CHARGES ══════════ --}}
<div class="sec-title">CHARGES (LE)</div>

@if($isPackage)
    <div class="li"><span class="n">Level Package<div class="sub">{{ $courseName }}</div></span><span class="a">{{ $m($finalPrice) }}</span></div>
@else
    <div class="li"><span class="n">Course Fee<div class="sub">{{ $courseName }}{{ $levelName !== '—' ? ' · '.$levelName : '' }}</div></span><span class="a">{{ $m($basePrice) }}</span></div>
    @if($discountValue > 0)
    <div class="li"><span class="n">Discount / Offer</span><span class="a">- {{ $m($discountValue) }}</span></div>
    @endif
@endif

@foreach($invMaterials as $mat)
<div class="li"><span class="n">{{ $mat->name }}<div class="sub">Material</div></span><span class="a">{{ $m($mat->price) }}</span></div>
@endforeach

@if($testFee > 0)
<div class="li"><span class="n">Placement Test</span><span class="a">{{ $m($testFee) }}</span></div>
@endif

<hr class="hr">
<div class="total-row"><span>GRAND TOTAL</span><span>{{ $m($grandTotal) }}</span></div>

<hr class="hr">

{{-- ══════════ PAYMENT ══════════ --}}
<div class="sec-title">PAYMENT</div>
<div class="row"><span class="k">Plan</span><span class="v">{{ $plan?->name ?? 'Full Cash' }}</span></div>
<div class="row"><span class="k">Deposit</span><span class="v">{{ rtrim(rtrim(number_format($depositPct,2),'0'),'.') }}% = {{ $m($depositOnCourse) }}</span></div>
@if($materialTotal > 0)<div class="row"><span class="k">Material (full)</span><span class="v">{{ $m($materialTotal) }}</span></div>@endif
@if($testFee > 0)<div class="row"><span class="k">Test (full)</span><span class="v">{{ $m($testFee) }}</span></div>@endif

<hr class="hr">
<div class="total-row"><span>PAID NOW</span><span>{{ $m($paidAmount) }}</span></div>
<div class="row"><span class="k bold">REMAINING</span><span class="v">{{ $m($remaining) }}</span></div>

{{-- Payment methods --}}
@if($depositPayments->isNotEmpty())
<div class="hr"></div>
<div class="sec-title">PAID BY</div>
@foreach($depositPayments as $dp)
<div class="li">
    <span class="n">{{ str_replace('_',' ',$dp->method) }}@if($dp->reference_number ?? false)<div class="sub">Ref: {{ $dp->reference_number }}</div>@endif</span>
    <span class="a">{{ $m($dp->amount) }}</span>
</div>
@endforeach
@endif

{{-- Installments --}}
@if($installments->isNotEmpty())
<hr class="hr">
<div class="sec-title">INSTALLMENTS</div>
<div class="inst bold"><span>#</span><span style="flex:1">Due date</span><span>Amount</span></div>
@foreach($installments->sortBy('installment_number') as $inst)
<div class="inst">
    <span>{{ $inst->installment_number ?? $loop->iteration }}</span>
    <span style="flex:1">{{ $inst->due_date ? \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') : 'Upon assignment' }}</span>
    <span>{{ $m($inst->amount) }}</span>
</div>
@endforeach
@endif

<hr class="hr-solid">

{{-- ══════════ FOOTER ══════════ --}}
<div class="footer">
    <div class="bold">Thank you for choosing Infinity Academy</div>
    <div class="xs" style="margin-top:1mm;">Please keep this receipt</div>
    <div class="xs" style="margin-top:2mm;">— see terms on back —</div>
</div>

{{-- ══════════ TERMS (BACK / SECOND SLIP) ══════════ --}}
<div class="terms">
    <div class="center bold" style="margin-bottom:2mm;">TERMS & CONDITIONS</div>
    <ul style="list-style:disc;">
        <li>Please keep your payment receipt for future reference.</li>
        <li>Refund requests are accepted only within three (3) days from the booking date.</li>
        <li>In the event of a refund, an administrative fee equal to 10% of the total paid amount will be deducted.</li>
        <li>No refunds will be granted for bookings made under promotional offers or discounted rates.</li>
    </ul>
    <div class="center xs" style="margin-top:3mm;">INV-{{ str_pad($enrollment->enrollment_id, 6, '0', STR_PAD_LEFT) }} · Infinity Academy</div>
</div>

<script>
    // Auto-open the print dialog as soon as the receipt loads.
    window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 350); });
</script>
</body>
</html>
