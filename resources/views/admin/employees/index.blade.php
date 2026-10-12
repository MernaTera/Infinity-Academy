@extends('admin.layouts.app')
@section('title', 'Employees')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-2:#2D6FDB;--orange:#F5911E;--orange-dk:#C47010;--green:#059669;--green-dk:#15803D;--red:#DC2626;--text:#16233F;--muted:#5A6A85;--faint:#93A3BC;--card:rgba(255,255,255,0.5);--glass-bd:rgba(255,255,255,0.6);--glass-sh:0 8px 30px rgba(23,45,90,0.07);--bg:#F8F6F2;}
.emp-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text)}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--blue);margin-bottom:4px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--text);margin-bottom:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}

.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:12px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:3px;text-decoration:none;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 10px 24px rgba(27,79,168,0.28)}
.btn-primary::before{display:none}
.btn-primary:hover::before{transform:none}
.btn-primary:hover{color:#fff;text-decoration:none;transform:translateY(-2px);box-shadow:0 14px 30px rgba(27,79,168,0.36)}
.btn-primary span,.btn-primary svg{position:relative;z-index:1}

.kpi-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:16px;padding:14px 16px;position:relative;overflow:hidden;cursor:pointer;transition:all 0.2s;text-decoration:none;display:block;box-shadow:var(--glass-sh)}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 16px 38px rgba(23,45,90,0.13);text-decoration:none}
.kpi-card::before{display:none}
.kpi-card.active-filter{box-shadow:0 0 0 2px var(--kc),var(--glass-sh)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:5px;font-weight:600}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1}

.toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;padding:10px 14px 10px 38px;border:1px solid var(--glass-bd);border-radius:12px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.6);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);outline:none}
.search-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.08)}
.filter-sel{padding:10px 14px;border:1px solid var(--glass-bd);border-radius:12px;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--text);background:rgba(255,255,255,0.6);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);cursor:pointer;outline:none}
.toolbar .app-select,.toolbar > .filter-select{flex:1 1 150px;min-width:140px;max-width:260px}
.tbl-card{background:var(--card);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid var(--glass-bd);border-radius:20px;overflow:hidden;box-shadow:var(--glass-sh)}
.tbl{width:100%;border-collapse:collapse}
.tbl thead th{padding:11px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:600;background:rgba(255,255,255,0.4);border-bottom:1px solid var(--glass-bd);white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.06);transition:background 0.2s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:rgba(27,79,168,0.04)}
.tbl td{padding:13px 14px;font-size:13px;color:var(--muted);vertical-align:middle}

.avatar{width:34px;height:34px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;flex-shrink:0}

.role-badge{display:inline-block;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:20px;font-weight:600}
.role-admin{background:rgba(245,145,30,0.12);color:var(--orange-dk);border:1px solid rgba(245,145,30,0.22)}
.role-cs{background:rgba(27,79,168,0.10);color:var(--blue);border:1px solid rgba(27,79,168,0.18)}
.role-teacher{background:rgba(5,150,105,0.10);color:var(--green-dk);border:1px solid rgba(5,150,105,0.18)}
.role-sc{background:rgba(124,58,237,0.10);color:#7C3AED;border:1px solid rgba(124,58,237,0.18)}

.status-dot{display:inline-flex;align-items:center;gap:5px;font-size:10px;letter-spacing:1px}
.dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.dot-active{background:var(--green)}
.dot-inactive{background:var(--red)}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:5px 11px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:8px;font-family:'DM Sans',sans-serif;border:1px solid;background:rgba(255,255,255,0.5);cursor:pointer;transition:all 0.2s;text-decoration:none;white-space:nowrap;font-weight:600}
.btn-view{color:var(--blue);border-color:rgba(27,79,168,0.22)}
.btn-view:hover{background:rgba(27,79,168,0.08);text-decoration:none}
.btn-toggle-off{color:var(--red);border-color:rgba(220,38,38,0.22)}
.btn-toggle-off:hover{background:rgba(220,38,38,0.07)}
.btn-toggle-on{color:var(--green-dk);border-color:rgba(5,150,105,0.22)}
.btn-toggle-on:hover{background:rgba(5,150,105,0.07)}

@media(max-width:768px){.kpi-grid{grid-template-columns:repeat(3,1fr)}.emp-page{padding:18px 14px}}
</style>

<div class="emp-page">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

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

    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#93A3BC" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
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
                    $roleColor= $colors[$role] ?? ['#5A6A85','rgba(122,138,154,0.1)'];
                    $roleCls  = match($role) { 'Customer Service'=>'role-cs', 'Teacher'=>'role-teacher', 'Student Care'=>'role-sc', 'Admin'=>'role-admin', default=>'role-cs' };
                @endphp
                <tr data-name="{{ strtolower($emp->full_name) }}" data-email="{{ strtolower($emp->user?->email ?? '') }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar" style="background:{{ $roleColor[1] }};color:{{ $roleColor[0] }}">{{ $initial }}</div>
                            <div>
                                <div style="font-weight:500;color:#16233F;font-size:13px">{{ $emp->full_name }}</div>
                                <div style="font-size:10px;color:#93A3BC;margin-top:1px">{{ $emp->user?->email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="role-badge {{ $roleCls }}">{{ $role }}</span></td>
                    <td style="font-size:12px;color:#5A6A85">{{ $emp->branch?->name ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:12px">
                        {{ $emp->salary ? number_format($emp->salary).' LE' : '—' }}
                    </td>
                    <td>
                        <div class="status-dot">
                            <div class="dot {{ $emp->status === 'Active' ? 'dot-active' : 'dot-inactive' }}"></div>
                            {{ $emp->status }}
                        </div>
                    </td>
                    <td style="font-size:11px;color:#93A3BC">
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
                    <td colspan="7" style="text-align:center;padding:48px;color:#93A3BC;font-size:13px">
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