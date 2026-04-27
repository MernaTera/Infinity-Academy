@extends('admin.layouts.app')
@section('title', 'Private Bundles')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.bnd-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
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

/* Bundle Cards */
.bundles-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;}
@media(max-width:1100px){.bundles-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:700px){.bundles-grid{grid-template-columns:1fr;}}

.bnd-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;position:relative;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.bnd-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(27,79,168,0.1);}
.bnd-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--purple),var(--blue));}
.bnd-card.inactive{opacity:0.6;filter:grayscale(30%);}

.bnd-card-header{padding:22px 24px 16px;text-align:center;position:relative;}
.bnd-hours{font-family:'Bebas Neue',sans-serif;font-size:52px;letter-spacing:3px;color:var(--blue);line-height:1;}
.bnd-hours-label{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--faint);margin-top:2px;}

.bnd-price-tag{display:inline-flex;align-items:baseline;gap:4px;margin-top:12px;padding:8px 20px;background:var(--blue-l);border:1px solid var(--border);border-radius:30px;}
.bnd-price-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--blue);}
.bnd-price-cur{font-size:11px;color:var(--muted);letter-spacing:1px;}

.bnd-stats{display:grid;grid-template-columns:1fr 1fr;border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.bnd-stat{padding:14px 16px;text-align:center;}
.bnd-stat:first-child{border-right:1px solid var(--border);}
.bnd-stat-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:5px;}
.bnd-stat-val{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:var(--text);}

.bnd-card-footer{padding:14px 22px;display:flex;align-items:center;justify-content:space-between;gap:8px;}
.bnd-status{font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;}
.bnd-status.active{background:var(--green-l);color:var(--green);border:1px solid rgba(5,150,105,0.2);}
.bnd-status.inactive{background:var(--red-l);color:var(--red);border:1px solid rgba(220,38,38,0.15);}

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
.form-control{width:100%;padding:9px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;box-sizing:border-box;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}

/* Modal */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(10,20,40,0.45);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;animation:fadeIn 0.2s ease both;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{width:100%;max-width:420px;background:var(--bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 24px 60px rgba(27,79,168,0.15);animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;position:relative;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--purple),var(--blue),transparent);}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:var(--blue);}
.modal-body{padding:22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}

@media(max-width:768px){.bnd-page{padding:18px 14px;}.kpi-grid{grid-template-columns:1fr 1fr;}}
</style>

<div class="bnd-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel — Financial</div>
            <h1 class="page-title">Private Bundles</h1>
        </div>
        <button onclick="document.getElementById('createModal').classList.add('open')" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Bundle</span>
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
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Total Bundles</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--red)"><div class="kpi-label">Inactive</div><div class="kpi-val">{{ $stats['inactive'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--purple)"><div class="kpi-label">Total Enrollments</div><div class="kpi-val">{{ $stats['total_enrollments'] }}</div></div>
    </div>

    {{-- Bundles Grid --}}
    <span class="sec-label">All Bundles</span>

    @if($bundles->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--faint);">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 16px;display:block;opacity:0.4;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        <div style="font-size:13px;">No bundles yet. Create your first one!</div>
    </div>
    @else
    <div class="bundles-grid">
        @foreach($bundles->sortBy('hours') as $bundle)
        <div class="bnd-card {{ !$bundle->is_active ? 'inactive' : '' }}">

            <div class="bnd-card-header">
                @if(!$bundle->is_active)
                <div style="position:absolute;top:12px;right:14px;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.15);padding:2px 8px;border-radius:3px;">Inactive</div>
                @endif
                <div class="bnd-hours">{{ number_format($bundle->hours, 0) }}</div>
                <div class="bnd-hours-label">Hours</div>
                <div style="display:flex;justify-content:center;">
                    <div class="bnd-price-tag">
                        <span class="bnd-price-val">{{ number_format($bundle->price, 0) }}</span>
                        <span class="bnd-price-cur">LE</span>
                    </div>
                </div>
            </div>

            <div class="bnd-stats">
                <div class="bnd-stat">
                    <div class="bnd-stat-label">Price / Hour</div>
                    <div class="bnd-stat-val" style="color:var(--orange);">
                        {{ $bundle->hours > 0 ? number_format($bundle->price / $bundle->hours, 0) : '—' }}
                        <span style="font-size:10px;font-family:'DM Sans',sans-serif;color:var(--faint);"> LE</span>
                    </div>
                </div>
                <div class="bnd-stat">
                    <div class="bnd-stat-label">Enrollments</div>
                    <div class="bnd-stat-val" style="color:var(--purple);">{{ $bundle->enrollments_count }}</div>
                </div>
            </div>

            <div class="bnd-card-footer">
                <div style="font-size:10px;color:var(--faint);">
                    By {{ $bundle->createdBy?->full_name ?? '—' }}<br>
                    {{ $bundle->created_at?->format('d M Y') }}
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end;">
                    <button class="btn-sm btn-edit"
                            onclick="openEdit({{ $bundle->bundle_id }}, {{ $bundle->hours }}, {{ $bundle->price }})">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.bundles.toggle', $bundle->bundle_id) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-sm {{ $bundle->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                            {{ $bundle->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    @if($bundle->enrollments_count === 0)
                    <form method="POST" action="{{ route('admin.bundles.destroy', $bundle->bundle_id) }}" style="display:inline;"
                          onsubmit="return confirm('Delete this bundle permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-danger">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                    @else
                    <span style="font-size:9px;color:var(--faint);align-self:center;" title="Cannot delete — has enrollments">🔒</span>
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
            <div class="modal-title">New Bundle</div>
            <button onclick="document.getElementById('createModal').classList.remove('open')"
                    style="background:none;border:none;cursor:pointer;color:var(--faint);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.bundles.store') }}">
            @csrf
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div class="form-field">
                        <label class="form-label">Hours <span style="color:var(--orange);">*</span></label>
                        <input type="number" name="hours" class="form-control" placeholder="e.g. 20" step="0.5" min="0.5" required
                               oninput="calcPPH('create')">
                        <span style="font-size:10px;color:var(--faint);">Number of teaching hours in this bundle</span>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Price (LE) <span style="color:var(--orange);">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="e.g. 3000" step="0.01" min="0" required
                               oninput="calcPPH('create')">
                    </div>
                    <div id="create_pph" style="display:none;padding:10px 14px;background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);border-radius:4px;font-size:12px;color:var(--purple);">
                        Price per hour: <strong id="create_pph_val">—</strong>
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
            <div class="modal-title">Edit Bundle</div>
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
                        <label class="form-label">Hours <span style="color:var(--orange);">*</span></label>
                        <input type="number" id="edit_hours" name="hours" class="form-control" step="0.5" min="0.5" required
                               oninput="calcPPH('edit')">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Price (LE) <span style="color:var(--orange);">*</span></label>
                        <input type="number" id="edit_price" name="price" class="form-control" step="0.01" min="0" required
                               oninput="calcPPH('edit')">
                    </div>
                    <div id="edit_pph" style="display:none;padding:10px 14px;background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);border-radius:4px;font-size:12px;color:var(--purple);">
                        Price per hour: <strong id="edit_pph_val">—</strong>
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

function openEdit(id, hours, price) {
    document.getElementById('editForm').action = `/admin/bundles/${id}`;
    document.getElementById('edit_hours').value = hours;
    document.getElementById('edit_price').value = price;
    calcPPH('edit');
    document.getElementById('editModal').classList.add('open');
}

function calcPPH(prefix) {
    let h, p;
    if (prefix === 'edit') {
        h = parseFloat(document.getElementById('edit_hours').value || 0);
        p = parseFloat(document.getElementById('edit_price').value || 0);
    } else {
        h = parseFloat(document.querySelector('#createModal [name="hours"]')?.value || 0);
        p = parseFloat(document.querySelector('#createModal [name="price"]')?.value || 0);
    }
    const wrap  = document.getElementById(prefix + '_pph');
    const valEl = document.getElementById(prefix + '_pph_val');
    if (h > 0 && p > 0) {
        wrap.style.display = 'block';
        valEl.textContent  = (p / h).toFixed(2) + ' LE / hr';
    } else {
        wrap.style.display = 'none';
    }
}
</script>

@endsection