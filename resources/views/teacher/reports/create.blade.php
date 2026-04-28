@extends('teacher.layouts.app')
@section('title', 'Write Report')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
:root{--blue:#1B4FA8;--blue-l:rgba(27,79,168,0.08);--orange:#F5911E;--orange-l:rgba(245,145,30,0.08);--green:#059669;--green-l:rgba(5,150,105,0.08);--red:#DC2626;--red-l:rgba(220,38,38,0.06);--border:rgba(27,79,168,0.1);--bg:#F8F6F2;--card:#fff;--text:#1A2A4A;--muted:#7A8A9A;--faint:#AAB8C8;}
*{box-sizing:border-box;}
.cr-page{background:var(--bg);min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:var(--text);}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:4px;}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:var(--blue);margin:0 0 28px;}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}

.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s;}
.btn-back:hover{border-color:var(--blue);color:var(--blue);text-decoration:none;}

.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:var(--orange);margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid rgba(245,145,30,0.15);display:block;}

/* Card */
.form-card{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:20px;position:relative;box-shadow:0 2px 12px rgba(27,79,168,0.06);}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--orange),var(--blue),transparent);}
.form-card-body{padding:24px 28px;}

/* Student selector */
.student-selector{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:4px;}
@media(max-width:700px){.student-selector{grid-template-columns:1fr;}}
.student-option{position:relative;}
.student-option input[type="radio"]{position:absolute;opacity:0;width:0;height:0;}
.student-option label{display:flex;align-items:center;gap:12px;padding:14px 16px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;transition:all 0.2s;background:var(--bg);}
.student-option label:hover{border-color:rgba(27,79,168,0.3);}
.student-option input:checked + label{border-color:var(--blue);background:var(--blue-l);}
.student-avatar{width:36px;height:36px;border-radius:50%;background:var(--blue-l);color:var(--blue);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:16px;flex-shrink:0;}
.student-option input:checked + label .student-avatar{background:var(--blue);color:#fff;}
.student-name{font-weight:600;color:var(--text);font-size:13px;}
.student-meta{font-size:10px;color:var(--faint);margin-top:2px;}

/* Course cards */
.course-selector{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
@media(max-width:700px){.course-selector{grid-template-columns:1fr;}}
.course-option{position:relative;}
.course-option input[type="radio"]{position:absolute;opacity:0;width:0;height:0;}
.course-option label{display:flex;flex-direction:column;gap:6px;padding:16px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;transition:all 0.2s;background:var(--bg);}
.course-option label:hover{border-color:rgba(27,79,168,0.3);}
.course-option input:checked + label{border-color:var(--blue);background:var(--blue-l);}
.course-name{font-weight:600;color:var(--text);font-size:13px;}
.course-level{font-size:10px;color:var(--faint);}
.course-student{font-size:11px;color:var(--blue);margin-top:2px;}

/* Score table */
.score-table{width:100%;border-collapse:collapse;}
.score-table thead th{padding:10px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);text-align:left;font-weight:500;background:rgba(27,79,168,0.02);border-bottom:1px solid var(--border);}
.score-table thead th:last-child{text-align:center;}
.score-table tbody tr{border-bottom:1px solid rgba(27,79,168,0.05);transition:background 0.15s;}
.score-table tbody tr:last-child{border-bottom:none;}
.score-table tbody tr:hover{background:rgba(27,79,168,0.02);}
.score-table td{padding:14px 14px;font-size:13px;color:var(--muted);vertical-align:middle;}

.component-name{font-weight:500;color:var(--text);}
.max-score-badge{display:inline-flex;align-items:center;justify-content:center;background:var(--blue-l);color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:1px;padding:3px 10px;border-radius:4px;border:1px solid var(--border);}

.score-input-wrap{display:flex;align-items:center;gap:8px;justify-content:center;}
.score-input{width:70px;padding:8px 10px;border:1.5px solid var(--border);border-radius:4px;font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:var(--text);text-align:center;outline:none;background:#fff;transition:border-color 0.2s;}
.score-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}
.score-input.error{border-color:var(--red);}
.score-slash{font-size:14px;color:var(--faint);}
.score-max{font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--faint);letter-spacing:1px;}

/* Score bar live */
.score-bar-wrap{display:flex;align-items:center;gap:8px;min-width:100px;}
.score-bar-track{flex:1;background:#F0F0F0;border-radius:3px;height:5px;overflow:hidden;}
.score-bar-fill{height:5px;border-radius:3px;transition:width 0.3s,background 0.3s;}
.score-pct{font-size:10px;font-family:'Bebas Neue',sans-serif;letter-spacing:1px;white-space:nowrap;min-width:32px;text-align:right;}

/* Total card */
.total-card{background:linear-gradient(135deg,#1A2A4A 0%,#1B4FA8 100%);border-radius:8px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;margin-top:16px;}
.total-label{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;}
.total-value{font-family:'Bebas Neue',sans-serif;font-size:42px;letter-spacing:3px;color:#fff;line-height:1;}
.total-sub{font-size:11px;color:rgba(255,255,255,0.4);margin-top:4px;}
.total-grade{font-family:'Bebas Neue',sans-serif;font-size:60px;letter-spacing:2px;color:rgba(255,255,255,0.15);}

/* Comments */
.form-field{display:flex;flex-direction:column;gap:6px;}
.form-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fff;outline:none;box-sizing:border-box;resize:vertical;}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(27,79,168,0.07);}

/* Footer */
.form-footer{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);}
.btn-draft{padding:10px 22px;background:transparent;border:1px solid var(--border);border-radius:4px;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;}
.btn-draft:hover{border-color:var(--blue);color:var(--blue);}
.btn-submit{display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:transparent;border:1.5px solid var(--blue);border-radius:4px;color:var(--blue);font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s;}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--blue),#2D6FDB);transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);}
.btn-submit:hover::before{transform:scaleX(1);}
.btn-submit:hover{color:#fff;}
.btn-submit span,.btn-submit svg{position:relative;z-index:1;}

/* Empty state */
.empty-state{text-align:center;padding:60px 20px;color:var(--faint);}
.empty-state svg{margin:0 auto 16px;display:block;opacity:0.3;}

@media(max-width:768px){.cr-page{padding:18px 14px;}.form-card-body{padding:18px;}}
</style>

<div class="cr-page">

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Teacher Dashboard</div>
            <h1 class="page-title">Write Report</h1>
        </div>
        <a href="{{ route('teacher.reports.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if(session('error'))
    <div style="background:var(--red-l);border:1px solid rgba(220,38,38,0.2);color:var(--red);padding:12px 16px;border-radius:4px;margin-bottom:20px;font-size:13px;">{{ session('error') }}</div>
    @endif

    @php
        $instance    = $instance ?? null;
        $components  = $components ?? [];
    @endphp

    <form method="POST" action="{{ route('teacher.reports.store') }}" id="reportForm">
        @csrf

        {{-- ── STEP 1: Select Enrollment ── --}}
        @if(!$enrollment)
        <div class="form-card">
            <div class="form-card-body">
                <span class="sec-label">Select Student & Course</span>

                @if($availableEnrollments->isEmpty())
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <div style="font-size:13px;">No completed courses without reports.</div>
                    <div style="font-size:11px;margin-top:6px;">All your completed courses already have reports submitted.</div>
                </div>
                @else
                <div class="course-selector" id="courseSelector">
                    @foreach($availableEnrollments as $e)
                    <div class="course-option">
                        <input type="radio" name="enrollment_id" id="enr_{{ $e->enrollment_id }}"
                               value="{{ $e->enrollment_id }}"
                               onchange="selectEnrollment(this)"
                               {{ request('enrollment_id') == $e->enrollment_id ? 'checked' : '' }}>
                        <label for="enr_{{ $e->enrollment_id }}">
                            <div class="course-name">{{ $e->courseTemplate?->name ?? '—' }}</div>
                            <div class="course-level">
                                {{ $e->level?->name ?? '' }}
                                @if($e->sublevel) › {{ $e->sublevel->name }} @endif
                            </div>
                            <div class="course-student">👤 {{ $e->student?->full_name ?? '—' }}</div>
                            @if($e->courseInstance?->end_date)
                            <div style="font-size:9px;color:var(--faint);margin-top:4px;">
                                Ended: {{ \Carbon\Carbon::parse($e->courseInstance->end_date)->format('d M Y') }}
                            </div>
                            @endif
                        </label>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @else
        {{-- Enrollment pre-selected --}}
        <input type="hidden" name="enrollment_id" value="{{ $enrollment->enrollment_id }}">

        {{-- ── STUDENT / COURSE INFO ── --}}
        <div class="form-card">
            <div class="form-card-body">
                <span class="sec-label">Student & Course</span>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:6px;">Student</div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="student-avatar">{{ strtoupper(substr($enrollment->student?->full_name ?? '?', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;color:var(--text);font-size:15px;">{{ $enrollment->student?->full_name ?? '—' }}</div>
                                <div style="font-size:10px;color:var(--faint);margin-top:2px;">Enrollment #{{ $enrollment->enrollment_id }}</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--faint);margin-bottom:6px;">Course</div>
                        <div style="font-weight:600;color:var(--text);font-size:15px;">{{ $enrollment->courseTemplate?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--faint);margin-top:3px;">
                            {{ $enrollment->level?->name ?? '' }}
                            @if($enrollment->sublevel) › {{ $enrollment->sublevel->name }} @endif
                        </div>
                        @if($instance?->end_date)
                        <div style="font-size:10px;color:var(--faint);margin-top:3px;">
                            Ended: {{ \Carbon\Carbon::parse($instance->end_date)->format('d M Y') }}
                            @php $deadline = \Carbon\Carbon::parse($instance->end_date)->addDays(3); @endphp
                            · Deadline: <span style="color:{{ now()->gt($deadline) ? 'var(--red)' : 'var(--blue)' }};">{{ $deadline->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── SCORES ── --}}
        <div class="form-card" id="scoresSection" style="{{ (!$enrollment && $availableEnrollments->isEmpty()) ? 'display:none;' : '' }}">
            <div class="form-card-body">
                <span class="sec-label">Score Sheet</span>

                <table class="score-table">
                    <thead>
                        <tr>
                            <th>Component</th>
                            <th style="text-align:center;">Max</th>
                            <th style="text-align:center;">Score</th>
                            <th style="text-align:left;padding-left:20px;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($components as $i => $comp)
                        <tr>
                            <td><span class="component-name">{{ $comp['name'] }}</span></td>
                            <td style="text-align:center;">
                                <span class="max-score-badge">{{ $comp['max'] }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div class="score-input-wrap">
                                    <input type="number"
                                           name="scores[{{ $i }}]"
                                           id="score_{{ $i }}"
                                           class="score-input"
                                           min="0"
                                           max="{{ $comp['max'] }}"
                                           step="0.5"
                                           placeholder="0"
                                           oninput="updateScore({{ $i }}, {{ $comp['max'] }}, this.value)"
                                           required>
                                </div>
                            </td>
                            <td style="padding-left:20px;">
                                <div class="score-bar-wrap">
                                    <div class="score-bar-track">
                                        <div class="score-bar-fill" id="bar_{{ $i }}" style="width:0%;background:var(--faint);"></div>
                                    </div>
                                    <span class="score-pct" id="pct_{{ $i }}" style="color:var(--faint);">0%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Total --}}
                <div class="total-card">
                    <div>
                        <div class="total-label">Total Score</div>
                        <div class="total-value" id="totalDisplay">0</div>
                        <div class="total-sub">out of 100</div>
                    </div>
                    <div class="total-grade" id="gradeDisplay">—</div>
                </div>
            </div>
        </div>

        {{-- ── INSTRUCTOR COMMENTS ── --}}
        <div class="form-card">
            <div class="form-card-body">
                <span class="sec-label">Instructor's Comments</span>
                <div class="form-field">
                    <label class="form-label">Comments & Notes</label>
                    <textarea name="comments" class="form-control" rows="5"
                              placeholder="Write your detailed feedback about the student's performance, strengths, areas for improvement..."></textarea>
                    <span style="font-size:10px;color:var(--faint);margin-top:4px;">This will appear on the student's report card.</span>
                </div>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="form-footer">
            <button type="button" class="btn-draft" onclick="submitForm('save_draft')">
                Save as Draft
            </button>
            <button type="button" class="btn-submit" onclick="submitForm('submit')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/></svg>
                <span>Submit to Admin</span>
            </button>
        </div>

        <input type="hidden" name="action" id="actionInput" value="save_draft">

    </form>
</div>

<script>
const maxScores = @json(array_column($components, 'max'));
let scores = new Array(maxScores.length).fill(0);

function updateScore(idx, max, val) {
    const v = parseFloat(val) || 0;
    const clamped = Math.min(Math.max(v, 0), max);
    scores[idx] = clamped;

    const pct = max > 0 ? (clamped / max) * 100 : 0;
    const color = pct >= 80 ? 'var(--green)' : pct >= 60 ? 'var(--blue)' : pct >= 40 ? 'var(--orange)' : 'var(--red)';

    const bar = document.getElementById('bar_' + idx);
    const pctEl = document.getElementById('pct_' + idx);
    if (bar) { bar.style.width = pct + '%'; bar.style.background = color; }
    if (pctEl) { pctEl.textContent = Math.round(pct) + '%'; pctEl.style.color = color; }

    updateTotal();
}

function updateTotal() {
    const total = scores.reduce((a, b) => a + b, 0);
    const totalEl = document.getElementById('totalDisplay');
    const gradeEl = document.getElementById('gradeDisplay');
    if (totalEl) totalEl.textContent = total.toFixed(1);

    let grade = '—';
    if (total >= 90) grade = 'A';
    else if (total >= 80) grade = 'B';
    else if (total >= 70) grade = 'C';
    else if (total >= 60) grade = 'D';
    else if (total > 0)   grade = 'F';

    if (gradeEl) gradeEl.textContent = grade;
}

function submitForm(action) {
    // Validate enrollment selected
    const enrollmentInput = document.querySelector('[name="enrollment_id"]');
    if (!enrollmentInput || !enrollmentInput.value) {
        alert('Please select a student/course first.');
        return;
    }

    // Validate scores if submitting
    if (action === 'submit') {
        const total = scores.reduce((a, b) => a + b, 0);
        if (total === 0) {
            alert('Please fill in the scores before submitting.');
            return;
        }
        // Check each score within range
        let valid = true;
        document.querySelectorAll('.score-input').forEach((input, i) => {
            const v = parseFloat(input.value) || 0;
            if (v < 0 || v > maxScores[i]) {
                input.classList.add('error');
                valid = false;
            } else {
                input.classList.remove('error');
            }
        });
        if (!valid) { alert('Some scores are out of range. Please check.'); return; }
    }

    document.getElementById('actionInput').value = action;
    document.getElementById('reportForm').submit();
}

function selectEnrollment(radio) {
    document.getElementById('scoresSection').style.display = '';
}

// Init if enrollment pre-selected
@if($enrollment)
document.getElementById('scoresSection').style.display = '';
@endif
</script>

@endsection