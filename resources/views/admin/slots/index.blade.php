
@extends('admin.layouts.app')
@section('title', 'Time & Break Slots')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
.slt-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh)}
.kpi-card::before{display:none}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}

.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start}

.panel-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:20px;overflow:hidden;position:relative;box-shadow:var(--glass-sh)}
/* .panel-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)} */
.panel-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.08);display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.35)}
.panel-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text)}
.panel-body{padding:16px 18px}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);margin-bottom:10px;display:block;font-weight:600}

.form-field{display:flex;flex-direction:column;gap:4px;margin-bottom:10px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600}
.form-control{width:100%;padding:9px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--text);background:rgba(255,255,255,0.65);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);outline:none;box-sizing:border-box}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.btn-add{width:100%;padding:9px;background:rgba(255,255,255,0.45);border:1.5px dashed rgba(27,79,168,0.25);border-radius:12px;color:var(--blue);font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;margin-top:4px;font-weight:600}
.btn-add:hover{border-color:var(--blue);background:rgba(27,79,168,0.06)}

.slot-item{display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:rgba(255,255,255,0.55);border:1px solid rgba(27,79,168,0.08);border-radius:12px;margin-bottom:8px}
.slot-name{font-size:12px;color:var(--text);font-weight:500}
.slot-time{font-size:10px;color:var(--muted);font-family:monospace;margin-top:2px}
.slot-type{display:inline-block;font-size:8px;letter-spacing:1px;text-transform:uppercase;padding:2px 6px;border-radius:20px;margin-top:3px;background:rgba(27,79,168,0.10);color:var(--blue);font-weight:600}
.toggle-btn{padding:4px 10px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:8px;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;font-weight:600}
.active-slot .toggle-btn{color:var(--red);border-color:rgba(220,38,38,0.22)}
.active-slot .toggle-btn:hover{background:rgba(220,38,38,0.07)}
.inactive-slot{opacity:0.5}
.inactive-slot .toggle-btn{color:var(--green-dk);border-color:rgba(5,150,105,0.22)}
.inactive-slot .toggle-btn:hover{background:rgba(5,150,105,0.07)}

.edit-btn{padding:4px 10px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:8px;border:1px solid rgba(27,79,168,0.22);background:rgba(255,255,255,0.5);color:var(--blue);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;font-weight:600}
.edit-btn:hover{background:rgba(27,79,168,0.08)}
.slot-actions{display:flex;gap:6px;align-items:center}

.slot-modal-bg{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
.slot-modal-bg.show{display:flex}
.slot-modal{width:100%;max-width:460px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18);animation:slotIn 0.3s cubic-bezier(0.16,1,0.3,1) both}
.slot-modal::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)}
@keyframes slotIn{from{opacity:0;transform:scale(0.94) translateY(10px)}to{opacity:1;transform:none}}
.slot-modal-header{padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.08)}
.slot-modal-eyebrow{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:3px;font-weight:600}
.slot-modal-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--text)}
.slot-modal-body{padding:18px 22px}
.slot-modal-footer{padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.08);display:flex;gap:10px;justify-content:flex-end}
.btn-cancel{padding:8px 18px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer}
.btn-cancel:hover{border-color:var(--blue);color:var(--blue)}
.btn-save{padding:9px 22px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36)}

.empty-state{text-align:center;padding:24px 16px;color:var(--faint);font-size:12px;font-style:italic}

@media(max-width:900px){
    .two-col{grid-template-columns:1fr}
    .slt-page{padding:18px 14px}
    .kpi-grid{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="slt-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Time & Break Slots</h1>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('error') }}</div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8"><div class="kpi-label">Time Slots</div><div class="kpi-val">{{ $timeSlots->count() }}</div></div>
        <div class="kpi-card" style="--kc:#059669"><div class="kpi-label">Active Slots</div><div class="kpi-val">{{ $timeSlots->where('is_active', true)->count() }}</div></div>
        <div class="kpi-card" style="--kc:#F5911E"><div class="kpi-label">Break Slots</div><div class="kpi-val">{{ $breakSlots->count() }}</div></div>
        <div class="kpi-card" style="--kc:#5A6A85"><div class="kpi-label">Active Breaks</div><div class="kpi-val">{{ $breakSlots->where('is_active', true)->count() }}</div></div>
    </div>

    <div class="two-col">

        <div class="panel-card">
            <div class="panel-header">
                <div class="panel-title">Time Slots</div>
            </div>
            <div class="panel-body">
                @forelse($timeSlots as $slot)
                <div class="slot-item {{ $slot->is_active ? 'active-slot' : 'inactive-slot' }}">
                    <div>
                        <div class="slot-name">{{ $slot->name }}</div>
                        <div class="slot-time">{{ substr($slot->start_time,0,5) }} → {{ substr($slot->end_time,0,5) }}</div>
                        @if($slot->slot_type)
                        <span class="slot-type">{{ $slot->slot_type }}</span>
                        @endif
                    </div>
                    <div class="slot-actions">
                        <button type="button" class="edit-btn"
                            onclick="openEditTimeSlot({{ $slot->time_slot_id }}, '{{ addslashes($slot->name) }}', '{{ substr($slot->start_time,0,5) }}', '{{ substr($slot->end_time,0,5) }}', '{{ $slot->slot_type }}')">
                            ✎ Edit
                        </button>
                        <form method="POST" action="{{ route('admin.patches.timeslots.toggle', $slot->time_slot_id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="toggle-btn">{{ $slot->is_active ? 'Disable' : 'Enable' }}</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state">No time slots yet — add your first below.</div>
                @endforelse

                <div style="margin-top:16px;border-top:1px solid rgba(27,79,168,0.07);padding-top:14px">
                    <span class="sec-label">Add Time Slot</span>
                    <form method="POST" action="{{ route('admin.patches.timeslots.store') }}">
                        @csrf
                        <div class="form-field">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Evening Slot" required>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                            <div class="form-field">
                                <label class="form-label">Start</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label class="form-label">End</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Type</label>
                            <select name="slot_type" class="form-control" required>
                                <option value="Morning">Morning</option>
                                <option value="Midday">Midday</option>
                                <option value="Night">Night</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-add">+ Add Slot</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <div class="panel-title">Break Slots</div>
            </div>
            <div class="panel-body">
                @forelse($breakSlots as $break)
                <div class="slot-item {{ $break->is_active ? 'active-slot' : 'inactive-slot' }}">
                    <div>
                        <div class="slot-name">{{ $break->name }}</div>
                        <div class="slot-time">{{ substr($break->start_time,0,5) }} → {{ substr($break->end_time,0,5) }}</div>
                    </div>
                    <div class="slot-actions">
                        <button type="button" class="edit-btn"
                            onclick="openEditBreakSlot({{ $break->break_slot_id }}, '{{ addslashes($break->name) }}', '{{ substr($break->start_time,0,5) }}', '{{ substr($break->end_time,0,5) }}')">
                            ✎ Edit
                        </button>
                        <form method="POST" action="{{ route('admin.patches.breakslots.toggle', $break->break_slot_id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="toggle-btn">{{ $break->is_active ? 'Disable' : 'Enable' }}</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state">No break slots yet — add your first below.</div>
                @endforelse

                <div style="margin-top:16px;border-top:1px solid rgba(27,79,168,0.07);padding-top:14px">
                    <span class="sec-label">Add Break Slot</span>
                    <form method="POST" action="{{ route('admin.patches.breakslots.store') }}">
                        @csrf
                        <div class="form-field">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Prayer Break" required>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                            <div class="form-field">
                                <label class="form-label">Start</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label class="form-label">End</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-add">+ Add Break</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="editTimeSlotModal" class="slot-modal-bg">
    <div class="slot-modal">
        <div class="slot-modal-header">
            <div class="slot-modal-eyebrow">Admin Panel</div>
            <div class="slot-modal-title">Edit Time Slot</div>
        </div>
        <form id="editTimeSlotForm" method="POST">
            @csrf @method('PUT')
            <div class="slot-modal-body">
                <div class="form-field">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="et_name" class="form-control" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div class="form-field">
                        <label class="form-label">Start</label>
                        <input type="time" name="start_time" id="et_start" class="form-control" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">End</label>
                        <input type="time" name="end_time" id="et_end" class="form-control" required>
                    </div>
                </div>
                <div class="form-field">
                    <label class="form-label">Type</label>
                    <select name="slot_type" id="et_type" class="form-control" required>
                        <option value="Morning">Morning</option>
                        <option value="Midday">Midday</option>
                        <option value="Night">Night</option>
                    </select>
                </div>
            </div>
            <div class="slot-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditTimeSlot()">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="editBreakSlotModal" class="slot-modal-bg">
    <div class="slot-modal">
        <div class="slot-modal-header">
            <div class="slot-modal-eyebrow">Admin Panel</div>
            <div class="slot-modal-title">Edit Break Slot</div>
        </div>
        <form id="editBreakSlotForm" method="POST">
            @csrf @method('PUT')
            <div class="slot-modal-body">
                <div class="form-field">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="eb_name" class="form-control" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div class="form-field">
                        <label class="form-label">Start</label>
                        <input type="time" name="start_time" id="eb_start" class="form-control" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">End</label>
                        <input type="time" name="end_time" id="eb_end" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="slot-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditBreakSlot()">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditTimeSlot(id, name, start, end, type) {
    document.getElementById('editTimeSlotForm').action = `/admin/patches/time-slots/${id}`;
    document.getElementById('et_name').value  = name;
    document.getElementById('et_start').value = start;
    document.getElementById('et_end').value   = end;
    document.getElementById('et_type').value  = type;
    document.getElementById('editTimeSlotModal').classList.add('show');
}
function closeEditTimeSlot() {
    document.getElementById('editTimeSlotModal').classList.remove('show');
}

function openEditBreakSlot(id, name, start, end) {
    document.getElementById('editBreakSlotForm').action = `/admin/patches/break-slots/${id}`;
    document.getElementById('eb_name').value  = name;
    document.getElementById('eb_start').value = start;
    document.getElementById('eb_end').value   = end;
    document.getElementById('editBreakSlotModal').classList.add('show');
}
function closeEditBreakSlot() {
    document.getElementById('editBreakSlotModal').classList.remove('show');
}

document.getElementById('editTimeSlotModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditTimeSlot();
});
document.getElementById('editBreakSlotModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditBreakSlot();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditTimeSlot();
        closeEditBreakSlot();
    }
});
</script>
@endsection
