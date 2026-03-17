@extends('layouts.app')

@section('title','Archived Leads')

@section('content')

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="fw-bold">Archived Leads</h2>

<a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
Back
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
<th>Archived At</th>

</tr>

</thead>

<tbody>

@forelse($leads as $lead)

<tr>

<td>{{ $lead->full_name }}</td>

<td>{{ $lead->phone }}</td>

<td>{{ $lead->courseTemplate->course_name ?? '-' }}</td>

<td>

<span class="badge bg-dark">
Archived
</span>

</td>

<td>

{{ $lead->updated_at->format('d M Y') }}

</td>

</tr>

@empty

<tr>

<td colspan="5" class="text-center p-4 text-muted">

No archived leads

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