@extends('layouts.leads')

@section('title', 'Register Student')

@section('content')
<script src="{{ asset('js/register/register-modal.js') }}"></script>

<div class="create-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Registration</div>
            <h1 class="page-title">Register Student</h1>
        </div>
    </div>

    <div class="form-card">
        <div class="form-card-body">

            <form method="POST" action="{{ route('registration.store') }}">
                @csrf

                <input type="hidden" name="lead_id" value="{{ $lead->lead_id }}">

                {{-- ================= BASIC INFO ================= --}}
                <div class="form-section-label">Student Info</div>

                <div class="form-grid cols-4">
                    <input class="form-control-inf" value="{{ $lead->full_name }}" readonly>
                    <input class="form-control-inf" value="{{ $lead->phone }}" readonly>
                    <input class="form-control-inf" value="{{ $lead->degree }}" readonly>
                    <input class="form-control-inf" value="{{ $lead->location }}" readonly>
                </div>

                <div class="form-divider"></div>

                {{-- ================= COURSE ================= --}}
                <div class="form-section-label">Course Setup</div>

                <div class="form-grid cols-3">

                    <select id="course_select" name="course_template_id" class="form-control-inf">
                        @foreach($courses as $course)
                            <option value="{{ $course->course_template_id }}">
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="level_select" name="level_id" class="form-control-inf">
                        <option value="">Select Level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->level_id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>

                    <select id="sublevel_select" name="sublevel_id" class="form-control-inf">
                        <option value="">Select Sublevel</option>
                        @foreach($sublevels as $sub)
                            <option value="{{ $sub->sublevel_id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="form-divider"></div>

                {{-- ================= TYPE ================= --}}
                <div class="form-section-label">Enrollment Type</div>

                <select id="type_select" name="type" class="form-control-inf">
                    <option value="group">Group</option>
                    <option value="private">Private</option>
                </select>

                <div class="form-divider"></div>

                {{-- ================= PATCH (GROUP) ================= --}}
                <div id="group_section">

                    <div class="form-section-label">Patch</div>

                    <select id="patch_select" name="patch_option" class="form-control-inf"></select>

                    <input type="hidden" name="patch_id" id="patch_id">

                    <input type="date" id="custom_date" name="custom_date"
                           class="form-control-inf mt-2"
                           style="display:none;">
                </div>

                {{-- ================= PRIVATE ================= --}}
                <div id="private_section" style="display:none;">

                    <div class="form-section-label">Schedule</div>

                    <div class="form-grid cols-3">

                        <select id="day_select" name="day" class="form-control-inf">
                            <option value="Sun">Sunday</option>
                            <option value="Mon">Monday</option>
                            <option value="Tue">Tuesday</option>
                            <option value="Wed">Wednesday</option>
                            <option value="Thu">Thursday</option>
                            <option value="Fri">Friday</option>
                            <option value="Sat">Saturday</option>
                        </select>

                        <select id="time_slot_select" name="time_slot_id" class="form-control-inf">
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot->time_slot_id }}">
                                    {{ $slot->name }}
                                </option>
                            @endforeach
                        </select>

                        <select id="teacher_select" name="teacher_id" class="form-control-inf"></select>

                    </div>

                    <input type="date" id="recommended_date"
                           name="recommended_date"
                           class="form-control-inf mt-2"
                           style="display:none;">
                </div>

                <div class="form-divider"></div>

                {{-- ================= PRICING ================= --}}
                <div class="form-section-label">Pricing</div>

                <div class="form-grid cols-3">

                    <input type="text" id="base_price" placeholder="Base Price" readonly class="form-control-inf">

                    <input type="number" id="discount" name="discount_value"
                           placeholder="Discount" value="0"
                           class="form-control-inf">

                    <input type="text" id="final_price" placeholder="Final Price"
                           readonly class="form-control-inf">
                </div>

                <div class="form-divider"></div>

                {{-- ================= PAYMENT ================= --}}
                <div class="form-section-label">Payment</div>

                <select name="payment_plan_id" class="form-control-inf">
                    @foreach($paymentPlans as $plan)
                        <option value="{{ $plan->payment_plan_id }}">
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>

                <div class="form-divider"></div>

                {{-- ================= SUBMIT ================= --}}
                <div class="form-footer">
                    <button class="btn-submit">Register Student</button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection