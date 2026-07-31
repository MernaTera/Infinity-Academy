{{-- Single lead row — used by both the main table and the registered table --}}
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
                                $statusClass = match($lead->status) {
                                    'Waiting'        => 'status-waiting',
                                    'Call_Again'     => 'status-call_again',
                                    'Scheduled_Call' => 'status-scheduled',
                                    'Registered'     => 'status-registered',
                                    'Not_Interested' => 'status-not_interest',
                                    'Archived'       => 'status-archived',
                                    default          => 'status-default',
                                };
                            @endphp
                            <div style="position:relative;display:inline-block;">
                                <div class="status-badge {{ $statusClass }}"
                                    style="cursor:pointer;user-select:none;{{ $lead->status === 'Registered' ? 'pointer-events:none;opacity:0.85;cursor:default;' : '' }}"
                                    @if($lead->status !== 'Registered') onclick="toggleDropdown(this)" @endif>
                                    {{ str_replace('_',' ',$lead->status) }}
                                    @if($lead->status !== 'Registered')
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                                    @endif
                                </div>
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
                            </div>
                            {{-- hidden select للـ function --}}
                            <select class="status-select" data-id="{{ $lead->lead_id }}" style="display:none;"
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
                                <span class="notes-cell" title="{{ $lead->notes }}">{{ $lead->notes }}</span>
                            @else
                                <span class="dash-muted">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="action-group">
                                @if($lead->status === 'Registered' && $lead->student_id)
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