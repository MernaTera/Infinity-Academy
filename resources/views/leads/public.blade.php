@extends('layouts.app')

@section('title','Public Leads')

@section('content')

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="fw-bold">Public Leads</h2>

<a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
My Leads
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
<th>Owner</th>
<th>Status</th>
<th>Days</th>
<th width="180">Actions</th>
</tr>

</thead>

<tbody>

@forelse($leads as $lead)

<tr>

<td>{{ $lead->full_name }}</td>

<td>{{ $lead->phone }}</td>

<td>
{{ $lead->courseTemplate->course_name ?? '-' }}
</td>

<td>
{{ $lead->owner->full_name ?? 'Public' }}
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

{{ $lead->created_at->diffInDays(now()) }}

</td>

<td>

<form action="{{ route('leads.assign',$lead->lead_id) }}" method="POST">

@csrf

<button class="btn btn-sm btn-primary">

Take Lead

</button>

</form>

</td>

</tr>

@empty

<tr>

<td colspan="7" class="text-center p-4 text-muted">

No public leads

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