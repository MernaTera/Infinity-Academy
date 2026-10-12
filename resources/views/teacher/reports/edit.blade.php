@extends('teacher.layouts.app')
@section('title', 'Resubmit Report')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

<style>
.rep-create{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.1;z-index:0;pointer-events:none}
.orb-2{width:300px;height:300px;background:radial-gradient(circle,#1B4FA8,transparent 70%);top:30px;right:1%}
.orb-3{width:380px;height:380px;background:radial-gradient(circle,#7C3AED,transparent 70%);bottom:-140px;left:35%}
.page-eyebrow{font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#1B4FA8;margin-bottom:6px;font-weight:600}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:40px;letter-spacing:2px;color:#1A2A4A;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:rgba(255,255,255,0.6);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.6);border-radius:11px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#1B4FA8;color:#1B4FA8;text-decoration:none}
.form-card{max-width:720px;background:rgba(255,255,255,0.5);-webkit-backdrop-filter:blur(22px) saturate(165%);backdrop-filter:blur(22px) saturate(165%);border:1px solid rgba(255,255,255,0.6);border-radius:20px;overflow:hidden;position:relative;box-shadow:0 8px 30px rgba(23,45,90,0.07)}
/* .form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#DC2626,transparent)} */
.form-body{padding:28px 32px}
.sec-label{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#7A8A9A;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid rgba(27,79,168,0.1);margin-top:4px;display:block;font-weight:600}
.score-grid{display:flex;flex-direction:column;gap:12px;margin-bottom:20px}
.score-row{display:flex;align-items:center;gap:16px;padding:12px 16px;background:rgba(255,255,255,0.6);border:1px solid rgba(27,79,168,0.08);border-radius:12px}
.score-name{flex:1;font-size:13px;color:#1A2A4A;font-weight:500}
.score-input{width:70px;padding:8px 10px;border:1px solid rgba(27,79,168,0.15);border-radius:8px;font-family:'Bebas Neue',sans-serif;font-size:18px;color:#1B4FA8;text-align:center;background:rgba(255,255,255,0.7);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);letter-spacing:1px;outline:none}
.score-input:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.total-row{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:rgba(27,79,168,0.04);border:1px solid rgba(27,79,168,0.15);border-radius:12px;margin-bottom:20px}
.total-label{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.total-val{font-family:'Bebas Neue',sans-serif;font-size:32px;color:#1B4FA8;letter-spacing:2px}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(27,79,168,0.12);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:rgba(255,255,255,0.7);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);outline:none;box-sizing:border-box}
.form-control:focus{border-color:#1B4FA8;box-shadow:0 0 0 3px rgba(27,79,168,0.07)}
.form-footer{padding:20px 32px;border-top:1px solid rgba(27,79,168,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-submit{isolation:isolate;padding:11px 28px;background:transparent;border:1.5px solid #DC2626;border-radius:10px;color:#DC2626;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-submit::before{z-index:-1;content:'';position:absolute;inset:0;background:#DC2626;transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}
.btn-cancel{padding:10px 20px;background:transparent;border:1px solid rgba(27,79,168,0.15);border-radius:10px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;text-decoration:none}
</style>

<div class="rep-create">
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Instructor</div>
            <h1 class="page-title">Resubmit Report</h1>
        </div>
        <a href="{{ route('teacher.reports.index') }}" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    @if($report->rejection_note)
    <div style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);border-radius:6px;padding:14px 18px;margin-bottom:20px;max-width:720px">
        <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#DC2626;margin-bottom:4px">Admin Rejection Note</div>
        <div style="font-size:13px;color:#1A2A4A">{{ $report->rejection_note }}</div>
    </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('teacher.reports.update', $report->report_id) }}">
            @csrf @method('PUT')
            <div class="form-body">

                <div style="background:#F8F6F2;border:1px solid rgba(27,79,168,0.08);border-radius:6px;padding:14px 16px;margin-bottom:20px">
                    <div style="font-size:13px;font-weight:500;color:#1A2A4A">
                        {{ $report->enrollment?->student?->full_name ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:#7A8A9A;margin-top:2px">
                        {{ $report->enrollment?->courseInstance?->courseTemplate?->name ?? '—' }}
                    </div>
                </div>

                <span class="sec-label">Update Scores</span>
                @php

                    $components = $components ?? [];

                    $existingScores = $report->reportScores->pluck('student_score', 'component_name');
                @endphp
                <div class="score-grid">
                    @foreach($components as $i => $comp)
                    @php $existing = $existingScores[$comp['name']] ?? 0; @endphp
                    <div class="score-row">
                        <div class="score-name">{{ $comp['name'] }} <span style="font-size:10px;color:#AAB8C8">· out of {{ $comp['max'] }}</span></div>
                        <div style="display:flex;align-items:center;gap:6px">
                            <input type="number" name="scores[{{ $i }}]"
                                   class="score-input" min="0" max="{{ $comp['max'] }}"
                                   value="{{ old('scores.'.$i, $existing) }}"
                                   onchange="calcTotal()" oninput="calcTotal()" required>
                            <span style="color:#AAB8C8">/</span>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#AAB8C8;letter-spacing:1px">{{ $comp['max'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="total-row">
                    <span class="total-label">Total Score</span>
                    <div>
                        <span class="total-val" id="totalDisplay">{{ $report->total_score }}</span>
                        <span style="font-size:14px;color:#AAB8C8"> / 100</span>
                    </div>
                </div>

                <span class="sec-label">Updated Comments</span>
                <textarea name="comments" class="form-control" rows="4"
                    placeholder="Update your comments...">{{ old('comments', $report->comments) }}</textarea>

            </div>
            <input type="hidden" name="action" value="submit">
            <div class="form-footer">
                <a href="{{ route('teacher.reports.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Resubmit Report</button>
            </div>
        </form>
    </div>
</div>

<script>

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.score-input').forEach(inp => {
        const max = parseFloat(inp.max) || Infinity;
        total += Math.min(parseFloat(inp.value || 0), max);
    });
    const d = document.getElementById('totalDisplay');
    if (!d) return;
    d.textContent = total;
    d.style.color = total >= 60 ? '#1B4FA8' : (total >= 40 ? '#C47010' : '#DC2626');
}
calcTotal();
</script>
@endsection