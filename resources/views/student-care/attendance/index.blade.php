@extends('student-care.layouts.app')
@section('title', 'Attendance')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.att-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}

/* Session Info Card */
.session-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:20px 24px;margin-bottom:20px;position:relative;overflow:hidden;box-shadow:0 2px 12px rgba(27,79,168,0.05);}
.session-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.session-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:18px;}
.sg-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:var(--faint);margin-bottom:4px;}
.sg-val{font-size:13px;color:var(--text);font-weight:500;}
.sg-big{font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--blue);letter-spacing:2px;line-height:1;}

/* Status banners */
.banner{border-radius:6px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;}
.banner-open{background:linear-gradient(135deg,rgba(27,79,168,0.06),rgba(27,79,168,0.02));border:1px solid rgba(27,79,168,0.2);color:var(--blue);}
.banner-closed{background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);}
.banner-future{background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);color:#C47010;}
.banner-done{background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);}
.banner-dot{width:8px;height:8px;border-radius:50%;background:currentColor;flex-shrink:0;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.4;}}

/* Stats row */
.stats-row{display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
.stat-chip{padding:8px 16px;border-radius:20px;font-size:12px;font-weight:500;display:flex;align-items:center;gap:6px;}
.stat-present{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.2);}
.stat-absent{background:var(--red-l);color:var(--red);border:1px solid rgba(220,38,38,0.15);}
.stat-restricted{background:rgba(127,119,221,0.08);color:#7F77DD;border:1px solid rgba(127,119,221,0.2);}
.stat-pending{background:var(--orange-l);color:#C47010;border:1px solid rgba(245,145,30,0.2);}

/* Attendance card */
.att-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(27,79,168,0.05);}
.att-card-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(27,79,168,0.01);}
.att-card-title{font-family:'Bebas Neue',sans-serif;font-size:17px;letter-spacing:2px;color:var(--text);}

/* Quick actions */
.quick-bar{padding:12px 22px;border-bottom:1px solid rgba(27,79,168,0.04);display:flex;gap:8px;flex-wrap:wrap;}
.quick-btn{padding:6px 16px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;}
.btn-all-present{color:var(--green);border-color:rgba(5,150,105,0.3);}
.btn-all-present:hover{background:var(--green-l);}
.btn-all-absent{color:var(--red);border-color:rgba(220,38,38,0.2);}
.btn-all-absent:hover{background:var(--red-l);}

/* Row */
.att-row{display:flex;align-items:center;gap:16px;padding:14px 22px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.att-row:last-child{border-bottom:none;}
.att-row:hover{background:rgba(27,79,168,0.018);}
.att-row.is-restricted{background:rgba(220,38,38,0.02);}

.att-num{font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--faint);letter-spacing:1px;min-width:28px;text-align:center;line-height:1;}
.att-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:15px;flex-shrink:0;}
.att-avatar.normal{background:var(--blue-l);color:var(--blue);}
.att-avatar.restricted{background:var(--red-l);color:var(--red);}
.att-name{font-weight:600;color:var(--text);font-size:13px;}
.att-phone{font-size:10px;color:var(--faint);font-family:monospace;margin-top:2px;}
.att-info{flex:1;}

/* Toggle */
.toggle-wrap{display:flex;border:1.5px solid var(--border);border-radius:6px;overflow:hidden;flex-shrink:0;}
.toggle-btn{padding:9px 18px;font-size:10px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;font-weight:500;border:none;background:transparent;white-space:nowrap;}
.toggle-btn.present{color:var(--green);}
.toggle-btn.present.active{background:var(--green);color:#fff;}
.toggle-btn.absent{color:var(--red);}
.toggle-btn.absent.active{background:var(--red);color:#fff;}
.toggle-divider{width:1px;background:var(--border);}

/* Read-only badge */
.att-badge{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:4px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;font-weight:500;}
.badge-present{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.2);}
.badge-absent{background:rgba(122,138,154,0.08);color:var(--muted);border:1px solid rgba(122,138,154,0.15);}
.badge-restricted{background:rgba(127,119,221,0.08);color:#7F77DD;border:1px solid rgba(127,119,221,0.2);}
.badge-none{background:var(--orange-l);color:#C47010;border:1px solid rgba(245,145,30,0.2);}

/* Footer */
.att-footer{padding:18px 22px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.btn-save{display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:transparent;border:1.5px solid var(--blue);border-radius:4px;color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s;}
.btn-save::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);}
.btn-save:hover::before{transform:scaleX(1);}
.btn-save:hover{color:#fff;}
.btn-save span,.btn-save svg{position:relative;z-index:1;}

/* Progress bar */
.progress-wrap{margin-bottom:20px;}
.progress-label{display:flex;justify-content:space-between;font-size:10px;color:var(--faint);margin-bottom:6px;letter-spacing:1px;text-transform:uppercase;}
.progress-track{background:#F0F0F0;border-radius:4px;height:6px;overflow:hidden;}
.progress-fill{height:6px;border-radius:4px;background:linear-gradient(90deg,var(--blue),var(--green));transition:width 0.4s ease;}

@media(max-width:768px){.att-page{padding:18px 14px;}.toggle-btn{padding:8px 10px;font-size:9px;}.att-row{gap:10px;}}
</style>

<div class="att-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care — Attendance</div>
            <h1 class="page-title">Take Attendance</h1>
        </div>
        <a href="{{ route('student-care.instances.show', $session->courseInstance->course_instance_id) }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Instance
        </a>
    </div>

    @if(session('success'))
    <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">
        {{ session('error') }}
    </div>
    @endif

    {{-- Session Info --}}
    <div class="session-card">
        <div class="session-grid">
            <div>
                <div class="sg-label">Course</div>
                <div class="sg-val">{{ $session->courseInstance?->courseTemplate?->name ?? '—' }}</div>
                @if($session->courseInstance?->level)
                <div style="font-size:10px;color:var(--faint);margin-top:2px;">
                    {{ $session->courseInstance->level->name }}
                    @if($session->courseInstance->sublevel) › {{ $session->courseInstance->sublevel->name }} @endif
                </div>
                @endif
            </div>
            <div>
                <div class="sg-label">Session</div>
                <div class="sg-big">#{{ $session->session_number }}</div>
            </div>
            <div>
                <div class="sg-label">Date</div>
                <div class="sg-val">{{ \Carbon\Carbon::parse($session->session_date)->format('D, d M Y') }}</div>
            </div>
            <div>
                <div class="sg-label">Time</div>
                <div class="sg-val" style="font-family:monospace;font-size:14px;">
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                    <span style="color:var(--faint);"> → </span>
                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                </div>
            </div>
            <div>
                <div class="sg-label">Teacher</div>
                <div class="sg-val">{{ $session->courseInstance?->teacher?->employee?->full_name ?? '—' }}</div>
            </div>
            <div>
                <div class="sg-label">Students</div>
                <div class="sg-big">{{ $session->courseInstance?->enrollments?->count() ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    @if($isCompleted)
    <div class="banner banner-done">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        Attendance saved.
        @if($isOpen) You can still update attendance during session time. @endif
    </div>
    @elseif($isFuture)
    <div class="banner banner-future">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Session hasn't started yet — {{ \Carbon\Carbon::parse($session->session_date)->diffForHumans() }}
    </div>
    @elseif($isOpen)
    <div class="banner banner-open">
        <div class="banner-dot"></div>
        Attendance window is open — session ends at {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
    </div>
    @elseif($isPast)
    <div class="banner banner-closed">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Session has ended. Attendance window is closed.
    </div>
    @else
    <div class="banner banner-future">
        Session is today but not started yet.
    </div>
    @endif

    {{-- Attendance Card --}}
    @php
        $enrollments    = $session->courseInstance->enrollments;
        $presentCount   = collect($existingAttendance)->filter(fn($s) => $s === 'Present')->count();
        $absentCount    = collect($existingAttendance)->filter(fn($s) => $s === 'Absent')->count();
        $restrictedCount= $enrollments->where('status','Restricted')->count();
        $total          = $enrollments->count();
        $markedCount    = $presentCount + $absentCount;
        $progressPct    = $total > 0 ? round(($markedCount / $total) * 100) : 0;
    @endphp

    {{-- Progress --}}
    @if($total > 0)
    <div class="progress-wrap">
        <div class="progress-label">
            <span>Attendance Progress</span>
            <span>{{ $markedCount }}/{{ $total }} marked</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" id="progressFill" style="width:{{ $progressPct }}%;"></div>
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-chip stat-present">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Present: <strong id="countPresent">{{ $presentCount }}</strong>
        </div>
        <div class="stat-chip stat-absent">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Absent: <strong id="countAbsent">{{ $absentCount }}</strong>
        </div>
        @if($restrictedCount > 0)
        <div class="stat-chip stat-restricted">
            Restricted: <strong>{{ $restrictedCount }}</strong>
        </div>
        @endif
        @if($total - $markedCount - $restrictedCount > 0)
        <div class="stat-chip stat-pending">
            Pending: <strong id="countPending">{{ $total - $markedCount - $restrictedCount }}</strong>
        </div>
        @endif
    </div>

    <div class="att-card">
        <div class="att-card-header">
            <div class="att-card-title">Student List</div>
            @if($isOpen)
            <span style="font-size:10px;color:var(--green);letter-spacing:1px;text-transform:uppercase;display:flex;align-items:center;gap:5px;">
                <div style="width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 2s infinite;"></div>
                Live
            </span>
            @endif
        </div>

        @if($isOpen)
        <div class="quick-bar">
            <button type="button" class="quick-btn btn-all-present" onclick="markAll('Present')">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                All Present
            </button>
            <button type="button" class="quick-btn btn-all-absent" onclick="markAll('Absent')">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                All Absent
            </button>
        </div>
        @endif

        <form method="POST" action="{{ route('student-care.attendance.store', $session->course_session_id) }}" id="attForm">
            @csrf

            @forelse($enrollments as $i => $enrollment)
            @php
                $isRestricted = $enrollment->status === 'Restricted';
                $existing     = $existingAttendance[$enrollment->enrollment_id] ?? null;
            @endphp
            <div class="att-row {{ $isRestricted ? 'is-restricted' : '' }}">

                <div class="att-num">{{ $i + 1 }}</div>

                <div class="att-avatar {{ $isRestricted ? 'restricted' : 'normal' }}">
                    {{ strtoupper(substr($enrollment->student?->full_name ?? '?', 0, 1)) }}
                </div>

                <div class="att-info">
                    <div class="att-name">{{ $enrollment->student?->full_name ?? '—' }}</div>
                    <div class="att-phone">
                        {{ $enrollment->student?->phones?->first()?->phone_number ?? '—' }}
                        @if($isRestricted)
                        <span style="color:#7F77DD;margin-left:8px;letter-spacing:1px;text-transform:uppercase;font-size:9px;">🔒 Restricted</span>
                        @endif
                    </div>
                </div>

                @if($isRestricted)
                    {{-- Restricted — always absent, no input --}}
                    <span class="att-badge badge-restricted">🔒 Restricted</span>

                @elseif($isOpen)
                    {{-- Active toggle --}}
                    <div class="toggle-wrap" id="toggle_{{ $enrollment->enrollment_id }}">
                        <button type="button"
                                class="toggle-btn present {{ ($existing ?? 'Absent') === 'Present' ? 'active' : '' }}"
                                onclick="setStatus({{ $enrollment->enrollment_id }}, 'Present')">
                            ✓ Present
                        </button>
                        <div class="toggle-divider"></div>
                        <button type="button"
                                class="toggle-btn absent {{ ($existing ?? 'Absent') === 'Absent' ? 'active' : '' }}"
                                onclick="setStatus({{ $enrollment->enrollment_id }}, 'Absent')">
                            ✕ Absent
                        </button>
                    </div>
                    <input type="hidden"
                           name="attendance[{{ $enrollment->enrollment_id }}]"
                           id="att_{{ $enrollment->enrollment_id }}"
                           value="{{ $existing ?? 'Absent' }}">

                @else
                    {{-- Read-only --}}
                    @if($existing === 'Present')
                    <span class="att-badge badge-present">✓ Present</span>
                    @elseif($existing === 'Absent')
                    <span class="att-badge badge-absent">✕ Absent</span>
                    @else
                    <span class="att-badge badge-none">— Not Marked</span>
                    @endif
                    <input type="hidden"
                           name="attendance[{{ $enrollment->enrollment_id }}]"
                           value="{{ $existing ?? 'Absent' }}">
                @endif

            </div>
            @empty
            <div style="padding:50px;text-align:center;color:var(--faint);font-size:13px;">
                No students enrolled in this course.
            </div>
            @endforelse

            @if($isOpen)
            <div class="att-footer">
                <div style="font-size:11px;color:var(--faint);">
                    <span id="footerStats">{{ $presentCount }} present · {{ $absentCount }} absent</span>
                </div>
                <button type="submit" class="btn-save">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Save Attendance</span>
                </button>
            </div>
            @endif
        </form>
    </div>

</div>

@if($isOpen)
<script>
let presentCount = {{ $presentCount }};
let absentCount  = {{ $absentCount }};
const total      = {{ $total - $restrictedCount }};

function setStatus(id, status) {
    const hidden = document.getElementById('att_' + id);
    const prev   = hidden.value;
    if (prev === status) return;

    // Update counts
    if (prev === 'Present') presentCount--;
    else absentCount--;
    if (status === 'Present') presentCount++;
    else absentCount++;

    hidden.value = status;

    const toggle = document.getElementById('toggle_' + id);
    toggle.querySelector('.present').classList.toggle('active', status === 'Present');
    toggle.querySelector('.absent').classList.toggle('active', status === 'Absent');

    updateStats();
}

function markAll(status) {
    document.querySelectorAll('[id^="att_"]').forEach(input => {
        const id   = input.id.replace('att_', '');
        const prev = input.value;
        if (prev === status) return;

        if (prev === 'Present') presentCount--;
        else absentCount--;
        if (status === 'Present') presentCount++;
        else absentCount++;

        input.value = status;

        const toggle = document.getElementById('toggle_' + id);
        if (toggle) {
            toggle.querySelector('.present').classList.toggle('active', status === 'Present');
            toggle.querySelector('.absent').classList.toggle('active', status === 'Absent');
        }
    });
    updateStats();
}

function updateStats() {
    document.getElementById('countPresent').textContent = presentCount;
    document.getElementById('countAbsent').textContent  = absentCount;
    document.getElementById('footerStats').textContent  = `${presentCount} present · ${absentCount} absent`;

    const marked = presentCount + absentCount;
    const pct    = total > 0 ? Math.round((marked / total) * 100) : 0;
    document.getElementById('progressFill').style.width = pct + '%';

    const pendingEl = document.getElementById('countPending');
    if (pendingEl) pendingEl.textContent = total - marked;
}
</script>
@endif

@endsection