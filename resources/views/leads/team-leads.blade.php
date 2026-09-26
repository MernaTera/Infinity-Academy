@extends('layouts.leads')
@section('title', 'Team Leads')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
    .tl-page{font-family:'DM Sans',sans-serif;color:#1A2A4A;padding:30px}
    .tl-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
    .tl-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
    .tl-sub{font-size:12px;color:#7A8A9A;margin-top:2px}

    .tl-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:22px 0}
    .tl-kpi{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;padding:14px 18px;position:relative;overflow:hidden}
    .tl-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
    .tl-kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
    .tl-kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
    a.tl-kpi{text-decoration:none;cursor:pointer;transition:transform .15s,box-shadow .15s}
    a.tl-kpi:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(27,79,168,0.1)}
    a.tl-kpi.active{box-shadow:0 0 0 2px var(--kc,#1B4FA8) inset}

    .tl-btn{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1px;text-transform:uppercase;border-radius:4px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;white-space:nowrap}
    .tl-btn-invoice{color:#059669;border-color:rgba(5,150,105,0.3)}
    .tl-btn-invoice:hover{background:rgba(5,150,105,0.07);color:#059669;text-decoration:none}
    .tl-btn-log{color:#7A8A9A;border-color:rgba(122,138,154,0.3)}
    .tl-btn-log:hover{background:rgba(122,138,154,0.08);color:#4e5e6e}

    /* Log modal */
    .tl-modal{display:none;position:fixed;inset:0;background:rgba(15,31,61,0.5);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:20px}
    .tl-modal.show{display:flex}
    .call-modal{display:none;position:fixed;inset:0;background:rgba(15,31,61,0.5);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:20px}
    .call-modal-box{background:#fff;border-radius:14px;padding:26px;width:90%;max-width:520px;box-shadow:0 20px 60px rgba(15,31,61,0.3)}
    .call-modal-title{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:#1A2A4A;margin-bottom:16px}
    .modal-actions{display:flex;justify-content:flex-end;gap:10px}
    .btn-cancel{padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:5px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer}

    .tl-filters{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;padding:14px 16px;margin-bottom:18px}
    .tl-field{display:flex;flex-direction:column;gap:4px}
    .tl-field label{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A}
    .tl-field select,.tl-field input{border:1px solid rgba(27,79,168,0.15);border-radius:5px;padding:8px 10px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;min-width:150px}
    .tl-field select:focus,.tl-field input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
    .tl-apply{padding:9px 20px;background:#1B4FA8;border:none;border-radius:5px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer}

    .tl-table-wrap{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden}
    .tl-table{width:100%;border-collapse:collapse;font-size:13px}
    .tl-table thead th{text-align:left;padding:12px 14px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A;border-bottom:1px solid rgba(27,79,168,0.1);background:rgba(27,79,168,0.02)}
    .tl-table tbody td{padding:12px 14px;border-bottom:1px solid rgba(27,79,168,0.05)}
    .tl-table tbody tr:hover{background:rgba(27,79,168,0.02)}
    .tl-name{font-weight:500;color:#1A2A4A}
    .tl-phone{font-family:monospace;font-size:12px;color:#7A8A9A}
    .tl-owner{display:inline-flex;align-items:center;gap:6px}
    .tl-owner-avatar{width:24px;height:24px;border-radius:50%;background:rgba(27,79,168,0.1);color:#1B4FA8;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600}
    .tl-badge{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:3px;font-weight:600}
    .b-waiting{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}
    .b-call{color:#1B4FA8;background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.2)}
    .b-sched{color:#7C3AED;background:rgba(124,58,237,0.08);border:1px solid rgba(124,58,237,0.2)}
    .b-reg{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2)}
    .b-not{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}
    .b-arch{color:#7A8A9A;background:rgba(122,138,154,0.08);border:1px solid rgba(122,138,154,0.2)}
    .tl-empty{text-align:center;padding:44px;color:#AAB8C8;font-size:13px}
    .tl-pag{padding:14px 16px}

    @media(max-width:768px){.tl-kpis{grid-template-columns:repeat(2,1fr)}.tl-page{padding:18px 14px}.tl-table-wrap{overflow-x:auto}}
</style>

@php
    $badge = [
        'Waiting'        => 'b-waiting',
        'Call_Again'     => 'b-call',
        'Scheduled_Call' => 'b-sched',
        'Registered'     => 'b-reg',
        'Not_Interested' => 'b-not',
        'Archived'       => 'b-arch',
    ];
@endphp

<div class="tl-page">

    <div>
        <div class="tl-eyebrow">CS Leader</div>
        <h1 class="tl-title">Team Leads</h1>
        <div class="tl-sub">Every lead in your branch, with the CS who owns it.</div>
    </div>

    {{-- KPIs — clickable status filters (preserve the current period filter) --}}
    @php
        $baseParams = ['filter'=>$filterType, 'month'=>$month, 'day'=>$day, 'patch'=>$patchId, 'cs'=>$csId];
    @endphp
    <div class="tl-kpis">
        <a href="{{ route('team.leads', array_merge($baseParams, ['status'=>null])) }}" class="tl-kpi {{ !$statusFilter ? 'active' : '' }}" style="--kc:#1B4FA8"><div class="tl-kpi-label">Total Leads</div><div class="tl-kpi-val">{{ $stats['total'] }}</div></a>
        <a href="{{ route('team.leads', array_merge($baseParams, ['status'=>'Registered'])) }}" class="tl-kpi {{ $statusFilter==='Registered' ? 'active' : '' }}" style="--kc:#059669"><div class="tl-kpi-label">Registered</div><div class="tl-kpi-val">{{ $stats['registered'] }}</div></a>
        <a href="{{ route('team.leads', array_merge($baseParams, ['status'=>'Call_Again'])) }}" class="tl-kpi {{ $statusFilter==='Call_Again' ? 'active' : '' }}" style="--kc:#1B4FA8"><div class="tl-kpi-label">Call Again</div><div class="tl-kpi-val">{{ $stats['call_again'] }}</div></a>
        <a href="{{ route('team.leads', array_merge($baseParams, ['status'=>'Waiting'])) }}" class="tl-kpi {{ $statusFilter==='Waiting' ? 'active' : '' }}" style="--kc:#C47010"><div class="tl-kpi-label">Waiting</div><div class="tl-kpi-val">{{ $stats['waiting'] }}</div></a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('team.leads') }}" class="tl-filters" id="tlFilterForm">
        <div class="tl-field">
            <label>Period</label>
            <select name="filter" id="tlFilter" onchange="tlToggle()">
                <option value="day"   {{ $filterType==='day'?'selected':'' }}>Day</option>
                <option value="week"  {{ $filterType==='week'?'selected':'' }}>Week</option>
                <option value="month" {{ $filterType==='month'?'selected':'' }}>Month</option>
                <option value="patch" {{ $filterType==='patch'?'selected':'' }}>Patch</option>
            </select>
        </div>
        <div class="tl-field" id="tlDayField">
            <label>Date</label>
            <input type="date" name="day" value="{{ $day }}">
        </div>
        <div class="tl-field" id="tlMonthField">
            <label>Month</label>
            <input type="month" name="month" value="{{ $month }}">
        </div>
        <div class="tl-field" id="tlPatchField">
            <label>Patch</label>
            <select name="patch">
                <option value="">— Select Patch —</option>
                @foreach($patches as $p)
                <option value="{{ $p->patch_id }}" {{ (string)$patchId===(string)$p->patch_id?'selected':'' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="tl-field">
            <label>CS</label>
            <select name="cs">
                <option value="">All CS</option>
                @foreach($csEmployees as $emp)
                <option value="{{ $emp->employee_id }}" {{ (string)$csId===(string)$emp->employee_id?'selected':'' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="tl-field">
            <label>Status</label>
            <select name="status">
                <option value="">All Statuses</option>
                @foreach(['Waiting','Call_Again','Scheduled_Call','Registered','Not_Interested','Archived'] as $st)
                <option value="{{ $st }}" {{ $statusFilter===$st?'selected':'' }}>{{ str_replace('_',' ',$st) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="tl-apply">Apply</button>
    </form>

    {{-- Table --}}
    <div class="tl-table-wrap">
        <table class="tl-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lead</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Owner (CS)</th>
                    <th>Source</th>
                    <th>Created</th>
                    <th>Next Call</th>
                    <th>Notes / Log</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $i => $lead)
                <tr>
                    <td style="color:#AAB8C8;font-size:11px">{{ $leads->firstItem() + $i }}</td>
                    <td><div class="tl-name">{{ $lead->full_name ?? '—' }}</div></td>
                    <td class="tl-phone">{{ $lead->phone ?? '—' }}</td>
                    <td><span class="tl-badge {{ $badge[$lead->status] ?? 'b-arch' }}">{{ str_replace('_',' ',$lead->status ?? '—') }}</span></td>
                    <td>
                        @if($lead->owner)
                        <span class="tl-owner">
                            <span class="tl-owner-avatar">{{ strtoupper(substr($lead->owner->full_name ?? '?',0,1)) }}</span>
                            {{ $lead->owner->full_name }}
                        </span>
                        @else
                        <span style="color:#AAB8C8">Unassigned</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#7A8A9A">{{ $lead->source ?? '—' }}</td>
                    <td style="font-size:12px;color:#7A8A9A">{{ $lead->created_at ? \Carbon\Carbon::parse($lead->created_at)->format('d M Y') : '—' }}</td>
                    <td style="font-size:12px;color:#7A8A9A">{{ $lead->next_call_at ? \Carbon\Carbon::parse($lead->next_call_at)->format('d M Y · H:i') : '—' }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            @if($lead->status === 'Registered')
                            <a href="{{ route('leads.invoice', $lead->lead_id) }}" target="_blank" class="tl-btn tl-btn-invoice" title="View / print invoice">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                Invoice
                            </a>
                            @endif
                            <button type="button" class="tl-btn tl-btn-log" onclick="openHistoryModal({{ $lead->lead_id }})" title="Notes & activity log">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                                Log
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="tl-empty">No leads match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($leads->hasPages())
        <div class="tl-pag">{{ $leads->links() }}</div>
        @endif
    </div>

</div>

<script>
function tlToggle(){
    const t = document.getElementById('tlFilter').value;
    document.getElementById('tlDayField').style.display   = (t==='day'||t==='week') ? '' : 'none';
    document.getElementById('tlMonthField').style.display = (t==='month') ? '' : 'none';
    document.getElementById('tlPatchField').style.display = (t==='patch') ? '' : 'none';
}
tlToggle();
</script>

{{-- Notes & activity log modal (reuses the shared leads history modal JS) --}}
<div id="historyModal" class="call-modal">
    <div class="call-modal-box" style="max-width:520px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div class="call-modal-title" style="margin-bottom:0;">Lead Notes &amp; Log</div>
            <button onclick="closeHistoryModal()" style="background:transparent;border:none;color:#7A8A9A;cursor:pointer;font-size:22px;line-height:1;">&times;</button>
        </div>
        <div id="historyContent" style="max-height:420px;overflow-y:auto;font-size:13px;color:#1A2A4A;">
            {{-- filled by JS --}}
        </div>
        <div class="modal-actions" style="margin-top:18px;">
            <button onclick="closeHistoryModal()" class="btn-cancel">Close</button>
        </div>
    </div>
</div>
<script src="{{ asset('js/leads/history-modal.js') }}"></script>
@endsection
