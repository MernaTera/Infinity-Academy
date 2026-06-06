@extends('admin.layouts.app')
@section('title', 'Students')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--purple:#7F77DD;--purple-l:rgba(127,119,221,0.08);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.st-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0 0 24px;}

/* KPIs */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden;}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,var(--blue));}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:6px;}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:2px;color:var(--kc,var(--blue));line-height:1;}

/* Filters */
.filter-bar{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;}
.filter-field{display:flex;flex-direction:column;gap:5px;min-width:160px;}
.filter-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
.filter-control{padding:8px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;appearance:none;}
.filter-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-l);}
.search-wrap{position:relative;flex:1;min-width:220px;}
.search-wrap input{width:100%;padding:8px 12px 8px 36px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;}
.search-wrap input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-l);}
.search-wrap svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--faint);}
.btn-filter{padding:8px 20px;background:var(--blue);border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:2px;cursor:pointer;}
.btn-reset{padding:8px 16px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:1px;text-decoration:none;display:inline-flex;align-items:center;}

/* Table */
.tbl-card{background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(27,79,168,0.04);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{padding:11px 16px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid rgba(27,79,168,0.04);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:rgba(27,79,168,0.02);}
.tbl td{padding:13px 16px;font-size:13px;color:var(--muted);vertical-align:middle;}

/* Status badges */
.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0;}
.badge-active{color:var(--green);background:var(--green-l);border:1px solid rgba(5,150,105,0.2);}
.badge-restricted{color:var(--red);background:var(--red-l);border:1px solid rgba(220,38,38,0.15);}
.badge-archived{color:var(--faint);background:rgba(170,184,200,0.1);border:1px solid rgba(170,184,200,0.2);}
.badge-dropped{color:var(--orange);background:var(--orange-l);border:1px solid rgba(245,145,30,0.2);}
.badge-waiting{color:#7F77DD;background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}
.badge-postponed{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2);}
.badge-completed{color:var(--purple);background:var(--purple-l);border:1px solid rgba(127,119,221,0.2);}

/* Balance indicator */
.balance-wrap{display:flex;align-items:center;gap:6px;}
.balance-bar{width:60px;height:4px;background:#F0F0F0;border-radius:2px;overflow:hidden;flex-shrink:0;}
.balance-fill{height:4px;border-radius:2px;}

/* Buttons */
.btn-view{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid rgba(27,79,168,0.25);color:var(--blue);background:transparent;text-decoration:none;transition:all 0.2s;}
.btn-view:hover{background:var(--blue-l);text-decoration:none;}

/* Avatar */
.stu-avatar{width:34px;height:34px;border-radius:50%;background:var(--blue-l);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:13px;color:var(--blue);flex-shrink:0;letter-spacing:1px;}

@media(max-width:900px){.kpi-grid{grid-template-columns:1fr 1fr;}.st-page{padding:18px 14px;}}
</style>

<div class="st-page">
    <div class="page-eyebrow">Admin Panel</div>
    <h1 class="page-title">Students</h1>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:var(--blue)"><div class="kpi-label">Total</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--green)"><div class="kpi-label">Active</div><div class="kpi-val">{{ $stats['active'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--faint)"><div class="kpi-label">Archived</div><div class="kpi-val">{{ $stats['archived'] }}</div></div>
        <div class="kpi-card" style="--kc:var(--orange)"><div class="kpi-label">Dropped</div><div class="kpi-val">{{ $stats['dropped'] }}</div></div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.students.index') }}">
        <div class="filter-bar">
            <div class="search-wrap">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="search" placeholder="Search name, phone, email..." value="{{ $search }}">
            </div>
            <div class="filter-field">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-control">
                    <option value="">All Statuses</option>
                    <option value="Active"   {{ $status === 'Active'   ? 'selected' : '' }}>Active</option>
                    <option value="Archived" {{ $status === 'Archived' ? 'selected' : '' }}>Archived</option>
                    <option value="Dropped"  {{ $status === 'Dropped'  ? 'selected' : '' }}>Dropped</option>
                </select>
            </div>
            <div class="filter-field">
                <label class="filter-label">CS User</label>
                <select name="cs_id" class="filter-control">
                    <option value="">All CS</option>
                    @foreach($csUsers as $cs)
                    <option value="{{ $cs->employee_id }}" {{ $csFilter == $cs->employee_id ? 'selected' : '' }}>
                        {{ $cs->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('admin.students.index') }}" class="btn-reset">Reset</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="tbl-card">
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
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
                        $barColor = $student->remaining > 0 ? '#F5911E' : '#059669';
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
                        <td style="font-family:monospace;font-size:12px;">
                            {{ $student->phones->where('is_primary',true)->first()?->phone_number ?? '—' }}
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
                        <td style="font-size:12px;">{{ $e?->teacher?->full_name ?? '—' }}</td>
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
                            <a href="{{ route('admin.students.show', $student->student_id) }}" class="btn-view">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:48px;color:var(--faint);font-size:13px;">
                            No students found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:flex-end;">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection