@extends('student-care.layouts.app')
@section('title', 'Student Care Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
        --green:#059669; --green-dk:#15803D; --purple:#7C3AED; --red:#DC2626;
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --card:rgba(255,255,255,0.5); --border:rgba(255,255,255,0.6);
        --glass-sh:0 8px 30px rgba(23,45,90,0.07);
        --bg:#F8F6F2;
    }
    * { box-sizing:border-box; }

    .sc-dash{ position:relative; overflow:hidden; margin:-30px; padding:30px 34px 44px; min-height:calc(100vh - 62px); background:var(--bg); color:var(--text); font-family:'DM Sans',sans-serif; }
    @media (max-width:600px){ .sc-dash{ margin:-30px; padding:18px 16px 32px; } }

    .orb{ position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2{ width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3{ width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }
    .patch-bar{display:flex;align-items:center;gap:20px;flex-wrap:wrap;padding:16px 22px;border-radius:16px;margin-bottom:22px;}
    .patch-bar-icon{width:42px;height:42px;border-radius:13px;background:linear-gradient(135deg,var(--blue),var(--blue-2));display:grid;place-items:center;flex-shrink:0;box-shadow:0 8px 18px rgba(27,79,168,0.28);}
    .patch-bar-info{flex:1;min-width:180px;}
    .patch-bar-name{font-family:'Bebas Neue',sans-serif;font-size:19px;letter-spacing:2px;color:var(--text);line-height:1;}
    .patch-bar-dates{font-size:11px;color:var(--muted);margin-top:5px;}
    .patch-bar-dates b{color:var(--blue);font-weight:600;}
    .patch-bar-prog{flex:1;min-width:200px;max-width:340px;}
    .patch-bar-prog-top{display:flex;justify-content:space-between;align-items:baseline;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);font-weight:600;margin-bottom:7px;}
    .patch-bar-prog-pct{font-family:'Bebas Neue',sans-serif;font-size:15px;color:var(--blue);letter-spacing:1px;}
    .patch-bar-track{background:rgba(27,79,168,0.1);border-radius:5px;height:7px;overflow:hidden;}
    .patch-bar-fill{height:7px;border-radius:5px;background:linear-gradient(90deg,var(--blue),var(--blue-2));transition:width .6s ease;}
    @media (max-width:600px){ .patch-bar-prog{max-width:none;} }

    .shift-chip { display:inline-flex; align-items:center; gap:9px; padding:9px 15px; border-radius:13px; margin-bottom:22px; }
    .shift-chip .sc-k { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); font-weight:600; }
    .shift-chip .sc-v { font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:1px; color:var(--text); line-height:1; margin-top:2px; }

    .glass{ background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--border); box-shadow:var(--glass-sh); }

    .page-header{ display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:16px; position:relative; z-index:1; }
    .page-eyebrow{ font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); font-weight:600; margin-bottom:7px; }
    .page-title{ font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:2px; color:var(--text); margin:0; line-height:0.95; }
    .page-sub{ font-size:12px; color:var(--muted); margin-top:7px; }

    .patch-banner{ background:linear-gradient(120deg,var(--blue),var(--blue-2)); border-radius:18px; padding:20px 24px; margin-bottom:22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; position:relative; overflow:hidden; z-index:1; box-shadow:0 10px 24px rgba(27,79,168,0.28); }
    .patch-banner::before{ content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.06); }
    .pb-label{ font-size:9px; letter-spacing:3px; text-transform:uppercase; color:rgba(255,255,255,0.6); margin-bottom:4px; }
    .pb-name{ font-family:'Bebas Neue',sans-serif; font-size:24px; letter-spacing:3px; color:#fff; }
    .pb-dates{ font-size:11px; color:rgba(255,255,255,0.72); margin-top:2px; }
    .pb-stats{ display:flex; gap:22px; flex-wrap:wrap; }
    .pb-stat-val{ font-family:'Bebas Neue',sans-serif; font-size:24px; color:#fff; letter-spacing:1px; line-height:1; }
    .pb-stat-label{ font-size:9px; color:rgba(255,255,255,0.5); letter-spacing:1px; text-transform:uppercase; margin-top:2px; }
    .pb-progress{ flex:1; max-width:260px; }
    .pb-prog-label{ display:flex; justify-content:space-between; font-size:10px; color:rgba(255,255,255,0.6); margin-bottom:6px; }
    .pb-prog-track{ background:rgba(255,255,255,0.15); border-radius:6px; height:6px; overflow:hidden; }
    .pb-prog-fill{ height:6px; border-radius:6px; background:linear-gradient(90deg,var(--orange),#FFB347); }

    .sec-label{ font-size:10px; letter-spacing:3px; text-transform:uppercase; color:var(--muted); font-weight:600; margin:26px 2px 12px; display:block; position:relative; z-index:1; }
    .two-col{ display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; position:relative; z-index:1; }

    .kpi-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; position:relative; z-index:1; }
    .kpi-grid-3{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px; position:relative; z-index:1; }
    .kpi-card{ position:relative; overflow:hidden; border-radius:18px; padding:20px; text-decoration:none; display:block; color:inherit; background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--border); box-shadow:var(--glass-sh); transition:transform .2s, box-shadow .2s; }
    .kpi-card:hover{ transform:translateY(-3px); box-shadow:0 16px 38px rgba(23,45,90,0.13); text-decoration:none; color:inherit; }
    .kpi-label{ font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:6px; }
    .kpi-val{ font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:1px; color:var(--kc,var(--blue)); line-height:1; }
    .kpi-sub{ font-size:11px; color:var(--faint); margin-top:6px; }

    .alert-list{ display:flex; flex-direction:column; gap:8px; margin-bottom:20px; position:relative; z-index:1; }
    .alert-item{ display:flex; align-items:flex-start; gap:10px; padding:14px 18px; border-radius:14px; font-size:12px; line-height:1.5; }
    .alert-dot{ width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; margin-top:4px; }
    .alert-danger{ background:rgba(220,38,38,0.07); border:1px solid rgba(220,38,38,0.16); color:var(--red); }
    .alert-warning{ background:rgba(245,145,30,0.08); border:1px solid rgba(245,145,30,0.2); color:var(--orange-dk); }
    .alert-info{ background:rgba(27,79,168,0.07); border:1px solid rgba(27,79,168,0.16); color:var(--blue); }
    .alert-link{ color:inherit; font-weight:600; margin-left:4px; text-decoration:underline; }

    .dash-card{ border-radius:20px; overflow:hidden; margin-bottom:16px; background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--border); box-shadow:var(--glass-sh); }
    .dash-card-header{ padding:18px 22px 14px; border-bottom:1px solid rgba(27,79,168,0.06); display:flex; align-items:center; justify-content:space-between; }
    .dash-card-title{ font-family:'Bebas Neue',sans-serif; font-size:17px; letter-spacing:2px; color:var(--text); }
    .dash-card-sub{ font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--faint); font-weight:600; }
    .dash-card-body{ padding:16px 22px; }

    .inst-row{ display:flex; align-items:center; gap:13px; padding:12px 22px; border-bottom:1px solid rgba(27,79,168,0.06); transition:background .15s; text-decoration:none; color:inherit; }
    .inst-row:last-child{ border-bottom:none; }
    .inst-row:hover{ background:rgba(27,79,168,0.04); text-decoration:none; }
    .inst-avatar{ width:40px; height:40px; border-radius:12px; background:rgba(27,79,168,0.10); display:grid; place-items:center; flex-shrink:0; }
    .inst-name{ font-size:13px; color:var(--text); font-weight:600; flex:1; }
    .inst-meta{ font-size:11px; color:var(--muted); margin-top:2px; }

    .ret-row{ display:flex; align-items:center; gap:13px; padding:12px 22px; border-bottom:1px solid rgba(27,79,168,0.06); }
    .ret-row:last-child{ border-bottom:none; }
    .ret-name{ font-size:13px; color:var(--text); font-weight:600; flex:1; }
    .ret-course{ font-size:11px; color:var(--muted); margin-top:2px; }
    .ret-badge{ display:inline-flex; align-items:center; gap:4px; padding:4px 11px; border-radius:20px; font-size:9px; letter-spacing:0.3px; font-weight:600; }
    .ret-last{ color:var(--red); background:rgba(220,38,38,0.10); }
    .ret-low{ color:var(--orange-dk); background:rgba(245,145,30,0.12); }

    .qa-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:24px; position:relative; z-index:1; }
    .qa-btn{ display:flex; flex-direction:column; align-items:center; gap:8px; padding:18px 12px; border-radius:18px; text-decoration:none; transition:transform .2s, box-shadow .2s; color:var(--muted); background:var(--card); backdrop-filter:blur(22px) saturate(165%); -webkit-backdrop-filter:blur(22px) saturate(165%); border:1px solid var(--border); box-shadow:var(--glass-sh); }
    .qa-btn:hover{ transform:translateY(-3px); box-shadow:0 16px 38px rgba(23,45,90,0.13); text-decoration:none; color:var(--blue); }
    .qa-icon{ width:38px; height:38px; border-radius:12px; display:grid; place-items:center; background:rgba(27,79,168,0.10); }
    .qa-label{ font-size:10px; letter-spacing:1px; text-transform:uppercase; text-align:center; }

    .cap-track{ background:rgba(27,79,168,0.08); border-radius:6px; height:6px; overflow:hidden; margin-top:4px; width:60px; }
    .cap-fill{ height:6px; border-radius:6px; }

    @media(max-width:1024px){ .kpi-grid,.kpi-grid-3{ grid-template-columns:repeat(2,1fr); } .two-col{ grid-template-columns:1fr; } .qa-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:640px){ .kpi-grid,.kpi-grid-3{ grid-template-columns:1fr 1fr; } }
</style>

<div class="sc-dash">

    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care</div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">{{ now()->format('l, d M Y') }}</p>
        </div>
    @if(!empty($me) && ($me->work_start_time || $me->work_end_time))
    <div class="shift-chip glass">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <div>
            <div class="sc-k">Your Shift</div>
            <div class="sc-v">
                {{ $me->work_start_time ? \Carbon\Carbon::parse($me->work_start_time)->format('g:i A') : '—' }}
                <span style="color:var(--faint);">→</span>
                {{ $me->work_end_time ? \Carbon\Carbon::parse($me->work_end_time)->format('g:i A') : '—' }}
            </div>
        </div>
    </div>
    @endif
    </div>

    @if($currentPatch)
    @php
        $pStart   = \Carbon\Carbon::parse($currentPatch->start_date);
        $pEnd     = \Carbon\Carbon::parse($currentPatch->end_date);
        $pTotal   = max(1, $pStart->diffInDays($pEnd));
        $pElapsed = max(0, min($pTotal, $pStart->diffInDays(now())));
        $pPct     = round($pElapsed / $pTotal * 100);
        $daysLeft = max(0, (int)now()->diffInDays($pEnd, false));
    @endphp
    <div class="patch-bar glass">
        <div class="patch-bar-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="patch-bar-info">
            <div class="patch-bar-name">{{ $currentPatch->name }}</div>
            <div class="patch-bar-dates">{{ $pStart->format('d M Y') }} → {{ $pEnd->format('d M Y') }} · <b>{{ $daysLeft }} days left</b></div>
        </div>
        <div class="patch-bar-prog">
            <div class="patch-bar-prog-top"><span>Patch Progress</span><span class="patch-bar-prog-pct">{{ $pPct }}%</span></div>
            <div class="patch-bar-track"><div class="patch-bar-fill" style="width:{{ $pPct }}%"></div></div>
        </div>
    </div>
    @endif

    @if($expiredPostponements->isNotEmpty() || $endingSoon->isNotEmpty() || $restrictedStudents > 0 || $expiringSoon->isNotEmpty())
    <div class="alert-list">
        @if($expiredPostponements->isNotEmpty())
        <div class="alert-item alert-danger">
            <div class="alert-dot"></div>
            <div>
                <strong>{{ $expiredPostponements->count() }}</strong> postponement(s) have passed their return date —
                <a href="{{ route('student-care.postponed') }}" class="alert-link">Review Now →</a>
            </div>
        </div>
        @endif
        @if($expiringSoon->isNotEmpty())
        <div class="alert-item alert-warning">
            <div class="alert-dot"></div>
            <div>
                <strong>{{ $expiringSoon->count() }}</strong> student(s) returning within 7 days —
                <a href="{{ route('student-care.postponed') }}" class="alert-link">View Postponed →</a>
            </div>
        </div>
        @endif
        @if($endingSoon->isNotEmpty())
        <div class="alert-item alert-warning">
            <div class="alert-dot"></div>
            <div>
                <strong>{{ $endingSoon->count() }}</strong> course(s) ending within 7 days
            </div>
        </div>
        @endif
        @if($restrictedStudents > 0)
        <div class="alert-item alert-info">
            <div class="alert-dot"></div>
            <div>
                <strong>{{ $restrictedStudents }}</strong> students currently restricted from attendance —
                <a href="{{ route('student-care.outstanding') }}" class="alert-link">View Outstanding →</a>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="qa-grid">
        <a href="{{ route('student-care.instances.create') }}?create=1" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></div>
            <span class="qa-label">New Course</span>
        </a>
        <a href="{{ route('student-care.waiting-list') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <span class="qa-label">Waiting List</span>
        </a>
        <a href="{{ route('student-care.postponed') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <span class="qa-label">Postponed</span>
        </a>
        <a href="{{ route('student-care.outstanding') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <span class="qa-label">Outstanding</span>
        </a>
    </div>

    <span class="sec-label">Academic Status</span>
    <div class="kpi-grid" style="margin-bottom:24px">
        <a href="{{ route('student-care.instances') }}" class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Active Courses</div>
            <div class="kpi-val">{{ $activeCourses }}</div>
        </a>
        <a href="{{ route('student-care.instances') }}" class="kpi-card" style="--kc:#1B6FA8">
            <div class="kpi-label">Upcoming Courses</div>
            <div class="kpi-val">{{ $upcomingCourses }}</div>
        </a>
        <a href="{{ route('students.index') }}" class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Students</div>
            <div class="kpi-val">{{ $totalStudents }}</div>
        </a>
        <a href="{{ route('student-care.outstanding') }}" class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $restrictedStudents }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </a>
    </div>

    <div class="kpi-grid-3" style="margin-bottom:24px">
        <a href="{{ route('student-care.postponed') }}" class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Postponed</div>
            <div class="kpi-val">{{ $postponedStudents }}</div>
            <div class="kpi-sub">active postponements</div>
        </a>
        <a href="{{ route('student-care.waiting-list') }}" class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Waiting List</div>
            <div class="kpi-val">{{ $waitingList }}</div>
            <div class="kpi-sub">students waiting</div>
        </a>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Expiring Soon</div>
            <div class="kpi-val">{{ $expiringSoon->count() }}</div>
            <div class="kpi-sub">postponements within 7 days</div>
        </div>
    </div>

    <div class="two-col">

        <div>
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Active Courses</div>
                    <a href="{{ route('student-care.instances') }}" style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#1B4FA8;text-decoration:none">View All →</a>
                </div>
                @forelse($recentInstances as $inst)
                @php
                    $enrolled = $inst->enrollments->count();
                    $cap      = $inst->capacity;
                    $cpct     = $cap > 0 ? min(100, round($enrolled/$cap*100)) : 0;
                    $ccolor   = $enrolled >= $cap ? '#DC2626' : ($cpct >= 80 ? '#C47010' : '#1B4FA8');
                @endphp
                <a href="{{ route('student-care.instances.show', $inst->course_instance_id) }}" class="inst-row">
                    <div class="inst-avatar">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="inst-name">{{ $inst->courseTemplate?->name ?? '—' }}</div>
                        <div class="inst-meta">
                            {{ $inst->teacher?->employee?->full_name ?? '—' }}
                            · {{ $inst->type }}
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-size:12px;color:{{ $ccolor }};font-weight:500">{{ $enrolled }}/{{ $cap }}</div>
                        <div class="cap-track">
                            <div class="cap-fill" style="width:{{ $cpct }}%;background:{{ $ccolor }}"></div>
                        </div>
                    </div>
                </a>
                @empty
                <div style="padding:30px;text-align:center;color:#AAB8C8;font-size:12px">No active courses</div>
                @endforelse
            </div>

            @if($endingSoon->isNotEmpty())
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Ending Soon</div>
                    <span class="dash-card-sub">Within 7 days</span>
                </div>
                @foreach($endingSoon as $inst)
                <div class="inst-row">
                    <div style="flex:1">
                        <div class="inst-name">{{ $inst->courseTemplate?->name ?? '—' }}</div>
                        <div class="inst-meta">{{ $inst->teacher?->employee?->full_name ?? '—' }}</div>
                    </div>
                    <div style="text-align:right;font-size:11px;color:#DC2626;font-weight:500">
                        {{ \Carbon\Carbon::parse($inst->end_date)->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Near Completion — Group</div>
                    <span class="dash-card-sub">Last session</span>
                </div>
                @forelse($nearCompletionGroup as $enr)
                <div class="ret-row">
                    <div style="flex:1;min-width:0">
                        <div class="ret-name">{{ $enr->student?->full_name ?? '—' }}</div>
                        <div class="ret-course">{{ $enr->courseInstance?->courseTemplate?->name ?? '—' }}</div>
                    </div>
                    <span class="ret-badge ret-last">Last Session</span>
                </div>
                @empty
                <div style="padding:24px;text-align:center;color:#AAB8C8;font-size:12px">No students near completion</div>
                @endforelse
            </div>

            @if($nearCompletionPrivate->isNotEmpty())
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Near Completion — Private</div>
                    <span class="dash-card-sub">≤ 4 hours remaining</span>
                </div>
                @foreach($nearCompletionPrivate as $enr)
                <div class="ret-row">
                    <div style="flex:1;min-width:0">
                        <div class="ret-name">{{ $enr->student?->full_name ?? '—' }}</div>
                        <div class="ret-course">{{ $enr->courseInstance?->courseTemplate?->name ?? '—' }}</div>
                    </div>
                    <span class="ret-badge ret-low">
                        {{ $enr->hours_remaining }}h left
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            @if($expiredPostponements->isNotEmpty())
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Overdue Returns</div>
                    <a href="{{ route('student-care.postponed') }}" style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#DC2626;text-decoration:none">Handle →</a>
                </div>
                @foreach($expiredPostponements->take(5) as $pp)
                <div class="ret-row">
                    <div style="flex:1;min-width:0">
                        <div class="ret-name">{{ $pp->enrollment?->student?->full_name ?? '—' }}</div>
                        <div class="ret-course">{{ $pp->enrollment?->courseInstance?->courseTemplate?->name ?? '—' }}</div>
                    </div>
                    <div style="text-align:right;font-size:10px;color:#DC2626;font-weight:500">
                        Was due {{ \Carbon\Carbon::parse($pp->expected_return_date)->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
<script>
document.querySelectorAll('.kpi-val').forEach(el => {
    const raw = el.textContent.trim();
    const suffix = raw.includes('%') ? '%' : '';
    const num = parseFloat(raw.replace(/[^0-9.]/g, ''));
    if (isNaN(num)) return;
    if (num === 0) { el.textContent = '0' + suffix; return; }
    const dur = 700, start = performance.now();
    (function tick(now) {
        const pct = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        el.textContent = Math.round(num * ease).toLocaleString() + suffix;
        if (pct < 1) requestAnimationFrame(tick);
    })(start);
});
</script>

@endsection