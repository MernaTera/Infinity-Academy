@extends('admin.layouts.app')
@section('title', 'Level Packages')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.pkg-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px;}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:6px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

/* Section */
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:16px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);display:block;}

/* Package Cards Grid */
.packages-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;}
@media(max-width:1100px){.packages-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:700px){.packages-grid{grid-template-columns:1fr;}}

.pkg-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;position:relative;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.pkg-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(27,79,168,0.1);}
.pkg-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange),var(--blue));}
.pkg-card.inactive{opacity:0.65;filter:grayscale(30%);}

.pkg-card-header{padding:20px 22px 16px;}
.pkg-course{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--orange);margin-bottom:6px;}
.pkg-name{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--blue);line-height:1;margin-bottom:12px;}

.pkg-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:0 22px 16px;}
.pkg-stat{background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:12px 14px;}
.pkg-stat-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:5px;}
.pkg-stat-val{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;color:var(--text);line-height:1;}
.pkg-stat-sub{font-size:9px;color:var(--faint);margin-top:2px;}

.pkg-card-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;}
.pkg-status{font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;}
.pkg-status.active{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.2);}
.pkg-status.inactive{background:var(--red-l);color:var(--red);border:1px solid rgba(220,38,38,0.15);}

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

/* Filter bar */
.filter-bar{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.filter-btn{padding:6px 16px;border-radius:4px;font-size:10px;letter-spacing:2px;text-transform:uppercase;border:1px solid var(--border);background:var(--card);color:var(--muted);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;}
.filter-btn.active,.filter-btn:hover{border-color:var(--blue);color:var(--blue);background:var(--blue-l);}

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
.modal-box{width:100%;max-width:500px;background:var(--bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 24px 60px rgba(27,79,168,0.15);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--blue);}
.modal-body{padding:22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}

@media(max-width:768px){.pkg-page{padding:18px 14px;}.kpi-grid{grid-template-columns:1fr 1fr;}}
</style>

<div class="pkg-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel — Financial</div>
            <h1 class="page-title">Level Packages</h1>
        </div>
        <button onclick="document.getElementById('createModal').classList.add('open')" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Package</span>
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
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Total Packages</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--red)"><div class="kpi-label">Inactive</div><div class="kpi-val">{{ $stats['inactive'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--orange)"><div class="kpi-label">Courses Covered</div><div class="kpi-val">{{ $stats['courses'] }}</div></div>
    </div>

    {{-- Filter by course --}}
    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterCourse('all', this)">All Courses</button>
        @foreach($courses as $c)
        <button class="filter-btn" onclick="filterCourse({{ $c->course_template_id }}, this)">{{ $c->name }}</button>
        @endforeach
    </div>

    {{-- Packages Grid --}}
    <span class="sec-label">All Packages</span>

    @if($packages->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--faint);">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 16px;display:block;opacity:0.4;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        <div style="font-size:13px;">No packages yet. Create your first one!</div>
    </div>
    @else
    <div class="packages-grid" id="packagesGrid">
        @foreach($packages as $pkg)
        @php
            $pricePerLevel = $pkg->levels_count > 0 ? round($pkg->package_price / $pkg->levels_count, 2) : 0;
        @endphp
        <div class="pkg-card {{ !$pkg->is_active ? 'inactive' : '' }}" data-course="{{ $pkg->course_template_id }}">
            <div class="pkg-card-header">
                <div class="pkg-course">{{ $pkg->courseTemplate?->name ?? '—' }}</div>
                <div class="pkg-name">{{ $pkg->name }}</div>
            </div>

            <div class="pkg-stats">
                <div class="pkg-stat">
                    <div class="pkg-stat-label">Package Price</div>
                    <div class="pkg-stat-val" style="color:var(--blue);">{{ number_format($pkg->package_price) }}</div>
                    <div class="pkg-stat-sub">LE total</div>
                </div>
                <div class="pkg-stat">
                    <div class="pkg-stat-label">Levels</div>
                    <div class="pkg-stat-val" style="color:var(--orange);">{{ $pkg->levels_count }}</div>
                    <div class="pkg-stat-sub">included</div>
                </div>
                <div class="pkg-stat">
                    <div class="pkg-stat-label">Per Level</div>
                    <div class="pkg-stat-val" style="color:var(--green);">{{ number_format($pricePerLevel) }}</div>
                    <div class="pkg-stat-sub">LE / level</div>
                </div>
                <div class="pkg-stat">
                    <div class="pkg-stat-label">Created By</div>
                    <div style="font-size:12px;font-weight:500;color:var(--text);margin-top:4px;">{{ $pkg->createdBy?->full_name ?? '—' }}</div>
                    <div class="pkg-stat-sub">{{ $pkg->created_at?->format('d M Y') }}</div>
                </div>
            </div>

            <div class="pkg-card-footer">
                <span class="pkg-status {{ $pkg->is_active ? 'active' : 'inactive' }}">
                    {{ $pkg->is_active ? 'Active' : 'Inactive' }}
                </span>
                <div style="display:flex;gap:6px;">
                    <button class="btn-sm btn-edit"
                            onclick="openEdit({{ $pkg->package_id }}, {{ $pkg->course_template_id }}, '{{ addslashes($pkg->name) }}', {{ $pkg->levels_count }}, {{ $pkg->package_price }})">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.packages.toggle', $pkg->package_id) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-sm {{ $pkg->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                            {{ $pkg->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.packages.destroy', $pkg->package_id) }}" style="display:inline;"
                          onsubmit="return confirm('Delete this package permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-danger">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            Delete
                        </button>
                    </form>
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
            <div class="modal-title">New Package</div>
            <button onclick="document.getElementById('createModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.packages.store') }}">
            @csrf
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div class="form-field">
                        <label class="form-label">Course <span style="color:var(--orange);">*</span></label>
                        <select name="course_template_id" class="form-control" required>
                            <option value="">— Select Course —</option>
                            @foreach($courses as $c)
                            <option value="{{ $c->course_template_id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label class="form-label">Package Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. 3 Levels Package" required>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-field">
                            <label class="form-label">Levels Count <span style="color:var(--orange);">*</span></label>
                            <input type="number" name="levels_count" class="form-control" placeholder="e.g. 3" min="1" required
                                   oninput="calcPerLevel('create')">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Package Price (LE) <span style="color:var(--orange);">*</span></label>
                            <input type="number" name="package_price" class="form-control" placeholder="e.g. 4500" step="0.01" min="0" required
                                   oninput="calcPerLevel('create')">
                        </div>
                    </div>

                    <div id="create_per_level" style="display:none;padding:10px 14px;background:var(--blue-l);border:1px solid var(--border);border-radius:4px;font-size:12px;color:var(--blue);">
                        Price per level: <strong id="create_per_level_val">—</strong>
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
            <div class="modal-title">Edit Package</div>
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
                        <label class="form-label">Course <span style="color:var(--orange);">*</span></label>
                        <select name="course_template_id" id="edit_course" class="form-control" required>
                            <option value="">— Select Course —</option>
                            @foreach($courses as $c)
                            <option value="{{ $c->course_template_id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label class="form-label">Package Name <span style="color:var(--orange);">*</span></label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="form-field">
                            <label class="form-label">Levels Count <span style="color:var(--orange);">*</span></label>
                            <input type="number" id="edit_levels" name="levels_count" class="form-control" min="1" required
                                   oninput="calcPerLevel('edit')">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Package Price (LE) <span style="color:var(--orange);">*</span></label>
                            <input type="number" id="edit_price" name="package_price" class="form-control" step="0.01" min="0" required
                                   oninput="calcPerLevel('edit')">
                        </div>
                    </div>

                    <div id="edit_per_level" style="display:none;padding:10px 14px;background:var(--blue-l);border:1px solid var(--border);border-radius:4px;font-size:12px;color:var(--blue);">
                        Price per level: <strong id="edit_per_level_val">—</strong>
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
// Close modals
['createModal','editModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') ['createModal','editModal'].forEach(id => document.getElementById(id).classList.remove('open'));
});

// Open edit
function openEdit(id, courseId, name, levels, price) {
    document.getElementById('editForm').action = `/admin/packages/${id}`;
    document.getElementById('edit_course').value = courseId;
    document.getElementById('edit_name').value   = name;
    document.getElementById('edit_levels').value = levels;
    document.getElementById('edit_price').value  = price;
    calcPerLevel('edit');
    document.getElementById('editModal').classList.add('open');
}

// Per level calc
function calcPerLevel(prefix) {
    const levels = parseFloat(document.getElementById(prefix === 'create' ? document.querySelector('#createModal [name="levels_count"]')?.value : 'edit_levels').value || 0);
    const price  = parseFloat(document.getElementById(prefix === 'create' ? document.querySelector('#createModal [name="package_price"]')?.value : 'edit_price').value || 0);

    // fix: get create modal inputs differently
    let l, p;
    if (prefix === 'edit') {
        l = parseFloat(document.getElementById('edit_levels').value || 0);
        p = parseFloat(document.getElementById('edit_price').value  || 0);
    } else {
        l = parseFloat(document.querySelector('#createModal [name="levels_count"]')?.value || 0);
        p = parseFloat(document.querySelector('#createModal [name="package_price"]')?.value || 0);
    }

    const wrap    = document.getElementById(prefix + '_per_level');
    const valEl   = document.getElementById(prefix + '_per_level_val');
    if (l > 0 && p > 0) {
        wrap.style.display = 'block';
        valEl.textContent  = (p / l).toFixed(2) + ' LE / level';
    } else {
        wrap.style.display = 'none';
    }
}

// Filter by course
function filterCourse(courseId, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.pkg-card').forEach(card => {
        if (courseId === 'all' || card.dataset.course == courseId) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

@endsection