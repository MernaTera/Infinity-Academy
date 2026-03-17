<form method="POST"
      action="{{ isset($lead) ? route('leads.update',$lead->lead_id) : route('leads.store') }}">

@csrf

@if(isset($lead))
@method('PUT')
@endif

<div class="row g-3">

{{-- Name --}}
<div class="col-md-6">

<label class="form-label">Full Name</label>

<input type="text"
       name="full_name"
       value="{{ old('full_name',$lead->full_name ?? '') }}"
       class="form-control @error('full_name') is-invalid @enderror">

@error('full_name')
<div class="invalid-feedback">{{ $message }}</div>
@enderror

</div>


{{-- Phone --}}
<div class="col-md-6">

<label class="form-label">Phone</label>

<input type="text"
       name="phone"
       value="{{ old('phone',$lead->phone ?? '') }}"
       class="form-control @error('phone') is-invalid @enderror">

@error('phone')
<div class="invalid-feedback">{{ $message }}</div>
@enderror

</div>


{{-- Source --}}
<div class="col-md-6">

<label class="form-label">Source</label>

<select name="source"
        class="form-select @error('source') is-invalid @enderror">

<option value="">Select</option>

<option value="Facebook">Facebook</option>
<option value="Website">Website</option>
<option value="Google">Google</option>
<option value="Friend">Friend</option>
<option value="Walk_In">Walk In</option>
<option value="Other">Other</option>

</select>

@error('source')
<div class="invalid-feedback">{{ $message }}</div>
@enderror

</div>


{{-- Degree --}}
<div class="col-md-6">

<label class="form-label">Degree</label>

<select name="degree"
        class="form-select @error('degree') is-invalid @enderror">

<option value="">Select</option>

<option value="Student">Student</option>
<option value="Graduate">Graduate</option>

</select>

@error('degree')
<div class="invalid-feedback">{{ $message }}</div>
@enderror

</div>


{{-- Course --}}
<div class="col-md-6">

<label class="form-label">Interested Course</label>

<select name="interested_course_template_id"
        class="form-select">

<option value="">Select Course</option>

@foreach($courses as $course)

<option value="{{ $course->course_template_id }}">

{{ $course->course_name }}

</option>

@endforeach

</select>

</div>


{{-- Level --}}
<div class="col-md-3">

<label class="form-label">Level</label>

<select name="interested_level_id"
        class="form-select">

<option value="">Level</option>

@foreach($levels as $level)

<option value="{{ $level->level_id }}">

{{ $level->level_name }}

</option>

@endforeach

</select>

</div>


{{-- SubLevel --}}
<div class="col-md-3">

<label class="form-label">Sublevel</label>

<select name="interested_sublevel_id"
        class="form-select">

<option value="">Sublevel</option>

@foreach($sublevels as $sub)

<option value="{{ $sub->sublevel_id }}">

{{ $sub->sublevel_name }}

</option>

@endforeach

</select>

</div>


{{-- Next Call --}}
<div class="col-md-6">

<label class="form-label">Next Call</label>

<input type="datetime-local"
       name="next_call_at"
       value="{{ old('next_call_at',$lead->next_call_at ?? '') }}"
       class="form-control">

</div>


{{-- Start Preference --}}
<div class="col-md-6">

<label class="form-label">Start Preference</label>

<select name="start_preference_type"
        class="form-select">

<option value="">Select</option>

<option value="Current Patch">Current Patch</option>
<option value="Next Patch">Next Patch</option>
<option value="Specific Date">Specific Date</option>

</select>

</div>


{{-- Notes --}}
<div class="col-12">

<label class="form-label">Notes</label>

<textarea name="notes"
          rows="3"
          class="form-control">{{ old('notes',$lead->notes ?? '') }}</textarea>

</div>


</div>

<div class="mt-4 d-flex justify-content-end">

<button class="btn btn-primary">

Save Lead

</button>

</div>

</form>