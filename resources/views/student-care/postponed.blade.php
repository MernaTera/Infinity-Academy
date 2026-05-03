@extends('student-care.layouts.app')
@section('title', 'Postponed Students')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.pp-page{;min-height:100vh;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#1B4FA8;margin:0}
.page-header{margin-bottom:28px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.kpi-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:6px;padding:16px 20px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--kc,#1B4FA8)}
.kpi-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;margin-bottom:5px}
.kpi-val{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--kc,#1B4FA8);line-height:1}
.kpi-sub{font-size:9px;color:#AAB8C8;margin-top:3px}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#F5911E;margin-bottom:14px;display:block}

/* Tab Nav */
.tab-nav{display:flex;gap:2px;margin-bottom:20px;border-bottom:1px solid rgba(27,79,168,0.08)}
.tab-btn{padding:10px 20px;font-size:10px;letter-spacing:2px;text-transform:uppercase;background:transparent;border:none;cursor:pointer;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-weight:500;position:relative;transition:color 0.2s;border-radius:4px 4px 0 0}
.tab-btn::after{content:'';position:absolute;bottom:-1px;left:0;right:0;height:2px;background:#1B4FA8;transform:scaleX(0);transition:transform 0.3s cubic-bezier(0.16,1,0.3,1)}
.tab-btn:hover{color:#1B4FA8}
.tab-btn.active{color:#1B4FA8}
.tab-btn.active::after{transform:scaleX(1)}
.tab-count{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:rgba(27,79,168,0.1);font-size:9px;color:#1B4FA8;margin-left:6px;font-weight:600}

/* Search */
.toolbar{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none}
.search-input{width:100%;padding:10px 14px 10px 38px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.search-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}

/* Cards */
.postponed-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px}
.pp-card{background:#fff;border:1px solid rgba(27,79,168,0.1);border-radius:8px;overflow:hidden;position:relative;transition:box-shadow 0.2s}
.pp-card:hover{box-shadow:0 4px 20px rgba(27,79,168,0.08)}
.pp-card.status-active::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#C47010,transparent)}
.pp-card.status-expired::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#DC2626,transparent)}
.pp-card.expiring-soon{border-color:rgba(220,38,38,0.2)}

.pc-header{padding:16px 18px 12px;border-bottom:1px solid rgba(27,79,168,0.06);display:flex;align-items:flex-start;justify-content:space-between;gap:10px}
.pc-student{font-weight:500;color:#1A2A4A;font-size:14px}
.pc-course{font-size:11px;color:#7A8A9A;margin-top:3px}

.badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:3px;font-weight:500;white-space:nowrap}
.badge::before{content:'';width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-active{color:#C47010;background:rgba(245,145,30,0.08);border:1px solid rgba(245,145,30,0.2)}
.badge-expired{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15)}
.badge-returned{color:#059669;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.15)}
.badge-soon{color:#DC2626;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.6}}

.pc-body{padding:14px 18px}
.pc-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.pc-meta-label{font-size:8px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-bottom:3px}
.pc-meta-val{font-size:12px;color:#1A2A4A;font-weight:500}
.pc-meta-val.orange{color:#C47010}
.pc-meta-val.red{color:#DC2626}

/* Timeline */
.timeline{display:flex;align-items:center;gap:0;margin-bottom:14px;padding:10px 0}
.tl-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.tl-dot-start{background:#1B4FA8}
.tl-dot-end{background:#059669}
.tl-dot-expired{background:#DC2626}
.tl-line{flex:1;height:2px;position:relative}
.tl-line-inner{position:absolute;top:0;left:0;height:100%;border-radius:1px;transition:width 0.6s}
.tl-line-bg{background:#F0F0F0;width:100%;height:2px;border-radius:1px}
.tl-labels{display:flex;justify-content:space-between;font-size:9px;color:#AAB8C8;margin-bottom:10px}

/* Progress hours */
.hours-prog{background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;margin-top:4px}
.hours-prog-fill{height:5px;border-radius:3px;background:linear-gradient(90deg,#1B4FA8,#2D6FDB)}

/* Reason */
.reason-box{background:rgba(245,145,30,0.04);border:1px solid rgba(245,145,30,0.12);border-radius:4px;padding:8px 10px;font-size:11px;color:#C47010;line-height:1.4;margin-bottom:12px}

.pc-footer{padding:10px 18px;border-top:1px solid rgba(27,79,168,0.06);display:flex;gap:8px}

.btn-sm{display:inline-flex;align-items:center;gap:4px;padding:6px 14px;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;border-radius:3px;border:1px solid;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap}
.btn-resume{color:#059669;border-color:rgba(5,150,105,0.25)}
.btn-resume:hover{background:rgba(5,150,105,0.07)}
.btn-expire{color:#DC2626;border-color:rgba(220,38,38,0.2)}
.btn-expire:hover{background:rgba(220,38,38,0.06)}

/* Empty */
.empty-state{text-align:center;padding:60px 24px;color:#AAB8C8}
.empty-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;margin-bottom:6px}

/* Confirm Modal */
#confirmModal{display:none;position:fixed;inset:0;background:rgba(209,216,231,0.55);backdrop-filter:blur(6px);align-items:center;justify-content:center;z-index:999;padding:20px}
#confirmModal.show{display:flex}
.modal-box{width:100%;max-width:420px;background:#F8F6F2;border:1px solid rgba(27,79,168,0.15);border-radius:8px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(27,79,168,0.18)}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#F5911E,#1B4FA8,transparent)}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid rgba(27,79,168,0.07)}
.modal-title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;color:#1B4FA8}
.modal-body{padding:16px 22px;font-size:13px;color:#7A8A9A;line-height:1.6}
.modal-footer{padding:12px 22px 18px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}

@media(max-width:768px){.pp-page{padding:18px 14px}.kpi-grid{grid-template-columns:repeat(2,1fr)}.postponed-grid{grid-template-columns:1fr}}
</style>

<div class="pp-page">

    <div class="page-header">
        <div class="page-eyebrow">Student Care</div>
        <h1 class="page-title">Postponed Students</h1>
    </div>

    @if(session('success'))
    <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);color:#059669;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:#DC2626;padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px">{{ session('error') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card" style="--kc:#C47010">
            <div class="kpi-label">Active Postponements</div>
            <div class="kpi-val">{{ $stats['active'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#DC2626">
            <div class="kpi-label">Expiring Soon</div>
            <div class="kpi-val">{{ $stats['expiring_soon'] }}</div>
            <div class="kpi-sub">within 7 days</div>
        </div>
        <div class="kpi-card" style="--kc:#7A8A9A">
            <div class="kpi-label">Expired</div>
            <div class="kpi-val">{{ $stats['expired'] }}</div>
        </div>
        <div class="kpi-card" style="--kc:#059669">
            <div class="kpi-label">Returned</div>
            <div class="kpi-val">{{ $stats['returned'] }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tab-nav">
        <button class="tab-btn active" onclick="showTab('groupTab', this)">
            Group
            <span class="tab-count">{{ $groupPostponed->count() }}</span>
        </button>
        <button class="tab-btn" onclick="showTab('privateTab', this)">
            Private
            <span class="tab-count">{{ $privatePostponed->count() }}</span>
        </button>
    </div>

    {{-- Search --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="ppSearch" class="search-input" placeholder="Search by student or course...">
        </div>
    </div>

    {{-- ══ GROUP TAB ══ --}}
    <div id="groupTab">
        @if($groupPostponed->isEmpty())
        <div class="empty-state">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1" style="margin:0 auto 14px;display:block">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <div class="empty-title">No Group Postponements</div>
            <div style="font-size:12px">No active group postponements found</div>
        </div>
        @else
        <div class="postponed-grid" id="groupGrid">
            @foreach($groupPostponed as $pp)
            @php
                $enrollment   = $pp->enrollment;
                $start        = \Carbon\Carbon::parse($pp->start_date);
                $end          = \Carbon\Carbon::parse($pp->expected_return_date);
                $today        = now();
                $totalDays    = max(1, $start->diffInDays($end));
                $elapsed      = max(0, min($totalDays, $start->diffInDays($today)));
                $pct          = round($elapsed / $totalDays * 100);
                $daysLeft     = max(0, (int)$today->diffInDays($end, false));
                $isExpiringSoon = $pp->status === 'Active' && $daysLeft <= 7;
                $maxDays      = 90;
                $totalPostpDays = $start->diffInDays($end);
                $overMax      = $totalPostpDays > $maxDays;

                // Sessions
                $totalSessions    = $enrollment->courseInstance?->sessions?->count() ?? 0;
                $completedSessions= $enrollment->attendances?->count() ?? 0;
                $remainingSessions= $totalSessions - $completedSessions;

                $cardClass = $pp->status === 'Expired' ? 'status-expired' :
                             ($isExpiringSoon ? 'pp-card status-active expiring-soon' : 'status-active');
            @endphp
            <div class="pp-card {{ $cardClass }}"
                 data-name="{{ strtolower($enrollment->student?->full_name ?? '') }}"
                 data-course="{{ strtolower($enrollment->courseInstance?->courseTemplate?->name ?? '') }}">

                <div class="pc-header">
                    <div>
                        <div class="pc-student">{{ $enrollment->student?->full_name ?? '—' }}</div>
                        <div class="pc-course">{{ $enrollment->courseInstance?->courseTemplate?->name ?? '—' }}</div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-end">
                        @if($pp->status === 'Active')
                            @if($isExpiringSoon)
                                <span class="badge badge-soon">⚠ {{ $daysLeft }}d left</span>
                            @else
                                <span class="badge badge-active">Active</span>
                            @endif
                        @elseif($pp->status === 'Expired')
                            <span class="badge badge-expired">Expired</span>
                        @else
                            <span class="badge badge-returned">Returned</span>
                        @endif
                        @if($overMax)
                        <span style="font-size:9px;color:#DC2626;letter-spacing:1px;text-transform:uppercase">Exceeds 3 months</span>
                        @endif
                    </div>
                </div>

                <div class="pc-body">

                    {{-- Timeline --}}
                    <div class="tl-labels">
                        <span>{{ $start->format('d M') }}</span>
                        <span>Return: {{ $end->format('d M Y') }}</span>
                    </div>
                    <div class="timeline">
                        <div class="tl-dot tl-dot-start"></div>
                        <div class="tl-line">
                            <div class="tl-line-bg"></div>
                            <div class="tl-line-inner"
                                 style="width:{{ $pct }}%;background:{{ $pp->status === 'Expired' ? '#DC2626' : '#C47010' }}">
                            </div>
                        </div>
                        <div class="tl-dot {{ $pp->status === 'Expired' ? 'tl-dot-expired' : 'tl-dot-end' }}"></div>
                    </div>

                    {{-- Meta --}}
                    <div class="pc-meta-grid">
                        <div>
                            <div class="pc-meta-label">Remaining Sessions</div>
                            <div class="pc-meta-val orange">{{ $remainingSessions }} sessions</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Postponement Duration</div>
                            <div class="pc-meta-val {{ $overMax ? 'red' : '' }}">{{ $totalPostpDays }} days</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Start Date</div>
                            <div class="pc-meta-val">{{ $start->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Expected Return</div>
                            <div class="pc-meta-val {{ $isExpiringSoon ? 'red' : '' }}">{{ $end->format('d M Y') }}</div>
                        </div>
                    </div>

                    {{-- Reason --}}
                    @if($pp->reason)
                    <div class="reason-box">
                        <strong>Reason:</strong> {{ $pp->reason }}
                    </div>
                    @endif

                    {{-- By --}}
                    <div style="font-size:10px;color:#AAB8C8">
                        Postponed by {{ $pp->createdBy?->full_name ?? '—' }}
                        · {{ \Carbon\Carbon::parse($pp->created_at)->format('d M Y') }}
                    </div>

                </div>

                @if($pp->status === 'Active')
                <div class="pc-footer">
                    <button class="btn-sm btn-resume"
                        onclick="openConfirm({{ $pp->postponement_id }}, 'resume', '{{ addslashes($enrollment->student?->full_name) }}')">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Resume Student
                    </button>
                    <button class="btn-sm btn-expire"
                        onclick="openConfirm({{ $pp->postponement_id }}, 'expire', '{{ addslashes($enrollment->student?->full_name) }}')">
                        Mark Expired
                    </button>
                </div>
                @elseif($pp->status === 'Expired')
                <div class="pc-footer">
                    <span style="font-size:10px;color:#DC2626;letter-spacing:1px;text-transform:uppercase">
                        ✕ Enrollment Expired — No Refund
                    </span>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ══ PRIVATE TAB ══ --}}
    <div id="privateTab" style="display:none">
        @if($privatePostponed->isEmpty())
        <div class="empty-state">
            <div class="empty-title">No Private Postponements</div>
            <div style="font-size:12px">No active private postponements found</div>
        </div>
        @else
        <div class="postponed-grid" id="privateGrid">
            @foreach($privatePostponed as $pp)
            @php
                $enrollment  = $pp->enrollment;
                $start       = \Carbon\Carbon::parse($pp->start_date);
                $end         = \Carbon\Carbon::parse($pp->expected_return_date);
                $today       = now();
                $totalDays   = max(1, $start->diffInDays($end));
                $elapsed     = max(0, min($totalDays, $start->diffInDays($today)));
                $pct         = round($elapsed / $totalDays * 100);
                $daysLeft    = max(0, (int)$today->diffInDays($end, false));
                $isExpiringSoon = $pp->status === 'Active' && $daysLeft <= 7;

                $totalHours     = $enrollment->courseInstance?->total_hours ?? 0;
                $hoursRemaining = $enrollment->hours_remaining ?? 0;
                $hoursUsed      = $totalHours - $hoursRemaining;
                $hoursPct       = $totalHours > 0 ? round($hoursUsed / $totalHours * 100) : 0;
            @endphp
            <div class="pp-card {{ $pp->status === 'Expired' ? 'status-expired' : ($isExpiringSoon ? 'status-active expiring-soon' : 'status-active') }}"
                 data-name="{{ strtolower($enrollment->student?->full_name ?? '') }}"
                 data-course="{{ strtolower($enrollment->courseInstance?->courseTemplate?->name ?? '') }}">

                <div class="pc-header">
                    <div>
                        <div class="pc-student">{{ $enrollment->student?->full_name ?? '—' }}</div>
                        <div class="pc-course">
                            {{ $enrollment->courseInstance?->courseTemplate?->name ?? '—' }}
                            <span style="color:#1B4FA8;font-weight:500"> · Private</span>
                        </div>
                    </div>
                    @if($pp->status === 'Active')
                        @if($isExpiringSoon)
                            <span class="badge badge-soon">⚠ {{ $daysLeft }}d left</span>
                        @else
                            <span class="badge badge-active">Active</span>
                        @endif
                    @elseif($pp->status === 'Expired')
                        <span class="badge badge-expired">Expired</span>
                    @endif
                </div>

                <div class="pc-body">

                    {{-- Hours --}}
                    <div class="pc-meta-grid" style="margin-bottom:10px">
                        <div>
                            <div class="pc-meta-label">Total Hours</div>
                            <div class="pc-meta-val" style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#1B4FA8;letter-spacing:1px">{{ $totalHours }}h</div>
                        </div>
                        <div>
                            <div class="pc-meta-label">Used / Remaining</div>
                            <div class="pc-meta-val">{{ $hoursUsed }}h / <span class="orange">{{ $hoursRemaining }}h</span></div>
                        </div>
                    </div>

                    {{-- Hours Progress --}}
                    <div style="margin-bottom:14px">
                        <div style="display:flex;justify-content:space-between;font-size:9px;color:#AAB8C8;margin-bottom:4px">
                            <span>Used: {{ $hoursPct }}%</span>
                            <span>Remaining: {{ 100 - $hoursPct }}%</span>
                        </div>
                        <div class="hours-prog">
                            <div class="hours-prog-fill" style="width:{{ $hoursPct }}%"></div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="tl-labels">
                        <span>{{ $start->format('d M') }}</span>
                        <span>Return: {{ $end->format('d M Y') }}</span>
                    </div>
                    <div class="timeline">
                        <div class="tl-dot tl-dot-start"></div>
                        <div class="tl-line">
                            <div class="tl-line-bg"></div>
                            <div class="tl-line-inner" style="width:{{ $pct }}%;background:#C47010"></div>
                        </div>
                        <div class="tl-dot {{ $pp->status === 'Expired' ? 'tl-dot-expired' : 'tl-dot-end' }}"></div>
                    </div>

                    @if($pp->reason)
                    <div class="reason-box">{{ $pp->reason }}</div>
                    @endif

                    <div style="font-size:10px;color:#AAB8C8">
                        By {{ $pp->createdBy?->full_name ?? '—' }}
                        · {{ \Carbon\Carbon::parse($pp->created_at)->format('d M Y') }}
                    </div>
                </div>

                @if($pp->status === 'Active')
                <div class="pc-footer">
                    <button class="btn-sm btn-resume"
                        onclick="openConfirm({{ $pp->postponement_id }}, 'resume', '{{ addslashes($enrollment->student?->full_name) }}')">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Resume Student
                    </button>
                    <button class="btn-sm btn-expire"
                        onclick="openConfirm({{ $pp->postponement_id }}, 'expire', '{{ addslashes($enrollment->student?->full_name) }}')">
                        Mark Expired
                    </button>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Confirm Modal --}}
<div id="confirmModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Confirm Action</div>
        </div>
        <div class="modal-body" id="modalBody">Are you sure?</div>
        <div class="modal-footer">
            <button type="button" onclick="closeConfirm()"
                style="padding:9px 18px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;cursor:pointer">
                Cancel
            </button>
            <form id="confirmForm" method="POST" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" id="confirmBtn"
                    style="padding:10px 22px;background:#1B4FA8;border:none;border-radius:4px;color:#fff;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;cursor:pointer">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabId, btn) {
    document.getElementById('groupTab').style.display   = 'none';
    document.getElementById('privateTab').style.display = 'none';
    document.getElementById(tabId).style.display        = 'block';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function openConfirm(id, action, studentName) {
    const isResume = action === 'resume';
    document.getElementById('modalTitle').textContent = isResume ? 'Resume Student' : 'Mark as Expired';
    document.getElementById('modalBody').textContent  = isResume
        ? `Resume ${studentName}? Their enrollment will be set back to Active.`
        : `Mark ${studentName}'s postponement as Expired? Their enrollment will be cancelled with no refund.`;
    document.getElementById('confirmBtn').textContent = isResume ? 'Resume' : 'Expire';
    document.getElementById('confirmBtn').style.background = isResume ? '#059669' : '#DC2626';
    document.getElementById('confirmForm').action = isResume
        ? `/student-care/postponed/${id}/resume`
        : `/student-care/postponed/${id}/expire`;
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirm() {
    document.getElementById('confirmModal').classList.remove('show');
}

document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});

// Search
document.getElementById('ppSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.pp-card[data-name]').forEach(card => {
        const match = card.dataset.name.includes(q) || card.dataset.course.includes(q);
        card.style.display = match ? '' : 'none';
    });
});
</script>
@endsection