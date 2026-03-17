@extends('layouts.app')

@section('title', 'Create Lead')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
@endonce

<style>
    .create-page {
        background: #060606;
        min-height: 100vh;
        padding: 40px 32px;
        color: #F0EDE6;
        font-family: 'DM Sans', sans-serif;
    }

    /* ── HEADER ── */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 36px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(201,168,76,0.1);
    }

    .page-eyebrow {
        font-size: 10px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #C9A84C;
        margin-bottom: 6px;
    }

    .page-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 36px;
        letter-spacing: 4px;
        color: #F0EDE6;
        line-height: 1;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: transparent;
        border: 1px solid rgba(201,168,76,0.2);
        border-radius: 2px;
        color: #5A5550;
        font-size: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s;
        font-family: 'DM Sans', sans-serif;
    }

    .btn-back:hover {
        border-color: rgba(201,168,76,0.5);
        color: #C9A84C;
        text-decoration: none;
    }

    /* ── FORM CARD ── */
    .form-card {
        max-width: 760px;
        background: #0F0F0F;
        border: 1px solid rgba(201,168,76,0.12);
        border-radius: 2px;
        overflow: hidden;
        position: relative;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #C9A84C, transparent);
    }

    .form-card-body {
        padding: 36px 40px 40px;
    }

    /* ── SECTION LABEL ── */
    .form-section-label {
        font-size: 9px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #C9A84C;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(201,168,76,0.08);
    }

    /* ── GRID ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px 24px;
        margin-bottom: 28px;
    }

    .form-grid.cols-1 { grid-template-columns: 1fr; }
    .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }

    @media (max-width: 640px) {
        .form-grid, .form-grid.cols-3 { grid-template-columns: 1fr; }
    }

    /* ── FIELD ── */
    .form-field { display: flex; flex-direction: column; }
    .form-field.span-2 { grid-column: span 2; }

    .form-label {
        font-size: 9px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #5A5550;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-label .required { color: #C9A84C; margin-left: 3px; }

    .form-control-inf {
        width: 100%;
        padding: 12px 14px;
        background: #161616;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 2px;
        color: #F0EDE6;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 300;
        outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none;
        -webkit-appearance: none;
    }

    .form-control-inf::placeholder { color: #3A3530; }

    .form-control-inf:focus {
        border-color: #C9A84C;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.08);
    }

    select.form-control-inf {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%235A5550'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
        cursor: pointer;
    }

    select.form-control-inf option {
        background: #161616;
        color: #F0EDE6;
    }

    textarea.form-control-inf {
        resize: vertical;
        min-height: 90px;
    }

    .form-error {
        font-size: 10px;
        color: #F87171;
        margin-top: 5px;
        letter-spacing: 0.3px;
    }

    /* ── DIVIDER ── */
    .form-divider {
        height: 1px;
        background: rgba(255,255,255,0.04);
        margin: 28px 0;
    }

    /* ── SUBMIT AREA ── */
    .form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    .btn-cancel {
        padding: 11px 24px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 2px;
        color: #5A5550;
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s;
        cursor: pointer;
    }

    .btn-cancel:hover {
        border-color: rgba(255,255,255,0.15);
        color: #9A9590;
        text-decoration: none;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 32px;
        background: transparent;
        border: 1px solid #C9A84C;
        border-radius: 2px;
        color: #C9A84C;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 14px;
        letter-spacing: 4px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: color 0.4s;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: #C9A84C;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }

    .btn-submit:hover::before { transform: scaleX(1); }
    .btn-submit:hover { color: #060606; }
    .btn-submit span, .btn-submit svg { position: relative; z-index: 1; }
</style>

<div class="create-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">CRM Module</div>
            <h1 class="page-title">Add New Lead</h1>
        </div>

        <a href="{{ route('leads.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Leads
        </a>
    </div>

    {{-- ── FORM CARD ── --}}
    <div class="form-card">
        <div class="form-card-body">

            @include('leads.partials.form')

        </div>
    </div>

</div>

@endsection