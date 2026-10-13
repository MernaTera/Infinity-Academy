@extends('admin.layouts.app')
@section('title', 'Patches')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
.ptch-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.btn-primary::before{display:none}
.btn-primary:hover::before{transform:none}
.btn-primary:hover{color:#fff;transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36)}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh)}
.kpi-card::before{display:none}
.kpi-card{cursor:pointer;transition:transform 0.2s,box-shadow 0.2s}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 16px 38px rgba(23,45,90,0.13)}
.kpi-card.active-filter{box-shadow:0 0 0 2px var(--kc),var(--glass-sh)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}

.two-col{display:block;}

.patch-list{display:flex;flex-direction:column;gap:12px}
.patch-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;position:relative;transition:transform 0.2s,box-shadow 0.2s;box-shadow:var(--glass-sh)}
.patch-card:hover{transform:translateY(-2px);box-shadow:0 16px 38px rgba(23,45,90,0.13)}
.patch-card.active{border-color:rgba(5,150,105,0.25)}
.patch-card.active::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--green),transparent)}
.patch-card.upcoming::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)}
.patch-card.closed::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:rgba(122,138,154,0.3)}
.pc-header{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.pc-name{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:var(--text)}
.pc-body{padding:0 20px 14px;display:flex;gap:20px;flex-wrap:wrap}
.pc-meta{display:flex;flex-direction:column;gap:2px}
.pc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint)}
.pc-meta-val{font-size:12px;color:var(--text);font-weight:500}
.pc-footer{padding:12px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px;flex-wrap:wrap}

.patch-prog{margin:0 20px 14px;background:rgba(27,79,168,0.10);border-radius:3px;height:4px;overflow:hidden}
.patch-prog-fill{height:4px;border-radius:3px;background:linear-gradient(90deg,var(--blue),var(--blue-2));transition:width 0.6s ease}

.status-badge{display:inline-flex;align-items:center;gap:5px;font-size:9px;letter-spacing:1.2px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600}
.status-badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.s-active{color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.2)}
.s-upcoming{color:var(--blue);background:rgba(27,79,168,0.10);border:1px solid rgba(27,79,168,0.2)}
.s-closed{color:var(--muted);background:rgba(122,138,154,0.10);border:1px solid rgba(122,138,154,0.2)}
.s-locked{color:var(--orange-dk);background:rgba(245,145,30,0.12);border:1px solid rgba(245,145,30,0.22)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap;font-weight:600}
.btn-activate{color:var(--green-dk);border-color:rgba(5,150,105,0.25)}
.btn-activate:hover{background:rgba(5,150,105,0.08)}
.btn-close{color:var(--red);border-color:rgba(220,38,38,0.22)}
.btn-close:hover{background:rgba(220,38,38,0.07)}
.btn-lock{color:var(--orange-dk);border-color:rgba(245,145,30,0.25)}
.btn-lock:hover{background:rgba(245,145,30,0.08)}
.btn-unlock{color:var(--muted);border-color:rgba(122,138,154,0.22)}
.btn-unlock:hover{background:rgba(122,138,154,0.08)}

.slot-item{display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:rgba(255,255,255,0.55);border:1px solid rgba(27,79,168,0.08);border-radius:10px;margin-bottom:6px}
.slot-name{font-size:12px;color:var(--text);font-weight:500}
.slot-time{font-size:10px;color:var(--muted);font-family:monospace}
.slot-actions{display:flex;gap:6px}
.toggle-btn{padding:3px 8px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:8px;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s}
.active-slot .toggle-btn{color:var(--red);border-color:rgba(220,38,38,0.2)}
.active-slot .toggle-btn:hover{background:rgba(220,38,38,0.07)}
.inactive-slot{opacity:0.5}
.inactive-slot .toggle-btn{color:var(--green-dk);border-color:rgba(5,150,105,0.2)}
.inactive-slot .toggle-btn:hover{background:rgba(5,150,105,0.07)}

@media(max-width:1024px){.two-col{grid-template-columns:1fr}.ptch-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="ptch-page" style="padding:40px 32px">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

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
    @if($errors->any())
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
    @endif
    @endif

    <div class="kpi-grid">
        <div class="kpi-card active-filter" style="--kc:#1B4FA8" data-filter="all" onclick="filterPatches('all', this)"><div class="kpi-label">Total</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:#059669" data-filter="active" onclick="filterPatches('active', this)"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:#1B6FA8" data-filter="upcoming" onclick="filterPatches('upcoming', this)"><div class="kpi-label">Upcoming</div><div class="kpi-val">{{ $stats['upcoming'] }}</div></div>
        <div class="kpi-card" style="--kc:#5A6A85" data-filter="closed" onclick="filterPatches('closed', this)"><div class="kpi-label">Closed</div><div class="kpi-val">{{ $stats['closed'] }}</div></div>
    </div>

    <div class="two-col">

        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                <span style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#5A6A85;font-weight:600">Academic Patches</span>
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
                    $totalDays  = (int) $start->diffInDays($end) + 1;
                    $elapsed    = max(0, min($totalDays, $start->diffInDays($today)));
                    $pct        = round($elapsed / $totalDays * 100);
                @endphp
                <div class="patch-card {{ $pClass }}" data-status="{{ $pClass }}">
                    <div class="pc-header">
                        <div>
                            <div class="pc-name">{{ $patch->name }}</div>
                            <div style="font-size:10px;color:#5A6A85;margin-top:2px">{{ $patch->branch?->name ?? '—' }}</div>
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
                    <div style="padding:0 20px 10px;font-size:10px;color:#93A3BC;text-align:right">{{ $pct }}% elapsed</div>
                    @endif
                    <div class="pc-footer">
                        @php $hasActive = $patch->active_courses_count > 0; @endphp

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

                        @if($patch->status !== 'Closed')
                        <button {{ $hasActive ? 'disabled' : '' }}
                            onclick="{{ $hasActive ? '' : "openEditPatch({$patch->patch_id}, '".addslashes($patch->name)."', {$patch->branch_id}, '".$patch->start_date->format('Y-m-d')."', '".$patch->end_date->format('Y-m-d')."')" }}"
                            class="btn-sm" style="color:#1B4FA8;border-color:rgba(27,79,168,0.2);{{ $hasActive ? 'opacity:0.4;cursor:not-allowed;' : '' }}"
                            {{ $hasActive ? 'title=Active courses block this action' : '' }}>
                            ✎ Edit
                        </button>
                        @endif

                        @if($patch->status !== 'Active' && $patch->course_instances_count == 0)
                        <form method="POST" action="{{ route('admin.patches.destroy', $patch->patch_id) }}"
                            style="display:inline" data-name="{{ $patch->name }}" onsubmit="return askDeletePatch(this)">
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
                <div style="text-align:center;padding:48px;color:#93A3BC;background:rgba(255,255,255,0.5);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid rgba(255,255,255,0.6);border-radius:18px;box-shadow:0 8px 30px rgba(23,45,90,0.07)">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">No Patches Yet</div>
                    <div style="font-size:12px">Create your first academic patch</div>
                </div>
                @endforelse
                <div id="patchFilterEmpty" style="display:none;text-align:center;padding:40px;color:#93A3BC;font-size:12px;background:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.6);border-radius:18px;box-shadow:0 8px 30px rgba(23,45,90,0.07)">No patches in this status</div>
            </div>
        </div>
    </div>
</div>

<div id="newPatchModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:500px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18)">
        <!-- <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)"></div> -->
        <div style="padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08)">
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#1B4FA8;margin-bottom:3px;font-weight:600">Admin Panel</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#16233F">New Patch</div>
        </div>
        <form method="POST" action="{{ route('admin.patches.store') }}">
            @csrf
            <div style="padding:20px 24px">
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">Patch Name *</label>
                    <input type="text" name="name" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box;width:100%" placeholder="e.g. Patch Spring 2026" required>
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">Branch *</label>
                    <select name="branch_id" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box;width:100%" required>
                        <option value="">— Select Branch —</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">Start Date *</label>
                        <input type="date" name="start_date" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box;width:100%" required>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">End Date *</label>
                        <input type="date" name="end_date" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box;width:100%" required>
                    </div>
                </div>
            </div>
            <div style="padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="document.getElementById('newPatchModal').classList.remove('show')"
                    style="padding:9px 20px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:#5A6A85;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 24px;background:linear-gradient(120deg,#1B4FA8,#2D6FDB);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(27,79,168,0.28);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Create Patch
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editPatchModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:500px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(23,45,90,0.18);position:relative">
        <!-- <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)"></div> -->
        <div style="padding:20px 24px 16px;border-bottom:1px solid rgba(27,79,168,0.08)">
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#1B4FA8;margin-bottom:3px;font-weight:600">Admin Panel</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#16233F">Edit Patch</div>
        </div>
        <form id="editPatchForm" method="POST">
            @csrf @method('PUT')
            <div style="padding:20px 24px">
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">Name *</label>
                    <input type="text" name="name" id="edit_patch_name" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;width:100%;box-sizing:border-box" required>
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px">
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">Branch *</label>
                    <select name="branch_id" id="edit_patch_branch" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;width:100%;box-sizing:border-box" required>
                        @foreach($branches as $b)
                        <option value="{{ $b->branch_id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">Start Date *</label>
                        <input type="date" name="start_date" id="edit_patch_start" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;width:100%;box-sizing:border-box" required>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#5A6A85">End Date *</label>
                        <input type="date" name="end_date" id="edit_patch_end" style="padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;background:rgba(255,255,255,0.7);outline:none;width:100%;box-sizing:border-box" required>
                    </div>
                </div>
            </div>
            <div style="padding:14px 24px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="closeEditPatch()" style="padding:9px 20px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:#5A6A85;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer">Cancel</button>
                <button type="submit" style="padding:10px 24px;background:linear-gradient(120deg,#1B4FA8,#2D6FDB);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(27,79,168,0.28);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">Save Changes</button>
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
#newPatchModal input:focus,#newPatchModal select:focus,#editPatchModal input:focus,#editPatchModal select:focus{border-color:#1B4FA8!important;box-shadow:0 0 0 3px rgba(27,79,168,0.07)!important}
</style>

<script>
document.getElementById('newPatchModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});
</script>

<div id="deletePatchModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:420px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18)">
        <!-- <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#DC2626,transparent)"></div> -->
        <div style="padding:26px 24px 8px;text-align:center">
            <div style="width:52px;height:52px;border-radius:16px;background:rgba(220,38,38,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </div>
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#DC2626;margin-bottom:3px;font-weight:600">Delete Patch</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#16233F" id="deletePatchName"></div>
            <div style="font-size:12px;color:#5A6A85;margin-top:8px;line-height:1.5">This patch will be permanently deleted. This action can't be undone.</div>
        </div>
        <div style="padding:18px 24px 22px;display:flex;gap:10px;justify-content:center">
            <button type="button" onclick="closeDeletePatch()"
                style="padding:9px 20px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:#5A6A85;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer">
                Cancel
            </button>
            <button type="button" onclick="confirmDeletePatch()"
                style="padding:10px 24px;background:linear-gradient(120deg,#DC2626,#EF4444);border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;box-shadow:0 10px 24px rgba(220,38,38,0.28)">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
var _pendingDeleteForm = null;
function askDeletePatch(form) {
    _pendingDeleteForm = form;
    document.getElementById('deletePatchName').textContent = form.dataset.name || '';
    document.getElementById('deletePatchModal').style.display = 'flex';
    return false;
}
function closeDeletePatch() {
    _pendingDeleteForm = null;
    document.getElementById('deletePatchModal').style.display = 'none';
}
function confirmDeletePatch() {
    if (_pendingDeleteForm) _pendingDeleteForm.submit();
}
document.getElementById('deletePatchModal').addEventListener('click', function (e) {
    if (e.target === this) closeDeletePatch();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDeletePatch();
});
</script>

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
                              color:#5A6A85;font-weight:600;margin-bottom:6px;display:block;">
                    Current End Date
                </label>
                <div id="currentEndDate" style="font-family:'Bebas Neue',sans-serif;font-size:20px;
                     letter-spacing:2px;color:#16233F;margin-bottom:16px;"></div>

                <label style="font-size:9px;letter-spacing:2.5px;text-transform:uppercase;
                              color:#5A6A85;font-weight:600;margin-bottom:6px;display:block;">
                    New End Date <span style="color:#DC2626;">*</span>
                </label>
                <input type="date" name="end_date" id="newEndDate" required
                       style="width:100%;padding:11px 14px;background:#F8F6F2;
                              border:1px solid rgba(27,79,168,0.15);border-radius:6px;
                              font-family:'DM Sans',sans-serif;font-size:13px;color:#16233F;
                              outline:none;transition:border-color 0.2s;">
                <div id="extendHint" style="font-size:10px;color:#5A6A85;margin-top:6px;letter-spacing:0.3px;"></div>
            </div>

            <div style="padding:14px 22px;border-top:1px solid rgba(27,79,168,0.09);
                        display:flex;justify-content:flex-end;gap:8px;background:#FAFAF7;">
                <button type="button" onclick="closeExtendModal()"
                        style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);
                               border-radius:5px;color:#5A6A85;font-family:'DM Sans',sans-serif;
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
        `Patch started on <strong style="color:#16233F;">${new Date(startDate).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'})}</strong>. ` +
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
<script>
function filterPatches(status, el) {
    document.querySelectorAll('.kpi-card[data-filter]').forEach(function (c) { c.classList.toggle('active-filter', c === el); });
    var shown = 0;
    document.querySelectorAll('.patch-list .patch-card[data-status]').forEach(function (card) {
        var match = status === 'all' || card.dataset.status === status;
        card.style.display = match ? '' : 'none';
        if (match) shown++;
    });
    var empty = document.getElementById('patchFilterEmpty');
    var total = document.querySelectorAll('.patch-list .patch-card[data-status]').length;
    if (empty) empty.style.display = (total > 0 && shown === 0) ? 'block' : 'none';
}
</script>
@endsection