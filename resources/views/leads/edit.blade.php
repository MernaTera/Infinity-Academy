@extends('layouts.leads')

@section('title', 'Edit Lead')

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

    /* ── HEADER ── */
    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 32px; padding-bottom: 22px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap; gap: 16px;
    }
    .page-eyebrow { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title   { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; }
    .page-title-sub { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 14px; color: #7A8A9A; margin-top: 4px; }
    .header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .lead-id-badge {
        font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #AAB8C8;
        padding: 6px 14px; border: 1px solid rgba(27,79,168,0.1); border-radius: 4px;
        background: rgba(255,255,255,0.6);
    }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.2); border-radius: 4px;
        color: #7A8A9A; font-size: 10px; letter-spacing: 3px;
        text-transform: uppercase; text-decoration: none; transition: all 0.3s;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-back:hover { border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    /* ── FORM CARD ── */
    .form-card {
        max-width: 860px;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 8px; overflow: hidden; position: relative;
        box-shadow: 0 4px 24px rgba(27,79,168,0.07);
        margin: 0 auto;
    }
    .form-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #F5911E, #1B4FA8, transparent);
    }

    /* ── META STRIP ── */
    .lead-meta-strip {
        display: flex; gap: 24px; flex-wrap: wrap;
        padding: 14px 24px;
        background: rgba(27,79,168,0.02);
        border-bottom: 1px solid rgba(27,79,168,0.07);
    }
    .meta-item { display: flex; flex-direction: column; gap: 3px; }
    .meta-item-label { font-size: 9px; letter-spacing: 3px; text-transform: uppercase; color: #AAB8C8; }
    .meta-item-value { font-size: 12px; color: #4A5A7A; }
    .meta-item-value.hl-blue   { color: #1B4FA8; font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 2px; }
    .meta-item-value.hl-orange { color: #F5911E; font-weight: 500; font-size: 12px; }

    /* ── FORM BODY ── */
    .form-card-body { padding: 28px 32px 32px; }

    .form-section-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 16px; padding-bottom: 9px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
    }

    /* ── GRIDS ── */
    .form-grid        { display: grid; grid-template-columns: 1fr 1fr;       gap: 16px 20px; margin-bottom: 20px; }
    .form-grid.cols-1 { grid-template-columns: 1fr; }
    .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    .form-grid.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }

    @media (max-width: 680px) {
        .form-grid, .form-grid.cols-3, .form-grid.cols-4 { grid-template-columns: 1fr; }
        .create-page { padding: 18px 14px; }
        .form-card-body { padding: 18px; }
        .lead-meta-strip { gap: 14px;}
    }

    .form-field { display: flex; flex-direction: column; }

    .form-label {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; margin-bottom: 6px; font-weight: 500;
    }
    .form-label .req { color: #F5911E; margin-left: 2px; }

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

    select.form-control-inf {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 11px center;
        padding-right: 32px; cursor: pointer;
        background-color: rgba(255,255,255,0.92);
    }
    select.form-control-inf option { background: #fff; color: #1A2A4A; }
    textarea.form-control-inf { resize: vertical; min-height: 88px; }

    .form-error { font-size: 10px; color: #DC2626; margin-top: 4px; }

    .form-divider { height: 1px; background: rgba(27,79,168,0.06); margin: 22px 0; }

    /* ── TOGGLE (is_active) ── */
    .toggle-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        background: rgba(27,79,168,0.02);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 4px;
    }
    .toggle-label-text { font-size: 13px; color: #1A2A4A; }
    .toggle-sub { font-size: 10px; color: #AAB8C8; margin-top: 1px; }

    .toggle-switch {
        position: relative; width: 40px; height: 22px; flex-shrink: 0;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0; cursor: pointer;
        background: #E2E8F0; border-radius: 22px;
        transition: background 0.3s;
    }
    .toggle-slider::before {
        content: ''; position: absolute;
        width: 16px; height: 16px; left: 3px; top: 3px;
        background: #fff; border-radius: 50%;
        transition: transform 0.3s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    .toggle-switch input:checked + .toggle-slider { background: #1B4FA8; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(18px); }

    /* ── FOOTER ── */
    .form-footer {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; padding-top: 20px;
        border-top: 1px solid rgba(27,79,168,0.07);
        flex-wrap: wrap;
    }
    .footer-left  { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #AAB8C8; }
    .footer-right { display: flex; align-items: center; gap: 10px; }

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
</style>

<div class="create-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">CRM — Edit Lead</div>
            <h1 class="page-title">Edit Lead</h1>
            <p class="page-title-sub">{{ $lead->full_name }}</p>
        </div>
        <div class="header-right">
            <span class="lead-id-badge"># {{ $lead->lead_id }}</span>
            <a href="{{ route('leads.index') }}" class="btn-back">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    {{-- ── CARD ── --}}
    <div class="form-card">

        {{-- Meta strip --}}
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
                <span class="meta-item-label">Lead Age</span>
                <span class="meta-item-value hl-blue">{{ intval(abs($lead->created_at->diffInHours(now())) / 24) }} d</span>
            </div>
            <div class="meta-item">
                <span class="meta-item-label">Status</span>
                <span class="meta-item-value hl-orange">{{ str_replace('_',' ',$lead->status) }}</span>
            </div>
            @if($lead->next_call_at)
            <div class="meta-item">
                <span class="meta-item-label">Next Call</span>
                <span class="meta-item-value">{{ $lead->next_call_at->format('d M Y, H:i') }}</span>
            </div>
            @endif
            <div class="meta-item">
                <span class="meta-item-label">Active</span>
                <span class="meta-item-value" style="color:{{ $lead->is_active ? '#15803D' : '#DC2626' }}">
                    {{ $lead->is_active ? 'Yes' : 'No' }}
                </span>
            </div>
        </div>

        <div class="form-card-body">
            <form method="POST" action="{{ route('leads.update', $lead->lead_id) }}">
                @csrf
                @method('PUT')

                {{-- ══ 1. BASIC INFO ══ --}}
                <div class="form-section-label">Basic Information</div>
                <div class="form-grid">

                    <div class="form-field">
                        <label class="form-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="full_name" class="form-control-inf"
                               placeholder="e.g. Ahmed Mohamed"
                               value="{{ old('full_name', $lead->full_name) }}" required>
                        @error('full_name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Phone <span class="req">*</span></label>
                        <input type="text" name="phone" class="form-control-inf"
                               placeholder="e.g. 01012345678"
                               value="{{ old('phone', $lead->phone) }}" required>
                        @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control-inf"
                               style="color-scheme:light;"
                               value="{{ old('birthdate', $lead->birthdate ? \Carbon\Carbon::parse($lead->birthdate)->format('Y-m-d') : '') }}">
                        @error('birthdate')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control-inf"
                               placeholder="e.g. Cairo"
                               value="{{ old('location', $lead->location) }}">
                        @error('location')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Degree <span class="req">*</span></label>
                        <select name="degree" class="form-control-inf" required>
                            @foreach(['Student','Graduate'] as $d)
                                <option value="{{ $d }}" {{ old('degree',$lead->degree)===$d?'selected':'' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                        @error('degree')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Source <span class="req">*</span></label>
                        <select name="source" class="form-control-inf" required>
                            @foreach(['Facebook','Website','Friend','Walk_In','Google','Other'] as $src)
                                <option value="{{ $src }}" {{ old('source',$lead->source)===$src?'selected':'' }}>
                                    {{ str_replace('_',' ',$src) }}
                                </option>
                            @endforeach
                        </select>
                        @error('source')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                </div>

                <div class="form-divider"></div>

                {{-- ══ 2. COURSE & LEVEL ══ --}}
                <div class="form-section-label">Course & Level</div>
                <div class="form-grid cols-3">

                    <div class="form-field">
                        <label class="form-label">Course</label>
                        <select name="interested_course_template_id" class="form-control-inf">
                            <option value="">— Select —</option>
                            @foreach($courses ?? [] as $ct)
                                <option value="{{ $ct->course_template_id }}"
                                    {{ old('interested_course_template_id',$lead->interested_course_template_id)==$ct->course_template_id?'selected':'' }}>
                                    {{ $ct->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('interested_course_template_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Level</label>
                        <select name="interested_level_id" class="form-control-inf">
                            <option value="">— Select —</option>
                            @foreach($levels ?? [] as $lv)
                                <option value="{{ $lv->level_id }}"
                                    {{ old('interested_level_id',$lead->interested_level_id)==$lv->level_id?'selected':'' }}>
                                    {{ $lv->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('interested_level_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Sublevel</label>
                        <select name="interested_sublevel_id" class="form-control-inf">
                            <option value="">— Select —</option>
                            @foreach($sublevels ?? [] as $sl)
                                <option value="{{ $sl->sublevel_id }}"
                                    {{ old('interested_sublevel_id',$lead->interested_sublevel_id)==$sl->sublevel_id?'selected':'' }}>
                                    {{ $sl->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('interested_sublevel_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                </div>

                <div class="form-divider"></div>

                {{-- ══ 3. FOLLOW-UP ══ --}}
                <div class="form-section-label">Follow-Up Details</div>
                <div class="form-grid cols-3">

                    <div class="form-field">
                        <label class="form-label">Status <span class="req">*</span></label>
                        <select name="status" class="form-control-inf" required>
                            @foreach(['Waiting','Call_Again'] as $s)
                                <option value="{{ $s }}" {{ old('status',$lead->status)===$s?'selected':'' }}>
                                    {{ str_replace('_',' ',$s) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Next Call At</label>
                        <input type="datetime-local" name="next_call_at" class="form-control-inf"
                               style="color-scheme:light;"
                               value="{{ old('next_call_at', $lead->next_call_at ? $lead->next_call_at->format('Y-m-d\TH:i') : '') }}">
                        @error('next_call_at')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Start Preference</label>
                        <select name="start_preference_type" class="form-control-inf">
                            <option value="">— Select —</option>
                            @foreach(['Current Patch','Next Patch','Specific Date'] as $pref)
                                <option value="{{ $pref }}" {{ old('start_preference_type',$lead->start_preference_type)===$pref?'selected':'' }}>
                                    {{ $pref }}
                                </option>
                            @endforeach
                        </select>
                        @error('start_preference_type')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field" id="specific_date_field" style="display:none;">
                        <label class="form-label">Specific Date</label>
                        <input type="datetime-local" name="start_preference_date" class="form-control-inf"
                            value="{{ old('start_preference_date', $lead->start_preference_date ? $lead->start_preference_date->format('Y-m-d\TH:i') : '') }}">
                    </div>

                </div>

                <div class="form-divider"></div>

                {{-- ══ 4. NOTES & ACTIVE ══ --}}
                <div class="form-section-label">Notes & Settings</div>

                <div class="form-grid cols-1" style="margin-bottom:16px;">
                    <div class="form-field">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control-inf"
                                  placeholder="Any additional info...">{{ old('notes', $lead->notes) }}</textarea>
                        @error('notes')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- is_active toggle --}}
                <div class="toggle-row">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $lead->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <div>
                        <div class="toggle-label-text">Active Lead</div>
                        <div class="toggle-sub">Inactive leads won't appear in the main pipeline</div>
                    </div>
                </div>

                {{-- ══ FOOTER ══ --}}
                <div class="form-footer" style="margin-top:24px;">
                    <div class="footer-left">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
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

<script>
    const prefSelect = document.querySelector('[name="start_preference_type"]');
    const dateField = document.getElementById('specific_date_field');

    function toggleDateField() {
        if (prefSelect.value === 'Specific Date') {
            dateField.style.display = 'block';
        } else {
            dateField.style.display = 'none';
        }
    }

    toggleDateField();
    prefSelect.addEventListener('change', toggleDateField);
</script>

@endsection