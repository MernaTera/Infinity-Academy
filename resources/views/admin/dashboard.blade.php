@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.adm-dash{background:#F8F6F2;min-height:100vh;padding:36px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.dash-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.dash-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0;line-height:1}
.dash-sub{font-size:12px;color:#7A8A9A;margin-top:4px}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

/* Patch Banner */
.patch-banner{background:linear-gradient(135deg,#1B4FA8 0%,#2D6FDB 100%);border-radius:8px;padding:18px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;position:relative;overflow:hidden}
.patch-banner::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.05)}
.patch-banner::after{content:'';position:absolute;bottom:-20px;left:100px;width:80px;height:80px;border-radius:50%;background:rgba(245,145,30,0.15)}
.pb-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px}
.pb-name{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:3px;color:#fff}
.pb-dates{font-size:11px;color:rgba(255,255,255,0.7);margin-top:2px}
.pb-progress{flex:1;max-width:280px}
.pb-prog-label{display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,0.6);margin-bottom:6px}
.pb-prog-track{background:rgba(255,255,255,0.15);border-radius:4px;height:6px;overflow:hidden}
.pb-prog-fill{height:6px;border-radius:4px;background:linear-gradient(90deg,#F5911E,#FFB347)}
.pb-stats{display:flex;gap:20px;flex-wrap:wrap}
.pb-stat{text-align:center}
.pb-stat-val{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#fff;letter-spacing:1px;line-height:1}
.pb-stat-label{font-size:9px;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;margin-top:2px}

/* KPI Grid */
.kpi-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.kpi-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;padding:18px 20px;position:relative;overflow:hidden;transition:box-shadow 0.2s}
.kpi-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.08)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:6px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:10px;color:#AAB8C8;margin-top:4px}
.kpi-link{position:absolute;bottom:12px;right:14px;font-size:9px;letter-spacing:1px;text-transform:uppercase;color:rgba(27,79,168,0.3);text-decoration:none;transition:color 0.2s}
.kpi-link:hover{color:#1B4FA8;text-decoration:none}

/* Section */
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;display:block}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:20px}

/* CS Ranking */
.cs-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:20px}
.cs-card-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;align-items:center;justify-content:space-between}
.cs-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.cs-row{display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.2s}
.cs-row:last-child{border-bottom:none}
.cs-row:hover{background:rgba(27,79,168,0.02)}
.cs-rank{font-family:'Bebas Neue',sans-serif;font-size:18px;color:#AAB8C8;letter-spacing:1px;width:24px;flex-shrink:0}
.cs-rank.gold{color:#F5911E}
.cs-rank.silver{color:#7A8A9A}
.cs-rank.bronze{color:#C47010}
.cs-avatar{width:32px;height:32px;border-radius:50%;background:rgba(27,79,168,0.1);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:13px;color:#1B4FA8;flex-shrink:0}
.cs-name{font-size:13px;color:#1A2A4A;font-weight:500;flex:1}
.cs-stats{text-align:right}
.cs-revenue{font-family:'Bebas Neue',sans-serif;font-size:16px;color:#1B4FA8;letter-spacing:1px;line-height:1}
.cs-target-pct{font-size:10px;margin-top:3px}
.cs-prog{flex:1;max-width:100px}
.cs-prog-track{background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;margin-top:4px}
.cs-prog-fill{height:4px;border-radius:3px}

/* Recent Enrollments */
.recent-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:20px}
.recent-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;align-items:center;justify-content:space-between}
.recent-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.enr-row{display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.2s}
.enr-row:last-child{border-bottom:none}
.enr-row:hover{background:rgba(27,79,168,0.02)}
.enr-name{font-size:13px;color:#1A2A4A;font-weight:500;flex:1}
.enr-course{font-size:10px;color:#7A8A9A;margin-top:1px}
.enr-cs{font-size:10px;color:#AAB8C8}
.enr-time{font-size:10px;color:#AAB8C8;white-space:nowrap}

/* Revenue by Course */
.rev-course-item{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(27,79,168,0.04)}
.rev-course-item:last-child{border-bottom:none}
.rev-course-name{font-size:12px;color:#1A2A4A;font-weight:500;flex:1}
.rev-course-bar{flex:1;background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden}
.rev-course-fill{height:4px;border-radius:3px;background:linear-gradient(90deg,#1B4FA8,#2D6FDB)}
.rev-course-val{font-family:'Bebas Neue',sans-serif;font-size:14px;color:#1B4FA8;letter-spacing:1px;white-space:nowrap}

/* Quick Actions */
.qa-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px}
.qa-btn{display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;text-decoration:none;transition:all 0.2s;color:#1A2A4A}
.qa-btn:hover{border-color:#1B4FA8;background:rgba(27,79,168,0.03);transform:translateY(-2px);box-shadow:0 4px 16px rgba(27,79,168,0.08);text-decoration:none;color:#1B4FA8}
.qa-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(27,79,168,0.08)}
.qa-label{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;text-align:center;color:#7A8A9A}
.qa-btn:hover .qa-label{color:#1B4FA8}

/* Alerts */
.alert-list{display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
.alert-item{display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:6px;font-size:12px}
.alert-warning{background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);color:#C47010}
.alert-danger{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626}
.alert-info{background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.15);color:#1B4FA8}
.alert-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0}

@media(max-width:1024px){.kpi-grid-4,.kpi-grid-3{grid-template-columns:repeat(2,1fr)}.two-col,.three-col,.qa-grid{grid-template-columns:1fr 1fr}}
@media(max-width:640px){.kpi-grid-4,.kpi-grid-3,.two-col,.three-col,.qa-grid{grid-template-columns:1fr}.adm-dash{padding:18px 14px}}
</style>

<div class="adm-dash">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="dash-eyebrow">Admin Panel</div>
            <h1 class="dash-title">Executive Dashboard</h1>
            <p class="dash-sub">{{ now()->format('l, d M Y') }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if($overdueInstallments > 0 || $restrictedStudents > 0 || $pendingApprovals > 0)
    <div class="alert-list">
        @if($overdueInstallments > 0)
        <div class="alert-item alert-danger">
            <div class="alert-dot"></div>
            <strong>{{ $overdueInstallments }}</strong> overdue installments require attention —
            <a href="{{ route('admin.outstanding.index') }}" style="color:inherit;font-weight:600;margin-left:4px">View Outstanding →</a>
        </div>
        @endif
        @if($pendingApprovals > 0)
        <div class="alert-item alert-warning">
            <div class="alert-dot"></div>
            <strong>{{ $pendingApprovals }}</strong> installment approval requests pending —
            <a href="{{ route('admin.installments.index') }}" style="color:inherit;font-weight:600;margin-left:4px">Review Now →</a>
        </div>
        @endif
        @if($restrictedStudents > 0)
        <div class="alert-item alert-info">
            <div class="alert-dot"></div>
            <strong>{{ $restrictedStudents }}</strong> students currently restricted from attendance
        </div>
        @endif
    </div>
    @endif

    {{-- Current Patch Banner --}}
    @if($currentPatch)
    @php
        $pStart    = \Carbon\Carbon::parse($currentPatch->start_date);
        $pEnd      = \Carbon\Carbon::parse($currentPatch->end_date);
        $pTotal    = max(1, $pStart->diffInDays($pEnd));
        $pElapsed  = max(0, min($pTotal, $pStart->diffInDays(now())));
        $pPct      = round($pElapsed / $pTotal * 100);
        $daysLeft  = max(0, now()->diffInDays($pEnd, false));
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
                <span>Progress</span>
                <span>{{ $pPct }}%</span>
            </div>
            <div class="pb-prog-track">
                <div class="pb-prog-fill" style="width:{{ $pPct }}%"></div>
            </div>
        </div>
        <div class="pb-stats">
            <div class="pb-stat">
                <div class="pb-stat-val">{{ $activeCourses }}</div>
                <div class="pb-stat-label">Active Courses</div>
            </div>
            <div class="pb-stat">
                <div class="pb-stat-val">{{ $totalStudents }}</div>
                <div class="pb-stat-label">Students</div>
            </div>
            <div class="pb-stat">
                <div class="pb-stat-val">{{ number_format($patchRevenue / 1000, 1) }}K</div>
                <div class="pb-stat-label">Revenue LE</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="qa-grid">
        <a href="{{ route('admin.employees.create') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg></div>
            <span class="qa-label">New Employee</span>
        </a>
        <a href="{{ route('admin.courses.create') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
            <span class="qa-label">New Course</span>
        </a>
        <a href="{{ route('admin.patches.index') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <span class="qa-label">Manage Patches</span>
        </a>
        <a href="{{ route('admin.outstanding.index') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <span class="qa-label">Outstanding Risk</span>
        </a>
    </div>

    {{-- Financial KPIs --}}
    <span class="sec-label">Financial Intelligence</span>
    <div class="kpi-grid-4" style="margin-bottom:20px">
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Patch Revenue</div>
            <div class="kpi-val">{{ number_format($patchRevenue) }}</div>
            <div class="kpi-sub">LE — current patch</div>
            <a href="{{ route('admin.outstanding.index') }}" class="kpi-link">Details →</a>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-val">{{ number_format($totalRevenue) }}</div>
            <div class="kpi-sub">LE — all time</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Outstanding</div>
            <div class="kpi-val">{{ number_format($totalOutstanding) }}</div>
            <div class="kpi-sub">LE unpaid balance</div>
            <a href="{{ route('admin.outstanding.index') }}" class="kpi-link">Override →</a>
        </div>
        <div class="kpi-card" style="--kc:#7A8A9A">
            <div class="kpi-label">Total Refunded</div>
            <div class="kpi-val">{{ number_format($totalRefunded) }}</div>
            <div class="kpi-sub">LE refunded</div>
        </div>
    </div>

    <div class="kpi-grid-4" style="margin-bottom:24px">
        <div class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Pending Installments</div>
            <div class="kpi-val">{{ $pendingInstallments }}</div>
            <div class="kpi-sub">awaiting payment</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Overdue Installments</div>
            <div class="kpi-val">{{ $overdueInstallments }}</div>
            <div class="kpi-sub">past due date</div>
        </div>
        <div class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Pending Approvals</div>
            <div class="kpi-val">{{ $pendingApprovals }}</div>
            <div class="kpi-sub">installment requests</div>
            <a href="{{ route('admin.installments.index') }}" class="kpi-link">Review →</a>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">CS Target</div>
            <div class="kpi-val">{{ $targetPct }}<span style="font-size:16px">%</span></div>
            <div class="kpi-sub">{{ number_format($totalAchieved) }} / {{ number_format($totalTarget) }} LE</div>
        </div>
    </div>

    {{-- Academic KPIs --}}
    <span class="sec-label">Academic Overview</span>
    <div class="kpi-grid-4" style="margin-bottom:24px">
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Active Courses</div>
            <div class="kpi-val">{{ $activeCourses }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B6FA8">
            <div class="kpi-label">Upcoming Courses</div>
            <div class="kpi-val">{{ $upcomingCourses }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Students</div>
            <div class="kpi-val">{{ $totalStudents }}</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $restrictedStudents }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="two-col">

        {{-- LEFT — CS Ranking --}}
        <div>
            <div class="cs-card">
                <div class="cs-card-header">
                    <div class="cs-card-title">CS Performance Ranking</div>
                    <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8">Current Patch</span>
                </div>
                @forelse($csEmployees as $i => $cs)
                @php
                    $rankClass = match($i) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => '' };
                    $barColor  = $cs->achievement >= 100 ? '#059669' : ($cs->achievement >= 70 ? '#1B4FA8' : '#C47010');
                @endphp
                <div class="cs-row">
                    <div class="cs-rank {{ $rankClass }}">{{ $i + 1 }}</div>
                    <div class="cs-avatar">{{ strtoupper(substr($cs->full_name, 0, 1)) }}</div>
                    <div style="flex:1">
                        <div class="cs-name">{{ $cs->full_name }}</div>
                        <div class="cs-prog-track" style="max-width:120px;margin-top:5px">
                            <div class="cs-prog-fill" style="width:{{ min(100, $cs->achievement) }}%;background:{{ $barColor }}"></div>
                        </div>
                    </div>
                    <div class="cs-stats">
                        <div class="cs-revenue">{{ number_format($cs->patch_revenue) }} <span style="font-size:9px;color:#AAB8C8">LE</span></div>
                        <div class="cs-target-pct" style="color:{{ $barColor }}">
                            {{ $cs->achievement }}% of target
                        </div>
                    </div>
                </div>
                @empty
                <div style="padding:30px;text-align:center;color:#AAB8C8;font-size:12px">No CS data available</div>
                @endforelse
            </div>

            {{-- Revenue by Course --}}
            @if($revenueByCourse->isNotEmpty())
            <div class="cs-card">
                <div class="cs-card-header">
                    <div class="cs-card-title">Revenue by Course</div>
                    <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8">Current Patch</span>
                </div>
                <div style="padding:14px 18px">
                    @php $maxRev = $revenueByCourse->max('total'); @endphp
                    @foreach($revenueByCourse as $rc)
                    <div class="rev-course-item">
                        <div class="rev-course-name">{{ $rc->name }}</div>
                        <div class="rev-course-bar">
                            <div class="rev-course-fill" style="width:{{ $maxRev > 0 ? round($rc->total/$maxRev*100) : 0 }}%"></div>
                        </div>
                        <div class="rev-course-val">{{ number_format($rc->total / 1000, 1) }}K</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT — Recent Enrollments + Workforce --}}
        <div>

            {{-- Workforce KPIs --}}
            <div class="kpi-grid-3" style="margin-bottom:14px">
                <div class="kpi-card" style="--kc:#1B4FA8">
                    <div class="kpi-label">Employees</div>
                    <div class="kpi-val">{{ $totalEmployees }}</div>
                    <div class="kpi-sub">active</div>
                </div>
                <div class="kpi-card" style="--kc:#059669">
                    <div class="kpi-label">Teachers</div>
                    <div class="kpi-val">{{ $totalTeachers }}</div>
                    <div class="kpi-sub">active</div>
                </div>
                <div class="kpi-card" style="--kc:#C47010">
                    <div class="kpi-label">Waiting List</div>
                    <div class="kpi-val">{{ $waitingList }}</div>
                    <div class="kpi-sub">students waiting</div>
                </div>
            </div>

            {{-- Recent Enrollments --}}
            <div class="recent-card">
                <div class="recent-header">
                    <div class="recent-title">Recent Enrollments</div>
                    <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#AAB8C8">Latest 8</span>
                </div>
                @forelse($recentEnrollments as $enr)
                <div class="enr-row">
                    <div style="width:28px;height:28px;border-radius:50%;background:rgba(27,79,168,0.1);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:#1B4FA8;flex-shrink:0">
                        {{ strtoupper(substr($enr->student?->full_name ?? '?', 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="enr-name">{{ $enr->student?->full_name ?? '—' }}</div>
                        <div class="enr-course">
                            {{ $enr->courseInstance?->courseTemplate?->name ?? '—' }}
                            <span style="color:#AAB8C8"> · by {{ $enr->createdByCs?->full_name ?? '—' }}</span>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:14px;color:#1B4FA8;letter-spacing:1px">
                            {{ number_format($enr->final_price) }}
                        </div>
                        <div class="enr-time">{{ \Carbon\Carbon::parse($enr->created_at)->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div style="padding:30px;text-align:center;color:#AAB8C8;font-size:12px">No recent enrollments</div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection