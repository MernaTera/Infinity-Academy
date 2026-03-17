@if ($errors->any())
    <div style="color:red">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
<form method="POST"
      action="{{ isset($lead) ? route('leads.update', $lead->lead_id) : route('leads.store') }}">
    @csrf
    @if(isset($lead)) @method('PUT') @endif

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
                   value="{{ old('full_name', $lead->full_name ?? '') }}"
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
                   value="{{ old('phone', $lead->phone ?? '') }}"
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
                   value="{{ old('email', $lead->email ?? '') }}">
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Course --}}
        <div class="form-field">
            <label class="form-label">Course</label>
            <select name="interested_course_template_id" class="form-control-inf">
                <option value="">— Select Course —</option>
                        @foreach($courses ?? [] as $course)
                        <option value="{{ $course->course_template_id }}"
                                {{ old('interested_course_template_id', $lead->interested_course_template_id ?? '') == $course->interested_course_template_id ? 'selected' : '' }}>
                                
                                {{ $course->name }}
                                
                        </option>
                        @endforeach
            </select>
            @error('interested_course_template_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Degree --}}
    <div class="form-field">
    <label class="form-label">Degree <span class="required">*</span></label>
    <select name="degree" class="form-control-inf" required>
        <option value="">— Select Degree —</option>
        @foreach(['Student','Graduate','Other'] as $deg)
            <option value="{{ $deg }}"
                {{ old('degree', $lead->degree ?? '') === $deg ? 'selected' : '' }}>
                {{ $deg }}
            </option>
        @endforeach
    </select>
    @error('degree')
        <span class="form-error">{{ $message }}</span>
    @enderror
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
                        {{ old('status', $lead->status ?? '') === $s ? 'selected' : '' }}>
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
                   value="{{ old('next_call_at', isset($lead->next_call_at) ? $lead->next_call_at->format('Y-m-d\TH:i') : '') }}"
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
                        {{ old('source', $lead->source ?? '') === $src ? 'selected' : '' }}>
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
                      placeholder="Any additional info about this lead...">{{ old('notes', $lead->notes ?? '') }}</textarea>
            @error('notes')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- ── FOOTER ── --}}
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