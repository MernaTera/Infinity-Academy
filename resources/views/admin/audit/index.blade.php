@extends('admin.layouts.app')
@section('title', 'Audit Logs')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
.audit-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin:0}
.page-header{margin-bottom:28px}
.page-sub{font-size:12px;color:var(--muted);margin-top:4px}

.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:14px 18px;position:relative;overflow:hidden;box-shadow:var(--glass-sh)}
.kpi-card::before{display:none}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}

.filter-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:16px 20px;margin-bottom:20px;box-shadow:var(--glass-sh)}
.filter-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:10px;align-items:end}
.form-field{display:flex;flex-direction:column;gap:4px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600}
.form-control{width:100%;padding:9px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--text);background:rgba(255,255,255,0.7);outline:none;box-sizing:border-box}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.btn-filter{padding:9px 18px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:10px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;cursor:pointer;white-space:nowrap;box-shadow:0 6px 16px rgba(27,79,168,0.25);transition:transform 0.2s,box-shadow 0.2s}
.btn-filter:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(27,79,168,0.32)}
.btn-reset{padding:9px 14px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.15);border-radius:10px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;font-weight:600}
.btn-reset:hover{border-color:var(--blue);color:var(--blue);text-decoration:none}

.tbl-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:18px;overflow:hidden;box-shadow:var(--glass-sh)}
.tbl-header{padding:12px 16px;border-bottom:1px solid rgba(27,79,168,0.08);display:flex;align-items:center;justify-content:space-between}
.tbl-title{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);font-weight:600}
.tbl-count{font-size:11px;color:var(--faint)}
.tbl{width:100%;border-collapse:collapse;min-width:900px}
.tbl thead th{padding:10px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.35);border-bottom:1px solid rgba(27,79,168,0.08);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s;animation:rowIn 0.3s ease both}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.03)}
.tbl td{padding:11px 14px;font-size:12px;color:var(--muted);vertical-align:middle}

@keyframes rowIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}

.action-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 9px;border-radius:20px;font-weight:600;white-space:nowrap}
.action-create{color:var(--green-dk);background:rgba(5,150,105,0.10);border:1px solid rgba(5,150,105,0.2)}
.action-update{color:var(--blue);background:rgba(27,79,168,0.08);border:1px solid rgba(27,79,168,0.18)}
.action-delete{color:var(--red);background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.18)}

.table-pill{display:inline-block;padding:2px 8px;background:rgba(245,145,30,0.10);border:1px solid rgba(245,145,30,0.22);border-radius:6px;font-size:9px;color:var(--orange-dk);letter-spacing:1px;font-family:monospace}

.val-wrap{display:flex;flex-direction:column;gap:2px;max-width:200px}
.val-old{font-size:10px;color:var(--red);text-decoration:line-through;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.val-new{font-size:10px;color:var(--green-dk);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.val-created{font-size:10px;color:var(--green-dk);font-style:italic}
.val-deleted{font-size:10px;color:var(--red);font-style:italic}
.val-none{font-size:10px;color:var(--faint);font-style:italic}

.emp-avatar{width:28px;height:28px;border-radius:50%;background:rgba(27,79,168,0.1);display:inline-flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:var(--blue);flex-shrink:0}

.pagination-wrap{padding:14px 16px;border-top:1px solid rgba(27,79,168,0.08);background:rgba(255,255,255,0.3)}
.pagination-wrap .page-link{background:rgba(255,255,255,0.6)!important;border:1px solid rgba(27,79,168,0.12)!important;color:var(--muted)!important;font-size:11px;border-radius:9px!important;padding:6px 12px;transition:all 0.2s}
.pagination-wrap .page-link:hover{background:rgba(27,79,168,0.06)!important;color:var(--blue)!important;border-color:var(--blue)!important}
.pagination-wrap .page-item.active .page-link{background:linear-gradient(120deg,var(--blue),var(--blue-2))!important;border-color:transparent!important;color:#fff!important;font-weight:600!important}

.immutable-notice{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.18);border-radius:20px;font-size:10px;color:var(--red);letter-spacing:1px;text-transform:uppercase;font-weight:600}

@media(max-width:1024px){.filter-grid{grid-template-columns:1fr 1fr}.kpi-grid{grid-template-columns:repeat(3,1fr)}.audit-page{padding:18px 14px}}
</style>

<div class="audit-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-header">
        <div class="page-eyebrow">Admin Panel</div>
        <h1 class="page-title">Audit Logs</h1>
        <p class="page-sub">Complete immutable activity trail — no records can be deleted or modified</p>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--blue)">
            <div class="kpi-label">Total Records</div>
            <div class="kpi-val">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--orange)">
            <div class="kpi-label">Today</div>
            <div class="kpi-val">{{ number_format($stats['today']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--green-dk)">
            <div class="kpi-label">Creates</div>
            <div class="kpi-val">{{ number_format($stats['creates']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--blue-2)">
            <div class="kpi-label">Updates</div>
            <div class="kpi-val">{{ number_format($stats['updates']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:var(--red)">
            <div class="kpi-label">Deletes</div>
            <div class="kpi-val">{{ number_format($stats['deletes']) }}</div>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('admin.audit.index') }}">
            <div class="filter-grid">
                <div class="form-field">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Table, field, or value...">
                </div>
                <div class="form-field">
                    <label class="form-label">Table</label>
                    <select name="table" class="form-control">
                        <option value="">All Tables</option>
                        @foreach($tables as $table)
                        <option value="{{ $table }}" {{ request('table') === $table ? 'selected' : '' }}>
                            {{ $table }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        <option value="Create" {{ request('action') === 'Create' ? 'selected' : '' }}>Create</option>
                        <option value="Update" {{ request('action') === 'Update' ? 'selected' : '' }}>Update</option>
                        <option value="Delete" {{ request('action') === 'Delete' ? 'selected' : '' }}>Delete</option>
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label">Employee</label>
                    <select name="employee" class="form-control">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->employee_id }}" {{ request('employee') == $emp->employee_id ? 'selected' : '' }}>
                            {{ $emp->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:12px;align-items:center">
                <div class="form-field" style="margin:0">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Date to">
                </div>
                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="{{ route('admin.audit.index') }}" class="btn-reset">Reset</a>
                <div style="margin-left:auto">
                    <span class="immutable-notice">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Immutable — Read Only
                    </span>
                </div>
            </div>
        </form>
    </div>

    <div class="tbl-card">
        <div class="tbl-header">
            <span class="tbl-title">Activity Trail</span>
            <span class="tbl-count">{{ $logs->total() }} records · Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
        </div>
        <div style="overflow-x:auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Timestamp</th>
                        <th>Employee</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record ID</th>
                        <th>Field</th>
                        <th>Change</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $actionClass = match($log->action_type) {
                            'Create' => 'action-create',
                            'Update' => 'action-update',
                            'Delete' => 'action-delete',
                            default  => 'action-update',
                        };
                        $empName = $log->employee?->full_name ?? '—';
                        $initial = strtoupper(substr($empName, 0, 1));
                    @endphp
                    <tr>
                        <td style="font-family:monospace;font-size:10px;color:#93A3BC">
                            #{{ $log->audit_log_id }}
                        </td>

                        <td>
                            <div style="font-size:12px;color:#16233F;font-weight:500">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}
                            </div>
                            <div style="font-size:10px;color:#93A3BC;font-family:monospace">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                            </div>
                            <div style="font-size:9px;color:#B4C0D0;margin-top:1px">
                                {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                            </div>
                        </td>

                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="emp-avatar">{{ $initial }}</div>
                                <div>
                                    <div style="font-size:12px;color:#16233F;font-weight:500">{{ $empName }}</div>
                                    <div style="font-size:9px;color:#93A3BC">
                                        {{ $log->employee?->user?->role?->role_name ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="action-badge {{ $actionClass }}">
                                {{ $log->action_type }}
                            </span>
                        </td>

                        <td>
                            <span class="table-pill">{{ $log->table_name }}</span>
                        </td>

                        <td style="font-family:monospace;font-size:11px;color:#5A6A85">
                            {{ $log->record_id }}
                        </td>

                        <td style="font-size:11px;color:#16233F;font-weight:500;font-family:monospace">
                            {{ $log->field_name }}
                        </td>

                        <td>
                            @if($log->action_type === 'Create')
                                <div class="val-wrap">
                                    <span class="val-created">Record created</span>
                                    @if($log->new_value)
                                    <span class="val-new">{{ Str::limit($log->new_value, 40) }}</span>
                                    @endif
                                </div>
                            @elseif($log->action_type === 'Delete')
                                <div class="val-wrap">
                                    <span class="val-deleted">Record deleted</span>
                                    @if($log->old_value)
                                    <span class="val-old" style="text-decoration:none">{{ Str::limit($log->old_value, 40) }}</span>
                                    @endif
                                </div>
                            @else
                                <div class="val-wrap">
                                    @if($log->old_value)
                                        <span class="val-old" title="{{ $log->old_value }}">
                                            {{ Str::limit($log->old_value, 40) }}
                                        </span>
                                    @else
                                        <span class="val-none">empty</span>
                                    @endif
                                    @if($log->new_value)
                                        <span class="val-new" title="{{ $log->new_value }}">
                                            {{ Str::limit($log->new_value, 40) }}
                                        </span>
                                    @else
                                        <span class="val-none">empty</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:60px;color:#93A3BC">
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px">No Logs Found</div>
                            <div style="font-size:12px">No activity matches your filters</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="pagination-wrap">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection