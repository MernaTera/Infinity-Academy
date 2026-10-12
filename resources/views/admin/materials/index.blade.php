@extends('admin.layouts.app')
@section('title', 'Materials')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--blue-l:rgba(27,79,168,0.10);--orange:#F5911E;--orange-dk:#C47010;--orange-l:rgba(245,145,30,0.12);--green:#059669;--green-dk:#15803D;--green-l:rgba(5,150,105,0.10);--red:#DC2626;--red-l:rgba(220,38,38,0.08);--purple:#7C3AED;--purple-l:rgba(124,58,237,0.10);--border:rgba(27,79,168,0.08);--bg:#F8F6F2;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;}
*{box-sizing:border-box;}
.mat-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none;}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%;}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0 0 24px;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px;}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh);}
.kpi-card::before{display:none;}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:600;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(27,79,168,0.1);display:block;margin-top:4px;font-weight:600;}

.card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;margin-bottom:24px;box-shadow:var(--glass-sh);}
.card-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;color:var(--text);}
.card-body{padding:22px;}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;}
.form-grid-3{grid-template-columns:2fr 1fr 1fr;}
.form-field{display:flex;flex-direction:column;gap:5px;}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600;}
.form-control{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box;appearance:none;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08);}
select.form-control{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%235A6A85'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px;background-color:rgba(255,255,255,0.7);}

.btn-primary{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 10px 24px rgba(27,79,168,0.28);}
.btn-primary::before{display:none;}
.btn-primary:hover::before{transform:none;}
.btn-primary:hover{color:#fff;transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36);}
.btn-primary span,.btn-primary svg{position:relative;z-index:1;}
.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;font-family:'DM Sans',sans-serif;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;transition:all 0.2s;font-weight:600;}
.btn-edit{color:var(--blue);border-color:rgba(27,79,168,0.22);}
.btn-edit:hover{background:var(--blue-l);}
.btn-danger{color:var(--red);border-color:rgba(220,38,38,0.22);}
.btn-danger:hover{background:var(--red-l);}
.btn-toggle-on{color:var(--green-dk);border-color:rgba(5,150,105,0.25);}
.btn-toggle-on:hover{background:var(--green-l);}
.btn-toggle-off{color:var(--red);border-color:rgba(220,38,38,0.22);}
.btn-toggle-off:hover{background:var(--red-l);}

.tbl-wrap{overflow-x:auto;}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid rgba(27,79,168,0.08);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.03);}
.tbl td{padding:13px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

.badge{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600;}
.badge-active{background:var(--green-l);color:var(--green-dk);border:1px solid rgba(5,150,105,0.2);}
.badge-inactive{background:var(--red-l);color:var(--red);border:1px solid rgba(220,38,38,0.18);}
.badge-mandatory{background:var(--orange-l);color:var(--orange-dk);border:1px solid rgba(245,145,30,0.25);}
.badge-optional{background:var(--blue-l);color:var(--blue);border:1px solid rgba(27,79,168,0.18);}

.assign-scope{font-size:11px;color:var(--text);}
.assign-scope span{color:var(--faint);margin:0 4px;}

.tab-bar{display:flex;gap:6px;border-bottom:1px solid rgba(27,79,168,0.1);margin-bottom:24px;flex-wrap:wrap;}
.tab{padding:10px 18px;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;border:none;background:none;color:var(--muted);border-bottom:2px solid transparent;margin-bottom:-1px;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.tab:hover{color:var(--blue);}
.tab.active{color:var(--blue);border-bottom-color:var(--blue);font-weight:600;}
.tab-panel{display:none;}
.tab-panel.active{display:block;animation:panelIn .3s ease both;}
@keyframes panelIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

.flash{padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px;border:1px solid;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.flash-success{background:var(--green-l);border-color:rgba(5,150,105,0.2);color:var(--green-dk);}
.flash-error{background:var(--red-l);border-color:rgba(220,38,38,0.2);color:var(--red);}
.hint-box{padding:12px 16px;background:var(--blue-l);border:1px solid rgba(27,79,168,0.15);border-radius:12px;font-size:11px;color:var(--muted);margin-bottom:18px;}

.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:460px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(23,45,90,0.18);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--text);}
.modal-body{padding:20px 22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}
.btn-modal-cancel{padding:9px 18px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;}
.btn-modal-submit{padding:10px 24px;background:linear-gradient(120deg,#1B4FA8,#2D6FDB);border:none;border-radius:12px;color:#fff;box-shadow:0 10px 24px rgba(27,79,168,0.28);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;}

@media(max-width:768px){.mat-page{padding:18px 14px;}.kpi-grid{grid-template-columns:1fr 1fr;}.form-grid,.form-grid-3{grid-template-columns:1fr;}}
</style>

<div class="mat-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel — Academic</div>
            <h1 class="page-title">Materials</h1>
        </div>
        <button onclick="document.getElementById('createModal').classList.add('open')" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Material</span>
        </button>
    </div>

    @if(session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Total Materials</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--orange)"><div class="kpi-label">Assigned</div><div class="kpi-val">{{ $stats['assigned'] }}</div></div>
    </div>

    <div class="tab-bar">
        <button class="tab active" onclick="switchTab('materials', this)">Materials</button>
        <button class="tab" onclick="switchTab('assignments', this)">Assignments</button>
        <button class="tab" onclick="switchTab('assign-new', this)">Assign Material</button>
    </div>

    <div id="tab-materials" class="tab-panel active">
        <div class="card">
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Revenue Type</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $mat)
                        <tr>
                            <td style="color:var(--faint);font-size:11px;">{{ $mat->material_id }}</td>
                            <td style="font-weight:500;color:var(--text);">{{ $mat->name }}</td>
                            <td style="font-family:monospace;font-size:13px;color:var(--text);">{{ number_format($mat->price, 2) }} LE</td>
                            <td>
                                @if($mat->revenue_type === 'Individual')
                                <span class="badge" style="background:var(--orange-l);color:var(--orange-dk);border:1px solid rgba(245,145,30,0.25);">Individual</span>
                                @else
                                <span class="badge" style="background:var(--blue-l);color:var(--blue);border:1px solid rgba(27,79,168,0.18);">Shared</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $mat->is_active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $mat->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @php $matAssigns = $assignments->get($mat->material_id, collect()); @endphp
                                @if($matAssigns->count())
                                <div style="display:flex;flex-direction:column;gap:4px;">
                                    @foreach($matAssigns->take(3) as $a)
                                    <div class="assign-scope">
                                        {{ $a->course_name ?? '—' }}
                                        @if($a->level_name) <span>›</span> {{ $a->level_name }} @endif
                                        @if($a->sublevel_name) <span>›</span> {{ $a->sublevel_name }} @endif
                                    </div>
                                    @endforeach
                                    @if($matAssigns->count() > 3)
                                    <div style="font-size:10px;color:var(--faint);">+{{ $matAssigns->count() - 3 }} more</div>
                                    @endif
                                </div>
                                @else
                                <span style="color:var(--faint);font-size:11px;">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <button class="btn-sm btn-edit"
                                            onclick="openEdit({{ $mat->material_id }}, '{{ addslashes($mat->name) }}', {{ $mat->price }}, '{{ $mat->revenue_type }}')">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.materials.toggle', $mat->material_id) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-sm {{ $mat->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                                            {{ $mat->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--faint);">No materials yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tab-assignments" class="tab-panel">
        <div class="card">
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Course</th>
                            <th>Level</th>
                            <th>Sublevel</th>
                            <th>Mandatory</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentBranchId = \App\Support\BranchContext::currentBranchId();
                            $allAssignments = DB::table('material_assignment')
                                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                                ->leftJoin('course_template', 'course_template.course_template_id', '=', 'material_assignment.course_template_id')
                                ->leftJoin('level', 'level.level_id', '=', 'material_assignment.level_id')
                                ->leftJoin('sublevel', 'sublevel.sublevel_id', '=', 'material_assignment.sublevel_id')
                                ->when($currentBranchId !== null, fn($q) => $q->where('materials.branch_id', $currentBranchId))
                                ->select('material_assignment.*', 'materials.name as mat_name', 'course_template.name as course_name', 'level.name as level_name', 'sublevel.name as sub_name')
                                ->orderByDesc('material_assignment.created_at')
                                ->get();
                        @endphp
                        @forelse($allAssignments as $a)
                        <tr>
                            <td style="font-weight:500;color:var(--text);">{{ $a->mat_name }}</td>
                            <td>{!! $a->course_name ?? '<span style="color:var(--faint);">—</span>' !!} </td>
                            <td>{{ $a->level_name ?? '—' }}</td>
                            <td>{{ $a->sub_name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $a->is_mandatory ? 'badge-mandatory' : 'badge-optional' }}">
                                    {{ $a->is_mandatory ? 'Mandatory' : 'Optional' }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.materials.unassign', $a->id) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--faint);">No assignments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tab-assign-new" class="tab-panel">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Assign Material to Course / Level / Sublevel</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.materials.assign') }}">
                    @csrf
                    <div class="form-grid" style="margin-bottom:16px;">

                        <div class="form-field">
                            <label class="form-label">Material <span style="color:var(--orange);">*</span></label>
                            <select name="material_id" class="form-control" required>
                                <option value="">— Select Material —</option>
                                @foreach($materials->where('is_active', 1) as $mat)
                                <option value="{{ $mat->material_id }}">{{ $mat->name }} ({{ number_format($mat->price, 2) }} LE)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Course</label>
                            <select name="course_template_id" id="assign_course" class="form-control" >
                                <option value="">— Course Level only —</option>
                                @foreach($courses as $c)
                                <option value="{{ $c->course_template_id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Level <span style="color:var(--faint);font-size:8px;">(optional)</span></label>
                            <select name="level_id" id="assign_level" class="form-control">
                                <option value="">— Select Level —</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Sublevel <span style="color:var(--faint);font-size:8px;">(optional)</span></label>
                            <select name="sublevel_id" id="assign_sublevel" class="form-control">
                                <option value="">— Select Sublevel —</option>
                            </select>
                        </div>

                    </div>

                    <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted);cursor:pointer;margin-bottom:18px;">
                        <input type="checkbox" name="is_mandatory" value="1" style="accent-color:var(--orange);">
                        Mark as mandatory
                    </label>

                    <div class="hint-box">
                        💡 Leave Level and Sublevel empty to assign to the whole course. Select Level only to assign to all sublevels under it.
                    </div>

                    <button type="submit" class="btn-primary">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Save Assignment</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<div class="modal-backdrop" id="createModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">New Material</div>
            <button onclick="document.getElementById('createModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.materials.store') }}">
            @csrf
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div class="form-field">
                        <label class="form-label">Material Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Headway Book" required>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-field">
                            <label class="form-label">Price (LE) <span style="color:var(--orange);">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Revenue Type <span style="color:var(--orange);">*</span></label>
                            <select name="revenue_type" class="form-control" required>
                                <option value="Shared">Shared — split among all CS in branch</option>
                                <option value="Individual">Individual — to CS who registered</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('createModal').classList.remove('open')"
                        class="btn-modal-cancel">
                    Cancel
                </button>
                <button type="submit"
                        class="btn-modal-submit">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Edit Material</div>
            <button onclick="document.getElementById('editModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div class="form-field">
                        <label class="form-label">Material Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-field">
                            <label class="form-label">Price (LE) <span style="color:var(--orange);">*</span></label>
                            <input type="number" id="edit_price" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Revenue Type <span style="color:var(--orange);">*</span></label>
                            <select id="edit_revenue" name="revenue_type" class="form-control" required>
                                <option value="Shared">Shared — split among all CS in branch</option>
                                <option value="Individual">Individual — to CS who registered</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('editModal').classList.remove('open')"
                        class="btn-modal-cancel">
                    Cancel
                </button>
                <button type="submit"
                        class="btn-modal-submit">
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

function openEdit(id, name, price, revenueType) {
    document.getElementById('editForm').action     = `/admin/materials/${id}`;
    document.getElementById('edit_name').value     = name;
    document.getElementById('edit_price').value    = price;
    document.getElementById('edit_revenue').value  = revenueType;
    document.getElementById('editModal').classList.add('open');
}

function switchTab(name, el) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    el.classList.add('active');
}

async function loadLevels(courseId) {
    const sel = document.getElementById('assign_level');
    const subSel = document.getElementById('assign_sublevel');
    sel.innerHTML = '<option value="">— Select Level —</option>';
    subSel.innerHTML = '<option value="">— Select Sublevel —</option>';
    if (!courseId) return;
    const res = await fetch(`/admin/materials/levels/${courseId}`);
    const data = await res.json();
    data.forEach(l => sel.innerHTML += `<option value="${l.level_id}">${l.name}</option>`);
}

async function loadSublevels(levelId) {
    const sel = document.getElementById('assign_sublevel');
    sel.innerHTML = '<option value="">— Select Sublevel —</option>';
    if (!levelId) return;
    const res = await fetch(`/admin/materials/sublevels/${levelId}`);
    const data = await res.json();
    data.forEach(s => sel.innerHTML += `<option value="${s.sublevel_id}">${s.name}</option>`);
}
document.addEventListener('DOMContentLoaded', function() {
    const courseEl   = document.getElementById('assign_course');
    const levelEl    = document.getElementById('assign_level');
    const subLevelEl = document.getElementById('assign_sublevel');

    if (courseEl) {
        courseEl.addEventListener('change', async function() {
            levelEl.innerHTML    = '<option value="">— Select Level —</option>';
            subLevelEl.innerHTML = '<option value="">— Select Sublevel —</option>';
            if (!this.value) return;
            const res  = await fetch(`/admin/materials/levels/${this.value}`);
            const data = await res.json();
            data.forEach(l => levelEl.innerHTML += `<option value="${l.level_id}">${l.name}</option>`);
        });
    }

    if (levelEl) {
        levelEl.addEventListener('change', async function() {
            subLevelEl.innerHTML = '<option value="">— Select Sublevel —</option>';
            if (!this.value) return;
            const res  = await fetch(`/admin/materials/sublevels/${this.value}`);
            const data = await res.json();
            data.forEach(s => subLevelEl.innerHTML += `<option value="${s.sublevel_id}">${s.name}</option>`);
        });
    }
});
</script>

@endsection