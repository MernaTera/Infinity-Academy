@extends('layouts.leads')
@section('title', 'Near Completion')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.nc-page{background:#F8F6F2;min-height:100vh;padding:36px 28px;font-family:'DM Sans',sans-serif;color:#1A2A4A;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0 0 24px;}

.kpi-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:28px;}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;padding:18px 20px;position:relative;overflow:hidden;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8);}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:6px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1;}
.kpi-sub{font-size:11px;color:#7A8A9A;margin-top:4px;}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;display:flex;align-items:center;gap:8px;margin-bottom:14px;}
.sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,145,30,0.2),transparent);}

.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:28px;}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:10px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:12px 16px;font-size:13px;color:#4A5A7A;vertical-align:middle;}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;}
.badge-warning{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);}
.badge-danger{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);}
.badge-private{color:#7F77DD;background:rgba(127,119,221,0.08);border:1px solid rgba(127,119,221,0.2);}
.badge-group{color:#1B4FA8;background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.15);}

.progress-wrap{width:120px;height:5px;background:rgba(27,79,168,0.08);border-radius:3px;overflow:hidden;}
.progress-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,#F5911E,#DC2626);}

.btn-renew{
    display:inline-flex;align-items:center;gap:5px;
    padding:6px 14px;background:transparent;
    border:1.5px solid #1B4FA8;border-radius:4px;
    color:#1B4FA8;font-family:'Bebas Neue',sans-serif;
    font-size:12px;letter-spacing:2px;
    cursor:pointer;text-decoration:none;
    transition:all 0.2s;white-space:nowrap;
}
.btn-renew:hover{background:#1B4FA8;color:#fff;text-decoration:none;}

.empty-state{padding:48px;text-align:center;color:#AAB8C8;font-size:13px;}
</style>

<div class="nc-page">
    <div class="page-eyebrow">Student Care</div>
    <h1 class="page-title">Near Completion</h1>

    {{-- KPIs --}}
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

    {{-- Private --}}
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

    {{-- Group --}}
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
                            @if($e->student?->lead)
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