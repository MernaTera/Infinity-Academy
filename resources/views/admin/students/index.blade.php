@extends(auth()->user()->panelLayout())
@section('title', 'Students')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{
    --blue:#1B4FA8; --blue-2:#2D6FDB; --blue-dk:#12305F; --blue-l:rgba(27,79,168,0.06);
    --orange:#12305F; --orange-l:rgba(27,79,168,0.06);
    --green:#2D6FDB; --green-l:rgba(45,111,219,0.08);
    --red:#5A6A85; --red-l:rgba(90,106,133,0.08);
    --purple:#2D6FDB; --purple-l:rgba(45,111,219,0.08);
    --border:rgba(255,255,255,0.6); --line:rgba(27,79,168,0.08);
    --bg:#F8F6F2; --card:rgba(255,255,255,0.72);
    --text:#16233F; --muted:#5A6A85; --faint:#93A3BC;
    --glass-sh:0 12px 34px -14px rgba(23,45,90,0.2);
}
*{box-sizing:border-box;}
.st-page{
    min-height:100vh; padding:40px 34px 52px; font-family:'DM Sans',sans-serif; color:var(--text);
    background:#F8F6F2;
}
    .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.1; z-index:0; pointer-events:none; }
    .orb-2 { width:300px; height:300px; background:radial-gradient(circle,#1B4FA8,transparent 70%); top:30px; right:1%; }
    .orb-3 { width:380px; height:380px; background:radial-gradient(circle,#7C3AED,transparent 70%); bottom:-140px; left:35%; }

.page-eyebrow{font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--blue);margin-bottom:7px;font-weight:600;display:flex;align-items:center;gap:8px;}
.page-eyebrow::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--blue-2);box-shadow:0 0 8px var(--blue-2);}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:42px;letter-spacing:2px;color:var(--text);margin:0 0 24px;line-height:0.95;}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;}
.kpi-card{background:var(--card);-webkit-backdrop-filter:blur(20px) saturate(150%);backdrop-filter:blur(20px) saturate(150%);border:1px solid var(--border);border-radius:18px;padding:18px 20px;position:relative;overflow:hidden;box-shadow:var(--glass-sh);}
/* .kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--kc,var(--blue));} */
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;font-weight:600;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:1px;color:var(--kc,var(--blue));line-height:1;}
a.kpi-card{text-decoration:none;display:block;cursor:pointer;transition:transform .2s,box-shadow .2s;}
a.kpi-card:hover{transform:translateY(-3px);box-shadow:0 18px 42px -14px rgba(23,45,90,0.28);text-decoration:none;}
a.kpi-card.active{box-shadow:0 0 0 2px var(--kc,var(--blue)),var(--glass-sh);border-color:transparent;}
a.kpi-card.active::before{height:4px;}
.kpi-hint{font-size:8px;letter-spacing:1px;text-transform:uppercase;color:var(--faint);margin-top:4px;opacity:0;transition:opacity .15s;}
a.kpi-card:hover .kpi-hint{opacity:1;}

.filter-bar{background:var(--card);-webkit-backdrop-filter:blur(20px) saturate(150%);backdrop-filter:blur(20px) saturate(150%);border:1px solid var(--border);border-radius:18px;padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;box-shadow:var(--glass-sh);}
.filter-field{display:flex;flex-direction:column;gap:5px;min-width:160px;}
.filter-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600;}
.filter-control{padding:9px 13px;border:1px solid rgba(27,79,168,0.12);border-radius:11px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.6);outline:none;appearance:none;}
.filter-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.1);}
.search-wrap{position:relative;flex:1;min-width:220px;}
.search-wrap input{width:100%;padding:9px 13px 9px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:11px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:rgba(255,255,255,0.6);outline:none;}
.search-wrap input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.1);}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--faint);}
.btn-filter{padding:10px 22px;background:linear-gradient(120deg,var(--blue),var(--blue-2));border:none;border-radius:11px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;cursor:pointer;box-shadow:0 6px 16px rgba(27,79,168,0.28);}
.btn-reset{padding:9px 16px;background:rgba(255,255,255,0.5);border:1px solid var(--border);border-radius:11px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:1px;text-decoration:none;display:inline-flex;align-items:center;}
.btn-reset:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}
.search-wrap .search-clear{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--faint);text-decoration:none;font-size:17px;line-height:1;padding:0 2px;}
.search-wrap .search-clear:hover{color:var(--blue);}
.result-line{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;font-size:11px;color:var(--muted);flex-wrap:wrap;gap:6px;}
.result-line .rl-pill{color:var(--blue);background:var(--blue-l);border:1px solid var(--border);padding:2px 9px;border-radius:99px;font-size:10px;letter-spacing:.5px;}
.phone-cell{display:flex;flex-direction:column;gap:2px;}
.phone-cell .pc-row{display:flex;align-items:center;gap:5px;}
.phone-cell .pc-star{color:var(--blue-2);font-size:10px;line-height:1;}
.phone-cell .pc-more{font-size:9px;color:var(--faint);}

.tbl-card{background:rgba(255,255,255,0.72);border:1px solid var(--border);border-radius:20px;overflow:hidden;box-shadow:var(--glass-sh);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:15px 16px;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);text-align:left;font-weight:700;background:rgba(255,255,255,0.55);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.06);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.04);}
.tbl td{padding:14px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:4px 9px;border-radius:20px;font-weight:600;}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-active{color:var(--blue);background:var(--blue-l);border:1px solid rgba(27,79,168,0.2);}
.badge-restricted{color:var(--muted);background:rgba(90,106,133,0.1);border:1px solid rgba(90,106,133,0.2);}
.badge-archived{color:var(--faint);background:rgba(147,163,188,0.12);border:1px solid rgba(147,163,188,0.25);}
.badge-dropped{color:var(--muted);background:rgba(90,106,133,0.1);border:1px solid rgba(90,106,133,0.2);}
.badge-waiting{color:var(--blue-2);background:rgba(45,111,219,0.1);border:1px solid rgba(45,111,219,0.22);}
.badge-postponed{color:var(--blue-dk);background:rgba(18,48,95,0.08);border:1px solid rgba(18,48,95,0.2);}
.badge-completed{color:var(--blue);background:var(--blue-l);border:1px solid rgba(27,79,168,0.2);}

.balance-wrap{display:flex;align-items:center;gap:6px;}
.balance-bar{width:60px;height:5px;background:rgba(27,79,168,0.1);border-radius:3px;overflow:hidden;flex-shrink:0;}
.balance-fill{height:5px;border-radius:3px;}

.btn-view{display:inline-flex;align-items:center;gap:4px;padding:6px 13px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:9px;border:1px solid rgba(27,79,168,0.25);color:var(--blue);background:rgba(27,79,168,0.06);text-decoration:none;transition:all 0.2s;}
.btn-view:hover{background:var(--blue);color:#fff;text-decoration:none;}

.stu-avatar{width:34px;height:34px;border-radius:11px;background:rgba(27,79,168,0.1);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:13px;color:var(--blue);flex-shrink:0;letter-spacing:1px;}

@media(max-width:900px){.kpi-grid{grid-template-columns:1fr 1fr;}.st-page{padding:20px 14px 36px;}.page-title{font-size:34px;}}
</style>

<div class="st-page">
        <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-eyebrow">{{ auth()->user()->panelLabel() }}</div>
    <h1 class="page-title">Students</h1>

    
    @php $kpiBase = array_filter(['search' => $search, 'cs_id' => $csFilter]); @endphp
    <div class="kpi-grid">
        <a href="{{ route('students.index', $kpiBase) }}"
           class="kpi-card {{ !$status ? 'active' : '' }}" style="--kc:var(--blue)">
            <div class="kpi-label">Total</div><div class="kpi-val">{{ $stats['total'] }}</div>
            <div class="kpi-hint">Show all</div>
        </a>
        <a href="{{ route('students.index', array_merge($kpiBase, ['status' => 'Active'])) }}"
           class="kpi-card {{ $status === 'Active' ? 'active' : '' }}" style="--kc:var(--green)">
            <div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div>
            <div class="kpi-hint">Filter</div>
        </a>
        <a href="{{ route('students.index', array_merge($kpiBase, ['status' => 'Waiting'])) }}"
           class="kpi-card {{ $status === 'Waiting' ? 'active' : '' }}" style="--kc:var(--blue-2)">
            <div class="kpi-label">Waiting</div><div class="kpi-val">{{ $stats['waiting'] }}</div>
            <div class="kpi-hint">Filter</div>
        </a>
        <a href="{{ route('students.index', array_merge($kpiBase, ['status' => 'Completed'])) }}"
           class="kpi-card {{ $status === 'Completed' ? 'active' : '' }}" style="--kc:var(--orange)">
            <div class="kpi-label">Completed</div><div class="kpi-val">{{ $stats['completed'] }}</div>
            <div class="kpi-hint">Filter</div>
        </a>
    </div>

    
    <form method="GET" action="{{ route('students.index') }}" id="studentFilterForm">
        <div class="filter-bar">
            <div class="search-wrap">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="search" placeholder="Search name, phone, email, or INV-000012…" value="{{ $search }}">
                @if($search)
                    <a href="{{ route('students.index', array_filter(['status' => $status, 'cs_id' => $csFilter])) }}" class="search-clear" title="Clear search">×</a>
                @endif
            </div>
            <div class="filter-field">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-control" onchange="document.getElementById('studentFilterForm').submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending_Approval" {{ $status === 'Pending_Approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="Active"           {{ $status === 'Active'           ? 'selected' : '' }}>Active</option>
                    <option value="Restricted"       {{ $status === 'Restricted'       ? 'selected' : '' }}>Restricted</option>
                    <option value="Waiting"          {{ $status === 'Waiting'          ? 'selected' : '' }}>Waiting</option>
                    <option value="Postponed"        {{ $status === 'Postponed'        ? 'selected' : '' }}>Postponed</option>
                    <option value="Completed"        {{ $status === 'Completed'        ? 'selected' : '' }}>Completed</option>
                    <option value="Expired"          {{ $status === 'Expired'          ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="filter-field">
                <label class="filter-label">CS User</label>
                <select name="cs_id" class="filter-control" onchange="document.getElementById('studentFilterForm').submit()">
                    <option value="">All CS</option>
                    @foreach($csUsers as $cs)
                    <option value="{{ $cs->employee_id }}" {{ $csFilter == $cs->employee_id ? 'selected' : '' }}>
                        {{ $cs->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">Search</button>
            @if($search || $status || $csFilter)
                <a href="{{ route('students.index') }}" class="btn-reset">Clear all</a>
            @endif
        </div>
    </form>

    
    <div class="result-line">
        <div>
            Showing <strong style="color:var(--text);">{{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }}</strong>
            of <strong style="color:var(--text);">{{ $students->total() }}</strong> students
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            @if($status)<span class="rl-pill">Status: {{ str_replace('_', ' ', $status) }}</span>@endif
            @if($csFilter)<span class="rl-pill">CS: {{ $csUsers->firstWhere('employee_id', $csFilter)?->full_name ?? '#'.$csFilter }}</span>@endif
            @if($search)<span class="rl-pill">“{{ $search }}”</span>@endif
        </div>
    </div>

    
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Invoice</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Teacher</th>
                        <th>CS</th>
                        <th>Status</th>
                        <th>Total Fees</th>
                        <th>Balance</th>
                        <th>Registered</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $e        = $student->active_enrollment;
                        $paidPct  = $student->total_fees > 0 ? min(100, round($student->total_paid / $student->total_fees * 100)) : 0;
                        $barColor = $student->remaining > 0 ? '#2D6FDB' : '#1B4FA8';
                        $initials = strtoupper(substr($student->full_name ?? 'S', 0, 2));
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="stu-avatar">{{ $initials }}</div>
                                <div>
                                    <div style="font-weight:600;color:var(--text);">{{ $student->full_name }}</div>
                                    <div style="font-size:10px;color:var(--faint);">{{ $student->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                        @php
                            $latestEnr = $student->enrollments->first();
                        @endphp
                        @if($latestEnr)
                            <a href="{{ route('students.show', $student->student_id) }}" 
                            style="font-family:monospace;font-size:11px;color:var(--blue);text-decoration:none;font-weight:500;">
                                INV-{{ str_pad($latestEnr->enrollment_id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                            @if($student->enrollments->count() > 1)
                                <div style="font-size:9px;color:var(--faint);margin-top:2px;">+{{ $student->enrollments->count() - 1 }} more</div>
                            @endif
                        @else
                            <span style="color:var(--faint);font-size:11px;">—</span>
                        @endif
                    </td>
                        <td style="font-family:monospace;font-size:12px;">
                            @php $phones = $student->phones->sortByDesc('is_primary'); @endphp
                            @forelse($phones as $ph)
                                <div class="pc-row">
                                    @if($ph->is_primary)<span class="pc-star" title="Primary">★</span>@endif
                                    <span>{{ $ph->phone_number }}</span>
                                </div>
                            @empty
                                <span style="color:var(--faint);">—</span>
                            @endforelse
                        </td>
                        <td>
                            @if($e)
                            <div style="font-size:12px;color:var(--blue);font-weight:500;">{{ $e->courseTemplate?->name ?? '—' }}</div>
                            <div style="font-size:10px;color:var(--faint);">
                                {{ $e->level?->name ?? '' }}
                                @if($e->sublevel) › {{ $e->sublevel->name }} @endif
                            </div>
                            @else
                            <span style="color:var(--faint);font-size:11px;">No active enrollment</span>
                            @endif
                        </td>
                        <td style="font-size:12px;">{{ $e?->teacher?->name ?? $e?->courseInstance?->teacher?->name ?? '—' }}</td>
                        <td style="font-size:12px;">{{ $student->enrollments->first()?->createdByCs?->full_name ?? $student->lead?->owner?->full_name ?? '—' }}</td>
                        <td>
                            @php
                                $mainStatus = $student->enrollments->first()?->status ?? $student->status;
                                $mainStatuses = $student->enrollments->pluck('status')->unique()->join(', ');
                            @endphp
                            @foreach($student->enrollments->pluck('status')->unique() as $es)
                            <span class="badge badge-{{ strtolower(str_replace('_','-',$es)) }}">{{ $es }}</span>
                            @endforeach
                        </td>
                        <td style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--blue);">
                            {{ number_format($student->total_fees, 0) }} LE
                        </td>
                        <td>
                            <div class="balance-wrap">
                                <div class="balance-bar">
                                    <div class="balance-fill" style="width:{{ $paidPct }}%;background:{{ $barColor }};"></div>
                                </div>
                                <span style="font-size:11px;color:{{ $student->remaining > 0 ? 'var(--orange)' : 'var(--green)' }};">
                                    {{ $student->remaining > 0 ? number_format($student->remaining, 0) . ' LE' : '✓ Paid' }}
                                </span>
                            </div>
                        </td>
                        <td style="font-size:11px;color:var(--faint);">
                            {{ $student->created_at?->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('students.show', $student->student_id) }}" class="btn-view">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:48px;color:var(--faint);font-size:13px;">
                            No students found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    
    @if($students->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:flex-end;">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection