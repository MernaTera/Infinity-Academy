@extends('layouts.leads')

@section('title', 'Register Student')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    .create-page {
        background: #F8F6F2;
        min-height: 100vh;
        padding: 40px 32px;
        color: #1A2A4A;
        font-family: 'DM Sans', sans-serif;
    }

    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 32px; padding-bottom: 22px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap; gap: 16px;
    }

    .page-eyebrow { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title   { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.2); border-radius: 4px;
        color: #7A8A9A; font-size: 10px; letter-spacing: 3px;
        text-transform: uppercase; text-decoration: none;
        transition: all 0.3s; font-family: 'DM Sans', sans-serif;
    }
    .btn-back:hover { border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    /* ── LEAD BADGE ── */
    .lead-badge-strip {
        display: flex; gap: 16px; flex-wrap: wrap;
        padding: 14px 0; margin-bottom: 20px;
    }
    .lead-badge { display: flex; flex-direction: column; gap: 2px; }
    .lead-badge-label { font-size: 8px; letter-spacing: 2px; text-transform: uppercase; color: #AAB8C8; }
    .lead-badge-value { font-size: 13px; color: #1A2A4A; font-weight: 500; }

    /* ── FORM CARD ── */
    .form-card {
        max-width: 920px;
        margin: 0 auto;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 8px; overflow: hidden; position: relative;
        box-shadow: 0 4px 24px rgba(27,79,168,0.07);
    }
    .form-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #F5911E, #1B4FA8, transparent);
    }

    .form-card-body { padding: 28px 32px 32px; }

    /* ── SECTION LABEL ── */
    .form-section-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 16px; padding-bottom: 9px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
        margin-top: 4px;
    }

    /* ── GRIDS ── */
    .form-grid        { display: grid; grid-template-columns: 1fr 1fr;         gap: 16px 20px; margin-bottom: 20px; }
    .form-grid.cols-1 { grid-template-columns: 1fr; }
    .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    .form-grid.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }

    @media (max-width: 680px) {
        .form-grid, .form-grid.cols-3, .form-grid.cols-4 { grid-template-columns: 1fr; }
        .create-page { padding: 18px 14px; }
        .form-card-body { padding: 18px; }
    }

    .form-field { display: flex; flex-direction: column; gap: 6px; }

    .form-label {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; font-weight: 500;
    }
    .form-label .required { color: #F5911E; margin-left: 2px; }

    .form-control-inf {
        width: 100%; padding: 10px 12px;
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(27,79,168,0.12); border-radius: 4px;
        color: #1A2A4A; font-family: 'DM Sans', sans-serif;
        font-size: 13px; font-weight: 300; outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none; -webkit-appearance: none;
    }
    .form-control-inf::placeholder { color: #B0BCCC; }
    .form-control-inf:focus {
        border-color: #1B4FA8;
        box-shadow: 0 0 0 3px rgba(27,79,168,0.08);
    }
    .form-control-inf[readonly] {
        background: rgba(248,246,242,0.8);
        color: #7A8A9A;
        cursor: default;
    }
    textarea.form-control-inf { resize: vertical; min-height: 88px; }

    select.form-control-inf {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 11px center;
        padding-right: 32px; cursor: pointer;
        background-color: rgba(255,255,255,0.92);
    }
    select.form-control-inf option { background: #fff; color: #1A2A4A; }

    .form-error { font-size: 10px; color: #DC2626; margin-top: 2px; }
    .form-divider { height: 1px; background: rgba(27,79,168,0.06); margin: 24px 0; }

    /* ── RADIO GROUP ── */
    .form-radio-group { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .form-radio-group label {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 18px;
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(27,79,168,0.12);
        border-radius: 4px; cursor: pointer;
        font-size: 12px; color: #4A5A7A;
        transition: all 0.2s;
    }
    .form-radio-group label:has(input:checked) {
        border-color: #1B4FA8;
        background: rgba(27,79,168,0.04);
        color: #1B4FA8;
    }
    .form-radio-group input[type="radio"] { accent-color: #1B4FA8; width: 14px; height: 14px; }

    /* ── MATERIAL TOGGLE ── */
    .material-toggle {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 14px;
        background: rgba(27,79,168,0.02);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 4px; cursor: pointer;
        font-size: 12px; color: #4A5A7A;
        transition: all 0.2s; margin-bottom: 12px;
    }
    .material-toggle:has(input:checked) {
        border-color: #1B4FA8; background: rgba(27,79,168,0.04); color: #1B4FA8;
    }
    .material-toggle input { accent-color: #1B4FA8; }

    /* ── MATERIAL CS SPLIT BADGE ── */
    .material-split {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 12px; margin-top: 8px;
        background: rgba(245,145,30,0.06);
        border: 1px solid rgba(245,145,30,0.2);
        border-radius: 4px;
        font-size: 10px; color: #92400E; letter-spacing: 0.5px;
    }

    /* ── PRICING ── */
    .pricing-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .pricing-card {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px; padding: 14px 16px;
        position: relative; overflow: hidden;
    }
    .pricing-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--pc, #1B4FA8), transparent);
    }
    .pricing-card-label {
        font-size: 9px; letter-spacing: 2px; text-transform: uppercase;
        color: #AAB8C8; margin-bottom: 8px;
    }
    .pricing-card .form-control-inf {
        border: none; background: transparent; padding: 0;
        font-family: 'Bebas Neue', sans-serif; font-size: 24px;
        color: var(--pc, #1B4FA8); letter-spacing: 2px;
        box-shadow: none !important;
    }

    /* ── MT UTILITY ── */
    .mt-2 { margin-top: 10px; }
    .mt-3 { margin-top: 16px; }

    /* ── FOOTER ── */
    .form-footer {
        display: flex; align-items: center; justify-content: flex-end;
        gap: 10px; padding-top: 20px;
        border-top: 1px solid rgba(27,79,168,0.07);
    }

    .btn-cancel {
        padding: 10px 22px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 4px;
        color: #7A8A9A; font-family: 'DM Sans', sans-serif;
        font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        text-decoration: none; transition: all 0.3s; cursor: pointer;
    }
    .btn-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; text-decoration: none; }

    .btn-submit {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 28px; background: transparent;
        border: 1.5px solid #1B4FA8; border-radius: 4px;
        color: #1B4FA8; font-family: 'Bebas Neue', sans-serif;
        font-size: 14px; letter-spacing: 4px;
        cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s;
    }
    .btn-submit::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-submit:hover::before { transform: scaleX(1); }
    .btn-submit:hover { color: #fff; }
    .btn-submit span, .btn-submit svg { position: relative; z-index: 1; }

    /* ── PAYMENT SUMMARY ── */
    #payment_details { display: none; margin-top: 16px; font-family: 'DM Sans', sans-serif; }

    .inf-pay-summary {
        background: rgba(27,79,168,0.04);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 5px; padding: 14px 16px; margin-bottom: 12px;
    }
    .inf-pay-row {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 5px 0; border-bottom: 1px solid rgba(27,79,168,0.06);
    }
    .inf-pay-row:last-child { border-bottom: none; }
    .inf-pay-key { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #7A8A9A; }
    .inf-pay-val { font-size: 12px; color: #1A2A4A; font-weight: 500; }
    .inf-pay-val.accent  { color: #F5911E; }
    .inf-pay-val.blue    { color: #1B4FA8; }
    .inf-pay-val.success { color: #059669; }

    .inf-inst-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin: 14px 0 8px; padding-bottom: 7px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
    }
    .payment-methods-section { margin-top: 16px; }
    
    .payment-method-row {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr auto;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
        padding: 12px 14px;
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 4px;
        animation: rowIn 0.25s ease both;
    }
    .deposit-error {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 10px 14px;
        background: rgba(220,38,38,0.05);
        border: 1px solid rgba(220,38,38,0.2);
        border-left: 3px solid #DC2626;
        border-radius: 4px;
        font-size: 12px;
        color: #DC2626;
        letter-spacing: 0.2px;
        animation: msgIn 0.25s ease both;
    }
    @keyframes rowIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:none; } }
    
    .payment-method-row .form-control-inf { margin: 0; }
    
    .btn-remove-method {
        width: 30px; height: 30px;
        display: flex; align-items: center; justify-content: center;
        background: transparent;
        border: 1px solid rgba(220,38,38,0.2);
        border-radius: 4px; cursor: pointer; color: #DC2626;
        transition: all 0.2s; flex-shrink: 0;
    }
    .btn-remove-method:hover { background: rgba(220,38,38,0.06); border-color: rgba(220,38,38,0.4); }
    
    .btn-add-method {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; margin-top: 4px;
        background: transparent;
        border: 1px dashed rgba(27,79,168,0.25);
        border-radius: 4px; color: #7A8A9A;
        font-family: 'DM Sans', sans-serif;
        font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-add-method:hover { border-color: #1B4FA8; color: #1B4FA8; }
    
    .payment-total-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px; margin-top: 8px;
        border-top: 1px solid rgba(27,79,168,0.08);
        font-size: 12px;
    }
    .payment-total-label { color: #7A8A9A; letter-spacing: 1px; text-transform: uppercase; font-size: 10px; }
    .payment-total-value { font-family: 'Bebas Neue', sans-serif; font-size: 18px; letter-spacing: 2px; color: #1B4FA8; }
    .payment-total-value.error { color: #DC2626; }
    .payment-total-value.success { color: #059669; }
    
    .payment-validation-msg {
        font-size: 11px; margin-top: 6px; padding: 8px 12px;
        border-radius: 4px; display: none;
    }
    .payment-validation-msg.error { color: #DC2626; background: rgba(220,38,38,0.05); border: 1px solid rgba(220,38,38,0.15); }
    .payment-validation-msg.success { color: #059669; background: rgba(5,150,105,0.05); border: 1px solid rgba(5,150,105,0.15); }
    .package-card {
        padding: 14px 18px;
        background: rgba(255,255,255,0.9);
        border: 1.5px solid rgba(27,79,168,0.12);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.25s;
        min-width: 160px;
        position: relative;
        overflow: hidden;
    }
    .package-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #1B4FA8, transparent);
        opacity: 0; transition: opacity 0.25s;
    }
    .package-card:hover { border-color: rgba(27,79,168,0.3); }
    .package-card.selected {
        border-color: #1B4FA8;
        background: rgba(27,79,168,0.04);
    }
    .package-card.selected::before { opacity: 1; }
    .package-card-levels {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 28px; letter-spacing: 2px; color: #1B4FA8; line-height: 1;
    }
    .package-card-label { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #AAB8C8; margin-bottom: 6px; }
    .package-card-price { font-size: 13px; color: #1A2A4A; font-weight: 500; margin-top: 4px; }
    .package-card-per { font-size: 10px; color: #7A8A9A; margin-top: 2px; }
    .package-card-check {
        position: absolute; top: 8px; right: 8px;
        width: 18px; height: 18px; border-radius: 50%;
        background: #1B4FA8;
        display: none; align-items: center; justify-content: center;
    }
    .optional-label {
        color: #AAB8C8;
        font-size: 8px;
        letter-spacing: 1px;
    }
    .package-card.selected .package-card-check { display: flex; }
    #installments_table { width: 100%; border-collapse: collapse; display: none; }
    #installments_table thead th {
        font-size: 8px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; padding: 6px 8px; text-align: left;
        border-bottom: 1px solid rgba(27,79,168,0.1); font-weight: 500;
    }
    #installments_table tbody td {
        font-size: 12px; color: #1A2A4A; font-weight: 300;
        padding: 7px 8px; border-bottom: 1px solid rgba(27,79,168,0.05);
    }
    #installments_table tbody td:last-child { text-align: right; color: #F5911E; }
    #installments_table tbody tr:last-child td { border-bottom: none; }
</style>

<div class="create-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Registration</div>
            <h1 class="page-title">Register Student</h1>
        </div>
        <a href="{{ route('leads.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-body">

            <form id="main_form" method="POST" action="{{ route('registration.store') }}">
                @csrf
                <input type="hidden" name="lead_id"      value="{{ $lead->lead_id }}">
                <input type="hidden" name="final_price"  id="final_price_hidden">
                <input type="hidden" name="discount_value" id="discount_hidden">
                <input type="hidden" name="material_price" id="material_price_hidden">
                <input type="hidden" name="course_instance_id" id="course_instance_id">

                {{-- ══ STUDENT INFO ══ --}}
                <div class="form-section-label">Student Information</div>

                <div class="lead-badge-strip">
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
                        <span class="lead-badge-value">{{ $lead->degree }}</span>
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

                <div class="form-divider"></div>

                {{-- ══ COURSE SETUP ══ --}}
                <div class="form-section-label">Course Setup</div>

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


                <div class="form-divider"></div>

                {{-- ══ ENROLLMENT TYPE ══ --}}
                <div class="form-section-label">Enrollment Type</div>

                <div class="form-radio-group">
                    <label><input type="radio" name="type" value="group" checked> Group</label>
                    <label><input type="radio" name="type" value="private"> Private</label>
                </div>

                <div class="form-divider"></div>

                {{-- ══ DELIVERY MODE ══ --}}
                <div class="form-section-label">Delivery Mode</div>

                <select name="mode" class="form-control-inf">
                    <option value="Offline">Offline</option>
                    <option value="Online">Online</option>
                </select>

                <div class="form-divider"></div>

                {{-- ══ START OPTION ══ --}}
                <div class="form-section-label">Start Option</div>

                <select id="patch_select" name="patch_option" class="form-control-inf"></select>
                <input type="hidden" id="patch_id" name="patch_id">

                <div id="custom_date_wrap" style="display:none; margin-top:12px;">
                    <label class="form-label">Specific Start Date <span class="required">*</span></label>
                    <input type="date" id="custom_date" name="custom_date"
                           class="form-control-inf" style="color-scheme:light;">
                </div>

                <div class="form-divider"></div>

                {{-- ══ PRIVATE EXTRA ══ --}}
                <div id="private_extra" style="display:none;">
                    <div id="teacher_block">
                        <div class="form-section-label">Teacher</div>
                        <select id="teacher_select" name="teacher_id" class="form-control-inf">
                            <option value="">— Select Teacher —</option>
                        </select>
                    </div>

                    <div class="form-section-label mt-2">Preferred Days</div>
                    <select id="day_select" name="day" class="form-control-inf">
                        <option value="">— Select Days —</option>
                        <option value="sat_tue">Saturday - Tuesday</option>
                        <option value="sun_wed">Sunday - Wednesday</option>
                        <option value="mon_thu">Monday - Thursday</option>
                    </select>

                    <div class="form-section-label mt-3">Bundles</div>
                    <select id="bundle_select" name="bundle_id" class="form-control-inf">
                        <option value="">— Select Bundle —</option>
                        @foreach($bundles as $b)
                            <option value="{{ $b->bundle_id }}" data-price="{{ $b->price }}">
                                {{ $b->hours }} hrs — {{ $b->price }} LE
                            </option>
                        @endforeach
                    </select>

                    <div class="form-divider"></div>
                </div>
                {{-- ══ MATERIAL ══ --}}
                <div id="material_section" style="display:none;">
                    <div class="form-section-label">Study Material</div>

                    <label class="material-toggle">
                        <input type="checkbox" id="material_check">
                        Include Study Material
                    </label>

                    <div id="material_price_block" style="display:none;">
                        <div class="form-grid cols-2">
                            <div class="form-field">
                                <label class="form-label">Material Name</label>
                                <input type="text" id="material_name" class="form-control-inf" readonly>
                            </div>
                            <div class="form-field">
                                <label class="form-label">Material Price</label>
                                <input type="text" id="material_price" class="form-control-inf" readonly>
                            </div>
                        </div>
                        {{-- CS split badge — shown when cs_percentage > 0 --}}
                        <div id="material_split_badge" class="material-split" style="display:none;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                            <span id="material_split_text"></span>
                        </div>
                    </div>

                    <div class="form-divider"></div>
                </div>
                <div id="package_section" style="display:none;">
                <div class="form-section-label">Level Package <span class="optional-label">(Optional)</span></div>
                
                    <div id="package_options" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px;"></div>
                
                    <input type="hidden" name="package_id" id="package_id_hidden">
                
                    <div id="package_selected_notice" style="display:none;
                        padding:10px 14px;margin-top:4px;
                        background:rgba(27,79,168,0.04);
                        border:1px solid rgba(27,79,168,0.15);
                        border-radius:4px;font-size:12px;color:#1B4FA8;">
                    </div>
                
                    <div class="form-divider"></div>
                </div>
                {{-- ══ PRICING ══ --}}
                <div class="form-section-label">Pricing</div>

                <div class="form-grid cols-3">
                    <div class="form-field">
                        <label class="form-label">Base Price</label>
                        <input id="base_price" class="form-control-inf" readonly placeholder="—">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Discount</label>
                        <input id="discount" class="form-control-inf" readonly placeholder="—">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Final Price (Course)</label>
                        <input id="final_price" class="form-control-inf" readonly placeholder="—">
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- ══ PLACEMENT TEST ══ --}}
                <div class="form-section-label">Placement Test</div>

                <div class="form-grid cols-2">
                    <div class="form-field">
                        <label class="form-label">Test Score</label>
                        <input name="test_score" class="form-control-inf" placeholder="e.g. 85">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Test Fee (LE)</label>
                        <input name="test_fee" class="form-control-inf" placeholder="e.g. 200">
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- ══ PAYMENT PLAN ══ --}}
                <div class="form-section-label">Payment Plan</div>

                <div class="form-field">
                    <label class="form-label">Select Plan <span class="required">*</span></label>
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

                <div class="form-divider"></div>
    
                {{-- ── DEPOSIT PAYMENT METHODS ── --}}
                <div id="deposit_section" style="display:none;">
                    <div class="form-section-label">Deposit Payment Methods</div>
                
                    <div id="deposit_required_notice"
                        style="font-size:12px;color:#7A8A9A;margin-bottom:12px;padding:10px 14px;
                                background:rgba(27,79,168,0.03);border:1px solid rgba(27,79,168,0.08);border-radius:4px;">
                        Deposit required:
                        <strong id="deposit_required_amount" style="color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;">
                            — LE
                        </strong>
                    </div>
                
                    <div class="payment-methods-section">
                        <div id="payment_methods_container">
                            {{-- First row always shown --}}
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
                                <div style="display:flex;align-items:flex-end;padding-bottom:0;">
                                    {{-- First row has no remove button --}}
                                </div>
                            </div>
                        </div>
                
                        <button type="button" class="btn-add-method" onclick="addPaymentMethod()">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Add Another Method
                        </button>
                
                        {{-- Total row --}}
                        <div class="payment-total-row">
                            <span class="payment-total-label">Total Entered</span>
                            <span class="payment-total-value" id="payment_total_display">0.00 LE</span>
                        </div>
                
                        <div class="payment-validation-msg" id="payment_validation_msg"></div>
                    </div>
                
                    <div class="form-divider"></div>
                </div>
                @error('deposit_methods')
                    <div class="deposit-error">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4m0 4h.01"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror

                {{-- ══ NOTES ══ --}}
                <div class="form-section-label">Registration Notes</div>

                <div class="form-field">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control-inf"
                              placeholder="Any additional notes about this registration..."></textarea>
                </div>

                <div class="form-divider"></div>

                {{-- ══ FOOTER ══ --}}
                <div class="form-footer">
                    <a href="{{ route('leads.index') }}" class="btn-cancel">Cancel</a>
                    <button type="button" id="preview_invoice_btn" class="btn-submit">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Review &amp; Register</span>
                    </button>
                </div>
                <input type="hidden" id="student_name"  value="{{ $lead->full_name }}">
                <input type="hidden" id="student_phone" value="{{ $lead->phone }}">
            </form>

            @include('registration.invoice')

        </div>
    </div>

</div>

<script src="{{ asset('js/register/register-modal.js') }}"></script>

@endsection