@extends('admin.layouts.app')
@section('title', 'Audit Logs')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.audit-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{margin-bottom:28px}
.page-sub{font-size:12px;color:#7A8A9A;margin-top:4px}

.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:14px 18px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

/* Filters */
.filter-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;padding:16px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:10px;align-items:end}
.form-field{display:flex;flex-direction:column;gap:4px}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.form-control{width:100%;padding:9px 10px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.btn-filter{padding:9px 18px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:2px;cursor:pointer;white-space:nowrap}
.btn-reset{padding:9px 14px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center}
.btn-reset:hover{border-color:rgba(27,79,168,0.3);color:#1B4FA8;text-decoration:none}

/* Table */
.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden}
.tbl-header{padding:12px 16px;border-bottom:1px solid rgba(27,79,168,0.07);display:flex;align-items:center;justify-content:space-between}
.tbl-title{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E}
.tbl-count{font-size:11px;color:#AAB8C8}
.tbl{width:100%;border-collapse:collapse;min-width:900px}
.tbl thead th{padding:10px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s;animation:rowIn 0.3s ease both}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:11px 14px;font-size:12px;color:#4A5A7A;vertical-align:middle}

@keyframes rowIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}

/* Action badges */
.action-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;white-space:nowrap}
.action-create{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.action-update{color:#1B4FA8;background:rgba(27,79,168,0.07);border:1px solid rgba(27,79,168,0.15)}
.action-delete{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}

/* Table name pill */
.table-pill{display:inline-block;padding:2px 8px;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.15);border-radius:3px;font-size:9px;color:#C47010;letter-spacing:1px;font-family:monospace}

/* Value diff */
.val-wrap{display:flex;flex-direction:column;gap:2px;max-width:200px}
.val-old{font-size:10px;color:#DC2626;text-decoration:line-through;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.val-new{font-size:10px;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.val-created{font-size:10px;color:#059669;font-style:italic}
.val-deleted{font-size:10px;color:#DC2626;font-style:italic}
.val-none{font-size:10px;color:#AAB8C8;font-style:italic}

/* Avatar */
.emp-avatar{width:28px;height:28px;border-radius:50%;background:rgba(27,79,168,0.1);display:inline-flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:#1B4FA8;flex-shrink:0}

/* Pagination */
.pagination-wrap{padding:14px 16px;border-top:1px solid rgba(27,79,168,0.07)}
.pagination-wrap .page-link{background:rgba(255,255,255,0.8)!important;border:1px solid rgba(27,79,168,0.12)!important;color:#7A8A9A!important;font-size:11px;border-radius:4px!important;padding:6px 12px;transition:all 0.2s}
.pagination-wrap .page-link:hover{background:rgba(27,79,168,0.06)!important;color:#1B4FA8!important;border-color:rgba(27,79,168,0.3)!important}
.pagination-wrap .page-item.active .page-link{background:transparent!important;border-color:#1B4FA8!important;color:#1B4FA8!important;font-weight:600!important}

/* Immutable notice */
.immutable-notice{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(220,38,38,0.04);border:1px solid rgba(220,38,38,0.1);border-radius:4px;font-size:10px;color:#DC2626;letter-spacing:1px;text-transform:uppercase}

@media(max-width:1024px){.filter-grid{grid-template-columns:1fr 1fr}.kpi-grid{grid-template-columns:repeat(3,1fr)}.audit-page{padding:18px 14px}}
</style>

<div class="audit-page">

    <div class="page-header">
        <div class="page-eyebrow">Admin Panel</div>
        <h1 class="page-title">Audit Logs</h1>
        <p class="page-sub">Complete immutable activity trail — no records can be deleted or modified</p>
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#1B4FA8">
            <div class="kpi-label">Total Records</div>
            <div class="kpi-val">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:#F5911E">
            <div class="kpi-label">Today</div>
            <div class="kpi-val">{{ number_format($stats['today']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Creates</div>
            <div class="kpi-val">{{ number_format($stats['creates']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:#1B6FA8">
            <div class="kpi-label">Updates</div>
            <div class="kpi-val">{{ number_format($stats['updates']) }}</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Deletes</div>
            <div class="kpi-val">{{ number_format($stats['deletes']) }}</div>
        </div>
    </div>

    {{-- Filters --}}
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

    {{-- Table --}}
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
                        {{-- ID --}}
                        <td style="font-family:monospace;font-size:10px;color:#AAB8C8">
                            #{{ $log->audit_log_id }}
                        </td>

                        {{-- Timestamp --}}
                        <td>
                            <div style="font-size:12px;color:#1A2A4A;font-weight:500">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}
                            </div>
                            <div style="font-size:10px;color:#AAB8C8;font-family:monospace">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                            </div>
                            <div style="font-size:9px;color:#C8D4E0;margin-top:1px">
                                {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                            </div>
                        </td>

                        {{-- Employee --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="emp-avatar">{{ $initial }}</div>
                                <div>
                                    <div style="font-size:12px;color:#1A2A4A;font-weight:500">{{ $empName }}</div>
                                    <div style="font-size:9px;color:#AAB8C8">
                                        {{ $log->employee?->user?->role?->role_name ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Action --}}
                        <td>
                            <span class="action-badge {{ $actionClass }}">
                                {{ $log->action_type }}
                            </span>
                        </td>

                        {{-- Table --}}
                        <td>
                            <span class="table-pill">{{ $log->table_name }}</span>
                        </td>

                        {{-- Record ID --}}
                        <td style="font-family:monospace;font-size:11px;color:#7A8A9A">
                            {{ $log->record_id }}
                        </td>

                        {{-- Field --}}
                        <td style="font-size:11px;color:#1A2A4A;font-weight:500;font-family:monospace">
                            {{ $log->field_name }}
                        </td>

                        {{-- Change --}}
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
                        <td colspan="8" style="text-align:center;padding:60px;color:#AAB8C8">
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