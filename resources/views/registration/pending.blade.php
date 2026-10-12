@extends('layouts.leads')
@section('title', 'Awaiting Approval')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
        --green:#059669; --green-dk:#15803D; --red:#DC2626;
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --card:rgba(255,255,255,0.5); --glass-bd:rgba(255,255,255,0.6);
        --glass-sh:0 8px 30px rgba(23,45,90,0.07);
    }
    .pending-page {
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        padding: 40px 24px; font-family: 'DM Sans', sans-serif; color: var(--text);
    }

    .pending-card {
        width: 100%; max-width: 480px;
        background: var(--card);
        -webkit-backdrop-filter: blur(22px) saturate(165%);
        backdrop-filter: blur(22px) saturate(165%);
        border: 1px solid var(--glass-bd);
        border-radius: 22px; overflow: hidden; position: relative;
        box-shadow: 0 16px 48px rgba(23,45,90,0.10);
        text-align: center;
    }
    /* .pending-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, transparent, var(--blue), transparent);
    } */

    .pending-body { padding: 48px 40px 40px; }

    .pulse-wrap {
        width: 80px; height: 80px; border-radius: 50%;
        background: rgba(245,145,30,0.10);
        border: 1.5px solid rgba(245,145,30,0.25);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px; position: relative;
    }
    .pulse-ring {
        position: absolute; inset: -10px; border-radius: 50%;
        border: 1.5px solid rgba(245,145,30,0.18);
        animation: pulseRing 2s ease-in-out infinite;
    }
    .pulse-ring-2 {
        position: absolute; inset: -20px; border-radius: 50%;
        border: 1px solid rgba(245,145,30,0.10);
        animation: pulseRing 2s ease-in-out infinite 0.5s;
    }
    @keyframes pulseRing {
        0%,100% { opacity: 1; transform: scale(1); }
        50%      { opacity: 0; transform: scale(1.15); }
    }

    .pending-eyebrow {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: var(--blue); margin-bottom: 8px; font-weight: 600;
    }
    .pending-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 28px; letter-spacing: 4px; color: var(--text);
        line-height: 1; margin-bottom: 12px;
    }
    .pending-msg {
        font-size: 13px; color: var(--muted); line-height: 1.7;
        margin-bottom: 28px;
    }

    .status-dots {
        display: flex; justify-content: center; align-items: center; gap: 8px;
        margin-bottom: 28px;
    }
    .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--blue); animation: dotBounce 1.4s ease-in-out infinite;
    }
    .dot:nth-child(2) { animation-delay: 0.2s; }
    .dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes dotBounce {
        0%,80%,100% { transform: scale(0.6); opacity: 0.4; }
        40%          { transform: scale(1);   opacity: 1; }
    }

    .pending-info {
        background: rgba(255,255,255,0.55);
        border: 1px solid rgba(27,79,168,0.10);
        border-radius: 14px; padding: 14px 16px;
        text-align: left; margin-bottom: 24px;
    }
    .pending-info-row {
        display: flex; justify-content: space-between;
        padding: 6px 0; border-bottom: 1px solid rgba(27,79,168,0.06);
        font-size: 12px;
    }
    .pending-info-row:last-child { border-bottom: none; }
    .pending-info-key { color: var(--muted); }
    .pending-info-val { color: var(--text); font-weight: 500; }

    .btn-home {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px; background: rgba(255,255,255,0.6);
        -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.18); border-radius: 12px;
        color: var(--muted); font-size: 10px; letter-spacing: 2px; font-weight: 600;
        text-transform: uppercase; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-home:hover { border-color: var(--blue); color: var(--blue); text-decoration: none; }

    .poll-status {
        font-size: 10px; color: var(--faint); margin-top: 16px; letter-spacing: 0.5px;
    }

    .state-success { display: none; }
    .state-success .pulse-wrap { background: rgba(5,150,105,0.10); border-color: rgba(5,150,105,0.25); }
    .state-success .pulse-ring { border-color: rgba(5,150,105,0.18); }
    .state-success .pending-eyebrow { color: var(--green-dk); }
    .state-success .pending-title { color: var(--green-dk); }
    .state-success .dot { background: var(--green-dk); }

    .state-rejected { display: none; }
    .state-rejected .pulse-wrap { background: rgba(220,38,38,0.08); border-color: rgba(220,38,38,0.22); }
    .state-rejected .pulse-ring { border-color: rgba(220,38,38,0.15); animation: none; }
    .state-rejected .pending-eyebrow { color: var(--red); }
    .state-rejected .pending-title { color: var(--red); }

    .rejection-note {
        background: rgba(220,38,38,0.06);
        border: 1px solid rgba(220,38,38,0.2);
        border-radius: 12px; padding: 12px 14px;
        font-size: 12px; color: var(--red);
        margin-bottom: 20px; text-align: left;
    }
</style>

<div class="pending-page">
    <div class="pending-card">
        <div class="pending-body">

            <div id="statePending">
                <div class="pulse-wrap">
                    <div class="pulse-ring"></div>
                    <div class="pulse-ring-2"></div>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>

                <div class="pending-eyebrow">Registration</div>
                <div class="pending-title">Awaiting Admin Approval</div>
                <div class="pending-msg">
                    Your registration request has been submitted successfully.<br>
                    The admin will review and approve it shortly.
                </div>

                <div class="status-dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>

                <div class="pending-info">
                    <div class="pending-info-row">
                        <span class="pending-info-key">Student</span>
                        <span class="pending-info-val">{{ $enrollment->student?->full_name ?? '—' }}</span>
                    </div>
                    <div class="pending-info-row">
                        <span class="pending-info-key">Course</span>
                        <span class="pending-info-val">{{ $enrollment->courseTemplate?->name ?? '—' }}</span>
                    </div>
                    <div class="pending-info-row">
                        <span class="pending-info-key">Payment Plan</span>
                        <span class="pending-info-val">{{ $enrollment->paymentPlan?->name ?? '—' }}</span>
                    </div>
                    <div class="pending-info-row">
                        <span class="pending-info-key">Status</span>
                        <span class="pending-info-val" style="color:#C47010;">Pending Approval</span>
                    </div>
                </div>

                <a href="{{ route('leads.index') }}" class="btn-home">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    Back to Leads
                </a>

                <div class="poll-status" id="pollStatus">Checking status every 10 seconds...</div>
            </div>

            <div id="stateSuccess" class="state-success" style="display:none;">
                <div class="pulse-wrap" style="background:rgba(5,150,105,0.10);border-color:rgba(5,150,105,0.25);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="pending-eyebrow" style="color:#15803D;">Approved</div>
                <div class="pending-title" style="color:#15803D;">Admin Approved!</div>
                <div class="pending-msg">
                    Your request has been approved by the admin.<br>
                    The student is now <strong>Active</strong>. Redirecting to leads...
                </div>
                <div style="display:flex;justify-content:center;gap:6px;margin-bottom:20px;">
                    <div class="dot" style="background:#15803D;"></div>
                    <div class="dot" style="background:#15803D;animation-delay:0.2s;"></div>
                    <div class="dot" style="background:#15803D;animation-delay:0.4s;"></div>
                </div>
            </div>

            <div id="stateRejected" style="display:none;">
                <div class="pulse-wrap" style="background:rgba(220,38,38,0.08);border-color:rgba(220,38,38,0.22);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </div>
                <div class="pending-eyebrow" style="color:#DC2626;">Declined</div>
                <div class="pending-title" style="color:#DC2626;">Request Declined</div>
                <div class="pending-msg">
                    The admin has declined your registration request.<br>
                    Please contact the admin for more information.
                </div>
                <div class="rejection-note" id="rejectionNote" style="display:none;"></div>
                <a href="{{ route('leads.index') }}" class="btn-home" style="color:#DC2626;border-color:rgba(220,38,38,0.2);">
                    Back to Leads
                </a>
            </div>

        </div>
    </div>
</div>

<script>
const enrollmentId = {{ $enrollment->enrollment_id }};
let pollInterval;
let pollCount = 0;
const MAX_POLLS = 90; 

function startPolling() {
    pollInterval = setInterval(checkStatus, 10000); 
}

async function checkStatus() {
    pollCount++;

    if (pollCount > MAX_POLLS) {
        clearInterval(pollInterval);
        showTimeout();
        return;
    }

    const statusEl = document.getElementById('pollStatus');
    if (statusEl) statusEl.textContent = `Checking status... (check #${pollCount})`;

    try {
        const res  = await fetch(`/registration/check-status/${enrollmentId}`);
        const data = await res.json();

        if (data.approval_status === 'Approved') {
            showSuccess();
            return;
        }

        if (data.approval_status === 'Rejected') {
            showRejected(data.rejection_note);
            return;
        }

        if (data.status === 'Cancelled') {
            showRejected(data.rejection_note || 'Registration was cancelled.');
            return;
        }

    } catch (e) {
        if (statusEl) statusEl.textContent = 'Connection error — retrying...';
    }
}

function showSuccess() {
    clearInterval(pollInterval);
    document.getElementById('statePending').style.display  = 'none';
    document.getElementById('stateSuccess').style.display  = 'block';
    document.getElementById('stateRejected').style.display = 'none';

    setTimeout(() => {
        window.location.href = '{{ route("leads.index") }}?approved=1';
    }, 3000);
}

function showRejected(note) {
    clearInterval(pollInterval);
    document.getElementById('statePending').style.display  = 'none';
    document.getElementById('stateSuccess').style.display  = 'none';
    document.getElementById('stateRejected').style.display = 'block';

    const noteEl = document.getElementById('rejectionNote');
    if (noteEl && note) {
        noteEl.textContent = 'Reason: ' + note;
        noteEl.style.display = 'block';
    }

    setTimeout(() => {
        window.location.href = '{{ route("leads.index") }}';
    }, 5000);
}

function showTimeout() {
    const statusEl = document.getElementById('pollStatus');
    if (statusEl) {
        statusEl.innerHTML = `
            <div style="color:#C47010;font-weight:600;margin-top:12px;">
                Still waiting for admin approval...
            </div>
            <div style="font-size:11px;color:#5A6A85;margin-top:6px;">
                You'll receive a notification once decided. Feel free to return to your leads.
            </div>
            <a href="{{ route('leads.index') }}"
               style="display:inline-block;margin-top:14px;padding:9px 20px;
                      background:linear-gradient(120deg,#1B4FA8,#2D6FDB);color:#fff;text-decoration:none;
                      border-radius:12px;font-size:11px;letter-spacing:1.5px;
                      text-transform:uppercase;font-weight:600;box-shadow:0 10px 24px rgba(27,79,168,0.28);">
                Back to Leads
            </a>
        `;
    }
}

startPolling();

setTimeout(checkStatus, 2000);
</script>

@endsection