@extends('teacher.layouts.app')
@section('title', 'Resubmit Report')

@section('content')
@once
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@endonce

{{-- نفس الـ styles من create --}}
<style>
.rep-create{background:#F8F6F2;min-height:100vh;padding:40px 32px;font-family:'DM Sans',sans-serif;color:#1A2A4A}
.page-eyebrow{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:4px}
.page-title{font-family:'Bebas Neue',sans-serif;font-size:34px;letter-spacing:4px;color:#059669;margin:0}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.btn-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:transparent;border:1px solid rgba(5,150,105,0.2);border-radius:4px;color:#7A8A9A;font-size:10px;letter-spacing:2.5px;text-transform:uppercase;text-decoration:none;transition:all 0.3s}
.btn-back:hover{border-color:#059669;color:#059669;text-decoration:none}
.form-card{max-width:720px;background:#fff;border:1px solid rgba(5,150,105,0.1);border-radius:8px;overflow:hidden;position:relative}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#DC2626,transparent)}
.form-body{padding:28px 32px}
.sec-label{font-size:9px;letter-spacing:4px;text-transform:uppercase;color:#059669;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid rgba(5,150,105,0.1);margin-top:4px;display:block}
.score-grid{display:flex;flex-direction:column;gap:12px;margin-bottom:20px}
.score-row{display:flex;align-items:center;gap:16px;padding:12px 16px;background:#F8F6F2;border:1px solid rgba(5,150,105,0.08);border-radius:6px}
.score-name{flex:1;font-size:13px;color:#1A2A4A;font-weight:500}
.score-input{width:70px;padding:8px 10px;border:1px solid rgba(5,150,105,0.15);border-radius:4px;font-family:'Bebas Neue',sans-serif;font-size:18px;color:#059669;text-align:center;background:#fff;letter-spacing:1px;outline:none}
.score-input:focus{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,0.07)}
.total-row{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:rgba(5,150,105,0.04);border:1px solid rgba(5,150,105,0.15);border-radius:6px;margin-bottom:20px}
.total-label{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A}
.total-val{font-family:'Bebas Neue',sans-serif;font-size:32px;color:#059669;letter-spacing:2px}
.form-control{width:100%;padding:10px 12px;border:1px solid rgba(5,150,105,0.12);border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;color:#1A2A4A;background:#fff;outline:none;box-sizing:border-box}
.form-control:focus{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,0.07)}
.form-footer{padding:20px 32px;border-top:1px solid rgba(5,150,105,0.07);display:flex;gap:10px;justify-content:flex-end}
.btn-submit{padding:11px 28px;background:transparent;border:1.5px solid #DC2626;border-radius:4px;color:#DC2626;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:4px;cursor:pointer;position:relative;overflow:hidden;transition:color 0.4s}
.btn-submit::before{content:'';position:absolute;inset:0;background:#DC2626;transform:scaleX(0);transform-origin:left;transition:transform 0.4s cubic-bezier(0.16,1,0.3,1)}
.btn-submit:hover::before{transform:scaleX(1)}
.btn-submit:hover{color:#fff}
.btn-cancel{padding:10px 20px;background:transparent;border:1px solid rgba(5,150,105,0.15);border-radius:4px;color:#7A8A9A;font-family:'DM Sans',sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;text-decoration:none}
</style>

<div class="rep-create">
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

    {{-- Rejection Note --}}
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

                {{-- Student Info --}}
                <div style="background:#F8F6F2;border:1px solid rgba(5,150,105,0.08);border-radius:6px;padding:14px 16px;margin-bottom:20px">
                    <div style="font-size:13px;font-weight:500;color:#1A2A4A">
                        {{ $report->enrollment?->student?->full_name ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:#7A8A9A;margin-top:2px">
                        {{ $report->enrollment?->courseInstance?->courseTemplate?->name ?? '—' }}
                    </div>
                </div>

                {{-- Scores --}}
                <span class="sec-label">Update Scores</span>
                @php
                $components = [
                    ['roleplay_1',   'Roleplay 1',         15],
                    ['roleplay_2',   'Roleplay 2',         15],
                    ['writing_1',    'Writing Task 1',     10],
                    ['writing_2',    'Writing Task 2',     10],
                    ['presentation', 'Presentation/Debate',20],
                    ['mcq',          'Final MCQ',          20],
                    ['writing_final','Final Writing Task', 10],
                ];
                $existingScores = $report->scores->pluck('student_score','component_name');
                $fieldMap = [
                    'roleplay_1'=>'Roleplay 1','roleplay_2'=>'Roleplay 2',
                    'writing_1'=>'Writing Task 1','writing_2'=>'Writing Task 2',
                    'presentation'=>'Presentation','mcq'=>'Final MCQ',
                    'writing_final'=>'Final Writing Task',
                ];
                @endphp
                <div class="score-grid">
                    @foreach($components as [$field, $label, $max])
                    @php $existing = $existingScores[$fieldMap[$field]] ?? 0; @endphp
                    <div class="score-row">
                        <div class="score-name">{{ $label }} <span style="font-size:10px;color:#AAB8C8">· out of {{ $max }}</span></div>
                        <div style="display:flex;align-items:center;gap:6px">
                            <input type="number" name="{{ $field }}" id="{{ $field }}"
                                   class="score-input" min="0" max="{{ $max }}"
                                   value="{{ old($field, $existing) }}"
                                   onchange="calcTotal()" oninput="calcTotal()" required>
                            <span style="color:#AAB8C8">/</span>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:#AAB8C8;letter-spacing:1px">{{ $max }}</span>
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
            <div class="form-footer">
                <a href="{{ route('teacher.reports.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Resubmit Report</button>
            </div>
        </form>
    </div>
</div>

<script>
const maxScores = {roleplay_1:15,roleplay_2:15,writing_1:10,writing_2:10,presentation:20,mcq:20,writing_final:10};
function calcTotal() {
    let total = 0;
    Object.keys(maxScores).forEach(f => {
        total += Math.min(parseFloat(document.getElementById(f)?.value||0), maxScores[f]);
    });
    const d = document.getElementById('totalDisplay');
    d.textContent = total;
    d.style.color = total >= 60 ? '#059669' : (total >= 40 ? '#C47010' : '#DC2626');
}
calcTotal();
</script>
@endsection