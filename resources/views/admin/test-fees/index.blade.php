@extends('admin.layouts.app')
@section('title', 'Test Fee Settings')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--blue-l:rgba(27,79,168,0.10);--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--green-l:rgba(5,150,105,0.10);--red:#DC2626;--red-l:rgba(220,38,38,0.08);--border:rgba(27,79,168,0.08);--bg:#F8F6F2;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;}
.tf-page{background:var(--bg);min-height:100vh;padding:36px 28px;font-family:'DM Sans',sans-serif;color:var(--text);}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none;}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%;}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);font-weight:600;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0 0 24px;}
.card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;margin-bottom:24px;box-shadow:var(--glass-sh);}
.card-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:10px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid var(--border);}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.03);}
.tbl td{padding:12px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}
.field-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:5px;font-weight:600;}
.field-input{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;}
.field-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:600;border:1px solid;background:rgba(255,255,255,0.5);transition:all 0.2s;}
.btn-primary{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;border-color:transparent;box-shadow:0 10px 24px rgba(27,79,168,0.28);}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36);}
.btn-edit{color:var(--blue);border-color:rgba(27,79,168,0.22);}
.btn-edit:hover{background:var(--blue-l);}
.btn-cancel{color:var(--muted);border-color:rgba(27,79,168,0.15);background:rgba(255,255,255,0.6);}
.btn-cancel:hover{color:var(--blue);border-color:var(--blue);}
.btn-toggle-on{color:var(--green-dk);border-color:rgba(5,150,105,0.25);}
.btn-toggle-on:hover{background:var(--green-l);}
.btn-toggle-off{color:var(--orange-dk);border-color:rgba(245,145,30,0.3);}
.btn-toggle-off:hover{background:rgba(245,145,30,0.08);}
.btn-danger{color:var(--red);border-color:rgba(220,38,38,0.22);}
.btn-danger:hover{background:var(--red-l);}
.badge-active{background:var(--green-l);color:var(--green-dk);border:1px solid rgba(5,150,105,0.2);font-size:9px;padding:3px 9px;border-radius:20px;letter-spacing:1px;text-transform:uppercase;font-weight:600;}
.badge-inactive{background:rgba(122,138,154,0.10);color:var(--muted);border:1px solid rgba(122,138,154,0.2);font-size:9px;padding:3px 9px;border-radius:20px;letter-spacing:1px;text-transform:uppercase;font-weight:600;}
.flash-success{background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green-dk);padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:13px;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.tf-modal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.tf-modal-box{width:min(440px,100%);background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18);}
.tf-modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--blue),transparent);}
.tf-modal-head{padding:16px 22px;border-bottom:1px solid var(--border);}
.tf-modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:var(--text);}
.tf-modal-foot{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}
@media(max-width:768px){.tf-page{padding:18px 14px;}}
</style>

<div class="tf-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="page-eyebrow">Admin Panel — Financial</div>
    <h1 class="page-title">Placement Test Fees</h1>

    @if(session('success'))
    <div class="flash-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header"><div class="card-title">Add New Test Fee</div></div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('admin.test-fees.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 160px auto;gap:12px;align-items:end;">
                    <div>
                        <label class="field-label">Name</label>
                        <input type="text" name="name" class="field-input" placeholder="e.g. Standard Placement Test" required>
                    </div>
                    <div>
                        <label class="field-label">Fee (LE)</label>
                        <input type="number" name="fee" class="field-input" placeholder="200" step="0.01" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Current Test Fees</div></div>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Fee (LE)</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($fees as $fee)
                <tr>
                    <td style="font-weight:500;color:var(--text);">{{ $fee->name }}</td>
                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--blue);letter-spacing:1px;">
                            {{ number_format($fee->fee, 2) }}
                        </span>
                        <span style="font-size:10px;color:var(--faint);"> LE</span>
                    </td>
                    <td>
                        <span class="{{ $fee->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $fee->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <button onclick="openEdit({{ $fee->id }}, '{{ addslashes($fee->name) }}', {{ $fee->fee }}, {{ $fee->is_active ? 1 : 0 }})"
                                class="btn btn-edit">Edit</button>
                            <form method="POST" action="{{ route('admin.test-fees.update', $fee->id) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="name" value="{{ $fee->name }}">
                                <input type="hidden" name="fee" value="{{ $fee->fee }}">
                                <input type="hidden" name="is_active" value="{{ $fee->is_active ? 0 : 1 }}">
                                <button type="submit" class="btn {{ $fee->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">{{ $fee->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.test-fees.destroy', $fee->id) }}" data-name="{{ $fee->name }}" onsubmit="return askDeleteFee(this)">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--faint);font-size:12px;">No test fees configured yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="tf-modal">
    <div class="tf-modal-box">
        <div class="tf-modal-head">
            <div class="tf-modal-title">Edit Test Fee</div>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PATCH')
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label class="field-label">Name</label>
                    <input type="text" name="name" id="edit_name" class="field-input" required>
                </div>
                <div>
                    <label class="field-label">Fee (LE)</label>
                    <input type="number" name="fee" id="edit_fee" class="field-input" step="0.01" min="0" required>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" id="edit_active" value="1" style="accent-color:var(--blue);width:14px;height:14px;">
                    <label for="edit_active" style="font-size:12px;color:var(--muted);cursor:pointer;">Active</label>
                </div>
            </div>
            <div class="tf-modal-foot">
                <button type="button" onclick="closeEdit()" class="btn btn-cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteFeeModal" style="display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px;font-family:'DM Sans',sans-serif">
    <div style="width:100%;max-width:420px;background:rgba(255,255,255,0.78);-webkit-backdrop-filter:blur(26px) saturate(170%);backdrop-filter:blur(26px) saturate(170%);border:1px solid rgba(255,255,255,0.7);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 24px 60px rgba(23,45,90,0.18)">
        <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#DC2626,transparent)"></div>
        <div style="padding:26px 24px 8px;text-align:center">
            <div style="width:52px;height:52px;border-radius:16px;background:rgba(220,38,38,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </div>
            <div style="font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#DC2626;margin-bottom:3px;font-weight:600">Delete Test Fee</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:#16233F" id="deleteFeeName"></div>
            <div style="font-size:12px;color:#5A6A85;margin-top:8px;line-height:1.5">This fee will be permanently deleted. This action can't be undone.</div>
        </div>
        <div style="padding:18px 24px 22px;display:flex;gap:10px;justify-content:center">
            <button type="button" onclick="closeDeleteFee()" style="padding:9px 20px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:11px;color:#5A6A85;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer">Cancel</button>
            <button type="button" onclick="confirmDeleteFee()" style="padding:10px 24px;background:linear-gradient(120deg,#DC2626,#EF4444);border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer;box-shadow:0 10px 24px rgba(220,38,38,0.28)">Delete</button>
        </div>
    </div>
</div>

<script>
function openEdit(id, name, fee, isActive) {
    document.getElementById('editForm').action = `/admin/test-fees/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_fee').value  = fee;
    document.getElementById('edit_active').checked = isActive == 1;
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}
function closeEdit() {
    document.getElementById('editModal').style.display = 'none';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
var _pendingFeeDelete = null;
function askDeleteFee(form) {
    _pendingFeeDelete = form;
    document.getElementById('deleteFeeName').textContent = form.dataset.name || '';
    document.getElementById('deleteFeeModal').style.display = 'flex';
    return false;
}
function closeDeleteFee() {
    _pendingFeeDelete = null;
    document.getElementById('deleteFeeModal').style.display = 'none';
}
function confirmDeleteFee() {
    if (_pendingFeeDelete) _pendingFeeDelete.submit();
}
document.getElementById('deleteFeeModal').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteFee();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeDeleteFee(); closeEdit(); }
});
</script>
@endsection