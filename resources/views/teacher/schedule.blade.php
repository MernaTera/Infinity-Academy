@extends('teacher.layouts.app')
@section('title', 'Patch Schedule')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.sch-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#059669;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

/* View Only */
.view-only-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.15);border-radius:4px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;margin-bottom:20px}

/* Patch Banner */
.patch-banner{background:linear-gradient(135deg,#059669 0%,#10B981 100%);border-radius:8px;padding:18px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;position:relative;overflow:hidden}
.patch-banner::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.05)}
.pb-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px}
.pb-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#fff}
.pb-dates{font-size:11px;color:rgba(255,255,255,0.7);margin-top:2px}
.pb-stat-val{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#fff;letter-spacing:1px;line-height:1}
.pb-stat-label{font-size:9px;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;margin-top:2px}

/* Filters */
.filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
.filter-sel{padding:9px 14px;border:1px solid rgba(5,150,105,0.15);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;cursor:pointer;outline:none}
.filter-sel:focus{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,0.07)}
.btn-filter{padding:9px 18px;background:#059669;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;cursor:pointer}
.btn-reset{padding:9px 14px;background:transparent;border:1px solid rgba(5,150,105,0.2);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:all 0.2s}
.btn-reset:hover{border-color:#059669;color:#059669;text-decoration:none}

/* Schedule Cards */
.schedule-grid{display:flex;flex-direction:column;gap:16px}
.sch-card{background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden;position:relative;transition:box-shadow 0.2s}
.sch-card:hover{box-shadow:0 4px 20px rgba(5,150,105,0.08)}
.sch-card.status-active::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.sch-card.status-upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)}
.sch-card.status-completed::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:rgba(122,138,154,0.3)}

.sch-header{padding:16px 20px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;border-bottom:1px solid rgba(5,150,105,0.06)}
.sch-course-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:#1A2A4A}
.sch-level{font-size:11px;color:#7A8A9A;margin-top:3px}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:500;white-space:nowrap}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-upcoming{color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15)}
.badge-completed{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.15)}

.tag{display:inline-block;font-size:9px;letter-spacing:1px;padding:2px 8px;border-radius:3px;text-transform:uppercase;font-weight:500}
.tag-group{background:rgba(27,79,168,0.05);border:1px solid rgba(27,79,168,0.12);color:#2D6FDB}
.tag-private{background:rgba(245,145,30,0.05);border:1px solid rgba(245,145,30,0.15);color:#C47010}
.tag-online{background:rgba(5,150,105,0.05);border:1px solid rgba(5,150,105,0.15);color:#059669}
.tag-offline{background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15);color:#7A8A9A}

.sch-body{padding:16px 20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px}
.sch-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:4px}
.sch-meta-val{font-size:13px;color:#1A2A4A;font-weight:500}
.sch-time{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#059669;letter-spacing:2px;line-height:1}

/* Progress */
.prog-wrap{padding:0 20px 14px;border-top:1px solid rgba(5,150,105,0.06)}
.prog-bar{background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;margin:10px 0 4px}
.prog-fill{height:5px;border-radius:3px;background:linear-gradient(90deg,#059669,#10B981)}
.prog-meta{display:flex;justify-content:space-between;font-size:10px;color:#AAB8C8}

/* Sessions dropdown */
.students-toggle{display:flex;align-items:center;gap:6px;padding:8px 20px;border-top:1px solid rgba(5,150,105,0.06);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#059669;cursor:pointer;background:none;border-left:none;border-right:none;border-bottom:none;width:100%;text-align:left;transition:background 0.2s}
.students-toggle:hover{background:rgba(5,150,105,0.02)}
.students-panel{display:none;padding:12px 20px;border-top:1px solid rgba(5,150,105,0.04)}
.students-panel.show{display:block}
.student-row{display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid rgba(5,150,105,0.04);font-size:12px}
.student-row:last-child{border-bottom:none}
.student-name{color:#1A2A4A;font-weight:500;flex:1}
.student-phone{font-size:10px;color:#AAB8C8;font-family:monospace}

/* Today indicator */
.today-session{background:rgba(5,150,105,0.04);border:1px solid rgba(5,150,105,0.15);border-radius:4px;padding:6px 10px;display:flex;align-items:center;gap:8px;font-size:11px;color:#059669;margin-top:10px}

/* Empty */
.empty-state{text-align:center;padding:60px 24px;background:#fff;border:1px solid rgba(5,150,105,0.08);border-radius:8px}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:#AAB8C8;margin-bottom:6px}

@media(max-width:768px){.sch-page{padding:18px 14px}.sch-body{grid-template-columns:1fr 1fr}}
</style>

<div class="sch-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Instructor</div>
            <h1 class="page-title">Patch Schedule</h1>
        </div>
    </div>

    <div class="view-only-badge">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        View Only — Contact Student Care to modify schedule
    </div>

    {{-- Patch Banner --}}
    @if($currentPatch)
    @php
        $pStart  = \Carbon\Carbon::parse($currentPatch->start_date);
        $pEnd    = \Carbon\Carbon::parse($currentPatch->end_date);
        $pTotal  = max(1, $pStart->diffInDays($pEnd));
        $pElap   = max(0, min($pTotal, $pStart->diffInDays(now())));
        $pPct    = round($pElap / $pTotal * 100);
        $daysLeft= max(0, (int)now()->diffInDays($pEnd, false));
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
        <div style="flex:1;max-width:220px">
            <div style="display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,0.6);margin-bottom:6px">
                <span>Progress</span><span>{{ $pPct }}%</span>
            </div>
            <div style="background:rgba(255,255,255,0.15);border-radius:4px;height:5px;overflow:hidden">
                <div style="height:5px;border-radius:4px;background:rgba(255,255,255,0.7);width:{{ $pPct }}%"></div>
            </div>
        </div>
        <div style="display:flex;gap:20px">
            <div>
                <div class="pb-stat-val">{{ $instances->count() }}</div>
                <div class="pb-stat-label">My Courses</div>
            </div>
            <div>
                <div class="pb-stat-val">{{ $instances->where('status','Active')->count() }}</div>
                <div class="pb-stat-label">Active</div>
            </div>
        </div>
    </div>
    @else
    <div style="background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);border-radius:6px;padding:14px 18px;font-size:13px;color:#C47010;margin-bottom:20px">
        No active patch at the moment.
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('teacher.schedule') }}">
        <div class="filter-bar">
            <select name="pair" class="filter-sel">
                <option value="">All Day Pairs</option>
                <option value="sun_wed" {{ $filterPair === 'sun_wed' ? 'selected' : '' }}>Sun & Wed</option>
                <option value="sat_tue" {{ $filterPair === 'sat_tue' ? 'selected' : '' }}>Sat & Tue</option>
                <option value="mon_thu" {{ $filterPair === 'mon_thu' ? 'selected' : '' }}>Mon & Thu</option>
            </select>
            <select name="slot" class="filter-sel">
                <option value="">All Slots</option>
                @foreach($timeSlots as $slot)
                <option value="{{ $slot->time_slot_id }}" {{ $filterSlot == $slot->time_slot_id ? 'selected' : '' }}>
                    {{ $slot->name }} ({{ substr($slot->start_time,0,5) }} – {{ substr($slot->end_time,0,5) }})
                </option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter">Apply</button>
            <a href="{{ route('teacher.schedule') }}" class="btn-reset">Reset</a>
            <span style="font-size:12px;color:#AAB8C8;margin-left:auto">
                Showing {{ $filtered->count() }} of {{ $instances->count() }} courses
            </span>
        </div>
    </form>

    {{-- Schedule Cards --}}
    @if($filtered->isEmpty())
    <div class="empty-state">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1" style="margin:0 auto 14px;display:block">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <div class="empty-title">No Courses Found</div>
        <div style="font-size:12px;color:#AAB8C8">No courses assigned in this patch</div>
    </div>
    @else
    <div class="schedule-grid">
        @foreach($filtered as $instance)
        @php
            $schedule = $instance->instanceSchedules->first();
            $sessions = $instance->sessions->sortBy('session_number');
            $total     = $sessions->count();
            $completed = $sessions->where('status','Completed')->count();
            $remaining = $total - $completed;
            $pct       = $total > 0 ? round($completed/$total*100) : 0;

            $todaySession = $sessions->first(function($s) {
                return \Carbon\Carbon::parse($s->session_date)->isToday()
                    && $s->status === 'Scheduled';
            });

            $pairLabels = [
                'sun_wed' => 'Sun & Wed',
                'sat_tue' => 'Sat & Tue',
                'mon_thu' => 'Mon & Thu',
            ];

            $statusClass = match($instance->status) {
                'Active'    => 'status-active',
                'Upcoming'  => 'status-upcoming',
                'Completed' => 'status-completed',
                default     => '',
            };
            $statusBadge = match($instance->status) {
                'Active'    => 'badge-active',
                'Upcoming'  => 'badge-upcoming',
                'Completed' => 'badge-completed',
                default     => 'badge-completed',
            };
        @endphp

        <div class="sch-card {{ $statusClass }}">

            <div class="sch-header">
                <div>
                    <div class="sch-course-name">{{ $instance->courseTemplate?->name ?? '—' }}</div>
                    <div class="sch-level">
                        @if($instance->level) {{ $instance->level->name }} @endif
                        @if($instance->sublevel) · {{ $instance->sublevel->name }} @endif
                    </div>
                    <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap">
                        <span class="badge {{ $statusBadge }}">{{ $instance->status }}</span>
                        <span class="tag {{ $instance->type === 'Group' ? 'tag-group' : 'tag-private' }}">{{ $instance->type }}</span>
                        <span class="tag {{ $instance->delivery_mood === 'Online' ? 'tag-online' : 'tag-offline' }}">{{ $instance->delivery_mood }}</span>
                    </div>
                </div>
                @if($todaySession)
                <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);border-radius:6px;padding:10px 14px;text-align:center;flex-shrink:0">
                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;margin-bottom:3px">Today</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#059669;letter-spacing:1px;line-height:1">
                        {{ \Carbon\Carbon::parse($todaySession->start_time)->format('H:i') }}
                    </div>
                    <div style="font-size:9px;color:#7A8A9A;margin-top:2px">
                        → {{ \Carbon\Carbon::parse($todaySession->end_time)->format('H:i') }}
                    </div>
                </div>
                @endif
            </div>

            <div class="sch-body">
                <div>
                    <div class="sch-meta-label">Day Pair</div>
                    <div class="sch-meta-val">{{ $pairLabels[$schedule?->day_of_week] ?? '—' }}</div>
                </div>
                <div>
                    <div class="sch-meta-label">Time Slot</div>
                    <div class="sch-meta-val">{{ $schedule?->timeSlot?->name ?? '—' }}</div>
                    @if($schedule?->start_time)
                    <div class="sch-time">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                    </div>
                    @endif
                </div>
                <div>
                    <div class="sch-meta-label">Room</div>
                    <div class="sch-meta-val">{{ $instance->room?->name ?? '—' }}</div>
                    <div style="font-size:10px;color:#AAB8C8;margin-top:2px">{{ $instance->branch?->name ?? '' }}</div>
                </div>
                <div>
                    <div class="sch-meta-label">Duration</div>
                    <div class="sch-meta-val">{{ $instance->total_hours }}h total</div>
                    <div style="font-size:10px;color:#AAB8C8;margin-top:2px">{{ $instance->session_duration }}h / session</div>
                </div>
                <div>
                    <div class="sch-meta-label">Date Range</div>
                    <div class="sch-meta-val">{{ \Carbon\Carbon::parse($instance->start_date)->format('d M') }}</div>
                    <div style="font-size:10px;color:#AAB8C8;margin-top:2px">→ {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="sch-meta-label">Students</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:1px;line-height:1">
                        {{ $instance->enrollments->count() }}
                    </div>
                    <div style="font-size:10px;color:#AAB8C8;margin-top:2px">enrolled</div>
                </div>
            </div>

            {{-- Progress --}}
            @if($total > 0)
            <div class="prog-wrap" style="padding-top:12px">
                <div class="prog-meta">
                    <span>{{ $completed }} completed · {{ $remaining }} remaining</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @endif

            {{-- Students Toggle --}}
            @if($instance->enrollments->isNotEmpty())
            <button class="students-toggle"
                onclick="toggleStudents('students_{{ $instance->course_instance_id }}', this)">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                View Students ({{ $instance->enrollments->count() }})
                <svg id="chevron_{{ $instance->course_instance_id }}" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:auto;transition:transform 0.2s"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="students-panel" id="students_{{ $instance->course_instance_id }}">
                @foreach($instance->enrollments as $enr)
                @php
                    $isRestricted = $enr->status === 'Restricted';
                @endphp
                <div class="student-row">
                    <div style="width:26px;height:26px;border-radius:50%;background:{{ $isRestricted ? 'rgba(220,38,38,0.1)' : 'rgba(5,150,105,0.1)' }};display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:{{ $isRestricted ? '#DC2626' : '#059669' }};flex-shrink:0">
                        {{ strtoupper(substr($enr->student?->full_name ?? '?', 0, 1)) }}
                    </div>
                    <div style="flex:1">
                        <div class="student-name">{{ $enr->student?->full_name ?? '—' }}</div>
                        @if($isRestricted)
                        <div style="font-size:9px;color:#DC2626;letter-spacing:1px;text-transform:uppercase;margin-top:1px">Restricted</div>
                        @endif
                    </div>
                    <div class="student-phone">{{ $enr->student?->phones?->first()?->phone_number ?? '—' }}</div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
        @endforeach
    </div>
    @endif

</div>

<script>
function toggleStudents(id, btn) {
    const panel   = document.getElementById(id);
    const chevron = document.getElementById('chevron_' + id.replace('students_', ''));
    const show    = !panel.classList.contains('show');
    panel.classList.toggle('show', show);
    if (chevron) chevron.style.transform = show ? 'rotate(180deg)' : '';
    const textNode = [...btn.childNodes].find(n => n.nodeType === 3);
    if (textNode) textNode.textContent = show
        ? textNode.textContent.replace('View', 'Hide')
        : textNode.textContent.replace('Hide', 'View');
}
</script>
@endsection