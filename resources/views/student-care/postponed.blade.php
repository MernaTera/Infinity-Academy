@php
    $__user    = auth()->user();
    $__isSC    = $__user?->isSC() ?? false;
    $__isAdmin = $__user?->isAdmin() ?? false;
    $__layout  = $__isSC ? 'student-care.layouts.app'
               : ($__isAdmin ? 'admin.layouts.app' : 'layouts.leads');
    $__eyebrow = $__isSC ? 'Student Care'
               : ($__isAdmin ? 'Administration' : 'Customer Service');
    $canRegister = $canRegister ?? false;
    $expireBase  = $expireBase ?? url('student-care/postponed');
@endphp

@extends($__layout)
@section('title', 'Postponed Students')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        /* --blue:#F5911E; --blue-dk:#C47010; --blue-l:rgba(245,145,30,0.07); */
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:rgba(255,255,255,0.72); --border:rgba(255,255,255,0.6);
        --glass-sh:0 12px 34px -14px rgba(23,45,90,0.2);
    }
    * { box-sizing:border-box; }

    .pp-page {
        min-height:100vh; padding:40px 34px 52px; font-family:'DM Sans',sans-serif; color:var(--text);
        background:#F8F6F2;
    }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .page-header { margin-bottom:26px; }
    .page-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:7px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .page-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--blue); box-shadow:0 0 8px var(--blue); }
    .page-title { font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0; }

    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
    .kpi-card { background:var(--card); -webkit-backdrop-filter:blur(20px) saturate(150%); backdrop-filter:blur(20px) saturate(150%); border:1px solid var(--border); border-radius:18px; padding:18px 20px; position:relative; overflow:hidden; box-shadow:var(--glass-sh); transition:transform 0.2s, box-shadow 0.2s; }
    .kpi-card:hover { transform:translateY(-3px); box-shadow:0 18px 42px -14px rgba(23,45,90,0.28); }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kc,var(--blue)); }
    .kpi-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:5px; font-weight:600; }
    .kpi-val { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:1px; color:var(--kc,var(--blue)); line-height:1; }
    .kpi-sub { font-size:9px; color:var(--faint); margin-top:3px; }

    .sec-label { display:flex; align-items:center; gap:10px; font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--text); font-weight:700; margin-bottom:14px; }
    .sec-label::before { content:''; width:20px; height:3px; border-radius:3px; background:linear-gradient(90deg, var(--blue), var(--blue)); flex-shrink:0; }

    .tab-nav { display:flex; gap:2px; margin-bottom:20px; border-bottom:1px solid var(--border); }
    .tab-btn { padding:11px 22px; font-size:10px; letter-spacing:2px; text-transform:uppercase; background:transparent; border:none; cursor:pointer; color:var(--muted); font-family:'DM Sans',sans-serif; font-weight:600; position:relative; transition:color 0.2s; border-radius:10px 10px 0 0; }
    .tab-btn::after { content:''; position:absolute; bottom:-1px; left:0; right:0; height:2px; background:var(--blue); transform:scaleX(0); transition:transform 0.3s cubic-bezier(0.16,1,0.3,1); }
    .tab-btn:hover { color:var(--blue); }
    .tab-btn.active { color:var(--blue); }
    .tab-btn.active::after { transform:scaleX(1); }
    .tab-count { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; border-radius:9px; background:rgba(27,79,168,0.1); font-size:9px; color:var(--blue); margin-left:6px; font-weight:700; }

    .toolbar { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
    .search-wrap { position:relative; flex:1; min-width:200px; }
    .search-wrap svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input { width:100%; padding:11px 14px 11px 40px; border:1px solid var(--border); border-radius:12px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:rgba(255,255,255,0.6); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); outline:none; box-sizing:border-box; box-shadow:var(--glass-sh); }
    .search-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }

    .postponed-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:16px; }
    .pp-card { background:var(--card); -webkit-backdrop-filter:blur(20px) saturate(150%); backdrop-filter:blur(20px) saturate(150%); border:1px solid var(--border); border-radius:18px; overflow:hidden; position:relative; box-shadow:var(--glass-sh); transition:transform 0.2s, box-shadow 0.2s; }
    .pp-card:hover { transform:translateY(-3px); box-shadow:0 18px 42px -14px rgba(23,45,90,0.28); }
    .pp-card.status-active::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,transparent,#C47010,transparent); }
    .pp-card.status-expired::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,transparent,#DC2626,transparent); }
    .pp-card.expiring-soon { border-color:rgba(220,38,38,0.25); }

    .pc-header { padding:16px 18px 12px; border-bottom:1px solid var(--border); display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
    .pc-student { font-weight:700; color:var(--text); font-size:14px; }
    .pc-course { font-size:11px; color:var(--muted); margin-top:3px; }

    .badge { display:inline-flex; align-items:center; gap:4px; font-size:9px; letter-spacing:1px; text-transform:uppercase; padding:4px 9px; border-radius:20px; font-weight:600; white-space:nowrap; }
    .badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; flex-shrink:0; }
    .badge-active { color:var(--blue-dk); background:var(--blue-l); border:1px solid rgba(245,145,30,0.2); }
    .badge-expired { color:var(--red); background:var(--red-l); border:1px solid rgba(220,38,38,0.15); }
    .badge-returned { color:var(--green-dk); background:var(--green-l); border:1px solid rgba(5,150,105,0.15); }
    .badge-soon { color:var(--red); background:var(--red-l); border:1px solid rgba(220,38,38,0.15); animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }

    .pc-body { padding:14px 18px; }
    .pc-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; }
    .pc-meta-label { font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); margin-bottom:3px; }
    .pc-meta-val { font-size:12px; color:var(--text); font-weight:600; }
    .pc-meta-val.blue { color:var(--blue-dk); }
    .pc-meta-val.red { color:var(--red); }

    .timeline { display:flex; align-items:center; gap:0; margin-bottom:14px; padding:10px 0; }
    .tl-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .tl-dot-start { background:var(--blue); }
    .tl-dot-end { background:var(--green); }
    .tl-dot-expired { background:var(--red); }
    .tl-line { flex:1; height:2px; position:relative; }
    .tl-line-inner { position:absolute; top:0; left:0; height:100%; border-radius:1px; transition:width 0.6s; }
    .tl-line-bg { background:rgba(27,79,168,0.1); width:100%; height:2px; border-radius:1px; }
    .tl-labels { display:flex; justify-content:space-between; font-size:9px; color:var(--faint); margin-bottom:10px; }

    .hours-prog { background:rgba(27,79,168,0.1); border-radius:3px; height:5px; overflow:hidden; margin-top:4px; }
    .hours-prog-fill { height:5px; border-radius:3px; background:linear-gradient(90deg,#1B4FA8,#2D6FDB); }

    .reason-box { background:var(--blue-l); border:1px solid rgba(245,145,30,0.18); border-radius:10px; padding:9px 11px; font-size:11px; color:var(--blue-dk); line-height:1.4; margin-bottom:12px; }

    .pc-footer { padding:12px 18px; border-top:1px solid var(--border); display:flex; gap:8px; }

    .btn-sm { display:inline-flex; align-items:center; gap:4px; padding:7px 15px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; border-radius:9px; border:1px solid; background:transparent; cursor:pointer; font-family:'DM Sans',sans-serif; font-weight:600; transition:all 0.2s; white-space:nowrap; text-decoration:none; }
    .btn-resume { color:var(--green-dk); border-color:rgba(5,150,105,0.3); background:var(--green-l); }
    .btn-resume:hover { background:var(--green); color:#fff; }
    .btn-expire { color:var(--red); border-color:rgba(220,38,38,0.25); background:var(--red-l); }
    .btn-expire:hover { background:var(--red); color:#fff; }

    .empty-state { text-align:center; padding:60px 24px; color:var(--faint); }
    .empty-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:3px; margin-bottom:6px; color:var(--muted); }

    #confirmModal { display:none; position:fixed; inset:0; background:rgba(15,31,61,0.5); -webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px); align-items:center; justify-content:center; z-index:999; padding:20px; }
    #confirmModal.show { display:flex; }
    .modal-box { width:100%; max-width:420px; background:rgba(255,255,255,0.95); -webkit-backdrop-filter:blur(28px) saturate(160%); backdrop-filter:blur(28px) saturate(160%); border:1px solid var(--border); border-radius:20px; overflow:hidden; position:relative; box-shadow:0 30px 70px -18px rgba(23,45,90,0.4); }
    .modal-box::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent); }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); }
    .modal-title { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:2px; color:var(--text); }
    .modal-body { padding:16px 22px; font-size:13px; color:var(--muted); line-height:1.6; }
    .modal-footer { padding:12px 22px 18px; border-top:1px solid var(--border); display:flex; gap:10px; justify-content:flex-end; }

    @media(max-width:768px){ .pp-page{ padding:20px 14px 36px; } .kpi-grid{ grid-template-columns:repeat(2,1fr); } .postponed-grid{ grid-template-columns:1fr; } .page-title{ font-size:34px; } }
</style>

<div class="pp-page" @unless($__isSC) style="padding:30px" @endunless>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div class="page-eyebrow">{{ $__eyebrow }}</div>
        <h1 class="page-title">Postponed Students</h1>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('error') }}</div>
    @endif

    
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Active Postponements</div>
            <div class="kpi-val">{{ $stats['active'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Expiring Soon</div>
            <div class="kpi-val">{{ $stats['expiring_soon'] }}</div>
            <div class="kpi-sub">within 7 days</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Expired</div>
            <div class="kpi-val">{{ $stats['expired'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Returned</div>
            <div class="kpi-val">{{ $stats['returned'] }}</div>
        </div>
    </div>

    
    <div class="tab-nav">
        <button class="tab-btn active" onclick="showTab('groupTab', this)">
            Group
            <span class="tab-count">{{ $groupPostponed->count() }}</span>
        </button>
        <button class="tab-btn" onclick="showTab('privateTab', this)">
            Private
            <span class="tab-count">{{ $privatePostponed->count() }}</span>
        </button>
    </div>

    
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="ppSearch" class="search-input" placeholder="Search by student or course...">
        </div>
    </div>

    
    <div id="groupTab">
        @if($groupPostponed->isEmpty())
        <div class="empty-state">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1" style="margin:0 auto 14px;display:block">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <div class="empty-title">No Group Postponements</div>
            <div style="font-size:12px">No active group postponements found</div>
        </div>
        @else
        <div class="postponed-grid" id="groupGrid">
            @foreach($groupPostponed as $pp)
            @php
                $enrollment   = $pp->enrollment;
                $start        = \Carbon\Carbon::parse($pp->start_date);
                $end          = \Carbon\Carbon::parse($pp->expected_return_date);
                $today        = now();
                $totalDays    = max(1, $start->diffInDays($end));
                $elapsed      = max(0, min($totalDays, $start->diffInDays($today)));
                $pct          = round($elapsed / $totalDays * 100);
                $daysLeft     = max(0, (int) $today->diffInDays($end, false));
                $isExpiringSoon = $pp->status === 'Active' && $daysLeft <= 7;
                $maxDays      = 90;
                $totalPostpDays = $start->diffInDays($end);
                $overMax      = $totalPostpDays > $maxDays;

                $courseName    = $enrollment?->courseTemplate?->name
                                ?? $enrollment?->courseInstance?->courseTemplate?->name
                                ?? '—';
                $levelName     = $enrollment?->sublevel?->name ?? $enrollment?->level?->name;
                $attendedCount = $enrollment?->attendances?->where('status', 'Present')->count() ?? 0;

                $cardClass = $pp->status === 'Expired' ? 'status-expired' :
                             ($isExpiringSoon ? 'pp-card status-active expiring-soon' : 'status-active');
            @endphp
            <div class="pp-card {{ $cardClass }}"
                 data-name="{{ strtolower($enrollment?->student?->full_name ?? '') }}"
                 data-course="{{ strtolower($courseName) }}">

                <div class="pc-header">
                    <div>
                        <div class="pc-student">{{ $enrollment?->student?->full_name ?? '—' }}</div>
                        <div class="pc-course">
                            {{ $courseName }}
                            @if($levelName)<span style="color:#1B4FA8"> · {{ $levelName }}</span>@endif
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-end">
                        @if($pp->status === 'Active')
                            @if($isExpiringSoon)
                                <span class="badge badge-soon">⚠ {{ $daysLeft }}d left</span>
                            @else
                                <span class="badge badge-active">Active</span>
                            @endif
                        @elseif($pp->status === 'Expired')
                            <span class="badge badge-expired">Expired</span>
                        @else
                            <span class="badge badge-returned">Returned</span>
                        @endif
                        @if($overMax)
                        <span style="font-size:9px;color:#DC2626;letter-spacing:1px;text-transform:uppercase">Exceeds 3 months</span>
                        @endif
                    </div>
                </div>

                <div class="pc-body">

                    
                    <div class="tl-labels">
                        <span>{{ $start->format('d M') }}</span>
                        <span>Return: {{ $end->format('d M Y') }}</span>
                    </div>
                    <div class="timeline">
                        <div class="tl-dot tl-dot-start"></div>
                        <div class="tl-line">
                            <div class="tl-line-bg"></div>
                            <div class="tl-line-inner"
                                 style="width:{{ $pct }}%;background:{{ $pp->status === 'Expired' ? '#DC2626' : '#C47010' }}">
                            </div>
                        </div>
                        <div class="tl-dot {{ $pp->status === 'Expired' ? 'tl-dot-expired' : 'tl-dot-end' }}"></div>
                    </div>

                    
                    <div class="pc-meta-grid">
                        <div>
                            <div class="pc-meta-label">Sessions Attended</div>
                            <div class="pc-meta-val blue">{{ $attendedCount }} {{ \Illuminate\Support\Str::plural('session', $attendedCount) }}</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Postponement Duration</div>
                            <div class="pc-meta-val {{ $overMax ? 'red' : '' }}">{{ $totalPostpDays }} days</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Start Date</div>
                            <div class="pc-meta-val">{{ $start->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Expected Return</div>
                            <div class="pc-meta-val {{ $isExpiringSoon ? 'red' : '' }}">{{ $end->format('d M Y') }}</div>
                        </div>
                    </div>

                    
                    @if($pp->reason)
                    <div class="reason-box">
                        <strong>Reason:</strong> {{ $pp->reason }}
                    </div>
                    @endif

                    
                    <div style="font-size:10px;color:#AAB8C8">
                        Postponed by {{ $pp->createdBy?->full_name ?? '—' }}
                        · {{ \Carbon\Carbon::parse($pp->created_at)->format('d M Y') }}
                    </div>

                </div>

                @if($pp->status === 'Active')
                <div class="pc-footer">
                    @if($canRegister)
                    <a class="btn-sm btn-resume" href="{{ route('cs.postponed.resume', $pp->postponement_id) }}">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Resume &amp; Register
                    </a>
                    @endif
                    <button class="btn-sm btn-expire"
                        onclick="openExpire({{ $pp->postponement_id }}, '{{ addslashes($enrollment?->student?->full_name) }}')">
                        Mark Expired
                    </button>
                </div>
                @elseif($pp->status === 'Expired')
                <div class="pc-footer">
                    <span style="font-size:10px;color:#DC2626;letter-spacing:1px;text-transform:uppercase">
                        ✕ Enrollment Expired — No Refund
                    </span>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
    </div>

    
    <div id="privateTab" style="display:none">
        @if($privatePostponed->isEmpty())
        <div class="empty-state">
            <div class="empty-title">No Private Postponements</div>
            <div style="font-size:12px">No active private postponements found</div>
        </div>
        @else
        <div class="postponed-grid" id="privateGrid">
            @foreach($privatePostponed as $pp)
            @php
                $enrollment  = $pp->enrollment;
                $start       = \Carbon\Carbon::parse($pp->start_date);
                $end         = \Carbon\Carbon::parse($pp->expected_return_date);
                $today       = now();
                $totalDays   = max(1, $start->diffInDays($end));
                $elapsed     = max(0, min($totalDays, $start->diffInDays($today)));
                $pct         = round($elapsed / $totalDays * 100);
                $daysLeft    = max(0, (int) $today->diffInDays($end, false));
                $isExpiringSoon = $pp->status === 'Active' && $daysLeft <= 7;

                $courseName     = $enrollment?->courseTemplate?->name
                                ?? $enrollment?->courseInstance?->courseTemplate?->name
                                ?? 'Private Lessons';

                $hoursRemaining = (float) ($enrollment?->hours_remaining ?? 0);
                $totalHours     = (float) ($enrollment?->privateBundle?->hours
                                    ?? $enrollment?->courseInstance?->total_hours
                                    ?? $hoursRemaining);
                $hoursUsed      = max(0, round($totalHours - $hoursRemaining, 2));
                $hoursPct       = $totalHours > 0 ? min(100, round($hoursUsed / $totalHours * 100)) : 0;
            @endphp
            <div class="pp-card {{ $pp->status === 'Expired' ? 'status-expired' : ($isExpiringSoon ? 'status-active expiring-soon' : 'status-active') }}"
                 data-name="{{ strtolower($enrollment?->student?->full_name ?? '') }}"
                 data-course="{{ strtolower($courseName) }}">

                <div class="pc-header">
                    <div>
                        <div class="pc-student">{{ $enrollment?->student?->full_name ?? '—' }}</div>
                        <div class="pc-course">
                            {{ $courseName }}
                            <span style="color:#1B4FA8;font-weight:500"> · Private</span>
                        </div>
                    </div>
                    @if($pp->status === 'Active')
                        @if($isExpiringSoon)
                            <span class="badge badge-soon">⚠ {{ $daysLeft }}d left</span>
                        @else
                            <span class="badge badge-active">Active</span>
                        @endif
                    @elseif($pp->status === 'Expired')
                        <span class="badge badge-expired">Expired</span>
                    @else
                        <span class="badge badge-returned">Returned</span>
                    @endif
                </div>

                <div class="pc-body">

                    
                    <div class="pc-meta-grid" style="margin-bottom:10px">
                        <div>
                            <div class="pc-meta-label">Total Hours</div>
                            <div class="pc-meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#1B4FA8;letter-spacing:1px">{{ rtrim(rtrim(number_format($totalHours, 2), '0'), '.') }}h</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Used / Remaining</div>
                            <div class="pc-meta-val">{{ rtrim(rtrim(number_format($hoursUsed, 2), '0'), '.') }}h / <span class="blue">{{ rtrim(rtrim(number_format($hoursRemaining, 2), '0'), '.') }}h</span></div>
                        </div>
                    </div>

                    
                    <div style="margin-bottom:14px">
                        <div style="display:flex;justify-content:space-between;font-size:9px;color:#AAB8C8;margin-bottom:4px">
                            <span>Used: {{ $hoursPct }}%</span>
                            <span>Remaining: {{ 100 - $hoursPct }}%</span>
                        </div>
                        <div class="hours-prog">
                            <div class="hours-prog-fill" style="width:{{ $hoursPct }}%"></div>
                        </div>
                    </div>

                    
                    <div class="tl-labels">
                        <span>{{ $start->format('d M') }}</span>
                        <span>Return: {{ $end->format('d M Y') }}</span>
                    </div>
                    <div class="timeline">
                        <div class="tl-dot tl-dot-start"></div>
                        <div class="tl-line">
                            <div class="tl-line-bg"></div>
                            <div class="tl-line-inner" style="width:{{ $pct }}%;background:{{ $pp->status === 'Expired' ? '#DC2626' : '#C47010' }}"></div>
                        </div>
                        <div class="tl-dot {{ $pp->status === 'Expired' ? 'tl-dot-expired' : 'tl-dot-end' }}"></div>
                    </div>

                    @if($pp->reason)
                    <div class="reason-box"><strong>Reason:</strong> {{ $pp->reason }}</div>
                    @endif

                    <div style="font-size:10px;color:#AAB8C8">
                        Postponed by {{ $pp->createdBy?->full_name ?? '—' }}
                        · {{ \Carbon\Carbon::parse($pp->created_at)->format('d M Y') }}
                    </div>
                </div>

                @if($pp->status === 'Active')
                <div class="pc-footer">
                    @if($canRegister)
                    <a class="btn-sm btn-resume" href="{{ route('cs.postponed.resume', $pp->postponement_id) }}">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Resume &amp; Register
                    </a>
                    @endif
                    <button class="btn-sm btn-expire"
                        onclick="openExpire({{ $pp->postponement_id }}, '{{ addslashes($enrollment?->student?->full_name) }}')">
                        Mark Expired
                    </button>
                </div>
                @elseif($pp->status === 'Expired')
                <div class="pc-footer">
                    <span style="font-size:10px;color:#DC2626;letter-spacing:1px;text-transform:uppercase">
                        ✕ Enrollment Expired — No Refund
                    </span>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

<div id="confirmModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Mark as Expired</div>
        </div>
        <div class="modal-body" id="modalBody">Are you sure?</div>
        <div class="modal-footer">
            <button type="button" onclick="closeConfirm()"
                style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer">
                Cancel
            </button>
            <form id="confirmForm" method="POST" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit"
                    style="padding:10px 22px;background:#DC2626;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Expire
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const ppExpireBase = @json($expireBase);

function showTab(tabId, btn) {
    document.getElementById('groupTab').style.display   = 'none';
    document.getElementById('privateTab').style.display = 'none';
    document.getElementById(tabId).style.display        = 'block';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function openExpire(id, studentName) {
    document.getElementById('modalBody').textContent =
        `Mark ${studentName || 'this student'}'s postponement as Expired? Their enrollment will be forfeited with no refund.`;
    document.getElementById('confirmForm').action = `${ppExpireBase}/${id}/expire`;
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirm() {
    document.getElementById('confirmModal').classList.remove('show');
}

document.getElementById('confirmModal').addEventListener('click', function (e) {
    if (e.target === this) closeConfirm();
});

document.getElementById('ppSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.pp-card[data-name]').forEach(card => {
        const match = card.dataset.name.includes(q) || card.dataset.course.includes(q);
        card.style.display = match ? '' : 'none';
    });
});
</script>
@endsection
