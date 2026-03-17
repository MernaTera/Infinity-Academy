@extends('layouts.app')

@section('title', 'Edit Lead')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    .create-page {
        background: #060606;
        min-height: 100vh;
        padding: 40px 32px;
        color: #F0EDE6;
        font-family: 'DM Sans', sans-serif;
    }

    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 36px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(201,168,76,0.1);
    }

    .page-eyebrow {
        font-size: 10px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #C9A84C;
        margin-bottom: 6px;
    }

    .page-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 36px;
        letter-spacing: 4px;
        color: #F0EDE6;
        line-height: 1;
    }

    .page-title-sub {
        font-family: 'Cormorant Garamond', serif;
        font-style: italic;
        font-size: 14px;
        color: #5A5550;
        margin-top: 4px;
        letter-spacing: 1px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lead-id-badge {
        font-size: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #3A3530;
        padding: 6px 14px;
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 2px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: transparent;
        border: 1px solid rgba(201,168,76,0.2);
        border-radius: 2px;
        color: #5A5550;
        font-size: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s;
        font-family: 'DM Sans', sans-serif;
    }

    .btn-back:hover {
        border-color: rgba(201,168,76,0.5);
        color: #C9A84C;
        text-decoration: none;
    }

    .form-card {
        max-width: 760px;
        background: #0F0F0F;
        border: 1px solid rgba(201,168,76,0.12);
        border-radius: 2px;
        overflow: hidden;
        position: relative;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #C9A84C, transparent);
    }

    /* edit mode: amber top line instead of gold */
    .form-card.edit-mode::before {
        background: linear-gradient(90deg, transparent, #E8A020, transparent);
    }

    .form-card-body {
        padding: 36px 40px 40px;
    }

    /* meta strip */
    .lead-meta-strip {
        display: flex;
        gap: 24px;
        padding: 14px 20px;
        background: rgba(201,168,76,0.03);
        border-bottom: 1px solid rgba(201,168,76,0.08);
    }

    .meta-item { display: flex; flex-direction: column; gap: 3px; }
    .meta-item-label { font-size: 9px; letter-spacing: 3px; text-transform: uppercase; color: #3A3530; }
    .meta-item-value { font-size: 12px; color: #8A8580; }
    .meta-item-value.highlight { color: #C9A84C; font-family: 'Bebas Neue', sans-serif; font-size: 14px; letter-spacing: 2px; }

    /* reuse same form styles from create */
    .form-section-label {
        font-size: 9px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #C9A84C;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(201,168,76,0.08);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px 24px;
        margin-bottom: 28px;
    }

    .form-grid.cols-1 { grid-template-columns: 1fr; }
    .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }

    @media (max-width: 640px) {
        .form-grid, .form-grid.cols-3 { grid-template-columns: 1fr; }
        .lead-meta-strip { flex-wrap: wrap; gap: 14px; }
    }

    .form-field { display: flex; flex-direction: column; }

    .form-label {
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #5A5550;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-label .required { color: #C9A84C; margin-left: 3px; }

    .form-control-inf {
        width: 100%;
        padding: 12px 14px;
        background: #161616;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 2px;
        color: #F0EDE6;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 300;
        outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none;
        -webkit-appearance: none;
    }

    .form-control-inf::placeholder { color: #3A3530; }

    .form-control-inf:focus {
        border-color: #C9A84C;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.08);
    }

    select.form-control-inf {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%235A5550'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
        cursor: pointer;
    }

    select.form-control-inf option {
        background: #161616;
        color: #F0EDE6;
    }

    textarea.form-control-inf {
        resize: vertical;
        min-height: 90px;
    }

    .form-error {
        font-size: 10px;
        color: #F87171;
        margin-top: 5px;
        letter-spacing: 0.3px;
    }

    .form-divider {
        height: 1px;
        background: rgba(255,255,255,0.04);
        margin: 28px 0;
    }

    .form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    .footer-left {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        letter-spacing: 1px;
        color: #3A3530;
    }

    .footer-left svg { opacity: 0.4; }

    .footer-right { display: flex; align-items: center; gap: 12px; }

    .btn-cancel {
        padding: 11px 24px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 2px;
        color: #5A5550;
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s;
        cursor: pointer;
    }

    .btn-cancel:hover {
        border-color: rgba(255,255,255,0.15);
        color: #9A9590;
        text-decoration: none;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 32px;
        background: transparent;
        border: 1px solid #C9A84C;
        border-radius: 2px;
        color: #C9A84C;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 14px;
        letter-spacing: 4px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: color 0.4s;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: #C9A84C;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }

    .btn-submit:hover::before { transform: scaleX(1); }
    .btn-submit:hover { color: #060606; }
    .btn-submit span, .btn-submit svg { position: relative; z-index: 1; }
</style>

<div class="create-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">CRM Module — Edit</div>
            <h1 class="page-title">Edit Lead</h1>
            <p class="page-title-sub">{{ $lead->full_name }}</p>
        </div>

        <div class="header-right">
            <span class="lead-id-badge"># {{ $lead->lead_id }}</span>
            <a href="{{ route('leads.index') }}" class="btn-back">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Leads
            </a>
        </div>
    </div>

    {{-- ── FORM CARD ── --}}
    <div class="form-card edit-mode">

        {{-- Meta strip ── --}}
        <div class="lead-meta-strip">
            <div class="meta-item">
                <span class="meta-item-label">Created</span>
                <span class="meta-item-value">{{ $lead->created_at->format('d M Y') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-item-label">Last Updated</span>
                <span class="meta-item-value">{{ $lead->updated_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-item-label">Age</span>
                <span class="meta-item-value highlight">{{ $lead->created_at->diffInDays(now()) }} Days</span>
            </div>
            @if($lead->next_call_at)
            <div class="meta-item">
                <span class="meta-item-label">Next Call</span>
                <span class="meta-item-value">{{ $lead->next_call_at->format('d M Y, H:i') }}</span>
            </div>
            @endif
        </div>

        <div class="form-card-body">

            <form method="POST" action="{{ route('leads.update', $lead->lead_id) }}">
                @csrf
                @method('PUT')

                {{-- ── SECTION: Basic Info ── --}}
                <div class="form-section-label">Basic Information</div>

                <div class="form-grid">
                    {{-- Full Name --}}
                    <div class="form-field">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text"
                               name="full_name"
                               class="form-control-inf"
                               placeholder="e.g. Ahmed Mohamed"
                               value="{{ old('full_name', $lead->full_name) }}"
                               required>
                        @error('full_name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="form-field">
                        <label class="form-label">Phone <span class="required">*</span></label>
                        <input type="text"
                               name="phone"
                               class="form-control-inf"
                               placeholder="e.g. 01012345678"
                               value="{{ old('phone', $lead->phone) }}"
                               required>
                        @error('phone')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-field">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control-inf"
                               placeholder="name@example.com"
                               value="{{ old('email', $lead->email) }}">
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Course --}}
                    <div class="form-field">
                        <label class="form-label">Course</label>
                        <select name="course_template_id" class="form-control-inf">
                            <option value="">— Select Course —</option>
                            @foreach($courseTemplates ?? [] as $course)
                                <option value="{{ $course->id }}"
                                    {{ old('course_template_id', $lead->course_template_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_template_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- ── SECTION: Follow-Up ── --}}
                <div class="form-section-label">Follow-Up Details</div>

                <div class="form-grid cols-3">
                    {{-- Status --}}
                    <div class="form-field">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" class="form-control-inf" required>
                            <option value="">— Select —</option>
                            @foreach(['Waiting','Call_Again','Scheduled_Call','Registered','Not_Interested'] as $s)
                                <option value="{{ $s }}"
                                    {{ old('status', $lead->status) === $s ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $s) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Next Call --}}
                    <div class="form-field">
                        <label class="form-label">Next Call At</label>
                        <input type="datetime-local"
                               name="next_call_at"
                               class="form-control-inf"
                               value="{{ old('next_call_at', $lead->next_call_at ? $lead->next_call_at->format('Y-m-d\TH:i') : '') }}"
                               style="color-scheme: dark;">
                        @error('next_call_at')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Source --}}
                    <div class="form-field">
                        <label class="form-label">Lead Source</label>
                        <select name="source" class="form-control-inf">
                            <option value="">— Select —</option>
                            @foreach(['Facebook','Instagram','WhatsApp','Website','Referral','Other'] as $src)
                                <option value="{{ $src }}"
                                    {{ old('source', $lead->source) === $src ? 'selected' : '' }}>
                                    {{ $src }}
                                </option>
                            @endforeach
                        </select>
                        @error('source')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- ── SECTION: Notes ── --}}
                <div class="form-section-label">Notes</div>

                <div class="form-grid cols-1">
                    <div class="form-field">
                        <label class="form-label">Notes</label>
                        <textarea name="notes"
                                  class="form-control-inf"
                                  placeholder="Any additional info about this lead...">{{ old('notes', $lead->notes) }}</textarea>
                        @error('notes')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ── FOOTER ── --}}
                <div class="form-footer">
                    <div class="footer-left">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4l3 3"/>
                        </svg>
                        Last saved {{ $lead->updated_at->diffForHumans() }}
                    </div>

                    <div class="footer-right">
                        <a href="{{ route('leads.index') }}" class="btn-cancel">Cancel</a>

                        <button type="submit" class="btn-submit">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            <span>Update Lead</span>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection