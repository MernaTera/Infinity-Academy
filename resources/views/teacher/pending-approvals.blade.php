@extends('teacher.layouts.app')
@section('title', 'Pending Approvals')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--border:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box}
.pa-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);position:relative;overflow:hidden}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}

.page-eyebrow{font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:6px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:40px;letter-spacing:2px;color:var(--text);margin:0;line-height:1}
.page-sub{font-size:12px;color:var(--faint);margin-top:6px}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px}

.btn-back{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:11px;color:var(--muted);font-size:10px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all 0.25s}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none}

.alert{padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:10px}
.alert-success{background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.2);color:var(--blue)}
.alert-error{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);color:var(--red)}

.hero{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--border);border-radius:18px;padding:18px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh);margin-bottom:28px}
.hero::before{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.08)}
.hero-label{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.hero-title{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:1px;color:var(--kc,var(--blue));line-height:1}
.hero-sub{font-size:8px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600;margin-top:4px}

.sec-label{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;display:block;padding-bottom:8px;border-bottom:1px solid rgba(27,79,168,0.1);font-weight:600}

.pending-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--border);border-radius:18px;overflow:hidden;margin-bottom:16px;box-shadow:var(--glass-sh);position:relative;transition:transform 0.2s,box-shadow 0.2s}
.pending-card:hover{transform:translateY(-3px);box-shadow:0 16px 38px rgba(23,45,90,0.13)}
.pending-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)}

.card-header{padding:18px 22px;border-bottom:1px solid rgba(27,79,168,0.06);display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap}
.course-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--text)}
.course-sub{font-size:11px;color:var(--muted);margin-top:3px}
.exceed-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);border-radius:20px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--red);font-weight:600;white-space:nowrap}

.card-body{padding:16px 22px}
.info-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}
.info-item{}
.info-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:4px}
.info-val{font-size:12px;color:var(--text);font-weight:500}

.schedule-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}
.schedule-pill{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.15);border-radius:20px;font-size:11px;color:var(--blue);font-weight:500}
.schedule-pill svg{opacity:0.6}

.contract-box{background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.15);border-left:3px solid var(--red);border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:12px;color:var(--red)}
.contract-box strong{font-weight:600}

.card-footer{padding:14px 22px;border-top:1px solid rgba(27,79,168,0.06);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}

.reject-form{display:flex;align-items:center;gap:8px;flex:1}
.reject-input{flex:1;min-width:180px;padding:9px 12px;border:1px solid rgba(220,38,38,0.2);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--text);background:rgba(255,255,255,0.6);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);outline:none;transition:border-color 0.2s}
.reject-input:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(220,38,38,0.07)}

.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;font-size:10px;letter-spacing:2px;text-transform:uppercase;border-radius:10px;border:1.5px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:500;transition:all 0.25s;white-space:nowrap;position:relative;overflow:hidden}
.btn span{position:relative;z-index:1}
.btn svg{position:relative;z-index:1}

.btn-approve{color:var(--blue);border-color:rgba(27,79,168,0.35)}
.btn-approve::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),var(--blue-2));transform:scaleX(0);transform-origin:left;transition:transform 0.35s cubic-bezier(0.16,1,0.3,1)}
.btn-approve:hover::before{transform:scaleX(1)}
.btn-approve:hover{color:#fff;border-color:var(--blue)}

.btn-reject{color:var(--red);border-color:rgba(220,38,38,0.3)}
.btn-reject:hover{background:rgba(220,38,38,0.06)}

.empty{text-align:center;padding:80px 24px}
.empty-icon{width:64px;height:64px;border-radius:50%;background:rgba(27,79,168,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:4px;color:var(--text);margin-bottom:6px}
.empty-sub{font-size:12px;color:var(--faint)}

@media(max-width:768px){
    .pa-page{padding:18px 14px}
    .info-grid{grid-template-columns:1fr 1fr}
    .card-footer{flex-direction:column;align-items:stretch}
    .reject-form{flex-direction:column}
}
</style>

<div class="pa-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Teacher Panel</div>
            <h1 class="page-title">Pending Approvals</h1>
            <div class="page-sub">{{ now()->format('l, d M Y') }}</div>
        </div>
        <a href="{{ route('teacher.dashboard') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="hero">
        <div style="position:relative;z-index:1;">
            <div class="hero-label">Action Required</div>
            <div class="hero-title">{{ $pending->count() }} Course{{ $pending->count() !== 1 ? 's' : '' }} Awaiting Your Decision</div>
            <div class="hero-sub">These courses exceed your contract session limit — approve to accept, reject to decline.</div>
        </div>
    </div>

    <div class="sec-label">Pending Course Approvals</div>

    @forelse($pending as $instance)
    @php
        $schedules = $instance->instanceSchedules;
        $pairLabels = ['sat_tue' => 'Sat & Tue', 'sun_wed' => 'Sun & Wed', 'mon_thu' => 'Mon & Thu'];
        $sessions   = (int) ceil($instance->total_hours / $instance->session_duration);
    @endphp

    <div class="pending-card">
        <div class="card-header">
            <div>
                <div class="course-name">{{ $instance->courseTemplate?->name ?? '—' }}</div>
                <div class="course-sub">
                    {{ $instance->patch?->name ?? '—' }}
                    @if($instance->level) · {{ $instance->level->name }} @endif
                    @if($instance->sublevel) / {{ $instance->sublevel->name }} @endif
                </div>
            </div>
            <span class="exceed-badge">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Exceeds Contract Limit
            </span>
        </div>

        <div class="card-body">

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Type</div>
                    <div class="info-val">{{ $instance->type }} · {{ $instance->delivery_mood }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Hours</div>
                    <div class="info-val">{{ $instance->total_hours }} hrs</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Sessions</div>
                    <div class="info-val" style="color:var(--red);font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;">{{ $sessions }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Duration</div>
                    <div class="info-val">{{ $instance->session_duration }} hr / session</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Start Date</div>
                    <div class="info-val">{{ \Carbon\Carbon::parse($instance->start_date)->format('d M Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">End Date</div>
                    <div class="info-val">{{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Room</div>
                    <div class="info-val">{{ $instance->room?->name ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Capacity</div>
                    <div class="info-val">{{ $instance->capacity }} students</div>
                </div>
            </div>

            @if($schedules->isNotEmpty())
            <div class="info-label" style="margin-bottom:8px;">Schedule</div>
            <div class="schedule-row">
                @foreach($schedules as $sch)
                <span class="schedule-pill">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $pairLabels[$sch->day_of_week] ?? $sch->day_of_week }}
                    @if($sch->start_time)
                        · {{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }}
                        @if($sch->timeSlot)
                        → {{ \Carbon\Carbon::parse($sch->timeSlot->end_time)->format('H:i') }}
                        @endif
                    @endif
                </span>
                @endforeach
            </div>
            @endif

            <div class="contract-box">
                ⚠ <strong>Contract Limit Exceeded:</strong>
                Accepting this course will add <strong>{{ $sessions }} sessions</strong> beyond your current contract allowance.
                By approving, you confirm your availability to teach these additional sessions.
            </div>

        </div>

        <div class="card-footer">
            <form method="POST" action="{{ route('teacher.instance.reject', $instance->course_instance_id) }}" class="reject-form">
                @csrf @method('PATCH')
                <input type="text" name="reason" class="reject-input" placeholder="Reason for rejection (optional)...">
                <button type="submit" class="btn btn-reject">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <span>Reject</span>
                </button>
            </form>

            <form method="POST" action="{{ route('teacher.instance.approve', $instance->course_instance_id) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-approve">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Approve & Generate Sessions</span>
                </button>
            </form>
        </div>
    </div>
    @empty

    <div class="pending-card">
        <div class="empty">
            <div class="empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="empty-title">All Clear</div>
            <div class="empty-sub">No pending approvals — you're up to date.</div>
        </div>
    </div>

    @endforelse

</div>
@endsection
