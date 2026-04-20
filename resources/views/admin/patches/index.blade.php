@extends('admin.layouts.app')
@section('title', 'Patches')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.ptch-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-primary:hover::before{transform:scaleX(1)}
.btn-primary:hover{color:#fff}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.two-col{display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start}

/* Patch Cards */
.patch-list{display:flex;flex-direction:column;gap:12px}
.patch-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;transition:box-shadow 0.2s}
.patch-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.08)}
.patch-card.active{border-color:rgba(5,150,105,0.2)}
.patch-card.active::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#059669,transparent)}
.patch-card.upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)}
.patch-card.closed::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:rgba(122,138,154,0.3)}
.pc-header{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.pc-name{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1B4FA8}
.pc-body{padding:0 20px 14px;display:flex;gap:20px;flex-wrap:wrap}
.pc-meta{display:flex;flex-direction:column;gap:2px}
.pc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8}
.pc-meta-val{font-size:12px;color:#1A2A4A;font-weight:500}
.pc-footer{padding:12px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px;flex-wrap:wrap}

/* Progress bar */
.patch-prog{margin:0 20px 14px;background:#F0F0F0;border-radius:3px;height:4px;overflow:hidden}
.patch-prog-fill{height:4px;border-radius:3px;background:linear-gradient(90deg,#1B4FA8,#059669);transition:width 0.6s ease}

.status-badge{display:inline-flex;align-items:center;gap:5px;font-size:9px;letter-spacing:1.2px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:500}
.status-badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.s-active{color:#15803D;background:rgba(21,128,61,0.08);border:1px solid rgba(21,128,61,0.2)}
.s-upcoming{color:#1B6FA8;background:rgba(27,111,168,0.08);border:1px solid rgba(27,111,168,0.2)}
.s-closed{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.2)}
.s-locked{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap}
.btn-activate{color:#059669;border-color:rgba(5,150,105,0.25)}
.btn-activate:hover{background:rgba(5,150,105,0.07)}
.btn-close{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-close:hover{background:rgba(220,38,38,0.06)}
.btn-lock{color:#C47010;border-color:rgba(245,145,30,0.2)}
.btn-lock:hover{background:rgba(245,145,30,0.06)}
.btn-unlock{color:#7A8A9A;border-color:rgba(122,138,154,0.2)}
.btn-unlock:hover{background:rgba(122,138,154,0.06)}

/* Side panels */
.side-panel{display:flex;flex-direction:column;gap:16px}
.panel-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative}
.panel-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.panel-header{padding:14px 18px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;justify-content:space-between;align-items:center}
.panel-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.panel-body{padding:16px 18px}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:10px;display:block}
.form-field{display:flex;flex-direction:column;gap:4px;margin-bottom:10px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:9px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.btn-add{width:100%;padding:9px;background:transparent;border:1.5px dashed rgba(27,79,168,0.2);border-radius:4px;color:#1B4FA8;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;margin-top:4px}
.btn-add:hover{border-color:#1B4FA8;background:rgba(27,79,168,0.03)}

/* Slot items */
.slot-item{display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.07);border-radius:4px;margin-bottom:6px}
.slot-name{font-size:12px;color:#1A2A4A;font-weight:500}
.slot-time{font-size:10px;color:#7A8A9A;font-family:monospace}
.slot-actions{display:flex;gap:6px}
.toggle-btn{padding:3px 8px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.active-slot .toggle-btn{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.active-slot .toggle-btn:hover{background:rgba(220,38,38,0.06)}
.inactive-slot{opacity:0.5}
.inactive-slot .toggle-btn{color:#059669;border-color:rgba(5,150,105,0.2)}
.inactive-slot .toggle-btn:hover{background:rgba(5,150,105,0.06)}

@media(max-width:1024px){.two-col{grid-template-columns:1fr}.ptch-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="ptch-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Patches</h1>
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
        <div class="kpi-card" style="--kc:#1B4FA8"><div class="kpi-label">Total</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:#059669"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:#1B6FA8"><div class="kpi-label">Upcoming</div><div class="kpi-val">{{ $stats['upcoming'] }}</div></div>
        <div class="kpi-card" style="--kc:#7A8A9A"><div class="kpi-label">Closed</div><div class="kpi-val">{{ $stats['closed'] }}</div></div>
    </div>

    <div class="two-col">

        {{-- LEFT — Patch List --}}
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                <span style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E">Academic Patches</span>
                <button onclick="document.getElementById('newPatchModal').classList.add('show')" class="btn-primary" style="padding:8px 16px;font-size:11px">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>New Patch</span>
                </button>
            </div>

            <div class="patch-list">
                @forelse($patches as $patch)
                @php
                    $pClass     = strtolower($patch->status);
                    $sBadge     = match($patch->status) { 'Active'=>'s-active', 'Upcoming'=>'s-upcoming', 'Closed'=>'s-closed', default=>'s-upcoming' };
                    $start      = \Carbon\Carbon::parse($patch->start_date);
                    $end        = \Carbon\Carbon::parse($patch->end_date);
                    $today      = now();
                    $totalDays  = $start->diffInDays($end) ?: 1;
                    $elapsed    = max(0, min($totalDays, $start->diffInDays($today)));
                    $pct        = round($elapsed / $totalDays * 100);
                @endphp
                <div class="patch-card {{ $pClass }}">
                    <div class="pc-header">
                        <div>
                            <div class="pc-name">{{ $patch->name }}</div>
                            <div style="font-size:10px;color:#7A8A9A;margin-top:2px">{{ $patch->branch?->name ?? '—' }}</div>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                            <span class="status-badge {{ $sBadge }}">{{ $patch->status }}</span>
                            @if($patch->is_locked)
                            <span class="status-badge s-locked">Locked</span>
                            @endif
                        </div>
                    </div>
                    <div class="pc-body">
                        <div class="pc-meta">
                            <span class="pc-meta-label">Start</span>
                            <span class="pc-meta-val">{{ $start->format('d M Y') }}</span>
                        </div>
                        <div class="pc-meta">
                            <span class="pc-meta-label">End</span>
                            <span class="pc-meta-val">{{ $end->format('d M Y') }}</span>
                        </div>
                        <div class="pc-meta">
                            <span class="pc-meta-label">Duration</span>
                            <span class="pc-meta-val">{{ $totalDays }} days</span>
                        </div>
                        <div class="pc-meta">
                            <span class="pc-meta-label">Instances</span>
                            <span class="pc-meta-val">{{ $patch->course_instances_count }}</span>
                        </div>
                    </div>
                    @if($patch->status === 'Active')
                    <div class="patch-prog">
                        <div class="patch-prog-fill" style="width:{{ $pct }}%"></div>
                    </div>
                    <div style="padding:0 20px 10px;font-size:10px;color:#AAB8C8;text-align:right">{{ $pct }}% elapsed</div>
                    @endif
                    <div class="pc-footer">
                        @if($patch->status === 'Upcoming')
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="btn-sm btn-activate">▶ Activate</button>
                        </form>
                        @endif
                        @if($patch->status === 'Active')
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="close">
                            <button type="submit" class="btn-sm btn-close">■ Close</button>
                        </form>
                        @endif
                        @if(!$patch->is_locked)
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="lock">
                            <button type="submit" class="btn-sm btn-lock">🔒 Lock</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="unlock">
                            <button type="submit" class="btn-sm btn-unlock">🔓 Unlock</button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:48px;color:#AAB8C8;background:#fff;border:1px solid rgba(27,79,168,0.08);border-radius:8px">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">No Patches Yet</div>
                    <div style="font-size:12px">Create your first academic patch</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT — Timetable Config --}}
        <div class="side-panel">

            {{-- Time Slots --}}
            <div class="panel-card">
                <div class="panel-header">
                    <div class="panel-title">Time Slots</div>
                </div>
                <div class="panel-body">
                    @foreach($timeSlots->merge(\App\Models\Academic\TimeSlot::where('is_active', false)->get()) as $slot)
                    <div class="slot-item {{ $slot->is_active ? 'active-slot' : 'inactive-slot' }}">
                        <div>
                            <div class="slot-name">{{ $slot->name }}</div>
                            <div class="slot-time">{{ substr($slot->start_time,0,5) }} → {{ substr($slot->end_time,0,5) }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.patches.timeslots.toggle', $slot->time_slot_id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="toggle-btn">{{ $slot->is_active ? 'Disable' : 'Enable' }}</button>
                        </form>
                    </div>
                    @endforeach

                    <div style="margin-top:12px;border-top:1px solid rgba(27,79,168,0.07);padding-top:12px">
                        <span class="sec-label">Add Time Slot</span>
                        <form method="POST" action="{{ route('admin.patches.timeslots.store') }}">
                            @csrf
                            <div class="form-field">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Evening Slot">
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                <div class="form-field">
                                    <label class="form-label">Start</label>
                                    <input type="time" name="start_time" class="form-control">
                                </div>
                                <div class="form-field">
                                    <label class="form-label">End</label>
                                    <input type="time" name="end_time" class="form-control">
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="form-label">Type</label>
                                <select name="slot_type" class="form-control">
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
                    @foreach($breakSlots->merge(\App\Models\Academic\BreakSlot::where('is_active', false)->get()) as $break)
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
                    @endforeach

                    <div style="margin-top:12px;border-top:1px solid rgba(27,79,168,0.07);padding-top:12px">
                        <span class="sec-label">Add Break Slot</span>
                        <form method="POST" action="{{ route('admin.patches.breakslots.store') }}">
                            @csrf
                            <div class="form-field">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Prayer Break">
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                <div class="form-field">
                                    <label class="form-label">Start</label>
                                    <input type="time" name="start_time" class="form-control">
                                </div>
                                <div class="form-field">
                                    <label class="form-label">End</label>
                                    <input type="time" name="end_time" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn-add">+ Add Break</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- New Patch Modal --}}
<div id="newPatchModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:500px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18)">
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)"></div>
        <div style="padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08)">
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px">Admin Panel</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#1B4FA8">New Patch</div>
        </div>
        <form method="POST" action="{{ route('admin.patches.index') }}">
            @csrf
            <div style="padding:20px 24px">
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Patch Name *</label>
                    <input type="text" name="name" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;width:100%" placeholder="e.g. Patch Spring 2026" required>
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Branch *</label>
                    <select name="branch_id" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;width:100%" required>
                        <option value="">— Select Branch —</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Start Date *</label>
                        <input type="date" name="start_date" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;width:100%" required>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">End Date *</label>
                        <input type="date" name="end_date" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box;width:100%" required>
                    </div>
                </div>
            </div>
            <div style="padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="document.getElementById('newPatchModal').classList.remove('show')"
                    style="padding:9px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 24px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Create Patch
                </button>
            </div>
        </form>
    </div>
</div>

<style>
#newPatchModal.show{display:flex!important}
#newPatchModal input:focus,#newPatchModal select:focus{border-color:#1B4FA8!important;box-shadow:0 0 0 3px rgba(27,79,168,0.07)!important}
</style>

<script>
document.getElementById('newPatchModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});
</script>
@endsection