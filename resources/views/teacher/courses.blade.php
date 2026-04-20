@extends('teacher.layouts.app')
@section('title', 'My Courses')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.crs-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#059669;margin:0}
.page-header{margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px}
.kpi-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#059669)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#059669);line-height:1}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:14px;display:block;padding-bottom:8px;border-bottom:1px solid rgba(5,150,105,0.1)}

/* Course Cards */
.courses-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;margin-bottom:28px}
.course-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden;text-decoration:none;color:inherit;display:block;transition:all 0.2s;position:relative}
.course-card:hover{box-shadow:0 6px 24px rgba(5,150,105,0.1);transform:translateY(-2px);text-decoration:none;color:inherit}
.course-card.active::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.course-card.upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)}
.course-card.completed::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:rgba(122,138,154,0.3)}

.cc-header{padding:18px 20px 14px;border-bottom:1px solid rgba(5,150,105,0.06)}
.cc-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:#1A2A4A}
.cc-level{font-size:11px;color:#7A8A9A;margin-top:3px}
.cc-badges{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-upcoming{color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15)}
.badge-completed{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.15)}
.tag{display:inline-block;font-size:9px;letter-spacing:1px;padding:2px 8px;border-radius:3px;text-transform:uppercase;font-weight:500}
.tag-group{background:rgba(27,79,168,0.05);border:1px solid rgba(27,79,168,0.12);color:#2D6FDB}
.tag-private{background:rgba(245,145,30,0.05);border:1px solid rgba(245,145,30,0.15);color:#C47010}

.cc-body{padding:14px 20px}
.cc-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px}
.cc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:3px}
.cc-meta-val{font-size:12px;color:#1A2A4A;font-weight:500}

.prog-bar{background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden;margin:10px 0 4px}
.prog-fill{height:4px;border-radius:3px;background:linear-gradient(90deg,#059669,#10B981)}
.prog-meta{display:flex;justify-content:space-between;font-size:10px;color:#AAB8C8}

.cc-footer{padding:12px 20px;border-top:1px solid rgba(5,150,105,0.06);display:flex;align-items:center;justify-content:space-between}
.view-btn{display:inline-flex;align-items:center;gap:6px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;font-weight:500}
.student-count{font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1A2A4A;letter-spacing:1px}

/* Completed table */
.tbl-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden}
.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(5,150,105,0.02);border-bottom:1px solid rgba(5,150,105,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(5,150,105,0.04);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(5,150,105,0.02)}
.tbl td{padding:12px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

.empty-state{text-align:center;padding:48px;color:#AAB8C8}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px}

@media(max-width:768px){.crs-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}.courses-grid{grid-template-columns:1fr}}
</style>

<div class="crs-page">

    <div class="page-header">
        <div class="page-eyebrow">Instructor</div>
        <h1 class="page-title">My Courses</h1>
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Active</div>
            <div class="kpi-val">{{ $stats['active'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Upcoming</div>
            <div class="kpi-val">{{ $stats['upcoming'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#7A8A9A">
            <div class="kpi-label">Completed</div>
            <div class="kpi-val">{{ $stats['completed'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1A2A4A">
            <div class="kpi-label">Total Students</div>
            <div class="kpi-val">{{ $stats['students'] }}</div>
        </div>
    </div>

    {{-- Active & Upcoming --}}
    <span class="sec-label">Active & Upcoming Courses</span>

    @if($activeCourses->isEmpty())
    <div class="empty-state" style="background:#fff;border:1px solid rgba(5,150,105,0.08);border-radius:8px;margin-bottom:28px">
        <div class="empty-title">No Active Courses</div>
        <div style="font-size:12px">No courses assigned in the current patch</div>
    </div>
    @else
    <div class="courses-grid">
        @foreach($activeCourses as $instance)
        @php
            $sessions   = $instance->sessions;
            $total      = $sessions->count();
            $completed  = $sessions->where('status','Completed')->count();
            $remaining  = $total - $completed;
            $pct        = $total > 0 ? round($completed/$total*100) : 0;
            $enrolled   = $instance->enrollments->count();
            $schedule   = $instance->instanceSchedules->first();
            $pairLabels = ['sun_wed'=>'Sun & Wed','sat_tue'=>'Sat & Tue','mon_thu'=>'Mon & Thu'];
            $statusClass= strtolower($instance->status);
        @endphp
        <a href="{{ route('teacher.courses.show', $instance->course_instance_id) }}" class="course-card {{ $statusClass }}">
            <div class="cc-header">
                <div class="cc-name">{{ $instance->courseTemplate?->name ?? '—' }}</div>
                <div class="cc-level">
                    @if($instance->level) {{ $instance->level->name }} @endif
                    @if($instance->sublevel) · {{ $instance->sublevel->name }} @endif
                </div>
                <div class="cc-badges">
                    <span class="badge {{ $statusClass === 'active' ? 'badge-active' : 'badge-upcoming' }}">
                        {{ $instance->status }}
                    </span>
                    <span class="tag {{ $instance->type === 'Group' ? 'tag-group' : 'tag-private' }}">
                        {{ $instance->type }}
                    </span>
                    @if($instance->patch)
                    <span style="font-size:9px;color:#AAB8C8;letter-spacing:1px">{{ $instance->patch->name }}</span>
                    @endif
                </div>
            </div>

            <div class="cc-body">
                <div class="cc-meta-grid">
                    <div>
                        <div class="cc-meta-label">Day Pair</div>
                        <div class="cc-meta-val">{{ $pairLabels[$schedule?->day_of_week] ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="cc-meta-label">Time</div>
                        <div class="cc-meta-val">
                            @if($schedule?->start_time)
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                            @else —
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="cc-meta-label">Start</div>
                        <div class="cc-meta-val">{{ \Carbon\Carbon::parse($instance->start_date)->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="cc-meta-label">End</div>
                        <div class="cc-meta-val">{{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}</div>
                    </div>
                </div>

                @if($total > 0)
                <div class="prog-meta">
                    <span>{{ $completed }}/{{ $total }} sessions</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $pct }}%"></div>
                </div>
                @endif
            </div>

            <div class="cc-footer">
                <div>
                    <span class="student-count">{{ $enrolled }}</span>
                    <span style="font-size:10px;color:#AAB8C8;margin-left:4px">students</span>
                </div>
                <span class="view-btn">
                    View Details
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Completed --}}
    @if($completedCourses->isNotEmpty())
    <span class="sec-label">Completed Courses</span>
    <div class="tbl-card">
        <div style="overflow-x:auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Patch</th>
                        <th>Sessions</th>
                        <th>Ended</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedCourses as $instance)
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#1A2A4A">{{ $instance->courseTemplate?->name ?? '—' }}</div>
                            @if($instance->level)
                            <div style="font-size:10px;color:#7A8A9A;margin-top:2px">{{ $instance->level->name }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="tag {{ $instance->type === 'Group' ? 'tag-group' : 'tag-private' }}">
                                {{ $instance->type }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#7A8A9A">{{ $instance->patch?->name ?? '—' }}</td>
                        <td style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#7A8A9A;letter-spacing:1px">
                            {{ $instance->sessions->count() }}
                        </td>
                        <td style="font-size:12px;color:#AAB8C8">
                            {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('teacher.courses.show', $instance->course_instance_id) }}"
                               style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(5,150,105,0.2);color:#059669;text-decoration:none;transition:all 0.2s"
                               onmouseover="this.style.background='rgba(5,150,105,0.06)'"
                               onmouseout="this.style.background='transparent'">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection