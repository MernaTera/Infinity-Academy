@extends('layouts.leads')
@section('title', 'Near Completion')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        /* --blue:#F5911E; --blue-dk:#C47010; --blue-l:rgba(245,145,30,0.07); */
        --green:#059669; --green-dk:#15803D;
        --purple:#7C3AED; --purple-l:rgba(124,58,237,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --card:rgba(255,255,255,0.72); --border:rgba(255,255,255,0.6);
        --glass-sh:0 12px 34px -14px rgba(23,45,90,0.2);
    }
    * { box-sizing:border-box; }

    .nc-page {
        min-height:100vh; padding:40px 34px 52px; font-family:'DM Sans',sans-serif; color:var(--text);
        background:#F8F6F2;
    }
       .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .page-eyebrow { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:7px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .page-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--blue); box-shadow:0 0 8px var(--blue); }
    .page-title { font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0 0 24px; }

    .kpi-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-bottom:28px; }
    @media (max-width:560px){ .kpi-grid{ grid-template-columns:1fr; } }
    .kpi-card { background:var(--card); -webkit-backdrop-filter:blur(20px) saturate(150%); backdrop-filter:blur(20px) saturate(150%); border:1px solid var(--border); border-radius:18px; padding:20px 22px; position:relative; overflow:hidden; box-shadow:var(--glass-sh); transition:transform 0.2s, box-shadow 0.2s; }
    .kpi-card:hover { transform:translateY(-3px); box-shadow:0 18px 42px -14px rgba(23,45,90,0.28); }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kc,var(--blue)); }
    .kpi-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; font-weight:600; }
    .kpi-val { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:1px; color:var(--kc,var(--blue)); line-height:1; }
    .kpi-sub { font-size:11px; color:var(--faint); margin-top:4px; }

    .sec-label { display:flex; align-items:center; gap:10px; font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--text); font-weight:700; margin-bottom:14px; }
    .sec-label::before { content:''; width:20px; height:3px; border-radius:3px; background:linear-gradient(90deg, var(--blue), var(--blue)); flex-shrink:0; }

    .tbl-card { background:rgba(255,255,255,0.72); border:1px solid var(--border); border-radius:20px; overflow:hidden; margin-bottom:28px; box-shadow:var(--glass-sh); }
    .tbl { width:100%; border-collapse:collapse; }
    .tbl thead th { padding:15px 16px; font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); text-align:left; font-weight:700; background:rgba(255,255,255,0.55); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); border-bottom:1px solid var(--border); white-space:nowrap; }
    .tbl tbody tr { border-bottom:1px solid rgba(27,79,168,0.06); transition:background 0.15s; }
    .tbl tbody tr:last-child { border-bottom:none; }
    .tbl tbody tr:hover { background:rgba(27,79,168,0.04); }
    .tbl td { padding:14px 16px; font-size:13px; color:var(--text); vertical-align:middle; }

    .badge { display:inline-flex; align-items:center; gap:4px; font-size:9px; letter-spacing:1px; text-transform:uppercase; padding:3px 8px; border-radius:6px; }
    .badge-warning { color:var(--blue-dk); background:var(--blue-l); border:1px solid rgba(245,145,30,0.2); }
    .badge-danger { color:var(--red); background:var(--red-l); border:1px solid rgba(220,38,38,0.15); }
    .badge-private { color:#6D28D9; background:var(--purple-l); border:1px solid rgba(124,58,237,0.2); }
    .badge-group { color:var(--blue); background:var(--blue-l); border:1px solid rgba(27,79,168,0.15); }

    .progress-wrap { width:120px; height:5px; background:rgba(27,79,168,0.08); border-radius:3px; overflow:hidden; }
    .progress-fill { height:100%; border-radius:3px; background:linear-gradient(90deg,#F5911E,#DC2626); }

    .btn-renew {
        display:inline-flex; align-items:center; gap:5px;
        padding:8px 15px; background:rgba(27,79,168,0.06);
        border:1.5px solid var(--blue); border-radius:10px;
        color:var(--blue); font-family:'Bebas Neue',sans-serif;
        font-size:12px; letter-spacing:2px;
        cursor:pointer; text-decoration:none;
        transition:all 0.2s; white-space:nowrap;
    }
    .btn-renew:hover { background:var(--blue); color:#fff; text-decoration:none; box-shadow:0 8px 18px rgba(27,79,168,0.3); }

    .empty-state { padding:48px; text-align:center; color:var(--faint); font-size:13px; }

    @media (max-width:600px){ .nc-page{ padding:20px 14px 36px; } .page-title{ font-size:34px; } }
</style>

<div class="nc-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-eyebrow">Customer Service</div>
    <h1 class="page-title">Near Completion</h1>

    
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#7F77DD">
            <div class="kpi-label">Private — Low Hours</div>
            <div class="kpi-val">{{ $privateCount }}</div>
            <div class="kpi-sub">≤ 4 hours remaining</div>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Group — Last Sessions</div>
            <div class="kpi-val">{{ $groupCount }}</div>
            <div class="kpi-sub">≤ 2 sessions remaining</div>
        </div>
    </div>

    
    <div class="sec-label">Private Students — Low Hours</div>
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Bundle Total</th>
                        <th>Hours Remaining</th>
                        <th>Progress</th>
                        <th>Teacher</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nearCompletionPrivate as $e)
                    @php
                        $totalHours = $e->privateBundle?->hours ?? 0;
                        $remaining  = $e->hours_remaining ?? 0;
                        $pct        = $totalHours > 0 ? max(0, 100 - round(($remaining / $totalHours) * 100)) : 100;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#1A2A4A;">{{ $e->student?->full_name ?? '—' }}</div>
                            <div style="font-size:11px;color:#AAB8C8;font-family:monospace;">
                                {{ $e->student?->phones?->where('is_primary',true)->first()?->phone_number ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:#1B4FA8;font-weight:500;">{{ $e->courseTemplate?->name ?? '—' }}</div>
                            @if($e->level)<div style="font-size:10px;color:#AAB8C8;">{{ $e->level->name }}</div>@endif
                        </td>
                        <td style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#7A8A9A;">
                            @if($e->privateBundle)
                                {{ $e->privateBundle->hours }} hrs
                            @else
                                <span style="font-size:20px;color:#AAB8C8;">No Bundle</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:{{ $remaining <= 2 ? '#DC2626' : '#F5911E' }};">
                                {{ $remaining }}
                            </span>
                            <span style="font-size:10px;color:#AAB8C8;"> hrs</span>
                        </td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress-fill" style="width:{{ $pct }}%;"></div>
                            </div>
                            <div style="font-size:9px;color:#AAB8C8;margin-top:3px;">{{ $pct }}% used</div>
                        </td>
                        <td style="font-size:12px;color:#4A5A7A;">{{ $e->teacher?->full_name ?? '—' }}</td>
                        <td>
                            @if($e->student)
                            <a href="{{ $e->student?->lead ? route('registration.from.lead', $e->student->lead->lead_id) . '?renew=1' : '#' }}"
                               class="btn-renew">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                Renew
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state">No private students with low hours</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="sec-label">Group Students — Last Sessions</div>
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course Instance</th>
                        <th>Total Sessions</th>
                        <th>Completed</th>
                        <th>Remaining</th>
                        <th>Progress</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nearCompletionGroup as $e)
                    @php
                        $total     = $e->courseInstance?->sessions?->count() ?? 0;
                        $completed = $e->courseInstance?->sessions?->where('status','Completed')->count() ?? 0;
                        $remaining = $total - $completed;
                        $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#1A2A4A;">{{ $e->student?->full_name ?? '—' }}</div>
                            <div style="font-size:11px;color:#AAB8C8;font-family:monospace;">
                                {{ $e->student?->phones?->where('is_primary',true)->first()?->phone_number ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:#1B4FA8;font-weight:500;">
                                {{ $e->courseInstance?->courseTemplate?->name ?? '—' }}
                            </div>
                            @if($e->courseInstance?->level)
                            <div style="font-size:10px;color:#AAB8C8;">{{ $e->courseInstance->level->name }}</div>
                            @endif
                        </td>
                        <td style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#7A8A9A;">{{ $total }}</td>
                        <td style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#059669;">{{ $completed }}</td>
                        <td>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:{{ $remaining <= 1 ? '#DC2626' : '#F5911E' }};">
                                {{ $remaining }}
                            </span>
                        </td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,#1B4FA8,#F5911E);"></div>
                            </div>
                            <div style="font-size:9px;color:#AAB8C8;margin-top:3px;">{{ $pct }}%</div>
                        </td>
                        <td>
                            @if($e->package_id && (int)$e->package_units_remaining > 0)
                                
                                <form method="POST" action="{{ route('student-care.package.continue', $e->enrollment_id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-renew" style="background:rgba(124,58,237,0.08);color:#7C3AED;border-color:rgba(124,58,237,0.3);"
                                        onclick="return confirm('Create the next package level for this student (free)?')">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                                        Continue Package ({{ $e->package_units_remaining }} left)
                                    </button>
                                </form>
                            @elseif($e->student?->lead)
                            <a href="{{ $e->student?->lead ? route('registration.from.lead', $e->student->lead->lead_id) . '?renew=1' : '#' }}"
                               class="btn-renew">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                Renew
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state">No group students near completion</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection