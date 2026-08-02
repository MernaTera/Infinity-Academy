@extends('layouts.leads')

@section('title', 'Register Student')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-l:rgba(5,150,105,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }

    .create-page {
        background: var(--bg);
        min-height: 100vh;
        padding: 32px;
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
    }

    /* ═══════════════ COMMAND HEADER ═══════════════ */
    .reg-header {
        max-width: 1080px; margin: 0 auto 24px;
        background: linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius: 14px; padding: 24px 30px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 16px; position: relative; overflow: hidden;
        box-shadow: 0 8px 32px rgba(15,31,61,0.15);
    }
    .reg-header::before {
        content:''; position:absolute; top:-60px; right:-40px; width:200px; height:200px;
        border-radius:50%; background:rgba(245,145,30,0.06);
    }
    .reg-header::after {
        content:''; position:absolute; bottom:-50px; left:20%; width:140px; height:140px;
        border-radius:50%; background:rgba(27,79,168,0.15);
    }
    .reg-header-left { position:relative; z-index:1; }
    .reg-eyebrow {
        font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange);
        margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px;
    }
    .reg-eyebrow::before {
        content:''; width:6px; height:6px; border-radius:50%; background:var(--orange);
        box-shadow:0 0 8px var(--orange);
    }
    .reg-title {
        font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:4px;
        color:#fff; line-height:1; margin:0;
    }
    .reg-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }
    .btn-back {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:rgba(255,255,255,0.08);
        border:1px solid rgba(255,255,255,0.15); border-radius:6px;
        color:rgba(255,255,255,0.8); font-size:10px; letter-spacing:2.5px;
        text-transform:uppercase; text-decoration:none; transition:all 0.25s;
        position:relative; z-index:1; font-weight:600;
    }
    .btn-back:hover { background:rgba(255,255,255,0.14); color:#fff; text-decoration:none; }

    /* ═══════════════ LEAD IDENTITY BAR ═══════════════ */
    .lead-bar {
        max-width:1080px; margin:0 auto 20px;
        background:var(--card); border:1px solid var(--border); border-radius:12px;
        padding:18px 26px; display:flex; align-items:center; gap:28px; flex-wrap:wrap;
        box-shadow:0 2px 12px rgba(27,79,168,0.04); position:relative; overflow:hidden;
    }
    .lead-bar::before {
        content:''; position:absolute; left:0; top:0; bottom:0; width:4px;
        background:linear-gradient(180deg, var(--orange), var(--blue));
    }
    .lead-avatar {
        width:52px; height:52px; border-radius:50%;
        background:linear-gradient(135deg, var(--blue-l), var(--orange-l));
        display:flex; align-items:center; justify-content:center;
        font-family:'Bebas Neue',sans-serif; font-size:22px; color:var(--blue);
        border:2px solid #fff; box-shadow:0 4px 12px rgba(27,79,168,0.12); flex-shrink:0;
    }
    .lead-badge { display:flex; flex-direction:column; gap:3px; }
    .lead-badge-label { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); font-weight:600; }
    .lead-badge-value { font-size:13px; color:var(--text); font-weight:600; }

    /* ═══════════════ MAIN GRID (form + rail) ═══════════════ */
    .reg-layout {
        max-width:1080px; margin:0 auto;
        display:grid; grid-template-columns:1fr; gap:20px;
    }

    /* ═══════════════ SECTION CARD ═══════════════ */
    .sec-card {
        background:var(--card); border:1px solid var(--border); border-radius:12px;
        overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.04);
        animation:secIn 0.4s ease both;
    }
    @keyframes secIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
    .sec-head {
        padding:15px 22px; background:linear-gradient(135deg, rgba(27,79,168,0.02), transparent);
        border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px;
    }
    .sec-num {
        width:26px; height:26px; background:var(--dark); color:var(--orange);
        border-radius:7px; display:flex; align-items:center; justify-content:center;
        font-family:'Bebas Neue',sans-serif; font-size:13px; flex-shrink:0;
    }
    .sec-title {
        font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:3px;
        color:var(--text); line-height:1;
    }
    .sec-hint { font-size:10px; color:var(--muted); letter-spacing:0.5px; margin-left:auto; }
    .sec-body { padding:22px; }

    /* ═══════════════ GRIDS ═══════════════ */
    .form-grid        { display:grid; grid-template-columns:1fr 1fr; gap:16px 20px; }
    .form-grid.cols-1 { grid-template-columns:1fr; }
    .form-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
    .form-grid.cols-4 { grid-template-columns:repeat(4,1fr); }

    @media (max-width:820px) {
        .form-grid, .form-grid.cols-3, .form-grid.cols-4 { grid-template-columns:1fr; }
        .create-page { padding:16px; }
    }

    .form-field { display:flex; flex-direction:column; gap:7px; }
    .form-label {
        font-size:9px; letter-spacing:2.5px; text-transform:uppercase;
        color:var(--muted); font-weight:600;
    }
    .form-label .required { color:var(--orange); margin-left:2px; }

    .form-control-inf {
        width:100%; padding:11px 13px; background:#fff;
        border:1px solid var(--border); border-radius:7px;
        color:var(--text); font-family:'DM Sans',sans-serif;
        font-size:13px; font-weight:400; outline:none;
        transition:border-color 0.25s, box-shadow 0.25s;
        appearance:none; -webkit-appearance:none;
    }
    .form-control-inf::placeholder { color:#B0BCCC; }
    .form-control-inf:focus {
        border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.08);
    }
    .form-control-inf[readonly] { background:var(--bg); color:var(--muted); cursor:default; }
    textarea.form-control-inf { resize:vertical; min-height:80px; }

    select.form-control-inf {
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 12px center;
        padding-right:34px; cursor:pointer; background-color:#fff;
    }
    select.form-control-inf option { background:#fff; color:var(--text); }

    /* ═══════════════ SEGMENTED CONTROL (Type / Mode) ═══════════════ */
    .segmented {
        display:inline-flex; background:var(--bg); border:1px solid var(--border);
        border-radius:9px; padding:4px; gap:3px; width:100%;
    }
    .segmented label {
        flex:1; display:flex; align-items:center; justify-content:center; gap:8px;
        padding:11px 16px; border-radius:6px; cursor:pointer;
        font-size:12px; font-weight:600; color:var(--muted);
        letter-spacing:1px; transition:all 0.2s; position:relative;
        text-transform:uppercase;
    }
    .segmented label:has(input:checked) {
        background:#fff; color:var(--blue);
        box-shadow:0 2px 8px rgba(27,79,168,0.12);
    }
    .segmented input { position:absolute; opacity:0; pointer-events:none; }
    .segmented label svg { opacity:0.6; }
    .segmented label:has(input:checked) svg { opacity:1; }

    /* Mode select styled as segmented (native select fallback) */
    .mode-select-wrap { position:relative; }

    /* ═══════════════ MATERIAL TOGGLE ═══════════════ */
    /* Multiple materials list */
    .mat-item {
        display:flex; align-items:center; gap:12px; padding:12px 14px; margin-bottom:8px;
        border:1px solid var(--border); border-radius:8px; background:var(--card); transition:all 0.2s; cursor:pointer;
    }
    .mat-item:has(input:checked) { border-color:var(--blue); background:var(--blue-l); }
    .mat-item.mandatory { border-color:rgba(245,145,30,0.3); background:rgba(245,145,30,0.04); cursor:default; }
    .mat-item input[type="checkbox"] { accent-color:var(--blue); width:16px; height:16px; flex-shrink:0; }
    .mat-item input:disabled { accent-color:#C47010; }
    .mat-item-body { flex:1; min-width:0; }
    .mat-item-name { font-size:13px; font-weight:600; color:var(--text); }
    .mat-item-meta { font-size:10px; color:var(--muted); margin-top:2px; display:flex; gap:8px; align-items:center; }
    .mat-item-price { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:0.5px; color:var(--blue); white-space:nowrap; }
    .mat-tag { font-size:8px; letter-spacing:1px; text-transform:uppercase; padding:2px 7px; border-radius:3px; font-weight:600; }
    .mat-tag-mand { color:#C47010; background:rgba(245,145,30,0.12); }
    .mat-tag-shared { color:#7C3AED; background:rgba(124,58,237,0.1); }
    .mat-tag-indiv { color:#1B4FA8; background:rgba(27,79,168,0.08); }
    .mat-total-row {
        display:flex; align-items:center; justify-content:space-between;
        padding:12px 14px; margin-top:6px; border-top:2px solid var(--border);
        font-size:12px;
    }
    .mat-total-label { color:var(--muted); letter-spacing:0.5px; font-weight:600; }
    .mat-total-val { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px; color:var(--green-dk); }

    .material-toggle {
        display:flex; align-items:center; gap:11px;
        padding:14px 16px; background:var(--blue-l);
        border:1px solid var(--border); border-radius:8px; cursor:pointer;
        font-size:13px; color:var(--text); font-weight:500;
        transition:all 0.2s; margin-bottom:14px;
    }
    .material-toggle:has(input:checked) {
        border-color:var(--blue); background:rgba(27,79,168,0.05);
    }
    .material-toggle input { accent-color:var(--blue); width:16px; height:16px; }

    .material-split {
        display:inline-flex; align-items:center; gap:8px;
        padding:8px 13px; margin-top:10px;
        background:var(--orange-l); border:1px solid rgba(245,145,30,0.2);
        border-radius:6px; font-size:10px; color:var(--orange-dk); letter-spacing:0.5px; font-weight:500;
    }

    /* ═══════════════ PRICING CARDS ═══════════════ */
    .pricing-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
    @media (max-width:680px){ .pricing-grid{ grid-template-columns:1fr; } }
    .pricing-card {
        background:var(--bg); border:1px solid var(--border);
        border-radius:10px; padding:16px 18px;
        position:relative; overflow:hidden;
    }
    .pricing-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:3px;
        background:var(--pc, var(--blue));
    }
    .pricing-card.accent-orange { --pc:var(--orange); }
    .pricing-card.accent-green  { --pc:var(--green); }
    .pricing-card-label {
        font-size:9px; letter-spacing:2px; text-transform:uppercase;
        color:var(--muted); margin-bottom:10px; font-weight:600;
    }
    .pricing-card .form-control-inf {
        border:none; background:transparent; padding:0;
        font-family:'Bebas Neue',sans-serif; font-size:26px;
        color:var(--pc, var(--blue)); letter-spacing:2px;
        box-shadow:none !important;
    }

    .mt-2 { margin-top:12px; }
    .mt-3 { margin-top:18px; }

    /* Sub-label inside a section */
    .sub-label {
        font-size:9px; letter-spacing:3px; text-transform:uppercase;
        color:var(--orange); margin-bottom:14px; padding-bottom:8px;
        border-bottom:1px solid var(--orange-l); font-weight:600;
    }
    .sub-label.spaced { margin-top:22px; }
    .divider-soft { height:1px; background:var(--border); margin:22px 0; }

    /* ═══════════════ PAYMENT SUMMARY ═══════════════ */
    #payment_details { display:none; margin-top:18px; }
    .inf-pay-summary {
        background:var(--blue-l); border:1px solid var(--border);
        border-radius:9px; padding:16px 18px; margin-bottom:14px;
    }
    .inf-pay-row {
        display:flex; justify-content:space-between; align-items:baseline;
        padding:7px 0; border-bottom:1px solid rgba(27,79,168,0.06);
    }
    .inf-pay-row:last-child { border-bottom:none; }
    .inf-pay-key { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; }
    .inf-pay-val { font-size:13px; color:var(--text); font-weight:600; }
    .inf-pay-val.accent  { color:var(--orange); }
    .inf-pay-val.blue    { color:var(--blue); }
    .inf-pay-val.success { color:var(--green); }

    .inf-inst-label {
        font-size:9px; letter-spacing:3px; text-transform:uppercase;
        color:var(--orange); margin:16px 0 10px; padding-bottom:8px;
        border-bottom:1px solid var(--orange-l); font-weight:600;
    }

    #installments_table { width:100%; border-collapse:collapse; display:none; }
    #installments_table thead th {
        font-size:8px; letter-spacing:2px; text-transform:uppercase;
        color:var(--muted); padding:8px 10px; text-align:left;
        border-bottom:1px solid var(--border); font-weight:600;
    }
    #installments_table tbody td {
        font-size:12px; color:var(--text); font-weight:400;
        padding:9px 10px; border-bottom:1px solid rgba(27,79,168,0.05);
    }
    #installments_table tbody td:last-child { text-align:right; color:var(--orange); }
    #installments_table tbody tr:last-child td { border-bottom:none; }

    /* ═══════════════ DEPOSIT METHODS ═══════════════ */
    .deposit-notice {
        font-size:12px; color:var(--muted); margin-bottom:14px; padding:12px 16px;
        background:var(--blue-l); border:1px solid var(--border); border-radius:8px;
        display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .deposit-notice strong {
        color:var(--blue); font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:1px;
    }
    .payment-method-row {
        display:grid; grid-template-columns:1.5fr 1fr auto; gap:12px;
        align-items:end; margin-bottom:12px; padding:14px 16px;
        background:var(--bg); border:1px solid var(--border);
        border-radius:8px; animation:rowIn 0.25s ease both;
    }
    @keyframes rowIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:none} }
    .payment-method-row .form-control-inf { margin:0; background:#fff; }
    .btn-remove-method {
        width:38px; height:38px; display:flex; align-items:center; justify-content:center;
        background:#fff; border:1px solid rgba(220,38,38,0.2); border-radius:7px;
        cursor:pointer; color:var(--red); transition:all 0.2s; flex-shrink:0;
    }
    .btn-remove-method:hover { background:var(--red-l); border-color:rgba(220,38,38,0.4); }
    .btn-add-method {
        display:inline-flex; align-items:center; gap:7px;
        padding:10px 18px; margin-top:4px; background:#fff;
        border:1px dashed rgba(27,79,168,0.3); border-radius:7px; color:var(--muted);
        font-family:'DM Sans',sans-serif; font-size:11px; letter-spacing:2px;
        text-transform:uppercase; cursor:pointer; transition:all 0.2s; font-weight:600;
    }
    .btn-add-method:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-l); }
    .payment-total-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:14px 16px; margin-top:12px; background:var(--dark);
        border-radius:8px; font-size:12px;
    }
    .payment-total-label { color:rgba(255,255,255,0.6); letter-spacing:2px; text-transform:uppercase; font-size:10px; font-weight:600; }
    .payment-total-value { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; color:#fff; }
    .payment-total-value.error { color:#FF6B6B; }
    .payment-total-value.success { color:#10B981; }
    .payment-validation-msg {
        font-size:11px; margin-top:8px; padding:10px 14px;
        border-radius:7px; display:none; font-weight:500;
    }
    .payment-validation-msg.error { color:var(--red); background:var(--red-l); border:1px solid rgba(220,38,38,0.15); }
    .payment-validation-msg.success { color:var(--green); background:var(--green-l); border:1px solid rgba(5,150,105,0.15); }

    .deposit-error {
        display:flex; align-items:center; gap:8px; margin-top:12px;
        padding:12px 16px; background:var(--red-l);
        border:1px solid rgba(220,38,38,0.2); border-left:3px solid var(--red);
        border-radius:8px; font-size:12px; color:var(--red); font-weight:500;
    }

    /* ═══════════════ PACKAGE CARDS ═══════════════ */
    .package-card {
        padding:16px 20px; background:var(--bg);
        border:1.5px solid var(--border); border-radius:10px; cursor:pointer;
        transition:all 0.25s; min-width:170px; position:relative; overflow:hidden;
    }
    .package-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:3px;
        background:var(--blue); opacity:0; transition:opacity 0.25s;
    }
    .package-card:hover { border-color:rgba(27,79,168,0.3); transform:translateY(-2px); }
    .package-card.selected { border-color:var(--blue); background:rgba(27,79,168,0.05); }
    .package-card.selected::before { opacity:1; }
    .package-card-levels {
        font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:2px;
        color:var(--blue); line-height:1;
    }
    .package-card-label { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); margin-bottom:8px; font-weight:600; }
    .package-card-price { font-size:13px; color:var(--text); font-weight:600; margin-top:6px; }
    .package-card-per { font-size:10px; color:var(--muted); margin-top:3px; }
    .package-card-check {
        position:absolute; top:10px; right:10px; width:20px; height:20px;
        border-radius:50%; background:var(--blue);
        display:none; align-items:center; justify-content:center;
    }
    .package-card.selected .package-card-check { display:flex; }
    .optional-label { color:var(--faint); font-size:8px; letter-spacing:1px; }

    /* ═══════════════ STICKY FOOTER ═══════════════ */
    .reg-footer {
        max-width:1080px; margin:20px auto 0;
        background:var(--card); border:1px solid var(--border); border-radius:12px;
        padding:18px 26px; display:flex; align-items:center; justify-content:space-between;
        gap:14px; box-shadow:0 -2px 16px rgba(27,79,168,0.05);
        position:sticky; bottom:16px; z-index:10;
    }
    .reg-footer-hint {
        font-size:11px; color:var(--muted); display:flex; align-items:center; gap:8px;
    }
    .reg-footer-actions { display:flex; align-items:center; gap:10px; }
    .btn-cancel {
        padding:11px 24px; background:transparent;
        border:1px solid var(--border); border-radius:7px;
        color:var(--muted); font-family:'DM Sans',sans-serif;
        font-size:11px; letter-spacing:2px; text-transform:uppercase;
        text-decoration:none; transition:all 0.25s; cursor:pointer; font-weight:600;
    }
    .btn-cancel:hover { border-color:rgba(27,79,168,0.3); color:var(--blue); text-decoration:none; }
    .btn-submit {
        display:inline-flex; align-items:center; gap:9px;
        padding:13px 32px; background:linear-gradient(135deg, var(--blue), var(--blue-2));
        border:none; border-radius:7px; color:#fff;
        font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:4px;
        cursor:pointer; transition:all 0.25s; box-shadow:0 4px 16px rgba(27,79,168,0.25);
    }
    .btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(27,79,168,0.35); }
    .btn-submit svg { stroke:#fff; }

    /* Error alert */
    .reg-alert {
        max-width:1080px; margin:0 auto 16px; padding:14px 20px;
        background:var(--red-l); border:1px solid rgba(220,38,38,0.2);
        border-left:3px solid var(--red); border-radius:8px;
        display:flex; align-items:center; gap:12px; font-size:13px; color:var(--red);
    }
</style>

<div class="create-page">

    {{-- ═══════════════ COMMAND HEADER ═══════════════ --}}
    <div class="reg-header">
        <div class="reg-header-left">
            <div class="reg-eyebrow">Registration</div>
            <h1 class="reg-title">Register Student</h1>
            <div class="reg-sub">Enroll a lead into a course &amp; set up payment</div>
        </div>
        <a href="{{ route('leads.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Leads
        </a>
    </div>

    @if(session('error'))
    <div class="reg-alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ═══════════════ LEAD IDENTITY BAR ═══════════════ --}}
    <div class="lead-bar">
        <div class="lead-avatar">{{ strtoupper(substr($lead->full_name, 0, 1)) }}</div>
        <div class="lead-badge">
            <span class="lead-badge-label">Full Name</span>
            <span class="lead-badge-value">{{ $lead->full_name }}</span>
        </div>
        <div class="lead-badge">
            <span class="lead-badge-label">Phone</span>
            <span class="lead-badge-value">{{ $lead->phone }}</span>
        </div>
        <div class="lead-badge">
            <span class="lead-badge-label">Degree</span>
            <span class="lead-badge-value">{{ $lead->degree ?? '—' }}</span>
        </div>
        <div class="lead-badge">
            <span class="lead-badge-label">Location</span>
            <span class="lead-badge-value">{{ $lead->location ?? '—' }}</span>
        </div>
        @if($lead->start_preference_type)
        <div class="lead-badge">
            <span class="lead-badge-label">Start Preference</span>
            <span class="lead-badge-value">{{ $lead->start_preference_type }}</span>
        </div>
        @endif
    </div>

    <form id="main_form" method="POST" action="{{ route('registration.store') }}">
        @csrf
        <input type="hidden" name="lead_id"      value="{{ $lead->lead_id }}">
        <input type="hidden" name="final_price"  id="final_price_hidden">
        <input type="hidden" name="discount_value" id="discount_hidden">
        <input type="hidden" name="material_price" id="material_price_hidden">

        <div class="reg-layout">

            {{-- ═══════════ 1 · COURSE SETUP ═══════════ --}}
            <div class="sec-card">
                <div class="sec-head">
                    <div class="sec-num">1</div>
                    <div class="sec-title">Course Setup</div>
                    <div class="sec-hint">Select course, level &amp; sublevel</div>
                </div>
                <div class="sec-body">
                    <div class="form-grid cols-3">
                        <div class="form-field">
                            <label class="form-label">Course <span class="required">*</span></label>
                            <select id="course_select" name="course_template_id" class="form-control-inf">
                                <option value="">— Select Course —</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->course_template_id }}"
                                        {{ $lead->interested_course_template_id == $course->course_template_id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Level</label>
                            <select id="level_select" name="level_id" class="form-control-inf"
                                    data-selected="{{ $lead->interested_level_id }}">
                                <option value="">— Select Level —</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->level_id }}"
                                        {{ $lead->interested_level_id == $level->level_id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Sublevel</label>
                            <select id="sublevel_select" name="sublevel_id" class="form-control-inf"
                                    data-selected="{{ $lead->interested_sublevel_id }}">
                                <option value="">— Select Sublevel —</option>
                                @foreach($sublevels as $sublevel)
                                    <option value="{{ $sublevel->sublevel_id }}"
                                        {{ $lead->interested_sublevel_id == $sublevel->sublevel_id ? 'selected' : '' }}>
                                        {{ $sublevel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ 2 · ENROLLMENT & SCHEDULE ═══════════ --}}
            <div class="sec-card">
                <div class="sec-head">
                    <div class="sec-num">2</div>
                    <div class="sec-title">Enrollment &amp; Schedule</div>
                    <div class="sec-hint">Type, mode &amp; start</div>
                </div>
                <div class="sec-body">
                    <div class="form-grid">
                        {{-- Type --}}
                        <div class="form-field">
                            <label class="form-label">Enrollment Type <span class="required">*</span></label>
                            <div class="segmented">
                                <label>
                                    <input type="radio" name="type" value="group" checked>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    Group
                                </label>
                                <label>
                                    <input type="radio" name="type" value="private">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Private
                                </label>
                            </div>
                        </div>

                        {{-- Mode --}}
                        <div class="form-field">
                            <label class="form-label">Delivery Mode <span class="required">*</span></label>
                            <select name="mode" class="form-control-inf">
                                <option value="Offline">Offline</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                    </div>

                    <div class="divider-soft"></div>

                    {{-- Start Option --}}
                    <div class="form-field">
                        <label class="form-label">Start Option <span class="required">*</span></label>
                        <select id="patch_select" name="patch_option" class="form-control-inf"></select>
                        <input type="hidden" id="patch_id" name="patch_id">
                        <input type="hidden" name="course_instance_id" id="course_instance_id" value="">
                    </div>

                    <div id="custom_date_wrap" style="display:none;" class="mt-3">
                        <div class="form-field">
                            <label class="form-label">Specific Start Date <span class="required">*</span></label>
                            <input type="date" id="custom_date" name="custom_date"
                                   class="form-control-inf" style="color-scheme:light;">
                        </div>
                    </div>

                    {{-- Private Extra (teacher + days + bundle) --}}
                    <div id="private_extra" style="display:none;">
                        <div class="divider-soft"></div>

                        <div id="teacher_block">
                            <div class="form-field">
                                <label class="form-label">Teacher</label>
                                <select id="teacher_select" name="teacher_id" class="form-control-inf">
                                    <option value="">— Select Teacher —</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid mt-3">
                            <div class="form-field">
                                <label class="form-label">Preferred Days</label>
                                <select id="day_select" name="day" class="form-control-inf">
                                    <option value="">— Select Days —</option>
                                    <option value="sat_tue">Saturday - Tuesday</option>
                                    <option value="sun_wed">Sunday - Wednesday</option>
                                    <option value="mon_thu">Monday - Thursday</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label class="form-label">Bundle</label>
                                <select id="bundle_select" name="bundle_id" class="form-control-inf">
                                    <option value="">— Select Bundle —</option>
                                    @foreach($bundles as $b)
                                        <option value="{{ $b->bundle_id }}" data-price="{{ $b->price }}">
                                            {{ $b->hours }} hrs — {{ $b->price }} LE
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ 3 · MATERIAL & PACKAGE ═══════════ --}}
            <div class="sec-card" id="material_package_card">
                <div class="sec-head">
                    <div class="sec-num">3</div>
                    <div class="sec-title">Material &amp; Package</div>
                    <div class="sec-hint">Optional add-ons</div>
                </div>
                <div class="sec-body">
                    {{-- Materials (supports multiple per course) --}}
                    <div id="material_section" style="display:none;">
                        <div class="sub-label">Study Materials</div>
                        <div id="materials_list"></div>
                        {{-- selected material ids are injected here as hidden inputs by JS --}}
                        <div id="material_ids_container"></div>
                        <input type="hidden" name="material_price" id="material_price_hidden" value="0">
                    </div>

                    {{-- Package --}}
                    <div id="package_section" style="display:none;">
                        <div class="sub-label spaced">Level Package <span class="optional-label">(Optional)</span></div>
                        <div id="package_options" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px;"></div>
                        <input type="hidden" name="package_id" id="package_id_hidden">
                        <div id="package_selected_notice" style="display:none;
                            padding:12px 16px;margin-top:4px;
                            background:var(--blue-l);border:1px solid var(--border);
                            border-radius:8px;font-size:12px;color:var(--blue);">
                        </div>
                    </div>

                    {{-- Empty state hint --}}
                    <div id="addon_empty_hint" style="text-align:center;padding:20px;color:var(--faint);font-size:12px;">
                        Select a course to see available materials &amp; packages.
                    </div>
                </div>
            </div>

            {{-- ═══════════ 4 · PRICING & PLACEMENT ═══════════ --}}
            <div class="sec-card">
                <div class="sec-head">
                    <div class="sec-num">4</div>
                    <div class="sec-title">Pricing &amp; Placement</div>
                    <div class="sec-hint">Course price &amp; test</div>
                </div>
                <div class="sec-body">
                    <div class="sub-label">Course Pricing</div>
                    <div class="pricing-grid">
                        <div class="pricing-card">
                            <div class="pricing-card-label">Base Price</div>
                            <input id="base_price" class="form-control-inf" readonly placeholder="—">
                        </div>
                        <div class="pricing-card accent-orange">
                            <div class="pricing-card-label">Discount</div>
                            <input id="discount" class="form-control-inf" readonly placeholder="—">
                        </div>
                        <div class="pricing-card accent-green">
                            <div class="pricing-card-label">Final Price (Course)</div>
                            <input id="final_price" class="form-control-inf" readonly placeholder="—">
                        </div>
                    </div>

                    <div class="sub-label spaced">Placement Test</div>
                    <div class="form-grid cols-3">
                        <div class="form-field">
                            <label class="form-label">Test Type</label>
                            <select id="test_fee_select" class="form-control-inf" onchange="onTestFeeChange()">
                                <option value="">— No Test —</option>
                                @foreach($testFees as $tf)
                                <option value="{{ $tf->id }}"
                                        data-fee="{{ $tf->fee }}"
                                        data-name="{{ $tf->name }}">
                                    {{ $tf->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Test Fee (LE)</label>
                            <input name="test_fee" id="test_fee_input" class="form-control-inf"
                                readonly placeholder="Auto-filled"
                                value="{{ old('test_fee', 0) }}">
                            <input type="hidden" name="test_fee_setting_id" id="test_fee_setting_id">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Test Score</label>
                            <input name="test_score" id="test_score_input" class="form-control-inf"
                                placeholder="e.g. 85" value="{{ old('test_score') }}"
                                oninput="onTestScoreChange()">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ 5 · PAYMENT ═══════════ --}}
            <div class="sec-card">
                <div class="sec-head">
                    <div class="sec-num">5</div>
                    <div class="sec-title">Payment</div>
                    <div class="sec-hint">Plan &amp; deposit</div>
                </div>
                <div class="sec-body">
                    <div class="form-field">
                        <label class="form-label">Payment Plan <span class="required">*</span></label>
                        <select id="payment_plan_id" name="payment_plan_id" class="form-control-inf">
                            <option value="">— Select Plan —</option>
                            @foreach($paymentPlans as $plan)
                                <option
                                    value="{{ $plan->payment_plan_id }}"
                                    data-deposit="{{ $plan->deposit_percentage }}"
                                    data-installments="{{ $plan->installment_count }}"
                                    data-grace="{{ $plan->grace_period_days }}"
                                    data-approval="{{ $plan->requires_admin_approval ? 1 : 0 }}">
                                    {{ $plan->name }}
                                    ({{ $plan->deposit_percentage }}% deposit
                                    @if($plan->installment_count > 0)
                                        · {{ $plan->installment_count }} installments
                                    @else
                                        · Full payment
                                    @endif)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Summary --}}
                    <div id="payment_details">
                        <div class="inf-pay-summary" id="payment_summary"></div>
                        <div class="inf-inst-label" id="installments_label" style="display:none;">Installment Schedule</div>
                        <table id="installments_table">
                            <thead>
                                <tr><th>#</th><th>Amount</th><th>Due Date</th></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    {{-- Deposit Payment Methods --}}
                    <div id="deposit_section" style="display:none;">
                        <div class="divider-soft"></div>
                        <div class="sub-label">Deposit Payment Methods</div>

                        <div class="deposit-notice">
                            <span>Deposit required</span>
                            <strong id="deposit_required_amount">— LE</strong>
                        </div>

                        <div class="payment-methods-section">
                            <div id="payment_methods_container">
                                <div class="payment-method-row" id="method_row_0">
                                    <div class="form-field">
                                        <label class="form-label">Method</label>
                                        <select name="deposit_methods[0][method]" class="form-control-inf method-select">
                                            <option value="Cash">Cash</option>
                                            <option value="Instapay">Instapay</option>
                                            <option value="Vodafone_Cash">Vodafone Cash</option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label class="form-label">Amount (LE)</label>
                                        <input type="number" name="deposit_methods[0][amount]"
                                            class="form-control-inf method-amount"
                                            placeholder="0.00" step="0.01" min="0"
                                            oninput="updatePaymentTotal()">
                                    </div>
                                    <div></div>
                                </div>
                            </div>

                            <button type="button" class="btn-add-method" onclick="addPaymentMethod()">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                                Add Another Method
                            </button>

                            <div class="payment-total-row">
                                <span class="payment-total-label">Total Entered</span>
                                <span class="payment-total-value" id="payment_total_display">0.00 LE</span>
                            </div>

                            <div class="payment-validation-msg" id="payment_validation_msg"></div>
                        </div>
                    </div>

                    @error('deposit_methods')
                        <div class="deposit-error">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- ═══════════ 6 · NOTES ═══════════ --}}
            <div class="sec-card">
                <div class="sec-head">
                    <div class="sec-num">6</div>
                    <div class="sec-title">Registration Notes</div>
                    <div class="sec-hint">Optional</div>
                </div>
                <div class="sec-body">
                    <div class="form-field">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control-inf"
                                  placeholder="Any additional notes about this registration..."></textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Hidden fields for invoice --}}
        <input type="hidden" id="student_name"  value="{{ $lead->full_name }}">
        <input type="hidden" id="student_phone" value="{{ $lead->phone }}">

        {{-- ═══════════════ STICKY FOOTER ═══════════════ --}}
        <div class="reg-footer">
            <div class="reg-footer-hint">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                Review the invoice before confirming registration.
            </div>
            <div class="reg-footer-actions">
                <a href="{{ route('leads.index') }}" class="btn-cancel">Cancel</a>
                <button type="button" id="preview_invoice_btn" class="btn-submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span>Review &amp; Register</span>
                </button>
            </div>
        </div>
    </form>

    @include('registration.invoice')

</div>

<script src="{{ asset('js/register/register-modal.js') }}"></script>

<script>
    // Toggle the add-on empty hint based on whether material/package sections are visible
    (function() {
        const hint = document.getElementById('addon_empty_hint');
        const matSection = document.getElementById('material_section');
        const pkgSection = document.getElementById('package_section');
        if (!hint) return;

        const observer = new MutationObserver(() => {
            const matVisible = matSection && matSection.style.display !== 'none';
            const pkgVisible = pkgSection && pkgSection.style.display !== 'none';
            hint.style.display = (matVisible || pkgVisible) ? 'none' : 'block';
        });

        [matSection, pkgSection].forEach(el => {
            if (el) observer.observe(el, { attributes:true, attributeFilter:['style'] });
        });
    })();
</script>

@endsection