@extends('layouts.leads')

@section('title', 'Register Student')

@section('content')

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

                {{-- ================= STUDENT INFO ================= --}}
                <div class="form-section-label">Student Info</div>

                <div class="form-grid cols-4">
                    <input value="{{ $lead->full_name }}" readonly class="form-control-inf">
                    <input value="{{ $lead->phone }}" readonly class="form-control-inf">
                    <input value="{{ $lead->degree }}" readonly class="form-control-inf">
                    <input value="{{ $lead->location }}" readonly class="form-control-inf">
                </div>

                <div class="form-divider"></div>

                {{-- ================= COURSE ================= --}}
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
                        @foreach($levels as $level)
                            <option value="{{ $level->level_id }}"
                                {{ $lead->interested_level_id == $level->level_id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="sublevel_select" name="sublevel_id" class="form-control-inf">
                        @foreach($sublevels as $sub)
                            <option value="{{ $sub->sublevel_id }}"
                                {{ $lead->interested_sublevel_id == $sub->sublevel_id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <div class="form-divider"></div>

                {{-- ================= TYPE ================= --}}
                <div class="form-section-label">Enrollment Type</div>

                <div class="form-radio-group">
                    <label><input type="radio" name="type" value="group" checked> Group</label>
                    <label><input type="radio" name="type" value="private"> Private</label>
                </div>

                <div class="form-divider"></div>

                {{-- ================= DELIVERY ================= --}}
                <div class="form-section-label">Delivery Mode</div>

                <select name="mode" class="form-control-inf">
                    <option value="Offline">Offline</option>
                    <option value="Online">Online</option>
                </select>

                <div class="form-divider"></div>

                {{-- ================= GROUP ================= --}}
                <div id="group_section">

                    <div class="form-section-label">Patch</div>

                    <select id="patch_select" name="patch_option" class="form-control-inf"></select>

                    <input type="hidden" id="patch_id" name="patch_id">

                    <input type="date" id="custom_date" name="custom_date"
                           class="form-control-inf mt-2"
                           style="display:none;">
                </div>

                {{-- ================= PRIVATE ================= --}}
                <div id="private_section" style="display:none;">

                    <div class="form-section-label">Schedule</div>

                    <div class="form-grid cols-3">
                        <select id="day_select" name="day" class="form-control-inf">
                            <option value="">Select Day</option>
                            <option value="Sun">Sunday</option>
                            <option value="Mon">Monday</option>
                            <option value="Tue">Tuesday</option>
                            <option value="Wed">Wednesday</option>
                            <option value="Thu">Thursday</option>
                        </select>

                        <select id="time_slot_select" name="time_slot_id" class="form-control-inf">
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot->time_slot_id }}">{{ $slot->name }}</option>
                            @endforeach
                        </select>

                        <select id="teacher_select" name="teacher_id" class="form-control-inf"></select>
                    </div>

                    <input type="date" id="recommended_date" name="recommended_date"
                           class="form-control-inf mt-2"
                           style="display:none;">

                    <div class="form-section-label mt-3">Bundles</div>

                    <select id="bundle_select" name="bundle_id" class="form-control-inf">
                        <option value="">Select Bundle</option>
                        @foreach($bundles as $b)
                            <option value="{{ $b->bundle_id }}" data-price="{{ $b->price }}">
                                {{ $b->hours }} hrs - {{ $b->price }} LE
                            </option>
                        @endforeach
                    </select>

                </div>

                <div class="form-divider"></div>

                {{-- ================= PRICING ================= --}}
                <div class="form-section-label">Pricing</div>

                <div class="form-grid cols-3">

                    <input id="base_price" readonly class="form-control-inf" placeholder="Base Price">

                    <input id="discount" name="discount_value" value="0"
                           class="form-control-inf" placeholder="Discount">

                    <input id="final_price" readonly class="form-control-inf" placeholder="Final Price">

                </div>

                <div class="form-divider"></div>

                {{-- ================= TEST ================= --}}
                <div class="form-section-label">Placement Test</div>

                <div class="form-grid cols-2">
                    <input name="test_score" placeholder="Score" class="form-control-inf">
                    <input name="test_fee" placeholder="Fee" class="form-control-inf">
                </div>

                <div class="form-divider"></div>

                {{-- ================= MATERIALS ================= --}}
                <div class="form-section-label">Materials</div>
                <div id="materials_container" class="form-grid cols-3"></div>

                <div class="form-divider"></div>
                {{-- ================= NOTES ================= --}}
                <div class="form-section-label">Notes</div>
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

{{-- JS --}}
<script src="{{ asset('js/register/register-modal.js') }}"></script>

@endsection