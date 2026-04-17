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

                <div class="assign-instance-list">
                    @forelse($instances as $instance)
                    @php
                        $count    = $instance->enrollments->count();
                        $capacity = $instance->capacity;
                        $pct      = $capacity > 0 ? round(($count / $capacity) * 100) : 0;
                        $isFull   = $count >= $capacity;
                        $capColor = $isFull ? '#DC2626' : ($pct >= 80 ? '#C47010' : '#1B4FA8');
                    @endphp
                    <label class="assign-instance-card {{ $isFull ? 'is-full' : '' }}"
                           onclick="selectInstance(this, '{{ $instance->course_instance_id }}')">

                        <input class="assign-card-radio" type="radio"
                               name="_instance_display"
                               value="{{ $instance->course_instance_id }}"
                               {{ $isFull ? 'disabled' : '' }}>

                        <div class="assign-card-info">
                            <div class="assign-card-name">
                                {{ $instance->courseTemplate->name ?? '—' }}
                                @if($instance->level)
                                    — {{ $instance->level->name }}
                                @endif
                            </div>
                            <div class="assign-card-meta">
                                @if($instance->teacher)
                                    <span class="assign-tag assign-tag-teacher">
                                        {{ $instance->teacher->employee->full_name ?? $instance->teacher->name ?? '—' }}
                                    </span>
                                @endif
                                @if($instance->patch)
                                    <span class="assign-tag assign-tag-patch">{{ $instance->patch->name }}</span>
                                @endif
                                <span class="assign-tag assign-tag-mode">{{ $instance->delivery_mood }}</span>
                            </div>
                        </div>

                        <div class="assign-cap-wrap" style="--cap-color:{{ $capColor }}">
                            <div class="assign-cap-text">{{ $count }}/{{ $capacity }}</div>
                            <div class="assign-cap-bar">
                                <div class="assign-cap-fill" style="width:{{ $pct }}%;"></div>
                            </div>
                            @if($isFull)
                                <div class="assign-full-badge">Full</div>
                            @endif
                        </div>

                    </label>
                    @empty
                    <div class="assign-empty">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <div class="assign-empty-title">No Instances Available</div>
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
function openAssignModal(waitingId) {
    document.getElementById('assign_waiting_id').value  = waitingId;
    document.getElementById('assign_instance_hidden').value = '';
    document.querySelectorAll('.assign-instance-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.assign-card-radio').forEach(r => r.checked = false);
    document.getElementById('assignModal').style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}

function selectInstance(card, instanceId) {
    document.querySelectorAll('.assign-instance-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('assign_instance_hidden').value = instanceId;
}

function submitAssign() {
    const instanceId = document.getElementById('assign_instance_hidden').value;
    if (!instanceId) {
        alert('Please select a course instance first.');
        return;
    }
    document.getElementById('assignForm').submit();
}
</script>