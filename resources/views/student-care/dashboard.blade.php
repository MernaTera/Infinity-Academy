@extends('student-care.layouts.app')
@section('title', 'Student Care Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.sc-dash{font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0;line-height:1}
.page-sub{font-size:12px;color:#7A8A9A;margin-top:4px}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

/* Patch Banner */
.patch-banner{background:linear-gradient(135deg,#1B4FA8 0%,#2D6FDB 100%);border-radius:8px;padding:18px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;position:relative;overflow:hidden}
.patch-banner::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.05)}
.pb-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px}
.pb-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#fff}
.pb-dates{font-size:11px;color:rgba(255,255,255,0.7);margin-top:2px}
.pb-stats{display:flex;gap:20px;flex-wrap:wrap}
.pb-stat-val{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#fff;letter-spacing:1px;line-height:1}
.pb-stat-label{font-size:9px;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;margin-top:2px}
.pb-progress{flex:1;max-width:260px}
.pb-prog-label{display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,0.6);margin-bottom:6px}
.pb-prog-track{background:rgba(255,255,255,0.15);border-radius:4px;height:5px;overflow:hidden}
.pb-prog-fill{height:5px;border-radius:4px;background:linear-gradient(90deg,#F5911E,#FFB347)}

/* KPI Grid */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.kpi-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;padding:18px 20px;position:relative;overflow:hidden;transition:box-shadow 0.2s;text-decoration:none;display:block;color:inherit}
.kpi-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.08);text-decoration:none;color:inherit}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:6px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:10px;color:#AAB8C8;margin-top:4px}

/* Alerts */
.alert-list{display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
.alert-item{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:6px;font-size:12px;line-height:1.5}
.alert-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;margin-top:4px}
.alert-danger{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626}
.alert-warning{background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);color:#C47010}
.alert-info{background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.15);color:#1B4FA8}
.alert-link{color:inherit;font-weight:600;margin-left:4px;text-decoration:underline}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;display:block}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}

/* Cards */
.dash-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:20px}
.dash-card-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;align-items:center;justify-content:space-between}
.dash-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.dash-card-sub{font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8}
.dash-card-body{padding:16px 18px}

/* Instance Row */
.inst-row{display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.2s;text-decoration:none;color:inherit}
.inst-row:last-child{border-bottom:none}
.inst-row:hover{background:rgba(27,79,168,0.02);text-decoration:none}
.inst-avatar{width:32px;height:32px;border-radius:6px;background:rgba(27,79,168,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.inst-name{font-size:13px;color:#1A2A4A;font-weight:500;flex:1}
.inst-meta{font-size:10px;color:#AAB8C8;margin-top:1px}

/* Retention Row */
.ret-row{display:flex;align-items:center;gap:10px;padding:8px 18px;border-bottom:1px solid rgba(27,79,168,0.04)}
.ret-row:last-child{border-bottom:none}
.ret-name{font-size:12px;color:#1A2A4A;font-weight:500;flex:1}
.ret-course{font-size:10px;color:#7A8A9A}
.ret-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:3px;font-size:9px;letter-spacing:1px;text-transform:uppercase;font-weight:500}
.ret-last{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}
.ret-low{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}

/* Quick Actions */
.qa-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:24px}
.qa-btn{display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;text-decoration:none;transition:all 0.2s;color:#7A8A9A}
.qa-btn:hover{border-color:#1B4FA8;background:rgba(27,79,168,0.03);transform:translateY(-2px);box-shadow:0 4px 16px rgba(27,79,168,0.08);text-decoration:none;color:#1B4FA8}
.qa-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(27,79,168,0.08)}
.qa-label{font-size:10px;letter-spacing:1px;text-transform:uppercase;text-align:center}

.cap-track{background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;margin-top:4px;width:60px}
.cap-fill{height:4px;border-radius:3px}

@media(max-width:1024px){.kpi-grid,.kpi-grid-3{grid-template-columns:repeat(2,1fr)}.two-col{grid-template-columns:1fr}.qa-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:640px){.sc-dash{padding:18px 14px}.kpi-grid,.kpi-grid-3{grid-template-columns:1fr 1fr}}
</style>

<div class="sc-dash">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Student Care</div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">{{ now()->format('l, d M Y') }}</p>
        </div>
    </div>

    {{-- Alerts --}}
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

    {{-- Patch Banner --}}
    @if($currentPatch)
    @php
        $pStart   = \Carbon\Carbon::parse($currentPatch->start_date);
        $pEnd     = \Carbon\Carbon::parse($currentPatch->end_date);
        $pTotal   = max(1, $pStart->diffInDays($pEnd));
        $pElapsed = max(0, min($pTotal, $pStart->diffInDays(now())));
        $pPct     = round($pElapsed / $pTotal * 100);
        $daysLeft = max(0, (int)now()->diffInDays($pEnd, false));
    @endphp
    <div class="patch-banner">
        <div>
            <div class="pb-label">Current Patch</div>
            <div class="pb-name">{{ $currentPatch->name }}</div>
            <div class="pb-dates">
                {{ $pStart->format('d M Y') }} → {{ $pEnd->format('d M Y') }}
                · {{ $daysLeft }} days remaining
            </div>
        </div>
        <div class="pb-progress">
            <div class="pb-prog-label">
                <span>Progress</span><span>{{ $pPct }}%</span>
            </div>
            <div class="pb-prog-track">
                <div class="pb-prog-fill" style="width:{{ $pPct }}%"></div>
            </div>
        </div>
        <div class="pb-stats">
            <div>
                <div class="pb-stat-val">{{ $activeCourses }}</div>
                <div class="pb-stat-label">Active</div>
            </div>
            <div>
                <div class="pb-stat-val">{{ $upcomingCourses }}</div>
                <div class="pb-stat-label">Upcoming</div>
            </div>
            <div>
                <div class="pb-stat-val">{{ $totalStudents }}</div>
                <div class="pb-stat-label">Students</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="qa-grid">
        <a href="{{ route('student-care.instances') }}?create=1" class="qa-btn">
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

    {{-- KPIs --}}
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
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Students</div>
            <div class="kpi-val">{{ $totalStudents }}</div>
        </div>
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

    {{-- Main Two Column --}}
    <div class="two-col">

        {{-- LEFT: Active Courses --}}
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

            {{-- Courses Ending Soon --}}
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

        {{-- RIGHT: Retention Near Completion --}}
        <div>
            {{-- Group Near Completion --}}
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

            {{-- Private Near Completion --}}
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

            {{-- Expired Postponements --}}
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
@endsection