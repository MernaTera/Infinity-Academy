@extends('teacher.layouts.app')
@section('title', 'Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--red:#DC2626;--amber:#D97706;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--border:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box;}
.td-wrap { padding: 36px 32px; font-family: 'DM Sans', sans-serif; color:var(--text); min-height:100vh; }

.td-eyebrow { font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:6px;font-weight:600 }
.td-title   { font-family:'Bebas Neue',sans-serif;font-size:40px;letter-spacing:2px;color:var(--text);margin:0 0 2px }
.td-sub     { font-size:11px;letter-spacing:2px;color:var(--muted);text-transform:uppercase }

.td-alerts  { display:flex;flex-direction:column;gap:8px;margin-bottom:28px }
.td-alert   { display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:14px;font-size:12px;letter-spacing:.5px;border-left:3px solid }
.td-alert.warning { background:rgba(245,158,11,0.06);border-color:#F59E0B;color:#92400E }
.td-alert.info    { background:rgba(27,79,168,0.06);border-color:var(--blue);color:var(--blue) }
.td-alert.danger  { background:rgba(220,38,38,0.05);border-color:var(--red);color:#991B1B }
.td-alert-cta { margin-left:auto;font-size:10px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;padding:5px 12px;border-radius:8px;border:1px solid currentColor;opacity:.7;transition:opacity .2s;white-space:nowrap }
.td-alert-cta:hover { opacity:1 }

.td-section { font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);font-weight:600;margin-bottom:14px;margin-top:28px }
.td-section:first-of-type { margin-top:0 }

.td-profile-card { background:var(--card);border:1px solid var(--border);border-radius:20px;padding:24px 28px;display:flex;align-items:center;gap:28px;-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);box-shadow:var(--glass-sh); }
.td-avatar-big { width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,rgba(27,79,168,0.15),rgba(45,111,219,0.1));border:2px solid rgba(27,79,168,0.25);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.td-avatar-big span { font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--blue);letter-spacing:2px }
.td-name { font-size:18px;font-weight:600;color:var(--text);letter-spacing:.5px }
.td-role { font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-top:3px }

.td-vline { width:1px;height:60px;background:rgba(27,79,168,0.1);flex-shrink:0 }

.td-profile-stats { display:flex;gap:28px;flex-wrap:wrap }
.td-pstat { display:flex;flex-direction:column;gap:2px }
.td-pstat-val { font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--text);line-height:1 }
.td-pstat-label { font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint) }

.td-contract-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:20px;background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.2);font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--blue);font-weight:600; }
.td-contract-dot { width:6px;height:6px;border-radius:50%;background:var(--blue) }

.td-salary-wrap { display:flex;align-items:center;gap:8px }
.td-salary-val  { font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--text) }
.td-salary-eye  { background:none;border:none;cursor:pointer;color:var(--faint);padding:0;display:flex;align-items:center;transition:color .2s }
.td-salary-eye:hover { color:var(--blue) }
.td-salary-blur { filter:blur(5px);transition:filter .3s;user-select:none }

.td-stats-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px }
.td-stat-card { background:var(--card);border:1px solid var(--border);border-radius:18px;padding:20px 22px;position:relative;overflow:hidden;-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);box-shadow:var(--glass-sh);transition:transform .2s, box-shadow .2s; }
.td-stat-card:hover { transform:translateY(-3px);box-shadow:0 16px 38px rgba(23,45,90,0.13) }
.td-stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--blue),var(--blue-2));opacity:0;transition:opacity .2s; }
.td-stat-card:hover::before { opacity:1 }
.td-stat-icon { font-size:20px;margin-bottom:10px;display:block }
.td-stat-val  { font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:3px;color:var(--text);line-height:1;margin-bottom:4px }
.td-stat-val.green { color:var(--blue) }
.td-stat-val.red   { color:var(--red) }
.td-stat-val.amber { color:var(--amber) }
.td-stat-label { font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--faint) }
.td-stat-link  { position:absolute;bottom:14px;right:16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(27,79,168,0.5);text-decoration:none;transition:color .2s }
.td-stat-link:hover { color:var(--blue) }

.td-courses-list { display:flex;flex-direction:column;gap:10px }
.td-course-row { background:var(--card);border:1px solid var(--border);border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:16px;text-decoration:none;-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);box-shadow:var(--glass-sh);transition:transform .2s, box-shadow .2s; }
.td-course-row:hover { transform:translateY(-2px);box-shadow:0 12px 30px rgba(23,45,90,0.12);text-decoration:none }
.td-cr-name  { font-size:13px;font-weight:600;color:var(--text);letter-spacing:.3px }
.td-cr-meta  { font-size:10px;letter-spacing:1px;color:var(--faint);margin-top:2px;text-transform:uppercase }
.td-cr-badge { padding:3px 10px;border-radius:12px;font-size:8px;letter-spacing:2px;text-transform:uppercase;font-weight:600 }
.td-cr-badge.active   { background:rgba(27,79,168,0.1);color:var(--blue);border:1px solid rgba(27,79,168,0.2) }
.td-cr-badge.upcoming { background:rgba(245,145,30,0.1);color:var(--orange-dk);border:1px solid rgba(245,145,30,0.2) }
.td-cr-progress-wrap { flex:1;min-width:0 }
.td-cr-prog-track { height:4px;background:rgba(27,79,168,0.1);border-radius:3px;overflow:hidden }
.td-cr-prog-fill  { height:100%;background:linear-gradient(90deg,var(--blue),var(--blue-2));border-radius:3px;transition:width .6s ease }
.td-cr-prog-label { font-size:9px;letter-spacing:1.5px;color:var(--faint);margin-top:4px }
.td-cr-students { display:flex;flex-direction:column;align-items:flex-end;gap:2px;flex-shrink:0 }
.td-cr-count { font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--text) }
.td-cr-count-label { font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint) }

.td-empty { text-align:center;padding:36px;background:var(--card);border:1px dashed rgba(27,79,168,0.2);border-radius:18px;-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%) }
.td-empty-icon { font-size:28px;margin-bottom:8px }
.td-empty-msg  { font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--faint) }

.td-patch-info { display:inline-flex;align-items:center;gap:10px;padding:9px 16px;border-radius:12px;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.14);font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--blue);margin-bottom:24px; }
.td-patch-dot { width:7px;height:7px;border-radius:50%;background:var(--blue);animation:td-pulse 2s infinite }
@keyframes td-pulse { 0%,100%{opacity:1}50%{opacity:.4} }

.td-overload { display:flex;align-items:center;gap:10px;padding:10px 16px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:12px;font-size:11px;color:#92400E;margin-top:8px; }
</style>

<div class="td-wrap">

    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
        <div>
            <div class="td-eyebrow">Instructor Portal</div>
            <h1 class="td-title">Dashboard</h1>
            <div class="td-sub">{{ now()->format('l, d F Y') }}</div>
        </div>

        @if($currentPatch)
        <div class="td-patch-info">
            <span class="td-patch-dot"></span>
            {{ $currentPatch->name }}
            &nbsp;·&nbsp;
            {{ \Carbon\Carbon::parse($currentPatch->start_date)->format('d M') }}
            –
            {{ \Carbon\Carbon::parse($currentPatch->end_date)->format('d M Y') }}
        </div>
        @endif
    </div>

    @if(count($alerts))
    <div class="td-alerts">
        @foreach($alerts as $alert)
        <div class="td-alert {{ $alert['type'] }}">
            <span style="font-size:16px">{{ $alert['icon'] }}</span>
            <span>{{ $alert['msg'] }}</span>
            <a href="{{ $alert['link'] }}" class="td-alert-cta">{{ $alert['cta'] }}</a>
        </div>
        @endforeach
    </div>
    @endif

    <div class="td-section">Personal Overview</div>
    <div class="td-profile-card" style="margin-bottom:28px">

        <div class="td-avatar-big">
            <span>{{ strtoupper(substr($employee->full_name, 0, 2)) }}</span>
        </div>

        <div>
            <div class="td-name">{{ $employee->full_name }}</div>
            <div class="td-role">Instructor · {{ $employee->branch->name ?? 'N/A' }}</div>
            <div style="margin-top:10px">
                @if($contract)
                    <span class="td-contract-badge">
                        <span class="td-contract-dot"></span>
                        Contract: {{ $contract->contractType->name }}
                        &nbsp;·&nbsp; max {{ $contract->contractType->max_sessions_allowed }} sessions/month
                    </span>
                @else
                    <span class="td-contract-badge" style="color:#AAB8C8;border-color:rgba(170,184,200,.3)">
                        No active contract
                    </span>
                @endif
            </div>
        </div>

        <div class="td-vline"></div>

        <div class="td-profile-stats">

            <div class="td-pstat">
                <div class="td-salary-wrap">
                    <div class="td-salary-val td-salary-blur" id="salaryVal">
                        {{ number_format($employee->salary ?? 0) }} EGP
                    </div>
                    <button class="td-salary-eye" onclick="toggleSalary()" title="Toggle salary">
                        <svg id="eyeIcon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <div class="td-pstat-label">Monthly Salary</div>
            </div>

            <div class="td-pstat">
                <div class="td-pstat-val" style="color:{{ $daysUntilSalary <= 5 ? '#1B4FA8' : '#1A2A4A' }}">
                    {{ $daysUntilSalary }}
                </div>
                <div class="td-pstat-label">Days to Payday</div>
            </div>

            <div class="td-pstat">
                <div class="td-pstat-val">{{ $sessionsThisMonth }}</div>
                <div class="td-pstat-label">Sessions This Month</div>
            </div>

            <div class="td-pstat">
                <div class="td-pstat-val">{{ $totalCourses }}</div>
                <div class="td-pstat-label">Assigned Courses</div>
            </div>

        </div>

        @if($contract && $contract->contractType && $sessionsThisMonth > $contract->contractType->max_sessions_allowed)
        <div style="margin-left:auto">
            <div class="td-overload">
                ⚠️ Session overload — {{ $sessionsThisMonth }}/{{ $contract->contractType->max_sessions_allowed }}
            </div>
        </div>
        @endif

    </div>

    <div class="td-section">Academic Summary</div>
    <div class="td-stats-grid" style="margin-bottom:32px">

        <div class="td-stat-card">
            <span class="td-stat-icon">📚</span>
            <div class="td-stat-val green">{{ $activeInstances->count() }}</div>
            <div class="td-stat-label">Active Courses</div>
            <a href="{{ route('teacher.courses') }}" class="td-stat-link">View →</a>
        </div>

        <div class="td-stat-card">
            <span class="td-stat-icon">🕐</span>
            <div class="td-stat-val" style="color:#1B4FA8">{{ $upcomingInstances->count() }}</div>
            <div class="td-stat-label">Upcoming Courses</div>
            <a href="{{ route('teacher.courses') }}" class="td-stat-link">View →</a>
        </div>

        <div class="td-stat-card">
            <span class="td-stat-icon">🎓</span>
            <div class="td-stat-val">{{ $totalStudents }}</div>
            <div class="td-stat-label">Students Assigned</div>
            <a href="{{ route('teacher.courses') }}" class="td-stat-link">View →</a>
        </div>

        <div class="td-stat-card">
            <span class="td-stat-icon">📝</span>
            <div class="td-stat-val {{ $pendingReports > 0 ? 'amber' : '' }}">{{ $pendingReports }}</div>
            <div class="td-stat-label">Pending Reports</div>
            <a href="{{ route('teacher.reports.index') }}" class="td-stat-link">View →</a>
        </div>

        <div class="td-stat-card">
            <span class="td-stat-icon">⏰</span>
            <div class="td-stat-val {{ $lateReports > 0 ? 'red' : '' }}">{{ $lateReports }}</div>
            <div class="td-stat-label">Late Reports</div>
            <a href="{{ route('teacher.reports.index') }}" class="td-stat-link">View →</a>
        </div>

        <div class="td-stat-card">
            <span class="td-stat-icon">🔒</span>
            <div class="td-stat-val {{ $restrictedStudents > 0 ? 'red' : '' }}">{{ $restrictedStudents }}</div>
            <div class="td-stat-label">Restricted Students</div>
            <a href="{{ route('teacher.courses') }}" class="td-stat-link">View →</a>
        </div>

    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="td-section" style="margin:0">Active Courses</div>
        <a href="{{ route('teacher.courses') }}"
           style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#1B4FA8;text-decoration:none;opacity:.7;transition:opacity .2s"
           onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.7">
            View All →
        </a>
    </div>

    <div class="td-courses-list">
        @forelse($activeInstances as $inst)
        @php
            $totalSess     = $inst->sessions->count();
            $doneSess      = $inst->sessions->where('status','Completed')->count();
            $pct           = $totalSess > 0 ? round(($doneSess / $totalSess) * 100) : 0;
            $studentsCount = $inst->enrollments->count();
        @endphp

        <a href="{{ route('teacher.courses.show', $inst->course_instance_id) }}" class="td-course-row">

            <div style="min-width:180px">
                <div class="td-cr-name">{{ $inst->courseTemplate->name ?? 'N/A' }}</div>
                <div class="td-cr-meta">
                    {{ $inst->level->name ?? '' }}
                    @if($inst->sublevel) · {{ $inst->sublevel->name }} @endif
                    · {{ ucfirst($inst->type ?? '') }}
                </div>
            </div>

            <span class="td-cr-badge active">Active</span>

            <div class="td-cr-progress-wrap">
                <div class="td-cr-prog-track">
                    <div class="td-cr-prog-fill" style="width:{{ $pct }}%"></div>
                </div>
                <div class="td-cr-prog-label">{{ $doneSess }}/{{ $totalSess }} sessions &nbsp;·&nbsp; {{ $pct }}%</div>
            </div>

            <div style="font-size:10px;letter-spacing:1px;color:#AAB8C8;text-align:center;flex-shrink:0">
                <div style="color:#1A2A4A;font-size:12px;font-weight:500">
                    {{ $inst->end_date ? \Carbon\Carbon::parse($inst->end_date)->format('d M') : '—' }}
                </div>
                <div style="font-size:8px;text-transform:uppercase;letter-spacing:2px">End Date</div>
            </div>

            <div class="td-cr-students">
                <div class="td-cr-count">{{ $studentsCount }}</div>
                <div class="td-cr-count-label">Students</div>
            </div>

        </a>
        @empty
        <div class="td-empty">
            <div class="td-empty-icon">📭</div>
            <div class="td-empty-msg">No active courses right now</div>
        </div>
        @endforelse

        @foreach($upcomingInstances as $inst)
        <a href="{{ route('teacher.courses.show', $inst->course_instance_id) }}" class="td-course-row">
            <div style="min-width:180px">
                <div class="td-cr-name">{{ $inst->courseTemplate->name ?? 'N/A' }}</div>
                <div class="td-cr-meta">
                    {{ $inst->level->name ?? '' }} · {{ ucfirst($inst->type ?? '') }}
                </div>
            </div>

            <span class="td-cr-badge upcoming">Upcoming</span>

            <div style="flex:1"></div>

            <div style="font-size:10px;letter-spacing:1px;color:#AAB8C8;text-align:center;flex-shrink:0">
                <div style="color:#1A2A4A;font-size:12px;font-weight:500">
                    {{ $inst->start_date ? \Carbon\Carbon::parse($inst->start_date)->format('d M') : '—' }}
                </div>
                <div style="font-size:8px;text-transform:uppercase;letter-spacing:2px">Start Date</div>
            </div>

            <div class="td-cr-students">
                <div class="td-cr-count">{{ $inst->enrollments->count() }}</div>
                <div class="td-cr-count-label">Students</div>
            </div>
        </a>
        @endforeach
    </div>

    <div style="height:40px"></div>
</div>

<script>
let salaryVisible = false;

function toggleSalary() {
    salaryVisible = !salaryVisible;
    const el = document.getElementById('salaryVal');
    if (salaryVisible) {
        el.classList.remove('td-salary-blur');
    } else {
        el.classList.add('td-salary-blur');
    }
}
</script>

@endsection