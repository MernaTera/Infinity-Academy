@extends('teacher.layouts.app')
@section('title', 'Patch Schedule')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--border:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box}
    .orb{ position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2{ width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3{ width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }
.sch-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.page-eyebrow{font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:6px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:40px;letter-spacing:2px;color:var(--text);margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
    .patch-bar{display:flex;align-items:center;gap:20px;flex-wrap:wrap;padding:16px 22px;border-radius:16px;margin-bottom:22px;}
    .patch-bar-icon{width:42px;height:42px;border-radius:13px;background:linear-gradient(135deg,var(--blue),var(--blue-2));display:grid;place-items:center;flex-shrink:0;box-shadow:0 8px 18px rgba(27,79,168,0.28);}
    .patch-bar-info{flex:1;min-width:180px;}
    .patch-bar-name{font-family:'Bebas Neue',sans-serif;font-size:19px;letter-spacing:2px;color:var(--text);line-height:1;}
    .patch-bar-dates{font-size:11px;color:var(--muted);margin-top:5px;}
    .patch-bar-dates b{color:var(--blue);font-weight:600;}
    .patch-bar-prog{flex:1;min-width:200px;max-width:340px;}
    .patch-bar-prog-top{display:flex;justify-content:space-between;align-items:baseline;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);font-weight:600;margin-bottom:7px;}
    .patch-bar-prog-pct{font-family:'Bebas Neue',sans-serif;font-size:15px;color:var(--blue);letter-spacing:1px;}
    .patch-bar-track{background:rgba(27,79,168,0.1);border-radius:5px;height:7px;overflow:hidden;}
    .patch-bar-fill{height:7px;border-radius:5px;background:linear-gradient(90deg,var(--blue),var(--blue-2));transition:width .6s ease;}
    @media (max-width:600px){ .patch-bar-prog{max-width:none;} }

.view-only-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.15);border-radius:20px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--blue);margin-bottom:20px}

.patch-banner{background:linear-gradient(120deg,var(--blue),var(--blue-2));border-radius:18px;padding:18px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;position:relative;overflow:hidden;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.patch-banner::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06)}
.pb-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px}
.pb-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#fff}
.pb-dates{font-size:11px;color:rgba(255,255,255,0.72);margin-top:2px}
.pb-stat-val{font-family:'Bebas Neue',sans-serif;font-size:22px;color:#1B4FA8;letter-spacing:1px;line-height:1}
.pb-stat-label{font-size:9px;color:rgba(35, 85, 194, 0.5);letter-spacing:1px;text-transform:uppercase;margin-top:2px}

.filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
    .filter-bar .app-select,
    .filter-bar > .filter-select { flex: 1 1 150px; min-width: 140px; max-width: 260px; }
.filter-sel{padding:10px 14px;border:1px solid var(--border);border-radius:12px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--text);background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);cursor:pointer;outline:none}
.filter-sel:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.btn-filter{padding:11px 20px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:11px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:2px;cursor:pointer;box-shadow:0 6px 16px rgba(27,79,168,0.28)}
.btn-reset{padding:10px 16px;background:var(--card);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:11px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:all 0.2s}
.btn-reset:hover{border-color:var(--blue);color:var(--blue);text-decoration:none}

.schedule-grid{display:flex;flex-direction:column;gap:16px}
.sch-card{max-width:600px; background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--border);border-radius:20px;overflow:hidden;position:relative;box-shadow:var(--glass-sh);transition:transform .2s, box-shadow 0.2s}
.sch-card:hover{transform:translateY(-3px);box-shadow:0 16px 38px rgba(23,45,90,0.13)}
.sch-card.status-active::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)}
.sch-card.status-upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue-2),transparent)}
.sch-card.status-completed::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:rgba(122,138,154,0.3)}

.sch-header{padding:16px 20px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;border-bottom:1px solid rgba(27,79,168,0.06)}
.sch-course-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--text)}
.sch-level{font-size:11px;color:var(--muted);margin-top:3px}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:4px 10px;border-radius:20px;font-weight:600;white-space:nowrap}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:var(--blue);background:rgba(27,79,168,0.1);border:1px solid rgba(27,79,168,0.2)}
.badge-upcoming{color:var(--blue-2);background:rgba(45,111,219,0.08);border:1px solid rgba(45,111,219,0.2)}
.badge-completed{color:var(--muted);background:rgba(122,138,154,0.1);border:1px solid rgba(122,138,154,0.2)}

.tag{display:inline-block;font-size:9px;letter-spacing:1px;padding:2px 8px;border-radius:6px;text-transform:uppercase;font-weight:600}
.tag-group{background:rgba(27,79,168,0.05);border:1px solid rgba(27,79,168,0.12);color:var(--blue-2)}
.tag-private{background:rgba(245,145,30,0.05);border:1px solid rgba(245,145,30,0.15);color:var(--orange-dk)}
.tag-online{background:rgba(27,79,168,0.05);border:1px solid rgba(27,79,168,0.12);color:var(--blue)}
.tag-offline{background:rgba(122,138,154,0.06);border:1px solid rgba(122,138,154,0.15);color:var(--muted)}

.sch-body{padding:16px 20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px}
.sch-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:4px}
.sch-meta-val{font-size:13px;color:var(--text);font-weight:500}
.sch-time{font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--blue);letter-spacing:2px;line-height:1}

.prog-wrap{padding:0 20px 14px;border-top:1px solid rgba(27,79,168,0.06)}
.prog-bar{background:rgba(27,79,168,0.1);border-radius:3px;height:5px;overflow:hidden;margin:10px 0 4px}
.prog-fill{height:5px;border-radius:3px;background:linear-gradient(90deg,var(--blue),var(--blue-2))}
.prog-meta{display:flex;justify-content:space-between;font-size:10px;color:var(--faint)}

.students-toggle{display:flex;align-items:center;gap:6px;padding:8px 20px;border-top:1px solid rgba(27,79,168,0.06);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--blue);cursor:pointer;background:none;border-left:none;border-right:none;border-bottom:none;width:100%;text-align:left;transition:background 0.2s}
.students-toggle:hover{background:rgba(27,79,168,0.03)}
.students-panel{display:none;padding:12px 20px;border-top:1px solid rgba(27,79,168,0.04)}
.students-panel.show{display:block}
.student-row{display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid rgba(27,79,168,0.04);font-size:12px}
.student-row:last-child{border-bottom:none}
.student-name{color:var(--text);font-weight:500;flex:1}
.student-phone{font-size:10px;color:var(--faint);font-family:monospace}

.today-session{background:rgba(27,79,168,0.04);border:1px solid rgba(27,79,168,0.15);border-radius:10px;padding:6px 10px;display:flex;align-items:center;gap:8px;font-size:11px;color:var(--blue);margin-top:10px}

.empty-state{text-align:center;padding:60px 24px;background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--border);border-radius:20px;box-shadow:var(--glass-sh)}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--faint);margin-bottom:6px}

@media(max-width:768px){.sch-page{padding:18px 14px}.sch-body{grid-template-columns:1fr 1fr}}
</style>

<div class="sch-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

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
    @if($currentPatch)
    @php
        $pStart   = \Carbon\Carbon::parse($currentPatch->start_date);
        $pEnd     = \Carbon\Carbon::parse($currentPatch->end_date);
        $pTotal   = max(1, $pStart->diffInDays($pEnd));
        $pElapsed = max(0, min($pTotal, $pStart->diffInDays(now())));
        $pPct     = round($pElapsed / $pTotal * 100);
        $daysLeft = max(0, (int)now()->diffInDays($pEnd, false));
    @endphp
    <div class="patch-bar glass" style= "padding:12px 16px;border-radius:14px;background:var(--card);border:1px solid var(--border);box-shadow:var(--glass-sh);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);">
        <div class="patch-bar-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="patch-bar-info">
            <div class="patch-bar-name">{{ $currentPatch->name }}</div>
            <div class="patch-bar-dates">{{ $pStart->format('d M Y') }} → {{ $pEnd->format('d M Y') }} · <b>{{ $daysLeft }} days left</b></div>
        </div>
        <div class="patch-bar-prog">
            <div class="patch-bar-prog-top"><span>Patch Progress</span><span class="patch-bar-prog-pct">{{ $pPct }}%</span></div>
            <div class="patch-bar-track"><div class="patch-bar-fill" style="width:{{ $pPct }}%"></div></div>
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
                <div style="background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.2);border-radius:6px;padding:10px 14px;text-align:center;flex-shrink:0">
                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#1B4FA8;margin-bottom:3px">Today</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;line-height:1">
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
                    <div style="width:26px;height:26px;border-radius:50%;background:{{ $isRestricted ? 'rgba(220,38,38,0.1)' : 'rgba(27,79,168,0.1)' }};display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:{{ $isRestricted ? '#DC2626' : '#1B4FA8' }};flex-shrink:0">
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