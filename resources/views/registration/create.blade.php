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
    .lead-badge {
        display: flex; flex-direction: column; gap: 2px;
    }
    .lead-badge-label {
        font-size: 8px; letter-spacing: 2px; text-transform: uppercase; color: #AAB8C8;
    }
    .lead-badge-value {
        font-size: 13px; color: #1A2A4A; font-weight: 500;
    }

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
    .form-grid        { display: grid; grid-template-columns: 1fr 1fr;             gap: 16px 20px; margin-bottom: 20px; }
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
    .form-radio-group {
        display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
    }
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
    .form-radio-group input[type="radio"] {
        accent-color: #1B4FA8;
        width: 14px; height: 14px;
    }

    /* ── PRICING CARDS ── */
    .pricing-grid {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;
        margin-bottom: 20px;
    }
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
        /* ══ PAYMENT SUMMARY WIDGET ══ */
    #payment_details {
        display: none;
        margin-top: 16px;
        font-family: 'DM Sans', sans-serif;
    }
 
    .inf-pay-summary {
        background: rgba(27,79,168,0.04);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 5px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }
 
    .inf-pay-row {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 5px 0; border-bottom: 1px solid rgba(27,79,168,0.06);
    }
    .inf-pay-row:last-child { border-bottom: none; }
 
    .inf-pay-key {
        font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
        color: #7A8A9A; font-weight: 400;
    }
    .inf-pay-val {
        font-size: 12px; color: #1A2A4A; font-weight: 500;
    }
    .inf-pay-val.accent  { color: #F5911E; }
    .inf-pay-val.blue    { color: #1B4FA8; }
    .inf-pay-val.success { color: #059669; }
 
    /* ── installments table ── */
    .inf-inst-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin: 14px 0 8px; padding-bottom: 7px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
    }
 
    #installments_table {
        width: 100%; border-collapse: collapse;
        display: none;
    }
    #installments_table thead th {
        font-size: 8px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; padding: 6px 8px; text-align: left;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        font-weight: 500;
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
        <a href="{{ route('leads.index') }}" class="btn-back">Back</a>
    </div>

    <div class="form-card">
        <div class="form-card-body">

            <form method="POST" action="{{ route('registration.store') }}">
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">

                {{-- STUDENT --}}
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
                </div>

                <div class="form-divider"></div>

                {{-- COURSE --}}
                <div class="form-section-label">Course Setup</div>

                <div class="form-grid cols-3">
                    <select id="course_select" name="course_template_id" class="form-control-inf">
                        @foreach($courses as $course)
                            <option value="{{ $course->course_template_id }}"
                                {{ $lead->interested_course_template_id == $course->course_template_id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="level_select" name="level_id" class="form-control-inf">
                        <option value="">Select Level</option>
                    </select>

                    <select id="sublevel_select" name="sublevel_id" class="form-control-inf">
                        <option value="">Select Sublevel</option>
                    </select>
                </div>

                <div class="form-divider"></div>

                {{-- MATERIAL --}}
                <div id="material_section" style="display:none;">
                    <div class="form-section-label">Material</div>

                    <label class="material-toggle">
                        <input type="checkbox" id="material_check">
                        Include Study Material
                    </label>

                    <div id="material_price_block" style="display:none;">
                        <input type="text" id="material_name" class="form-control-inf mb-2" readonly>
                        <input type="text" id="material_price" class="form-control-inf" readonly>
                        <input type="hidden" id="material_price_hidden" name="material_price">
                    </div>

                    <div class="form-divider"></div>
                </div>

                {{-- TYPE --}}
                <div class="form-section-label">Enrollment Type</div>

                <div class="form-radio-group">
                    <label><input type="radio" name="type" value="group" checked> Group</label>
                    <label><input type="radio" name="type" value="private"> Private</label>
                </div>

                <div class="form-divider"></div>

                {{-- DELIVERY --}}
                <div class="form-section-label">Delivery Mode</div>

                <select name="mode" class="form-control-inf">
                    <option value="Offline">Offline</option>
                    <option value="Online">Online</option>
                </select>

                <div class="form-divider"></div>

                {{-- PATCH --}}
                <div class="form-section-label">Start Option</div>

                <select id="patch_select" name="patch_option" class="form-control-inf"></select>
                <input type="hidden" id="patch_id" name="patch_id">

                <input type="date" id="custom_date" name="custom_date"
                       class="form-control-inf mt-2"
                       style="display:none;">

                <div class="form-divider"></div>

                {{-- PRIVATE --}}
                <div id="private_extra" style="display:none;">
                    <div id="teacher_block">
                        <div class="form-section-label">Teacher</div>
                        <select id="teacher_select" name="teacher_id" class="form-control-inf"></select>
                    </div>
                    <div class="form-section-label mt-2">Preferred Days</div>
                    <select id="day_select" name="day" class="form-control-inf">
                        <option value="">Select Pair</option>
                        <option value="sat_tue">Saturday - Tuesday</option>
                        <option value="sun_wed">Sunday - Wednesday</option>
                        <option value="mon_thu">Monday - Thursday</option>
                    </select>

                    <div class="form-section-label mt-3">Bundles</div>
                    <select id="bundle_select" name="bundle_id" class="form-control-inf">
                        <option value="">Select Bundle</option>
                        @foreach($bundles as $b)
                            <option value="{{ $b->bundle_id }}" data-price="{{ $b->price }}">
                                {{ $b->hours }} hrs ({{ $b->price }} LE)
                            </option>
                        @endforeach
                    </select>

                    <div class="form-divider"></div>
                </div>

                {{-- PRICING --}}
                <div class="form-section-label">Pricing</div>

                <div class="form-grid cols-3">
                    <div>
                        <label>Base Price</label>
                        <input id="base_price" readonly class="form-control-inf">
                    </div>

                    <div>
                        <label>Discount</label>
                        <input id="discount" readonly class="form-control-inf">
                        <input type="hidden" name="discount_value" id="discount_hidden">
                    </div>

                    <div>
                        <label>Final Price</label>
                        <input id="final_price" readonly class="form-control-inf">
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- TEST --}}
                <div class="form-section-label">Placement Test</div>

                <div class="form-grid cols-2">
                    <input name="test_score" placeholder="Score" class="form-control-inf">
                    <input name="test_fee" placeholder="Fee" class="form-control-inf">
                </div>

                <div class="form-divider"></div>

                {{-- PAYMENT --}}
                <div class="form-section-label">Payment</div>

                <select id="payment_plan_id" name="payment_plan_id">
                    @foreach($paymentPlans as $plan)
                        <option 
                            value="{{ $plan->payment_plan_id }}"
                            data-deposit="{{ $plan->deposit_percentage }}"
                            data-installments="{{ $plan->installment_count }}"
                            data-grace="{{ $plan->grace_period_days }}"
                        >
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>

                {{-- ══ PAYMENT SUMMARY ══ --}}
                <div id="payment_details">
                
                    <div class="inf-pay-summary" id="payment_summary"></div>
                
                    <div class="inf-inst-label" id="installments_label" style="display:none;">
                        Installment Schedule
                    </div>
                
                    <table id="installments_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                
                </div>

                <div class="form-divider"></div>

                <div class="form-footer">
                    <input type="hidden" id="student_name" value="{{ $lead->full_name }}">
                    <input type="hidden" id="student_phone" value="{{ $lead->phone }}">
                    <input type="hidden" id="discount_hidden">
                    <input type="hidden" id="material_price_hidden">
                    <input type="hidden" name="final_price" id="final_price_hidden">
                    <button type="button" id="preview_invoice_btn" class="btn-submit">view Invoice</button>
                </div>

            </form>
            @include('registration.invoice')

        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/register/register-modal.js') }}"></script>


@endsection