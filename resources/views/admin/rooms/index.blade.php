@extends('admin.layouts.app')
@section('title', 'Rooms')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.rm-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px;}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:6px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:16px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);display:block;}

/* Filter */
.filter-bar{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.filter-btn{padding:6px 16px;border-radius:4px;font-size:10px;letter-spacing:2px;text-transform:uppercase;border:1px solid var(--border);background:var(--card);color:var(--muted);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.filter-btn.active,.filter-btn:hover{border-color:var(--blue);color:var(--blue);background:var(--blue-l);}

/* Rooms Grid */
.rooms-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;}
@media(max-width:1100px){.rooms-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:700px){.rooms-grid{grid-template-columns:1fr;}}

.rm-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;position:relative;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.rm-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(27,79,168,0.1);}
.rm-card.offline::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--blue),#2D6FDB);}
.rm-card.online::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--green),#10B981);}
.rm-card.inactive{opacity:0.6;filter:grayscale(30%);}

.rm-card-header{padding:20px 22px 14px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
.rm-type-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.rm-type-icon.offline{background:var(--blue-l);}
.rm-type-icon.online{background:var(--green-l);}
.rm-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--text);line-height:1.1;margin-top:2px;}
.rm-branch{font-size:10px;color:var(--faint);margin-top:4px;letter-spacing:0.5px;}

.rm-type-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;padding:3px 9px;border-radius:3px;}
.rm-type-badge.offline{background:var(--blue-l);color:var(--blue);border:1px solid var(--border);}
.rm-type-badge.online{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.2);}

.rm-stats{display:grid;grid-template-columns:1fr 1fr 1fr;border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.rm-stat{padding:12px 14px;text-align:center;border-right:1px solid var(--border);}
.rm-stat:last-child{border-right:none;}
.rm-stat-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:4px;}
.rm-stat-val{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;line-height:1;}

.rm-card-footer{padding:12px 22px;display:flex;align-items:center;justify-content:space-between;gap:8px;}

/* Buttons */
.btn-primary{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:transparent;border:1.5px solid var(--blue);border-radius:4px;color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.3s;}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);}
.btn-primary:hover::before{transform:scaleX(1);}
.btn-primary:hover{color:#fff;}
.btn-primary span,.btn-primary svg{position:relative;z-index:1;}
.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;font-family:'DM Sans',sans-serif;border:1px solid;background:transparent;cursor:pointer;transition:all 0.2s;}
.btn-edit{color:var(--blue);border-color:rgba(27,79,168,0.25);}
.btn-edit:hover{background:var(--blue-l);}
.btn-danger{color:var(--red);border-color:rgba(220,38,38,0.2);}
.btn-danger:hover{background:var(--red-l);}
.btn-toggle-on{color:var(--green);border-color:rgba(5,150,105,0.25);}
.btn-toggle-on:hover{background:var(--green-l);}
.btn-toggle-off{color:var(--red);border-color:rgba(220,38,38,0.2);}
.btn-toggle-off:hover{background:var(--red-l);}

/* Form */
.form-field{display:flex;flex-direction:column;gap:5px;}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
.form-control{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;box-sizing:border-box;appearance:none;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
select.form-control{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237A8A9A'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px;background-color:#fff;}

/* Modal */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.45);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:460px;background:var(--bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 24px 60px rgba(27,79,168,0.15);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--blue);}
.modal-body{padding:22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}

/* Type selector */
.type-options{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.type-option{position:relative;}
.type-option input{position:absolute;opacity:0;width:0;height:0;}
.type-option label{display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px;border:1.5px solid var(--border);border-radius:6px;cursor:pointer;transition:all 0.2s;background:var(--card);}
.type-option input:checked + label{border-color:var(--blue);background:var(--blue-l);}
.type-option label:hover{border-color:rgba(27,79,168,0.3);}
.type-option-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;}
.type-option-label{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
.type-option input:checked + label .type-option-label{color:var(--blue);font-weight:600;}

@media(max-width:768px){.rm-page{padding:18px 14px;}.kpi-grid{grid-template-columns:1fr 1fr;}}
</style>

<div class="rm-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel — Academic</div>
            <h1 class="page-title">Rooms</h1>
        </div>
        <button onclick="document.getElementById('createModal').classList.add('open')" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Room</span>
        </button>
    </div>

    @if(session('success'))
    <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('error') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Total Rooms</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Offline</div><div class="kpi-val">{{ $stats['offline'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Online</div><div class="kpi-val">{{ $stats['online'] }}</div></div>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterRooms('all', this)">All</button>
        <button class="filter-btn" onclick="filterRooms('offline', this)">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Offline
        </button>
        <button class="filter-btn" onclick="filterRooms('online', this)">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            Online
        </button>
        @foreach($branches as $b)
        <button class="filter-btn" onclick="filterByBranch({{ $b->branch_id }}, this)">{{ $b->name }}</button>
        @endforeach
    </div>

    {{-- Rooms Grid --}}
    <span class="sec-label">All Rooms</span>

    @if($rooms->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--faint);">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 16px;display:block;opacity:0.4;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <div style="font-size:13px;">No rooms yet. Create your first one!</div>
    </div>
    @else
    <div class="rooms-grid" id="roomsGrid">
        @foreach($rooms as $room)
        @php $type = strtolower($room->room_type); @endphp
        <div class="rm-card {{ $type }} {{ !$room->is_active ? 'inactive' : '' }}"
             data-type="{{ $type }}" data-branch="{{ $room->branch_id }}">

            <div class="rm-card-header">
                <div>
                    <div class="rm-type-badge {{ $type }}">
                        @if($type === 'offline')
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        Offline
                        @else
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
                        Online
                        @endif
                    </div>
                    <div class="rm-name">{{ $room->name }}</div>
                    <div class="rm-branch">{{ $room->branch?->name ?? '—' }}</div>
                </div>
                <div class="rm-type-icon {{ $type }}">
                    @if($type === 'offline')
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    @else
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    @endif
                </div>
            </div>

            <div class="rm-stats">
                <div class="rm-stat">
                    <div class="rm-stat-label">Capacity</div>
                    <div class="rm-stat-val" style="color:var(--blue);">{{ $room->capacity }}</div>
                </div>
                <div class="rm-stat">
                    <div class="rm-stat-label">Courses</div>
                    <div class="rm-stat-val" style="color:var(--purple);">{{ $room->course_instances_count }}</div>
                </div>
                <div class="rm-stat">
                    <div class="rm-stat-label">Status</div>
                    <div class="rm-stat-val" style="font-size:12px;font-family:'DM Sans',sans-serif;color:{{ $room->is_active ? 'var(--green)' : 'var(--red)' }};">
                        {{ $room->is_active ? 'Active' : 'Off' }}
                    </div>
                </div>
            </div>

            <div class="rm-card-footer">
                <div style="font-size:10px;color:var(--faint);">
                    {{ $room->created_at?->format('d M Y') }}
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end;">
                    <button class="btn-sm btn-edit"
                            onclick="openEdit({{ $room->room_id }}, '{{ addslashes($room->name) }}', {{ $room->branch_id }}, {{ $room->capacity }}, '{{ $room->room_type }}')">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.rooms.toggle', $room->room_id) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-sm {{ $room->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                            {{ $room->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    @if($room->course_instances_count === 0)
                    <form method="POST" action="{{ route('admin.rooms.destroy', $room->room_id) }}" style="display:inline;"
                          onsubmit="return confirm('Delete this room permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-danger">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                        </button>
                    </form>
                    @else
                    <span style="font-size:9px;color:var(--faint);align-self:center;" title="Has active courses">🔒</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- ── CREATE MODAL ── --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">New Room</div>
            <button onclick="document.getElementById('createModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div class="form-field">
                        <label class="form-label">Room Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Room A1" required>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-field">
                            <label class="form-label">Branch <span style="color:var(--orange);">*</span></label>
                            <select name="branch_id" class="form-control" required>
                                <option value="">— Select Branch —</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Capacity <span style="color:var(--orange);">*</span></label>
                            <input type="number" name="capacity" class="form-control" placeholder="e.g. 15" min="1" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label" style="margin-bottom:10px;">Room Type <span style="color:var(--orange);">*</span></label>
                        <div class="type-options">
                            <div class="type-option">
                                <input type="radio" name="room_type" id="c_offline" value="Offline" checked>
                                <label for="c_offline">
                                    <div class="type-option-icon" style="background:var(--blue-l);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                    </div>
                                    <span class="type-option-label">Offline</span>
                                </label>
                            </div>
                            <div class="type-option">
                                <input type="radio" name="room_type" id="c_online" value="Online">
                                <label for="c_online">
                                    <div class="type-option-icon" style="background:var(--green-l);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    </div>
                                    <span class="type-option-label">Online</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('createModal').classList.remove('open')"
                        style="padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:10px 24px;background:var(--blue);border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT MODAL ── --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Edit Room</div>
            <button onclick="document.getElementById('editModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div class="form-field">
                        <label class="form-label">Room Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-field">
                            <label class="form-label">Branch <span style="color:var(--orange);">*</span></label>
                            <select id="edit_branch" name="branch_id" class="form-control" required>
                                <option value="">— Select Branch —</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Capacity <span style="color:var(--orange);">*</span></label>
                            <input type="number" id="edit_capacity" name="capacity" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label" style="margin-bottom:10px;">Room Type</label>
                        <div class="type-options">
                            <div class="type-option">
                                <input type="radio" name="room_type" id="e_offline" value="Offline">
                                <label for="e_offline">
                                    <div class="type-option-icon" style="background:var(--blue-l);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                    </div>
                                    <span class="type-option-label">Offline</span>
                                </label>
                            </div>
                            <div class="type-option">
                                <input type="radio" name="room_type" id="e_online" value="Online">
                                <label for="e_online">
                                    <div class="type-option-icon" style="background:var(--green-l);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    </div>
                                    <span class="type-option-label">Online</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('editModal').classList.remove('open')"
                        style="padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:10px 24px;background:var(--blue);border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
['createModal','editModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') ['createModal','editModal'].forEach(id => document.getElementById(id).classList.remove('open'));
});

function openEdit(id, name, branchId, capacity, type) {
    document.getElementById('editForm').action   = `/admin/rooms/${id}`;
    document.getElementById('edit_name').value    = name;
    document.getElementById('edit_branch').value  = branchId;
    document.getElementById('edit_capacity').value = capacity;
    document.getElementById(type === 'Offline' ? 'e_offline' : 'e_online').checked = true;
    document.getElementById('editModal').classList.add('open');
}

function filterRooms(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.rm-card').forEach(card => {
        if (type === 'all' || card.dataset.type === type) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterByBranch(branchId, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.rm-card').forEach(card => {
        card.style.display = card.dataset.branch == branchId ? '' : 'none';
    });
}
</script>

@endsection