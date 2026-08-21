<style>
    #assignModal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }

    .assign-modal-box {
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(20px);
        border-radius: 8px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(27,79,168,0.15), 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid rgba(27,79,168,0.1);
        border-top: 2px solid #1B4FA8;
        animation: assignModalIn 0.3s ease;
        font-family: 'DM Sans', sans-serif;
    }

    @keyframes assignModalIn {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .assign-modal-header {
        padding: 22px 28px 18px;
        border-bottom: 1px solid rgba(27,79,168,0.08);
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0;
    }
    .assign-modal-eyebrow {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 3px;
    }
    .assign-modal-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 22px; letter-spacing: 4px; color: #1B4FA8; line-height: 1;
    }
    .assign-modal-close {
        background: none; border: none; cursor: pointer;
        color: #AAB8C8; padding: 4px; border-radius: 4px;
        transition: color 0.2s; flex-shrink: 0;
    }
    .assign-modal-close:hover { color: #DC2626; }

    .assign-modal-body {
        flex: 1; overflow-y: auto; padding: 24px 28px;
    }

    .assign-modal-footer {
        padding: 16px 28px;
        border-top: 1px solid rgba(27,79,168,0.06);
        display: flex; justify-content: flex-end; gap: 10px;
        flex-shrink: 0;
    }

    /* ── INSTANCE CARDS ── */
    .assign-instance-list {
        display: flex; flex-direction: column; gap: 10px;
        margin-top: 8px;
    }

    .assign-instance-card {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        background: rgba(255,255,255,0.9);
        border: 1.5px solid rgba(27,79,168,0.1);
        border-radius: 6px; cursor: pointer;
        transition: all 0.2s; position: relative;
    }
    .assign-instance-card:hover {
        border-color: rgba(27,79,168,0.3);
        background: rgba(27,79,168,0.02);
    }
    .assign-instance-card.selected {
        border-color: #1B4FA8;
        background: rgba(27,79,168,0.04);
    }
    .assign-instance-card.is-full {
        opacity: 0.5; cursor: not-allowed; pointer-events: none;
        border-color: rgba(220,38,38,0.2);
    }

    .assign-card-radio {
        width: 16px; height: 16px;
        accent-color: #1B4FA8;
        flex-shrink: 0; cursor: pointer;
    }

    .assign-card-info { flex: 1; min-width: 0; }
    .assign-card-name {
        font-weight: 500; color: #1A2A4A; font-size: 13px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .assign-card-meta {
        display: flex; gap: 8px; flex-wrap: wrap; margin-top: 5px;
    }

    .assign-tag {
        display: inline-block; font-size: 8px; letter-spacing: 1px;
        padding: 2px 7px; border-radius: 3px; white-space: nowrap;
        text-transform: uppercase; font-weight: 500;
    }
    .assign-tag-teacher { background: rgba(27,79,168,0.06);  border: 1px solid rgba(27,79,168,0.15);  color: #1B4FA8; }
    .assign-tag-patch   { background: rgba(245,145,30,0.06); border: 1px solid rgba(245,145,30,0.2);  color: #C47010; }
    .assign-tag-mode    { background: rgba(122,138,154,0.06);border: 1px solid rgba(122,138,154,0.15);color: #7A8A9A; }

    .assign-cap-wrap {
        display: flex; flex-direction: column; align-items: flex-end;
        gap: 4px; flex-shrink: 0;
    }
    .assign-cap-text {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 16px; letter-spacing: 1px;
        color: var(--cap-color, #1B4FA8); line-height: 1;
    }
    .assign-cap-bar {
        width: 52px; height: 4px;
        background: rgba(27,79,168,0.08); border-radius: 2px; overflow: hidden;
    }
    .assign-cap-fill {
        height: 100%; border-radius: 2px;
        background: var(--cap-color, #1B4FA8);
    }
    .assign-full-badge {
        font-size: 8px; letter-spacing: 1.5px; text-transform: uppercase;
        color: #DC2626; font-weight: 500;
    }

    .assign-empty {
        text-align: center; padding: 32px 0; color: #AAB8C8;
    }
    .assign-empty-title {
        font-family: 'Bebas Neue', sans-serif; font-size: 16px;
        letter-spacing: 3px; margin-top: 10px; color: #7A8A9A;
    }

    /* ── LABEL ── */
    .assign-label {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: #7A8A9A; margin-bottom: 4px; display: block;
    }

    /* ── BUTTONS ── */
    .btn-assign-cancel {
        padding: 9px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 4px;
        color: #7A8A9A; font-family: 'DM Sans', sans-serif;
        font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-assign-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; }

    .btn-assign-confirm {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px; background: transparent;
        border: 1.5px solid #1B4FA8; border-radius: 4px;
        color: #1B4FA8; font-family: 'Bebas Neue', sans-serif;
        font-size: 14px; letter-spacing: 4px;
        cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s;
    }
    .btn-assign-confirm::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-assign-confirm:hover::before { transform: scaleX(1); }
    .btn-assign-confirm:hover { color: #fff; }
    .btn-assign-confirm span, .btn-assign-confirm svg { position: relative; z-index: 1; }

    @media (max-width: 480px) {
        .assign-modal-body { padding: 16px 18px; }
        .assign-modal-header { padding: 18px; }
        .assign-modal-footer { padding: 14px 18px; }
    }
</style>

<div id="assignModal">
    <div class="assign-modal-box">

        {{-- Header --}}
        <div class="assign-modal-header">
            <div>
                <div class="assign-modal-eyebrow">Student Care</div>
                <div class="assign-modal-title">Assign to Instance</div>
            </div>
            <button class="assign-modal-close" onclick="closeAssignModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="assign-modal-body">
            <form id="assignForm" method="POST" action="{{ route('student-care.assign') }}">
                @csrf
                <input type="hidden" name="waiting_id" id="assign_waiting_id">
                <input type="hidden" name="course_instance_id" id="assign_instance_hidden">

                <span class="assign-label">Select Course Instance</span>

                {{-- Type filter banner (populated by JS) --}}
                <div id="assign_type_banner" style="display:none;font-size:10px;letter-spacing:1px;text-transform:uppercase;
                            color:#1B4FA8;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.15);
                            padding:7px 12px;border-radius:5px;margin:8px 0 4px;font-weight:600;"></div>

                {{-- Shown when the student's type has no matching course --}}
                <div id="assign_no_type_match" style="display:none;text-align:center;padding:28px 20px;color:#DC2626;
                            background:rgba(220,38,38,0.04);border:1px dashed rgba(220,38,38,0.3);border-radius:6px;
                            margin-top:8px;font-size:12px;line-height:1.5;">
                    No course instance of this student's type is available.<br>
                    <span style="color:#7A8A9A;font-size:11px;">A Private student needs a Private course, and a Group student needs a Group course.</span>
                </div>

                <div class="assign-instance-list">
                    @forelse($instances as $instance)
                    @php
                        $count           = $instance->enrollments->count();
                        $capacity        = $instance->capacity;
                        $pct             = $capacity > 0 ? round(($count / $capacity) * 100) : 0;
                        $isFull          = $count >= $capacity;
                        $completedCount  = $instance->completed_sessions_count ?? 0;
                        $sessionsExceeded= $completedCount > 2;
                        $isDisabled      = $isFull || $sessionsExceeded;
                        $capColor        = $isFull ? '#DC2626' : ($pct >= 80 ? '#C47010' : '#1B4FA8');
                    @endphp
                    <label class="assign-instance-card {{ $isDisabled ? 'is-full' : '' }}"
                        data-course-type="{{ $instance->type }}"
                        onclick="{{ $isDisabled ? '' : "selectInstance(this, '{$instance->course_instance_id}')" }}">

                        <input class="assign-card-radio" type="radio"
                            name="_instance_display"
                            value="{{ $instance->course_instance_id }}"
                            {{ $isDisabled ? 'disabled' : '' }}>

                        <div class="assign-card-info">
                            <div class="assign-card-name">
                                {{ $instance->courseTemplate->name ?? '—' }}
                            </div>
                            <div class="assign-card-meta">
                                {{-- your existing meta content --}}
                                <span>{{ $instance->teacher?->employee?->full_name ?? '—' }}</span>
                                <span>·</span>
                                <span>{{ $count }}/{{ $capacity }}</span>
                                <span>·</span>
                                @if($instance->delivery_mood === 'Online')
                                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;color:#15803D;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);padding:2px 8px;border-radius:4px;">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                        Online
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);padding:2px 8px;border-radius:4px;">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                                        Offline
                                    </span>
                                @endif
                            </div>

                            {{-- ══ SESSION PROGRESS INDICATOR ══ --}}
                            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                                <span style="font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#7A8A9A;">
                                    Sessions completed: {{ $completedCount }}
                                </span>

                                @if($sessionsExceeded)
                                    <span style="font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;
                                                color:#DC2626;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);
                                                padding:2px 8px;border-radius:3px;">
                                        ⚠ Exceeded 2-session limit
                                    </span>
                                @elseif($completedCount === 3)
                                    <span style="font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;
                                                color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);
                                                padding:2px 8px;border-radius:3px;">
                                        Last chance
                                    </span>
                                @elseif($completedCount > 0)
                                    <span style="font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;
                                                color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);
                                                padding:2px 8px;border-radius:3px;">
                                        Available ({{ 2 - $completedCount }} left)
                                    </span>
                                @endif
                            </div>

                            @if($sessionsExceeded)
                            <div style="margin-top:6px;font-size:10px;color:#DC2626;line-height:1.4;">
                                Cannot join — This course has moved past the 2-session join window. Please wait for next patch.
                            </div>
                            @endif
                        </div>

                        {{-- Capacity bar --}}
                        <div style="width:60px;flex-shrink:0;">
                            <div style="height:4px;background:rgba(27,79,168,0.08);border-radius:2px;overflow:hidden;">
                                <div style="width:{{ $pct }}%;height:100%;background:{{ $capColor }};"></div>
                            </div>
                            <div style="font-size:9px;color:{{ $capColor }};text-align:right;margin-top:2px;">{{ $pct }}%</div>
                        </div>
                    </label>
                    @empty
                    <div style="text-align:center;padding:32px;color:#AAB8C8;font-size:12px;">
                        No matching course instances available.
                    </div>
                    @endforelse
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="assign-modal-footer">
            <button class="btn-assign-cancel" onclick="closeAssignModal()">Cancel</button>
            <button class="btn-assign-confirm" onclick="submitAssign()">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>Confirm Assign</span>
            </button>
        </div>

    </div>
</div>

<script>
// NOTE: openAssignModal() and closeAssignModal() are defined in the parent
// waiting-list view (they handle the Private/Group type filtering there).
// This partial only defines the selection + submit helpers.

function selectInstance(card, instanceId) {
    document.querySelectorAll('.assign-instance-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('assign_instance_hidden').value = instanceId;
}

function submitAssign() {
    const instanceId = document.getElementById('assign_instance_hidden').value;
    if (!instanceId) {
        infAlert({ type:'warning', title:'Select a Course', message:'Please select a course instance first.' });
        return;
    }
    document.getElementById('assignForm').submit();
}
</script>