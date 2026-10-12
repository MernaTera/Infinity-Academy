@extends('admin.layouts.app')
@section('title', 'Contract Types')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
*{box-sizing:border-box}
.ct-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.ct-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.ct-title{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--text);margin:0 0 28px}

.alert{padding:12px 16px;border-radius:12px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.alert-success{background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:var(--green-dk)}
.alert-error  {background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);color:var(--red)}

.ct-layout{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start}

.ct-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh)}
.ct-card-header{padding:16px 20px;border-bottom:1px solid rgba(27,79,168,0.08);display:flex;align-items:center;justify-content:space-between}
.ct-card-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text)}
.ct-card-count{font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--blue);letter-spacing:1px;background:rgba(27,79,168,0.08);border-radius:10px;padding:1px 10px}

.ct-row{padding:16px 20px;border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s}
.ct-row:last-child{border-bottom:none}
.ct-row:hover{background:rgba(27,79,168,0.03)}
.ct-row-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
.ct-name{font-size:14px;font-weight:600;color:var(--text)}
.ct-meta{font-size:11px;color:var(--faint);margin-top:3px}
.ct-actions{display:flex;gap:6px;flex-shrink:0}

.ct-edit-form{display:none;margin-top:12px;padding-top:12px;border-top:1px solid rgba(27,79,168,0.08)}
.ct-edit-form.open{display:block;animation:panelIn .3s ease both}
@keyframes panelIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.edit-grid{display:grid;grid-template-columns:1fr 140px auto;gap:10px;align-items:flex-end}

.status-pill{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600}
.status-active  {background:rgba(5,150,105,0.10);color:var(--green-dk);border:1px solid rgba(5,150,105,0.2)}
.status-inactive{background:rgba(220,38,38,0.08);color:var(--red);border:1px solid rgba(220,38,38,0.18)}

.sessions-badge{display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,0.55);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border:1px solid rgba(27,79,168,0.14);border-radius:12px;padding:4px 12px}
.sessions-val{font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--blue);letter-spacing:1px;line-height:1}
.sessions-lbl{font-size:9px;color:var(--faint);letter-spacing:1px;text-transform:uppercase}

.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:9px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;border:1px solid;font-weight:600;background:rgba(255,255,255,0.5)}
.btn-primary{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;border-color:transparent;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36)}
.btn-edit{color:var(--blue);border-color:rgba(27,79,168,0.22)}
.btn-edit:hover{background:rgba(27,79,168,0.08)}
.btn-toggle-on {color:var(--red);border-color:rgba(220,38,38,0.22)}
.btn-toggle-on:hover{background:rgba(220,38,38,0.07)}
.btn-toggle-off{color:var(--green-dk);border-color:rgba(5,150,105,0.25)}
.btn-toggle-off:hover{background:rgba(5,150,105,0.08)}
.btn-save{background:linear-gradient(120deg,var(--blue),var(--blue-2));color:#fff;border-color:transparent;box-shadow:0 6px 16px rgba(27,79,168,0.25)}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(27,79,168,0.32)}
.btn-cancel{background:rgba(255,255,255,0.6);color:var(--muted);border-color:rgba(27,79,168,0.15)}
.btn-cancel:hover{color:var(--blue);border-color:var(--blue)}

.add-form-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh);position:sticky;top:20px}
.add-form-header{padding:16px 20px;border-bottom:1px solid rgba(27,79,168,0.08)}
.add-form-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--text)}
.add-form-body{padding:20px}
.form-field{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
.form-lbl{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:600}
.form-ctrl{padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;width:100%}
.form-ctrl:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.form-hint{font-size:10px;color:var(--faint);margin-top:3px}

.empty{padding:40px;text-align:center;color:var(--faint)}
.empty svg{display:block;margin:0 auto 12px;opacity:0.3}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:3px;margin-bottom:4px;color:var(--muted)}
.empty-sub{font-size:12px}

@media(max-width:900px){.ct-layout{grid-template-columns:1fr}}
@media(max-width:480px){.ct-page{padding:18px 14px}.edit-grid{grid-template-columns:1fr;gap:8px}}
</style>

<div class="ct-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="ct-eyebrow">Admin Panel</div>
    <h1 class="ct-title">Contract Types</h1>

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

    <div class="ct-layout">

        <div class="ct-card">
            <div class="ct-card-header">
                <div class="ct-card-title">Defined Contract Types</div>
                <span class="ct-card-count">{{ $contractTypes->count() }}</span>
            </div>

            @forelse($contractTypes as $ct)
            <div class="ct-row">
                <div class="ct-row-top">
                    <div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="ct-name">{{ $ct->name }}</div>
                            <span class="status-pill {{ $ct->is_active ? 'status-active' : 'status-inactive' }}">
                                <span style="width:5px;height:5px;border-radius:50%;background:currentColor;display:inline-block"></span>
                                {{ $ct->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="ct-meta">
                            Created by {{ $ct->createdByAdmin?->full_name ?? '—' }} ·
                            {{ $ct->created_at?->format('d M Y') }}
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="sessions-badge">
                            <div>
                                <div class="sessions-val">{{ $ct->max_sessions_allowed }}</div>
                                <div class="sessions-lbl">max sessions</div>
                            </div>
                        </div>

                        <div class="ct-actions">
                            <button class="btn btn-edit" onclick="toggleEdit({{ $ct->contract_type_id }})">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.contract-types.toggle', $ct->contract_type_id) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn {{ $ct->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                                    {{ $ct->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="ct-edit-form" id="edit-{{ $ct->contract_type_id }}">
                    <form method="POST" action="{{ route('admin.contract-types.update', $ct->contract_type_id) }}">
                        @csrf @method('PUT')
                        <div class="edit-grid">
                            <div class="form-field" style="margin:0">
                                <label class="form-lbl">Name</label>
                                <input type="text" name="name" class="form-ctrl"
                                       value="{{ $ct->name }}" required>
                            </div>
                            <div class="form-field" style="margin:0">
                                <label class="form-lbl">Max Sessions</label>
                                <input type="number" name="max_sessions_allowed" class="form-ctrl"
                                       value="{{ $ct->max_sessions_allowed }}" min="1" max="200" required>
                            </div>
                            <div style="display:flex;gap:6px;padding-bottom:0">
                                <button type="submit" class="btn btn-save">Save</button>
                                <button type="button" class="btn btn-cancel"
                                        onclick="toggleEdit({{ $ct->contract_type_id }})">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @empty
            <div class="empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="1.2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <div class="empty-title">No Contract Types</div>
                <div class="empty-sub">Add your first contract type using the form →</div>
            </div>
            @endforelse
        </div>

        <div class="add-form-card">
            <div class="add-form-header">
                <div class="add-form-title">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:6px"><path d="M12 5v14M5 12h14"/></svg>
                    New Contract Type
                </div>
            </div>
            <div class="add-form-body">
                <form method="POST" action="{{ route('admin.contract-types.store') }}">
                    @csrf

                    <div class="form-field">
                        <label class="form-lbl">Name <span style="color:var(--orange)">*</span></label>
                        <input type="text" name="name" class="form-ctrl"
                               placeholder="e.g. Part Time"
                               value="{{ old('name') }}" required>
                        @error('name')
                        <div style="font-size:10px;color:var(--red);margin-top:3px">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Free text — not an enum. Example: Part Time, Full Time, Overtime</div>
                    </div>

                    <div class="form-field">
                        <label class="form-lbl">Max Sessions Allowed <span style="color:var(--orange)">*</span></label>
                        <input type="number" name="max_sessions_allowed" class="form-ctrl"
                               placeholder="e.g. 9"
                               value="{{ old('max_sessions_allowed') }}"
                               min="1" max="200" required>
                        @error('max_sessions_allowed')
                        <div style="font-size:10px;color:var(--red);margin-top:3px">{{ $message }}</div>
                        @enderror
                        <div class="form-hint"> Maximum number of sessions allowed for the teacher in the patch </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        Create Contract Type
                    </button>
                </form>

                <div style="margin-top:16px;padding:12px 14px;background:rgba(27,79,168,0.06);border:1px solid rgba(27,79,168,0.12);border-radius:12px;font-size:11px;color:var(--muted);line-height:1.6">
                    <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--blue);margin-bottom:6px;font-weight:600">How it works</div>
                    After creating the contract type here, you can assign it to teachers in their employee profiles, indicating which contract type they belong to in each patch.
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleEdit(id) {
    const form = document.getElementById('edit-' + id);
    if (!form) return;
    const isOpen = form.classList.contains('open');
    document.querySelectorAll('.ct-edit-form').forEach(f => f.classList.remove('open'));
    if (!isOpen) form.classList.add('open');
}
</script>
@endsection