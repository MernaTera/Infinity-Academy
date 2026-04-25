@extends('layouts.leads')
@section('title', 'Awaiting Approval')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
    .pending-page {
        min-height: 100vh; background: #F8F6F2;
        display: flex; align-items: center; justify-content: center;
        padding: 40px 24px; font-family: 'DM Sans', sans-serif;
    }

    .pending-card {
        width: 100%; max-width: 480px;
        background: #fff;
        border: 1px solid rgba(27,79,168,0.12);
        border-radius: 12px; overflow: hidden; position: relative;
        box-shadow: 0 8px 40px rgba(27,79,168,0.1);
        text-align: center;
    }
    .pending-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F5911E, #1B4FA8, #2D6FDB);
    }

    .pending-body { padding: 48px 40px 40px; }

    /* Pulse icon */
    .pulse-wrap {
        width: 80px; height: 80px; border-radius: 50%;
        background: rgba(245,145,30,0.07);
        border: 1.5px solid rgba(245,145,30,0.2);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px; position: relative;
    }
    .pulse-ring {
        position: absolute; inset: -10px; border-radius: 50%;
        border: 1.5px solid rgba(245,145,30,0.15);
        animation: pulseRing 2s ease-in-out infinite;
    }
    .pulse-ring-2 {
        position: absolute; inset: -20px; border-radius: 50%;
        border: 1px solid rgba(245,145,30,0.08);
        animation: pulseRing 2s ease-in-out infinite 0.5s;
    }
    @keyframes pulseRing {
        0%,100% { opacity: 1; transform: scale(1); }
        50%      { opacity: 0; transform: scale(1.15); }
    }

    .pending-eyebrow {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 8px;
    }
    .pending-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 28px; letter-spacing: 4px; color: #1B4FA8;
        line-height: 1; margin-bottom: 12px;
    }
    .pending-msg {
        font-size: 13px; color: #7A8A9A; line-height: 1.7;
        margin-bottom: 28px;
    }

    /* Status dots */
    .status-dots {
        display: flex; justify-content: center; align-items: center; gap: 8px;
        margin-bottom: 28px;
    }
    .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #1B4FA8; animation: dotBounce 1.4s ease-in-out infinite;
    }
    .dot:nth-child(2) { animation-delay: 0.2s; }
    .dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes dotBounce {
        0%,80%,100% { transform: scale(0.6); opacity: 0.4; }
        40%          { transform: scale(1);   opacity: 1; }
    }

    .pending-info {
        background: rgba(27,79,168,0.03);
        border: 1px solid rgba(27,79,168,0.08);
        border-radius: 6px; padding: 14px 16px;
        text-align: left; margin-bottom: 24px;
    }
    .pending-info-row {
        display: flex; justify-content: space-between;
        padding: 5px 0; border-bottom: 1px solid rgba(27,79,168,0.05);
        font-size: 12px;
    }
    .pending-info-row:last-child { border-bottom: none; }
    .pending-info-key { color: #7A8A9A; }
    .pending-info-val { color: #1A2A4A; font-weight: 500; }

    .btn-home {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px; background: transparent;
        border: 1px solid rgba(27,79,168,0.2); border-radius: 6px;
        color: #7A8A9A; font-size: 10px; letter-spacing: 2px;
        text-transform: uppercase; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-home:hover { border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    .poll-status {
        font-size: 10px; color: #AAB8C8; margin-top: 16px; letter-spacing: 0.5px;
    }

    /* ── SUCCESS STATE ── */
    .state-success { display: none; }
    .state-success .pulse-wrap { background: rgba(5,150,105,0.07); border-color: rgba(5,150,105,0.2); }
    .state-success .pulse-ring { border-color: rgba(5,150,105,0.15); }
    .state-success .pending-eyebrow { color: #059669; }
    .state-success .pending-title { color: #059669; }
    .state-success .dot { background: #059669; }

    /* ── REJECTED STATE ── */
    .state-rejected { display: none; }
    .state-rejected .pulse-wrap { background: rgba(220,38,38,0.07); border-color: rgba(220,38,38,0.2); }
    .state-rejected .pulse-ring { border-color: rgba(220,38,38,0.15); animation: none; }
    .state-rejected .pending-eyebrow { color: #DC2626; }
    .state-rejected .pending-title { color: #DC2626; }

    .rejection-note {
        background: rgba(220,38,38,0.04);
        border: 1px solid rgba(220,38,38,0.15);
        border-radius: 6px; padding: 12px 14px;
        font-size: 12px; color: #DC2626;
        margin-bottom: 20px; text-align: left;
    }
</style>

<div class="pending-page">
    <div class="pending-card">
        <div class="pending-body">

            {{-- ── PENDING STATE (default) ── --}}
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

            {{-- ── SUCCESS STATE ── --}}
            <div id="stateSuccess" class="state-success" style="display:none;">
                <div class="pulse-wrap" style="background:rgba(5,150,105,0.07);border-color:rgba(5,150,105,0.2);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="pending-eyebrow" style="color:#059669;">Approved</div>
                <div class="pending-title" style="color:#059669;">Admin Approved!</div>
                <div class="pending-msg">
                    Your request has been approved by the admin.<br>
                    The student is now <strong>Active</strong>. Redirecting to leads...
                </div>
                <div style="display:flex;justify-content:center;gap:6px;margin-bottom:20px;">
                    <div class="dot" style="background:#059669;"></div>
                    <div class="dot" style="background:#059669;animation-delay:0.2s;"></div>
                    <div class="dot" style="background:#059669;animation-delay:0.4s;"></div>
                </div>
            </div>

            {{-- ── REJECTED STATE ── --}}
            <div id="stateRejected" style="display:none;">
                <div class="pulse-wrap" style="background:rgba(220,38,38,0.07);border-color:rgba(220,38,38,0.2);">
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

function startPolling() {
    pollInterval = setInterval(checkStatus, 10000); // every 10s
}

async function checkStatus() {
    pollCount++;
    const statusEl = document.getElementById('pollStatus');
    if (statusEl) statusEl.textContent = `Checking status... (check #${pollCount})`;

    try {
        const res  = await fetch(`/registration/check-status/${enrollmentId}`);
        const data = await res.json();

        if (data.status === 'Active') {
            showSuccess();
        } else if (data.status === 'Cancelled' && data.approval_status === 'Rejected') {
            showRejected(data.rejection_note);
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

    // Redirect after 3 seconds
    setTimeout(() => {
        window.location.href = '{{ route("leads.index") }}?approved=1';
    }, 3000);
}

function showRejected(note) {
    clearInterval(pollInterval);
    document.getElementById('statePending').style.display  = 'none';
    document.getElementById('stateSuccess').style.display  = 'none';
    document.getElementById('stateRejected').style.display = 'block';

    if (note) {
        const noteEl = document.getElementById('rejectionNote');
        noteEl.textContent = 'Reason: ' + note;
        noteEl.style.display = 'block';
    }

    // Redirect after 5 seconds
    setTimeout(() => {
        window.location.href = '{{ route("leads.index") }}';
    }, 5000);
}

// Start polling on page load
startPolling();

// Also check immediately after 2 seconds (in case already approved)
setTimeout(checkStatus, 2000);
</script>

@endsection