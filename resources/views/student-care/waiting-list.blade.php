@extends('student-care.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">Waiting List</h2>

<div style="background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.05);">

    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead style="background:#f5f7fb;">
            <tr>
                <th style="padding:12px;">Student</th>
                <th>Course</th>
                <th>Level</th>
                <th>Type</th>
                <th>Preferred Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        @forelse($waiting as $item)
            <tr style="border-top:1px solid #eee;">

                <td style="padding:12px;">
                    {{ $item->enrollment->student->full_name ?? '-' }}
                </td>

                <td>
                    {{ $item->enrollment->courseTemplate->name ?? '-' }}
                </td>

                <td>
                    {{ $item->enrollment->level->name ?? '-' }}
                </td>

                <td>
                    {{ $item->preferred_type ?? '-' }}
                </td>

                <td>
                    {{ $item->preferred_start_date?->format('Y-m-d') ?? '-' }}
                </td>

                <td>
                    <span style="color:#F59E0B; font-weight:500;">
                        {{ $item->status }}
                    </span>
                </td>

                <td>
                    <button style="padding:6px 10px; background:#1B4FA8; color:#fff; border:none; border-radius:5px;">
                        Assign
                    </button>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" style="padding:20px; text-align:center;">
                    No waiting students
                </td>
            </tr>
        @endforelse

        </tbody>
    </table>

</div>

@endsection