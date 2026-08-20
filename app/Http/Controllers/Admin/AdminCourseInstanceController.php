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
    /**
     * List every running course instance (Active + Upcoming) with headline
     * data: teacher, patch, enrolment count, session progress, and revenue.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all'); // all | Active | Upcoming | Completed | Cancelled

        $query = CourseInstance::with([
            'courseTemplate',
            'level',
            'sublevel',
            'teacher.employee',
            'patch',
            'room',
            'branch',
            'enrollments',
        ])
        ->withCount([
            'sessions as total_sessions_count',
            'sessions as completed_sessions_count' => fn($q) => $q->where('status', 'Completed'),
            'enrollments as enrollments_count' => fn($q) => $q->where('status', '!=', 'Cancelled'),
        ]);

        if (in_array($statusFilter, ['Active', 'Upcoming', 'Completed', 'Cancelled'])) {
            $query->where('status', $statusFilter);
        } else {
            // "all" default → the running ones first (Active + Upcoming),
            // but still show completed/cancelled below.
            $query->orderByRaw("FIELD(status, 'Active','Upcoming','Completed','Cancelled')");
        }

        $instances = $query->orderBy('start_date')->get();

        // ── Revenue per instance ───────────────────────────────────────
        // Sum Payment/Installment transactions of the enrolments in each
        // instance, EXCLUDING unpaid (Pending/Overdue) installment rows —
        // same rule the finance dashboards use.
        $revenueByInstance = $this->revenueByInstance($instances->pluck('course_instance_id')->all());

        foreach ($instances as $ci) {
            $ci->revenue_total = $revenueByInstance[$ci->course_instance_id] ?? 0;
            $ci->remaining_sessions = max(0, ($ci->total_sessions_count ?? 0) - ($ci->completed_sessions_count ?? 0));
            $ci->progress_pct = ($ci->total_sessions_count ?? 0) > 0
                ? round(($ci->completed_sessions_count / $ci->total_sessions_count) * 100)
                : 0;
        }

        // ── Headline stats ─────────────────────────────────────────────
        $all = CourseInstance::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $stats = [
            'active'    => (int) ($all['Active'] ?? 0),
            'upcoming'  => (int) ($all['Upcoming'] ?? 0),
            'completed' => (int) ($all['Completed'] ?? 0),
            'cancelled' => (int) ($all['Cancelled'] ?? 0),
            'total'     => (int) $all->sum(),
            'revenue'   => array_sum($revenueByInstance),
        ];

        return view('admin.course-instances.index', compact('instances', 'stats', 'statusFilter'));
    }

    /**
     * Full detail for one course instance: schedule, sessions, enrolled
     * students with attendance, progress, and revenue breakdown.
     */
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

        // ── Session stats ──────────────────────────────────────────────
        $totalSessions     = $instance->sessions->count();
        $completedSessions = $instance->sessions->where('status', 'Completed')->count();
        $cancelledSessions = $instance->sessions->where('status', 'Cancelled')->count();
        $remainingSessions = max(0, $totalSessions - $completedSessions - $cancelledSessions);
        $progressPct       = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0;

        // ── Attendance map: [course_session_id][enrollment_id] => status ──
        $sessionIds = $instance->sessions->pluck('course_session_id')->all();
        $enrollIds  = $activeEnrollments->pluck('enrollment_id')->all();

        $attendanceRows = Attendance::whereIn('course_session_id', $sessionIds)
            ->whereIn('enrollment_id', $enrollIds)
            ->get();

        $attendanceMap = [];
        foreach ($attendanceRows as $a) {
            $attendanceMap[$a->course_session_id][$a->enrollment_id] = $a->status;
        }

        // ── Per-student attendance summary ─────────────────────────────
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

        // ── Revenue for this instance ──────────────────────────────────
        $revenueByInstance = $this->revenueByInstance([$instance->course_instance_id]);
        $revenueTotal      = $revenueByInstance[$instance->course_instance_id] ?? 0;

        // Revenue split by category (Course / Test / Material / Installment)
        $revenueByCategory = $this->revenueByCategory($enrollIds);

        return view('admin.course-instances.show', compact(
            'instance', 'activeEnrollments',
            'totalSessions', 'completedSessions', 'cancelledSessions', 'remainingSessions', 'progressPct',
            'attendanceMap', 'studentAttendance',
            'revenueTotal', 'revenueByCategory'
        ));
    }

    /**
     * Admin action: cancel a course instance.
     * Marks the instance Cancelled and cancels its still-scheduled sessions.
     * Completed sessions and enrolments are left intact for the record.
     */
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
            // Cancel only the sessions that haven't happened yet.
            CourseSession::where('course_instance_id', $instance->course_instance_id)
                ->where('status', 'Scheduled')
                ->update(['status' => 'Cancelled']);

            $instance->update(['status' => 'Cancelled']);

            // Notify the teacher (if any)
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

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Revenue per course instance, keyed by course_instance_id.
     * Counts Payment + Installment transactions of each instance's
     * enrolments, EXCLUDING unpaid (Pending/Overdue) installment rows.
     */
    private function revenueByInstance(array $instanceIds): array
    {
        if (empty($instanceIds)) return [];

        // enrollment_id → course_instance_id
        $enrollMap = DB::table('enrollment')
            ->whereIn('course_instance_id', $instanceIds)
            ->where('status', '!=', 'Cancelled')
            ->when(BranchContext::currentBranchId() !== null, fn($q) => $q->where('branch_id', BranchContext::currentBranchId()))
            ->pluck('course_instance_id', 'enrollment_id');

        if ($enrollMap->isEmpty()) return [];

        $enrollIds = $enrollMap->keys()->all();

        // Unpaid installment transaction ids to exclude
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

    /**
     * Revenue grouped by transaction_category for a set of enrolments,
     * excluding unpaid installments.
     */
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