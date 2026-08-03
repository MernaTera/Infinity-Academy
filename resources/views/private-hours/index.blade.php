@php
    // Pick the layout that matches the viewer's role, so each role keeps its
    // own sidebar (Admin / Student Care / CS-Leads).
    $user = auth()->user();
    $__layout = $user?->isAdmin() ? 'admin.layouts.app'
        : ($user?->isSC() ? 'student-care.layouts.app'
        : 'layouts.leads');
@endphp
@extends($__layout)
@section('title', 'Private Hours')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-l:rgba(27,79,168,0.06);
        --orange:#F5911E; --orange-dk:#C47010; --orange-l:rgba(245,145,30,0.07);
        --green:#059669; --green-dk:#15803D; --green-l:rgba(5,150,105,0.07);
        --purple:#7C3AED; --purple-l:rgba(124,58,237,0.07);
        --red:#DC2626; --red-l:rgba(220,38,38,0.05);
        --dark:#0F1F3D; --text:#1A2A4A; --muted:#7A8A9A; --faint:#AAB8C8;
        --bg:#F8F6F2; --card:#fff; --border:rgba(27,79,168,0.1);
    }
    * { box-sizing:border-box; }
    .ph-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .ph-wrap { max-width:1300px; margin:0 auto; }

    /* HEADER */
    .ph-header {
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px; margin-bottom:22px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .ph-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .ph-header::after { content:''; position:absolute; bottom:-60px; left:30%; width:150px; height:150px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .ph-header-inner { position:relative; z-index:1; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; }
    .ph-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .ph-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--orange); box-shadow:0 0 8px var(--orange); }
    .ph-title { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; color:#fff; line-height:1; margin:0; }
    .ph-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }
    .btn-new { display:inline-flex; align-items:center; gap:7px; padding:11px 20px; background:var(--orange); border:none; border-radius:8px; color:#fff; font-size:11px; letter-spacing:1px; text-transform:uppercase; text-decoration:none; font-weight:700; transition:all 0.2s; box-shadow:0 4px 14px rgba(245,145,30,0.3); }
    .btn-new:hover { background:var(--orange-dk); color:#fff; text-decoration:none; transform:translateY(-1px); }

    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin:24px 0 14px; font-weight:600; }

    /* STATS */
    .ph-stats { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; }
    @media (max-width:1100px){ .ph-stats{ grid-template-columns:repeat(3,1fr); } }
    @media (max-width:560px){ .ph-stats{ grid-template-columns:1fr 1fr; } }
    .stat { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px 18px; position:relative; overflow:hidden; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--sc,var(--blue)); }
    .stat-label { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:7px; }
    .stat-val { font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:1px; line-height:0.9; color:var(--sc,var(--blue)); }
    .stat-val.sm { font-size:22px; }

    /* FILTER */
    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; }
    .filter-pill { padding:9px 18px; border-radius:8px; font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:all 0.2s; background:var(--card); border:1px solid var(--border); color:var(--muted); text-decoration:none; }
    .filter-pill:hover { border-color:var(--blue); color:var(--blue); text-decoration:none; }
    .filter-pill.active { background:var(--blue); border-color:var(--blue); color:#fff; box-shadow:0 4px 12px rgba(27,79,168,0.2); }

    /* TABLE */
    .tbl-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .tbl-scroll { overflow-x:auto; }
    .tbl { width:100%; border-collapse:collapse; min-width:950px; }
    .tbl thead th { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); padding:14px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600; background:var(--bg); white-space:nowrap; }
    .tbl thead th.num { text-align:right; }
    .tbl tbody td { padding:14px 16px; border-bottom:1px solid rgba(27,79,168,0.05); font-size:12px; color:var(--text); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:var(--blue-l); }
    .tbl .num { text-align:right; font-variant-numeric:tabular-nums; }

    .std-cell { display:flex; align-items:center; gap:11px; }
    .std-avatar { width:36px; height:36px; border-radius:50%; background:var(--blue-l); display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:15px; color:var(--blue); flex-shrink:0; }
    .std-name { font-weight:700; color:var(--text); }
    .std-course { font-size:10px; color:var(--muted); margin-top:1px; }

    .hours-cell { min-width:150px; }
    .hours-top { display:flex; align-items:baseline; gap:5px; margin-bottom:5px; }
    .hours-remain { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:0.5px; }
    .hours-total { font-size:10px; color:var(--faint); }
    .hours-bar { height:6px; background:var(--bg); border-radius:3px; overflow:hidden; }
    .hours-fill { height:100%; border-radius:3px; }

    .absence-badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; font-variant-numeric:tabular-nums; }
    .abs-ok { background:var(--green-l); color:var(--green-dk); }
    .abs-warn { background:var(--orange-l); color:var(--orange-dk); }
    .abs-bad { background:var(--red-l); color:var(--red); }

    .state-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:10px; font-weight:600; white-space:nowrap; }
    .state-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .st-active { background:var(--green-l); color:var(--green-dk); }
    .st-low { background:var(--orange-l); color:var(--orange-dk); }
    .st-depleted { background:var(--red-l); color:var(--red); }
    .st-leftover { background:var(--blue-l); color:var(--blue); }

    .btn-enroll { display:inline-flex; align-items:center; gap:5px; padding:7px 13px; border-radius:7px; font-size:10px; font-weight:600; letter-spacing:0.3px; text-decoration:none; background:var(--orange-l); color:var(--orange-dk); border:1px solid rgba(245,145,30,0.3); transition:all 0.2s; white-space:nowrap; }
    .btn-enroll:hover { background:var(--orange); color:#fff; text-decoration:none; }

    .tbl-empty { text-align:center; padding:50px 20px; color:var(--faint); }
    .tbl-empty svg { opacity:0.35; margin-bottom:12px; }
    .tbl-empty-title { font-size:15px; font-weight:600; color:var(--muted); margin-bottom:4px; }

    @media (max-width:600px){ .ph-page{ padding:16px; } }
</style>

<div class="ph-page">
    <div class="ph-wrap">

        {{-- HEADER --}}
        <div class="ph-header">
            <div class="ph-header-inner">
                <div>
                    <div class="ph-eyebrow">Private Program</div>
                    <h1 class="ph-title">Private Hours</h1>
                    <div class="ph-sub">Bundle balances, course progress & attendance for private students</div>
                </div>
                @if($canAct)
                <a href="{{ route('leads.index') }}" class="btn-new">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Registration
                </a>
                @endif
            </div>
        </div>

        {{-- STATS --}}
        <div class="ph-stats">
            <div class="stat" style="--sc:var(--dark)">
                <div class="stat-label">Total Private</div>
                <div class="stat-val">{{ $stats['total'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--green)">
                <div class="stat-label">Active</div>
                <div class="stat-val">{{ $stats['active'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--orange)">
                <div class="stat-label">Running Low</div>
                <div class="stat-val">{{ $stats['low'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--red)">
                <div class="stat-label">Depleted</div>
                <div class="stat-val">{{ $stats['depleted'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--blue)">
                <div class="stat-label">Leftover Hours</div>
                <div class="stat-val">{{ $stats['leftover'] }}</div>
            </div>
            <div class="stat" style="--sc:var(--green-dk)">
                <div class="stat-label">Total Hours Left</div>
                <div class="stat-val sm">{{ number_format($stats['hours_left'], 1) }}<span style="font-size:11px;"> h</span></div>
            </div>
        </div>

        {{-- FILTER --}}
        <span class="sec-label">Filter by State</span>
        <div class="filter-bar">
            <a href="{{ route('private-hours.index') }}" class="filter-pill {{ $stateFilter === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('private-hours.index', ['state' => 'active']) }}" class="filter-pill {{ $stateFilter === 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('private-hours.index', ['state' => 'low']) }}" class="filter-pill {{ $stateFilter === 'low' ? 'active' : '' }}">Running Low</a>
            <a href="{{ route('private-hours.index', ['state' => 'depleted']) }}" class="filter-pill {{ $stateFilter === 'depleted' ? 'active' : '' }}">Depleted</a>
            <a href="{{ route('private-hours.index', ['state' => 'leftover']) }}" class="filter-pill {{ $stateFilter === 'leftover' ? 'active' : '' }}">Leftover</a>
        </div>

        {{-- TABLE --}}
        <span class="sec-label">{{ $rows->count() }} {{ Str::plural('Student', $rows->count()) }}</span>
        <div class="tbl-card">
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course / Level</th>
                            <th class="num">Remaining Hours</th>
                            <th class="num">Absences</th>
                            <th>State</th>
                            @if($canAct)<th>Action</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $e)
                        @php
                            $stateMeta = match($e->v_state) {
                                'active'   => ['st-active','Active','var(--green)'],
                                'low'      => ['st-low','Running Low','var(--orange)'],
                                'depleted' => ['st-depleted','Depleted','var(--red)'],
                                'leftover' => ['st-leftover','Course Done · Hours Left','var(--blue)'],
                                default    => ['st-active','—','var(--green)'],
                            };
                            $absClass = $e->v_absences >= 3 ? 'abs-bad' : ($e->v_absences >= 1 ? 'abs-warn' : 'abs-ok');
                            $fillColor = $e->v_state === 'depleted' ? 'var(--red)' : ($e->v_state === 'low' ? 'var(--orange)' : 'var(--green)');
                            $remainColor = $e->v_state === 'depleted' ? 'var(--red)' : ($e->v_state === 'low' ? 'var(--orange-dk)' : 'var(--green-dk)');
                        @endphp
                        <tr>
                            <td>
                                <div class="std-cell">
                                    <div class="std-avatar">{{ strtoupper(substr($e->student?->full_name ?? '?', 0, 1)) }}</div>
                                    <div>
                                        <div class="std-name">{{ $e->student?->full_name ?? 'Student #'.$e->enrollment_id }}</div>
                                        <div class="std-course">Enrollment #{{ $e->enrollment_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $e->v_course }}</div>
                                <div style="font-size:10px;color:var(--muted);margin-top:1px;">{{ $e->v_level }}</div>
                            </td>
                            <td class="num">
                                <div class="hours-cell">
                                    <div class="hours-top" style="justify-content:flex-end;">
                                        <span class="hours-remain" style="color:{{ $remainColor }};">{{ rtrim(rtrim(number_format($e->v_remaining, 2), '0'), '.') }}h</span>
                                        @if($e->v_bundle_hours)<span class="hours-total">/ {{ rtrim(rtrim(number_format($e->v_bundle_hours, 2), '0'), '.') }}h</span>@endif
                                    </div>
                                    <div class="hours-bar"><div class="hours-fill" style="width:{{ 100 - $e->v_used_pct }}%;background:{{ $fillColor }};"></div></div>
                                </div>
                            </td>
                            <td class="num">
                                <span class="absence-badge {{ $absClass }}">{{ $e->v_absences }}</span>
                            </td>
                            <td><span class="state-badge {{ $stateMeta[0] }}">{{ $stateMeta[1] }}</span></td>
                            @if($canAct)
                            <td>
                                @if($e->v_state === 'depleted')
                                    {{-- Depleted mid-course → top up hours on the SAME enrolment via modal --}}
                                    <button type="button" class="btn-enroll"
                                        onclick="openBundleModal({{ $e->enrollment_id }}, '{{ addslashes($e->student?->full_name ?? 'Student') }}', {{ $e->v_remaining }})">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Add Bundle
                                    </button>
                                @elseif($e->v_state === 'leftover')
                                    {{-- Finished course with hours left → brand-new course registration --}}
                                    @if($e->v_lead_id)
                                    <a href="{{ route('registration.from.lead', $e->v_lead_id) }}?renew=1" class="btn-enroll">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        New Course
                                    </a>
                                    @else
                                    <a href="{{ route('leads.index') }}" class="btn-enroll" title="No lead linked — open leads">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        New Course
                                    </a>
                                    @endif
                                @else
                                <span style="color:var(--faint);font-size:11px;">—</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $canAct ? 6 : 5 }}">
                                <div class="tbl-empty">
                                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#1B4FA8" stroke-width="1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <div class="tbl-empty-title">No Private Students</div>
                                    <div style="font-size:12px;">No private enrollments match this filter.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@if($canAct)
{{-- ADD BUNDLE MODAL --}}
<div id="bundleModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(15,31,61,0.55);backdrop-filter:blur(3px);z-index:3000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;max-width:460px;width:100%;box-shadow:0 24px 60px rgba(15,31,61,0.3);overflow:hidden;font-family:'DM Sans',sans-serif;">
        {{-- header --}}
        <div style="background:linear-gradient(135deg,#0F1F3D,#243B69);padding:20px 24px;position:relative;">
            <div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;font-weight:600;margin-bottom:4px;">Charge Hours</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:2px;color:#fff;">Add Bundle</div>
            <div id="bm_student" style="font-size:12px;color:rgba(255,255,255,0.6);margin-top:3px;"></div>
        </div>

        <form method="POST" id="bundleForm" style="padding:24px;">
            @csrf
            <div style="background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.2);border-radius:8px;padding:11px 14px;margin-bottom:18px;font-size:12px;color:#DC2626;">
                Current balance: <strong id="bm_balance"></strong> — student is restricted until recharged.
            </div>

            <label style="display:block;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#7A8A9A;font-weight:600;margin-bottom:7px;">Select Bundle</label>
            <select id="bm_bundle" name="bundle_id" required style="width:100%;padding:11px 13px;border:1px solid rgba(27,79,168,0.15);border-radius:8px;font-size:13px;color:#1A2A4A;margin-bottom:18px;background:#fff;">
                <option value="">— Choose a bundle —</option>
                @foreach($bundles as $b)
                <option value="{{ $b->bundle_id }}" data-hours="{{ $b->hours }}" data-price="{{ $b->price }}">
                    {{ rtrim(rtrim(number_format($b->hours, 2), '0'), '.') }} hours — {{ number_format($b->price) }} LE
                </option>
                @endforeach
            </select>

            <label style="display:block;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#7A8A9A;font-weight:600;margin-bottom:7px;">Payment Method</label>
            <select id="bm_method" name="payment_method" required style="width:100%;padding:11px 13px;border:1px solid rgba(27,79,168,0.15);border-radius:8px;font-size:13px;color:#1A2A4A;margin-bottom:18px;background:#fff;">
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Instapay">Instapay</option>
                <option value="Vodafone_Cash">Vodafone Cash</option>
            </select>

            {{-- summary --}}
            <div id="bm_summary" style="display:none;background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.2);border-radius:8px;padding:13px 15px;margin-bottom:18px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#1A2A4A;margin-bottom:6px;">
                    <span>Hours added</span><strong id="bm_sum_hours" style="color:#15803D;"></strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#1A2A4A;margin-bottom:6px;">
                    <span>New balance</span><strong id="bm_sum_newbal" style="color:#15803D;"></strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#1A2A4A;padding-top:8px;border-top:1px solid rgba(5,150,105,0.15);">
                    <span style="font-weight:600;">Amount to pay</span><strong id="bm_sum_price" style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#C47010;"></strong>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="button" onclick="closeBundleModal()" style="flex:1;padding:12px;border:1px solid rgba(27,79,168,0.15);background:#fff;color:#7A8A9A;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">Cancel</button>
                <button type="submit" style="flex:2;padding:12px;border:none;background:#F5911E;color:#fff;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:0.5px;cursor:pointer;box-shadow:0 4px 14px rgba(245,145,30,0.3);">Charge &amp; Activate</button>
            </div>
        </form>
    </div>
</div>

<script>
    let bmCurrentBalance = 0;

    function openBundleModal(enrollmentId, studentName, remaining) {
        const form = document.getElementById('bundleForm');
        form.action = '{{ url('private-hours') }}/' + enrollmentId + '/charge-bundle';
        document.getElementById('bm_student').textContent = studentName;
        bmCurrentBalance = parseFloat(remaining) || 0;
        document.getElementById('bm_balance').textContent = bmCurrentBalance + ' hours';
        document.getElementById('bm_bundle').value = '';
        document.getElementById('bm_summary').style.display = 'none';
        document.getElementById('bundleModalOverlay').style.display = 'flex';
    }

    function closeBundleModal() {
        document.getElementById('bundleModalOverlay').style.display = 'none';
    }

    document.getElementById('bm_bundle')?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const hours = parseFloat(opt?.dataset.hours || 0);
        const price = parseFloat(opt?.dataset.price || 0);
        const summary = document.getElementById('bm_summary');
        if (!hours) { summary.style.display = 'none'; return; }
        document.getElementById('bm_sum_hours').textContent = '+' + hours + ' h';
        document.getElementById('bm_sum_newbal').textContent = (bmCurrentBalance + hours) + ' h';
        document.getElementById('bm_sum_price').textContent = price.toLocaleString() + ' LE';
        summary.style.display = 'block';
    });

    // Close on overlay click
    document.getElementById('bundleModalOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) closeBundleModal();
    });
</script>
@endif

@endsection