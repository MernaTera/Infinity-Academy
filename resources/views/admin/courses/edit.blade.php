@extends('admin.layouts.app')
@section('title', 'Edit Course')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.edit-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#1B4FA8;color:#1B4FA8;text-decoration:none}
.form-card{max-width:900px;background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;margin-bottom:20px}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.form-body{padding:28px 32px}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);margin-top:4px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;margin-bottom:20px}
.form-field{display:flex;flex-direction:column;gap:5px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control,.form-control-sm{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control-sm{padding:8px 10px;font-size:12px}
.form-control:focus,.form-control-sm:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.req{color:#DC2626}

/* Level Builder */
.level-card{background:#F8F6F2;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 18px;margin-bottom:12px;position:relative}
.level-card.existing{border-left:3px solid #059669}
.level-card.new{border-left:3px solid #F5911E}
.level-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.level-title{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;color:#1B4FA8}
.level-badge{font-size:9px;letter-spacing:2px;text-transform:uppercase;padding:2px 8px;border-radius:3px;margin-left:8px}
.level-badge.existing{background:rgba(5,150,105,0.1);color:#059669}
.level-badge.new{background:rgba(245,145,30,0.1);color:#F5911E}
.btn-remove{padding:5px 10px;background:transparent;border:1px solid rgba(220,38,38,0.2);border-radius:3px;color:#DC2626;font-size:9px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all 0.2s}
.btn-remove:hover{background:rgba(220,38,38,0.06);border-color:#DC2626}
.level-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:12px}

/* Sublevel */
.sublevels-wrap{margin-top:12px;padding-top:12px;border-top:1px dashed rgba(27,79,168,0.15)}
.sublevel-item{background:#fff;border:1px solid rgba(27,79,168,0.08);border-radius:4px;padding:10px 12px;margin-bottom:8px}
.sublevel-item.existing{border-left:2px solid #059669}
.sublevel-item.new{border-left:2px solid #F5911E}
.sub-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
.btn-add-sub{padding:6px 12px;background:transparent;border:1px dashed rgba(27,79,168,0.2);border-radius:3px;color:#1B4FA8;font-size:10px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;margin-top:4px}
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
.info-box{background:rgba(245,145,30,0.04);border:1px solid rgba(245,145,30,0.15);border-radius:4px;padding:10px 14px;font-size:11px;color:#C47010;margin-bottom:16px}
.legend{display:flex;gap:14px;font-size:10px;color:#7A8A9A;margin-bottom:14px}
.legend-item{display:flex;align-items:center;gap:6px}
.legend-dot{width:10px;height:3px;border-radius:2px}
@media(max-width:680px){.form-grid,.level-grid{grid-template-columns:1fr}.edit-page{padding:18px 14px}.form-body{padding:18px 20px}}
</style>

<div class="edit-page">
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Edit Course</h1>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;max-width:900px">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;max-width:900px">
        <strong>Please fix the following:</strong>
        <ul style="margin:6px 0 0 18px;padding:0;">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.courses.update', $course->course_template_id) }}">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="form-card">
            <div class="form-body">
                <div class="sec-label">Course Information</div>
                <div class="info-box">⚠ Editing existing courses affects future registrations only. Historical enrollments keep their original data.</div>
                <div class="form-grid">
                    <div class="form-field" style="grid-column:1/-1">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $course->name) }}" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Base Price (LE)</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $course->price) }}">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Min Teacher Level</label>
                        <select name="english_level_id" class="form-control">
                            <option value="">— Any —</option>
                            @foreach($englishLevels as $lvl)
                            <option value="{{ $lvl->english_level_id }}" {{ old('english_level_id', $course->english_level_id) == $lvl->english_level_id ? 'selected' : '' }}>
                                {{ $lvl->level_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Total Hours</label>
                        <input type="number" step="0.5" name="total_hours" class="form-control" value="{{ old('total_hours', $course->total_hours) }}">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Session Duration (h)</label>
                        <input type="number" step="0.5" name="default_session_duration" class="form-control" value="{{ old('default_session_duration', $course->default_session_duration) }}">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Max Capacity</label>
                        <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity', $course->max_capacity) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Levels Builder --}}
        <div class="form-card">
            <div class="form-body">
                <div class="sec-label">Levels & Sublevels</div>

                <div class="legend">
                    <div class="legend-item"><span class="legend-dot" style="background:#059669"></span> Existing</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#F5911E"></span> New / Unsaved</div>
                </div>

                <div id="levels_container"></div>

                <button type="button" class="btn-add-level" onclick="addLevel()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Level
                </button>
            </div>
            <div class="form-footer">
                <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </div>

    </form>
</div>

<script>
const englishLevels = @json($englishLevels);
const existingLevels = @json($existingLevels);

let levelCounter = 0;
let subCounters = {};

function levelIndexOptions(selectedId = null) {
    return englishLevels.map(l =>
        `<option value="${l.english_level_id}" ${selectedId == l.english_level_id ? 'selected' : ''}>${l.level_name}</option>`
    ).join('');
}

function addLevel(data = null) {
    const i = levelCounter++;
    subCounters[i] = 0;
    const isExisting = data && data.level_id;
    const div = document.createElement('div');
    div.className = 'level-card ' + (isExisting ? 'existing' : 'new');
    div.id = 'level_' + i;

    div.innerHTML = `
        <div class="level-header">
            <div>
                <span class="level-title">Level ${i + 1}</span>
                <span class="level-badge ${isExisting ? 'existing' : 'new'}">${isExisting ? 'Existing' : 'New'}</span>
            </div>
            <button type="button" class="btn-remove" onclick="removeLevel('level_${i}')">✕ Remove</button>
        </div>
        ${isExisting ? `<input type="hidden" name="levels[${i}][level_id]" value="${data.level_id}">` : ''}
        <div class="level-grid">
            <div class="form-field">
                <label class="form-label">Name <span class="req">*</span></label>
                <input type="text" name="levels[${i}][name]" class="form-control-sm" value="${data ? escapeHtml(data.name) : ''}" required>
            </div>
            <div class="form-field">
                <label class="form-label">Price (LE) <span class="req">*</span></label>
                <input type="number" name="levels[${i}][price]" class="form-control-sm" value="${data ? data.price : ''}" required>
            </div>
            <div class="form-field">
                <label class="form-label">Total Hours <span class="req">*</span></label>
                <input type="number" step="0.5" name="levels[${i}][total_hours]" class="form-control-sm" value="${data ? data.total_hours : ''}" required>
            </div>
            <div class="form-field">
                <label class="form-label">Session Duration (h) <span class="req">*</span></label>
                <input type="number" step="0.5" name="levels[${i}][default_session_duration]" class="form-control-sm" value="${data ? data.default_session_duration : ''}" required>
            </div>
            <div class="form-field">
                <label class="form-label">Max Capacity <span class="req">*</span></label>
                <input type="number" name="levels[${i}][max_capacity]" class="form-control-sm" value="${data ? data.max_capacity : ''}" required>
            </div>
            <div class="form-field">
                <label class="form-label">Min Teacher Level <span class="req">*</span></label>
                <select name="levels[${i}][teacher_level]" class="form-control-sm" required>
                    <option value="">— Select —</option>
                    ${levelIndexOptions(data ? data.teacher_level : null)}
                </select>
            </div>
        </div>
        <div class="sublevels-wrap">
            <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:8px">Sublevels (Optional)</div>
            <div id="sublevels_${i}"></div>
            <button type="button" class="btn-add-sub" onclick="addSublevel(${i})">+ Add Sublevel</button>
        </div>
    `;

    document.getElementById('levels_container').appendChild(div);

    if (data && data.sublevels && data.sublevels.length) {
        data.sublevels.forEach(sub => addSublevel(i, sub));
    }
}

function removeLevel(id) {
    if (!confirm('Remove this level? (If it has enrollments/instances, it will be kept.)')) return;
    document.getElementById(id)?.remove();
}

function addSublevel(levelIdx, data = null) {
    if (subCounters[levelIdx] === undefined) subCounters[levelIdx] = 0;
    const j = subCounters[levelIdx]++;
    const isExisting = data && data.sublevel_id;
    const div = document.createElement('div');
    div.className = 'sublevel-item ' + (isExisting ? 'existing' : 'new');
    div.id = `sub_${levelIdx}_${j}`;

    div.innerHTML = `
        <div class="sub-header">
            <span style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#1B4FA8">
                Sublevel ${j + 1}
                <span class="level-badge ${isExisting ? 'existing' : 'new'}" style="font-size:8px;margin-left:6px">${isExisting ? 'Existing' : 'New'}</span>
            </span>
            <button type="button" class="btn-remove" onclick="removeSublevel('sub_${levelIdx}_${j}')">✕</button>
        </div>
        ${isExisting ? `<input type="hidden" name="levels[${levelIdx}][sublevels][${j}][sublevel_id]" value="${data.sublevel_id}">` : ''}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
            <div class="form-field">
                <label class="form-label">Name <span class="req">*</span></label>
                <input type="text" name="levels[${levelIdx}][sublevels][${j}][name]" class="form-control-sm" value="${data ? escapeHtml(data.name) : ''}" required>
            </div>
            <div class="form-field">
                <label class="form-label">Price (LE)</label>
                <input type="number" name="levels[${levelIdx}][sublevels][${j}][price]" class="form-control-sm" value="${data && data.price ? data.price : ''}" placeholder="Inherits level">
            </div>
            <div class="form-field">
                <label class="form-label">Total Hours</label>
                <input type="number" step="0.5" name="levels[${levelIdx}][sublevels][${j}][total_hours]" class="form-control-sm" value="${data && data.total_hours ? data.total_hours : ''}" placeholder="Inherits level">
            </div>
            <div class="form-field">
                <label class="form-label">Session Duration (h)</label>
                <input type="number" step="0.5" name="levels[${levelIdx}][sublevels][${j}][default_session_duration]" class="form-control-sm" value="${data && data.default_session_duration ? data.default_session_duration : ''}" placeholder="Inherits level">
            </div>
            <div class="form-field">
                <label class="form-label">Max Capacity</label>
                <input type="number" name="levels[${levelIdx}][sublevels][${j}][max_capacity]" class="form-control-sm" value="${data && data.max_capacity ? data.max_capacity : ''}" placeholder="Inherits level">
            </div>
            <div class="form-field">
                <label class="form-label">Min Teacher Level</label>
                <select name="levels[${levelIdx}][sublevels][${j}][teacher_level]" class="form-control-sm">
                    <option value="">— Inherits level —</option>
                    ${englishLevels.map(l => `<option value="${l.english_level_id}" ${data && data.teacher_min_level == l.english_level_id ? 'selected' : ''}>${l.level_name}</option>`).join('')}
                </select>
            </div>
        </div>
    `;

    document.getElementById(`sublevels_${levelIdx}`).appendChild(div);
}

function removeSublevel(id) {
    if (!confirm('Remove this sublevel? (If it has enrollments/instances, it will be kept.)')) return;
    document.getElementById(id)?.remove();
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
}

document.addEventListener('DOMContentLoaded', function() {
    existingLevels.forEach(lvl => addLevel(lvl));
});
</script>
@endsection