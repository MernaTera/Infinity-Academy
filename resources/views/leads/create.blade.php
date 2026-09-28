@extends('layouts.leads')

@section('title', 'Create Lead')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
        --green:#059669; --green-dk:#15803D; --purple:#7C3AED; --red:#DC2626;
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --glass-bd:rgba(255,255,255,0.7);
    }
    * { box-sizing:border-box; }

    .create-page {
        position:relative; min-height:100vh; overflow:hidden;
        padding:44px 34px 60px; color:var(--text); font-family:'DM Sans',sans-serif;
        background:#F8F6F2;
    }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:-80px; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .page-header {
        position:relative; z-index:1;
        display:flex; align-items:flex-end; justify-content:space-between;
        max-width:920px; margin:0 auto 22px; flex-wrap:wrap; gap:16px;
    }
    .page-eyebrow { font-size:12px; letter-spacing:4px; text-transform:uppercase; color:var(--blue); margin-bottom:8px; font-weight:600; }
    .page-title   { font-family:'Bebas Neue',sans-serif; font-size:46px; letter-spacing:2px; color:var(--text); line-height:0.92; margin:0; }
    .btn-back {
        display:inline-flex; align-items:center; gap:8px; padding:11px 18px;
        background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(14px) saturate(150%); backdrop-filter:blur(14px) saturate(150%);
        border:1px solid var(--glass-bd); border-radius:14px;
        color:var(--muted); font-size:10px; letter-spacing:2px; text-transform:uppercase; font-weight:600;
        text-decoration:none; transition:color .25s, border-color .25s, background .25s; font-family:'DM Sans',sans-serif;
        box-shadow:0 6px 18px rgba(23,45,90,0.06);
    }
    .btn-back:hover { border-color:var(--blue); color:var(--blue); background:rgba(255,255,255,0.7); text-decoration:none; }

    .form-card {
        position:relative; z-index:1; max-width:920px; margin:0 auto;
        background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(30px) saturate(180%); backdrop-filter:blur(30px) saturate(180%);
        border:1px solid var(--glass-bd); border-radius:28px; overflow:hidden;
        box-shadow:0 30px 70px -22px rgba(23,45,90,0.28), inset 0 1px 0 rgba(255,255,255,0.7);
    }
    .form-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:4px;
        background:linear-gradient(90deg, var(--orange), var(--blue), var(--purple));
    }
    .form-card-body { padding:34px 38px 36px; }
    @media (max-width:680px){ .create-page{ padding:22px 14px 40px; } .form-card-body{ padding:22px; } .page-title{ font-size:38px; } }

    .form-section-label {
        display:flex; align-items:center; gap:10px;
        font-size:11px; letter-spacing:3px; text-transform:uppercase;
        color:var(--text); font-weight:700; margin-bottom:18px;
    }
    .form-section-label::before {
        content:''; width:20px; height:3px; border-radius:3px;
        background:linear-gradient(90deg, var(--orange), var(--blue));
    }

    .form-grid        { display:grid; grid-template-columns:1fr 1fr; gap:18px 20px; margin-bottom:22px; }
    .form-grid.cols-1 { grid-template-columns:1fr; }
    .form-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
    @media (max-width:680px){ .form-grid, .form-grid.cols-3 { grid-template-columns:1fr; } }

    .form-field { display:flex; flex-direction:column; }
    .form-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:8px; font-weight:600; }
    .form-label .required, .form-label .req { color:var(--orange); margin-left:2px; }

    .form-control-inf {
        width:100%; padding:13px 15px;
        background:rgba(255,255,255,0.55); border:1px solid rgba(27,79,168,0.1); border-radius:14px;
        box-shadow:inset 0 1px 2px rgba(255,255,255,0.85);
        color:var(--text); font-family:'DM Sans',sans-serif; font-size:13px; font-weight:400; outline:none;
        transition:border-color .25s, box-shadow .25s, background .25s;
        appearance:none; -webkit-appearance:none;
    }
    .form-control-inf::placeholder { color:var(--faint); }
    .form-control-inf:focus { border-color:var(--blue); background:rgba(255,255,255,0.85); box-shadow:0 0 0 4px rgba(27,79,168,0.12), inset 0 1px 2px rgba(255,255,255,0.85); }

    select.form-control-inf {
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%235A6A85'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 13px center;
        padding-right:36px; cursor:pointer;
    }
    select.form-control-inf option { background:#fff; color:var(--text); }
    textarea.form-control-inf { resize:vertical; min-height:96px; }

    .form-error { font-size:10px; color:var(--red); margin-top:5px; }
    .form-divider { height:1px; background:linear-gradient(90deg, transparent, rgba(27,79,168,0.12), transparent); margin:26px 0; }

    .form-footer {
        display:flex; align-items:center; justify-content:flex-end;
        gap:12px; padding-top:24px; border-top:1px solid rgba(27,79,168,0.08);
    }

    .btn-cancel {
        padding:12px 24px; background:rgba(255,255,255,0.5); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px);
        border:1px solid var(--glass-bd); border-radius:13px;
        color:var(--muted); font-family:'DM Sans',sans-serif; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-weight:600;
        text-decoration:none; transition:color .25s, border-color .25s; cursor:pointer;
    }
    .btn-cancel:hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }

    .btn-submit {
        display:inline-flex; align-items:center; gap:9px; padding:13px 32px;
        background:linear-gradient(120deg, var(--blue), var(--blue-2)); border:none; border-radius:13px;
        color:#fff; font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:3px;
        cursor:pointer; box-shadow:0 12px 28px rgba(27,79,168,0.36); transition:box-shadow .3s, filter .3s;
    }
    .btn-submit:hover { box-shadow:0 18px 40px rgba(27,79,168,0.46); filter:brightness(1.07); }
    .btn-submit span, .btn-submit svg { position:relative; z-index:1; }
</style>
<script src="{{ asset('js/leads/create-modal.js') }}"></script>
<div class="create-page">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Leads</div>
            <h1 class="page-title">Add New Lead</h1>
        </div>
        <a href="{{ route('leads.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Leads
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-body">
            @include('leads.partials.form')
        </div>
    </div>

</div>

@endsection
