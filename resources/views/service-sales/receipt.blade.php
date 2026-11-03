<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Receipt — {{ $sale->receipt_number }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

@php
    $isTest     = $sale->isTest();
    $typeLabel  = $isTest ? 'Placement Test' : 'Certificate';
    $csName     = $sale->createdByCs?->full_name ?? '—';
    $m          = fn($n) => number_format((float) $n, 2);
@endphp

<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { background:#fff; }
    body {
        width:80mm;
        margin:0 auto;
        padding:3mm 4mm;
        font-family:'Courier New', monospace;
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

    .row { display:flex; justify-content:space-between; gap:6px; margin:0.6mm 0; }
    .row .k { color:#000; opacity:0.8; white-space:nowrap; }
    .row .v { text-align:right; font-weight:bold; word-break:break-word; }

    .sec-title { font-weight:bold; font-size:10px; letter-spacing:1px; margin:1mm 0 1.2mm; }

    .li { display:flex; justify-content:space-between; gap:8px; margin:0.6mm 0; }
    .li .n { flex:1; }
    .li .a { font-weight:bold; white-space:nowrap; }
    .li .sub { font-size:9px; opacity:0.7; }

    .total-row { display:flex; justify-content:space-between; font-weight:bold; font-size:13px; margin:1mm 0; }

    .note-box { font-size:10px; word-break:break-word; white-space:pre-wrap; }

    .footer { text-align:center; font-size:10px; margin-top:2mm; }

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

<div class="print-toolbar">
    <button onclick="window.print()">🖨 Print Receipt</button>
    <button class="sec" onclick="window.close()">Close</button>
</div>

<div class="center">
    <img src="{{ asset('images/logo.png') }}" class="logo" alt="Infinity Academy"
         onerror="this.style.display='none'">
    <div class="big">INFINITY ACADEMY</div>
    <div class="xs">{{ $typeLabel }} Receipt</div>
    @if($sale->branch?->name)
        <div class="xs">{{ $sale->branch->name }}</div>
    @endif
</div>

<hr class="hr">

<div class="row"><span class="k">Receipt</span><span class="v">{{ $sale->receipt_number }}</span></div>
<div class="row"><span class="k">Date</span><span class="v">{{ $sale->created_at->format('d/m/Y') }}</span></div>
<div class="row"><span class="k">Time</span><span class="v">{{ $sale->created_at->format('h:i A') }}</span></div>
<div class="row"><span class="k">Served by</span><span class="v">{{ $csName }}</span></div>

<hr class="hr">

<div class="sec-title">CLIENT</div>
<div class="row"><span class="k">Name</span><span class="v">{{ $sale->client_name }}</span></div>
<div class="row"><span class="k">Phone</span><span class="v">{{ $sale->client_phone ?: '—' }}</span></div>

<hr class="hr">

<div class="sec-title">CHARGES (LE)</div>
<div class="li">
    <span class="n">{{ $sale->item_name }}<div class="sub">{{ $typeLabel }}</div></span>
    <span class="a">{{ $m($sale->price) }}</span>
</div>

<hr class="hr">
<div class="total-row"><span>GRAND TOTAL</span><span>{{ $m($sale->price) }}</span></div>

<hr class="hr">

<div class="sec-title">PAYMENT</div>
<div class="total-row"><span>PAID NOW</span><span>{{ $m($sale->price) }}</span></div>
<div class="row"><span class="k bold">REMAINING</span><span class="v">{{ $m(0) }}</span></div>

<div class="hr"></div>
<div class="sec-title">PAID BY</div>
<div class="li">
    <span class="n">{{ $sale->payment_method_label }}@if($sale->reference_number)<div class="sub">Ref: {{ $sale->reference_number }}</div>@endif</span>
    <span class="a">{{ $m($sale->price) }}</span>
</div>

@if($sale->notes)
<hr class="hr">
<div class="sec-title">NOTES</div>
<div class="note-box">{{ $sale->notes }}</div>
@endif

<hr class="hr-solid">

<div class="footer">
    <div class="bold">Thank you for choosing Infinity Academy</div>
    <div class="xs" style="margin-top:1mm;">Please keep this receipt</div>
    <div class="xs" style="margin-top:2mm;">— see terms on back —</div>
</div>

<div class="terms">
    <div class="center bold" style="margin-bottom:2mm;">TERMS & CONDITIONS</div>
    <ul style="list-style:disc;">
        <li>Please keep your payment receipt for future reference.</li>
        <li>{{ $isTest ? 'Placement test fees are non-refundable.' : 'Certificate fees are non-refundable.' }}</li>
    </ul>
    <div class="center xs" style="margin-top:3mm;">{{ $sale->receipt_number }} · Infinity Academy</div>
</div>

<script>
    window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 350); });
</script>
</body>
</html>
