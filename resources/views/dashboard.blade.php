@extends('layouts.leads')
@section('title', 'Dashboard')

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

    .cs-page { background:var(--bg); min-height:100vh; padding:28px 32px; color:var(--text); font-family:'DM Sans',sans-serif; }

    /* ═══ HEADER ═══ */
    .cs-header {
        margin:0 auto 22px;
        background:linear-gradient(135deg, var(--dark) 0%, #1A2A4A 60%, #243B69 100%);
        border-radius:14px; padding:24px 30px;
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:16px; position:relative; overflow:hidden;
        box-shadow:0 8px 32px rgba(15,31,61,0.15);
    }
    .cs-header::before { content:''; position:absolute; top:-70px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(245,145,30,0.06); }
    .cs-header::after { content:''; position:absolute; bottom:-60px; left:25%; width:150px; height:150px; border-radius:50%; background:rgba(27,79,168,0.15); }
    .cs-header-left { position:relative; z-index:1; }
    .cs-eyebrow { font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin-bottom:5px; font-weight:600; display:flex; align-items:center; gap:8px; }
    .cs-eyebrow::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--orange); box-shadow:0 0 8px var(--orange); }
    .cs-title { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; color:#fff; line-height:1; margin:0; }
    .cs-sub { font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; letter-spacing:0.5px; }
    .cs-actions { display:flex; gap:10px; position:relative; z-index:1; flex-wrap:wrap; }
    .btn-h { display:inline-flex; align-items:center; gap:7px; padding:11px 20px; border-radius:7px; font-size:10px; letter-spacing:2px; text-transform:uppercase; text-decoration:none; transition:all 0.25s; font-weight:600; }
    .btn-h-ghost { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:rgba(255,255,255,0.85); }
    .btn-h-ghost:hover { background:rgba(255,255,255,0.14); color:#fff; text-decoration:none; }
    .btn-h-solid { background:var(--orange); border:none; color:#fff; font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:3px; }
    .btn-h-solid:hover { background:#E8850F; text-decoration:none; color:#fff; }

    .cs-wrap { margin:0 auto; }
    .sec-label { display:block; font-size:9px; letter-spacing:4px; text-transform:uppercase; color:var(--orange); margin:26px 0 14px; font-weight:600; }
    .sec-label:first-child { margin-top:0; }

    /* ═══ CALLS-DUE BANNER ═══ */
    .calls-banner {
        display:flex; align-items:center; gap:14px; padding:16px 20px; margin-bottom:6px;
        background:linear-gradient(135deg, var(--orange-l), transparent);
        border:1px solid rgba(245,145,30,0.2); border-left:3px solid var(--orange);
        border-radius:12px; text-decoration:none; transition:all 0.2s;
    }
    .calls-banner:hover { background:linear-gradient(135deg, rgba(245,145,30,0.12), transparent); text-decoration:none; }
    .calls-banner-icon { width:44px; height:44px; border-radius:11px; background:var(--orange); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .calls-banner-text { flex:1; }
    .calls-banner-title { font-size:14px; font-weight:700; color:var(--text); }
    .calls-banner-sub { font-size:11px; color:var(--muted); margin-top:2px; }
    .calls-banner-count { font-family:'Bebas Neue',sans-serif; font-size:34px; color:var(--orange); letter-spacing:1px; }

    /* ═══ KPI GRID ═══ */
    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
    @media (max-width:900px){ .kpi-grid{ grid-template-columns:repeat(2,1fr); } }
    @media (max-width:520px){ .kpi-grid{ grid-template-columns:1fr; } }
    .kpi-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:18px; text-decoration:none; position:relative; overflow:hidden; transition:transform 0.2s, box-shadow 0.2s; display:block; box-shadow:0 2px 10px rgba(27,79,168,0.04); }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kc,var(--blue)); }
    .kpi-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(27,79,168,0.1); text-decoration:none; }
    .kpi-icon { width:36px; height:36px; border-radius:9px; background:var(--kc,var(--blue)); opacity:0.12; margin-bottom:12px; }
    .kpi-label { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-bottom:6px; }
    .kpi-val { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:1px; line-height:0.9; color:var(--kc,var(--blue)); }
    .kpi-sub { font-size:10px; color:var(--faint); margin-top:5px; }

    /* ═══ TARGET PANEL ═══ */
    .target-panel { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .target-body { padding:24px; display:grid; grid-template-columns:1.4fr 1fr; gap:28px; align-items:center; }
    @media (max-width:760px){ .target-body{ grid-template-columns:1fr; gap:20px; } }
    .target-ring-wrap { display:flex; align-items:center; justify-content:center; }
    .target-ring { position:relative; width:150px; height:150px; }
    .target-ring svg { transform:rotate(-90deg); }
    .target-ring-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .target-ring-pct { font-family:'Bebas Neue',sans-serif; font-size:38px; color:var(--green); line-height:1; letter-spacing:1px; }
    .target-ring-lbl { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:600; margin-top:2px; }
    .target-stats { display:flex; flex-direction:column; gap:14px; }
    .target-stat-row { display:flex; align-items:center; justify-content:space-between; padding-bottom:14px; border-bottom:1px solid var(--border); }
    .target-stat-row:last-child { border-bottom:none; padding-bottom:0; }
    .target-stat-label { font-size:11px; color:var(--muted); letter-spacing:0.3px; }
    .target-stat-val { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:1px; }

    /* ═══ TWO COL ═══ */
    .two-col { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
    @media (max-width:900px){ .two-col{ grid-template-columns:1fr; } }

    .mini-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(27,79,168,0.05); }
    .mini-card-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg, rgba(27,79,168,0.02), transparent); }
    .mini-card-title { font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:2px; color:var(--text); }
    .mini-card-link { font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--blue); font-weight:600; text-decoration:none; }
    .mini-card-link:hover { text-decoration:underline; }
    .mini-card-body { padding:8px 20px; }

    .mini-row { display:flex; align-items:center; gap:12px; padding:13px 0; border-bottom:1px solid rgba(27,79,168,0.05); }
    .mini-row:last-child { border-bottom:none; }
    .mini-avatar { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Bebas Neue',sans-serif; font-size:16px; flex-shrink:0; }
    .mini-row-name { font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .mini-row-sub { font-size:10px; color:var(--muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .mini-empty { text-align:center; padding:32px 20px; color:var(--faint); font-size:12px; }

    /* Payment category badge */
    .pay-cat { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:6px; font-size:9px; font-weight:600; letter-spacing:0.3px; text-transform:uppercase; }
    .cat-course   { background:var(--blue-l); color:var(--blue); }
    .cat-test     { background:var(--purple-l); color:var(--purple); }
    .cat-material { background:var(--orange-l); color:var(--orange-dk); }
    .cat-install  { background:var(--green-l); color:var(--green-dk); }
    .pay-method-tag { font-size:9px; color:var(--faint); margin-top:2px; }

    /* Lead status mini badge */
    .lead-mini-badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:9px; font-weight:600; }
    .lmb-waiting { background:rgba(122,138,154,0.12); color:var(--muted); }
    .lmb-call    { background:var(--orange-l); color:var(--orange-dk); }
    .lmb-sched   { background:var(--blue-l); color:var(--blue); }
    .lmb-reg     { background:var(--green-l); color:var(--green-dk); }

    @media (max-width:600px){ .cs-page{ padding:16px; } }
</style>

<div class="cs-page">

    {{-- ── HEADER ── --}}
    <div class="cs-header">
        <div class="cs-header-left">
            <div class="cs-eyebrow">Customer Service</div>
            <h1 class="cs-title">Welcome back{{ $employee?->full_name ? ', '.explode(' ', $employee->full_name)[0] : '' }}</h1>
            <div class="cs-sub">{{ now()->format('l, d M Y') }}@if($currentPatch) · {{ $currentPatch->name }}@endif</div>
        </div>
        <div class="cs-actions">
            <a href="{{ route('leads.public') }}" class="btn-h btn-h-ghost">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 11l2 2 4-4M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                Public Pool
            </a>
            <a href="{{ route('leads.create') }}" class="btn-h btn-h-solid">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Lead
            </a>
        </div>
    </div>

    <div class="cs-wrap">

        {{-- ── CALLS DUE BANNER ── --}}
        @if($callsDueToday > 0)
        <a href="{{ route('leads.index') }}" class="calls-banner">
            <div class="calls-banner-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div class="calls-banner-text">
                <div class="calls-banner-title">You have calls scheduled today</div>
                <div class="calls-banner-sub">Follow up with your leads to keep them warm →</div>
            </div>
            <div class="calls-banner-count">{{ $callsDueToday }}</div>
        </a>
        @endif

        {{-- ── MY LEADS KPIs ── --}}
        <span class="sec-label">My Leads</span>
        <div class="kpi-grid">
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--blue)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Total Leads</div>
                <div class="kpi-val">{{ $leadsStats['my_total'] }}</div>
                <div class="kpi-sub">assigned to me</div>
            </a>
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--orange)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Active</div>
                <div class="kpi-val">{{ $leadsStats['my_active'] }}</div>
                <div class="kpi-sub">in follow-up</div>
            </a>
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--green)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Registered</div>
                <div class="kpi-val">{{ $leadsStats['my_registered'] }}</div>
                <div class="kpi-sub">converted</div>
            </a>
            <a href="{{ route('leads.index') }}" class="kpi-card" style="--kc:var(--red)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Overdue</div>
                <div class="kpi-val">{{ $leadsStats['my_overdue'] }}</div>
                <div class="kpi-sub">need attention</div>
            </a>
        </div>

        {{-- ── SALES TARGET ── --}}
        <span class="sec-label">Monthly Target</span>
        <div class="target-panel">
            <div class="target-body">
                {{-- Ring --}}
                <div class="target-ring-wrap">
                    @php
                        $pct = min($salesStats['percentage'], 100);
                        $circumference = 2 * 3.14159 * 65;
                        $offset = $circumference - ($pct / 100) * $circumference;
                    @endphp
                    <div class="target-ring">
                        <svg width="150" height="150">
                            <circle cx="75" cy="75" r="65" fill="none" stroke="var(--bg)" stroke-width="12"/>
                            <circle cx="75" cy="75" r="65" fill="none" stroke="var(--green)" stroke-width="12"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $offset }}"/>
                        </svg>
                        <div class="target-ring-center">
                            <div class="target-ring-pct">{{ $salesStats['percentage'] }}%</div>
                            <div class="target-ring-lbl">Achieved</div>
                        </div>
                    </div>
                </div>
                {{-- Stats --}}
                <div class="target-stats">
                    <div class="target-stat-row">
                        <span class="target-stat-label">Target</span>
                        <span class="target-stat-val" style="color:var(--text);">{{ number_format($salesStats['target']) }} LE</span>
                    </div>
                    <div class="target-stat-row">
                        <span class="target-stat-label">Achieved</span>
                        <span class="target-stat-val" style="color:var(--green);">{{ number_format($salesStats['achieved']) }} LE</span>
                    </div>
                    <div class="target-stat-row">
                        <span class="target-stat-label">Remaining</span>
                        <span class="target-stat-val" style="color:var(--orange);">{{ number_format($salesStats['remaining']) }} LE</span>
                    </div>
                    <div class="target-stat-row">
                        <span class="target-stat-label">Registrations this month</span>
                        <span class="target-stat-val" style="color:var(--blue);">{{ $salesStats['registrations'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── OUTSTANDING KPIs ── --}}
        <span class="sec-label">Outstanding & Collections</span>
        <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);">
            <a href="{{ route('outstanding.index') }}" class="kpi-card" style="--kc:var(--red)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Outstanding Students</div>
                <div class="kpi-val">{{ $outstandingStats['count'] }}</div>
                <div class="kpi-sub">with balance due</div>
            </a>
            <a href="{{ route('outstanding.index') }}" class="kpi-card" style="--kc:var(--orange-dk)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Total Outstanding</div>
                <div class="kpi-val">{{ number_format($outstandingStats['total_le']) }}</div>
                <div class="kpi-sub">LE to collect</div>
            </a>
            <a href="{{ route('outstanding.index') }}" class="kpi-card" style="--kc:var(--red)">
                <div class="kpi-icon"></div>
                <div class="kpi-label">Restricted</div>
                <div class="kpi-val">{{ $outstandingStats['restricted'] }}</div>
                <div class="kpi-sub">access restricted</div>
            </a>
        </div>

        {{-- ── RECENT ACTIVITY ── --}}
        <span class="sec-label">Recent Activity</span>
        <div class="two-col">
            {{-- Recent Leads --}}
            <div class="mini-card">
                <div class="mini-card-header">
                    <div class="mini-card-title">Recent Leads</div>
                    <a href="{{ route('leads.index') }}" class="mini-card-link">View All →</a>
                </div>
                <div class="mini-card-body">
                    @forelse($recentLeads as $lead)
                        @php
                            $lmb = match($lead->status) {
                                'Waiting'        => ['lmb-waiting','Waiting'],
                                'Call_Again'     => ['lmb-call','Call Again'],
                                'Scheduled_Call' => ['lmb-sched','Scheduled'],
                                'Registered'     => ['lmb-reg','Registered'],
                                default          => ['lmb-waiting', str_replace('_',' ',$lead->status)],
                            };
                        @endphp
                        <div class="mini-row">
                            <div class="mini-avatar" style="background:var(--blue-l);color:var(--blue);">{{ strtoupper(substr($lead->full_name,0,1)) }}</div>
                            <div style="flex:1;min-width:0;">
                                <div class="mini-row-name">{{ $lead->full_name }}</div>
                                <div class="mini-row-sub">{{ $lead->phone }} · {{ $lead->courseTemplate?->name ?? 'No course' }}</div>
                            </div>
                            <span class="lead-mini-badge {{ $lmb[0] }}">{{ $lmb[1] }}</span>
                        </div>
                    @empty
                        <div class="mini-empty">No leads yet. Start by adding one!</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="mini-card">
                <div class="mini-card-header">
                    <div class="mini-card-title">Recent Payments</div>
                    <a href="{{ route('sales.index') }}" class="mini-card-link">Sales Table →</a>
                </div>
                <div class="mini-card-body">
                    @forelse($recentPayments as $tx)
                        @php
                            // Distinguish the payment type so CS can tell Course / Test / Material / Installment apart
                            if ($tx->transaction_type === 'Installment') {
                                $catClass = 'cat-install'; $catLabel = 'Installment';
                            } else {
                                $catClass = match($tx->transaction_category) {
                                    'Course'   => 'cat-course',
                                    'Test'     => 'cat-test',
                                    'Material' => 'cat-material',
                                    default    => 'cat-course',
                                };
                                $catLabel = $tx->transaction_category;
                            }
                            $methodLabel = match($tx->payment_method) {
                                'Transfer' => 'Instapay',
                                'Online'   => 'Vodafone Cash',
                                'Cash'     => 'Cash',
                                'Card'     => 'Card',
                                default    => $tx->payment_method,
                            };
                        @endphp
                        <div class="mini-row">
                            <div class="mini-avatar" style="background:var(--green-l);color:var(--green);">
                                {{ strtoupper(substr($tx->enrollment?->student?->full_name ?? '?', 0, 1)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="mini-row-name">{{ $tx->enrollment?->student?->full_name ?? '—' }}</div>
                                <div class="mini-row-sub">
                                    <span class="pay-cat {{ $catClass }}">{{ $catLabel }}</span>
                                    · {{ $tx->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--green);letter-spacing:1px;">+{{ number_format($tx->amount) }}</div>
                                <div class="pay-method-tag">{{ $methodLabel }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="mini-empty">No payments recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>{{-- /cs-wrap --}}

</div>

<script>
// Animate KPI numbers
document.querySelectorAll('.kpi-val').forEach(el => {
    const text = el.textContent.trim();
    const num  = parseFloat(text.replace(/[^0-9.]/g, ''));
    if (isNaN(num) || num === 0) return;
    const dur = 700, start = performance.now();
    (function tick(now) {
        const pct = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - pct, 3);
        el.textContent = Math.round(num * ease).toLocaleString();
        if (pct < 1) requestAnimationFrame(tick);
    })(start);
});
</script>

@endsection