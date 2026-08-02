@extends('admin.layouts.app')
@section('title', 'Course Instances')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --purple:#7C3AED; --purple-l:rgba(124,58,237,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }
    .aci-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }

    /* HEADER */
    .aci-header {
        margin:0 auto 22px;
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .aci-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .aci-header::after { content:''; position:absolute; bottom:-60px; left:30%; width:150px; height:150px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .aci-header-inner { position:relative; z-index:1; }
    .aci-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .aci-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--orange); box-shadow:0 0 8px var(--orange); }
    .aci-title { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; color:#fff; line-height:1; margin:0; }
    .aci-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }

    .aci-wrap { margin:0 auto; }
    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin:24px 0 14px; font-weight:600; }

    /* STATS */
    .aci-stats { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; }
    @media (max-width:1100px){ .aci-stats{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:560px){ .aci-stats{ grid-template-columns:1fr 1fr; } }
    .stat { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px 18px; position:relative; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--sc,var(--blue)); }
    .stat-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .stat-val { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }
    .stat-val.money { font-size:22px; }

    /* FILTER PILLS */
    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:6px; }
    .filter-pill { padding:9px 18px; border-radius:8px; font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:all 0.2s; background:var(--card); border:1px solid var(--border); color:var(--muted); text-decoration:none; }
    .filter-pill:hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }
    .filter-pill.active { background:var(--blue); border-color:var(--blue); color:#fff; box-shadow:0 4px 12px rgba(27,79,168,0.2); }

    /* TABLE */
    .tbl-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:1000px; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:14px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:var(--bg); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:var(--blue-l); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }

    .course-cell { display:flex; align-items:center; gap:11px; }
    .course-ic { width:36px; height:36px; border-radius:9px; background:var(--blue-l); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .course-name { font-weight:700; color:var(--text); }
    .course-lvl { font-size:10px; color:var(--muted); margin-top:1px; }

    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:10px; font-weight:600; white-space:nowrap; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .b-active { background:var(--green-l); color:var(--green-dk); }
    .b-upcoming { background:var(--blue-l); color:var(--blue); }
    .b-completed { background:rgba(122,138,154,0.12); color:var(--muted); }
    .b-cancelled { background:var(--red-l); color:var(--red); }
    .b-group { background:var(--blue-l); color:var(--blue); }
    .b-private { background:var(--purple-l); color:var(--purple); }

    .prog-wrap { display:flex; align-items:center; gap:8px; min-width:120px; }
    .prog-bar { flex:1; height:6px; background:var(--bg); border-radius:3px; overflow:hidden; }
    .prog-fill { height:100%; background:var(--green); border-radius:3px; }
    .prog-txt { font-size:10px; color:var(--muted); font-variant-numeric:tabular-nums; white-space:nowrap; }

    .rev-amt { font-family:'Bebas Neue',sans-serif; font-size:15px; letter-spacing:0.5px; color:var(--green-dk); }

    .row-actions { display:flex; gap:6px; align-items:center; }
    .btn-view { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:7px; font-size:10px; font-weight:600; letter-spacing:0.3px; text-decoration:none; background:var(--blue-l); color:var(--blue); border:1px solid transparent; transition:all 0.2s; }
    .btn-view:hover { border-color:var(--blue); text-decoration:none; }
    .btn-cancel-c { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:7px; font-size:10px; font-weight:600; letter-spacing:0.3px; cursor:pointer; background:transparent; color:var(--red); border:1px solid rgba(220,38,38,0.3); transition:all 0.2s; }
    .btn-cancel-c:hover { background:var(--red); color:#fff; }

    .tbl-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .tbl-empty svg { opacity:0.35; margin-bottom:12px; }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-bottom:4px; }

    @media (max-width:600px){ .aci-page{ padding:16px; } }
</style>

<div class="aci-page">

    {{-- HEADER --}}
    <div class="aci-header">
        <div class="aci-header-inner">
            <div class="aci-eyebrow">Admin — Academic</div>
            <h1 class="aci-title">Course Instances</h1>
            <div class="aci-sub">All running and upcoming courses across the academy</div>
        </div>
    </div>

    <div class="aci-wrap">

        {{-- STATS --}}
        <div class="aci-stats">
            <div class="stat" style="--sc:var(--green)">
                <div class="stat-label">Active</div>
                <div class="stat-val">{{ $stats['active'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--blue)">
                <div class="stat-label">Upcoming</div>
                <div class="stat-val">{{ $stats['upcoming'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--muted)">
                <div class="stat-label">Completed</div>
                <div class="stat-val">{{ $stats['completed'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--dark)">
                <div class="stat-label">Total</div>
                <div class="stat-val">{{ $stats['total'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--green-dk)">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-val money">{{ number_format($stats['revenue']) }} <span style="font-size:11px;">LE</span></div>
            </div>
        </div>

        {{-- FILTER --}}
        <span class="sec-label">Filter by Status</span>
        <div class="filter-bar">
            <a href="{{ route('admin.course-instances.index') }}" class="filter-pill {{ $statusFilter === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.course-instances.index', ['status' => 'Active']) }}" class="filter-pill {{ $statusFilter === 'Active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('admin.course-instances.index', ['status' => 'Upcoming']) }}" class="filter-pill {{ $statusFilter === 'Upcoming' ? 'active' : '' }}">Upcoming</a>
            <a href="{{ route('admin.course-instances.index', ['status' => 'Completed']) }}" class="filter-pill {{ $statusFilter === 'Completed' ? 'active' : '' }}">Completed</a>
        </div>

        {{-- TABLE --}}
        <span class="sec-label">{{ $instances->count() }} {{ Str::plural('Course', $instances->count()) }}</span>
        <div class="tbl-card">
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Patch</th>
                            <th>Type</th>
                            <th class="num">Students</th>
                            <th>Progress</th>
                            <th class="num">Revenue</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($instances as $ci)
                        @php
                            $statusBadge = match($ci->status) {
                                'Active'    => ['b-active','Active'],
                                'Upcoming'  => ['b-upcoming','Upcoming'],
                                'Completed' => ['b-completed','Completed'],
                                'Cancelled' => ['b-cancelled','Cancelled'],
                                default     => ['b-upcoming', $ci->status],
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="course-cell">
                                    <div class="course-ic">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="course-name">{{ $ci->courseTemplate?->name ?? 'Course' }}</div>
                                        <div class="course-lvl">
                                            {{ $ci->level?->name }}@if($ci->sublevel) · {{ $ci->sublevel->name }}@endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $ci->teacher?->employee?->full_name ?? '—' }}</td>
                            <td style="color:var(--muted);">{{ $ci->patch?->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $ci->type === 'Private' ? 'b-private' : 'b-group' }}">{{ $ci->type }}</span>
                            </td>
                            <td class="num">
                                <span style="font-weight:700;">{{ $ci->enrollments_count }}</span>
                                <span style="color:var(--faint);font-size:10px;"> / {{ $ci->capacity }}</span>
                            </td>
                            <td>
                                <div class="prog-wrap">
                                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $ci->progress_pct }}%"></div></div>
                                    <span class="prog-txt">{{ $ci->completed_sessions_count }}/{{ $ci->total_sessions_count }}</span>
                                </div>
                            </td>
                            <td class="num"><span class="rev-amt">{{ number_format($ci->revenue_total) }} LE</span></td>
                            <td><span class="badge {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.course-instances.show', $ci->course_instance_id) }}" class="btn-view">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="tbl-empty">
                                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    <div class="tbl-empty-title">No Course Instances</div>
                                    <div style="font-size:12px;">No courses match this filter.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@if(session('success'))
<div id="aciToast" style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--green);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(15,31,61,0.18);font-size:13px;color:var(--text);">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
    {{ session('success') }}
</div>
<script>setTimeout(()=>document.getElementById('aciToast')?.remove(),4000);</script>
@endif

@if(session('error'))
<div id="aciErr" style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--red);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(15,31,61,0.18);font-size:13px;color:var(--text);">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ session('error') }}
</div>
<script>setTimeout(()=>document.getElementById('aciErr')?.remove(),5000);</script>
@endif

<script>
document.querySelectorAll('.cancel-course-form').forEach(f => {
    f.addEventListener('submit', function(e) {
        if (!confirm('Cancel this course instance? Scheduled sessions will be cancelled. This cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>

@endsection