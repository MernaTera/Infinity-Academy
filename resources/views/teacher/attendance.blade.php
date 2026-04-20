@extends('teacher.layouts.app')
@section('title', 'Take Attendance')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.att-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#059669;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(5,150,105,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#059669;color:#059669;text-decoration:none}

/* Session Info */
.session-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;padding:18px 22px;margin-bottom:20px;position:relative;overflow:hidden}
.session-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.session-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:16px}
.sg-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;margin-bottom:4px}
.sg-val{font-size:13px;color:#1A2A4A;font-weight:500}
.sg-time{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#059669;letter-spacing:2px;line-height:1}

/* Timer */
.timer-banner{background:linear-gradient(135deg,#059669,#10B981);border-radius:8px;padding:14px 22px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between}
.timer-text{color:rgba(255,255,255,0.8);font-size:12px}
.timer-val{font-family:'Bebas Neue',sans-serif;font-size:28px;color:#fff;letter-spacing:2px}
.timer-warn{background:rgba(245,145,30,0.15);border:1px solid rgba(245,145,30,0.3);border-radius:8px;padding:14px 22px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:12px;color:#C47010}
.timer-closed{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);border-radius:8px;padding:14px 22px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:12px;color:#DC2626}
.timer-done{background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.15);border-radius:8px;padding:14px 22px;margin-bottom:20px;font-size:12px;color:#059669}

/* Attendance Table */
.att-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden}
.att-card-header{padding:14px 20px;border-bottom:1px solid rgba(5,150,105,0.07);display:flex;align-items:center;justify-content:space-between}
.att-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1A2A4A}

.att-row{display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid rgba(5,150,105,0.04);transition:background 0.2s}
.att-row:last-child{border-bottom:none}
.att-row:hover{background:rgba(5,150,105,0.02)}
.att-row.restricted{background:rgba(220,38,38,0.02)}

.att-avatar{width:34px;height:34px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:14px;color:#059669;flex-shrink:0}
.att-avatar.restricted{background:rgba(220,38,38,0.1);color:#DC2626}
.att-name{font-weight:500;color:#1A2A4A;font-size:13px;flex:1}
.att-phone{font-size:10px;color:#AAB8C8;font-family:monospace;margin-top:2px}

/* Toggle */
.att-toggle{display:flex;border:1px solid rgba(5,150,105,0.2);border-radius:6px;overflow:hidden;flex-shrink:0}
.att-toggle label{display:flex;align-items:center;gap:6px;padding:8px 16px;cursor:pointer;font-size:11px;letter-spacing:1px;text-transform:uppercase;transition:all 0.2s;white-space:nowrap}
.att-toggle input{position:absolute;opacity:0;pointer-events:none}
.att-toggle input[value="Present"]:checked ~ label.present,
.att-toggle .present-wrap input:checked ~ label{background:#059669;color:#fff}
.att-toggle input[value="Absent"]:checked ~ label.absent{background:#DC2626;color:#fff}
.att-toggle .divider{width:1px;background:rgba(5,150,105,0.2)}

/* Submit */
.submit-wrap{padding:20px;border-top:1px solid rgba(5,150,105,0.07);display:flex;justify-content:flex-end;gap:10px}
.btn-submit{padding:12px 32px;background:transparent;border:1.5px solid #059669;border-radius:4px;color:#059669;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#059669,#10B981);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}

.quick-btns{display:flex;gap:8px;padding:12px 20px;border-bottom:1px solid rgba(5,150,105,0.06)}
.quick-btn{padding:6px 14px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.quick-all-present{color:#059669;border-color:rgba(5,150,105,0.25)}
.quick-all-present:hover{background:rgba(5,150,105,0.07)}
.quick-all-absent{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.quick-all-absent:hover{background:rgba(220,38,38,0.06)}

@media(max-width:768px){.att-page{padding:18px 14px}.att-toggle label{padding:7px 10px;font-size:10px}}
</style>

<div class="att-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Instructor</div>
            <h1 class="page-title">Attendance</h1>
        </div>
        <a href="{{ route('teacher.courses.show', $session->courseInstance->course_instance_id) }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    {{-- Session Info --}}
    <div class="session-card">
        <div class="session-grid">
            <div>
                <div class="sg-label">Course</div>
                <div class="sg-val">{{ $session->courseInstance?->courseTemplate?->name ?? '—' }}</div>
            </div>
            <div>
                <div class="sg-label">Session #</div>
                <div class="sg-time">{{ $session->session_number }}</div>
            </div>
            <div>
                <div class="sg-label">Date</div>
                <div class="sg-val">{{ \Carbon\Carbon::parse($session->session_date)->format('l, d M Y') }}</div>
            </div>
            <div>
                <div class="sg-label">Time</div>
                <div class="sg-val">
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                    → {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                </div>
            </div>
            <div>
                <div class="sg-label">Students</div>
                <div class="sg-time">{{ $session->courseInstance?->enrollments?->count() ?? 0 }}</div>
            </div>
            <div>
                <div class="sg-label">Status</div>
                <div class="sg-val" style="color:{{ $session->status === 'Completed' ? '#059669' : '#C47010' }}">
                    {{ $session->status }}
                </div>
            </div>
        </div>
    </div>

    {{-- Timer / Status Banner --}}
    @if($session->status === 'Completed')
    <div class="timer-done">
        ✓ Attendance already saved for this session.
    </div>
    @elseif($isOpen)
    <div class="timer-banner" id="timerBanner">
        <div>
            <div class="timer-text">Attendance window closes in</div>
            <div class="timer-val" id="timerDisplay">{{ $minutesLeft }}:00</div>
        </div>
        <div style="text-align:right;color:rgba(255,255,255,0.7);font-size:11px">
            Window closes at {{ \Carbon\Carbon::parse($session->start_time)->addMinutes(20)->format('H:i') }}
        </div>
    </div>
    @elseif($isLocked)
    <div class="timer-closed">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Attendance window is closed. Only Student Care can modify attendance.
    </div>
    @else
    <div class="timer-warn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Session hasn't started yet or is not today.
    </div>
    @endif

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">
        {{ session('success') }}
    </div>
    @endif

    {{-- Attendance Form --}}
    <div class="att-card">
        <div class="att-card-header">
            <div class="att-card-title">Student Attendance</div>
            @if($isOpen)
            <span style="font-size:10px;color:#059669;letter-spacing:1px;text-transform:uppercase">Window Open</span>
            @endif
        </div>

        @if($isOpen)
        <div class="quick-btns">
            <button type="button" class="quick-btn quick-all-present" onclick="markAll('Present')">
                ✓ All Present
            </button>
            <button type="button" class="quick-btn quick-all-absent" onclick="markAll('Absent')">
                ✕ All Absent
            </button>
        </div>
        @endif

        <form method="POST" action="{{ route('teacher.attendance.store', $session->course_session_id) }}" id="attForm">
            @csrf

            @forelse($session->courseInstance->enrollments as $enrollment)
            @php
                $existing     = $existingAttendance[$enrollment->enrollment_id] ?? null;
                $isRestricted = $enrollment->status === 'Restricted';
            @endphp
            <div class="att-row {{ $isRestricted ? 'restricted' : '' }}">
                <div class="att-avatar {{ $isRestricted ? 'restricted' : '' }}">
                    {{ strtoupper(substr($enrollment->student?->full_name ?? '?', 0, 1)) }}
                </div>
                <div style="flex:1">
                    <div class="att-name">{{ $enrollment->student?->full_name ?? '—' }}</div>
                    <div class="att-phone">
                        {{ $enrollment->student?->phones?->first()?->phone_number ?? '—' }}
                        @if($isRestricted)
                        <span style="color:#DC2626;margin-left:8px;letter-spacing:1px;text-transform:uppercase;font-size:9px">• Restricted</span>
                        @endif
                    </div>
                </div>

                @if($isRestricted || !$isOpen)
                    {{-- Read only --}}
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:11px;letter-spacing:1px;text-transform:uppercase;border-radius:4px;
                        {{ $isRestricted ? 'color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2)' : ($existing === 'Present' ? 'color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2)' : 'color:#7A8A9A;background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15)') }}">
                        {{ $isRestricted ? 'Restricted' : ($existing ?? '—') }}
                    </span>
                    @if(!$isRestricted)
                    <input type="hidden" name="attendance[{{ $enrollment->enrollment_id }}]"
                           value="{{ $existing ?? 'Absent' }}">
                    @endif
                @else
                    {{-- Toggle buttons --}}
                    <div class="att-toggle" id="toggle_{{ $enrollment->enrollment_id }}">
                        <label style="{{ ($existing ?? 'Absent') === 'Present' ? 'background:#059669;color:#fff' : 'color:#059669' }}"
                               onclick="setAttendance({{ $enrollment->enrollment_id }}, 'Present', this)">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Present
                        </label>
                        <div class="divider"></div>
                        <label style="{{ ($existing ?? 'Absent') === 'Absent' ? 'background:#DC2626;color:#fff' : 'color:#DC2626' }}"
                               onclick="setAttendance({{ $enrollment->enrollment_id }}, 'Absent', this)">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Absent
                        </label>
                        <input type="hidden" name="attendance[{{ $enrollment->enrollment_id }}]"
                               id="att_{{ $enrollment->enrollment_id }}"
                               value="{{ $existing ?? 'Absent' }}">
                    </div>
                @endif
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:#AAB8C8;font-size:12px">No students enrolled</div>
            @endforelse

            @if($isOpen)
            <div class="submit-wrap">
                <button type="submit" class="btn-submit">Save Attendance</button>
            </div>
            @endif
        </form>
    </div>

</div>

@if($isOpen)
<script>
// Countdown timer
let seconds = {{ $minutesLeft }} * 60;
const display = document.getElementById('timerDisplay');

function updateTimer() {
    if (seconds <= 0) {
        display.textContent = '00:00';
        document.getElementById('attForm').querySelector('button[type=submit]')?.setAttribute('disabled','true');
        return;
    }
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    display.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    if (seconds <= 120) display.style.color = '#FFD700'; // warning yellow
    seconds--;
    setTimeout(updateTimer, 1000);
}
updateTimer();

function setAttendance(id, status, clickedLabel) {
    document.getElementById('att_' + id).value = status;
    const toggle = document.getElementById('toggle_' + id);
    const labels = toggle.querySelectorAll('label');
    labels.forEach(l => {
        l.style.background = '';
        l.style.color = l === labels[0] ? '#059669' : '#DC2626';
    });
    clickedLabel.style.background = status === 'Present' ? '#059669' : '#DC2626';
    clickedLabel.style.color = '#fff';
}

function markAll(status) {
    document.querySelectorAll('[id^="att_"]').forEach(input => {
        const id = input.id.replace('att_','');
        const toggle = document.getElementById('toggle_' + id);
        if (!toggle) return;
        const labels = toggle.querySelectorAll('label');
        labels.forEach(l => {
            l.style.background = '';
            l.style.color = l === labels[0] ? '#059669' : '#DC2626';
        });
        if (status === 'Present') {
            labels[0].style.background = '#059669';
            labels[0].style.color = '#fff';
        } else {
            labels[1].style.background = '#DC2626';
            labels[1].style.color = '#fff';
        }
        input.value = status;
    });
}
</script>
@endif
@endsection