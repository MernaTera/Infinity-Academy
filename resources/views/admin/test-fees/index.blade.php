@extends('admin.layouts.app')
@section('title', 'Test Fee Settings')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.tf-page{background:#F8F6F2;min-height:100vh;padding:36px 28px;font-family:'DM Sans',sans-serif;color:#1A2A4A;}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0 0 24px;}
.card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;margin-bottom:24px;}
.card-header{padding:14px 20px;border-bottom:1px solid rgba(27,79,168,0.07);background:rgba(27,79,168,0.01);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8;}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:10px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;text-align:left;border-bottom:1px solid rgba(27,79,168,0.07);}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:12px 16px;font-size:13px;color:#4A5A7A;vertical-align:middle;}
.field-input{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;outline:none;}
.field-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:4px;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;border:1px solid;transition:all 0.2s;}
.btn-primary{background:#1B4FA8;color:#fff;border-color:#1B4FA8;}
.btn-primary:hover{background:#2D6FDB;}
.btn-danger{color:#DC2626;border-color:rgba(220,38,38,0.2);background:transparent;}
.btn-danger:hover{background:rgba(220,38,38,0.05);}
.badge-active{background:rgba(5,150,105,0.08);color:#059669;border:1px solid rgba(5,150,105,0.2);font-size:9px;padding:2px 8px;border-radius:3px;letter-spacing:1px;text-transform:uppercase;}
.badge-inactive{background:rgba(122,138,154,0.08);color:#7A8A9A;border:1px solid rgba(122,138,154,0.2);font-size:9px;padding:2px 8px;border-radius:3px;letter-spacing:1px;text-transform:uppercase;}
</style>

<div class="tf-page">
    <div class="page-eyebrow">Admin Panel — Financial</div>
    <h1 class="page-title">Placement Test Fees</h1>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Add New --}}
    <div class="card">
        <div class="card-header"><div class="card-title">Add New Test Fee</div></div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('admin.test-fees.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 160px auto;gap:12px;align-items:end;">
                    <div>
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;display:block;margin-bottom:5px;">Name</label>
                        <input type="text" name="name" class="field-input" placeholder="e.g. Standard Placement Test" required>
                    </div>
                    <div>
                        <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;display:block;margin-bottom:5px;">Fee (LE)</label>
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

    {{-- List --}}
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
                    <td style="font-weight:500;color:#1A2A4A;">{{ $fee->name }}</td>
                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px;">
                            {{ number_format($fee->fee, 2) }}
                        </span>
                        <span style="font-size:10px;color:#AAB8C8;"> LE</span>
                    </td>
                    <td>
                        <span class="{{ $fee->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $fee->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:8px;align-items:center;">
                            {{-- Edit inline --}}
                            <button onclick="openEdit({{ $fee->id }}, '{{ addslashes($fee->name) }}', {{ $fee->fee }}, {{ $fee->is_active ? 1 : 0 }})"
                                class="btn" style="color:#1B4FA8;border-color:rgba(27,79,168,0.2);">Edit</button>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.test-fees.destroy', $fee->id) }}" onsubmit="return confirm('Delete this fee?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:32px;color:#AAB8C8;font-size:12px;">No test fees configured yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(5,10,25,0.6);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:8px;width:min(440px,100%);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="padding:16px 22px;border-bottom:1px solid rgba(27,79,168,0.08);background:rgba(27,79,168,0.02);">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1B4FA8;">Edit Test Fee</div>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PATCH')
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;display:block;margin-bottom:5px;">Name</label>
                    <input type="text" name="name" id="edit_name" class="field-input" required>
                </div>
                <div>
                    <label style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;display:block;margin-bottom:5px;">Fee (LE)</label>
                    <input type="number" name="fee" id="edit_fee" class="field-input" step="0.01" min="0" required>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" id="edit_active" value="1" style="accent-color:#1B4FA8;width:14px;height:14px;">
                    <label for="edit_active" style="font-size:12px;color:#4A5A7A;cursor:pointer;">Active</label>
                </div>
            </div>
            <div style="padding:14px 22px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeEdit()" class="btn" style="color:#7A8A9A;border-color:rgba(27,79,168,0.15);">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
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
</script>
@endsection