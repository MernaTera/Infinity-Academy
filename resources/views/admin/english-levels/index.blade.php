@extends('admin.layouts.app')
@section('title', 'English Levels')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.el-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:16px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);display:block;}

/* Levels grid */
.levels-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;}
@media(max-width:1000px){.levels-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:700px){.levels-grid{grid-template-columns:1fr 1fr;}}

.lv-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;position:relative;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(27,79,168,0.04);text-align:center;}
.lv-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(27,79,168,0.1);}
.lv-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange),var(--blue));}

.lv-rank{font-family:'Bebas Neue',sans-serif;font-size:52px;letter-spacing:2px;color:var(--blue);line-height:1;padding:22px 20px 4px;}
.lv-name{font-size:13px;font-weight:600;color:var(--text);padding:0 16px 6px;letter-spacing:0.5px;}
.lv-teachers{font-size:10px;color:var(--faint);padding:0 16px 16px;}

.lv-footer{padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:6px;justify-content:center;}

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

/* Table */
.tbl-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:24px;}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid var(--border);}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:13px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

/* Form */
.form-field{display:flex;flex-direction:column;gap:5px;}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
.form-control{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;box-sizing:border-box;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}

/* Modal */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.45);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:400px;background:var(--bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 24px 60px rgba(27,79,168,0.15);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--blue);}
.modal-body{padding:22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}

@media(max-width:768px){.el-page{padding:18px 14px;}}
</style>

<div class="el-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel — Academic</div>
            <h1 class="page-title">English Levels</h1>
        </div>
        <button onclick="document.getElementById('createModal').classList.add('open')" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Level</span>
        </button>
    </div>

    @if(session('success'))
    <div style="background:var(--green-l);border:1px solid rgba(5,150,105,0.2);color:var(--green);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('error') }}</div>
    @endif

    {{-- Cards --}}
    <span class="sec-label">All Levels — sorted by rank</span>

    @if($levels->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--faint);">
        <div style="font-size:13px;">No levels yet. Create your first one!</div>
    </div>
    @else

    <div class="levels-grid">
        @foreach($levels as $lv)
        <div class="lv-card">
            <div class="lv-rank">{{ $lv->level_rank }}</div>
            <div class="lv-name">{{ $lv->level_name }}</div>
            <div class="lv-teachers">
                {{ $lv->teachers_count }} teacher{{ $lv->teachers_count != 1 ? 's' : '' }}
            </div>
            <div class="lv-footer">
                <button class="btn-sm btn-edit"
                        onclick="openEdit({{ $lv->english_level_id }}, '{{ addslashes($lv->level_name) }}', {{ $lv->level_rank }})">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </button>
                @if($lv->teachers_count === 0)
                <form method="POST" action="{{ route('admin.english-levels.destroy', $lv->english_level_id) }}" style="display:inline;"
                      onsubmit="return confirm('Delete {{ $lv->level_name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-danger">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                        Delete
                    </button>
                </form>
                @else
                <span style="font-size:9px;color:var(--faint);align-self:center;" title="Has teachers assigned">🔒</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Table view --}}
    <span class="sec-label">Detail View</span>
    <div class="tbl-card">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Level Name</th>
                    <th>Teachers</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($levels as $lv)
                <tr>
                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;color:var(--blue);">
                            {{ $lv->level_rank }}
                        </span>
                    </td>
                    <td style="font-weight:600;color:var(--text);font-size:14px;">{{ $lv->level_name }}</td>
                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--purple);">
                            {{ $lv->teachers_count }}
                        </span>
                        <span style="font-size:11px;color:var(--faint);margin-left:2px;">teachers</span>
                    </td>
                    <td style="font-size:11px;color:var(--faint);">{{ $lv->created_at?->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="btn-sm btn-edit"
                                    onclick="openEdit({{ $lv->english_level_id }}, '{{ addslashes($lv->level_name) }}', {{ $lv->level_rank }})">
                                Edit
                            </button>
                            @if($lv->teachers_count === 0)
                            <form method="POST" action="{{ route('admin.english-levels.destroy', $lv->english_level_id) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete {{ $lv->level_name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- ── CREATE MODAL ── --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">New English Level</div>
            <button onclick="document.getElementById('createModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.english-levels.store') }}">
            @csrf
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div class="form-field">
                        <label class="form-label">Level Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" name="level_name" class="form-control" placeholder="e.g. A1, B2, C1" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Rank <span style="color:var(--orange);">*</span></label>
                        <input type="number" name="level_rank" class="form-control" placeholder="e.g. 1" min="1" required>
                        <span style="font-size:10px;color:var(--faint);">Lower rank = lower level (1 = beginner)</span>
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
            <div class="modal-title">Edit Level</div>
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
                        <label class="form-label">Level Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" id="edit_name" name="level_name" class="form-control" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Rank <span style="color:var(--orange);">*</span></label>
                        <input type="number" id="edit_rank" name="level_rank" class="form-control" min="1" required>
                        <span style="font-size:10px;color:var(--faint);">Lower rank = lower level</span>
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
                    Save
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

function openEdit(id, name, rank) {
    document.getElementById('editForm').action  = `/admin/english-levels/${id}`;
    document.getElementById('edit_name').value  = name;
    document.getElementById('edit_rank').value  = rank;
    document.getElementById('editModal').classList.add('open');
}
</script>

@endsection