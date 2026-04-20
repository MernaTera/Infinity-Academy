@extends('admin.layouts.app')
@section('title', 'Courses')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.crs-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-primary:hover::before{transform:scaleX(1)}
.btn-primary:hover{color:#fff;text-decoration:none}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.search-wrap{position:relative;margin-bottom:18px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;max-width:380px;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

/* Course Cards */
.courses-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px}
.course-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;transition:all 0.2s;position:relative}
.course-card:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(27,79,168,0.1)}
.course-card.archived{opacity:0.6}
.course-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#1B4FA8,transparent)}
.cc-header{padding:18px 20px 14px;border-bottom:1px solid rgba(27,79,168,0.06)}
.cc-name{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:#1B4FA8}
.cc-price{font-family:'Bebas Neue',sans-serif;font-size:24px;color:#F5911E;letter-spacing:1px;line-height:1}
.cc-body{padding:14px 20px}
.cc-meta{display:flex;gap:16px;flex-wrap:wrap}
.cc-meta-item{display:flex;flex-direction:column;gap:2px}
.cc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8}
.cc-meta-val{font-size:12px;color:#1A2A4A;font-weight:500}

/* Levels accordion */
.cc-levels{border-top:1px solid rgba(27,79,168,0.06);padding:12px 20px}
.level-item{background:rgba(27,79,168,0.02);border:1px solid rgba(27,79,168,0.06);border-radius:4px;padding:10px 12px;margin-bottom:8px;cursor:pointer;transition:background 0.2s}
.level-item:last-child{margin-bottom:0}
.level-item:hover{background:rgba(27,79,168,0.04)}
.level-header{display:flex;justify-content:space-between;align-items:center}
.level-name{font-size:12px;color:#1A2A4A;font-weight:500}
.level-price{font-family:'Bebas Neue',sans-serif;font-size:16px;color:#1B4FA8;letter-spacing:1px}
.level-meta{font-size:10px;color:#7A8A9A;margin-top:4px}
.sublevel-list{margin-top:8px;padding-top:8px;border-top:1px solid rgba(27,79,168,0.06);display:none}
.sublevel-list.show{display:block}
.sublevel-item{display:flex;justify-content:space-between;padding:4px 0;font-size:11px;color:#7A8A9A;border-bottom:1px solid rgba(27,79,168,0.04)}
.sublevel-item:last-child{border-bottom:none}

.cc-footer{padding:12px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px;align-items:center}
.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all 0.2s}
.btn-edit{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-edit:hover{background:rgba(27,79,168,0.07);text-decoration:none}
.btn-archive{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-archive:hover{background:rgba(220,38,38,0.06)}
.btn-restore{color:#059669;border-color:rgba(5,150,105,0.2)}
.btn-restore:hover{background:rgba(5,150,105,0.06)}

.badge-active{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:3px;color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-archived{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:3px;color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.15)}

@media(max-width:768px){.crs-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(3,1fr)}.courses-grid{grid-template-columns:1fr}}
</style>

<div class="crs-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Courses</h1>
        </div>
        <a href="{{ route('admin.courses.create') }}" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Course</span>
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('error') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Total Courses</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Active</div>
            <div class="kpi-val">{{ $stats['active'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#7A8A9A">
            <div class="kpi-label">Archived</div>
            <div class="kpi-val">{{ $stats['archived'] }}</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="search-wrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="courseSearch" class="search-input" placeholder="Search courses...">
    </div>

    {{-- Cards --}}
    <div class="courses-grid" id="coursesGrid">
        @forelse($courses as $course)
        <div class="course-card {{ !$course->is_active ? 'archived' : '' }}"
             data-name="{{ strtolower($course->name) }}">

            <div class="cc-header">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div>
                        <div class="cc-name">{{ $course->name }}</div>
                        @if($course->is_active)
                            <span class="badge-active">
                                <div style="width:4px;height:4px;border-radius:50%;background:currentColor"></div>
                                Active
                            </span>
                        @else
                            <span class="badge-archived">Archived</span>
                        @endif
                    </div>
                    @if($course->price)
                    <div style="text-align:right">
                        <div class="cc-price">{{ number_format($course->price) }}</div>
                        <div style="font-size:9px;color:#AAB8C8;letter-spacing:1px">LE Base</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="cc-body">
                <div class="cc-meta">
                    <div class="cc-meta-item">
                        <span class="cc-meta-label">Levels</span>
                        <span class="cc-meta-val">{{ $course->levels_count }}</span>
                    </div>
                    <div class="cc-meta-item">
                        <span class="cc-meta-label">Instances</span>
                        <span class="cc-meta-val">{{ $course->course_instances_count }}</span>
                    </div>
                    @if($course->englishLevel)
                    <div class="cc-meta-item">
                        <span class="cc-meta-label">Min Teacher Level</span>
                        <span class="cc-meta-val">{{ $course->englishLevel->level_name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Levels --}}
            @if($course->levels->isNotEmpty())
            <div class="cc-levels">
                <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:8px">Levels</div>
                @foreach($course->levels->sortBy('level_order') as $level)
                <div class="level-item" onclick="toggleSublevels(this)">
                    <div class="level-header">
                        <div>
                            <div class="level-name">{{ $level->name }}</div>
                            <div class="level-meta">
                                {{ $level->total_hours }}h total · {{ $level->default_session_duration }}h/session · Cap {{ $level->max_capacity }}
                            </div>
                        </div>
                        <div style="text-align:right">
                            <div class="level-price">{{ number_format($level->price) }} LE</div>
                            @if($level->sublevels->isNotEmpty())
                            <div style="font-size:9px;color:#AAB8C8;margin-top:2px">{{ $level->sublevels->count() }} sublevels ↓</div>
                            @endif
                        </div>
                    </div>
                    @if($level->sublevels->isNotEmpty())
                    <div class="sublevel-list">
                        @foreach($level->sublevels as $sub)
                        <div class="sublevel-item">
                            <span>{{ $sub->name }}</span>
                            <span style="color:#1B4FA8;font-weight:500">{{ number_format($sub->price) }} LE</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <div class="cc-footer">
                <a href="{{ route('admin.courses.edit', $course->course_template_id) }}" class="btn-sm btn-edit">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.courses.archive', $course->course_template_id) }}" style="display:inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm {{ $course->is_active ? 'btn-archive' : 'btn-restore' }}">
                        {{ $course->is_active ? 'Archive' : 'Restore' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:#AAB8C8">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">No Courses Yet</div>
            <div style="font-size:12px">Create your first course to get started</div>
        </div>
        @endforelse
    </div>

</div>

<script>
document.getElementById('courseSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.course-card[data-name]').forEach(card => {
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
});

function toggleSublevels(el) {
    const list = el.querySelector('.sublevel-list');
    if (list) list.classList.toggle('show');
}
</script>
@endsection