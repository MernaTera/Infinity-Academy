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
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

/* Level read-only cards */
.level-view{background:#F8F6F2;border:1px solid rgba(27,79,168,0.08);border-radius:6px;padding:16px 18px;margin-bottom:10px}
.lv-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.lv-name{font-weight:500;color:#1A2A4A;font-size:13px}
.lv-price{font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;letter-spacing:1px}
.lv-meta{font-size:11px;color:#7A8A9A}
.sub-list{margin-top:10px;padding-top:10px;border-top:1px solid rgba(27,79,168,0.06)}
.sub-item{display:flex;justify-content:space-between;font-size:11px;color:#7A8A9A;padding:3px 0;border-bottom:1px solid rgba(27,79,168,0.04)}
.sub-item:last-child{border-bottom:none}

.form-footer{padding:20px 32px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-submit{padding:11px 28px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}
.btn-cancel{padding:10px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;text-decoration:none;transition:all 0.2s}
.btn-cancel:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8;text-decoration:none}
.info-box{background:rgba(245,145,30,0.04);border:1px solid rgba(245,145,30,0.15);border-radius:4px;padding:10px 14px;font-size:11px;color:#C47010;margin-bottom:16px}
@media(max-width:680px){.form-grid{grid-template-columns:1fr}.edit-page{padding:18px 14px}.form-body{padding:18px 20px}}
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

    {{-- Edit Basic Info --}}
    <div class="form-card">
        <form method="POST" action="{{ route('admin.courses.update', $course->course_template_id) }}">
            @csrf @method('PUT')
            <div class="form-body">
                <div class="sec-label">Course Information</div>
                <div class="info-box">
                    ⚠ Price changes affect future registrations only. Historical pricing is locked.
                </div>
                <div class="form-grid">
                    <div class="form-field" style="grid-column:1/-1">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $course->name }}" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Base Price (LE)</label>
                        <input type="number" name="price" class="form-control" value="{{ $course->price }}">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Min Teacher Level</label>
                        <select name="english_level_id" class="form-control">
                            <option value="">— Any —</option>
                            @foreach($englishLevels as $lvl)
                            <option value="{{ $lvl->english_level_id }}"
                                {{ $course->english_level_id == $lvl->english_level_id ? 'selected' : '' }}>
                                {{ $lvl->level_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>

    {{-- Levels (View Only) --}}
    @if($course->levels->isNotEmpty())
    <div class="form-card">
        <div class="form-body">
            <div class="sec-label">Levels (View Only)</div>
            <div class="info-box">Level editing requires creating a new course version to protect historical data.</div>
            @foreach($course->levels->sortBy('level_order') as $level)
            <div class="level-view">
                <div class="lv-header">
                    <div>
                        <div class="lv-name">{{ $level->name }}</div>
                        <div class="lv-meta">
                            {{ $level->total_hours }}h · {{ $level->default_session_duration }}h/session · Cap {{ $level->max_capacity }}
                        </div>
                    </div>
                    <div class="lv-price">{{ number_format($level->price) }} LE</div>
                </div>
                @if($level->sublevels->isNotEmpty())
                <div class="sub-list">
                    @foreach($level->sublevels as $sub)
                    <div class="sub-item">
                        <span>{{ $sub->name }}</span>
                        <span>{{ number_format($sub->price) }} LE</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection