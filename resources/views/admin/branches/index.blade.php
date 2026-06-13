@extends('admin.layouts.app')
@section('title', 'Branches')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
*{box-sizing:border-box}
.br-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.br-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.br-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:#1B4FA8;margin:0 0 24px}

.alert{padding:12px 16px;border-radius:4px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px}
.alert-success{background:rgba(5,150,105,0.07);border:1px solid rgba(5,150,105,0.2);color:#059669}
.alert-error  {background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.18);color:#DC2626}

/* Layout */
.br-layout{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}

/* Table card */
.br-card{background:#fff;border:1px solid rgba(27,79,168,0.09);border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(27,79,168,0.05)}
.br-card-hdr{padding:14px 20px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;align-items:center;justify-content:space-between;background:rgba(27,79,168,0.01)}
.br-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1A2A4A}

/* Table */
.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:10px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;text-align:left;font-weight:600;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:13px 16px;font-size:13px;color:#4A5A7A;vertical-align:middle}

/* Status */
.status-pill{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600}
.pill-on {background:rgba(5,150,105,0.07);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.pill-off{background:rgba(220,38,38,0.06);color:#DC2626;border:1px solid rgba(220,38,38,0.15)}

/* Count badge */
.count-badge{display:inline-flex;align-items:center;gap:3px;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.1);border-radius:4px;padding:2px 8px;font-size:10px;color:#5A7AB8}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:4px;font-family:'DM Sans',sans-serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all 0.18s;border:1px solid;font-weight:500}
.btn-edit   {color:#1B4FA8;border-color:rgba(27,79,168,0.25);background:transparent}
.btn-edit:hover{background:rgba(27,79,168,0.07)}
.btn-danger {color:#DC2626;border-color:rgba(220,38,38,0.2);background:transparent}
.btn-danger:hover{background:rgba(220,38,38,0.06)}
.btn-warn   {color:#C47010;border-color:rgba(245,145,30,0.25);background:transparent}
.btn-warn:hover{background:rgba(245,145,30,0.07)}
.btn-primary{background:#1B4FA8;color:#fff;border-color:#1B4FA8}
.btn-primary:hover{background:#1645a0}
.btn-success{background:#059669;color:#fff;border-color:#059669}
.btn-success:hover{background:#047857}
.btn-cancel {background:transparent;color:#7A8A9A;border-color:rgba(27,79,168,0.12)}
.btn-cancel:hover{color:#1B4FA8}

/* Add form */
.add-card{background:#fff;border:1px solid rgba(27,79,168,0.09);border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(27,79,168,0.05);position:sticky;top:20px}
.add-card-hdr{padding:14px 20px;border-bottom:1px solid rgba(27,79,168,0.07);background:rgba(27,79,168,0.01)}
.add-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1A2A4A}
.add-body{padding:20px}
.f-field{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}
.f-lbl{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:#7A8A9A;font-weight:500}
.f-ctrl{padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;width:100%}
.f-ctrl:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

/* Modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.5);backdrop-filter:blur(4px);z-index:100;align-items:center;justify-content:center;padding:20px}
.modal-bg.open{display:flex}
.modal{width:100%;max-width:420px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.2)}
.modal::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.modal-hdr{padding:16px 20px;border-bottom:1px solid rgba(27,79,168,0.07)}
.modal-eyebrow{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:3px}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:#1B4FA8}
.modal-sub{font-size:11px;color:#7A8A9A;margin-top:3px}
.modal-body{padding:18px 20px}
.modal-foot{padding:12px 20px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:8px;justify-content:flex-end}

/* Warning box */
.warn-box{background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.15);border-left:3px solid #DC2626;border-radius:4px;padding:10px 12px;font-size:11px;color:#DC2626;margin-bottom:14px;line-height:1.5}
.pass-eye{position:relative}
.pass-eye .f-ctrl{padding-right:36px}
.pass-eye button{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:none;background:transparent;cursor:pointer;color:#AAB8C8;padding:2px}

/* Empty */
.empty{padding:40px;text-align:center;color:#AAB8C8}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px}

@media(max-width:900px){.br-layout{grid-template-columns:1fr}}
@media(max-width:480px){.br-page{padding:18px 14px}}
</style>

<div class="br-page">
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

        {{-- ══ TABLE ══ --}}
        <div class="br-card">
            <div class="br-card-hdr">
                <div class="br-card-title">All Branches</div>
                <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#AAB8C8;letter-spacing:1px">{{ $branches->count() }}</span>
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
                            <div style="font-weight:600;color:#1A2A4A">{{ $branch->name }}</div>
                            @if($branch->address)
                            <div style="font-size:11px;color:#AAB8C8;margin-top:2px">{{ $branch->address }}</div>
                            @endif
                        </td>
                        <td>
                            @if($branch->code)
                            <span style="font-family:monospace;font-size:12px;background:rgba(27,79,168,0.05);padding:2px 8px;border-radius:3px;color:#1B4FA8">
                                {{ $branch->code }}
                            </span>
                            @else
                            <span style="color:#AAB8C8">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;font-family:monospace;color:#7A8A9A">
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
                                {{-- Edit --}}
                                <button class="btn btn-edit"
                                    onclick="openEdit({{ $branch->branch_id }}, '{{ addslashes($branch->name) }}', '{{ addslashes($branch->code ?? '') }}', '{{ addslashes($branch->address ?? '') }}', '{{ addslashes($branch->phone ?? '') }}')">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </button>

                                {{-- Toggle --}}
                                <button class="btn {{ $branch->is_active ? 'btn-warn' : 'btn-success' }}"
                                    onclick="openToggle({{ $branch->branch_id }}, '{{ addslashes($branch->name) }}', {{ $branch->is_active ? 'true' : 'false' }})">
                                    {{ $branch->is_active ? 'Deactivate' : 'Activate' }}
                                </button>

                                {{-- Delete --}}
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

        {{-- ══ ADD FORM ══ --}}
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

{{-- ══ EDIT MODAL ══ --}}
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
                <div style="height:1px;background:rgba(27,79,168,0.07);margin:14px 0"></div>
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

{{-- ══ TOGGLE MODAL ══ --}}
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

{{-- ══ DELETE MODAL ══ --}}
<div class="modal-bg" id="deleteModal">
    <div class="modal">
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
                <button type="submit" class="btn btn-danger" style="background:#DC2626;color:#fff;border-color:#DC2626">Delete Branch</button>
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

// Close on backdrop click
document.querySelectorAll('.modal-bg').forEach(bg => {
    bg.addEventListener('click', e => { if(e.target === bg) bg.classList.remove('open'); });
});

function togglePass(id) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
@endsection