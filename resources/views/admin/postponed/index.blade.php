@extends('admin.layouts.app')
@section('title', 'Postponed Enrollments')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --bg:#F8F6F2;
    --ink:#1A2A4A;
    --ink-2:#3A4A6A;
    --muted:#7A8A9A;
    --faint:#AAB8C8;
    --border:rgba(27,79,168,0.08);
    --border-2:rgba(27,79,168,0.15);
    --green:#059669;
    --green-l:rgba(5,150,105,0.08);
    --blue:#1B4FA8;
    --blue-l:rgba(27,79,168,0.06);
    --orange:#F5911E;
    --orange-l:rgba(245,145,30,0.08);
    --red:#DC2626;
    --red-l:rgba(220,38,38,0.06);
    --purple:#7C3AED;
    --purple-l:rgba(124,58,237,0.07);
}
*{box-sizing:border-box}
.pp-page{background:var(--bg);min-height:100vh;padding:32px;font-family:'DM Sans',sans-serif;color:var(--ink);}

/* HEADER */
.pp-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;flex-wrap:wrap;gap:16px;}
.pp-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:6px;font-weight:500;}
.pp-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:5px;color:var(--blue);margin:0;line-height:1;}
.pp-subtitle{font-size:12px;color:var(--muted);margin-top:8px;letter-spacing:0.3px;}

/* FLASH */
.pp-flash{padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:12.5px;display:flex;align-items:center;gap:10px;}
.pp-flash.success{background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);}
.pp-flash.error{background:var(--red-l);border:1px solid rgba(220,38,38,0.15);color:var(--red);}

/* KPI STATS */
.pp-kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px;}
.pp-kpi{background:#fff;border:1px solid var(--border);border-radius:8px;padding:16px 18px;position:relative;overflow:hidden;transition:transform 0.2s;}
.pp-kpi:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(27,79,168,0.06);}
.pp-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kpi-c,var(--blue));}
.pp-kpi-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:500;}
.pp-kpi-val{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:2px;color:var(--kpi-c,var(--ink));line-height:1;}
.pp-kpi-hint{font-size:9px;color:var(--faint);margin-top:4px;letter-spacing:0.5px;}
.pp-kpi.active{--kpi-c:var(--orange);}
.pp-kpi.warning{--kpi-c:var(--red);}
.pp-kpi.returned{--kpi-c:var(--green);}
.pp-kpi.expired{--kpi-c:var(--muted);}

/* TOOLBAR */
.pp-toolbar{display:flex;gap:10px;align-items:center;margin-bottom:22px;flex-wrap:wrap;background:#fff;padding:14px 18px;border-radius:8px;border:1px solid var(--border);}
.pp-search{flex:1;max-width:300px;min-width:200px;position:relative;}
.pp-search input{width:100%;padding:9px 12px 9px 34px;background:var(--bg);border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink);outline:none;transition:border-color 0.2s;}
.pp-search input:focus{border-color:var(--blue);background:#fff;}
.pp-search svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--faint);}
.pp-pills{display:flex;gap:5px;flex-wrap:wrap;}
.pp-pill{padding:7px 13px;background:transparent;border:1px solid var(--border-2);border-radius:5px;font-size:10px;letter-spacing:1.8px;text-transform:uppercase;color:var(--muted);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:6px;text-decoration:none;}
.pp-pill:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}
.pp-pill.active{background:var(--blue);color:#fff;border-color:var(--blue);}
.pp-pill-count{background:rgba(255,255,255,0.15);padding:1px 6px;border-radius:10px;font-size:9px;font-weight:600;}
.pp-pill:not(.active) .pp-pill-count{background:var(--blue-l);color:var(--blue);}

/* CARDS GRID */
.pp-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.pp-card{background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:box-shadow 0.2s;}
.pp-card:hover{box-shadow:0 4px 16px rgba(27,79,168,0.05);}
.pp-card.expiring{border-color:rgba(220,38,38,0.2);}
.pp-card.expired{opacity:0.7;}

/* Card header */
.pp-card-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;}
.pp-card-info{flex:1;min-width:200px;}
.pp-badges{display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap;}
.pp-badge{font-size:9px;letter-spacing:1.8px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:600;}
.pp-badge-group  {background:var(--blue-l);color:var(--blue);}
.pp-badge-private{background:var(--purple-l);color:var(--purple);}
.pp-badge-active {background:var(--orange-l);color:#C47010;}
.pp-badge-expired{background:rgba(122,138,154,0.1);color:var(--muted);}
.pp-badge-returned{background:var(--green-l);color:var(--green);}
.pp-badge-expiring{background:var(--red-l);color:var(--red);}
.pp-student-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--ink);line-height:1.1;margin-bottom:4px;}
.pp-course-name{font-size:11.5px;color:var(--muted);letter-spacing:0.3px;}
.pp-phone{font-size:10px;color:var(--faint);margin-top:3px;letter-spacing:0.3px;}

/* Card body */
.pp-card-body{padding:16px 20px;}

/* Timeline visualization */
.pp-timeline{background:var(--bg);border-radius:6px;padding:14px;margin-bottom:14px;}
.pp-timeline-dates{display:flex;justify-content:space-between;font-size:10px;letter-spacing:1px;color:var(--muted);margin-bottom:8px;text-transform:uppercase;font-weight:500;}
.pp-timeline-track{position:relative;height:6px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;}
.pp-timeline-fill{height:100%;transition:width 0.4s;border-radius:3px;}
.pp-timeline-fill.ok{background:linear-gradient(90deg,var(--green),#0BA870);}
.pp-timeline-fill.warn{background:linear-gradient(90deg,var(--orange),#EF7D0E);}
.pp-timeline-fill.danger{background:linear-gradient(90deg,var(--red),#B91C1C);}
.pp-timeline-marker{position:absolute;top:-3px;width:2px;height:12px;background:var(--ink);border-radius:2px;}
.pp-timeline-labels{display:flex;justify-content:space-between;margin-top:8px;font-size:10px;color:var(--ink);font-weight:600;}
.pp-timeline-labels .today{color:var(--blue);}

/* Meta grid */
.pp-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;}
.pp-meta-item{background:var(--bg);padding:10px 12px;border-radius:5px;}
.pp-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:3px;font-weight:500;}
.pp-meta-val{font-size:13px;color:var(--ink);font-weight:600;}
.pp-meta-val.warn{color:var(--orange);}
.pp-meta-val.danger{color:var(--red);}
.pp-meta-val.success{color:var(--green);}

/* Reason */
.pp-reason{background:var(--orange-l);border-left:3px solid var(--orange);padding:9px 12px;border-radius:4px;font-size:11.5px;color:var(--ink);margin-bottom:12px;line-height:1.4;}
.pp-reason strong{font-weight:600;color:#C47010;margin-right:5px;}

/* Postponed by */
.pp-by{font-size:10px;color:var(--faint);letter-spacing:0.3px;margin-bottom:12px;padding-top:10px;border-top:1px dashed var(--border);}
.pp-by strong{color:var(--ink);font-weight:500;}

/* Actions */
.pp-actions{display:flex;gap:6px;flex-wrap:wrap;padding-top:12px;border-top:1px solid var(--border);}
.pp-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:4px;font-family:'DM Sans',sans-serif;font-size:9.5px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;border:1px solid;background:#fff;cursor:pointer;transition:all 0.2s;text-decoration:none;}
.pp-btn:hover{transform:translateY(-1px);}
.pp-btn-resume {color:var(--green);border-color:rgba(5,150,105,0.3);}
.pp-btn-resume:hover  {background:var(--green);color:#fff;border-color:var(--green);}
.pp-btn-extend {color:var(--blue);border-color:rgba(27,79,168,0.25);}
.pp-btn-extend:hover  {background:var(--blue);color:#fff;border-color:var(--blue);}
.pp-btn-expire {color:var(--orange);border-color:rgba(245,145,30,0.3);}
.pp-btn-expire:hover  {background:var(--orange);color:#fff;border-color:var(--orange);}
.pp-btn-cancel {color:var(--red);border-color:rgba(220,38,38,0.3);}
.pp-btn-cancel:hover  {background:var(--red);color:#fff;border-color:var(--red);}
.pp-btn-disabled{color:var(--faint);border-color:var(--border);cursor:default;}

/* Status footer for expired */
.pp-status-footer{padding:10px 20px;background:var(--red-l);text-align:center;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--red);font-weight:600;border-top:1px solid rgba(220,38,38,0.15);}
.pp-status-footer.returned{background:var(--green-l);color:var(--green);border-color:rgba(5,150,105,0.15);}

/* EMPTY STATE */
.pp-empty{background:#fff;border:1px dashed var(--border-2);border-radius:10px;padding:60px 32px;text-align:center;grid-column:1/-1;}
.pp-empty-icon{width:56px;height:56px;background:var(--bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
.pp-empty-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:var(--muted);margin-bottom:8px;}
.pp-empty-sub{font-size:12px;color:var(--faint);max-width:340px;margin:0 auto;line-height:1.5;}

/* MODAL */
.pp-modal-bg{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.5);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:20px;}
.pp-modal-bg.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.pp-modal{background:#fff;border-radius:10px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 20px 60px rgba(10,20,40,0.3);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;}
@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:none;opacity:1}}
.pp-modal-head{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.pp-modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;}
.pp-modal-title.danger{color:var(--red);}
.pp-modal-title.info{color:var(--blue);}
.pp-modal-title.warn{color:var(--orange);}
.pp-modal-close{background:transparent;border:none;cursor:pointer;color:var(--muted);padding:4px;}
.pp-modal-body{padding:18px 22px;font-size:13px;color:var(--ink-2);line-height:1.5;}
.pp-modal-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;display:block;font-weight:500;margin-top:14px;}
.pp-modal-input{width:100%;padding:10px 12px;background:var(--bg);border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--ink);outline:none;transition:border-color 0.2s;}
.pp-modal-input:focus{border-color:var(--blue);background:#fff;}
.pp-modal-textarea{width:100%;padding:11px 14px;background:var(--bg);border:1px solid var(--border-2);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--ink);outline:none;resize:vertical;min-height:80px;}
.pp-modal-foot{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;}
.pp-modal-btn-cancel{padding:9px 18px;background:transparent;border:1px solid var(--border-2);border-radius:5px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-weight:600;}
.pp-modal-btn-confirm{padding:10px 22px;border:none;border-radius:5px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;}
.pp-modal-btn-confirm.danger{background:var(--red);}
.pp-modal-btn-confirm.danger:hover{background:#B91C1C;}
.pp-modal-btn-confirm.info{background:var(--blue);}
.pp-modal-btn-confirm.info:hover{background:#153A82;}
.pp-modal-btn-confirm.warn{background:var(--orange);}
.pp-modal-btn-confirm.warn:hover{background:#D77E11;}
.pp-modal-btn-confirm.success{background:var(--green);}
.pp-modal-btn-confirm.success:hover{background:#047857;}

/* RESPONSIVE */
@media(max-width:1100px){.pp-grid{grid-template-columns:1fr;}}
@media(max-width:900px){
    .pp-page{padding:20px 16px;}
    .pp-title{font-size:28px;}
    .pp-kpi-grid{grid-template-columns:1fr 1fr;}
    .pp-meta-grid{grid-template-columns:1fr;}
}
</style>

<div class="pp-page">

    {{-- HEADER --}}
    <div class="pp-header">
        <div>
            <div class="pp-eyebrow">Admin Panel</div>
            <h1 class="pp-title">Postponed Enrollments</h1>
            <div class="pp-subtitle">Manage active postponements, extensions, and expirations. Maximum postponement is 90 days.</div>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
    <div class="pp-flash success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="pp-flash error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- KPI STATS --}}
    <div class="pp-kpi-grid">
        <div class="pp-kpi active">
            <div class="pp-kpi-label">Active</div>
            <div class="pp-kpi-val">{{ $stats['active'] }}</div>
            <div class="pp-kpi-hint">Currently postponed</div>
        </div>
        <div class="pp-kpi warning">
            <div class="pp-kpi-label">Expiring Soon</div>
            <div class="pp-kpi-val">{{ $stats['expiring_soon'] }}</div>
            <div class="pp-kpi-hint">Within 7 days</div>
        </div>
        <div class="pp-kpi returned">
            <div class="pp-kpi-label">Returned</div>
            <div class="pp-kpi-val">{{ $stats['returned'] }}</div>
            <div class="pp-kpi-hint">Successfully resumed</div>
        </div>
        <div class="pp-kpi expired">
            <div class="pp-kpi-label">Expired</div>
            <div class="pp-kpi-val">{{ $stats['expired'] }}</div>
            <div class="pp-kpi-hint">Past deadline · no refund</div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="pp-toolbar">
        <div class="pp-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="pp-search" placeholder="Search student or course..." oninput="filterCards()">
        </div>

        <div class="pp-pills">
            <a href="?status=all&type={{ $filterType }}" class="pp-pill {{ $filterStatus === 'all' ? 'active' : '' }}">
                All Statuses
            </a>
            <a href="?status=Active&type={{ $filterType }}" class="pp-pill {{ $filterStatus === 'Active' ? 'active' : '' }}">
                Active <span class="pp-pill-count">{{ $stats['active'] }}</span>
            </a>
            <a href="?status=Returned&type={{ $filterType }}" class="pp-pill {{ $filterStatus === 'Returned' ? 'active' : '' }}">
                Returned <span class="pp-pill-count">{{ $stats['returned'] }}</span>
            </a>
            <a href="?status=Expired&type={{ $filterType }}" class="pp-pill {{ $filterStatus === 'Expired' ? 'active' : '' }}">
                Expired <span class="pp-pill-count">{{ $stats['expired'] }}</span>
            </a>
        </div>

        <div style="width:1px;height:24px;background:var(--border-2);"></div>

        <div class="pp-pills">
            <a href="?type=all&status={{ $filterStatus }}" class="pp-pill {{ $filterType === 'all' ? 'active' : '' }}">
                All Types
            </a>
            <a href="?type=group&status={{ $filterStatus }}" class="pp-pill {{ $filterType === 'group' ? 'active' : '' }}">
                Group <span class="pp-pill-count">{{ $stats['group_count'] }}</span>
            </a>
            <a href="?type=private&status={{ $filterStatus }}" class="pp-pill {{ $filterType === 'private' ? 'active' : '' }}">
                Private <span class="pp-pill-count">{{ $stats['private_count'] }}</span>
            </a>
        </div>
    </div>

    {{-- CARDS --}}
    <div class="pp-grid" id="pp-container">

        @forelse($postponements as $pp)
            @php
                $enrollment = $pp->enrollment;
                $student    = $enrollment?->student;
                $start      = \Carbon\Carbon::parse($pp->start_date);
                $end        = \Carbon\Carbon::parse($pp->expected_return_date);
                $today      = now();

                $totalDays  = max(1, $start->diffInDays($end));
                $elapsed    = max(0, min($totalDays, (int) $start->diffInDays($today)));
                $daysLeft   = (int) $today->diffInDays($end, false);
                $pct        = round(($elapsed / $totalDays) * 100);

                $isExpiringSoon = $pp->status === 'Active' && $daysLeft <= 7 && $daysLeft >= 0;
                $isOverMax      = $totalDays > 90;

                // Bar color
                if ($isExpiringSoon) $fillClass = 'danger';
                elseif ($pct >= 70) $fillClass = 'warn';
                else $fillClass = 'ok';

                $isPrivate = $enrollment?->enrollment_type === 'Private';

                // Sessions info
                $totalSessions = $enrollment?->courseInstance?->sessions?->count() ?? 0;
                $completedSessions = $enrollment?->courseInstance?->sessions?->where('status','Completed')->count() ?? 0;
                $remainingSessions = $totalSessions - $completedSessions;

                // Private hours info
                $totalHours     = $enrollment?->courseInstance?->total_hours ?? 0;
                $remainingHours = $enrollment?->hours_remaining ?? 0;
            @endphp

            <div class="pp-card {{ $isExpiringSoon ? 'expiring' : '' }} {{ $pp->status === 'Expired' ? 'expired' : '' }}"
                 data-name="{{ strtolower($student?->full_name ?? '') }}"
                 data-course="{{ strtolower($enrollment?->courseTemplate?->name ?? '') }}">

                <div class="pp-card-head">
                    <div class="pp-card-info">
                        <div class="pp-badges">
                            <span class="pp-badge {{ $isPrivate ? 'pp-badge-private' : 'pp-badge-group' }}">
                                {{ $isPrivate ? 'Private' : 'Group' }}
                            </span>
                            @if($pp->status === 'Active' && $isExpiringSoon)
                                <span class="pp-badge pp-badge-expiring">Expiring in {{ $daysLeft }}d</span>
                            @elseif($pp->status === 'Active')
                                <span class="pp-badge pp-badge-active">Active</span>
                            @elseif($pp->status === 'Returned')
                                <span class="pp-badge pp-badge-returned">Returned</span>
                            @elseif($pp->status === 'Expired')
                                <span class="pp-badge pp-badge-expired">Expired</span>
                            @endif
                        </div>
                        <div class="pp-student-name">{{ $student?->full_name ?? '—' }}</div>
                        <div class="pp-course-name">
                            {{ $enrollment?->courseTemplate?->name ?? '—' }}
                            @if($enrollment?->level) · {{ $enrollment->level->name }} @endif
                        </div>
                        @if($student?->phones?->first())
                        <div class="pp-phone">{{ $student->phones->first()->phone_number }}</div>
                        @endif
                    </div>
                </div>

                <div class="pp-card-body">
                    {{-- Timeline visualization --}}
                    <div class="pp-timeline">
                        <div class="pp-timeline-dates">
                            <span>Postponement Period</span>
                            <span>{{ $totalDays }} days total</span>
                        </div>
                        <div class="pp-timeline-track">
                            <div class="pp-timeline-fill {{ $fillClass }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <div class="pp-timeline-labels">
                            <span>{{ $start->format('d M Y') }}</span>
                            @if($pp->status === 'Active')
                            <span class="today">Day {{ $elapsed }} of {{ $totalDays }}</span>
                            @endif
                            <span>{{ $end->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- Meta grid --}}
                    <div class="pp-meta-grid">
                        @if($isPrivate)
                            <div class="pp-meta-item">
                                <div class="pp-meta-label">Remaining Hours</div>
                                <div class="pp-meta-val warn">{{ number_format($remainingHours, 1) }} / {{ number_format($totalHours, 1) }} hrs</div>
                            </div>
                        @else
                            <div class="pp-meta-item">
                                <div class="pp-meta-label">Remaining Sessions</div>
                                <div class="pp-meta-val warn">{{ $remainingSessions }} of {{ $totalSessions }}</div>
                            </div>
                        @endif
                        <div class="pp-meta-item">
                            <div class="pp-meta-label">Days {{ $daysLeft >= 0 ? 'Left' : 'Overdue' }}</div>
                            <div class="pp-meta-val {{ $daysLeft < 0 ? 'danger' : ($isExpiringSoon ? 'warn' : 'success') }}">
                                {{ abs($daysLeft) }} days
                            </div>
                        </div>
                    </div>

                    {{-- Reason --}}
                    @if($pp->reason)
                    <div class="pp-reason">
                        <strong>Reason:</strong> {{ $pp->reason }}
                    </div>
                    @endif

                    {{-- Postponed by --}}
                    <div class="pp-by">
                        Postponed by <strong>{{ $pp->createdBy?->full_name ?? '—' }}</strong>
                        · {{ \Carbon\Carbon::parse($pp->created_at)->format('d M Y') }}
                        @if($pp->actual_return_date)
                            · Resumed {{ \Carbon\Carbon::parse($pp->actual_return_date)->format('d M Y') }}
                        @endif
                    </div>

                    {{-- Actions --}}
                    @if($pp->status === 'Active')
                    <div class="pp-actions">
                        <button class="pp-btn pp-btn-resume"
                                onclick="openConfirm({{ $pp->postponement_id }}, 'resume', '{{ addslashes($student?->full_name ?? '') }}')">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            Resume
                        </button>
                        <button class="pp-btn pp-btn-extend"
                                onclick="openExtend({{ $pp->postponement_id }}, '{{ addslashes($student?->full_name ?? '') }}', '{{ $end->format('Y-m-d') }}', '{{ $start->format('Y-m-d') }}')">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Extend
                        </button>
                        <button class="pp-btn pp-btn-expire"
                                onclick="openConfirm({{ $pp->postponement_id }}, 'expire', '{{ addslashes($student?->full_name ?? '') }}')">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                            Expire
                        </button>
                        <button class="pp-btn pp-btn-cancel"
                                onclick="openForceCancel({{ $pp->postponement_id }}, '{{ addslashes($student?->full_name ?? '') }}')">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                            Force Cancel
                        </button>
                    </div>
                    @endif
                </div>

                @if($pp->status === 'Expired')
                <div class="pp-status-footer">
                    ✕ Enrollment Cancelled — No Refund
                </div>
                @elseif($pp->status === 'Returned')
                <div class="pp-status-footer returned">
                    ✓ Student Resumed Successfully
                </div>
                @endif
            </div>
        @empty
            <div class="pp-empty">
                <div class="pp-empty-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="pp-empty-title">No Postponements Found</div>
                <div class="pp-empty-sub">There are no postponed enrollments matching your filters.</div>
            </div>
        @endforelse

    </div>
</div>

{{-- ══════════════ CONFIRM MODAL (Resume / Expire) ══════════════ --}}
<div class="pp-modal-bg" id="confirmModal">
    <div class="pp-modal">
        <div class="pp-modal-head">
            <div class="pp-modal-title" id="confirmTitle">Confirm</div>
            <button class="pp-modal-close" onclick="closeConfirm()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="pp-modal-body" id="confirmBody">Are you sure?</div>
        <form id="confirmForm" method="POST">
            @csrf @method('PATCH')
            <div class="pp-modal-foot">
                <button type="button" class="pp-modal-btn-cancel" onclick="closeConfirm()">Cancel</button>
                <button type="submit" class="pp-modal-btn-confirm" id="confirmBtn">Confirm</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ EXTEND MODAL ══════════════ --}}
<div class="pp-modal-bg" id="extendModal">
    <div class="pp-modal">
        <div class="pp-modal-head">
            <div class="pp-modal-title info" id="extendTitle">Extend Postponement</div>
            <button class="pp-modal-close" onclick="closeExtend()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <form id="extendForm" method="POST">
            @csrf @method('PATCH')
            <div class="pp-modal-body">
                <div>Select new expected return date. Maximum <strong>90 days</strong> from postponement start.</div>
                <label class="pp-modal-label">New Return Date <span style="color:var(--red);">*</span></label>
                <input type="date" name="new_return_date" id="extendDate" class="pp-modal-input" required>
                <div id="extendHint" style="font-size:10px;color:var(--muted);margin-top:6px;letter-spacing:0.3px;"></div>
            </div>
            <div class="pp-modal-foot">
                <button type="button" class="pp-modal-btn-cancel" onclick="closeExtend()">Cancel</button>
                <button type="submit" class="pp-modal-btn-confirm info">Extend</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ FORCE CANCEL MODAL ══════════════ --}}
<div class="pp-modal-bg" id="cancelModal">
    <div class="pp-modal">
        <div class="pp-modal-head">
            <div class="pp-modal-title danger" id="cancelTitle">Force Cancel Postponement</div>
            <button class="pp-modal-close" onclick="closeForceCancel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <form id="cancelForm" method="POST">
            @csrf @method('PATCH')
            <div class="pp-modal-body">
                <div style="color:var(--red);font-weight:500;">⚠ This will cancel the enrollment permanently. This action cannot be undone.</div>
                <label class="pp-modal-label">Reason <span style="color:var(--red);">*</span></label>
                <textarea name="reason" class="pp-modal-textarea" placeholder="Explain why you're force-cancelling this postponement..." required></textarea>
            </div>
            <div class="pp-modal-foot">
                <button type="button" class="pp-modal-btn-cancel" onclick="closeForceCancel()">Cancel</button>
                <button type="submit" class="pp-modal-btn-confirm danger">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterCards() {
    const q = document.getElementById('pp-search').value.toLowerCase().trim();
    document.querySelectorAll('.pp-card').forEach(card => {
        const name   = card.dataset.name   || '';
        const course = card.dataset.course || '';
        const show   = !q || name.includes(q) || course.includes(q);
        card.style.display = show ? '' : 'none';
    });
}

function openConfirm(id, action, name) {
    const isResume = action === 'resume';
    const title = document.getElementById('confirmTitle');
    const body  = document.getElementById('confirmBody');
    const btn   = document.getElementById('confirmBtn');

    title.textContent = isResume ? 'Resume Student' : 'Mark as Expired';
    title.className = 'pp-modal-title ' + (isResume ? 'success' : 'warn');
    title.style.color = isResume ? 'var(--green)' : 'var(--orange)';

    body.textContent = isResume
        ? `Resume ${name}? Their enrollment will be reactivated with all remaining sessions.`
        : `Mark ${name}'s postponement as Expired? Their enrollment will be cancelled with NO refund.`;

    btn.textContent = isResume ? 'Resume' : 'Mark Expired';
    btn.className = 'pp-modal-btn-confirm ' + (isResume ? 'success' : 'warn');

    document.getElementById('confirmForm').action = isResume
        ? `/admin/postponed/${id}/resume`
        : `/admin/postponed/${id}/expire`;

    document.getElementById('confirmModal').classList.add('open');
}
function closeConfirm() { document.getElementById('confirmModal').classList.remove('open'); }

function openExtend(id, name, currentEnd, startDate) {
    document.getElementById('extendTitle').textContent = `Extend Postponement — ${name}`;
    document.getElementById('extendForm').action = `/admin/postponed/${id}/extend`;

    const start = new Date(startDate);
    const max = new Date(start.getTime() + (90 * 24 * 60 * 60 * 1000));
    const maxStr = max.toISOString().split('T')[0];

    const dateInput = document.getElementById('extendDate');
    dateInput.value = currentEnd;
    dateInput.min = new Date().toISOString().split('T')[0];
    dateInput.max = maxStr;

    document.getElementById('extendHint').innerHTML =
        `Postponement started on <strong style="color:var(--ink);">${startDate}</strong>. ` +
        `Max end date: <strong style="color:var(--red);">${maxStr}</strong> (90 days).`;

    document.getElementById('extendModal').classList.add('open');
}
function closeExtend() { document.getElementById('extendModal').classList.remove('open'); }

function openForceCancel(id, name) {
    document.getElementById('cancelTitle').textContent = `Force Cancel — ${name}`;
    document.getElementById('cancelForm').action = `/admin/postponed/${id}/force-cancel`;
    document.getElementById('cancelModal').classList.add('open');
}
function closeForceCancel() { document.getElementById('cancelModal').classList.remove('open'); }

document.querySelectorAll('.pp-modal-bg').forEach(bg => {
    bg.addEventListener('click', e => {
        if (e.target === bg) bg.classList.remove('open');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.pp-modal-bg').forEach(bg => bg.classList.remove('open'));
    }
});
</script>
@endsection