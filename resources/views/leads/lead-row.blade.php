{{-- Single lead row — used by both the main table and the registered table --}}

{{-- Notes popup --}}
<style>
    .notes-pill {
        display:inline-flex; align-items:center; gap:6px; max-width:200px;
        padding:5px 11px; border-radius:16px; cursor:pointer;
        background:rgba(27,79,168,0.06); border:1px solid rgba(27,79,168,0.15);
        color:#1B4FA8; font-size:11px; font-family:'DM Sans',sans-serif;
        transition:all 0.18s; text-align:left;
    }
    .notes-pill:hover { background:rgba(27,79,168,0.1); border-color:rgba(27,79,168,0.3); }
    .notes-pill-text { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .notes-overlay {
        display:none; position:fixed; inset:0; z-index:1000;
        background:rgba(10,20,40,0.5); backdrop-filter:blur(3px);
        align-items:center; justify-content:center; padding:20px;
    }
    .notes-overlay.open { display:flex; animation:notesFade 0.2s ease both; }
    @keyframes notesFade { from{opacity:0} to{opacity:1} }

    .notes-modal {
        background:#fff; border-radius:14px; max-width:520px; width:100%;
        max-height:80vh; display:flex; flex-direction:column; overflow:hidden;
        box-shadow:0 20px 60px rgba(15,31,61,0.3); animation:notesPop 0.25s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes notesPop { from{opacity:0;transform:scale(0.96) translateY(10px)} to{opacity:1;transform:none} }

    .notes-modal-head {
        background:linear-gradient(135deg,#0F1F3D,#243B69); padding:18px 22px;
        display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .notes-modal-head-title { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:2px; color:#fff; }
    .notes-modal-head-sub { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:#F5911E; margin-top:2px; }
    .notes-modal-close {
        background:rgba(255,255,255,0.1); border:none; width:30px; height:30px; border-radius:8px;
        color:#fff; cursor:pointer; font-size:18px; line-height:1; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; transition:background 0.2s;
    }
    .notes-modal-close:hover { background:rgba(255,255,255,0.2); }
    .notes-modal-body {
        padding:22px; overflow-y:auto; font-size:14px; line-height:1.65; color:#1A2A4A;
        white-space:pre-wrap; word-break:break-word;
    }
</style>
<div class="notes-overlay" id="notesOverlay" onclick="if(event.target===this)closeNotes()">
    <div class="notes-modal">
        <div class="notes-modal-head">
            <div>
                <div class="notes-modal-head-sub">Waiting List Note</div>
                <div class="notes-modal-head-title" id="notesModalStudent">Note</div>
            </div>
            <button type="button" class="notes-modal-close" onclick="closeNotes()">&times;</button>
        </div>
        <div class="notes-modal-body" id="notesModalBody"></div>
    </div>
</div>
                    <tr data-status="{{ $lead->status }}">
                        {{-- Name & Contact --}}
                        <td>
                            <div class="lead-name">{{ $lead->full_name }}</div>
                            <div class="lead-phone">{{ $lead->phone }}</div>
                            @if($lead->location)
                                <div class="lead-loc">📍 {{ $lead->location }}</div>
                            @endif
                        </td>

                        {{-- Source --}}
                        <td><span class="src-chip">{{ str_replace('_',' ',$lead->source) }}</span></td>

                        {{-- Degree --}}
                        <td><span class="degree-txt">{{ $lead->degree }}</span></td>

                        {{-- Course & Level --}}
                        <td>
                            @if($lead->courseTemplate)
                                <div class="course-name">{{ $lead->courseTemplate->name }}</div>
                                @if($lead->level)
                                    <div class="course-lvl">{{ $lead->level->name }}@if($lead->sublevel) · {{ $lead->sublevel->name }}@endif</div>
                                @endif
                            @else
                                <span class="dash-muted">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @php
                                $isPendingApproval = $isPendingApproval ?? false;
                                // A lead awaiting admin installment approval is locked: it can't
                                // be edited until the admin approves or rejects. It's rendered
                                // like the "Registered" lock (no dropdown, no chevron).
                                $isLocked = $isPendingApproval || $lead->status === 'Registered';
                                $statusClass = $isPendingApproval ? 'status-pending-approval' : match($lead->status) {
                                    'Waiting'        => 'status-waiting',
                                    'Call_Again'     => 'status-call_again',
                                    'Scheduled_Call' => 'status-scheduled',
                                    'Registered'     => 'status-registered',
                                    'Not_Interested' => 'status-not_interest',
                                    'Archived'       => 'status-archived',
                                    default          => 'status-default',
                                };
                                $statusLabel = $isPendingApproval ? 'Pending Approval' : str_replace('_',' ',$lead->status);
                            @endphp
                            <div style="position:relative;display:inline-block;">
                                <div class="status-badge {{ $statusClass }}"
                                    style="cursor:pointer;user-select:none;{{ $isLocked ? 'pointer-events:none;opacity:0.85;cursor:default;' : '' }}"
                                    @if(!$isLocked) onclick="toggleDropdown(this)" @endif>
                                    {{ $statusLabel }}
                                    @if(!$isLocked)
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                                    @endif
                                </div>
                                @unless($isLocked)
                                <div class="status-dropdown">
                                    @foreach(['Waiting','Call_Again','Registered'] as $s)
                                        @if($s === 'Registered')
                                            <div class="status-dropdown-item"
                                                style="{{ $lead->status === 'Registered' ? 'opacity:0.4;cursor:default;pointer-events:none;' : '' }}"
                                                @if($lead->status !== 'Registered')
                                                    data-status="{{ $s }}"
                                                    onclick="updateLeadStatus(document.querySelector('.status-select[data-id=\'{{ $lead->lead_id }}\']') ?? this, {{ $lead->lead_id }}, '{{ $s }}')"
                                                @endif>
                                                Registered
                                            </div>
                                        @else
                                            <div class="status-dropdown-item"
                                                data-status="{{ $s }}"
                                                onclick="updateLeadStatus(document.querySelector('.status-select[data-id=\'{{ $lead->lead_id }}\']') ?? this, {{ $lead->lead_id }}, '{{ $s }}')">
                                                {{ str_replace('_',' ',$s) }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @endunless
                            </div>
                            {{-- hidden select للـ function --}}
                            <select class="status-select" data-id="{{ $lead->lead_id }}" style="display:none;" data-no-enhance
                                    onchange="updateLeadStatus(this, {{ $lead->lead_id }})">
                                @foreach(['Waiting','Call_Again','Registered','Not_Interested','Archived'] as $status)
                                    <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>
                                        {{ str_replace('_',' ',$status) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- Start Preference --}}
                        <td><span class="pref-text">{{ $lead->start_preference_type ?? '—' }}</span></td>
                        <td>
                            @if($lead->start_preference_type === 'Specific Date' && $lead->start_preference_date)
                                <div class="call-date" style="color:var(--orange);">{{ $lead->start_preference_date->format('d M Y') }}</div>
                                <div class="call-time">{{ $lead->start_preference_date->format('H:i') }}</div>
                            @else
                                <span class="dash-muted">—</span>
                            @endif
                        </td>

                        {{-- Next Call --}}
                        <td>
                            @if($lead->next_call_at)
                                <div class="call-date">{{ $lead->next_call_at->format('d M Y') }}</div>
                                <div class="call-time">{{ $lead->next_call_at->format('H:i') }}</div>
                            @else
                                <span class="dash-muted">—</span>
                            @endif
                        </td>

                        {{-- Age --}}
                        @php
                            $totalHours = abs($lead->updated_at->diffInHours(now()));
                            $days  = intval($totalHours / 24);
                            $hours = $totalHours % 24;
                        @endphp
                        <td>
                            <div class="days-num {{ $days >= 3 ? 'danger' : '' }}">{{ $days }} days</div>
                            <div class="days-lbl">{{ $hours }} h</div>
                        </td>

                        {{-- Notes --}}
                        <td>
                            @if($lead->notes)
                                <button type="button"
                                        class="notes-pill"
                                        onclick="openNotes(this)"
                                        data-note="{{ e($lead->notes) }}"
                                        data-student="{{ e($lead->enrollment->student->full_name ?? 'Student') }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    <span class="notes-pill-text">{{ \Illuminate\Support\Str::limit($lead->notes, 28) }}</span>
                                </button>
                            @else
                                <span style="color:#AAB8C8;">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="action-group">
                                @if($isPendingApproval)
                                    {{-- Awaiting admin installment approval: locked, no Edit --}}
                                    <span class="btn-action" style="background:rgba(245,145,30,0.1);color:#C47010;border:1px dashed rgba(245,145,30,0.4);cursor:default;" title="Waiting for admin to approve or reject the installment plan">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Awaiting Approval
                                    </span>
                                @elseif($lead->status === 'Registered' && $lead->student_id)
                                    {{-- Registered: show Invoice, hide Edit --}}
                                    <a href="{{ route('leads.invoice', $lead->lead_id) }}" target="_blank" class="btn-action btn-invoice" title="View / Print Invoice">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                                        Invoice
                                    </a>
                                @else
                                    {{-- Not registered: show Edit --}}
                                    <a href="{{ route('leads.edit', $lead->lead_id) }}" class="btn-action btn-edit">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </a>
                                @endif

                                <button class="btn-action btn-log" onclick="openHistoryModal({{ $lead->lead_id }})">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    Log
                                </button>
                            </div>
                        </td>
                    </tr>