@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div style="padding:40px 32px;font-family:'DM Sans',sans-serif;">
    <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px">Administrator</div>
    <h1 style="font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0">Admin Dashboard</h1>
    <p style="color:#7A8A9A;font-size:12px;margin-top:4px">{{ now()->format('l, d M Y') }}</p>
</div>
@endsection