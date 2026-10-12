@extends('student-care.layouts.app')
@section('title', 'Attendance')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-dk:#C47010;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-dk:#15803D;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--border:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box;}
.att-page{min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);font-weight:600;margin-bottom:7px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:40px;letter-spacing:2px;color:var(--text);margin:0;line-height:0.95;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid rgba(27,79,168,0.1);flex-wrap:wrap;gap:12px;}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:11px;color:var(--muted);font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;font-weight:600;box-shadow:var(--glass-sh);transition:all 0.3s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}

.session-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--border);border-radius:18px;padding:20px 24px;margin-bottom:20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh);}
.session-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:18px;}
.sg-label{font-size:8px;letter-spacing:2.5px;text-transform:uppercase;color:var(--faint);margin-bottom:4px;font-weight:600;}
.sg-val{font-size:13px;color:var(--text);font-weight:500;}
.sg-big{font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--blue);letter-spacing:2px;line-height:1;}

.banner{border-radius:14px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);box-shadow:var(--glass-sh);}
.banner-open{background:linear-gradient(90deg,rgba(27,79,168,0.10),rgba(27,79,168,0.02));border-color:rgba(27,79,168,0.25);color:var(--blue);}
.banner-closed{background:linear-gradient(90deg,rgba(220,38,38,0.08),rgba(220,38,38,0.02));border-color:rgba(220,38,38,0.25);color:var(--red);}
.banner-future{background:linear-gradient(90deg,rgba(245,145,30,0.10),rgba(245,145,30,0.02));border-color:rgba(245,145,30,0.35);color:var(--orange-dk);}
.banner-done{background:linear-gradient(90deg,rgba(5,150,105,0.10),rgba(5,150,105,0.02));border-color:rgba(21,128,61,0.3);color:var(--green-dk);}
.banner-dot{width:8px;height:8px;border-radius:50%;background:currentColor;flex-shrink:0;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.4;}}

.stats-row{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;}
.stat-chip{padding:8px 16px;border-radius:20px;font-size:12px;font-weight:500;display:flex;align-items:center;gap:6px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.stat-present{background:rgba(5,150,105,0.08);color:var(--green-dk);border-color:rgba(21,128,61,0.22);}
.stat-absent{background:rgba(220,38,38,0.06);color:var(--red);border-color:rgba(220,38,38,0.18);}
.stat-restricted{background:rgba(127,119,221,0.08);color:var(--purple);border-color:rgba(127,119,221,0.22);}
.stat-pending{background:rgba(245,145,30,0.08);color:var(--orange-dk);border-color:rgba(245,145,30,0.25);}

.att-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh);}
.att-card-header{padding:16px 22px;border-bottom:1px solid rgba(27,79,168,0.08);display:flex;align-items:center;justify-content:space-between;}
.att-card-title{font-family:'Bebas Neue',sans-serif;font-size:17px;letter-spacing:2px;color:var(--text);}

.quick-bar{padding:12px 22px;border-bottom:1px solid rgba(27,79,168,0.06);display:flex;gap:8px;flex-wrap:wrap;}
.quick-btn{padding:6px 16px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:600;transition:all 0.2s;}
.btn-all-present{color:var(--green-dk);border-color:rgba(21,128,61,0.3);}
.btn-all-present:hover{background:rgba(5,150,105,0.08);}
.btn-all-absent{color:var(--red);border-color:rgba(220,38,38,0.22);}
.btn-all-absent:hover{background:rgba(220,38,38,0.06);}

.att-row{display:flex;align-items:center;gap:16px;padding:14px 22px;border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s;}
.att-row:last-child{border-bottom:none;}
.att-row:hover{background:rgba(27,79,168,0.03);}
.att-row.is-restricted{background:rgba(220,38,38,0.03);}

.att-num{font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--faint);letter-spacing:1px;min-width:28px;text-align:center;line-height:1;}
.att-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:15px;flex-shrink:0;}
.att-avatar.normal{background:rgba(27,79,168,0.1);color:var(--blue);}
.att-avatar.restricted{background:rgba(220,38,38,0.1);color:var(--red);}
.att-name{font-weight:600;color:var(--text);font-size:13px;}
.att-phone{font-size:10px;color:var(--faint);font-family:monospace;margin-top:2px;}
.att-info{flex:1;}

.toggle-wrap{display:flex;border:1px solid rgba(27,79,168,0.2);border-radius:8px;overflow:hidden;flex-shrink:0;background:rgba(255,255,255,0.4);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.toggle-btn{padding:9px 18px;font-size:10px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;font-weight:500;border:none;background:transparent;white-space:nowrap;}
.toggle-btn.present{color:var(--green-dk);}
.toggle-btn.present.active{background:var(--green-dk);color:#fff;}
.toggle-btn.absent{color:var(--red);}
.toggle-btn.absent.active{background:var(--red);color:#fff;}
.toggle-divider{width:1px;background:rgba(27,79,168,0.2);}

.att-badge{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:20px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;border:1px solid;}
.badge-present{background:rgba(5,150,105,0.08);color:var(--green-dk);border-color:rgba(21,128,61,0.22);}
.badge-absent{background:rgba(220,38,38,0.06);color:var(--red);border-color:rgba(220,38,38,0.18);}
.badge-restricted{background:rgba(127,119,221,0.08);color:var(--purple);border-color:rgba(127,119,221,0.22);}
.badge-none{background:rgba(245,145,30,0.08);color:var(--orange-dk);border-color:rgba(245,145,30,0.25);}

.att-footer{padding:18px 22px;border-top:1px solid rgba(27,79,168,0.08);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.btn-save{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;box-shadow:0 10px 24px rgba(27,79,168,0.28);transition:transform 0.2s,box-shadow 0.2s;}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.34);}
.btn-save span,.btn-save svg{position:relative;z-index:1;}

.progress-wrap{margin-bottom:20px;}
.progress-label{display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-bottom:6px;letter-spacing:1px;text-transform:uppercase;font-weight:600;}
.progress-track{background:rgba(27,79,168,0.08);border-radius:6px;height:6px;overflow:hidden;}
.progress-fill{height:6px;border-radius:6px;background:linear-gradient(90deg,var(--blue),var(--blue-2));transition:width 0.6s cubic-bezier(.16,1,.3,1);}

.flash{padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.flash-success{background:rgba(5,150,105,0.08);border-color:rgba(21,128,61,0.22);color:var(--green-dk);}
.flash-error{background:rgba(220,38,38,0.06);border-color:rgba(220,38,38,0.2);color:var(--red);}

@media(max-width:768px){.toggle-btn{padding:8px 10px;font-size:9px;}.att-row{gap:10px;}}
@media(max-width:480px){.page-header{flex-direction:column;align-items:flex-start;}}
</style>

<div class="att-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care — Attendance</div>
            <h1 class="page-title">View Attendance</h1>
        </div>
        <a href="{{ route('student-care.instances.show', $session->courseInstance->course_instance_id) }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Instance
        </a>
    </div>

    @if(session('success'))
    <div class="flash flash-success">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">
        {{ session('error') }}
    </div>
    @endif

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

    @if($isCompleted)
    <div class="banner banner-done">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        Attendance saved.
        @if($isOpen) Teacher can still update attendance during session time. @endif
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

    @php
        $enrollments    = $session->courseInstance->enrollments;
        $presentCount   = collect($existingAttendance)->filter(fn($s) => $s === 'Present')->count();
        $absentCount    = collect($existingAttendance)->filter(fn($s) => $s === 'Absent')->count();
        $restrictedCount= $enrollments->where('status','Restricted')->count();
        $total          = $enrollments->count();
        $markedCount    = $presentCount + $absentCount;
        $progressPct    = $total > 0 ? round(($markedCount / $total) * 100) : 0;
    @endphp

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
            <span style="font-size:10px;color:var(--green-dk);letter-spacing:1px;font-weight:600;text-transform:uppercase;display:flex;align-items:center;gap:5px;">
                <div style="width:6px;height:6px;border-radius:50%;background:var(--green-dk);animation:pulse 2s infinite;"></div>
                Live
            </span>
            @endif
        </div>

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
                        <span style="color:var(--purple);margin-left:8px;letter-spacing:1px;text-transform:uppercase;font-size:9px;">🔒 Restricted</span>
                        @endif
                    </div>
                </div>

                @if($isRestricted)
                    <span class="att-badge badge-restricted">🔒 Restricted</span>

                @elseif($isOpen)
                    <input type="hidden"
                           name="attendance[{{ $enrollment->enrollment_id }}]"
                           id="att_{{ $enrollment->enrollment_id }}"
                           value="{{ $existing ?? 'Absent' }}">

                @else
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

        </form>
    </div>

</div>

@endsection