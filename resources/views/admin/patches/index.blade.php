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

.two-col{display:block;}

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
                    @if($patch->active_courses_count > 0)
                    <div style="margin:0 20px 12px;padding:8px 12px;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.25);border-left:3px solid #F5911E;border-radius:4px;display:flex;align-items:center;gap:8px;font-size:11px;color:#C47010;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <span><strong>{{ $patch->active_courses_count }}</strong> active {{ Str::plural('course', $patch->active_courses_count) }} — changes are locked.</span>
                    </div>
                    @endif
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
                        @php $hasActive = $patch->active_courses_count > 0; @endphp

                        {{-- Activate --}}
                        @if($patch->status === 'Upcoming')
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="btn-sm btn-activate" {{ $hasActive ? 'disabled' : '' }}
                                style="{{ $hasActive ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                                {{ $hasActive ? 'title=Active courses block this action' : '' }}>
                                ▶ Activate
                            </button>
                        </form>
                        @endif

                        {{-- Close --}}
                        @if($patch->status === 'Active')
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="close">
                            <button type="submit" class="btn-sm btn-close" {{ $hasActive ? 'disabled' : '' }}
                                style="{{ $hasActive ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                                {{ $hasActive ? 'title=Active courses block this action' : '' }}>
                                ■ Close
                            </button>
                        </form>
                        @endif

                        {{-- Lock / Unlock --}}
                        @if(!$patch->is_locked)
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="lock">
                            <button type="submit" class="btn-sm btn-lock" {{ $hasActive ? 'disabled' : '' }}
                                style="{{ $hasActive ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                                {{ $hasActive ? 'title=Active courses block this action' : '' }}>
                                🔒 Lock
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.patches.status', $patch->patch_id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="unlock">
                            <button type="submit" class="btn-sm btn-unlock">🔓 Unlock</button>
                        </form>
                        @endif

                        {{-- Edit --}}
                        @if($patch->status !== 'Closed')
                        <button {{ $hasActive ? 'disabled' : '' }}
                            onclick="{{ $hasActive ? '' : "openEditPatch({$patch->patch_id}, '".addslashes($patch->name)."', {$patch->branch_id}, '".$patch->start_date->format('Y-m-d')."', '".$patch->end_date->format('Y-m-d')."')" }}"
                            class="btn-sm" style="color:#1B4FA8;border-color:rgba(27,79,168,0.2);{{ $hasActive ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                            {{ $hasActive ? 'title=Active courses block this action' : '' }}>
                            ✎ Edit
                        </button>
                        @endif

                        {{-- Delete --}}
                        @if($patch->status !== 'Active' && $patch->course_instances_count == 0)
                        <form method="POST" action="{{ route('admin.patches.destroy', $patch->patch_id) }}"
                            style="display:inline" onsubmit="return confirm('Delete this patch?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-close">✕ Delete</button>
                        </form>
                        @endif
                        <button type="button" 
                                onclick="openExtendModal({{ $patch->patch_id }}, '{{ $patch->name }}', '{{ $patch->end_date }}', '{{ $patch->start_date }}')"
                                style="padding:6px 12px;background:transparent;border:1px solid rgba(27,79,168,0.25);
                                    border-radius:5px;color:var(--blue);font-family:'DM Sans',sans-serif;
                                    font-size:10px;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;
                                    font-weight:600;transition:all 0.2s;display:inline-flex;align-items:center;gap:5px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <line x1="12" y1="10" x2="12" y2="16"/>
                                <line x1="9" y1="13" x2="15" y2="13"/>
                            </svg>
                            Adjust End Date
                        </button>
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
        <form method="POST" action="{{ route('admin.patches.store') }}">
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

{{-- Edit Patch Modal --}}
<div id="editPatchModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:500px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;box-shadow:0 20px 60px rgba(27,79,168,0.18);position:relative">
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)"></div>
        <div style="padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08)">
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:3px">Admin Panel</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#1B4FA8">Edit Patch</div>
        </div>
        <form id="editPatchForm" method="POST">
            @csrf @method('PUT')
            <div style="padding:20px 24px">
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Name *</label>
                    <input type="text" name="name" id="edit_patch_name" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;width:100%;box-sizing:border-box" required>
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Branch *</label>
                    <select name="branch_id" id="edit_patch_branch" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;width:100%;box-sizing:border-box" required>
                        @foreach($branches as $b)
                        <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">Start Date *</label>
                        <input type="date" name="start_date" id="edit_patch_start" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;width:100%;box-sizing:border-box" required>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A">End Date *</label>
                        <input type="date" name="end_date" id="edit_patch_end" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;width:100%;box-sizing:border-box" required>
                    </div>
                </div>
            </div>
            <div style="padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="closeEditPatch()" style="padding:9px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer">Cancel</button>
                <button type="submit" style="padding:10px 24px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPatch(id, name, branchId, startDate, endDate) {
    document.getElementById('editPatchForm').action = `/admin/patches/${id}`;
    document.getElementById('edit_patch_name').value   = name;
    document.getElementById('edit_patch_branch').value = branchId;
    document.getElementById('edit_patch_start').value  = startDate;
    document.getElementById('edit_patch_end').value    = endDate;
    document.getElementById('editPatchModal').style.display = 'flex';
}
function closeEditPatch() {
    document.getElementById('editPatchModal').style.display = 'none';
}
document.getElementById('editPatchModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditPatch();
});
</script>


<style>
#newPatchModal.show{display:flex!important}
#newPatchModal input:focus,#newPatchModal select:focus{border-color:#1B4FA8!important;box-shadow:0 0 0 3px rgba(27,79,168,0.07)!important}
</style>

<script>
document.getElementById('newPatchModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});
</script>
{{-- ══════════════ EXTEND END DATE MODAL ══════════════ --}}
<div id="extendModal" style="display:none;position:fixed;inset:0;background:rgba(10,20,40,0.5);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:10px;width:100%;max-width:480px;overflow:hidden;
                box-shadow:0 20px 60px rgba(10,20,40,0.3);">
        <div style="padding:18px 22px;border-bottom:1px solid rgba(27,79,168,0.09);
                    background:linear-gradient(135deg,#F5911E,#FFB347);color:#fff;">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;">
                Adjust Patch End Date
            </div>
            <div id="extendPatchName" style="font-size:11px;opacity:0.9;margin-top:3px;letter-spacing:0.5px;"></div>
        </div>

        <form id="extendForm" method="POST">
            @csrf @method('PATCH')
            <div style="padding:20px 22px;">
                <div style="background:rgba(245,145,30,0.08);border-left:3px solid #F5911E;
                            padding:10px 14px;border-radius:4px;margin-bottom:16px;font-size:11.5px;
                            color:#C47010;line-height:1.5;">
                    <strong>Notice:</strong> You can extend or shorten the patch end date. New date must be after the last scheduled course session.
                </div>

                <label style="font-size:9px;letter-spacing:2.5px;text-transform:uppercase;
                              color:#7A8A9A;font-weight:600;margin-bottom:6px;display:block;">
                    Current End Date
                </label>
                <div id="currentEndDate" style="font-family:'Bebas Neue',sans-serif;font-size:20px;
                     letter-spacing:2px;color:#1A2A4A;margin-bottom:16px;"></div>

                <label style="font-size:9px;letter-spacing:2.5px;text-transform:uppercase;
                              color:#7A8A9A;font-weight:600;margin-bottom:6px;display:block;">
                    New End Date <span style="color:#DC2626;">*</span>
                </label>
                <input type="date" name="end_date" id="newEndDate" required
                       style="width:100%;padding:11px 14px;background:#F8F6F2;
                              border:1px solid rgba(27,79,168,0.15);border-radius:6px;
                              font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;
                              outline:none;transition:border-color 0.2s;">
                <div id="extendHint" style="font-size:10px;color:#7A8A9A;margin-top:6px;letter-spacing:0.3px;"></div>
            </div>

            <div style="padding:14px 22px;border-top:1px solid rgba(27,79,168,0.09);
                        display:flex;justify-content:flex-end;gap:8px;background:#FAFAF7;">
                <button type="button" onclick="closeExtendModal()"
                        style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);
                               border-radius:5px;color:#7A8A9A;font-family:'DM Sans',sans-serif;
                               font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;
                               font-weight:600;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:10px 22px;background:#F5911E;border:none;border-radius:5px;
                               color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;
                               letter-spacing:3px;cursor:pointer;">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openExtendModal(patchId, patchName, currentEnd, startDate) {
    document.getElementById('extendPatchName').textContent = patchName;
    document.getElementById('extendForm').action = `/admin/patches/${patchId}/extend`;

    const dateInput = document.getElementById('newEndDate');
    dateInput.value = currentEnd;
    dateInput.min = new Date(new Date(startDate).getTime() + 86400000).toISOString().split('T')[0];

    const formatted = new Date(currentEnd).toLocaleDateString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric'
    });
    document.getElementById('currentEndDate').textContent = formatted;

    document.getElementById('extendHint').innerHTML =
        `Patch started on <strong style="color:#1A2A4A;">${new Date(startDate).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'})}</strong>. ` +
        `You can extend or shorten as long as no session is left unscheduled.`;

    document.getElementById('extendModal').style.display = 'flex';
}

function closeExtendModal() {
    document.getElementById('extendModal').style.display = 'none';
}

document.getElementById('extendModal').addEventListener('click', function(e) {
    if (e.target === this) closeExtendModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('extendModal').style.display === 'flex') {
        closeExtendModal();
    }
});
</script>
@endsection