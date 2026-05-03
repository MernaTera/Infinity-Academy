@extends('admin.layouts.app')
@section('title', 'Executive Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);
    --orange:#F5911E;--orange-l:rgba(245,145,30,0.08);
    --green:#059669;--green-l:rgba(5,150,105,0.08);
    --red:#DC2626;--red-l:rgba(220,38,38,0.06);
    --purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);
    --teal:#0891B2;--teal-l:rgba(8,145,178,0.08);
    --border:rgba(27,79,168,0.09);
    --bg:#F8F6F2;--card:#fff;
    --text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;
}
*{box-sizing:border-box;}

.adm-dash{background:var(--bg);min-height:100vh;padding:36px 28px;font-family:'DM Sans',sans-serif;color:var(--text);}
.dash-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.dash-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--blue);margin:0;line-height:1;}
.dash-sub{font-size:12px;color:var(--muted);margin-top:4px;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px;}

/* ── PERIOD FILTER ── */
.period-tabs{display:flex;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:4px;gap:2px;box-shadow:0 2px 8px rgba(27,79,168,0.05);}
.period-tab{padding:7px 16px;border-radius:5px;font-size:9px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;color:var(--muted);font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap;border:none;background:none;cursor:pointer;}
.period-tab.active{background:var(--blue);color:#fff;box-shadow:0 2px 8px rgba(27,79,168,0.25);}
.period-tab:hover:not(.active){color:var(--blue);background:var(--blue-l);}

/* ── ALERTS ── */
.alert-strip{display:flex;flex-direction:column;gap:8px;margin-bottom:20px;}
.alert-item{display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:6px;font-size:12px;animation:fadeIn 0.4s ease both;}
.alert-danger{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);color:var(--red);border-left:3px solid var(--red);}
.alert-warning{background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);color:#C47010;border-left:3px solid var(--orange);}
.alert-info{background:var(--blue-l);border:1px solid rgba(27,79,168,0.15);color:var(--blue);border-left:3px solid var(--blue);}
.alert-item a{color:inherit;font-weight:700;margin-left:4px;text-decoration:none;}

/* ── PATCH BANNER ── */
.patch-banner{background:linear-gradient(135deg,#1A2A4A 0%,var(--blue) 60%,#2D6FDB 100%);border-radius:10px;padding:22px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(27,79,168,0.2);}
.patch-banner::before{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(245,145,30,0.08);}
.patch-banner::after{content:'';position:absolute;bottom:-30px;left:120px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04);}
.pb-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:5px;}
.pb-name{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:4px;color:#fff;line-height:1;}
.pb-dates{font-size:11px;color:rgba(255,255,255,0.6);margin-top:4px;}
.pb-prog-track{background:rgba(255,255,255,0.12);border-radius:4px;height:6px;overflow:hidden;margin:6px 0;}
.pb-prog-fill{height:6px;border-radius:4px;background:linear-gradient(90deg,var(--orange),#FFB347);transition:width 1s ease;}
.pb-stat-val{font-family:'Bebas Neue',sans-serif;font-size:26px;color:#fff;letter-spacing:1px;line-height:1;}
.pb-stat-label{font-size:9px;color:rgba(255,255,255,0.45);letter-spacing:2px;text-transform:uppercase;margin-top:3px;}

/* ── SECTION LABEL ── */
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);display:flex;align-items:center;gap:8px;margin-bottom:14px;margin-top:6px;}
.sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

/* ── KPI CARDS ── */
.kpi-grid{display:grid;gap:12px;margin-bottom:20px;}
.kpi-grid-2{grid-template-columns:repeat(2,1fr);}
.kpi-grid-3{grid-template-columns:repeat(3,1fr);}
.kpi-grid-4{grid-template-columns:repeat(4,1fr);}
.kpi-grid-5{grid-template-columns:repeat(5,1fr);}

.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:18px 20px;position:relative;overflow:hidden;transition:all 0.3s;cursor:default;}
.kpi-card:hover{box-shadow:0 6px 24px rgba(27,79,168,0.1);transform:translateY(-2px);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-card-icon{position:absolute;top:14px;right:14px;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:var(--ki,var(--blue-l));opacity:0.7;}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:7px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}
.kpi-sub{font-size:10px;color:var(--faint);margin-top:5px;}
.kpi-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;padding:2px 7px;border-radius:3px;margin-top:6px;font-weight:600;}
.kpi-badge.up{background:var(--green-l);color:var(--green);}
.kpi-badge.down{background:var(--red-l);color:var(--red);}
.kpi-link{position:absolute;bottom:12px;right:14px;font-size:9px;letter-spacing:1px;text-transform:uppercase;color:rgba(27,79,168,0.25);text-decoration:none;transition:color 0.2s;}
.kpi-link:hover{color:var(--blue);text-decoration:none;}

/* ── PAYMENT METHOD CARDS ── */
.pm-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;}
.pm-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;transition:all 0.3s;}
.pm-card:hover{box-shadow:0 6px 20px rgba(27,79,168,0.08);transform:translateY(-2px);}
.pm-card::before{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:var(--pc,var(--blue));}
.pm-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:var(--pi,var(--blue-l));margin-bottom:10px;}
.pm-name{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:4px;}
.pm-val{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;color:var(--pc,var(--blue));line-height:1;}
.pm-sub{font-size:10px;color:var(--faint);margin-top:3px;}
.pm-bar{margin-top:10px;background:rgba(0,0,0,0.04);border-radius:4px;height:4px;overflow:hidden;}
.pm-bar-fill{height:4px;border-radius:4px;background:var(--pc);transition:width 0.8s ease;}

/* ── MAIN LAYOUT ── */
.dash-grid{display:grid;grid-template-columns:1fr 380px;gap:20px;margin-bottom:20px;}
.dash-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;}

/* ── CARDS ── */
.card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(27,79,168,0.04);}
.card-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(27,79,168,0.01);}
.card-title{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2px;color:var(--text);}
.card-body{padding:16px 18px;}

/* ── CS RANKING ── */
.cs-row{display:flex;align-items:center;gap:10px;padding:11px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.cs-row:last-child{border-bottom:none;}
.cs-row:hover{background:rgba(27,79,168,0.02);}
.cs-rank{font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--faint);width:22px;flex-shrink:0;letter-spacing:1px;}
.cs-rank.gold{color:var(--orange);}
.cs-rank.silver{color:#7A8A9A;}
.cs-rank.bronze{color:#C47010;}
.cs-avatar{width:32px;height:32px;border-radius:50%;background:var(--blue-l);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:13px;color:var(--blue);flex-shrink:0;}
.cs-prog-track{background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;margin-top:4px;}
.cs-prog-fill{height:4px;border-radius:3px;transition:width 0.8s ease;}

/* ── REVENUE BARS ── */
.rev-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(27,79,168,0.04);}
.rev-row:last-child{border-bottom:none;}
.rev-bar-track{flex:1;background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;}
.rev-bar-fill{height:5px;border-radius:3px;background:linear-gradient(90deg,var(--blue),#2D6FDB);transition:width 0.8s ease;}

/* ── RECENT ENROLLMENTS ── */
.enr-row{display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.enr-row:last-child{border-bottom:none;}
.enr-row:hover{background:rgba(27,79,168,0.02);}
.enr-avatar{width:28px;height:28px;border-radius:50%;background:var(--blue-l);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:var(--blue);flex-shrink:0;}

/* ── CHART AREA ── */
.chart-wrap{padding:16px 18px;position:relative;}
canvas{max-width:100%;display:block;}

/* ── QUICK ACTIONS ── */
.qa-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;margin-bottom:24px;}
.qa-btn{display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 8px;background:var(--card);border:1px solid var(--border);border-radius:8px;text-decoration:none;transition:all 0.25s;color:var(--muted);}
.qa-btn:hover{border-color:var(--blue);background:var(--blue-l);transform:translateY(-3px);box-shadow:0 6px 20px rgba(27,79,168,0.1);color:var(--blue);text-decoration:none;}
.qa-icon{width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;background:var(--blue-l);transition:background 0.2s;}
.qa-btn:hover .qa-icon{background:var(--blue);}
.qa-btn:hover .qa-icon svg{stroke:#fff;}
.qa-label{font-size:9px;letter-spacing:1px;text-transform:uppercase;text-align:center;}

/* ── STATS TABLE ── */
.stat-table{width:100%;border-collapse:collapse;}
.stat-table td{padding:9px 12px;font-size:12px;border-bottom:1px solid rgba(27,79,168,0.04);}
.stat-table tr:last-child td{border-bottom:none;}
.stat-table td:first-child{color:var(--muted);}
.stat-table td:last-child{text-align:right;font-weight:600;color:var(--text);}

/* ── BADGE ── */
.mini-badge{display:inline-flex;align-items:center;font-size:9px;letter-spacing:0.5px;padding:2px 8px;border-radius:3px;font-weight:600;}
.badge-green{background:var(--green-l);color:var(--green);}
.badge-red{background:var(--red-l);color:var(--red);}
.badge-orange{background:var(--orange-l);color:#C47010;}
.badge-blue{background:var(--blue-l);color:var(--blue);}

/* ── ANIMATE ── */
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
@keyframes countUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.kpi-card,.pm-card,.card,.patch-banner{animation:fadeIn 0.4s ease both;}
.kpi-val{animation:countUp 0.5s ease both;}

/* ── RESPONSIVE ── */
@media(max-width:1200px){
    .kpi-grid-5{grid-template-columns:repeat(3,1fr);}
    .kpi-grid-4{grid-template-columns:repeat(2,1fr);}
    .qa-grid{grid-template-columns:repeat(3,1fr);}
    .dash-grid{grid-template-columns:1fr;}
    .dash-grid-3{grid-template-columns:1fr 1fr;}
}
@media(max-width:768px){
    .adm-dash{padding:18px 14px;}
    .kpi-grid-4,.kpi-grid-3,.kpi-grid-5,.pm-grid,.dash-grid-3{grid-template-columns:1fr 1fr;}
    .qa-grid{grid-template-columns:repeat(3,1fr);}
    .period-tabs{flex-wrap:wrap;}
}
@media(max-width:480px){
    .kpi-grid-4,.kpi-grid-3,.kpi-grid-5,.pm-grid,.kpi-grid-2{grid-template-columns:1fr;}
    .qa-grid{grid-template-columns:1fr 1fr;}
    .dash-title{font-size:28px;}
}
</style>

<div class="adm-dash">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="dash-eyebrow">Admin Panel</div>
            <h1 class="dash-title">Executive Dashboard</h1>
            <p class="dash-sub">{{ now()->format('l, d M Y') }} · Showing: <strong>{{ ucfirst($period === 'patch' ? 'Current Patch' : ($period === 'all' ? 'All Time' : ucfirst($period))) }}</strong></p>
        </div>
        {{-- Period Tabs --}}
        <div class="period-tabs">
            @foreach(['day' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'patch' => 'This Patch', 'all' => 'All Time'] as $p => $label)
            <a href="?period={{ $p }}" class="period-tab {{ $period === $p ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- ── ALERTS ── --}}
    @if($overdueInstallments > 0 || $restrictedStudents > 0 || $pendingApprovals > 0 || $pendingRefunds > 0 || $pendingReports > 0)
    <div class="alert-strip">
        @if($overdueInstallments > 0)
        <div class="alert-item alert-danger">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <strong>{{ $overdueInstallments }}</strong>&nbsp;overdue installments require immediate attention —
            <a href="{{ route('admin.outstanding.index') }}">View Outstanding →</a>
        </div>
        @endif
        @if($pendingApprovals > 0)
        <div class="alert-item alert-warning">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            <strong>{{ $pendingApprovals }}</strong>&nbsp;installment approval requests pending —
            <a href="{{ route('admin.installments.index') }}">Review Now →</a>
        </div>
        @endif
        @if($pendingRefunds > 0)
        <div class="alert-item alert-warning">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg>
            <strong>{{ $pendingRefunds }}</strong>&nbsp;refund requests awaiting approval —
            <a href="{{ route('admin.refunds.index') }}">Review →</a>
        </div>
        @endif
        @if($pendingReports > 0)
        <div class="alert-item alert-info">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <strong>{{ $pendingReports }}</strong>&nbsp;student reports pending approval —
            <a href="{{ route('admin.reports.index') }}">Review →</a>
        </div>
        @endif
        @if($restrictedStudents > 0)
        <div class="alert-item alert-info">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <strong>{{ $restrictedStudents }}</strong>&nbsp;students currently restricted from attendance
        </div>
        @endif
    </div>
    @endif

    {{-- ── PATCH BANNER ── --}}
    @if($currentPatch)
    @php
        $pStart   = \Carbon\Carbon::parse($currentPatch->start_date);
        $pEnd     = \Carbon\Carbon::parse($currentPatch->end_date);
        $pTotal   = max(1, $pStart->diffInDays($pEnd));
        $pElapsed = max(0, min($pTotal, $pStart->diffInDays(now())));
        $pPct     = round($pElapsed / $pTotal * 100);
        $daysLeft = max(0, (int) now()->diffInDays($pEnd, false));
    @endphp
    <div class="patch-banner">
        <div style="position:relative;z-index:1;">
            <div class="pb-label">Current Patch</div>
            <div class="pb-name">{{ $currentPatch->name }}</div>
            <div class="pb-dates">{{ $pStart->format('d M Y') }} → {{ $pEnd->format('d M Y') }} · {{ $daysLeft }} days remaining</div>
        </div>
        <div style="flex:1;max-width:260px;position:relative;z-index:1;">
            <div style="display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,0.55);margin-bottom:6px;">
                <span>Progress</span><span>{{ $pPct }}%</span>
            </div>
            <div class="pb-prog-track">
                <div class="pb-prog-fill" style="width:{{ $pPct }}%"></div>
            </div>
            <div style="font-size:10px;color:rgba(255,255,255,0.4);margin-top:5px;">{{ $pElapsed }} of {{ $pTotal }} days elapsed</div>
        </div>
        <div style="display:flex;gap:28px;flex-wrap:wrap;position:relative;z-index:1;">
            <div style="text-align:center;">
                <div class="pb-stat-val">{{ $activeCourses }}</div>
                <div class="pb-stat-label">Active Courses</div>
            </div>
            <div style="text-align:center;">
                <div class="pb-stat-val">{{ $totalStudents }}</div>
                <div class="pb-stat-label">Students</div>
            </div>
            <div style="text-align:center;">
                <div class="pb-stat-val">{{ number_format($patchRevenue / 1000, 1) }}K</div>
                <div class="pb-stat-label">Revenue LE</div>
            </div>
            <div style="text-align:center;">
                <div class="pb-stat-val">{{ $targetPct }}%</div>
                <div class="pb-stat-label">CS Target</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── QUICK ACTIONS ── --}}
    <div class="qa-grid">
        <a href="{{ route('admin.employees.create') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg></div>
            <span class="qa-label">New Employee</span>
        </a>
        <a href="{{ route('admin.courses.create') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
            <span class="qa-label">New Course</span>
        </a>
        <a href="{{ route('admin.installments.index') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span class="qa-label">Approvals @if($pendingApprovals > 0)<span style="color:#F5911E"> {{ $pendingApprovals }}</span>@endif</span>
        </a>
        <a href="{{ route('admin.outstanding.index') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <span class="qa-label">Outstanding</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <span class="qa-label">Reports @if($pendingReports > 0)<span style="color:#F5911E"> {{ $pendingReports }}</span>@endif</span>
        </a>
        <a href="{{ route('admin.refunds.index') }}" class="qa-btn">
            <div class="qa-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg></div>
            <span class="qa-label">Refunds @if($pendingRefunds > 0)<span style="color:#F5911E"> {{ $pendingRefunds }}</span>@endif</span>
        </a>
    </div>

    {{-- ── REVENUE KPIs ── --}}
    <span class="sec-label">Financial Overview — {{ ucfirst($period === 'patch' ? 'Current Patch' : ($period === 'all' ? 'All Time' : $period)) }}</span>
    <div class="kpi-grid kpi-grid-4">
        <div class="kpi-card" style="--kc:var(--blue);--ki:var(--blue-l)">
            <div class="kpi-card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><line x1="12" y1="20" x2="12" y2="4"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
            <div class="kpi-label">Period Revenue</div>
            <div class="kpi-val">{{ number_format($periodRevenue) }}</div>
            <div class="kpi-sub">LE — selected period</div>
        </div>
        <div class="kpi-card" style="--kc:var(--green);--ki:var(--green-l)">
            <div class="kpi-card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-val">{{ number_format($totalRevenue) }}</div>
            <div class="kpi-sub">LE — all time</div>
        </div>
        <div class="kpi-card" style="--kc:var(--red);--ki:var(--red-l)">
            <div class="kpi-card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <div class="kpi-label">Outstanding Balance</div>
            <div class="kpi-val">{{ number_format($totalOutstanding) }}</div>
            <div class="kpi-sub">LE unpaid</div>
            <a href="{{ route('admin.outstanding.index') }}" class="kpi-link">View →</a>
        </div>
        <div class="kpi-card" style="--kc:var(--muted);--ki:rgba(122,138,154,0.1)">
            <div class="kpi-card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7A8A9A" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg></div>
            <div class="kpi-label">Total Refunded</div>
            <div class="kpi-val">{{ number_format($totalRefunded) }}</div>
            <div class="kpi-sub">LE refunded — period</div>
        </div>
    </div>

    {{-- ── PAYMENT METHODS ── --}}
    <span class="sec-label">Payment Method Breakdown</span>
    @php $pmTotal = $cashRevenue + $instapayRevenue + $vodafoneRevenue + $cardRevenue + $transferRevenue; @endphp
    <div class="pm-grid">
        <div class="pm-card" style="--pc:#059669;--pi:var(--green-l)">
            <div class="pm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M12 12h.01M6 12h.01M18 12h.01"/></svg></div>
            <div class="pm-name">Cash</div>
            <div class="pm-val">{{ number_format($cashRevenue) }}</div>
            <div class="pm-sub">{{ $cashCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $pmTotal > 0 ? round($cashRevenue/$pmTotal*100) : 0 }}%"></div></div>
            <div style="font-size:9px;color:var(--faint);margin-top:4px;">{{ $pmTotal > 0 ? round($cashRevenue/$pmTotal*100) : 0 }}% of total</div>
        </div>
        <div class="pm-card" style="--pc:#7F77DD;--pi:var(--purple-l)">
            <div class="pm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7F77DD" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg></div>
            <div class="pm-name">Instapay</div>
            <div class="pm-val">{{ number_format($instapayRevenue) }}</div>
            <div class="pm-sub">{{ $instapayCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $pmTotal > 0 ? round($instapayRevenue/$pmTotal*100) : 0 }}%;background:#7F77DD"></div></div>
            <div style="font-size:9px;color:var(--faint);margin-top:4px;">{{ $pmTotal > 0 ? round($instapayRevenue/$pmTotal*100) : 0 }}% of total</div>
        </div>
        <div class="pm-card" style="--pc:#E11D48;--pi:rgba(225,29,72,0.08)">
            <div class="pm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E11D48" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/></svg></div>
            <div class="pm-name">Vodafone Cash</div>
            <div class="pm-val">{{ number_format($vodafoneRevenue) }}</div>
            <div class="pm-sub">{{ $vodafoneCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $pmTotal > 0 ? round($vodafoneRevenue/$pmTotal*100) : 0 }}%;background:#E11D48"></div></div>
            <div style="font-size:9px;color:var(--faint);margin-top:4px;">{{ $pmTotal > 0 ? round($vodafoneRevenue/$pmTotal*100) : 0 }}% of total</div>
        </div>
    </div>
    @if($cardRevenue > 0 || $transferRevenue > 0)
    <div class="kpi-grid kpi-grid-2" style="margin-bottom:24px;margin-top:-8px;">
        <div class="kpi-card" style="--kc:var(--teal);--ki:var(--teal-l)">
            <div class="kpi-label">Card</div>
            <div class="kpi-val" style="font-size:22px;">{{ number_format($cardRevenue) }}</div>
            <div class="kpi-sub">LE via Card · {{ $pmTotal > 0 ? round($cardRevenue/$pmTotal*100) : 0 }}%</div>
        </div>
        <div class="kpi-card" style="--kc:var(--blue);--ki:var(--blue-l)">
            <div class="kpi-label">Bank Transfer</div>
            <div class="kpi-val" style="font-size:22px;">{{ number_format($transferRevenue) }}</div>
            <div class="kpi-sub">LE via Transfer · {{ $pmTotal > 0 ? round($transferRevenue/$pmTotal*100) : 0 }}%</div>
        </div>
    </div>
    @endif

    {{-- ── REVENUE CHART + ENROLLMENT CHART ── --}}
    <div class="dash-grid-3" style="margin-bottom:24px;">
        <div class="card" style="grid-column:span 2;">
            <div class="card-header">
                <div class="card-title">Revenue Trend — Last 7 Days</div>
                <span style="font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;">Daily LE</span>
            </div>
            <div class="chart-wrap" style="height:200px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Enrollments — 14 Days</div>
            </div>
            <div class="chart-wrap" style="height:200px;">
                <canvas id="enrollChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── FINANCIAL MISC KPIs ── --}}
    <span class="sec-label">Payments & Collections</span>
    <div class="kpi-grid kpi-grid-4" style="margin-bottom:24px;">
        <div class="kpi-card" style="--kc:#C47010;--ki:var(--orange-l)">
            <div class="kpi-label">Pending Installments</div>
            <div class="kpi-val">{{ $pendingInstallments }}</div>
            <div class="kpi-sub">awaiting payment</div>
        </div>
        <div class="kpi-card" style="--kc:var(--red);--ki:var(--red-l)">
            <div class="kpi-label">Overdue Installments</div>
            <div class="kpi-val">{{ $overdueInstallments }}</div>
            <div class="kpi-sub">past due date</div>
            <a href="{{ route('admin.outstanding.index') }}" class="kpi-link">View →</a>
        </div>
        <div class="kpi-card" style="--kc:#C47010;--ki:var(--orange-l)">
            <div class="kpi-label">Pending Approvals</div>
            <div class="kpi-val">{{ $pendingApprovals }}</div>
            <div class="kpi-sub">installment requests</div>
            <a href="{{ route('admin.installments.index') }}" class="kpi-link">Review →</a>
        </div>
        <div class="kpi-card" style="--kc:var(--orange);--ki:var(--orange-l)">
            <div class="kpi-label">CS Target Achievement</div>
            <div class="kpi-val">{{ $targetPct }}<span style="font-size:16px;">%</span></div>
            <div class="kpi-sub">{{ number_format($totalAchieved) }} / {{ number_format($totalTarget) }} LE</div>
        </div>
    </div>

    {{-- ── ACADEMIC KPIs ── --}}
    <span class="sec-label">Academic Overview</span>
    <div class="kpi-grid kpi-grid-5" style="margin-bottom:24px;">
        <div class="kpi-card" style="--kc:var(--green);--ki:var(--green-l)">
            <div class="kpi-label">Active Courses</div>
            <div class="kpi-val">{{ $activeCourses }}</div>
            <a href="{{ route('admin.courses.index') }}" class="kpi-link">View →</a>
        </div>
        <div class="kpi-card" style="--kc:var(--teal);--ki:var(--teal-l)">
            <div class="kpi-label">Upcoming Courses</div>
            <div class="kpi-val">{{ $upcomingCourses }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--blue);--ki:var(--blue-l)">
            <div class="kpi-label">Active Students</div>
            <div class="kpi-val">{{ $totalStudents }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--red);--ki:var(--red-l)">
            <div class="kpi-label">Restricted</div>
            <div class="kpi-val">{{ $restrictedStudents }}</div>
            <div class="kpi-sub">attendance blocked</div>
        </div>
        <div class="kpi-card" style="--kc:#C47010;--ki:var(--orange-l)">
            <div class="kpi-label">Waiting List</div>
            <div class="kpi-val">{{ $waitingList }}</div>
            <div class="kpi-sub">students waiting</div>
        </div>
    </div>

    {{-- ── LEADS + HR KPIs ── --}}
    <div class="kpi-grid kpi-grid-4" style="margin-bottom:24px;">
        <div class="kpi-card" style="--kc:var(--purple);--ki:var(--purple-l)">
            <div class="kpi-label">Total Leads</div>
            <div class="kpi-val">{{ $totalLeads }}</div>
            <div class="kpi-sub">in selected period</div>
        </div>
        <div class="kpi-card" style="--kc:var(--green);--ki:var(--green-l)">
            <div class="kpi-label">Converted Leads</div>
            <div class="kpi-val">{{ $convertedLeads }}</div>
            <div class="kpi-sub">registered students</div>
        </div>
        <div class="kpi-card" style="--kc:var(--orange);--ki:var(--orange-l)">
            <div class="kpi-label">Conversion Rate</div>
            <div class="kpi-val">{{ $conversionRate }}<span style="font-size:16px;">%</span></div>
            <div class="kpi-sub">leads → enrollments</div>
        </div>
        <div class="kpi-card" style="--kc:var(--blue);--ki:var(--blue-l)">
            <div class="kpi-label">New Enrollments</div>
            <div class="kpi-val">{{ $periodEnrollments }}</div>
            <div class="kpi-sub">in selected period</div>
        </div>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="dash-grid">

        {{-- LEFT --}}
        <div>

            {{-- CS Ranking --}}
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div class="card-title">CS Performance Ranking</div>
                    <span class="mini-badge badge-blue">{{ ucfirst($period === 'patch' ? 'Current Patch' : $period) }}</span>
                </div>
                @forelse($csEmployees as $i => $cs)
                @php
                    $rankClass = match($i) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => '' };
                    $barColor  = $cs->achievement >= 100 ? '#059669' : ($cs->achievement >= 70 ? '#1B4FA8' : '#C47010');
                @endphp
                <div class="cs-row">
                    <div class="cs-rank {{ $rankClass }}">{{ $i + 1 }}</div>
                    <div class="cs-avatar">{{ strtoupper(substr($cs->full_name, 0, 1)) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $cs->full_name }}</div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                            <div class="cs-prog-track" style="flex:1;">
                                <div class="cs-prog-fill" style="width:{{ min(100,$cs->achievement) }}%;background:{{ $barColor }};"></div>
                            </div>
                            <span style="font-size:9px;color:{{ $barColor }};font-weight:600;white-space:nowrap;">{{ $cs->achievement }}%</span>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;margin-left:10px;">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--blue);letter-spacing:1px;">{{ number_format($cs->patch_revenue) }} <span style="font-size:9px;color:var(--faint);">LE</span></div>
                        <div style="font-size:10px;color:var(--faint);margin-top:2px;">{{ $cs->registrations }} registrations</div>
                    </div>
                </div>
                @empty
                <div style="padding:30px;text-align:center;color:var(--faint);font-size:12px;">No CS data available</div>
                @endforelse
            </div>

            {{-- Revenue by Course --}}
            @if($revenueByCourse->isNotEmpty())
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div class="card-title">Revenue by Course</div>
                    <span style="font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;">Top 6</span>
                </div>
                <div class="card-body">
                    @php $maxRev = $revenueByCourse->max('total'); @endphp
                    @foreach($revenueByCourse as $rc)
                    <div class="rev-row">
                        <div style="font-size:12px;color:var(--text);font-weight:500;min-width:120px;">{{ $rc->name }}</div>
                        <div class="rev-bar-track">
                            <div class="rev-bar-fill" style="width:{{ $maxRev > 0 ? round($rc->total/$maxRev*100) : 0 }}%"></div>
                        </div>
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:var(--blue);letter-spacing:1px;white-space:nowrap;margin-left:8px;">
                            {{ number_format($rc->total/1000, 1) }}K
                        </div>
                        <span style="font-size:9px;color:var(--faint);margin-left:6px;">{{ $rc->count }} tx</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Revenue by Branch --}}
            @if($revenueByBranch->isNotEmpty())
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div class="card-title">Revenue by Branch</div>
                </div>
                <div class="card-body">
                    @php $maxBranch = $revenueByBranch->max('total'); @endphp
                    @foreach($revenueByBranch as $rb)
                    <div class="rev-row">
                        <div style="font-size:12px;color:var(--text);font-weight:500;min-width:120px;">{{ $rb->name }}</div>
                        <div class="rev-bar-track">
                            <div class="rev-bar-fill" style="width:{{ $maxBranch > 0 ? round($rb->total/$maxBranch*100) : 0 }}%;background:linear-gradient(90deg,var(--orange),#FFB347)"></div>
                        </div>
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:var(--orange);letter-spacing:1px;white-space:nowrap;margin-left:8px;">
                            {{ number_format($rb->total/1000, 1) }}K
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT --}}
        <div>

            {{-- HR + Academic Summary --}}
            <div class="card" style="margin-bottom:16px;">
                <div class="card-header">
                    <div class="card-title">Workforce & Operations</div>
                </div>
                <table class="stat-table" style="padding:0;">
                    <tr><td>Active Employees</td><td><strong>{{ $totalEmployees }}</strong></td></tr>
                    <tr><td>Active Teachers</td><td><strong>{{ $totalTeachers }}</strong></td></tr>
                    <tr><td>CS Team</td><td><strong>{{ $csEmployees->count() }}</strong></td></tr>
                    <tr><td>Active Courses</td><td><strong>{{ $activeCourses }}</strong></td></tr>
                    <tr><td>Avg Capacity Used</td><td><span class="mini-badge {{ $avgCapacity >= 80 ? 'badge-red' : ($avgCapacity >= 60 ? 'badge-orange' : 'badge-green') }}">{{ $avgCapacity }}%</span></td></tr>
                    <tr><td>Waiting List</td><td><strong>{{ $waitingList }}</strong></td></tr>
                    <tr><td>Pending Reports</td><td><strong>{{ $pendingReports }}</strong></td></tr>
                    <tr><td>Pending Refunds</td><td><strong>{{ $pendingRefunds }}</strong></td></tr>
                </table>
            </div>

            {{-- Recent Enrollments --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Recent Enrollments</div>
                    <span style="font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase;">Latest 10</span>
                </div>
                @forelse($recentEnrollments as $enr)
                <div class="enr-row">
                    <div class="enr-avatar">{{ strtoupper(substr($enr->student?->full_name ?? '?', 0, 1)) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;color:var(--text);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enr->student?->full_name ?? '—' }}</div>
                        <div style="font-size:10px;color:var(--faint);margin-top:1px;">{{ $enr->courseInstance?->courseTemplate?->name ?? '—' }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:14px;color:var(--blue);letter-spacing:1px;">{{ number_format($enr->final_price) }}</div>
                        <div style="font-size:10px;color:var(--faint);">{{ \Carbon\Carbon::parse($enr->created_at)->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div style="padding:30px;text-align:center;color:var(--faint);font-size:12px;">No enrollments in this period</div>
                @endforelse
            </div>

        </div>
    </div>

</div>

{{-- ── CHART.JS ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: {
        backgroundColor: '#1A2A4A',
        titleColor: '#AAB8C8',
        bodyColor: '#fff',
        padding: 10,
        cornerRadius: 6,
        titleFont: { size: 10, family: 'DM Sans' },
        bodyFont: { size: 13, family: 'Bebas Neue', letterSpacing: '1px' },
    }},
    scales: {
        x: { grid: { display: false }, ticks: { color: '#AAB8C8', font: { size: 9, family: 'DM Sans' } } },
        y: { grid: { color: 'rgba(27,79,168,0.06)' }, ticks: { color: '#AAB8C8', font: { size: 9 },
            callback: v => v >= 1000 ? (v/1000).toFixed(0)+'K' : v } }
    }
};

// Revenue Trend
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: @json($trendDays),
        datasets: [{
            data: @json($trendValues),
            backgroundColor: 'rgba(27,79,168,0.12)',
            borderColor: '#1B4FA8',
            borderWidth: 2,
            borderRadius: 4,
            hoverBackgroundColor: 'rgba(27,79,168,0.25)',
        }]
    },
    options: {
        ...chartDefaults,
        plugins: { ...chartDefaults.plugins, tooltip: {
            ...chartDefaults.plugins.tooltip,
            callbacks: { label: ctx => ' ' + Number(ctx.raw).toLocaleString() + ' LE' }
        }}
    }
});

// Enrollment Trend
new Chart(document.getElementById('enrollChart'), {
    type: 'line',
    data: {
        labels: @json($enrollDays),
        datasets: [{
            data: @json($enrollValues),
            borderColor: '#F5911E',
            backgroundColor: 'rgba(245,145,30,0.08)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#F5911E',
            pointRadius: 3,
            pointHoverRadius: 5,
        }]
    },
    options: {
        ...chartDefaults,
        scales: {
            ...chartDefaults.scales,
            y: { ...chartDefaults.scales.y, ticks: { ...chartDefaults.scales.y.ticks, stepSize: 1 } }
        }
    }
});

// Animate KPI numbers
document.querySelectorAll('.kpi-val').forEach(el => {
    const text = el.textContent.trim();
    const num = parseFloat(text.replace(/[^0-9.]/g, ''));
    if (isNaN(num) || num === 0) return;
    const suffix = text.replace(/[\d,.]/g, '').trim();
    const duration = 800;
    const start = performance.now();
    const isFloat = text.includes('.');
    (function update(now) {
        const pct = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        const val = num * ease;
        el.textContent = (isFloat ? val.toFixed(1) : Math.round(val).toLocaleString()) + (suffix ? ' ' + suffix : '');
        if (pct < 1) requestAnimationFrame(update);
    })(start);
});

// Animate progress bars on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.width = entry.target.dataset.width || entry.target.style.width;
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.pb-prog-fill, .cs-prog-fill, .rev-bar-fill, .pm-bar-fill').forEach(el => {
    observer.observe(el);
});
</script>

@endsection