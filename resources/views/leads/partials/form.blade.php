<script src="{{ asset('js/leads/history-modal.js') }}"></script>

<form method="POST"
      action="{{ isset($isRegistration) ? route('registration.store') : (isset($lead) ? route('leads.update', $lead->lead_id) : route('leads.store')) }}">
    @csrf
    @if(isset($lead)) @method('PUT') @endif

    {{-- ══ 1. BASIC INFO ══ --}}
    <div class="form-section-label">Basic Information</div>

    <div class="form-grid">

        {{-- Full Name --}}
        <div class="form-field">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" name="full_name"
                   class="form-control-inf @error('full_name') is-error @enderror"
                   placeholder="e.g. Ahmed Mohamed"
                   value="{{ old('full_name', $lead->full_name ?? '') }}" required>
            @error('full_name')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="form-field">
            <label class="form-label">Phone <span class="required">*</span></label>
            <input type="text" name="phone"
                   class="form-control-inf @error('phone') is-error @enderror"
                   placeholder="e.g. 01012345678"
                   value="{{ old('phone', $lead->phone ?? '') }}"
                   maxlength="15" required>
            @error('phone')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @else
                <div class="field-msg field-msg--hint">Numbers only · 11–15 digits</div>
            @enderror
        </div>

        {{-- Birthdate --}}
        <div class="form-field">
            <label class="form-label">Birthdate</label>
            <input type="date" name="birthdate"
                   class="form-control-inf @error('birthdate') is-error @enderror"
                   style="color-scheme:light;"
                   value="{{ old('birthdate', isset($lead->birthdate) ? \Carbon\Carbon::parse($lead->birthdate)->format('Y-m-d') : '') }}">
            @error('birthdate')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Location --}}
        <div class="form-field">
            <label class="form-label">Location</label>
            <input type="text" name="location"
                   class="form-control-inf @error('location') is-error @enderror"
                   placeholder="e.g. Cairo"
                   value="{{ old('location', $lead->location ?? '') }}">
            @error('location')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Degree --}}
        <div class="form-field">
            <label class="form-label">Degree <span class="required">*</span></label>
            <select name="degree" class="form-control-inf @error('degree') is-error @enderror" required>
                <option value="">— Select —</option>
                @foreach(['Student','Graduate'] as $deg)
                    <option value="{{ $deg }}" {{ old('degree', $lead->degree ?? '') === $deg ? 'selected' : '' }}>
                        {{ $deg }}
                    </option>
                @endforeach
            </select>
            @error('degree')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Source --}}
        <div class="form-field">
            <label class="form-label">Lead Source <span class="required">*</span></label>
            <select name="source" class="form-control-inf @error('source') is-error @enderror" required>
                <option value="">— Select —</option>
                @foreach(['Facebook','Website','Friend','Walk_In','Google','Other'] as $src)
                    <option value="{{ $src }}" {{ old('source', $lead->source ?? '') === $src ? 'selected' : '' }}>
                        {{ str_replace('_',' ',$src) }}
                    </option>
                @endforeach
            </select>
            @error('source')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

    </div>

    <div class="form-divider"></div>

    {{-- ══ 2. COURSE & LEVEL ══ --}}
    <div class="form-section-label">Course & Level</div>

    <div class="form-grid cols-3">

        <div class="form-field">
            <label class="form-label">Course</label>
            <select name="interested_course_template_id"
                    class="form-control-inf @error('interested_course_template_id') is-error @enderror"
                    id="course_select">
                <option value="">— Select Course —</option>
                @foreach($courses ?? [] as $course)
                    <option value="{{ $course->course_template_id }}"
                        {{ old('interested_course_template_id', $lead->interested_course_template_id ?? '') == $course->course_template_id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            @error('interested_course_template_id')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-field">
            <label class="form-label">Level</label>
            <select name="interested_level_id"
                    class="form-control-inf @error('interested_level_id') is-error @enderror"
                    id="level_select">
                <option value="">— Select Level —</option>
                @foreach($levels ?? [] as $level)
                    <option value="{{ $level->level_id }}"
                        {{ old('interested_level_id', $lead->interested_level_id ?? '') == $level->level_id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
            @error('interested_level_id')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-field">
            <label class="form-label">Sublevel</label>
            <select name="interested_sublevel_id"
                    class="form-control-inf @error('interested_sublevel_id') is-error @enderror"
                    id="sublevel_select">
                <option value="">— Select Sublevel —</option>
                @foreach($sublevels ?? [] as $sublevel)
                    <option value="{{ $sublevel->sublevel_id }}"
                        {{ old('interested_sublevel_id', $lead->interested_sublevel_id ?? '') == $sublevel->sublevel_id ? 'selected' : '' }}>
                        {{ $sublevel->name }}
                    </option>
                @endforeach
            </select>
            @error('interested_sublevel_id')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

    </div>

    <div class="form-divider"></div>

    {{-- ══ 3. FOLLOW-UP ══ --}}
    <div class="form-section-label">Follow-Up Details</div>

    <div class="form-grid cols-3">

        {{-- Status --}}
        <div class="form-field">
            <label class="form-label">Status <span class="required">*</span></label>
            <select name="status" class="form-control-inf @error('status') is-error @enderror" required>
                <option value="">— Select —</option>
                @foreach(['Waiting','Call_Again'] as $s)
                    <option value="{{ $s }}"
                        {{ old('status', $lead->status ?? 'Waiting') === $s ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $s) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Next Call --}}
        <div class="form-field">
            <label class="form-label">Next Call At</label>
            <input type="datetime-local" name="next_call_at"
                   class="form-control-inf @error('next_call_at') is-error @enderror"
                   style="color-scheme:light;"
                   value="{{ old('next_call_at', isset($lead->next_call_at) ? $lead->next_call_at->format('Y-m-d\TH:i') : '') }}">
            @error('next_call_at')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Start Preference --}}
        <div class="form-field">
            <label class="form-label">Start Preference</label>
            <select name="start_preference_type"
                    class="form-control-inf @error('start_preference_type') is-error @enderror">
                <option value="">— Select —</option>
                @foreach(['Current Patch','Next Patch','Specific Date'] as $pref)
                    <option value="{{ $pref }}"
                        {{ old('start_preference_type', $lead->start_preference_type ?? '') === $pref ? 'selected' : '' }}>
                        {{ $pref }}
                    </option>
                @endforeach
            </select>
            @error('start_preference_type')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Specific Date (hidden until selected) --}}
        <div class="form-field" id="specific_date_field" style="display:none;">
            <label class="form-label">
                Specific Date <span class="required">*</span>
            </label>
            <input type="date" name="start_preference_date"
                   class="form-control-inf @error('start_preference_date') is-error @enderror"
                   style="color-scheme:light;"
                   value="{{ old('start_preference_date', isset($lead->start_preference_date) ? \Carbon\Carbon::parse($lead->start_preference_date)->format('Y-m-d') : '') }}">
            @error('start_preference_date')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

    </div>

    <div class="form-divider"></div>

    {{-- ══ 4. NOTES ══ --}}
    <div class="form-section-label">Notes</div>

    <div class="form-grid cols-1">
        <div class="form-field">
            <label class="form-label">Notes</label>
            <textarea name="notes"
                      class="form-control-inf @error('notes') is-error @enderror"
                      placeholder="Any additional info about this lead..."
                      maxlength="2000">{{ old('notes', $lead->notes ?? '') }}</textarea>
            @error('notes')
                <div class="field-msg field-msg--error">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="form-footer">
        <a href="{{ route('leads.index') }}" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-submit">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <span>{{ isset($lead) ? 'Update Lead' : 'Save Lead' }}</span>
        </button>
    </div>

</form>

{{-- ── Error + hint styles (scoped here so they work inside any layout) ── --}}
<style>
    .form-control-inf.is-error {
        border-color: #DC2626 !important;
        background: rgba(220,38,38,0.02) !important;
    }
    .form-control-inf.is-error:focus {
        box-shadow: 0 0 0 3px rgba(220,38,38,0.10) !important;
    }

    .field-msg {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
        font-size: 11px;
        letter-spacing: 0.2px;
        line-height: 1.4;
        animation: msgIn 0.25s ease both;
    }
    @keyframes msgIn {
        from { opacity: 0; transform: translateY(-3px); }
        to   { opacity: 1; transform: none; }
    }

    .field-msg--error { color: #DC2626; }
    .field-msg--error svg { flex-shrink: 0; color: #DC2626; }

    .field-msg--hint { color: #AAB8C8; }
    .field-msg--hint svg { flex-shrink: 0; }
</style>