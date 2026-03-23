@extends('layouts.leads')

@section('title', 'Create Lead')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    .create-page {
        background: #F8F6F2;
        min-height: 100vh;
        padding: 40px 32px;
        color: #1A2A4A;
        font-family: 'DM Sans', sans-serif;
    }

    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 32px; padding-bottom: 22px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap; gap: 16px;
    }

    .page-eyebrow { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title   { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.2); border-radius: 4px;
        color: #7A8A9A; font-size: 10px; letter-spacing: 3px;
        text-transform: uppercase; text-decoration: none;
        transition: all 0.3s; font-family: 'DM Sans', sans-serif;
    }
    .btn-back:hover { border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    /* ── FORM CARD ── */
    .form-card {
        max-width: 860px;
        margin: 0 auto;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 8px; overflow: hidden; position: relative;
        box-shadow: 0 4px 24px rgba(27,79,168,0.07);
    }
    .form-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #F5911E, #1B4FA8, transparent);
    }

    .form-card-body { padding: 28px 32px 32px; }

    /* ── SECTION LABEL ── */
    .form-section-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 16px; padding-bottom: 9px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
    }

    /* ── GRIDS ── */
    .form-grid        { display: grid; grid-template-columns: 1fr 1fr;       gap: 16px 20px; margin-bottom: 20px; }
    .form-grid.cols-1 { grid-template-columns: 1fr; }
    .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }

    @media (max-width: 680px) {
        .form-grid, .form-grid.cols-3 { grid-template-columns: 1fr; }
        .create-page { padding: 18px 14px; }
        .form-card-body { padding: 18px; }
    }

    .form-field { display: flex; flex-direction: column; }

    .form-label {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; margin-bottom: 6px; font-weight: 500;
    }
    .form-label .required { color: #F5911E; margin-left: 2px; }

    .form-control-inf {
        width: 100%; padding: 10px 12px;
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(27,79,168,0.12); border-radius: 4px;
        color: #1A2A4A; font-family: 'DM Sans', sans-serif;
        font-size: 13px; font-weight: 300; outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none; -webkit-appearance: none;
    }
    .form-control-inf::placeholder { color: #B0BCCC; }
    .form-control-inf:focus {
        border-color: #1B4FA8;
        box-shadow: 0 0 0 3px rgba(27,79,168,0.08);
    }

    select.form-control-inf {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 11px center;
        padding-right: 32px; cursor: pointer;
        background-color: rgba(255,255,255,0.92);
    }
    select.form-control-inf option { background: #fff; color: #1A2A4A; }
    textarea.form-control-inf { resize: vertical; min-height: 88px; }

    .form-error { font-size: 10px; color: #DC2626; margin-top: 4px; }
    .form-divider { height: 1px; background: rgba(27,79,168,0.06); margin: 22px 0; }

    /* ── FOOTER ── */
    .form-footer {
        display: flex; align-items: center; justify-content: flex-end;
        gap: 10px; padding-top: 20px;
        border-top: 1px solid rgba(27,79,168,0.07);
    }

    .btn-cancel {
        padding: 10px 22px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 4px;
        color: #7A8A9A; font-family: 'DM Sans', sans-serif;
        font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        text-decoration: none; transition: all 0.3s; cursor: pointer;
    }
    .btn-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; text-decoration: none; }

    .btn-submit {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 28px; background: transparent;
        border: 1.5px solid #1B4FA8; border-radius: 4px;
        color: #1B4FA8; font-family: 'Bebas Neue', sans-serif;
        font-size: 14px; letter-spacing: 4px;
        cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s;
    }
    .btn-submit::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-submit:hover::before { transform: scaleX(1); }
    .btn-submit:hover { color: #fff; }
    .btn-submit span, .btn-submit svg { position: relative; z-index: 1; }
</style>

<div class="create-page">

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