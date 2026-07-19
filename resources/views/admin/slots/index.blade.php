
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
                    <form method="POST" action="{{ route('admin.patches.timeslots.toggle', $slot->time_slot_id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="toggle-btn">{{ $slot->is_active ? 'Disable' : 'Enable' }}</button>
                    </form>
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
                    <form method="POST" action="{{ route('admin.patches.breakslots.toggle', $break->break_slot_id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="toggle-btn">{{ $break->is_active ? 'Disable' : 'Enable' }}</button>
                    </form>
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
@endsection
