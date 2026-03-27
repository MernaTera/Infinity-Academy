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

            {{-- reuse lead form 🔥 --}}
            @include('leads.partials.form')

            {{-- 🔥 ADDITIONAL REGISTRATION FIELDS --}}
            <div class="form-divider"></div>

            <div class="form-section-label">Enrollment Details</div>

            <div class="form-grid cols-3">

                <div class="form-field">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control-inf">
                        <option value="group">Group</option>
                        <option value="private">Private</option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Payment Type</label>
                    <select name="payment_type" class="form-control-inf">
                        <option value="full">Full</option>
                        <option value="installment">Installment</option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Placement Test Score</label>
                    <input type="number" name="placement_score" class="form-control-inf">
                </div>

            </div>

        </div>
    </div>

</div>

@endsection