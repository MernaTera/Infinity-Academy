@extends('admin.layouts.app')
@section('title', 'Employees')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.emp-page{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin-bottom:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:transparent;border:1.5px solid #1B4FA8;border-radius:4px;color:#1B4FA8;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-primary::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#1B4FA8,#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-primary:hover::before{transform:scaleX(1)}
.btn-primary:hover{color:#fff;text-decoration:none}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:14px 16px;position:relative;overflow:hidden;cursor:pointer;transition:all 0.2s;text-decoration:none;display:block}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(27,79,168,0.1);text-decoration:none}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-card.active-filter{box-shadow:0 0 0 2px var(--kc)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}

.toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.filter-sel{padding:10px 14px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;color:#1A2A4A;background:#fff;cursor:pointer;outline:none}

.tbl-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;overflow:hidden}
.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid rgba(27,79,168,0.07);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02)}
.tbl td{padding:13px 14px;font-size:13px;color:#4A5A7A;vertical-align:middle}

.avatar{width:34px;height:34px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;flex-shrink:0}

.role-badge{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:3px}
.role-admin{background:rgba(245,145,30,0.1);color:#C47010;border:1px solid rgba(245,145,30,0.2)}
.role-cs{background:rgba(27,79,168,0.07);color:#1B4FA8;border:1px solid rgba(27,79,168,0.15)}
.role-teacher{background:rgba(5,150,105,0.07);color:#059669;border:1px solid rgba(5,150,105,0.15)}
.role-sc{background:rgba(127,119,221,0.07);color:#534AB7;border:1px solid rgba(127,119,221,0.15)}

.status-dot{display:inline-flex;align-items:center;gap:5px;font-size:10px;letter-spacing:1px}
.dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.dot-active{background:#059669}
.dot-inactive{background:#DC2626}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;font-family:'DM Sans',sans-serif;border:1px solid;background:transparent;cursor:pointer;transition:all 0.2s;text-decoration:none;white-space:nowrap}
.btn-view{color:#1B4FA8;border-color:rgba(27,79,168,0.25)}
.btn-view:hover{background:rgba(27,79,168,0.07);text-decoration:none}
.btn-toggle-off{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-toggle-off:hover{background:rgba(220,38,38,0.06)}
.btn-toggle-on{color:#059669;border-color:rgba(5,150,105,0.2)}
.btn-toggle-on:hover{background:rgba(5,150,105,0.06)}

@media(max-width:768px){.kpi-grid{grid-template-columns:repeat(3,1fr)}.emp-page{padding:18px 14px}}
</style>

<div class="emp-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Admin Panel</div>
            <h1 class="page-title">Employees</h1>
        </div>
        <a href="{{ route('admin.employees.create') }}" class="btn-primary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Employee</span>
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">
        {{ session('success') }}
    </div>
    @endif

    {{-- KPI Cards --}}
    <div class="kpi-grid">
        <a href="{{ route('admin.employees.index') }}" class="kpi-card {{ !request('role') && !request('status') ? 'active-filter' : '' }}" style="--kc:#1B4FA8">
            <div class="kpi-label">Total</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
        </a>
        <a href="{{ route('admin.employees.index', ['status'=>'Active']) }}" class="kpi-card {{ request('status')==='Active' ? 'active-filter' : '' }}" style="--kc:#059669">
            <div class="kpi-label">Active</div>
            <div class="kpi-val">{{ $stats['active'] }}</div>
        </a>
        <a href="{{ route('admin.employees.index', ['status'=>'Inactive']) }}" class="kpi-card {{ request('status')==='Inactive' ? 'active-filter' : '' }}" style="--kc:#DC2626">
            <div class="kpi-label">Inactive</div>
            <div class="kpi-val">{{ $stats['inactive'] }}</div>
        </a>
        <a href="{{ route('admin.employees.index', ['role'=>'Admin']) }}" 
            class="kpi-card {{ request('role')==='Admin' ? 'active-filter' : '' }}" 
            style="--kc:#C47010">
            <div class="kpi-label">Admin</div>
            <div class="kpi-val">{{ $stats['admin'] }}</div>
        </a>
        <a href="{{ route('admin.employees.index', ['role'=>'Customer Service']) }}" class="kpi-card {{ request('role')==='Customer Service' ? 'active-filter' : '' }}" style="--kc:#1B4FA8">
            <div class="kpi-label">Customer Service</div>
            <div class="kpi-val">{{ $stats['cs'] }}</div>
        </a>
        <a href="{{ route('admin.employees.index', ['role'=>'Teacher']) }}" class="kpi-card {{ request('role')==='Teacher' ? 'active-filter' : '' }}" style="--kc:#059669">
            <div class="kpi-label">Teachers</div>
            <div class="kpi-val">{{ $stats['teachers'] }}</div>
        </a>
        <a href="{{ route('admin.employees.index', ['role'=>'Student Care']) }}" class="kpi-card {{ request('role')==='Student Care' ? 'active-filter' : '' }}" style="--kc:#534AB7">
            <div class="kpi-label">Student Care</div>
            <div class="kpi-val">{{ $stats['sc'] }}</div>
        </a>

    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="empSearch" class="search-input" placeholder="Search by name or email...">
        </div>
        <select class="filter-sel" onchange="window.location=this.value">
            <option value="{{ route('admin.employees.index') }}" {{ !request('role') ? 'selected' : '' }}>All Roles</option>
            @foreach($roles as $role)
            <option value="{{ route('admin.employees.index', ['role'=>$role->role_name, 'status'=>request('status')]) }}"
                {{ request('role') === $role->role_name ? 'selected' : '' }}>
                {{ $role->role_name }}
            </option>
            @endforeach
        </select>
        <select class="filter-sel" onchange="window.location=this.value">
            <option value="{{ route('admin.employees.index', ['role'=>request('role')]) }}" {{ !request('status') ? 'selected' : '' }}>All Status</option>
            <option value="{{ route('admin.employees.index', ['role'=>request('role'), 'status'=>'Active']) }}" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
            <option value="{{ route('admin.employees.index', ['role'=>request('role'), 'status'=>'Inactive']) }}" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="tbl-card">
        <table class="tbl" id="empTable">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Hired</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                @php
                    $role     = $emp->user?->role?->role_name ?? '—';
                    $initial  = strtoupper(substr($emp->full_name, 0, 1));
                    $colors   = ['Customer Service'=>['#1B4FA8','rgba(27,79,168,0.1)'], 'Teacher'=>['#059669','rgba(5,150,105,0.1)'], 'Student Care'=>['#534AB7','rgba(127,119,221,0.1)'], 'Admin'=>['#C47010','rgba(245,145,30,0.1)']];
                    $roleColor= $colors[$role] ?? ['#7A8A9A','rgba(122,138,154,0.1)'];
                    $roleCls  = match($role) { 'Customer Service'=>'role-cs', 'Teacher'=>'role-teacher', 'Student Care'=>'role-sc', 'Admin'=>'role-admin', default=>'role-cs' };
                @endphp
                <tr data-name="{{ strtolower($emp->full_name) }}" data-email="{{ strtolower($emp->user?->email ?? '') }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar" style="background:{{ $roleColor[1] }};color:{{ $roleColor[0] }}">{{ $initial }}</div>
                            <div>
                                <div style="font-weight:500;color:#1A2A4A;font-size:13px">{{ $emp->full_name }}</div>
                                <div style="font-size:10px;color:#AAB8C8;margin-top:1px">{{ $emp->user?->email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="role-badge {{ $roleCls }}">{{ $role }}</span></td>
                    <td style="font-size:12px;color:#7A8A9A">{{ $emp->branch?->name ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:12px">
                        {{ $emp->salary ? number_format($emp->salary).' LE' : '—' }}
                    </td>
                    <td>
                        <div class="status-dot">
                            <div class="dot {{ $emp->status === 'Active' ? 'dot-active' : 'dot-inactive' }}"></div>
                            {{ $emp->status }}
                        </div>
                    </td>
                    <td style="font-size:11px;color:#AAB8C8">
                        {{ \Carbon\Carbon::parse($emp->hired_at)->format('d M Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center">
                            <a href="{{ route('admin.employees.show', $emp->employee_id) }}" class="btn-sm btn-view">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            <form method="POST" action="{{ route('admin.employees.toggle', $emp->employee_id) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm {{ $emp->status === 'Active' ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                                    {{ $emp->status === 'Active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#AAB8C8;font-size:13px">
                        No employees found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employees->hasPages())
    <div style="margin-top:20px">{{ $employees->links() }}</div>
    @endif

</div>

<script>
document.getElementById('empSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#empTable tbody tr[data-name]').forEach(row => {
        const match = row.dataset.name.includes(q) || row.dataset.email.includes(q);
        row.style.display = match ? '' : 'none';
    });
});
</script>
@endsection