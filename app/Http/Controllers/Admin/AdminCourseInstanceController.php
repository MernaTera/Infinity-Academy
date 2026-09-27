<?php

namespace App\Http\Controllers\Admin;

use App\Support\BranchContext;

use App\Http\Controllers\Controller;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseSession;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Attendance\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCourseInstanceController extends Controller
{

    public function index(Request $request)
    {
        // Auto-promote: a course moves Upcoming -> Active when its start date
        // arrives, and Active -> Completed once its end date has passed (as
        // long as no non-cancelled session is still to run, so postponed
        // courses keep running). Mirrors the Student Care board + the daily
        // patches:update-statuses command.
        CourseInstance::where('status', 'Upcoming')
            ->whereDate('start_date', '<=', today())
            ->update(['status' => 'Active']);

        CourseInstance::where('status', 'Active')
            ->whereDate('end_date', '<', today())
            ->whereDoesntHave('sessions', function ($q) {
                $q->where('status', '!=', 'Cancelled')
                  ->whereDate('session_date', '>=', today());
            })
            ->update(['status' => 'Completed']);

        $with = [
            'courseTemplate','level','sublevel','teacher','patch','instanceSchedules','room','branch',
            'sessions' => fn($q) => $q->where('status', '!=', 'Cancelled')
                                      ->orderBy('session_date')->orderBy('start_time'),
            'enrollments' => fn($q) => $q->where('status', '!=', 'Cancelled'),
        ];

        // Three tabs: Active / Next Patch (upcoming) / Completed.
        $active    = CourseInstance::with($with)->where('status', 'Active')
                        ->orderBy('start_date')->get();
        $nextPatch = CourseInstance::with($with)->where('status', 'Upcoming')
                        ->orderBy('start_date')->get();
        $completed = CourseInstance::with($with)->where('status', 'Completed')
                        ->latest('end_date')->limit(40)->get();

        return view('admin.course-instances.index', compact('active', 'nextPatch', 'completed'));
    }


    public function show($id)
    {
        $instance = CourseInstance::with([
            'courseTemplate',
            'level',
            'sublevel',
            'teacher.employee',
            'patch',
            'room',
            'branch',
            'createdBy',
            'instanceSchedules',
            'sessions' => fn($q) => $q->orderBy('session_date')->orderBy('start_time'),
            'enrollments.student',
        ])->findOrFail($id);

        $activeEnrollments = $instance->enrollments->where('status', '!=', 'Cancelled')->values();

        $totalSessions     = $instance->sessions->count();
        $completedSessions = $instance->sessions->where('status', 'Completed')->count();
        $cancelledSessions = $instance->sessions->where('status', 'Cancelled')->count();
        $remainingSessions = max(0, $totalSessions - $completedSessions - $cancelledSessions);
        $progressPct       = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0;

        $sessionIds = $instance->sessions->pluck('course_session_id')->all();
        $enrollIds  = $activeEnrollments->pluck('enrollment_id')->all();

        $attendanceRows = Attendance::whereIn('course_session_id', $sessionIds)
            ->whereIn('enrollment_id', $enrollIds)
            ->get();

        $attendanceMap = [];
        foreach ($attendanceRows as $a) {
            $attendanceMap[$a->course_session_id][$a->enrollment_id] = $a->status;
        }

        $completedSessionIds = $instance->sessions->where('status', 'Completed')->pluck('course_session_id')->all();
        $studentAttendance = [];
        foreach ($activeEnrollments as $enr) {
            $present = 0; $absent = 0;
            foreach ($completedSessionIds as $sid) {
                $st = $attendanceMap[$sid][$enr->enrollment_id] ?? null;
                if ($st === 'Present') $present++;
                elseif ($st === 'Absent') $absent++;
            }
            $marked = $present + $absent;
            $studentAttendance[$enr->enrollment_id] = [
                'present' => $present,
                'absent'  => $absent,
                'rate'    => $marked > 0 ? round(($present / $marked) * 100) : null,
            ];
        }

        $revenueByInstance = $this->revenueByInstance([$instance->course_instance_id]);
        $revenueTotal      = $revenueByInstance[$instance->course_instance_id] ?? 0;

        $revenueByCategory = $this->revenueByCategory($enrollIds);

        return view('admin.course-instances.show', compact(
            'instance', 'activeEnrollments',
            'totalSessions', 'completedSessions', 'cancelledSessions', 'remainingSessions', 'progressPct',
            'attendanceMap', 'studentAttendance',
            'revenueTotal', 'revenueByCategory'
        ));
    }


    public function cancel(Request $request, $id)
    {
        $instance = CourseInstance::with('sessions')->findOrFail($id);

        if ($instance->status === 'Cancelled') {
            return back()->with('error', 'This course is already cancelled.');
        }
        if ($instance->status === 'Completed') {
            return back()->with('error', 'A completed course cannot be cancelled.');
        }

        DB::transaction(function () use ($instance) {
            CourseSession::where('course_instance_id', $instance->course_instance_id)
                ->where('status', 'Scheduled')
                ->update(['status' => 'Cancelled']);

            $instance->update(['status' => 'Cancelled']);

            if ($instance->teacher?->employee) {
                \DB::table('user_notification')->insert([
                    'employee_id'         => $instance->teacher->employee->employee_id,
                    'title'               => 'Course Cancelled',
                    'message'             => 'The course "' . ($instance->courseTemplate?->name ?? 'a course') . '" has been cancelled by admin.',
                    'related_entity_type' => 'course_instance',
                    'related_entity_id'   => $instance->course_instance_id,
                    'is_read'             => false,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        });

        return back()->with('success', 'Course instance cancelled.');
    }


    private function revenueByInstance(array $instanceIds): array
    {
        if (empty($instanceIds)) return [];

        $enrollMap = DB::table('enrollment')
            ->whereIn('course_instance_id', $instanceIds)
            ->where('status', '!=', 'Cancelled')
            ->when(BranchContext::currentBranchId() !== null, fn($q) => $q->where('branch_id', BranchContext::currentBranchId()))
            ->pluck('course_instance_id', 'enrollment_id');

        if ($enrollMap->isEmpty()) return [];

        $enrollIds = $enrollMap->keys()->all();

        $unpaidTxIds = InstallmentSchedule::whereIn('enrollment_id', $enrollIds)
            ->whereIn('status', ['Pending', 'Overdue'])
            ->whereNotNull('transaction_id')
            ->pluck('transaction_id')
            ->all();

        $txs = FinancialTransaction::whereIn('enrollment_id', $enrollIds)
            ->whereIn('transaction_type', ['Payment', 'Installment'])
            ->when(!empty($unpaidTxIds), fn($q) => $q->whereNotIn('transaction_id', $unpaidTxIds))
            ->get(['enrollment_id', 'amount']);

        $result = [];
        foreach ($txs as $tx) {
            $ciId = $enrollMap[$tx->enrollment_id] ?? null;
            if ($ciId === null) continue;
            $result[$ciId] = ($result[$ciId] ?? 0) + (float) $tx->amount;
        }

        return $result;
    }


    private function revenueByCategory(array $enrollIds): array
    {
        $out = ['Course' => 0, 'Test' => 0, 'Material' => 0, 'Installment' => 0];
        if (empty($enrollIds)) return $out;

        $unpaidTxIds = InstallmentSchedule::whereIn('enrollment_id', $enrollIds)
            ->whereIn('status', ['Pending', 'Overdue'])
            ->whereNotNull('transaction_id')
            ->pluck('transaction_id')
            ->all();

        $txs = FinancialTransaction::whereIn('enrollment_id', $enrollIds)
            ->whereIn('transaction_type', ['Payment', 'Installment'])
            ->when(!empty($unpaidTxIds), fn($q) => $q->whereNotIn('transaction_id', $unpaidTxIds))
            ->get(['transaction_type', 'transaction_category', 'amount']);

        foreach ($txs as $tx) {
            if ($tx->transaction_type === 'Installment') {
                $out['Installment'] += (float) $tx->amount;
            } else {
                $cat = in_array($tx->transaction_category, ['Course', 'Test', 'Material'])
                    ? $tx->transaction_category : 'Course';
                $out[$cat] += (float) $tx->amount;
            }
        }

        return $out;
    }
}