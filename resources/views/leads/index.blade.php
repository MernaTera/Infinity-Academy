@extends('layouts.app')

@section('title','My Leads')

@section('content')

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="fw-bold">My Follow-Up Leads</h2>

<a href="{{ route('leads.create') }}" class="btn btn-primary">
+ Add Lead
</a>

</div>

<div class="card shadow-sm border-0">

<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">

<tr>

<th>Name</th>
<th>Phone</th>
<th>Course</th>
<th>Status</th>
<th>Next Call</th>
<th>Days</th>
<th width="200">Actions</th>

</tr>

</thead>

<tbody>

@forelse($leads as $lead)

<tr>

<td class="fw-semibold">
{{ $lead->full_name }}
</td>

<td>
{{ $lead->phone }}
</td>

<td>

@if($lead->courseTemplate)
{{ $lead->courseTemplate->course_name }}
@else
-
@endif

</td>

<td>

@php
$color = match($lead->status){
'Waiting' => 'secondary',
'Call_Again' => 'warning',
'Scheduled_Call' => 'info',
'Registered' => 'success',
'Not_Interested' => 'dark',
default => 'light'
};
@endphp

<span class="badge bg-{{ $color }}">
{{ str_replace('_',' ',$lead->status) }}
</span>

</td>

<td>

@if($lead->next_call_at)
{{ $lead->next_call_at->format('d M Y H:i') }}
@else
-
@endif

</td>

<td>

{{ $lead->created_at->diffInDays(now()) }}

</td>

<td>

<a href="{{ route('leads.edit',$lead->lead_id) }}"
class="btn btn-sm btn-outline-primary">

Edit

</a>

<form
action="{{ route('leads.destroy',$lead->lead_id) }}"
method="POST"
class="d-inline">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-outline-danger">

Delete

</button>

</form>

</td>

</tr>

@empty

<tr>

<td colspan="7" class="text-center p-4 text-muted">

No leads found

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

<div class="mt-4">

{{ $leads->links() }}

</div>

</div>

@endsection