{{--
    One course-instance card. Props:
      $instance  – the CourseInstance (with courseTemplate, level, sublevel,
                   teacher, patch, room, sessions, enrollments loaded)
      $context   – 'active' | 'next' | 'completed'
    For the "active" context it also computes today's sessions and whether a
    session is live now / starting within 30 min, and embeds that as data
    attributes so the page's JS can keep the highlight + alerts live.
--}}
@php
    $context = $context ?? 'active';

    $count    = $instance->enrollments->count();
    $capacity = (int) ($instance->capacity ?? 0);
    $pct      = $capacity > 0 ? min(100, round(($count / $capacity) * 100)) : 0;
    $isFull   = $capacity > 0 && $count >= $capacity;
    $capColor = $isFull ? '#DC2626' : ($pct >= 80 ? '#C47010' : '#1B4FA8');

    $typeClass = ($instance->type === 'Private') ? 'tag-private' : 'tag-group';
    $modeClass = ($instance->delivery_mood === 'Online') ? 'tag-online' : 'tag-offline';
    $statusClass = match($instance->status) {
        'Upcoming'  => 'status-upcoming',
        'Active'    => 'status-active-ci',
        'Completed' => 'status-completed',
        'Cancelled' => 'status-cancelled',
        default     => 'status-upcoming',
    };

    $courseName  = $instance->courseTemplate->name ?? '—';
    $teacherName = $instance->teacher->name ?? null;
    $roomName    = $instance->room->name ?? null;

    // ── Live / soon layer (active courses only) ──────────────────────────
    $todaysSessions = [];
    $liveState = null;              // 'live' | 'soon' | null  (server snapshot)
    $liveLabel = null;
    $todayTimeLabel = null;         // "6:00 PM – 8:00 PM" for today's session
    if ($context === 'active') {
        $now = now();
        $soonEdge = $now->copy()->addMinutes(30);
        foreach ($instance->sessions as $s) {
            if (!$s->session_date || !$s->start_time || !$s->end_time) continue;
            $st = $s->session_date->copy()->setTimeFrom($s->start_time);
            $en = $s->session_date->copy()->setTimeFrom($s->end_time);

            if ($st->isToday() || $en->isToday()) {
                $todaysSessions[] = ['s' => $st->toIso8601String(), 'e' => $en->toIso8601String()];
                $todayTimeLabel = $st->format('g:i A') . ' – ' . $en->format('g:i A');
            }
            if ($now->between($st, $en)) {
                $liveState = 'live';
                $liveLabel = 'In progress · ends in ' . $now->diffInMinutes($en) . ' min';
            } elseif ($liveState === null && $now->lt($st) && $st->lte($soonEdge)) {
                $liveState = 'soon';
                $liveLabel = 'Starts in ' . $now->diffInMinutes($st) . ' min';
            }
        }
    }
@endphp

<div class="ci-card {{ $liveState ? 'ci-'.$liveState : '' }}"
     data-context="{{ $context }}"
     data-status="{{ $instance->status }}"
     data-type="{{ $instance->type }}"
     data-mode="{{ $instance->delivery_mood }}"
     data-course="{{ strtolower($courseName) }}"
     data-teacher="{{ strtolower($teacherName ?? '') }}"
     data-instance="{{ $instance->course_instance_id }}"
     data-course-name="{{ $courseName }}"
     data-teacher-name="{{ $teacherName ?? 'Not assigned' }}"
     data-room-name="{{ $roomName ?? '—' }}"
     data-time-label="{{ $todayTimeLabel ?? '' }}"
     data-sessions='@json($todaysSessions)'>

    {{-- Live ribbon (shown/updated by JS) --}}
    @if($context === 'active')
        <span class="ci-live-badge {{ $liveState === 'live' ? 'is-live' : 'is-soon' }}"
              style="{{ $liveState ? '' : 'display:none;' }}">{{ $liveLabel }}</span>
    @endif

    {{-- Header --}}
    <div class="ci-card-head">
        <div>
            <div class="ci-card-title">{{ $courseName }}</div>
            <div class="ci-card-sub">
                @if($instance->level){{ $instance->level->name }}@endif
                @if($instance->sublevel) <span style="color:#AAB8C8;">› {{ $instance->sublevel->name }}</span>@endif
                @if(!$instance->level && !$instance->sublevel)<span style="color:#AAB8C8;">No level</span>@endif
            </div>
        </div>
        <span class="status-badge {{ $statusClass }}">{{ $instance->status }}</span>
    </div>

    {{-- Tags --}}
    <div class="ci-card-tags">
        <span class="tag {{ $typeClass }}">{{ $instance->type ?? 'Group' }}</span>
        <span class="tag {{ $modeClass }}">{{ $instance->delivery_mood }}</span>
        @if($instance->patch)<span class="tag tag-course">{{ $instance->patch->name }}</span>@endif
    </div>

    {{-- Detail grid --}}
    <div class="ci-meta-grid">
        <div class="ci-meta">
            <span class="ci-meta-k">Teacher</span>
            <span class="ci-meta-v">
                @if($teacherName){{ $teacherName }}@else<span style="color:#DC2626;">Not assigned</span>@endif
            </span>
        </div>
        <div class="ci-meta">
            <span class="ci-meta-k">Room</span>
            <span class="ci-meta-v">{{ $roomName ?? '—' }}</span>
        </div>
        <div class="ci-meta">
            <span class="ci-meta-k">{{ $context === 'completed' ? 'Ran' : 'Schedule' }}</span>
            <span class="ci-meta-v">
                {{ \Carbon\Carbon::parse($instance->start_date)->format('d M') }}
                → {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}
            </span>
        </div>
        <div class="ci-meta">
            <span class="ci-meta-k">Hours</span>
            <span class="ci-meta-v">{{ $instance->total_hours }}h · {{ $instance->session_duration }}h/session</span>
        </div>
        @if($context === 'active' && $todayTimeLabel)
        <div class="ci-meta ci-meta-today">
            <span class="ci-meta-k">Today</span>
            <span class="ci-meta-v" style="color:#1B4FA8;font-weight:600;">{{ $todayTimeLabel }}</span>
        </div>
        @endif
    </div>

    {{-- Capacity --}}
    <div class="ci-cap">
        <div class="cap-wrap">
            <span class="cap-text">{{ $count }} / {{ $capacity }}</span>
            <div class="cap-track"><div class="cap-fill" style="width:{{ $pct }}%;background:{{ $capColor }};"></div></div>
            @if($isFull)<span class="cap-full">Full</span>@endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="ci-card-actions">
        <a href="{{ route('student-care.instances.show', $instance->course_instance_id) }}" class="btn-action ci-btn-view">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        @if(!in_array($instance->status, ['Completed','Cancelled']))
        <a href="{{ route('student-care.instances.edit', $instance->course_instance_id) }}" class="btn-action ci-btn-edit">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </a>
        @endif
    </div>
</div>
