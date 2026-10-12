@extends('admin.layouts.app')
@section('title', 'Branches')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box}
.br-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.br-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.br-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--text);margin:0 0 24px}

.alert{padding:12px 16px;border-radius:12px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.alert-success{background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:var(--green-dk)}
.alert-error  {background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);color:var(--red)}

.br-layout{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}

.br-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh)}
.br-card-hdr{padding:14px 20px;border-bottom:1px solid rgba(27,79,168,0.08);display:flex;align-items:center;justify-content:space-between}
.br-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text)}

.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:10px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid rgba(27,79,168,0.08);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.03)}
.tbl td{padding:13px 16px;font-size:13px;color:var(--muted);vertical-align:middle}

.status-pill{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600}
.pill-on {background:rgba(5,150,105,0.10);color:var(--green-dk);border:1px solid rgba(5,150,105,0.2)}
.pill-off{background:rgba(220,38,38,0.08);color:var(--red);border:1px solid rgba(220,38,38,0.18)}

.count-badge{display:inline-flex;align-items:center;gap:3px;background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.14);border-radius:20px;padding:2px 9px;font-size:10px;color:var(--blue);font-weight:600}

.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all 0.18s;border:1px solid;font-weight:600;background:rgba(255,255,255,0.5)}
.btn-edit   {color:var(--blue);border-color:rgba(27,79,168,0.22)}
.btn-edit:hover{background:rgba(27,79,168,0.08)}
.btn-danger {color:var(--red);border-color:rgba(220,38,38,0.22)}
.btn-danger:hover{background:rgba(220,38,38,0.07)}
.btn-warn   {color:var(--orange-dk);border-color:rgba(245,145,30,0.3)}
.btn-warn:hover{background:rgba(245,145,30,0.08)}
.btn-primary{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;border-color:transparent;box-shadow:0 8px 20px rgba(27,79,168,0.26)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(27,79,168,0.34)}
.btn-success{background:linear-gradient(120deg,#15803D,#059669);color:#fff;border-color:transparent;box-shadow:0 6px 14px rgba(21,128,61,0.22)}
.btn-success:hover{transform:translateY(-1px);box-shadow:0 10px 20px rgba(21,128,61,0.3)}
.btn-cancel {background:rgba(255,255,255,0.6);color:var(--muted);border-color:rgba(27,79,168,0.15)}
.btn-cancel:hover{color:var(--blue);border-color:var(--blue)}

.add-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh);position:sticky;top:20px}
.add-card-hdr{position:relative;padding:14px 20px;border-bottom:1px solid rgba(27,79,168,0.08)}
/* .add-card-hdr::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)} */
.add-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text)}
.add-body{padding:20px}
.f-field{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}
.f-lbl{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:600}
.f-ctrl{padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;width:100%}
.f-ctrl:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}

.modal-bg{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px}
.modal-bg.open{display:flex;animation:fadeIn 0.2s ease both}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal{width:100%;max-width:420px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
/* .modal::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent)} */
.modal-hdr{padding:16px 20px;border-bottom:1px solid rgba(27,79,168,0.08)}
.modal-eyebrow{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:3px;font-weight:600}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--text)}
.modal-sub{font-size:11px;color:var(--muted);margin-top:3px}
.modal-body{padding:18px 20px}
.modal-foot{padding:12px 20px;border-top:1px solid rgba(27,79,168,0.08);display:flex;gap:8px;justify-content:flex-end}

.warn-box{background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);border-radius:12px;padding:10px 12px;font-size:11px;color:var(--red);margin-bottom:14px;line-height:1.5}
.pass-eye{position:relative}
.pass-eye .f-ctrl{padding-right:36px}
.pass-eye button{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:none;background:transparent;cursor:pointer;color:var(--faint);padding:2px}

.empty{padding:40px;text-align:center;color:var(--faint)}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;color:var(--muted)}

@media(max-width:900px){.br-layout{grid-template-columns:1fr}}
@media(max-width:480px){.br-page{padding:18px 14px}}
</style>

<div class="br-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="br-eyebrow">Admin Panel</div>
    <h1 class="br-title">Branches</h1>

    @if(session('success'))
    <div class="alert alert-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="br-layout">

        <div class="br-card">
            <div class="br-card-hdr">
                <div class="br-card-title">All Branches</div>
                <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#1B4FA8;letter-spacing:1px;background:rgba(27,79,168,0.08);border-radius:10px;padding:0 10px">{{ $branches->count() }}</span>
            </div>
            <div style="overflow-x:auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Code</th>
                            <th>Phone</th>
                            <th>Employees</th>
                            <th>Courses</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($branches as $branch)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#16233F">{{ $branch->name }}</div>
                            @if($branch->address)
                            <div style="font-size:11px;color:#93A3BC;margin-top:2px">{{ $branch->address }}</div>
                            @endif
                        </td>
                        <td>
                            @if($branch->code)
                            <span style="font-family:monospace;font-size:12px;background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.14);padding:2px 8px;border-radius:6px;color:#1B4FA8">
                                {{ $branch->code }}
                            </span>
                            @else
                            <span style="color:#93A3BC">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;font-family:monospace;color:#5A6A85">
                            {{ $branch->phone ?? '—' }}
                        </td>
                        <td>
                            <span class="count-badge">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                {{ $branch->employees_count }}
                            </span>
                        </td>
                        <td>
                            <span class="count-badge">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                {{ $branch->course_instances_count }}
                            </span>
                        </td>
                        <td>
                            <span class="status-pill {{ $branch->is_active ? 'pill-on' : 'pill-off' }}">
                                <span style="width:5px;height:5px;border-radius:50%;background:currentColor;display:inline-block"></span>
                                {{ $branch->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap">
                                <button class="btn btn-edit"
                                    onclick="openEdit({{ $branch->branch_id }}, '{{ addslashes($branch->name) }}', '{{ addslashes($branch->code ?? '') }}', '{{ addslashes($branch->address ?? '') }}', '{{ addslashes($branch->phone ?? '') }}')">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </button>

                                <button class="btn {{ $branch->is_active ? 'btn-warn' : 'btn-success' }}"
                                    onclick="openToggle({{ $branch->branch_id }}, '{{ addslashes($branch->name) }}', {{ $branch->is_active ? 'true' : 'false' }})">
                                    {{ $branch->is_active ? 'Deactivate' : 'Activate' }}
                                </button>

                                @if($branch->employees_count === 0 && $branch->course_instances_count === 0)
                                <button class="btn btn-danger"
                                    onclick="openDelete({{ $branch->branch_id }}, '{{ addslashes($branch->name) }}')">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">
                        <div class="empty">
                            <div class="empty-title">No Branches</div>
                            <div style="font-size:12px;margin-top:4px">Add your first branch using the form →</div>
                        </div>
                    </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="add-card">
            <div class="add-card-hdr">
                <div class="add-card-title">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:5px"><path d="M12 5v14M5 12h14"/></svg>
                    New Branch
                </div>
            </div>
            <div class="add-body">
                <form method="POST" action="{{ route('admin.branches.store') }}">
                    @csrf
                    <div class="f-field">
                        <label class="f-lbl">Name <span style="color:#F5911E">*</span></label>
                        <input type="text" name="name" class="f-ctrl" placeholder="e.g. Cairo Branch" value="{{ old('name') }}" required>
                        @error('name')<div style="font-size:10px;color:#DC2626;margin-top:3px">{{ $message }}</div>@enderror
                    </div>
                    <div class="f-field">
                        <label class="f-lbl">Code</label>
                        <input type="text" name="code" class="f-ctrl" placeholder="e.g. CAI" value="{{ old('code') }}">
                        @error('code')<div style="font-size:10px;color:#DC2626;margin-top:3px">{{ $message }}</div>@enderror
                    </div>
                    <div class="f-field">
                        <label class="f-lbl">Address</label>
                        <input type="text" name="address" class="f-ctrl" placeholder="e.g. 12 Tahrir St, Cairo" value="{{ old('address') }}">
                    </div>
                    <div class="f-field">
                        <label class="f-lbl">Phone</label>
                        <input type="text" name="phone" class="f-ctrl" placeholder="e.g. 01012345678" value="{{ old('phone') }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        Create Branch
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<div class="modal-bg" id="editModal">
    <div class="modal">
        <div class="modal-hdr">
            <div class="modal-eyebrow">Admin Action</div>
            <div class="modal-title">Edit Branch</div>
            <div class="modal-sub" id="editBranchName">—</div>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="f-field">
                    <label class="f-lbl">Name <span style="color:#F5911E">*</span></label>
                    <input type="text" name="name" id="editName" class="f-ctrl" required>
                </div>
                <div class="f-field">
                    <label class="f-lbl">Code</label>
                    <input type="text" name="code" id="editCode" class="f-ctrl">
                </div>
                <div class="f-field">
                    <label class="f-lbl">Address</label>
                    <input type="text" name="address" id="editAddress" class="f-ctrl">
                </div>
                <div class="f-field">
                    <label class="f-lbl">Phone</label>
                    <input type="text" name="phone" id="editPhone" class="f-ctrl">
                </div>
                <div style="height:1px;background:rgba(27,79,168,0.08);margin:14px 0"></div>
                <div class="warn-box">
                    <strong>⚠ Password Required</strong><br>
                    Enter your admin password to confirm this action.
                </div>
                <div class="f-field" style="margin:0">
                    <label class="f-lbl">Your Password <span style="color:#F5911E">*</span></label>
                    <div class="pass-eye">
                        <input type="password" name="confirm_password" id="editPass" class="f-ctrl" placeholder="••••••••" required>
                        <button type="button" onclick="togglePass('editPass')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-bg" id="toggleModal">
    <div class="modal">
        <div class="modal-hdr">
            <div class="modal-eyebrow">Admin Action</div>
            <div class="modal-title" id="toggleModalTitle">Toggle Branch</div>
            <div class="modal-sub" id="toggleBranchName">—</div>
        </div>
        <form method="POST" id="toggleForm">
            @csrf @method('PATCH')
            <div class="modal-body">
                <div class="warn-box" id="toggleWarning">—</div>
                <div class="f-field" style="margin:0">
                    <label class="f-lbl">Your Password <span style="color:#F5911E">*</span></label>
                    <div class="pass-eye">
                        <input type="password" name="confirm_password" id="togglePass" class="f-ctrl" placeholder="••••••••" required>
                        <button type="button" onclick="togglePass('togglePass')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-cancel" onclick="closeModal('toggleModal')">Cancel</button>
                <button type="submit" id="toggleSubmitBtn" class="btn btn-primary">Confirm</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-bg" id="deleteModal">
    <div class="modal">
        <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#DC2626,transparent);z-index:1"></div>
        <div class="modal-hdr">
            <div class="modal-eyebrow" style="color:#DC2626">Danger Zone</div>
            <div class="modal-title" style="color:#DC2626">Delete Branch</div>
            <div class="modal-sub" id="deleteBranchName">—</div>
        </div>
        <form method="POST" id="deleteForm">
            @csrf @method('DELETE')
            <div class="modal-body">
                <div class="warn-box">
                    <strong>⚠ This action is irreversible.</strong><br>
                    The branch will be permanently deleted. This can only proceed if no employees or courses are linked.
                </div>
                <div class="f-field" style="margin:0">
                    <label class="f-lbl">Your Password <span style="color:#F5911E">*</span></label>
                    <div class="pass-eye">
                        <input type="password" name="confirm_password" id="deletePass" class="f-ctrl" placeholder="••••••••" required>
                        <button type="button" onclick="togglePass('deletePass')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn btn-danger" style="background:linear-gradient(120deg,#DC2626,#EF4444);color:#fff;border-color:transparent;box-shadow:0 8px 20px rgba(220,38,38,0.26)">Delete Branch</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name, code, address, phone) {
    document.getElementById('editForm').action = `/admin/branches/${id}`;
    document.getElementById('editBranchName').textContent = name;
    document.getElementById('editName').value    = name;
    document.getElementById('editCode').value    = code;
    document.getElementById('editAddress').value = address;
    document.getElementById('editPhone').value   = phone;
    document.getElementById('editPass').value    = '';
    openModal('editModal');
}

function openToggle(id, name, isActive) {
    document.getElementById('toggleForm').action = `/admin/branches/${id}/toggle`;
    document.getElementById('toggleBranchName').textContent = name;
    document.getElementById('toggleModalTitle').textContent = isActive ? 'Deactivate Branch' : 'Activate Branch';
    document.getElementById('toggleWarning').innerHTML = isActive
        ? '<strong>⚠ Deactivating this branch</strong> will not remove any data but will mark it as inactive.'
        : '<strong>✓ Activating this branch</strong> will make it available again across the system.';
    document.getElementById('toggleSubmitBtn').textContent = isActive ? 'Deactivate' : 'Activate';
    document.getElementById('togglePass').value = '';
    openModal('toggleModal');
}

function openDelete(id, name) {
    document.getElementById('deleteForm').action = `/admin/branches/${id}`;
    document.getElementById('deleteBranchName').textContent = name;
    document.getElementById('deletePass').value = '';
    openModal('deleteModal');
}

function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

document.querySelectorAll('.modal-bg').forEach(bg => {
    bg.addEventListener('click', e => { if(e.target === bg) bg.classList.remove('open'); });
});

function togglePass(id) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
@endsection