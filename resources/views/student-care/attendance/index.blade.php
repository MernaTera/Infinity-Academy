@extends('student-care.layouts.app')

@section('title', 'Attendance')

@section('content')

@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endonce

<style>
    body, .att-page * { font-family: 'DM Sans', sans-serif; }
    body { min-width: fit-content; }

    .att-page {
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

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 18px; background: transparent;
        border: 1px solid rgba(27,79,168,0.2); border-radius: 4px;
        color: #7A8A9A; font-size: 10px; letter-spacing: 2.5px;
        text-transform: uppercase; text-decoration: none;
        transition: all 0.3s; font-family: 'DM Sans', sans-serif;
    }
    .btn-back:hover { border-color: #1B4FA8; color: #1B4FA8; text-decoration: none; }

    /* ── SESSION INFO CARD ── */
    .session-card {
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px; padding: 20px 24px;
        margin-bottom: 22px;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
        position: relative; overflow: hidden;
    }
    .session-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #1B4FA8, transparent);
    }
    .session-info-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px;
    }
    .session-info-item {}
    .session-info-label { font-size: 8px; letter-spacing: 2.5px; text-transform: uppercase; color: #AAB8C8; margin-bottom: 4px; }
    .session-info-value { font-size: 13px; color: #1A2A4A; font-weight: 500; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 9px; letter-spacing: 1.2px; text-transform: uppercase;
        padding: 4px 9px; border-radius: 3px; white-space: nowrap; font-weight: 500;
    }
    .status-badge::before {
        content: ''; width: 4px; height: 4px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .status-scheduled { color: #1B6FA8; background: rgba(27,111,168,0.08); border: 1px solid rgba(27,111,168,0.2); }
    .status-completed { color: #15803D; background: rgba(21,128,61,0.08);  border: 1px solid rgba(21,128,61,0.2); }
    .status-cancelled { color: #DC2626; background: rgba(220,38,38,0.06);  border: 1px solid rgba(220,38,38,0.2); }

    /* ── ATTENDANCE TABLE ── */
    .table-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(27,79,168,0.1);
        border-radius: 6px; overflow: hidden;
        box-shadow: 0 4px 24px rgba(27,79,168,0.06);
        margin-bottom: 20px;
    }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card thead tr { border-bottom: 1px solid rgba(27,79,168,0.08); }
    .table-card thead th {
        padding: 12px 16px; font-size: 9px; letter-spacing: 2.5px;
        text-transform: uppercase; color: #7A8A9A; font-weight: 500;
        background: rgba(27,79,168,0.02); text-align: left;
    }
    .table-card tbody tr { border-bottom: 1px solid rgba(27,79,168,0.04); transition: background 0.2s; }
    .table-card tbody tr:hover { background: rgba(27,79,168,0.02); }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody td { padding: 12px 16px; font-size: 13px; color: #4A5A7A; vertical-align: middle; }

    .st-name  { font-weight: 500; color: #1A2A4A; font-size: 13px; }
    .st-phone { font-size: 11px; color: #7A8A9A; font-family: monospace; margin-top: 2px; }

    /* ── ATTENDANCE TOGGLE ── */
    .att-toggle {
        display: flex; gap: 6px;
    }
    .att-radio-label {
        display: flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 4px; cursor: pointer;
        font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;
        border: 1.5px solid; transition: all 0.2s; font-weight: 500;
    }
    .att-radio-label.present {
        color: #15803D; border-color: rgba(21,128,61,0.25);
        background: transparent;
    }
    .att-radio-label.present:has(input:checked) {
        background: rgba(21,128,61,0.08); border-color: #15803D;
    }
    .att-radio-label.absent {
        color: #DC2626; border-color: rgba(220,38,38,0.25);
        background: transparent;
    }
    .att-radio-label.absent:has(input:checked) {
        background: rgba(220,38,38,0.06); border-color: #DC2626;
    }
    .att-radio-label input {
        position: absolute;
        opacity: 0;
    }

    /* ── FOOTER ── */
    .form-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding-top: 8px;
    }

    .btn-cancel-att {
        padding: 10px 22px; background: transparent;
        border: 1px solid rgba(27,79,168,0.15); border-radius: 4px;
        color: #7A8A9A; font-family: 'DM Sans', sans-serif;
        font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
        text-decoration: none; transition: all 0.3s;
    }
    .btn-cancel-att:hover { border-color: rgba(27,79,168,0.3); color: #1B4FA8; text-decoration: none; }

    .btn-save-att {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 28px; background: transparent;
        border: 1.5px solid #1B4FA8; border-radius: 4px;
        color: #1B4FA8; font-family: 'Bebas Neue', sans-serif;
        font-size: 14px; letter-spacing: 4px;
        cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s;
    }
    .btn-save-att::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-save-att:hover::before { transform: scaleX(1); }
    .btn-save-att:hover { color: #fff; }
    .btn-save-att span, .btn-save-att svg { position: relative; z-index: 1; }

    .empty-state { padding: 60px 24px; text-align: center; }
    .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 18px; letter-spacing: 4px; color: #7A8A9A; margin-bottom: 6px; }
    .empty-sub   { font-size: 12px; color: #AAB8C8; }

    @media (max-width: 768px) { .att-page { padding: 20px 14px; } }
</style>

<div class="att-page">

    {{-- ── HEADER ── --}}
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Attendance</div>
            <h1 class="page-title">
                Session {{ $session->session_number }}
            </h1>
            <p class="page-subtitle">
                {{ $session->courseInstance->courseTemplate->name ?? '—' }}
            </p>
        </div>
        <a href="{{ route('student-care.instances.show', $session->course_instance_id) }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Instance
        </a>
    </div>

    {{-- ── SESSION INFO ── --}}
    @php
        $statusClass = match($session->status) {
            'Scheduled' => 'status-scheduled',
            'Completed' => 'status-completed',
            'Cancelled' => 'status-cancelled',
            default     => 'status-scheduled',
        };
    @endphp

    <div class="session-card">
        <div class="session-info-grid">
            <div class="session-info-item">
                <div class="session-info-label">Date</div>
                <div class="session-info-value">
                    {{ $session->session_date ? \Carbon\Carbon::parse($session->session_date)->format('d M Y') : '—' }}
                </div>
            </div>
            <div class="session-info-item">
                <div class="session-info-label">Time</div>
                <div class="session-info-value">
                    @if($session->start_time && $session->end_time)
                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                        → {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="session-info-item">
                <div class="session-info-label">Teacher</div>
                <div class="session-info-value">
                    {{ $session->courseInstance->teacher->employee->full_name ?? '—' }}
                </div>
            </div>
            <div class="session-info-item">
                <div class="session-info-label">Students</div>
                <div class="session-info-value">
                    {{ $session->courseInstance->enrollments->count() }}
                </div>
            </div>
            <div class="session-info-item">
                <div class="session-info-label">Status</div>
                <div class="session-info-value">
                    <span class="status-badge {{ $statusClass }}">{{ $session->status }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ATTENDANCE FORM ── --}}
    @if($session->status === 'Cancelled')
        <div style="text-align:center;padding:40px;color:#DC2626;font-size:13px;">
            This session has been cancelled.
        </div>
    @else

    <form method="POST" action="{{ route('student-care.attendance.store') }}">
        @csrf
        <input type="hidden" name="session_id" value="{{ $session->course_session_id }}">

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Phone</th>
                        <th>Enrollment Status</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($session->courseInstance->enrollments as $i => $enrollment)
                    @php
                        $existing = $enrollment->attendances
                            ->where('course_session_id', $session->course_session_id)
                            ->first();
                        $isRestricted = $enrollment->status === 'Restricted';
                        $isCancelled  = $enrollment->status === 'Cancelled';
                    @endphp
                    <tr style="{{ $isCancelled ? 'opacity:0.4;' : '' }}">
                        <td style="color:#AAB8C8;font-size:11px;">{{ $i + 1 }}</td>
                        <td>
                            <div class="st-name">{{ $enrollment->student->full_name ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="st-phone">
                                {{ $enrollment->student->phones->first()->phone_number ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <span style="font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                         color:{{ $isRestricted ? '#DC2626' : ($isCancelled ? '#9A8A7A' : '#15803D') }};">
                                {{ $enrollment->status }}
                            </span>
                        </td>
                        <td>
                            @if($isCancelled)
                                <span style="font-size:10px;color:#AAB8C8;">N/A</span>
                                <input type="hidden" name="attendance[{{ $enrollment->enrollment_id }}]" value="Absent">
                            @else
                                <div class="att-toggle">
                                    <label class="att-radio-label present">
                                        <input type="radio"
                                               name="attendance[{{ $enrollment->enrollment_id }}]"
                                               value="Present"
                                               {{ ($existing && $existing->status === 'Present') || (!$existing && !$isRestricted) ? 'checked' : '' }}
                                               {{ $isRestricted ? 'disabled' : '' }}>
                                               @if($isRestricted)
                                                    <input type="hidden" name="attendance[{{ $enrollment->enrollment_id }}]" value="Absent">
                                                @endif
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        Present
                                    </label>
                                    <label class="att-radio-label absent">
                                        <input type="radio"
                                               name="attendance[{{ $enrollment->enrollment_id }}]"
                                               value="Absent"
                                               {{ ($existing && $existing->status === 'Absent') || $isRestricted ? 'checked' : '' }}>
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        Absent
                                    </label>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-title">No Students</div>
                                <div class="empty-sub">No active enrollments in this instance</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($session->courseInstance->enrollments->count() > 0)
        <div class="form-footer">
            <a href="{{ route('student-care.instances.show', $session->course_instance_id) }}"
               class="btn-cancel-att">Cancel</a>
            <button type="submit" class="btn-save-att">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span>Save Attendance</span>
            </button>
        </div>
        @endif

    </form>
    @endif

</div>

@endsection