@extends('layouts.app')

@section('title','Create Lead')

@section('content')

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow-sm border-0">

<div class="card-header bg-white d-flex justify-content-between align-items-center">

<h5 class="mb-0 fw-bold">Add New Lead</h5>

<a href="{{ route('leads.index') }}" class="btn btn-sm btn-outline-secondary">
Back
</a>

</div>

<div class="card-body">

@include('leads.partials.form')

</div>

</div>

</div>

</div>

</div>

@endsection