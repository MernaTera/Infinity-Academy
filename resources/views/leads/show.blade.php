@extends('layouts.app')

@section('content')

<h2>Lead Details</h2>

<p>Name: {{ $lead->full_name }}</p>
<p>Phone: {{ $lead->phone }}</p>
<p>Status: {{ $lead->status }}</p>
<p>Location: {{ $lead->location }}</p>
<p>Source: {{ $lead->source }}</p>

@endsection