@extends('layouts.app')
 
@section('title', 'Archived Leads')
 
@section('content')
 
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce
 
<style>
    body, .leads-page * { font-family: 'DM Sans', sans-serif; min-width: fit-content; }
 
    .leads-page {
        background: #F8F6F2;
        min-height: 100vh;
        padding: 36px 32px;
        color: #1A2A4A;
    }
 
    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 28px; padding-bottom: 20px;
        border-bottom: 1px solid rgba(27,79,168,0.1);
        flex-wrap: wrap; gap: 16px;
    }
 
    .page-eyebrow  { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #F5911E; margin-bottom: 4px; }
    .page-title    { font-family: 'Bebas Neue', sans-serif; font-size: 34px; letter-spacing: 4px; color: #1B4FA8; line-height: 1; }
    .page-subtitle { font-size: 12px; color: #7A8A9A; margin-top: 4px; }
 
    .table-card {
        min-height: 400px;
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px; overflow: visible;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
    }
 
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
 
    .table-card table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .table-card thead tr { border-bottom: 1px solid rgba(27,79,168,0.08); }
    .table-card thead th {
        padding: 12px 14px; font-size: 9px; letter-spacing: 2.5px;
        text-transform: uppercase; color: #7A8A9A; font-weight: 500;
        white-space: nowrap; background: rgba(27,79,168,0.02); text-align: left;
    }
    .table-card tbody tr { border-bottom: 1px solid rgba(27,79,168,0.04); transition: background 0.2s; }
    .table-card tbody tr:hover { background: rgba(27,79,168,0.025); }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody td { padding: 12px 14px; font-size: 13px; color: #4A5A7A; vertical-align: middle; }
 
    .lead-name  { font-weight: 500; color: #1A2A4A; font-size: 13px; }
    .lead-phone { font-size: 11px; color: #7A8A9A; font-family: monospace; letter-spacing: 0.5px; margin-top: 2px; }
    .lead-loc   { font-size: 10px; color: #9AAABB; margin-top: 2px; }
 
    .tag {
        display: inline-block; font-size: 9px; letter-spacing: 1px;
        padding: 2px 8px; border-radius: 3px; white-space: nowrap;
        text-transform: uppercase; font-weight: 500; margin-bottom: 3px;
    }
    .tag-course { background: rgba(27,79,168,0.07);  border: 1px solid rgba(27,79,168,0.15);  color: #1B4FA8; }
    .tag-level  { background: rgba(245,145,30,0.07); border: 1px solid rgba(245,145,30,0.2);  color: #C47010; }
    .tag-sub    { background: rgba(245,145,30,0.04); border: 1px solid rgba(245,145,30,0.1);  color: #C47010; font-size: 8px; }
    .tag-degree { background: rgba(27,79,168,0.05);  border: 1px solid rgba(27,79,168,0.12);  color: #2D6FDB; }
    .tag-source { background: rgba(245,145,30,0.05); border: 1px solid rgba(245,145,30,0.15); color: #C47010; }
 
    .status-archived-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 9px; letter-spacing: 1.2px; text-transform: uppercase;
        padding: 4px 9px; border-radius: 3px; white-space: nowrap; font-weight: 500;
        color: #9A8A7A; background: rgba(154,138,122,0.08); border: 1px solid rgba(154,138,122,0.2);
    }
    .status-archived-badge::before {
        content: ''; width: 4px; height: 4px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
 
    .prev-status {
        font-size: 9px; color: #AAB8C8; margin-top: 4px;
        display: flex; align-items: center; gap: 4px;
    }
    .prev-status span {
        font-size: 9px; letter-spacing: 1px; text-transform: uppercase;
        color: #7A8A9A; background: rgba(122,138,154,0.06);
        border: 1px solid rgba(122,138,154,0.15); padding: 1px 6px; border-radius: 2px;
    }
 
    .pref-text { font-size: 12px; color: #7A8A9A; }
    .days-lbl  { font-size: 10px; color: #7A8A9A; letter-spacing: 1px; }
    .days-num  { font-family: 'Bebas Neue'; font-size: 16px; color: #1B4FA8; }
    .days-num.danger { color: #DC2626; }
 
    .action-group { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
 
    .btn-action {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 11px; font-size: 9px; letter-spacing: 1.5px;
        text-transform: uppercase; border-radius: 3px; text-decoration: none;
        font-family: 'DM Sans', sans-serif; font-weight: 500;
        border: 1px solid; background: transparent; cursor: pointer;
        transition: all 0.25s; white-space: nowrap;
    }
 
    .empty-state { padding: 60px 24px; text-align: center; }
    .empty-state svg { margin: 0 auto 14px; opacity: 0.2; }
    .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 18px; letter-spacing: 4px; color: #7A8A9A; margin-bottom: 6px; }
    .empty-sub   { font-size: 12px; color: #AAB8C8; }
 
    .pagination-wrap { margin-top: 20px; }
    .pagination-wrap .page-link {
        background: rgba(255,255,255,0.8) !important; border: 1px solid rgba(27,79,168,0.12) !important;
        color: #7A8A9A !important; font-size: 11px; letter-spacing: 1px;
        border-radius: 4px !important; padding: 6px 12px; transition: all 0.2s;
    }
    .pagination-wrap .page-link:hover { background: rgba(27,79,168,0.06) !important; color: #1B4FA8 !important; border-color: rgba(27,79,168,0.3) !important; }
    .pagination-wrap .page-item.active .page-link { background: transparent !important; border-color: #1B4FA8 !important; color: #1B4FA8 !important; font-weight: 600 !important; }
 
    .call-modal {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(6px);
        z-index: 999; align-items: center; justify-content: center;
    }
    .call-box {
        background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
        border-radius: 12px;
        box-shadow: 0 24px 60px rgba(27,79,168,0.15), 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid rgba(27,79,168,0.1); border-top: 2px solid #F5911E;
        animation: fadeIn 0.3s ease;
    }
    .call-header  { font-family: 'Bebas Neue'; letter-spacing: 4px; font-size: 20px; color: #1B4FA8; margin-bottom: 6px; }
    .call-subtext { font-size: 11px; color: #AAB8C8; letter-spacing: 1px; margin-bottom: 0; }
    .btn-cancel {
        padding: 9px 20px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 6px;
        color: #7A8A9A; font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s;
    }
    .btn-cancel:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; }
 
    @keyframes fadeIn {
        from { opacity:0; transform:translateY(10px); }
        to   { opacity:1; transform:translateY(0); }
    }
 
    @media (max-width: 768px) { .leads-page { padding: 20px 14px; } .page-title { font-size: 26px; } }
    @media (max-width: 480px) { .page-header { flex-direction: column; align-items: flex-start; } }
</style>
 
<script src="{{ asset('js/leads/history-modal.js') }}"></script>
 
<div class="leads-page">
 
    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Leads</div>
            <h1 class="page-title">Archived Leads</h1>
            <p class="page-subtitle">All archived leads</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;padding:10px 18px;
                    background:rgba(255,255,255,0.7);border:1px solid rgba(27,79,168,0.1);
                    border-radius:6px;box-shadow:0 2px 8px rgba(27,79,168,0.04);">
            <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;">Total</span>
            <span style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:#9A8A7A;letter-spacing:2px;line-height:1;">
                {{ $leads->total() }}
            </span>
        </div>
    </div>
 
    {{-- ── SEARCH ── --}}
    <div style="margin-bottom:16px;position:relative;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"
             style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="leadSearch" placeholder="Search by name or phone..."
               oninput="searchLeads(this.value)"
               style="width:100%;max-width:360px;padding:10px 14px 10px 40px;
                      background:rgba(255,255,255,0.8);border:1px solid rgba(27,79,168,0.12);
                      border-radius:6px;font-family:'DM Sans',sans-serif;font-size:13px;
                      color:#1A2A4A;outline:none;transition:border-color 0.3s,box-shadow 0.3s;"
               onfocus="this.style.borderColor='#1B4FA8';this.style.boxShadow='0 0 0 3px rgba(27,79,168,0.08)'"
               onblur="this.style.borderColor='rgba(27,79,168,0.12)';this.style.boxShadow=''">
    </div>
 
    {{-- ── TABLE ── --}}
    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Name & Contact</th>
                        <th>Source</th>
                        <th>Degree</th>
                        <th>Course & Level</th>
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
                                <span style="color:#AAB8C8;font-size:11px;">—</span>
                            @endif
                            @if($lead->level)
                                <br><span class="tag tag-level">{{ $lead->level->name ?? '' }}</span>
                            @endif
                            @if($lead->sublevel)
                                <br><span class="tag tag-sub">{{ $lead->sublevel->name ?? '' }}</span>
                            @endif
                        </td>
 
                        {{-- Status ثابت Archived + الـ status السابق --}}
                        <td>
                            <div class="status-archived-badge">Archived</div>
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
                                <span style="font-size:11px;color:#4A5A7A;max-width:150px;display:block;
                                             overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                      title="{{ $lead->notes }}">
                                    {{ $lead->notes }}
                                </span>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>
 
                        <td>
                            <div class="action-group">
 
                                {{-- Log button --}}
                                <button class="btn-action"
                                        onclick="openHistoryModal({{ $lead->lead_id }})"
                                        style="color:#7A8A9A;border-color:rgba(122,138,154,0.25);"
                                        onmouseover="this.style.background='rgba(122,138,154,0.07)';this.style.borderColor='#4e5e6e'"
                                        onmouseout="this.style.background='';this.style.borderColor='rgba(122,138,154,0.25)'">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                    Log
                                </button>
 
                                {{-- Take Lead button --}}
                                <button class="btn-action"
                                        onclick="takeLead({{ $lead->lead_id }})"
                                        style="color:#15803D;border-color:rgba(21,128,61,0.25);"
                                        onmouseover="this.style.background='rgba(21,128,61,0.07)';this.style.borderColor='#15803D'"
                                        onmouseout="this.style.background='';this.style.borderColor='rgba(21,128,61,0.25)'">
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
                        <td colspan="9">
                            <div class="empty-state">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#9A8A7A" stroke-width="1">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <div class="empty-title">No Archived Leads</div>
                                <div class="empty-sub">Archived leads will appear here</div>
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
 
{{-- History Modal --}}
<div id="historyModal" class="call-modal">
    <div class="call-box" style="width:540px;max-height:85vh;display:flex;flex-direction:column;padding:0;overflow:hidden;">
        <div style="padding:24px 28px 20px;border-bottom:1px solid rgba(27,79,168,0.08);flex-shrink:0;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div class="call-header" style="margin-bottom:2px;">Lead History</div>
                    <div class="call-subtext">All changes & activities</div>
                </div>
                <button onclick="closeHistoryModal()"
                        style="background:none;border:none;cursor:pointer;color:#AAB8C8;padding:4px;border-radius:4px;transition:color 0.2s;"
                        onmouseover="this.style.color='#DC2626'" onmouseout="this.style.color='#AAB8C8'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="historyContent" style="flex:1;overflow-y:auto;padding:16px 28px;">
            <div style="text-align:center;padding:32px 0;color:#AAB8C8;font-size:12px;letter-spacing:1px;">Loading...</div>
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
    const btn = event.target.closest('button');
    btn.innerHTML = '<span>Taking...</span>';
    btn.style.pointerEvents = 'none';
 
    fetch(`/leads/${id}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ source: 'archived' })
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
 