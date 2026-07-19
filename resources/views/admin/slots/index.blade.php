
@extends('admin.layouts.app')
@section('title', 'Time & Break Slots')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.slt-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start}

/* Panels */
.panel-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative}
.panel-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.panel-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;justify-content:space-between;align-items:center}
.panel-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.panel-body{padding:16px 18px}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:10px;display:block}

/* Form */
.form-field{display:flex;flex-direction:column;gap:4px;margin-bottom:10px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:9px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.btn-add{width:100%;padding:9px;background:transparent;border:1.5px dashed rgba(27,79,168,0.2);border-radius:4px;color:#1B4FA8;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;margin-top:4px}
.btn-add:hover{border-color:#1B4FA8;background:rgba(27,79,168,0.03)}

/* Slot items */
.slot-item{display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.07);border-radius:4px;margin-bottom:8px}
.slot-name{font-size:12px;color:#1A2A4A;font-weight:500}
.slot-time{font-size:10px;color:#7A8A9A;font-family:monospace;margin-top:2px}
.slot-type{display:inline-block;font-size:8px;letter-spacing:1px;text-transform:uppercase;padding:2px 6px;border-radius:2px;margin-top:3px;background:rgba(27,79,168,0.06);color:#1B4FA8}
.toggle-btn{padding:4px 10px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.active-slot .toggle-btn{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.active-slot .toggle-btn:hover{background:rgba(220,38,38,0.06)}
.inactive-slot{opacity:0.5}
.inactive-slot .toggle-btn{color:#059669;border-color:rgba(5,150,105,0.2)}
.inactive-slot .toggle-btn:hover{background:rgba(5,150,105,0.06)}

/* Edit button */
.edit-btn{padding:4px 10px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(27,79,168,0.2);background:transparent;color:#1B4FA8;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.edit-btn:hover{background:rgba(27,79,168,0.06)}
.slot-actions{display:flex;gap:6px;align-items:center}

/* Modal */
.slot-modal-bg{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.45);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
.slot-modal-bg.show{display:flex}
.slot-modal{width:100%;max-width:460px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18);animation:slotIn 0.3s cubic-bezier(0.16,1,0.3,1) both}
.slot-modal::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
@keyframes slotIn{from{opacity:0;transform:scale(0.94) translateY(10px)}to{opacity:1;transform:none}}
.slot-modal-header{padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.08)}
.slot-modal-eyebrow{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px}
.slot-modal-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#1B4FA8}
.slot-modal-body{padding:18px 22px}
.slot-modal-footer{padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-cancel{padding:8px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer}
.btn-cancel:hover{border-color:#1B4FA8;color:#1B4FA8}
.btn-save{padding:9px 22px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;transition:background 0.2s}
.btn-save:hover{background:#2D6FDB}

.empty-state{text-align:center;padding:24px 16px;color:#AAB8C8;font-size:12px;font-style:italic}

@media(max-width:900px){
    .two-col{grid-template-columns:1fr}
    .slt-page{padding:18px 14px}
    .kpi-grid{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="slt-page">

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

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8"><div class="kpi-label">Time Slots</div><div class="kpi-val">{{ $timeSlots->count() }}</div></div>
        <div class="kpi-card" style="--kc:#059669"><div class="kpi-label">Active Slots</div><div class="kpi-val">{{ $timeSlots->where('is_active', true)->count() }}</div></div>
        <div class="kpi-card" style="--kc:#F5911E"><div class="kpi-label">Break Slots</div><div class="kpi-val">{{ $breakSlots->count() }}</div></div>
        <div class="kpi-card" style="--kc:#7A8A9A"><div class="kpi-label">Active Breaks</div><div class="kpi-val">{{ $breakSlots->where('is_active', true)->count() }}</div></div>
    </div>

    <div class="two-col">

        {{-- Time Slots --}}
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

        {{-- Break Slots --}}
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
{{-- Edit Time Slot Modal --}}
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

{{-- Edit Break Slot Modal --}}
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

// Close on backdrop click
document.getElementById('editTimeSlotModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditTimeSlot();
});
document.getElementById('editBreakSlotModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditBreakSlot();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditTimeSlot();
        closeEditBreakSlot();
    }
});
</script>
@endsection
