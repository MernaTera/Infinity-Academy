
<form method="POST"
      action="{{ isset($lead) ? route('leads.update', $lead->lead_id) : route('leads.store') }}">
    @csrf
    @if(isset($lead)) @method('PUT') @endif

    @if ($errors->any())
        <div style="padding:12px 14px;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);border-radius:4px;margin-bottom:20px;">
            @foreach ($errors->all() as $error)
                <p style="font-size:12px;color:#DC2626;margin:2px 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- ══ 1. BASIC INFO ══ --}}
    <div class="form-section-label">Basic Information</div>

    <div class="form-grid">

        <div class="form-field">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" name="full_name" class="form-control-inf"
                   placeholder="e.g. Ahmed Mohamed"
                   value="{{ old('full_name', $lead->full_name ?? '') }}" required>
            @error('full_name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label class="form-label">Phone <span class="required">*</span></label>
            <input type="text" name="phone" class="form-control-inf"
                   placeholder="e.g. 01012345678"
                   value="{{ old('phone', $lead->phone ?? '') }}" required>
            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label class="form-label">Birthdate</label>
            <input type="date" name="birthdate" class="form-control-inf"
                   style="color-scheme:light;"
                   value="{{ old('birthdate', isset($lead->birthdate) ? \Carbon\Carbon::parse($lead->birthdate)->format('Y-m-d') : '') }}">
            @error('birthdate')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control-inf"
                   placeholder="e.g. Cairo"
                   value="{{ old('location', $lead->location ?? '') }}">
            @error('location')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- Degree — DB enum: Student, Graduate --}}
        <div class="form-field">
            <label class="form-label">Degree <span class="required">*</span></label>
            <select name="degree" class="form-control-inf" required>
                <option value="">— Select —</option>
                @foreach(['Student','Graduate'] as $deg)
                    <option value="{{ $deg }}"
                        {{ old('degree', $lead->degree ?? '') === $deg ? 'selected' : '' }}>
                        {{ $deg }}
                    </option>
                @endforeach
            </select>
            @error('degree')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- Source — DB enum: Facebook, Website, Friend, Walk_In, Google, Other --}}
        <div class="form-field">
            <label class="form-label">Lead Source <span class="required">*</span></label>
            <select name="source" class="form-control-inf" required>
                <option value="">— Select —</option>
                @foreach(['Facebook','Website','Friend','Walk_In','Google','Other'] as $src)
                    <option value="{{ $src }}"
                        {{ old('source', $lead->source ?? '') === $src ? 'selected' : '' }}>
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
            <select name="interested_course_template_id" class="form-control-inf" id="course_select">
                <option value="">— Select Course —</option>
                @foreach($courses ?? [] as $course)
                    <option value="{{ $course->course_template_id }}"
                        {{ old('interested_course_template_id', $lead->interested_course_template_id ?? '') == $course->course_template_id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            @error('interested_course_template_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label class="form-label">Level</label>
            <select name="interested_level_id" class="form-control-inf" id="level_select">
                <option value="">— Select Level —</option>
                @foreach($levels ?? [] as $level)
                    <option value="{{ $level->level_id }}"
                        {{ old('interested_level_id', $lead->interested_level_id ?? '') == $level->level_id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
            @error('interested_level_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label class="form-label">Sublevel</label>
            <select name="interested_sublevel_id" class="form-control-inf" id="sublevel_select">
                <option value="">— Select Sublevel —</option>
                @foreach($sublevels ?? [] as $sublevel)
                    <option value="{{ $sublevel->sublevel_id }}"
                        {{ old('interested_sublevel_id', $lead->interested_sublevel_id ?? '') == $sublevel->sublevel_id ? 'selected' : '' }}>
                        {{ $sublevel->name }}
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

        {{-- Status — DB enum: Waiting, Call_Again, Scheduled_Call, Registered, Not_Interested, Archived --}}
        <div class="form-field">
            <label class="form-label">Status <span class="required">*</span></label>
            <select name="status" class="form-control-inf" required>
                <option value="">— Select —</option>
                @foreach(['Waiting','Call_Again','Scheduled_Call','Registered','Not_Interested','Archived'] as $s)
                    <option value="{{ $s }}"
                        {{ old('status', $lead->status ?? 'Waiting') === $s ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $s) }}
                    </option>
                @endforeach
            </select>
            @error('status')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label class="form-label">Next Call At</label>
            <input type="datetime-local" name="next_call_at" class="form-control-inf"
                   style="color-scheme:light;"
                   value="{{ old('next_call_at', isset($lead->next_call_at) ? $lead->next_call_at->format('Y-m-d\TH:i') : '') }}">
            @error('next_call_at')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- Start Preference — DB enum: Current Patch, Next Patch, Specific Date --}}
        <div class="form-field">
            <label class="form-label">Start Preference</label>
            <select name="start_preference_type" class="form-control-inf">
                <option value="">— Select —</option>
                @foreach(['Current Patch','Next Patch','Specific Date'] as $pref)
                    <option value="{{ $pref }}"
                        {{ old('start_preference_type', $lead->start_preference_type ?? '') === $pref ? 'selected' : '' }}>
                        {{ $pref }}
                    </option>
                @endforeach
            </select>
            @error('start_preference_type')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field" id="specific_date_field" style="display:none;">
            <label class="form-label">Specific Date</label>
            <input type="datetime-local" name="start_preference_date" class="form-control-inf"
                value="{{ old('start_preference_date', isset($lead->start_preference_date) ? $lead->start_preference_date->format('Y-m-d\TH:i') : '') }}">
        </div>

    </div>

    <div class="form-divider"></div>

    {{-- ══ 4. NOTES ══ --}}
    <div class="form-section-label">Notes</div>

    <div class="form-grid cols-1">
        <div class="form-field">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control-inf"
                      placeholder="Any additional info about this lead...">{{ old('notes', $lead->notes ?? '') }}</textarea>
            @error('notes')<span class="form-error">{{ $message }}</span>@enderror
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


<script>
    document.getElementById('course_select').addEventListener('change', function() {
        const courseId = this.value;
        const levelSelect = document.getElementById('level_select');
        const sublevelSelect = document.getElementById('sublevel_select');
        
        levelSelect.innerHTML = '<option value="">— Select Level —</option>';
        sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';
        
        if (!courseId) return;
        
        fetch(`/levels/${courseId}`)
            .then(r => r.json())
            .then(levels => {
                levels.forEach(l => {
                    levelSelect.innerHTML += 
                        `<option value="${l.level_id}">${l.name}</option>`;
                });
            });
    });

    document.getElementById('level_select').addEventListener('change', function() {
        const levelId = this.value;
        const sublevelSelect = document.getElementById('sublevel_select');
        
        sublevelSelect.innerHTML = '<option value="">— Select Sublevel —</option>';
        
        if (!levelId) return;
        
        fetch(`/sublevels/${levelId}`)
            .then(r => r.json())
            .then(sublevels => {
                if (sublevels.length === 0) return; 
                sublevels.forEach(s => {
                    sublevelSelect.innerHTML += 
                        `<option value="${s.sublevel_id}">${s.name}</option>`;
                });
            });
    });

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