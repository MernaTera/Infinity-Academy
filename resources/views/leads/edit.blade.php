@extends('layouts.leads')

@section('title', 'Edit Lead')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
        --green:#059669; --green-dk:#15803D; --purple:#7C3AED; --red:#DC2626;
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --glass-bd:rgba(255,255,255,0.7);
    }
    * { box-sizing:border-box; }

    .create-page {
        position:relative; min-height:100vh; overflow:hidden;
        padding:44px 34px 60px; color:var(--text); font-family:'DM Sans',sans-serif;
        background:#F8F6F2;
    }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:-80px; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .page-header {
        position:relative; z-index:1;
        display:flex; align-items:flex-end; justify-content:space-between;
        max-width:920px; margin:0 auto 22px; flex-wrap:wrap; gap:16px;
    }
    .page-eyebrow { font-size:12px; letter-spacing:4px; text-transform:uppercase; color:var(--orange-dk); margin-bottom:8px; font-weight:600; }
    .page-title   { font-family:'Bebas Neue',sans-serif; font-size:46px; letter-spacing:2px; color:var(--text); line-height:0.92; margin:0; }
    .page-title-sub { font-family:'Cormorant Garamond',serif; font-style:italic; font-size:17px; color:var(--muted); margin-top:6px; }
    .header-right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }

    .lead-id-badge {
        font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--muted);
        padding:9px 15px; border:1px solid var(--glass-bd); border-radius:12px;
        background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(12px); backdrop-filter:blur(12px);
        box-shadow:0 6px 18px rgba(23,45,90,0.06);
    }

    .btn-back {
        display:inline-flex; align-items:center; gap:8px; padding:11px 18px;
        background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(14px) saturate(150%); backdrop-filter:blur(14px) saturate(150%);
        border:1px solid var(--glass-bd); border-radius:14px;
        color:var(--muted); font-size:10px; letter-spacing:2px; text-transform:uppercase; font-weight:600;
        text-decoration:none; transition:color .25s, border-color .25s, background .25s; font-family:'DM Sans',sans-serif;
        box-shadow:0 6px 18px rgba(23,45,90,0.06);
    }
    .btn-back:hover { border-color:var(--blue); color:var(--blue); background:rgba(255,255,255,0.7); text-decoration:none; }

    .form-card {
        position:relative; z-index:1; max-width:920px; margin:0 auto;
        background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(30px) saturate(180%); backdrop-filter:blur(30px) saturate(180%);
        border:1px solid var(--glass-bd); border-radius:28px; overflow:hidden;
        box-shadow:0 30px 70px -22px rgba(23,45,90,0.28), inset 0 1px 0 rgba(255,255,255,0.7);
    }
    .form-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:4px;
        background:linear-gradient(90deg, var(--orange), var(--blue), var(--purple));
    }

    .lead-meta-strip {
        display:flex; gap:28px; flex-wrap:wrap;
        padding:18px 30px; background:rgba(255,255,255,0.28);
        border-bottom:1px solid rgba(255,255,255,0.5);
    }
    .meta-item { display:flex; flex-direction:column; gap:3px; }
    .meta-item-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); font-weight:600; }
    .meta-item-value { font-size:12px; color:var(--muted); }
    .meta-item-value.hl-blue   { color:var(--blue); font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; }
    .meta-item-value.hl-orange { color:var(--orange-dk); font-weight:600; font-size:12px; }

    .form-card-body { padding:32px 38px 36px; }
    @media (max-width:680px){ .create-page{ padding:22px 14px 40px; } .form-card-body{ padding:22px; } .lead-meta-strip{ gap:18px; padding:16px 20px; } .page-title{ font-size:38px; } }

    .form-section-label {
        display:flex; align-items:center; gap:10px;
        font-size:11px; letter-spacing:3px; text-transform:uppercase;
        color:var(--text); font-weight:700; margin-bottom:18px;
    }
    .form-section-label::before {
        content:''; width:20px; height:3px; border-radius:3px;
        background:linear-gradient(90deg, var(--orange), var(--blue));
    }

    .form-grid        { display:grid; grid-template-columns:1fr 1fr; gap:18px 20px; margin-bottom:22px; }
    .form-grid.cols-1 { grid-template-columns:1fr; }
    .form-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
    .form-grid.cols-4 { grid-template-columns:1fr 1fr 1fr 1fr; }
    @media (max-width:680px){ .form-grid, .form-grid.cols-3, .form-grid.cols-4 { grid-template-columns:1fr; } }

    .form-field { display:flex; flex-direction:column; }
    .form-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:8px; font-weight:600; }
    .form-label .req, .form-label .required { color:var(--orange); margin-left:2px; }

    .form-control-inf {
        width:100%; padding:13px 15px;
        background:rgba(255,255,255,0.55); border:1px solid rgba(27,79,168,0.1); border-radius:14px;
        box-shadow:inset 0 1px 2px rgba(255,255,255,0.85);
        color:var(--text); font-family:'DM Sans',sans-serif; font-size:13px; font-weight:400; outline:none;
        transition:border-color .25s, box-shadow .25s, background .25s;
        appearance:none; -webkit-appearance:none;
    }
    .form-control-inf::placeholder { color:var(--faint); }
    .form-control-inf:focus { border-color:var(--blue); background:rgba(255,255,255,0.85); box-shadow:0 0 0 4px rgba(27,79,168,0.12), inset 0 1px 2px rgba(255,255,255,0.85); }

    select.form-control-inf {
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%235A6A85'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 13px center;
        padding-right:36px; cursor:pointer;
    }
    select.form-control-inf option { background:#fff; color:var(--text); }
    textarea.form-control-inf { resize:vertical; min-height:96px; }

    .form-error { font-size:10px; color:var(--red); margin-top:5px; }
    .form-divider { height:1px; background:linear-gradient(90deg, transparent, rgba(27,79,168,0.12), transparent); margin:26px 0; }

    .toggle-row {
        display:flex; align-items:center; gap:12px; padding:15px 18px;
        background:rgba(255,255,255,0.4); border:1px solid var(--glass-bd); border-radius:16px;
        box-shadow:inset 0 1px 2px rgba(255,255,255,0.7);
    }
    .toggle-label-text { font-size:13px; color:var(--text); font-weight:600; }
    .toggle-sub { font-size:10px; color:var(--faint); margin-top:1px; }
    .toggle-switch { position:relative; width:42px; height:23px; flex-shrink:0; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; inset:0; cursor:pointer; background:rgba(122,138,154,0.35); border-radius:23px; transition:background 0.3s; }
    .toggle-slider::before { content:''; position:absolute; width:17px; height:17px; left:3px; top:3px; background:#fff; border-radius:50%; transition:left 0.3s; box-shadow:0 1px 4px rgba(0,0,0,0.18); }
    .toggle-switch input:checked + .toggle-slider { background:linear-gradient(120deg, var(--blue), var(--blue-2)); }
    .toggle-switch input:checked + .toggle-slider::before { left:22px; }

    .form-footer {
        display:flex; align-items:center; justify-content:space-between;
        gap:12px; padding-top:24px; border-top:1px solid rgba(27,79,168,0.08); flex-wrap:wrap;
    }
    .footer-left  { display:flex; align-items:center; gap:6px; font-size:11px; color:var(--faint); }
    .footer-right { display:flex; align-items:center; gap:10px; }

    .btn-cancel {
        padding:12px 24px; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px);
        border:1px solid var(--glass-bd); border-radius:13px;
        color:var(--muted); font-family:'DM Sans',sans-serif; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-weight:600;
        text-decoration:none; transition:color .25s, border-color .25s; cursor:pointer;
    }
    .btn-cancel:hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }

    .btn-submit {
        display:inline-flex; align-items:center; gap:9px; padding:13px 32px;
        background:linear-gradient(120deg, var(--blue), var(--blue-2)); border:none; border-radius:13px;
        color:#fff; font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:3px;
        cursor:pointer; box-shadow:0 12px 28px rgba(27,79,168,0.36); transition:box-shadow .3s, filter .3s;
    }
    .btn-submit:hover { box-shadow:0 18px 40px rgba(27,79,168,0.46); filter:brightness(1.07); }
    .btn-submit span, .btn-submit svg { position:relative; z-index:1; }
</style>

<div class="create-page">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

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

    <div class="form-card">

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

                <div class="form-section-label">Course & Level</div>
                <div class="form-grid cols-3">

                    <div class="form-field">
                        <label class="form-label">Course</label>
                        <select name="interested_course_template_id"
                                class="form-control-inf"
                                id="course_select">
                            <option value="">— Select —</option>
                            @foreach($courses ?? [] as $ct)
                                <option value="{{ $ct->course_template_id }}"
                                    {{ old('interested_course_template_id', $lead->interested_course_template_id) == $ct->course_template_id ? 'selected' : '' }}>
                                    {{ $ct->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('interested_course_template_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Level</label>
                        <select name="interested_level_id"
                                class="form-control-inf"
                                id="level_select"
                                data-selected="{{ old('interested_level_id', $lead->interested_level_id) }}">
                            <option value="">— Select —</option>
                            @foreach($levels ?? [] as $lv)
                                <option value="{{ $lv->level_id }}"
                                    {{ old('interested_level_id', $lead->interested_level_id) == $lv->level_id ? 'selected' : '' }}>
                                    {{ $lv->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('interested_level_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label">Sublevel</label>
                        <select name="interested_sublevel_id"
                                class="form-control-inf"
                                id="sublevel_select"
                                data-selected="{{ old('interested_sublevel_id', $lead->interested_sublevel_id) }}">
                            <option value="">— Select —</option>
                            @foreach($sublevels ?? [] as $sl)
                                <option value="{{ $sl->sublevel_id }}"
                                    {{ old('interested_sublevel_id', $lead->interested_sublevel_id) == $sl->sublevel_id ? 'selected' : '' }}>
                                    {{ $sl->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('interested_sublevel_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                </div>

                <div class="form-divider"></div>

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

                <div class="form-section-label">Notes & Settings</div>

                <div class="form-grid cols-1" style="margin-bottom:16px;">
                    <div class="form-field">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control-inf"
                                  placeholder="Any additional info...">{{ old('notes', $lead->notes) }}</textarea>
                        @error('notes')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

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

                <div class="form-footer" style="margin-top:24px;">
                    <div class="footer-left">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="1.5">
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

<script src="{{ asset('js/leads/create-modal.js') }}"></script>
@endsection
