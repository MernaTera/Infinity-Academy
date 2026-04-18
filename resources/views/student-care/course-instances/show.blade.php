@extends('student-care.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">
    {{ $instance->courseTemplate->name }}
</h2>

{{-- ========= OVERVIEW CARD ========= --}}
<div style="background:#fff; padding:20px; border-radius:12px; margin-bottom:20px;
box-shadow:0 10px 25px rgba(0,0,0,0.05);">

    <div style="display:flex; gap:40px; flex-wrap:wrap; font-size:13px;">

        <div>
            <strong>Teacher:</strong><br>
            {{ $instance->teacher->name ?? '-' }}
        </div>

        <div>
            <strong>Branch:</strong><br>
            {{ $instance->branch->name ?? '-' }}
        </div>

        <div>
            <strong>Students:</strong><br>
            {{ $instance->enrollments->count() }} / {{ $instance->capacity }}
        </div>

        <div>
            <strong>Dates:</strong><br>
            {{ $instance->start_date }} → {{ $instance->end_date }}
        </div>

        <div>
            <strong>Status:</strong><br>
            <span style="color:#1B4FA8;">
                {{ $instance->status }}
            </span>
        </div>

    </div>
</div>

{{-- ========= TABS ========= --}}
<div style="display:flex; gap:20px; margin-bottom:20px;">

    <button onclick="showTab('students')" class="tab-btn active">Students</button>
    <button onclick="showTab('attendance')" class="tab-btn">Attendance</button>
    <button onclick="showTab('schedule')" class="tab-btn">Schedule</button>

</div>

<style>
.tab-btn {
    padding:8px 16px;
    border:none;
    background:#eee;
    cursor:pointer;
    border-radius:6px;
}
.tab-btn.active {
    background:#1B4FA8;
    color:#fff;
}
</style>

{{-- ========= STUDENTS TAB ========= --}}
<div id="studentsTab">

    <div style="background:#fff; border-radius:12px; overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);">

        <table style="width:100%; font-size:13px; border-collapse:collapse;">

            <thead style="background:#f5f7fb;">
                <tr>
                    <th style="padding:12px;">Student</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Hours Taken</th>
                    <th>Start Date</th>
                    <th>Payment</th>
                </tr>
            </thead>

            <tbody>

            @forelse($instance->enrollments as $enrollment)

            <tr style="border-top:1px solid #eee;">

                {{-- Student --}}
                <td>
                    <strong>{{ $enrollment->student->full_name }}</strong>
                </td>

                {{-- Phone --}}
                <td>
                    {{ $enrollment->student->phones->first()->phone_number ?? '-' }}
                </td>

                {{-- Status --}}
                <td>
                    <span style="color:#10B981;">
                        {{ $enrollment->status }}
                    </span>
                </td>

                {{-- Hours --}}
                <td>
                    {{ $enrollment->hours_remaining ?? '-' }} hrs
                </td>

                {{-- Start Date --}}
                <td>
                    {{ $enrollment->actual_start_date ?? '-' }}
                </td>

                {{-- Payment --}}
                <td>
                    <span style="color:#F59E0B;">
                        Pending
                    </span>
                </td>

            </tr>

            @empty
                <tr>
                    <td colspan="3" style="padding:20px; text-align:center;">
                        No students yet
                    </td>
                </tr>
            @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- ========= ATTENDANCE TAB ========= --}}
<div id="attendanceTab" style="display:none;">
    <div style="background:#fff; padding:20px; border-radius:12px;">

        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            @foreach($instance->sessions as $session)
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:10px;">Session {{ $session->session_number }}</td>
                <td>{{ $session->session_date }}</td>
                <td>
                    <a href="{{ route('student-care.attendance.show', $session->course_session_id) }}"
                       style="color:#1B4FA8;">
                        Take Attendance
                    </a>
                </td>
            </tr>
            @endforeach
        </table>

    </div>
</div>

{{-- ========= SCHEDULE TAB ========= --}}
<div id="scheduleTab" style="display:none;">
    <div style="background:#fff; padding:20px; border-radius:12px;">
        Schedule will be here 👀
    </div>
</div>

{{-- ========= JS ========= --}}
<script>
function showTab(tab) {

    document.getElementById('studentsTab').style.display = 'none';
    document.getElementById('attendanceTab').style.display = 'none';
    document.getElementById('scheduleTab').style.display = 'none';

    document.getElementById(tab + 'Tab').style.display = 'block';

    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function openAttendance(sessionId) {
    document.getElementById('session_id').value = sessionId;

    document.getElementById('attendanceTab').style.display = 'block';
}
</script>

@endsection