<style>
    #createInstanceModal {
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

    .ci-modal-box {
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(20px);
        border-radius: 8px;
        width: 640px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(27,79,168,0.15), 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid rgba(27,79,168,0.1);
        border-top: 2px solid #F5911E;
        animation: ciModalIn 0.3s ease;
    }

    @keyframes ciModalIn {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .ci-modal-header {
        padding: 22px 28px 18px;
        border-bottom: 1px solid rgba(27,79,168,0.08);
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0;
    }
    .ci-modal-eyebrow {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 3px;
        font-family: 'DM Sans', sans-serif;
    }
    .ci-modal-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 22px; letter-spacing: 4px; color: #1B4FA8; line-height: 1;
    }
    .ci-modal-close {
        background: none; border: none; cursor: pointer;
        color: #AAB8C8; padding: 4px; border-radius: 4px;
        transition: color 0.2s;
    }
    .ci-modal-close:hover { color: #DC2626; }

    .ci-modal-body {
        flex: 1; overflow-y: auto; padding: 24px 28px;
    }

    .ci-modal-footer {
        padding: 16px 28px;
        border-top: 1px solid rgba(27,79,168,0.06);
        display: flex; justify-content: flex-end; gap: 10px;
        flex-shrink: 0;
    }

    /* ── FORM ELEMENTS ── */
    .ci-section-label {
        font-size: 9px; letter-spacing: 4px; text-transform: uppercase;
        color: #F5911E; margin-bottom: 14px; padding-bottom: 8px;
        border-bottom: 1px solid rgba(245,145,30,0.15);
        font-family: 'DM Sans', sans-serif;
    }
    .ci-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 16px; margin-bottom: 18px; }
    .ci-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px 16px; margin-bottom: 18px; }
    .ci-grid-1 { display: grid; grid-template-columns: 1fr; gap: 14px; margin-bottom: 18px; }

    .ci-field { display: flex; flex-direction: column; gap: 5px; }
    .ci-label {
        font-size: 9px; letter-spacing: 2.5px; text-transform: uppercase;
        color: #7A8A9A; font-weight: 500;
        font-family: 'DM Sans', sans-serif;
    }
    .ci-label .req { color: #F5911E; margin-left: 2px; }

    .ci-input, .ci-select {
        width: 100%; padding: 9px 12px;
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(27,79,168,0.12); border-radius: 4px;
        color: #1A2A4A; font-family: 'DM Sans', sans-serif;
        font-size: 13px; font-weight: 300; outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none; -webkit-appearance: none;
    }
    .ci-input:focus, .ci-select:focus {
        border-color: #1B4FA8;
        box-shadow: 0 0 0 3px rgba(27,79,168,0.08);
    }
    .ci-input::placeholder { color: #B0BCCC; }
    .ci-input[type="date"] { color-scheme: light; }

    .ci-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center;
        background-color: rgba(255,255,255,0.92);
        padding-right: 30px; cursor: pointer;
    }
    .ci-select option { background: #fff; color: #1A2A4A; }

    .ci-divider { height: 1px; background: rgba(27,79,168,0.06); margin: 18px 0; }

    /* ── BUTTONS ── */
    .btn-ci-cancel {
        padding: 9px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 4px;
        color: #7A8A9A; font-family: 'DM Sans', sans-serif;
        font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-ci-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; }

    .btn-ci-submit {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px; background: transparent;
        border: 1.5px solid #1B4FA8; border-radius: 4px;
        color: #1B4FA8; font-family: 'Bebas Neue', sans-serif;
        font-size: 14px; letter-spacing: 4px;
        cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s;
    }
    .btn-ci-submit::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-ci-submit:hover::before { transform: scaleX(1); }
    .btn-ci-submit:hover { color: #fff; }
    .btn-ci-submit span, .btn-ci-submit svg { position: relative; z-index: 1; }
</style>
<div id="createInstanceModal">
    <div class="ci-modal-box">

        {{-- Header --}}
        <div class="ci-modal-header">
            <div>
                <div class="ci-modal-eyebrow">Student Care</div>
                <div class="ci-modal-title">New Course Instance</div>
            </div>
            <button class="ci-modal-close" onclick="closeCreateInstanceModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="ci-modal-body">
            <form id="createInstanceForm" method="POST" action="{{ route('student-care.instance.store') }}">
                @csrf

                {{-- ══ COURSE SETUP ══ --}}
                <div class="ci-section-label">Course Setup</div>
                <div class="ci-grid-1">
                    <div class="ci-field">
                        <label class="ci-label">Course <span class="req">*</span></label>
                        <select name="course_template_id" id="ci_course_select" class="ci-select" required>
                            <option value="">— Select Course —</option>
                            @foreach($templates as $t)
                                <option value="{{ $t->course_template_id }}"
                                        data-english-level="{{ $t->english_level_id ?? '' }}">
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="ci-grid">
                    <div class="ci-field">
                        <label class="ci-label">Level</label>
                        <select name="level_id" id="ci_level_select" class="ci-select" disabled>
                            <option value="">— Select Course First —</option>
                        </select>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Sublevel</label>
                        <select name="sublevel_id" id="ci_sublevel_select" class="ci-select" disabled>
                            <option value="">— Select Level First —</option>
                        </select>
                    </div>
                </div>

                <div class="ci-divider"></div>

                {{-- ══ ASSIGNMENT ══ --}}
                <div class="ci-section-label">Assignment</div>
                <div class="ci-grid">
                    <div class="ci-field">
                        <label class="ci-label">Patch <span class="req">*</span></label>
                        <select name="patch_id" class="ci-select" required>
                            <option value="">— Select Patch —</option>
                            @foreach($patches as $p)
                                <option value="{{ $p->patch_id }}">
                                    {{ $p->name }} ({{ $p->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Teacher <span class="req">*</span></label>
                        <select name="teacher_id" id="ci_teacher_select" class="ci-select" required>
                            <option value="">— Select Course First —</option>
                        </select>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Branch <span class="req">*</span></label>
                        <select name="branch_id" class="ci-select" required>
                            <option value="">— Select Branch —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Room</label>
                        <select name="room_id" class="ci-select">
                            <option value="">— Select Room —</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_id }}">
                                    {{ $room->name ?? 'Room ' . $room->room_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Capacity <span class="req">*</span></label>
                        <input type="number" name="capacity" id="ci_capacity"
                               class="ci-input" placeholder="e.g. 12" required min="1">
                    </div>
                </div>

                <div class="ci-divider"></div>

                {{-- ══ SCHEDULE ══ --}}
                <div class="ci-section-label">Schedule</div>
                <div class="ci-grid">
                    <div class="ci-field">
                        <label class="ci-label">Start Date <span class="req">*</span></label>
                        <input type="date" name="start_date" class="ci-input" required>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">End Date <span class="req">*</span></label>
                        <input type="date" name="end_date" class="ci-input" required>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Total Hours <span class="req">*</span></label>
                        <input type="number" step="0.1" name="total_hours" id="ci_total_hours"
                               class="ci-input" placeholder="e.g. 40" required>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Session Duration <span class="req">*</span></label>
                        <input type="number" step="0.1" name="session_duration" id="ci_session_duration"
                               class="ci-input" placeholder="e.g. 1.5" required>
                    </div>
                </div>

                <div class="ci-divider"></div>

                {{-- ══ DELIVERY ══ --}}
                <div class="ci-section-label">Delivery</div>
                <div class="ci-grid">
                    <div class="ci-field">
                        <label class="ci-label">Type <span class="req">*</span></label>
                        <select name="type" class="ci-select" required>
                            <option value="Group">Group</option>
                            <option value="Private">Private</option>
                        </select>
                    </div>
                    <div class="ci-field">
                        <label class="ci-label">Mode <span class="req">*</span></label>
                        <select name="delivery_mood" class="ci-select" required>
                            <option value="Offline">Offline</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="ci-modal-footer">
            <button class="btn-ci-cancel" onclick="closeCreateInstanceModal()">Cancel</button>
            <button class="btn-ci-submit" onclick="document.getElementById('createInstanceForm').submit()">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span>Save Instance</span>
            </button>
        </div>

    </div>
</div>

<script>
function openCreateInstanceModal() {
    document.getElementById('createInstanceModal').style.display = 'flex';
}

function closeCreateInstanceModal() {
    document.getElementById('createInstanceModal').style.display = 'none';
}

const ciCourse   = document.getElementById('ci_course_select');
const ciLevel    = document.getElementById('ci_level_select');
const ciSublevel = document.getElementById('ci_sublevel_select');
const ciTeacher  = document.getElementById('ci_teacher_select');

function resetSelect(el, placeholder) {
    el.innerHTML = `<option value="">${placeholder}</option>`;
    el.disabled  = true;
}

function setLoading(el) {
    el.innerHTML = '<option value="">Loading...</option>';
    el.disabled  = true;
}

async function loadTeachers(englishLevelId) {
    setLoading(ciTeacher);
    try {
        const res  = await fetch(`/student-care/teachers/by-course-level/${englishLevelId || 0}`);
        const data = await res.json();

        if (!data.length) {
            resetSelect(ciTeacher, 'No available teachers');
            return;
        }

        ciTeacher.innerHTML = '<option value="">— Select Teacher —</option>';
        data.forEach(t => {
            ciTeacher.innerHTML += `
                <option value="${t.teacher_id}">
                    ${t.employee?.full_name ?? '—'}
                    ${t.english_level ? '(' + t.english_level.level_name + ')' : ''}
                </option>`;
        });
        ciTeacher.disabled = false;

    } catch {
        resetSelect(ciTeacher, 'Error loading teachers');
    }
}

ciCourse.addEventListener('change', async function () {
    const courseId     = this.value;
    const englishLevel = this.options[this.selectedIndex]?.dataset.englishLevel || '';

    resetSelect(ciLevel,    '— Select Level (optional) —');
    resetSelect(ciSublevel, '— Select Level First —');
    resetSelect(ciTeacher,  '— Select Course First —');

    if (!courseId) return;

    try {
        setLoading(ciLevel);
        const res = await fetch(`/student-care/levels/${courseId}`);
        const data = await res.json();

        if (!data.length) {
            resetSelect(ciLevel, '— No Levels —');
        } else {
            ciLevel.innerHTML = '<option value="">— Select Level (optional) —</option>';
            data.forEach(l => {
                ciLevel.innerHTML += `
                    <option value="${l.level_id}"
                            data-teacher-level="${l.teacher_level ?? ''}"
                            data-hours="${l.total_hours ?? ''}"
                            data-session="${l.default_session_duration ?? ''}"
                            data-capacity="${l.max_capacity ?? ''}">
                        ${l.name}
                    </option>`;
            });
            ciLevel.disabled = false;
        }
    } catch {
        resetSelect(ciLevel, '— Error loading levels —');
    }

    if (englishLevel) {
        await loadTeachers(englishLevel);
    } else {
        resetSelect(ciTeacher, '— Select Level for Teachers —');
    }
});

ciLevel.addEventListener('change', async function () {
    const levelId      = this.value;
    const opt          = this.options[this.selectedIndex];
    const teacherLevel = opt?.dataset.teacherLevel || '';

    resetSelect(ciSublevel, '— Select Sublevel (optional) —');

    if (opt && levelId) {
        if (opt.dataset.hours)    document.getElementById('ci_total_hours').value      = opt.dataset.hours;
        if (opt.dataset.session)  document.getElementById('ci_session_duration').value = opt.dataset.session;
        if (opt.dataset.capacity) document.getElementById('ci_capacity').value         = opt.dataset.capacity;
    }

    if (!levelId) return;

    try {
        setLoading(ciSublevel);
        const res = await fetch(`/student-care/sublevels/${levelId}`);
        const data = await res.json();

        if (!data.length) {
            resetSelect(ciSublevel, '— No Sublevels —');
        } else {
            ciSublevel.innerHTML = '<option value="">— Select Sublevel (optional) —</option>';
            data.forEach(s => {
                ciSublevel.innerHTML += `
                    <option value="${s.sublevel_id}"
                            data-hours="${s.total_hours ?? ''}"
                            data-session="${s.default_session_duration ?? ''}"
                            data-capacity="${s.max_capacity ?? ''}">
                        ${s.name}
                    </option>`;
            });
            ciSublevel.disabled = false;
        }
    } catch {
        resetSelect(ciSublevel, '— Error loading sublevels —');
    }

    if (teacherLevel) await loadTeachers(teacherLevel);
});

ciSublevel.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (!opt || !this.value) return;
    if (opt.dataset.hours)    document.getElementById('ci_total_hours').value      = opt.dataset.hours;
    if (opt.dataset.session)  document.getElementById('ci_session_duration').value = opt.dataset.session;
    if (opt.dataset.capacity) document.getElementById('ci_capacity').value         = opt.dataset.capacity;
});
</script>