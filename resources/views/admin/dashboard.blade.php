@extends('admin.layouts.app')
@section('title', 'Executive Dashboard')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --blue:#1B4FA8;--blue-l:rgba(27,79,168,0.10);--blue-2:#2D6FDB;
    --orange:#F5911E;--orange-l:rgba(245,145,30,0.12);--orange-dk:#C47010;
    --green:#059669;--green-dk:#15803D;--green-l:rgba(5,150,105,0.10);
    --red:#DC2626;--red-l:rgba(220,38,38,0.08);
    --purple:#7C3AED;--purple-l:rgba(124,58,237,0.10);
    --teal:#0891B2;--teal-l:rgba(8,145,178,0.10);
    --dark:#16233F;--dark-2:#16233F;
    --border:rgba(27,79,168,0.08);--border-strong:rgba(27,79,168,0.14);
    --bg:#F8F6F2;--card:rgba(255,255,255,0.5);
    --glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);
    --text:#16233F;--muted:#5A6A85;--faint:#93A3BC;
}
*{box-sizing:border-box;}
.dash{background:var(--bg);min-height:100vh;padding:28px;font-family:'DM Sans',sans-serif;color:var(--text);}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none;}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%;}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%;}

.cmd-strip{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);
    border:1px solid var(--glass-bd);border-radius:20px;padding:20px 26px;margin-bottom:22px;
    display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;
    box-shadow:var(--glass-sh);position:relative;overflow:hidden;}
/* .cmd-strip::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent);} */
.cmd-strip::after{display:none;}
.cmd-left{position:relative;z-index:1;}
.cmd-eyebrow{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600;
    display:flex;align-items:center;gap:8px;}
.cmd-eyebrow::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);
    animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.3}}
.cmd-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0;line-height:1;}
.cmd-sub{font-size:11px;color:var(--muted);margin-top:4px;letter-spacing:0.5px;}
.cmd-right{display:flex;align-items:center;gap:10px;position:relative;z-index:1;flex-wrap:wrap;}
.cmd-live{display:flex;align-items:center;gap:5px;padding:5px 10px;background:var(--green-l);
    border:1px solid rgba(5,150,105,0.22);border-radius:20px;font-size:9px;letter-spacing:1.5px;
    text-transform:uppercase;color:var(--green-dk);font-weight:600;}
.cmd-live-dot{width:5px;height:5px;border-radius:50%;background:var(--green);animation:pulse 1.5s infinite;}

.period-tabs{display:flex;background:rgba(255,255,255,0.5);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);
    border:1px solid var(--glass-bd);border-radius:12px;padding:3px;gap:2px;}
.period-tab{padding:6px 13px;border-radius:9px;font-size:9px;letter-spacing:1.8px;text-transform:uppercase;
    text-decoration:none;color:var(--muted);font-family:'DM Sans',sans-serif;transition:all 0.2s;
    white-space:nowrap;border:none;background:none;cursor:pointer;font-weight:600;}
.period-tab.active{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;box-shadow:0 6px 14px rgba(27,79,168,0.28);}
.period-tab:hover:not(.active){color:var(--blue);background:rgba(27,79,168,0.06);text-decoration:none;}

.sec{margin-top:6px;margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-num{width:22px;height:22px;background:linear-gradient(135deg,var(--blue),var(--blue-2));color:#fff;border-radius:7px;
    display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;
    font-size:11px;letter-spacing:1px;font-weight:600;flex-shrink:0;box-shadow:0 4px 10px rgba(27,79,168,0.25);}
.sec-label{font-size:10px;letter-spacing:3.5px;text-transform:uppercase;color:var(--muted);font-weight:700;}
.sec-desc{font-size:10px;color:var(--faint);letter-spacing:0.5px;flex-shrink:0;}
.sec-line{flex:1;height:1px;background:linear-gradient(90deg,var(--border-strong),transparent);}

.hero-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:26px;}
.hero-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);
    border:1px solid var(--glass-bd);border-radius:18px;padding:20px 22px;box-shadow:var(--glass-sh);
    position:relative;overflow:hidden;transition:all 0.3s cubic-bezier(0.16,1,0.3,1);}
.hero-card:hover{box-shadow:0 16px 38px rgba(23,45,90,0.13);transform:translateY(-3px);}
.hero-card::before{display:none;}
.hero-card::after{display:none;}
.hero-icon{position:absolute;top:18px;right:20px;width:36px;height:36px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;background:var(--hi,var(--blue-l));z-index:1;}
.hero-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);
    margin-bottom:8px;font-weight:600;position:relative;z-index:1;}
.hero-val{font-family:'Bebas Neue',sans-serif;font-size:38px;letter-spacing:2px;color:var(--hc,var(--blue));
    line-height:1;position:relative;z-index:1;margin-bottom:6px;}
.hero-val-sm{font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;color:var(--muted);margin-left:2px;}
.hero-meta{display:flex;align-items:center;gap:8px;position:relative;z-index:1;flex-wrap:wrap;}
.hero-sub{font-size:10px;color:var(--faint);letter-spacing:0.3px;}
.trend-badge{display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:20px;
    font-size:9px;font-weight:700;letter-spacing:0.5px;}
.trend-up  {background:var(--green-l);color:var(--green-dk);}
.trend-down{background:var(--red-l);color:var(--red);}
.trend-flat{background:rgba(122,138,154,0.12);color:var(--muted);}
.hero-link{position:absolute;bottom:14px;right:16px;font-size:8px;letter-spacing:1.5px;text-transform:uppercase;
    color:var(--faint);text-decoration:none;transition:color 0.2s;font-weight:600;}
.hero-link:hover{color:var(--hc);text-decoration:none;}

.action-queue{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);
    border:1px solid var(--glass-bd);border-radius:20px;overflow:hidden;margin-bottom:26px;box-shadow:var(--glass-sh);}
.aq-head{padding:16px 22px;background:rgba(255,255,255,0.35);border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;color:var(--text);}
.aq-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;display:flex;align-items:center;gap:10px;color:var(--text);}
.aq-title-icon{width:26px;height:26px;background:linear-gradient(135deg,var(--orange),#ffb45f);border-radius:8px;
    display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(245,145,30,0.3);}
.aq-count{background:var(--red);color:#fff;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;
    display:flex;align-items:center;gap:6px;}
.aq-count.zero{background:var(--green);}
.aq-count-dot{width:6px;height:6px;background:#fff;border-radius:50%;animation:pulse 1.5s infinite;}

.aq-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--border);}
.aq-item{background:rgba(255,255,255,0.55);padding:16px 20px;text-decoration:none;transition:all 0.2s;
    display:flex;align-items:center;gap:14px;position:relative;}
.aq-item:hover{background:rgba(255,255,255,0.8);text-decoration:none;transform:translateX(3px);}
.aq-item::before{content:'';position:absolute;left:0;top:0;bottom:0;width:0;background:var(--ac);transition:width 0.2s;}
.aq-item:hover::before{width:3px;}
.aq-icon{width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;
    background:var(--ai);flex-shrink:0;}
.aq-info{flex:1;min-width:0;}
.aq-label{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);
    margin-bottom:2px;font-weight:600;}
.aq-val{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;color:var(--ac);line-height:1;}
.aq-arrow{color:var(--faint);opacity:0.5;transition:all 0.2s;flex-shrink:0;}
.aq-item:hover .aq-arrow{color:var(--ac);opacity:1;transform:translateX(4px);}
.aq-critical{--ac:var(--red);--ai:var(--red-l);}
.aq-warn    {--ac:var(--orange-dk);--ai:var(--orange-l);}
.aq-info-c  {--ac:var(--blue);--ai:var(--blue-l);}
.aq-item.empty{opacity:0.35;pointer-events:none;}
.aq-item.empty .aq-arrow{display:none;}
.aq-empty{padding:28px;text-align:center;color:var(--faint);font-size:12px;grid-column:1/-1;background:transparent;}
.aq-empty-icon{width:44px;height:44px;background:var(--green-l);border-radius:50%;
    display:flex;align-items:center;justify-content:center;margin:0 auto 10px;}

.patch-banner{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);
    border:1px solid var(--glass-bd);border-radius:18px;padding:20px 26px;margin-bottom:20px;display:flex;align-items:center;
    justify-content:space-between;flex-wrap:wrap;gap:20px;position:relative;overflow:hidden;
    box-shadow:var(--glass-sh);}
.patch-banner::before{content:'';position:absolute;top:0;left:0;bottom:0;width:4px;border-radius:0;background:linear-gradient(180deg,var(--blue),var(--blue-2));}
.pb-info{position:relative;z-index:1;min-width:200px;}
.pb-label{font-size:8px;letter-spacing:3px;text-transform:uppercase;color:var(--faint);margin-bottom:4px;font-weight:600;}
.pb-name{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:3px;color:var(--text);line-height:1;}
.pb-dates{font-size:11px;color:var(--muted);margin-top:4px;letter-spacing:0.3px;}
.pb-prog-wrap{position:relative;z-index:1;flex:1;max-width:280px;min-width:180px;}
.pb-prog-label{display:flex;justify-content:space-between;font-size:9px;letter-spacing:2px;
    text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:600;}
.pb-prog-track{background:rgba(27,79,168,0.10);border-radius:5px;height:6px;overflow:hidden;}
.pb-prog-fill{height:6px;border-radius:5px;background:linear-gradient(90deg,var(--blue),var(--blue-2));}
.pb-stats{display:flex;gap:24px;position:relative;z-index:1;}
.pb-stat{text-align:center;}
.pb-stat-val{font-family:'Bebas Neue',sans-serif;font-size:26px;color:var(--blue);letter-spacing:1px;line-height:1;}
.pb-stat-label{font-size:8px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-top:4px;font-weight:600;}

.pm-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
.pm-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);
    border:1px solid var(--glass-bd);border-radius:18px;padding:16px 18px;box-shadow:var(--glass-sh);
    position:relative;overflow:hidden;transition:all 0.3s;}
.pm-card:hover{box-shadow:0 16px 38px rgba(23,45,90,0.13);transform:translateY(-2px);}
.pm-card::before{display:none;}
.pm-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.pm-icon{width:36px;height:36px;border-radius:12px;display:flex;align-items:center;justify-content:center;
    background:var(--pi,var(--blue-l));}
.pm-share{font-size:9px;font-weight:700;color:var(--pc,var(--blue));background:var(--pi);
    padding:3px 8px;border-radius:12px;letter-spacing:0.3px;}
.pm-name{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;font-weight:600;}
.pm-val{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;color:var(--pc,var(--blue));line-height:1;}
.pm-sub{font-size:10px;color:var(--faint);margin-top:4px;letter-spacing:0.3px;}
.pm-bar{margin-top:10px;background:rgba(27,79,168,0.08);border-radius:4px;height:3px;overflow:hidden;}
.pm-bar-fill{height:3px;border-radius:4px;background:var(--pc);transition:width 0.8s ease;}

.card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);
    border:1px solid var(--glass-bd);border-radius:20px;overflow:hidden;box-shadow:var(--glass-sh);}
.card-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;
    align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;background:rgba(255,255,255,0.35);}
.card-title{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2.5px;color:var(--text);
    display:flex;align-items:center;gap:8px;}
.card-title-icon{width:22px;height:22px;background:var(--blue-l);border-radius:7px;
    display:flex;align-items:center;justify-content:center;}
.card-body{padding:16px 18px;}

.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;}
.grid-2-1{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;}
.grid-1-2{display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:24px;}

.mini-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
.mini-card{background:rgba(255,255,255,0.55);-webkit-backdrop-filter:blur(14px);backdrop-filter:blur(14px);
    border:1px solid var(--glass-bd);border-radius:14px;padding:14px 16px;box-shadow:0 4px 16px rgba(23,45,90,0.05);
    position:relative;transition:all 0.25s;}
.mini-card:hover{border-color:var(--mc,var(--blue));transform:translateY(-1px);}
.mini-card::before{display:none;}
.mini-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600;}
.mini-val{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:1.5px;color:var(--mc,var(--text));line-height:1;}
.mini-sub{font-size:9px;color:var(--faint);margin-top:3px;letter-spacing:0.3px;}

.cs-row{display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid rgba(27,79,168,0.06);transition:all 0.15s;}
.cs-row:last-child{border-bottom:none;}
.cs-row:hover{background:rgba(27,79,168,0.04);}
.cs-rank{font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--faint);width:26px;flex-shrink:0;letter-spacing:1px;text-align:center;}
.cs-rank.gold  {color:#E0A800;}
.cs-rank.silver{color:#9AA5B4;}
.cs-rank.bronze{color:#CD7F32;}
.cs-avatar{width:34px;height:34px;border-radius:12px;background:var(--blue-l);
    display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:14px;
    color:var(--blue);flex-shrink:0;}
.cs-prog-track{background:rgba(27,79,168,0.08);border-radius:3px;height:5px;overflow:hidden;margin-top:5px;}
.cs-prog-fill{height:5px;border-radius:3px;transition:width 0.8s ease;}

.rev-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid rgba(27,79,168,0.06);}
.rev-row:last-child{border-bottom:none;}
.rev-bar-track{flex:1;background:rgba(27,79,168,0.08);border-radius:3px;height:5px;overflow:hidden;min-width:60px;}
.rev-bar-fill{height:5px;border-radius:3px;transition:width 0.8s ease;}

.enr-row{display:flex;align-items:center;gap:10px;padding:11px 18px;border-bottom:1px solid rgba(27,79,168,0.06);transition:all 0.15s;}
.enr-row:last-child{border-bottom:none;}
.enr-row:hover{background:rgba(27,79,168,0.04);}
.enr-avatar{width:30px;height:30px;border-radius:10px;background:var(--purple-l);display:flex;align-items:center;
    justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:12px;color:var(--purple);flex-shrink:0;}
.enr-time{font-size:9px;color:var(--faint);letter-spacing:0.5px;text-transform:uppercase;}

.chart-wrap{padding:16px 18px;position:relative;}
canvas{max-width:100%;display:block;}

.stat-table{width:100%;border-collapse:collapse;}
.stat-table td{padding:10px 18px;font-size:12px;border-bottom:1px solid rgba(27,79,168,0.06);}
.stat-table tr:last-child td{border-bottom:none;}
.stat-table td:first-child{color:var(--muted);letter-spacing:0.3px;}
.stat-table td:last-child{text-align:right;font-weight:600;color:var(--text);font-family:'Bebas Neue',sans-serif;letter-spacing:1px;font-size:15px;}

.mini-badge{display:inline-flex;align-items:center;font-size:9px;letter-spacing:0.5px;padding:3px 8px;border-radius:20px;font-weight:600;}
.badge-green {background:var(--green-l);color:var(--green-dk);}
.badge-red   {background:var(--red-l);color:var(--red);}
.badge-orange{background:var(--orange-l);color:var(--orange-dk);}
.badge-blue  {background:var(--blue-l);color:var(--blue);}
.badge-purple{background:var(--purple-l);color:var(--purple);}

.sparkline{margin-top:8px;position:relative;height:24px;}
.sparkline svg{width:100%;height:100%;}

@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.hero-card,.pm-card,.card,.patch-banner,.action-queue,.mini-card{animation:fadeIn 0.4s ease both;}

@media(max-width:1200px){
    .hero-grid{grid-template-columns:repeat(2,1fr);}
    .pm-grid{grid-template-columns:repeat(2,1fr);}
    .aq-grid{grid-template-columns:repeat(2,1fr);}
    .mini-grid{grid-template-columns:repeat(2,1fr);}
    .grid-2,.grid-3,.grid-2-1,.grid-1-2{grid-template-columns:1fr;}
}
@media(max-width:768px){
    .dash{padding:16px;}
    .cmd-title{font-size:28px;}
    .hero-grid,.pm-grid,.mini-grid,.aq-grid{grid-template-columns:1fr;}
    .hero-val{font-size:32px;}
    .pb-stats{gap:14px;}
}
</style>

<div class="dash">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="cmd-strip">
        <div class="cmd-left">
            <div class="cmd-eyebrow">
                <span class="cmd-live-dot"></span>
                Admin Command Center · Live
            </div>
            <h1 class="cmd-title">Executive Dashboard</h1>
            <div class="cmd-sub">{{ now()->format('l, d M Y') }} · Auto-updates on refresh</div>
        </div>
        <div class="cmd-right">
            <div class="cmd-live">
                <span class="cmd-live-dot"></span>
                System Online
            </div>
            <div class="period-tabs">
                <a href="?period=day"   class="period-tab {{ $period=='day'  ?'active':'' }}">Day</a>
                <a href="?period=week"  class="period-tab {{ $period=='week' ?'active':'' }}">Week</a>
                <a href="?period=month" class="period-tab {{ $period=='month'?'active':'' }}">Month</a>
                <a href="?period=patch" class="period-tab {{ $period=='patch'?'active':'' }}">Patch</a>
            </div>
        </div>
    </div>

    @if($currentPatch)
    @php
        $patchStart    = \Carbon\Carbon::parse($currentPatch->start_date);
        $patchEnd      = \Carbon\Carbon::parse($currentPatch->end_date);
        $totalDays     = max(1, $patchStart->diffInDays($patchEnd));
        $daysElapsed   = max(0, min($totalDays, (int) $patchStart->diffInDays(now())));
        $patchProgress = round(($daysElapsed / $totalDays) * 100);
    @endphp
    <div class="patch-banner">
        <div class="pb-info">
            <div class="pb-label">Current Active Patch</div>
            <div class="pb-name">{{ $currentPatch->name }}</div>
            <div class="pb-dates">{{ $patchStart->format('d M') }} → {{ $patchEnd->format('d M Y') }}</div>
        </div>
        <div class="pb-prog-wrap">
            <div class="pb-prog-label">
                <span>Patch Progress</span>
                <span>{{ $patchProgress }}%</span>
            </div>
            <div class="pb-prog-track">
                <div class="pb-prog-fill" style="width:{{ $patchProgress }}%;"></div>
            </div>
            <div class="pb-dates" style="margin-top:6px;">Day {{ $daysElapsed }} of {{ $totalDays }}</div>
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
                <div class="pb-stat-val">{{ number_format($periodRevenue/1000,0) }}K</div>
                <div class="pb-stat-label">LE Revenue</div>
            </div>
        </div>
    </div>
    @endif

        <div class="sec">
        <div class="sec-num">1</div>
        <div class="sec-label">Action Queue</div>
        <div class="sec-desc">Items needing your attention</div>
        <div class="sec-line"></div>
    </div>

    <div class="action-queue">
        <div class="aq-head">
            <div class="aq-title">
                <div class="aq-title-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                </div>
                Priority Actions
            </div>
            <div class="aq-count {{ $totalActions == 0 ? 'zero' : '' }}">
                @if($totalActions > 0)
                    <span class="aq-count-dot"></span>
                    {{ $totalActions }} pending
                @else
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    All Clear
                @endif
            </div>
        </div>

        @if($totalActions == 0)
        <div class="aq-empty">
            <div class="aq-empty-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="font-weight:600;color:var(--green);letter-spacing:0.5px;">Great work! No pending actions.</div>
            <div style="margin-top:5px;color:var(--faint);font-size:11px;">All requests, reviews, and approvals are up to date.</div>
        </div>
        @else
        <div class="aq-grid">
            @if($overdueInstallments > 0)
            <a href="{{ route('admin.outstanding.index') }}" class="aq-item aq-critical">
                <div class="aq-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="aq-info">
                    <div class="aq-label">Overdue Installments</div>
                    <div class="aq-val">{{ $overdueInstallments }}</div>
                </div>
                <svg class="aq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif

            @if($pendingApprovals > 0)
            <a href="{{ route('admin.installments.index') }}" class="aq-item aq-warn">
                <div class="aq-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div class="aq-info">
                    <div class="aq-label">Installment Approvals</div>
                    <div class="aq-val">{{ $pendingApprovals }}</div>
                </div>
                <svg class="aq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif

            @if($pendingRefunds > 0)
            <a href="{{ route('admin.refunds.index') }}" class="aq-item aq-warn">
                <div class="aq-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2">
                        <path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
                <div class="aq-info">
                    <div class="aq-label">Refund Requests</div>
                    <div class="aq-val">{{ $pendingRefunds }}</div>
                </div>
                <svg class="aq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif

            @if($pendingReports > 0)
            <a href="{{ route('admin.reports.index') }}" class="aq-item aq-info-c">
                <div class="aq-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div class="aq-info">
                    <div class="aq-label">Reports to Review</div>
                    <div class="aq-val">{{ $pendingReports }}</div>
                </div>
                <svg class="aq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif

            @if($expiringPostponements > 0)
            <a href="{{ route('admin.postponed.index') }}" class="aq-item aq-critical">
                <div class="aq-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="aq-info">
                    <div class="aq-label">Postponements Expiring</div>
                    <div class="aq-val">{{ $expiringPostponements }}</div>
                </div>
                <svg class="aq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif

            @if($pendingInstallments > 0)
            <a href="{{ route('admin.outstanding.index') }}" class="aq-item aq-info-c">
                <div class="aq-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2">
                        <rect x="3" y="4" width="18" height="16" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    </svg>
                </div>
                <div class="aq-info">
                    <div class="aq-label">Pending Installments</div>
                    <div class="aq-val">{{ $pendingInstallments }}</div>
                </div>
                <svg class="aq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif
        </div>
        @endif
    </div>

    <div class="sec">
        <div class="sec-num">2</div>
        <div class="sec-label">Executive Overview</div>
        <div class="sec-desc">At-a-glance snapshot</div>
        <div class="sec-line"></div>
    </div>

    <div class="hero-grid">

        <div class="hero-card" style="--hc:var(--blue);--hi:var(--blue-l)">
            <div class="hero-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
            <div class="hero-label">Period Revenue</div>
            <div class="hero-val">{{ number_format($periodRevenue) }} <span class="hero-val-sm">LE</span></div>
            <div class="hero-meta">
                @if($revenueTrendPct > 0)
                    <span class="trend-badge trend-up">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="18 15 12 9 6 15"/></svg>
                        +{{ $revenueTrendPct }}%
                    </span>
                @elseif($revenueTrendPct < 0)
                    <span class="trend-badge trend-down">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
                        {{ $revenueTrendPct }}%
                    </span>
                @else
                    <span class="trend-badge trend-flat">—</span>
                @endif
                <span class="hero-sub">vs previous {{ $period }}</span>
            </div>

            @if(!empty($revenueSparkline))
            @php
                $spMax = max($revenueSparkline) ?: 1;
                $spWidth = 100; $spStep = $spWidth / max(1, count($revenueSparkline) - 1);
                $spPoints = collect($revenueSparkline)->map(function($v, $i) use ($spMax, $spStep) {
                    return round($i * $spStep, 1) . ',' . round(20 - ($v / $spMax * 18), 1);
                })->join(' ');
            @endphp
            <div class="sparkline">
                <svg viewBox="0 0 100 22" preserveAspectRatio="none">
                    <polyline fill="none" stroke="var(--blue)" stroke-width="1.5" points="{{ $spPoints }}" opacity="0.8"/>
                    <polyline fill="rgba(27,79,168,0.1)" stroke="none" points="0,22 {{ $spPoints }} 100,22"/>
                </svg>
            </div>
            @endif
        </div>

        <div class="hero-card" style="--hc:var(--green);--hi:var(--green-l)">
            <div class="hero-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
            </div>
            <div class="hero-label">New Enrollments</div>
            <div class="hero-val">{{ $periodEnrollments }}</div>
            <div class="hero-meta">
                @if($enrollmentsTrendPct > 0)
                    <span class="trend-badge trend-up">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="18 15 12 9 6 15"/></svg>
                        +{{ $enrollmentsTrendPct }}%
                    </span>
                @elseif($enrollmentsTrendPct < 0)
                    <span class="trend-badge trend-down">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
                        {{ $enrollmentsTrendPct }}%
                    </span>
                @else
                    <span class="trend-badge trend-flat">—</span>
                @endif
                <span class="hero-sub">vs previous {{ $period }}</span>
            </div>
        </div>

        <div class="hero-card" style="--hc:var(--red);--hi:var(--red-l)">
            <div class="hero-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div class="hero-label">Outstanding Balance</div>
            <div class="hero-val">{{ number_format($totalOutstanding) }} <span class="hero-val-sm">LE</span></div>
            <div class="hero-meta">
                <span class="mini-badge badge-red">{{ $overdueInstallments }} overdue</span>
                <span class="hero-sub">across all enrollments</span>
            </div>
            <a href="{{ route('admin.outstanding.index') }}" class="hero-link">View →</a>
        </div>

        <div class="hero-card" style="--hc:var(--orange);--hi:var(--orange-l)">
            <div class="hero-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                </svg>
            </div>
            <div class="hero-label">Target Achievement</div>
            <div class="hero-val">{{ $targetPct }}<span class="hero-val-sm">%</span></div>
            <div class="hero-meta">
                <span class="hero-sub">{{ number_format($totalAchieved) }} / {{ number_format($totalTarget) }} LE</span>
            </div>
        </div>
    </div>

    <div class="sec">
        <div class="sec-num">3</div>
        <div class="sec-label">Financial Breakdown</div>
        <div class="sec-desc">Revenue streams & payment methods</div>
        <div class="sec-line"></div>
    </div>

    @php
        $totalPMRev = $cashRevenue + $instapayRevenue + $vodafoneRevenue + $cardRevenue;
    @endphp
    <div class="pm-grid">
        <div class="pm-card" style="--pc:var(--green);--pi:var(--green-l)">
            <div class="pm-top">
                <div class="pm-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                @if($totalPMRev > 0)<span class="pm-share">{{ round($cashRevenue/$totalPMRev*100) }}%</span>@endif
            </div>
            <div class="pm-name">Cash</div>
            <div class="pm-val">{{ number_format($cashRevenue) }}</div>
            <div class="pm-sub">LE · {{ $cashCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $totalPMRev>0?round($cashRevenue/$totalPMRev*100):0 }}%"></div></div>
        </div>

        <div class="pm-card" style="--pc:var(--purple);--pi:var(--purple-l)">
            <div class="pm-top">
                <div class="pm-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--purple)" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/>
                    </svg>
                </div>
                @if($totalPMRev > 0)<span class="pm-share">{{ round($instapayRevenue/$totalPMRev*100) }}%</span>@endif
            </div>
            <div class="pm-name">InstaPay</div>
            <div class="pm-val">{{ number_format($instapayRevenue) }}</div>
            <div class="pm-sub">LE · {{ $instapayCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $totalPMRev>0?round($instapayRevenue/$totalPMRev*100):0 }}%"></div></div>
        </div>

        <div class="pm-card" style="--pc:var(--red);--pi:var(--red-l)">
            <div class="pm-top">
                <div class="pm-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
                    </svg>
                </div>
                @if($totalPMRev > 0)<span class="pm-share">{{ round($vodafoneRevenue/$totalPMRev*100) }}%</span>@endif
            </div>
            <div class="pm-name">Vodafone Cash</div>
            <div class="pm-val">{{ number_format($vodafoneRevenue) }}</div>
            <div class="pm-sub">LE · {{ $vodafoneCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $totalPMRev>0?round($vodafoneRevenue/$totalPMRev*100):0 }}%"></div></div>
        </div>

        <div class="pm-card" style="--pc:var(--blue);--pi:var(--blue-l)">
            <div class="pm-top">
                <div class="pm-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
                @if($totalPMRev > 0)<span class="pm-share">{{ round($cardRevenue/$totalPMRev*100) }}%</span>@endif
            </div>
            <div class="pm-name">Card</div>
            <div class="pm-val">{{ number_format($cardRevenue) }}</div>
            <div class="pm-sub">LE · {{ $cardCount }} transactions</div>
            <div class="pm-bar"><div class="pm-bar-fill" style="width:{{ $totalPMRev>0?round($cardRevenue/$totalPMRev*100):0 }}%"></div></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                    </div>
                    Revenue Trend
                </div>
                <span class="mini-badge badge-blue">Last 14 Days</span>
            </div>
            <div class="chart-wrap" style="height:220px;"><canvas id="revenueChart"></canvas></div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                    </div>
                    New Enrollments
                </div>
                <span class="mini-badge badge-green">Last 14 Days</span>
            </div>
            <div class="chart-wrap" style="height:220px;"><canvas id="enrollChart"></canvas></div>
        </div>
    </div>

    <div class="grid-2">
        @if($revenueByCourse->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                    </div>
                    Revenue by Course
                </div>
                <span class="mini-badge badge-blue">Top 6</span>
            </div>
            <div class="card-body">
                @php $maxRev = $revenueByCourse->max('total'); @endphp
                @foreach($revenueByCourse as $rc)
                <div class="rev-row">
                    <div style="font-size:12px;color:var(--text);font-weight:500;min-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $rc->name }}</div>
                    <div class="rev-bar-track"><div class="rev-bar-fill" style="width:{{ $maxRev>0?round($rc->total/$maxRev*100):0 }}%;background:linear-gradient(90deg,var(--blue),var(--blue-2))"></div></div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--blue);letter-spacing:1px;white-space:nowrap;margin-left:8px;">{{ number_format($rc->total/1000,1) }}K</div>
                    <span style="font-size:9px;color:var(--faint);margin-left:6px;">{{ $rc->count }} tx</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($revenueByBranch->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    Revenue by Branch
                </div>
                <span class="mini-badge badge-orange">Location Split</span>
            </div>
            <div class="card-body">
                @php $maxBranch = $revenueByBranch->max('total'); @endphp
                @foreach($revenueByBranch as $rb)
                <div class="rev-row">
                    <div style="font-size:12px;color:var(--text);font-weight:500;min-width:120px;">{{ $rb->name }}</div>
                    <div class="rev-bar-track"><div class="rev-bar-fill" style="width:{{ $maxBranch>0?round($rb->total/$maxBranch*100):0 }}%;background:linear-gradient(90deg,var(--orange),#FFB347)"></div></div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--orange);letter-spacing:1px;white-space:nowrap;margin-left:8px;">{{ number_format($rb->total/1000,1) }}K</div>
                    <span style="font-size:9px;color:var(--faint);margin-left:6px;">{{ $rb->count }} tx</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="mini-grid" style="margin-bottom:24px;">
        <div class="mini-card" style="--mc:var(--muted)">
            <div class="mini-label">Total Refunded</div>
            <div class="mini-val">{{ number_format($totalRefunded) }} <span style="font-size:12px;color:var(--faint);">LE</span></div>
            <div class="mini-sub">Period total</div>
        </div>
        <div class="mini-card" style="--mc:var(--green)">
            <div class="mini-label">All-time Revenue</div>
            <div class="mini-val">{{ number_format($totalRevenue/1000,0) }}K <span style="font-size:12px;color:var(--faint);">LE</span></div>
            <div class="mini-sub">Since inception</div>
        </div>
        <div class="mini-card" style="--mc:var(--blue)">
            <div class="mini-label">Avg Deposit</div>
            <div class="mini-val">
                @if($periodEnrollments > 0)
                {{ number_format($periodRevenue/$periodEnrollments) }} <span style="font-size:12px;color:var(--faint);">LE</span>
                @else — @endif
            </div>
            <div class="mini-sub">Per enrollment</div>
        </div>
        <div class="mini-card" style="--mc:var(--purple)">
            <div class="mini-label">Refund Rate</div>
            <div class="mini-val">
                @if($periodRevenue > 0)
                {{ round($totalRefunded/$periodRevenue*100,1) }}<span style="font-size:12px;">%</span>
                @else — @endif
            </div>
            <div class="mini-sub">Of period revenue</div>
        </div>
    </div>

    <div class="sec">
        <div class="sec-num">4</div>
        <div class="sec-label">Academic Operations</div>
        <div class="sec-desc">Courses, students, capacity</div>
        <div class="sec-line"></div>
    </div>

    <div class="mini-grid" style="margin-bottom:24px;">
        <div class="mini-card" style="--mc:var(--green)">
            <div class="mini-label">Active Courses</div>
            <div class="mini-val">{{ $activeCourses }}</div>
            <div class="mini-sub">Currently running</div>
        </div>
        <div class="mini-card" style="--mc:var(--teal)">
            <div class="mini-label">Upcoming</div>
            <div class="mini-val">{{ $upcomingCourses }}</div>
            <div class="mini-sub">Not yet started</div>
        </div>
        <div class="mini-card" style="--mc:var(--blue)">
            <div class="mini-label">Active Students</div>
            <div class="mini-val">{{ $totalStudents }}</div>
            <div class="mini-sub">Enrolled</div>
        </div>
        <div class="mini-card" style="--mc:var(--red)">
            <div class="mini-label">Restricted</div>
            <div class="mini-val">{{ $restrictedStudents }}</div>
            <div class="mini-sub">Attendance blocked</div>
        </div>
    </div>

    <div class="mini-grid" style="margin-bottom:24px;">
        <div class="mini-card" style="--mc:var(--orange)">
            <div class="mini-label">Waiting List</div>
            <div class="mini-val">{{ $waitingList }}</div>
            <div class="mini-sub">Students waiting</div>
        </div>
        <div class="mini-card" style="--mc:var(--purple)">
            <div class="mini-label">Course Capacity</div>
            <div class="mini-val">{{ $avgCapacity }}<span style="font-size:14px;">%</span></div>
            <div class="mini-sub">Avg utilization</div>
        </div>
        <div class="mini-card" style="--mc:var(--green)">
            <div class="mini-label">Completed</div>
            <div class="mini-val">{{ $completedCourses }}</div>
            <div class="mini-sub">In period</div>
        </div>
        <div class="mini-card" style="--mc:var(--teal)">
            <div class="mini-label">Postponements</div>
            <div class="mini-val">{{ $expiringPostponements + \App\Models\Enrollment\Postponement::where('status','Active')->count() - $expiringPostponements }}</div>
            <div class="mini-sub">Active total</div>
        </div>
    </div>

    <div class="sec">
        <div class="sec-num">5</div>
        <div class="sec-label">Sales & Performance</div>
        <div class="sec-desc">Leads funnel, CS ranking, recent activity</div>
        <div class="sec-line"></div>
    </div>

    <div class="mini-grid" style="margin-bottom:20px;">
        <div class="mini-card" style="--mc:var(--purple)">
            <div class="mini-label">Total Leads</div>
            <div class="mini-val">{{ $totalLeads }}</div>
            <div class="mini-sub">This {{ $period }}</div>
        </div>
        <div class="mini-card" style="--mc:var(--green)">
            <div class="mini-label">Converted</div>
            <div class="mini-val">{{ $convertedLeads }}</div>
            <div class="mini-sub">Registered</div>
        </div>
        <div class="mini-card" style="--mc:var(--orange)">
            <div class="mini-label">Conversion Rate</div>
            <div class="mini-val">{{ $conversionRate }}<span style="font-size:14px;">%</span></div>
            <div class="mini-sub">Leads → Enrolled</div>
        </div>
        <div class="mini-card" style="--mc:var(--blue)">
            <div class="mini-label">New Enrollments</div>
            <div class="mini-val">{{ $periodEnrollments }}</div>
            <div class="mini-sub">Signed up</div>
        </div>
    </div>

    <div class="grid-2-1">

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2.5"><path d="M12 15l-6.16 3.24 1.18-6.84L2 6.63l6.87-1 3.13-6.22 3.13 6.22 6.87 1-5.02 4.77 1.18 6.84z"/></svg>
                    </div>
                    CS Performance
                </div>
                <span class="mini-badge badge-blue">{{ match($period){'patch'=>'Current Patch','day'=>'Today','week'=>'This Week','month'=>'This Month',default=>'All Time'} }}</span>
            </div>
            @forelse($csEmployees as $i => $cs)
            @php
                $rankClass = match($i){0=>'gold',1=>'silver',2=>'bronze',default=>''};
                $barColor  = $cs->achievement>=100?'var(--green)':($cs->achievement>=70?'var(--blue)':'var(--orange)');
            @endphp
            <div class="cs-row">
                <div class="cs-rank {{ $rankClass }}">{{ $i+1 }}</div>
                <div class="cs-avatar">{{ strtoupper(substr($cs->full_name,0,1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;color:var(--text);font-weight:600;">{{ $cs->full_name }}</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                        <div class="cs-prog-track" style="flex:1;"><div class="cs-prog-fill" style="width:{{ min(100,$cs->achievement) }}%;background:{{ $barColor }};"></div></div>
                        <span style="font-size:10px;color:{{ $barColor }};font-weight:700;white-space:nowrap;">{{ $cs->achievement }}%</span>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;margin-left:12px;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:17px;color:var(--blue);letter-spacing:1px;">{{ number_format($cs->patch_revenue) }} <span style="font-size:9px;color:var(--faint);">LE</span></div>
                    <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $cs->registrations }} regs · {{ $cs->leads_count }} leads</div>
                </div>
            </div>
            @empty
            <div style="padding:30px;text-align:center;color:var(--faint);font-size:12px;">No CS data available</div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--purple)" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    Recent Activity
                </div>
            </div>
            @forelse($recentEnrollments as $enr)
            <div class="enr-row">
                <div class="enr-avatar">{{ strtoupper(substr($enr->student?->full_name ?? 'S', 0, 1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;color:var(--text);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enr->student?->full_name ?? '—' }}</div>
                    <div style="font-size:10px;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $enr->courseTemplate?->name ?? $enr->courseInstance?->courseTemplate?->name ?? '—' }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div class="enr-time">{{ \Carbon\Carbon::parse($enr->created_at)->diffForHumans(null, true) }}</div>
                    <div style="font-size:9px;color:var(--faint);margin-top:2px;">{{ $enr->createdByCs?->full_name ?? '—' }}</div>
                </div>
            </div>
            @empty
            <div style="padding:30px;text-align:center;color:var(--faint);font-size:12px;">No recent enrollments</div>
            @endforelse
        </div>
    </div>

    <div class="sec">
        <div class="sec-num">6</div>
        <div class="sec-label">Workforce</div>
        <div class="sec-desc">Team snapshot</div>
        <div class="sec-line"></div>
    </div>

    <div class="card">
        <table class="stat-table">
            <tr>
                <td>Total Active Employees</td>
                <td>{{ $totalEmployees }}</td>
            </tr>
            <tr>
                <td>Admins</td>
                <td>{{ $totalAdmin }}</td>
            </tr>
            <tr>
                <td>Teachers</td>
                <td>{{ $totalTeachers }}</td>
            </tr>
            <tr>
                <td>Customer Service Team</td>
                <td>{{ $totalCS }}</td>
            </tr>            
            <tr>
                <td>Student Care Team</td>
                <td>{{ $totalSC }}</td>
            </tr> 
            <tr>
                <td>Total Refunds This Period</td>
                <td>{{ number_format($totalRefunded) }} LE</td>
            </tr>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dmSans = { family: 'DM Sans', size: 10 };

    const rc = document.getElementById('revenueChart');
    if (rc) {
        new Chart(rc, {
            type: 'line',
            data: {
                labels: @json($trendDays),
                datasets: [{
                    label: 'Revenue (LE)',
                    data: @json($trendValues),
                    borderColor: '#1B4FA8',
                    backgroundColor: 'rgba(27,79,168,0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#1B4FA8',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { font: dmSans, color: '#7A8A9A' }, grid: { color: 'rgba(27,79,168,0.05)' } },
                    x: { ticks: { font: dmSans, color: '#7A8A9A' }, grid: { display: false } }
                }
            }
        });
    }

    const ec = document.getElementById('enrollChart');
    if (ec) {
        new Chart(ec, {
            type: 'bar',
            data: {
                labels: @json($enrollDays),
                datasets: [{
                    label: 'Enrollments',
                    data: @json($enrollValues),
                    backgroundColor: 'rgba(5,150,105,0.7)',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { font: dmSans, color: '#7A8A9A', stepSize: 1 }, grid: { color: 'rgba(27,79,168,0.05)' } },
                    x: { ticks: { font: dmSans, color: '#7A8A9A' }, grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endsection