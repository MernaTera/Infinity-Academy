@extends('layouts.leads')

@section('title', 'Public Leads')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    :root {
        --blue:#1B4FA8; --blue-2:#2D6FDB; --orange:#F5911E; --orange-dk:#C47010;
        --green:#059669; --green-dk:#15803D; --red:#DC2626;
        --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
        --bg:#F8F6F2; --glass:rgba(255,255,255,0.5); --glass-bd:rgba(255,255,255,0.6); --glass-sh:0 8px 30px rgba(23,45,90,0.07);
    }
    * { box-sizing:border-box; }

    .leads-page { background:var(--bg); min-height:100vh; padding:30px 34px 44px; color:var(--text); font-family:'DM Sans',sans-serif; }
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:-80px; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

    .page-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:16px; }
    .page-eyebrow  { font-size:12px; letter-spacing:3px; text-transform:uppercase; color:var(--blue); margin-bottom:7px; font-weight:600; }
    .page-title    { font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:2px; color:var(--text); line-height:0.95; margin:0; }
    .page-subtitle { font-size:12px; color:var(--muted); margin-top:6px; }

    .total-chip {
        display:inline-flex; align-items:center; gap:10px; padding:11px 18px; border-radius:14px;
        background:var(--glass); -webkit-backdrop-filter:blur(14px) saturate(150%); backdrop-filter:blur(14px) saturate(150%);
        border:1px solid var(--glass-bd); box-shadow:var(--glass-sh);
    }
    .tc-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--faint); font-weight:600; }
    .tc-num { font-family:'Bebas Neue',sans-serif; font-size:24px; letter-spacing:1px; line-height:1; color:var(--text); }

    .search-wrap { position:relative; max-width:400px; margin-bottom:18px; }
    .search-wrap svg { position:absolute; left:15px; top:50%; transform:translateY(-50%); pointer-events:none; }
    .search-input {
        width:100%; padding:12px 16px 12px 42px;
        background:var(--glass); -webkit-backdrop-filter:blur(12px) saturate(150%); backdrop-filter:blur(12px) saturate(150%);
        border:1px solid var(--glass-bd); border-radius:13px;
        font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text);
        outline:none; transition:border-color 0.25s, box-shadow 0.25s;
    }
    .search-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(27,79,168,0.1); }

    .table-card { background:rgba(255,255,255,0.72); border:1px solid var(--glass-bd); border-radius:20px; overflow:hidden; box-shadow:var(--glass-sh); min-height:400px; }
    .table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; min-width:900px; }
    thead th {
        padding:15px 16px; font-size:8px; letter-spacing:2px; text-transform:uppercase; color:var(--muted); font-weight:700;
        white-space:nowrap; background:rgba(255,255,255,0.55); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px);
        text-align:left; border-bottom:1px solid var(--glass-bd); position:sticky; top:0; z-index:2;
    }
    tbody td { padding:14px 16px; font-size:12px; color:var(--text); vertical-align:middle; border-bottom:1px solid rgba(27,79,168,0.06); }
    tbody tr { transition:background 0.15s; }
    tbody tr:hover { background:rgba(27,79,168,0.04); }
    tbody tr:last-child td { border-bottom:none; }

    .lead-name  { font-weight:600; color:var(--text); font-size:13px; }
    .lead-phone { font-size:11px; color:var(--muted); letter-spacing:0.3px; margin-top:2px; }
    .lead-loc   { font-size:10px; color:var(--faint); margin-top:3px; }

    .tag { display:inline-block; font-size:9px; letter-spacing:0.6px; padding:3px 9px; border-radius:7px; white-space:nowrap; text-transform:uppercase; font-weight:600; margin-bottom:3px; }
    .tag-course { background:rgba(27,79,168,0.08);  border:1px solid rgba(27,79,168,0.15);  color:var(--blue); }
    .tag-level  { background:rgba(245,145,30,0.10); border:1px solid rgba(245,145,30,0.2);  color:var(--orange-dk); }
    .tag-sub    { background:rgba(245,145,30,0.05); border:1px solid rgba(245,145,30,0.12); color:var(--orange-dk); font-size:8px; }
    .tag-degree { background:rgba(27,79,168,0.06);  border:1px solid rgba(27,79,168,0.12);  color:var(--blue-2); }
    .tag-source { background:rgba(245,145,30,0.07); border:1px solid rgba(245,145,30,0.15); color:var(--orange-dk); }

    .status-archived-badge {
        display:inline-flex; align-items:center; gap:6px;
        font-size:9px; letter-spacing:0.8px; text-transform:uppercase;
        padding:5px 11px; border-radius:20px; white-space:nowrap; font-weight:600;
        color:#9A8A7A; background:rgba(154,138,122,0.14); border:1px solid rgba(154,138,122,0.25);
    }
    .status-archived-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; flex-shrink:0; }

    .prev-status { font-size:9px; color:var(--faint); margin-top:5px; display:flex; align-items:center; gap:4px; }
    .prev-status span { font-size:9px; letter-spacing:0.5px; text-transform:uppercase; color:var(--muted); background:rgba(122,138,154,0.12); border:1px solid rgba(122,138,154,0.2); padding:2px 7px; border-radius:20px; }

    .pref-text { font-size:12px; color:var(--muted); }
    .days-lbl  { font-size:10px; color:var(--faint); letter-spacing:0.5px; }
    .days-num  { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:1px; color:var(--blue); }
    .days-num.danger { color:var(--red); }

    .action-group { display:flex; gap:7px; align-items:center; flex-wrap:wrap; }
    .btn-action {
        display:inline-flex; align-items:center; gap:5px; padding:7px 13px; font-size:10px; letter-spacing:0.4px;
        text-transform:uppercase; border-radius:9px; text-decoration:none; font-family:'DM Sans',sans-serif; font-weight:600;
        border:1px solid var(--glass-bd); background:rgba(255,255,255,0.4); cursor:pointer; transition:all 0.2s; white-space:nowrap;
    }
    .btn-log  { color:var(--muted); border-color:rgba(122,138,154,0.25); }
    .btn-log:hover  { background:rgba(122,138,154,0.1); border-color:#4e5e6e; }
    .btn-take { color:var(--green-dk); border-color:rgba(5,150,105,0.25); }
    .btn-take:hover { background:rgba(5,150,105,0.08); border-color:var(--green); }

    .empty-state { padding:60px 24px; text-align:center; }
    .empty-state svg { margin:0 auto 14px; opacity:0.3; }
    .empty-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; color:var(--muted); margin-bottom:6px; }
    .empty-sub   { font-size:12px; color:var(--faint); }

    .pagination-wrap { margin-top:20px; }
    .pagination-wrap .page-link {
        background:rgba(255,255,255,0.6) !important; border:1px solid var(--glass-bd) !important;
        color:var(--muted) !important; font-size:11px; letter-spacing:0.5px;
        border-radius:10px !important; padding:6px 12px; transition:all 0.2s;
    }
    .pagination-wrap .page-link:hover { background:rgba(27,79,168,0.06) !important; color:var(--blue) !important; border-color:rgba(27,79,168,0.3) !important; }
    .pagination-wrap .page-item.active .page-link { background:linear-gradient(120deg,var(--blue),var(--blue-2)) !important; border-color:transparent !important; color:#fff !important; font-weight:600 !important; }

    .call-modal { display:none; position:fixed; inset:0; background:rgba(15,31,61,0.5); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center; }
    .call-box {
        background:rgba(255,255,255,0.96); backdrop-filter:blur(20px) saturate(150%); -webkit-backdrop-filter:blur(20px) saturate(150%);
        border-radius:20px; box-shadow:0 24px 60px rgba(15,31,61,0.28);
        border:1px solid var(--glass-bd); border-top:3px solid var(--orange); animation:fadeIn 0.3s ease;
    }
    .call-header  { font-family:'Bebas Neue',sans-serif; letter-spacing:2px; font-size:20px; color:var(--text); margin-bottom:6px; }
    .call-subtext { font-size:11px; color:var(--faint); letter-spacing:0.5px; margin-bottom:0; }
    .btn-cancel {
        padding:10px 20px; background:var(--glass); border:1px solid var(--glass-bd); border-radius:11px;
        color:var(--muted); font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-weight:600;
        cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s;
    }
    .btn-cancel:hover { border-color:var(--blue); color:var(--blue); }

    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

    @media (max-width:768px) { .leads-page { padding:18px 14px 32px; } .page-title { font-size:32px; } }
    @media (max-width:480px) { .page-header { flex-direction:column; align-items:flex-start; } }
</style>

<script src="{{ asset('js/leads/history-modal.js') }}"></script>

<div class="leads-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Leads</div>
            <h1 class="page-title">Public Leads</h1>
            <p class="page-subtitle">All public leads — read only</p>
        </div>
        <div class="total-chip">
            <span class="tc-label">Total</span>
            <span class="tc-num">{{ $leads->total() }}</span>
        </div>
    </div>

    <div class="search-wrap">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="leadSearch" class="search-input" placeholder="Search by name or phone..." oninput="searchLeads(this.value)">
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Name &amp; Contact</th>
                        <th>Source</th>
                        <th>Degree</th>
                        <th>Course &amp; Level</th>
                        <th>Status</th>
                        <th>Start Pref.</th>
                        <th>Lead Age</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr id="lead-{{ $lead->lead_id }}" data-status="{{ $lead->status }}">

                        <td>
                            <div class="lead-name">{{ $lead->full_name }}</div>
                            <div class="lead-phone">{{ $lead->phone }}</div>
                            @if($lead->location)
                                <div class="lead-loc">📍 {{ $lead->location }}</div>
                            @endif
                        </td>

                        <td>
                            <span class="tag tag-source">{{ str_replace('_',' ',$lead->source) }}</span>
                        </td>

                        <td>
                            <span class="tag tag-degree">{{ $lead->degree }}</span>
                        </td>

                        <td>
                            @if($lead->courseTemplate)
                                <span class="tag tag-course">{{ $lead->courseTemplate->name }}</span>
                            @else
                                <span style="color:var(--faint);font-size:11px;">—</span>
                            @endif
                            @if($lead->level)
                                <br><span class="tag tag-level">{{ $lead->level->name ?? '' }}</span>
                            @endif
                            @if($lead->sublevel)
                                <br><span class="tag tag-sub">{{ $lead->sublevel->name ?? '' }}</span>
                            @endif
                        </td>

                        <td>
                            <div class="status-archived-badge">Expired</div>
                            @php
                                $prevStatus = $lead->leadHistories()
                                    ->where('new_status', 'Archived')
                                    ->latest('changed_at')
                                    ->value('old_status');
                            @endphp
                            @if($prevStatus)
                                <div class="prev-status">
                                    was <span>{{ str_replace('_',' ',$prevStatus) }}</span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <span class="pref-text">{{ $lead->start_preference_type ?? '—' }}</span>
                        </td>

                        @php
                            $totalHours = abs($lead->updated_at->diffInHours(now()));
                            $days  = intval($totalHours / 24);
                            $hours = $totalHours % 24;
                        @endphp
                        <td>
                            <div class="days-num {{ $days >= 3 ? 'danger' : '' }}">{{ $days }} days</div>
                            <div class="days-lbl">{{ $hours }} h</div>
                        </td>

                        <td>
                            @if($lead->notes)
                                <span style="font-size:11px;color:var(--muted);max-width:150px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                      title="{{ $lead->notes }}">
                                    {{ $lead->notes }}
                                </span>
                            @else
                                <span style="color:var(--faint);">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="action-group">
                                <button class="btn-action btn-log" onclick="openHistoryModal({{ $lead->lead_id }})">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                    Log
                                </button>

                                <button class="btn-action btn-take" onclick="takeLead({{ $lead->lead_id }})">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Take Lead
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#9A8A7A" stroke-width="1">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <div class="empty-title">No Public Leads</div>
                                <div class="empty-sub">Public leads will appear here</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($leads->hasPages())
    <div class="pagination-wrap">{{ $leads->links() }}</div>
    @endif

</div>

<div id="historyModal" class="call-modal">
    <div class="call-box" style="width:540px;max-height:85vh;display:flex;flex-direction:column;padding:0;overflow:hidden;">
        <div style="padding:24px 28px 20px;border-bottom:1px solid rgba(27,79,168,0.08);flex-shrink:0;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div class="call-header" style="margin-bottom:2px;">Lead History</div>
                    <div class="call-subtext">All changes &amp; activities</div>
                </div>
                <button onclick="closeHistoryModal()"
                        style="background:none;border:none;cursor:pointer;color:#93A3BC;padding:4px;border-radius:4px;transition:color 0.2s;"
                        onmouseover="this.style.color='#DC2626'" onmouseout="this.style.color='#93A3BC'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="historyContent" style="flex:1;overflow-y:auto;padding:16px 28px;">
            <div style="text-align:center;padding:32px 0;color:#93A3BC;font-size:12px;letter-spacing:1px;">Loading...</div>
        </div>
        <div style="padding:16px 28px;border-top:1px solid rgba(27,79,168,0.06);flex-shrink:0;display:flex;justify-content:flex-end;">
            <button onclick="closeHistoryModal()" class="btn-cancel">Close</button>
        </div>
    </div>
</div>

<script>

function searchLeads(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('tbody tr[data-status]').forEach(row => {
        const name  = row.querySelector('.lead-name')?.textContent.toLowerCase() ?? '';
        const phone = row.querySelector('.lead-phone')?.textContent.toLowerCase() ?? '';
        row.style.display = (q === '' || name.includes(q) || phone.includes(q)) ? '' : 'none';
    });
}

function takeLead(id) {
    fetch(`/leads/${id}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ source: 'public' })
    })
    .then(res => {
        if (res.ok) {
            const row = document.getElementById('lead-' + id);
            row.style.transition = 'opacity 0.4s, transform 0.4s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => row.remove(), 400);
        } else {
            btn.innerHTML = '<span>Failed</span>';
            btn.style.color = '#DC2626';
        }
    })
    .catch(() => {
        btn.innerHTML = '<span>Failed</span>';
        btn.style.color = '#DC2626';
    });
}
</script>

@endsection
