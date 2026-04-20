<style>
#scheduleModal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif}
#scheduleModal.show{display:flex}
.sch-box{width:100%;max-width:580px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18);max-height:90vh;display:flex;flex-direction:column}
.sch-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent);z-index:1}
.sch-header{padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08);flex-shrink:0}
.sch-eyebrow{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px}
.sch-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#1B4FA8}
.sch-body{padding:20px 24px;overflow-y:auto;flex:1}
.sch-footer{padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end;flex-shrink:0}
.sch-sec{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(245,145,30,0.15)}
.sch-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:6px;display:block}

/* Day Cards */
.day-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px}
.day-card{padding:14px 10px;text-align:center;border:1.5px solid rgba(27,79,168,0.12);border-radius:6px;cursor:pointer;transition:all 0.2s;background:#fff;position:relative;user-select:none}
.day-card:hover:not(.unavailable){border-color:#1B4FA8;background:rgba(27,79,168,0.02)}
.day-card.selected{border-color:#1B4FA8;background:rgba(27,79,168,0.06)}
.day-card.unavailable{opacity:0.35;cursor:not-allowed}
.day-card input{position:absolute;opacity:0;pointer-events:none}
.day-card-pair{font-size:13px;color:#1A2A4A;font-weight:500;margin-bottom:2px}
.day-card-sub{font-size:9px;color:#AAB8C8;letter-spacing:1px;text-transform:uppercase}
.day-card.selected .day-card-pair{color:#1B4FA8}

/* Selects */
.sch-select,.sch-input{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;margin-bottom:16px;appearance:none}
.sch-select:focus,.sch-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.sch-select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-color:#fff;padding-right:30px}

/* Time hint */
.time-hint{font-size:10px;color:#7A8A9A;margin-top:-12px;margin-bottom:14px;padding:6px 10px;background:rgba(27,79,168,0.03);border-radius:3px;border-left:2px solid rgba(27,79,168,0.15)}

/* Preview */
.preview-box{background:rgba(27,79,168,0.04);border:1px solid rgba(27,79,168,0.1);border-radius:5px;padding:14px 16px;margin-top:4px}
.prev-row{display:flex;justify-content:space-between;align-items:baseline;padding:5px 0;border-bottom:1px solid rgba(27,79,168,0.05)}
.prev-row:last-child{border-bottom:none}
.prev-key{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A}
.prev-val{font-size:12px;color:#1A2A4A;font-weight:500}
.prev-val.blue{color:#1B4FA8}
.prev-val.orange{color:#F5911E}

/* Buttons */
.btn-sch-cancel{padding:9px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer;transition:all 0.2s}
.btn-sch-cancel:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8}
.btn-sch-confirm{display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-sch-confirm::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-sch-confirm:hover::before{transform:scaleX(1)}
.btn-sch-confirm:hover{color:#fff}
.btn-sch-confirm:disabled{opacity:0.4;cursor:not-allowed;pointer-events:none}
.btn-sch-confirm span,.btn-sch-confirm svg{position:relative;z-index:1}
</style>

<div id="scheduleModal">
    <div class="sch-box">

        <div class="sch-header">
            <div class="sch-eyebrow">Student Care</div>
            <div class="sch-title">Set Course Schedule</div>
        </div>

        <div class="sch-body">
            <form id="scheduleForm" method="POST">
                @csrf

                {{-- Day Pair --}}
                <div class="sch-sec">Teaching Days</div>
                <input type="hidden" name="time_slot_id" id="slotIdHidden">

                {{-- Pairs container --}}
                <div class="day-grid" id="pairContainer"></div>
                

                {{-- Time Slot --}}
                <div class="sch-sec">Time Slot & Start Time</div>

                <span class="sch-label">Session Start Time</span>
                <input type="time" name="start_time" id="startTimeInput"
                       class="sch-input" step="1800"
                       disabled onchange="onStartTimeChange()">
                <div class="time-hint" id="timeHint" style="display:none"></div>


                {{-- Preview --}}
                <div id="previewBox" style="display:none">
                    <div class="sch-sec" style="margin-top:4px">Session Preview</div>
                    <div class="preview-box">
                        <div class="prev-row">
                            <span class="prev-key">Total Sessions</span>
                            <span class="prev-val blue" id="prev-count">—</span>
                        </div>
                        <div class="prev-row">
                            <span class="prev-key">Session Time</span>
                            <span class="prev-val" id="prev-time">—</span>
                        </div>
                        <div class="prev-row">
                            <span class="prev-key">First Session</span>
                            <span class="prev-val" id="prev-first">—</span>
                        </div>
                        <div class="prev-row">
                            <span class="prev-key">Last Session</span>
                            <span class="prev-val orange" id="prev-last">—</span>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="sch-footer">
            <button class="btn-sch-cancel" type="button" onclick="closeScheduleModal()">Cancel</button>
            <button class="btn-sch-confirm" id="confirmSchBtn" type="button"
                    onclick="submitSchedule()" disabled>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>Generate Sessions</span>
            </button>
        </div>

    </div>
</div>

<script>
let _schInstanceId   = null;
let _schInstanceData = {};
let _availDays       = [];
let _slotBounds      = { start: null, end: null };
let _previewTimeout  = null;

function openScheduleModal(instanceId) {
    _schInstanceId = instanceId;

    // Reset
    document.getElementById('pairContainer').innerHTML = '<div style="color:#AAB8C8;font-size:12px">Loading...</div>';
    document.getElementById('startTimeInput').value    = '';
    document.getElementById('startTimeInput').disabled = true;
    document.getElementById('timeHint').style.display  = 'none';
    document.getElementById('previewBox').style.display= 'none';
    document.getElementById('confirmSchBtn').disabled  = true;
    document.getElementById('slotIdHidden').value      = '';

    fetch(`/student-care/instance/${instanceId}/schedule-data`)
        .then(r => r.json())
        .then(data => {
            _schInstanceData = data.instance;
            renderPairs(data.pairs);
        });

    document.getElementById('scheduleModal').classList.add('show');
}

function renderPairs(pairs) {
    const container = document.getElementById('pairContainer');

    if (!pairs.length) {
        container.innerHTML = '<div style="color:#DC2626;font-size:12px;padding:12px">No available day pairs for this teacher.</div>';
        return;
    }

    container.innerHTML = '';
    pairs.forEach((p, i) => {
        container.innerHTML += `
        <label class="day-card" onclick="selectPair(this, '${p.pair}', '${p.time_slot.time_slot_id}', '${p.time_slot.start_time}', '${p.time_slot.end_time}', '${p.time_slot.name}')">
            <input type="radio" name="day_of_week" value="${p.pair}">
            <div class="day-card-pair">${p.label}</div>
            <div class="day-card-sub" style="color:#1B4FA8;margin-top:4px">${p.time_slot.name}</div>
            <div class="day-card-sub">${p.time_slot.start_time.slice(0,5)} – ${p.time_slot.end_time.slice(0,5)}</div>
        </label>`;
    });
}

function selectPair(card, pair, slotId, slotStart, slotEnd, slotName) {
    document.querySelectorAll('.day-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;

    // حفظ الـ slot
    _slotBounds = { start: slotStart, end: slotEnd };
    document.getElementById('slotIdHidden').value = slotId;

    // فعّل start time picker
    const input = document.getElementById('startTimeInput');
    input.min      = slotStart.slice(0,5);
    input.max      = slotEnd.slice(0,5);
    input.value    = slotStart.slice(0,5);
    input.disabled = false;

    // Show hint
    const hint = document.getElementById('timeHint');
    hint.textContent   = `${slotName}: ${slotStart.slice(0,5)} → ${slotEnd.slice(0,5)}`;
    hint.style.display = 'block';

    onStartTimeChange();
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').classList.remove('show');
}

function selectDay(card, day) {
    if (card.classList.contains('unavailable')) return;
    document.querySelectorAll('.day-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
    triggerPreview();
}

function onSlotChange() {
    const sel = document.getElementById('slotSelect');
    const opt = sel.options[sel.selectedIndex];

    if (!opt.value) {
        document.getElementById('startTimeInput').disabled = true;
        document.getElementById('timeHint').style.display  = 'none';
        return;
    }

    _slotBounds.start = opt.dataset.start;
    _slotBounds.end   = opt.dataset.end;

    const input = document.getElementById('startTimeInput');
    input.min      = _slotBounds.start.slice(0,5);
    input.max      = _slotBounds.end.slice(0,5);
    input.value    = _slotBounds.start.slice(0,5);
    input.disabled = false;

    const hint = document.getElementById('timeHint');
    hint.textContent = `Allowed: ${_slotBounds.start.slice(0,5)} → ${_slotBounds.end.slice(0,5)} — session ends at ${calcEndTime(input.value)}`;
    hint.style.display = 'block';

    triggerPreview();
}

function onStartTimeChange() {
    const input = document.getElementById('startTimeInput');
    const end   = calcEndTime(input.value);
    const hint  = document.getElementById('timeHint');
    hint.textContent = `Session: ${input.value} → ${end} — Slot: ${_slotBounds.start.slice(0,5)} → ${_slotBounds.end.slice(0,5)}`;
    triggerPreview();
}

function calcEndTime(startStr) {
    if (!startStr) return '—';
    const [h, m] = startStr.split(':').map(Number);
    const dur    = parseFloat(_schInstanceData.sessionDuration || 0);
    const total  = h * 60 + m + dur * 60;
    return String(Math.floor(total/60)).padStart(2,'0') + ':' + String(total%60).padStart(2,'0');
}

function triggerPreview() {
    clearTimeout(_previewTimeout);
    _previewTimeout = setTimeout(fetchPreview, 400);
}

function fetchPreview() {
    const day   = document.querySelector('[name="day_of_week"]:checked')?.value;
    const start = document.getElementById('startTimeInput').value;
    const slot  = document.getElementById('slotIdHidden').value; // ← هنا التغيير

    if (!day || !start || !slot) {
        document.getElementById('previewBox').style.display = 'none';
        document.getElementById('confirmSchBtn').disabled   = true;
        return;
    }

    fetch(`/student-care/instance/${_schInstanceId}/preview`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ day_of_week: day, start_time: start }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('prev-count').textContent = data.total_sessions + ' sessions';
        document.getElementById('prev-time').textContent  = data.start_time + ' → ' + data.end_time;
        document.getElementById('prev-first').textContent = data.first_session ?? '—';
        document.getElementById('prev-last').textContent  = data.last_session  ?? '—';
        document.getElementById('previewBox').style.display  = 'block';
        document.getElementById('confirmSchBtn').disabled    = false;
    })
    .catch(() => {
        document.getElementById('previewBox').style.display = 'none';
    });
}

function submitSchedule() {
    document.getElementById('scheduleForm').action =
        `/student-care/instance/${_schInstanceId}/schedule`;
    document.getElementById('scheduleForm').submit();
}

// Close on backdrop
document.getElementById('scheduleModal').addEventListener('click', function(e) {
    if (e.target === this) closeScheduleModal();
});
</script>