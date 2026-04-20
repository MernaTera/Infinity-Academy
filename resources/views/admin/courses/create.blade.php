@extends('admin.layouts.app')
@section('title', 'New Course')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.create-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#1B4FA8;color:#1B4FA8;text-decoration:none}

.form-card{max-width:900px;background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.form-body{padding:28px 32px}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);margin-top:4px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;margin-bottom:20px}
.form-field{display:flex;flex-direction:column;gap:5px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.req{color:#F5911E}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.form-divider{height:1px;background:rgba(27,79,168,0.06);margin:20px 0}

/* Level Builder */
.level-builder{margin-bottom:16px}
.level-block{background:#F8F6F2;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:18px 20px;margin-bottom:12px;position:relative}
.level-block-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.level-title{font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:#1B4FA8}
.btn-remove{background:none;border:none;color:#DC2626;cursor:pointer;font-size:11px;letter-spacing:1px;text-transform:uppercase;opacity:0.6;transition:opacity 0.2s}
.btn-remove:hover{opacity:1}
.level-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px 16px}
.form-control-sm{width:100%;padding:8px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control-sm:focus{border-color:#1B4FA8}

/* Sublevel --*/
.sublevel-builder{margin-top:12px;padding-top:12px;border-top:1px solid rgba(27,79,168,0.08)}
.sublevel-row{display:grid;grid-template-columns:1fr 1fr auto;gap:8px;align-items:end;margin-bottom:8px}
.btn-add-sub{background:none;border:1px dashed rgba(27,79,168,0.25);border-radius:3px;color:#1B4FA8;font-size:10px;letter-spacing:2px;text-transform:uppercase;padding:6px 12px;cursor:pointer;width:100%;transition:all 0.2s;font-family:'DM Sans',sans-serif}
.btn-add-sub:hover{border-color:#1B4FA8;background:rgba(27,79,168,0.03)}

.btn-add-level{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;border:1.5px dashed rgba(27,79,168,0.2);border-radius:6px;background:transparent;color:#1B4FA8;font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s}
.btn-add-level:hover{border-color:#1B4FA8;background:rgba(27,79,168,0.03)}

.form-footer{padding:20px 32px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-submit{padding:11px 28px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}
.btn-cancel{padding:10px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;text-decoration:none;transition:all 0.2s}
.btn-cancel:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8;text-decoration:none}
@media(max-width:680px){.form-grid,.level-grid{grid-template-columns:1fr}.create-page{padding:18px 14px}.form-body{padding:18px 20px}}
</style>

<div class="create-page">
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">New Course</h1>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;max-width:900px">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('admin.courses.store') }}" id="courseForm">
            @csrf
            <div class="form-body">

                {{-- Basic Info --}}
                <div class="sec-label">Course Information</div>
                <div class="form-grid">
                    <div class="form-field" style="grid-column:1/-1">
                        <label class="form-label">Course Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" placeholder="e.g. General English" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Base Price (LE)</label>
                        <input type="number" name="price" class="form-control"
                               value="{{ old('price') }}" placeholder="Course-level base price">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Min Teacher English Level</label>
                        <select name="english_level_id" class="form-control">
                            <option value="">— Any Level —</option>
                            @foreach($englishLevels as $lvl)
                            <option value="{{ $lvl->english_level_id }}">{{ $lvl->level_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- Level Builder --}}
                <div class="sec-label">Levels</div>
                <div class="level-builder" id="levelBuilder"></div>
                <button type="button" class="btn-add-level" onclick="addLevel()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Level
                </button>

            </div>
            <div class="form-footer">
                <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Create Course</button>
            </div>
        </form>
    </div>
</div>

<script>
let levelCount = 0;

const englishLevels = @json($englishLevels);

function addLevel() {
    const i = levelCount++;
    const div = document.createElement('div');
    div.className = 'level-block';
    div.id = `level_${i}`;

    div.innerHTML = `
    <div class="level-block-header">
        <div class="level-title">Level ${i + 1}</div>
        <button type="button" class="btn-remove" onclick="removeLevel('level_${i}')">✕ Remove</button>
    </div>
    <div class="level-grid">
        <div class="form-field">
            <label class="form-label">Name <span class="req">*</span></label>
            <input type="text" name="levels[${i}][name]" class="form-control-sm" placeholder="e.g. Level 1" required>
        </div>
        <div class="form-field">
            <label class="form-label">Price (LE) <span class="req">*</span></label>
            <input type="number" name="levels[${i}][price]" class="form-control-sm" placeholder="e.g. 3500" required>
        </div>
        <div class="form-field">
            <label class="form-label">Total Hours <span class="req">*</span></label>
            <input type="number" step="0.5" name="levels[${i}][total_hours]" class="form-control-sm" placeholder="e.g. 24" required>
        </div>
        <div class="form-field">
            <label class="form-label">Session Duration (hrs) <span class="req">*</span></label>
            <input type="number" step="0.5" name="levels[${i}][default_session_duration]" class="form-control-sm" placeholder="e.g. 2" required>
        </div>
        <div class="form-field">
            <label class="form-label">Max Capacity <span class="req">*</span></label>
            <input type="number" name="levels[${i}][max_capacity]" class="form-control-sm" placeholder="e.g. 8" required>
        </div>
        <div class="form-field">
            <label class="form-label">Min Teacher Level <span class="req">*</span></label>
            <select name="levels[${i}][teacher_level]" class="form-control-sm" required>
                <option value="">— Select —</option>
                ${englishLevels.map(l => `<option value="${l.english_level_id}">${l.level_name}</option>`).join('')}
            </select>
        </div>
    </div>
    <div class="sublevel-builder">
        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:8px">Sublevels (Optional)</div>
        <div id="subs_${i}"></div>
        <button type="button" class="btn-add-sub" onclick="addSublevel(${i})">+ Add Sublevel</button>
    </div>`;

    document.getElementById('levelBuilder').appendChild(div);
}

let subCounts = {};
function addSublevel(levelIdx) {
    if (!subCounts[levelIdx]) subCounts[levelIdx] = 0;
    const j = subCounts[levelIdx]++;
    const container = document.getElementById(`subs_${levelIdx}`);
    const row = document.createElement('div');
    row.className = 'sublevel-row';
    row.id = `sub_${levelIdx}_${j}`;
    row.innerHTML = `
        <div class="form-field">
            <label class="form-label">Name</label>
            <input type="text" name="levels[${levelIdx}][sublevels][${j}][name]" class="form-control-sm" placeholder="e.g. A1">
        </div>
        <div class="form-field">
            <label class="form-label">Price (LE)</label>
            <input type="number" name="levels[${levelIdx}][sublevels][${j}][price]" class="form-control-sm" placeholder="Optional">
        </div>
        <button type="button" class="btn-remove" onclick="document.getElementById('sub_${levelIdx}_${j}').remove()" style="margin-top:18px">✕</button>`;
    container.appendChild(row);
}

function removeLevel(id) {
    document.getElementById(id)?.remove();
}

// Add first level by default
addLevel();
</script>
@endsection