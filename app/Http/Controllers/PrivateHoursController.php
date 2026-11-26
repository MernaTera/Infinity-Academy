<?php

namespace App\Http\Controllers;

use App\Support\BranchContext;

use App\Models\Enrollment\Enrollment;
use App\Models\Attendance\Attendance;
use App\Models\Finance\PrivateBundle;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Academic\Patch;
use App\Models\Enrollment\WaitingList;
use App\Events\WaitingListUpdated;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivateHoursController extends Controller
{
   
    public function index(Request $request)
    {
        $stateFilter = $request->query('state', 'all');
        $search      = trim((string) $request->query('q', ''));
        $enrollments = Enrollment::with([
                'student.phones',
                'courseTemplate',
                'level',
                'sublevel',
                'courseInstance.courseTemplate',
                'privateBundle' => fn($q) => $q->withoutGlobalScope('branch'),
            ])
            ->where('enrollment_type', 'Private')
            ->whereNotNull('hours_remaining')
            ->where(function ($q) {
                $q->whereIn('status', ['Active', 'Restricted', 'Waiting', 'Pending_Approval'])
                  ->orWhere('status', 'Postponed')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'Completed')
                         ->where('hours_remaining', '>', 0);
                  });
            })
            ->orderByDesc('updated_at')
            ->get();

        $enrollIds = $enrollments->pluck('enrollment_id')->all();
        $absenceCounts = empty($enrollIds) ? collect() :
            Attendance::whereIn('enrollment_id', $enrollIds)
                ->where('status', 'Absent')
                ->select('enrollment_id', DB::raw('COUNT(*) as c'))
                ->groupBy('enrollment_id')
                ->pluck('c', 'enrollment_id');

        $studentIds = $enrollments->pluck('student_id')->unique()->filter()->all();
        $leadByStudent = empty($studentIds) ? collect() :
            DB::table('lead')
                ->whereIn('student_id', $studentIds)
                ->when(BranchContext::currentBranchId() !== null, fn($q) => $q->where('branch_id', BranchContext::currentBranchId()))
                ->orderByDesc('lead_id')
                ->pluck('lead_id', 'student_id');

        $usedHours = DB::table('bundle_usage_log')
            ->whereIn('enrollment_id', $enrollments->pluck('enrollment_id'))
            ->selectRaw('enrollment_id, SUM(hours_deducted) as used')
            ->groupBy('enrollment_id')
            ->pluck('used', 'enrollment_id');

        $rows = $enrollments->map(function ($e) use ($absenceCounts, $leadByStudent, $usedHours) {
            $bundleHours = $e->privateBundle?->hours ? (float) $e->privateBundle->hours : null;
            $remaining   = (float) $e->hours_remaining;
            $loggedUsed  = (float) ($usedHours[$e->enrollment_id] ?? 0);
            $totalPool   = max((float) ($bundleHours ?? 0), $remaining + $loggedUsed);
            $used        = $totalPool > 0 ? max(0, $totalPool - $remaining) : null;
            $usedPct     = $totalPool > 0
                ? min(100, round(($used / $totalPool) * 100))
                : 0;

            if ($e->status === 'Postponed') {
                $state = 'postponed';
            } elseif ($remaining <= 0) {
                $state = 'depleted';
            } elseif ($e->status === 'Restricted') {
                $state = 'restricted';
            } elseif ($e->status === 'Completed' && $remaining > 0) {
                $state = 'leftover';
            } elseif ($remaining <= 4) {
                $state = 'low';
            } else {
                $state = 'active';
            }

            $courseName = $e->courseTemplate?->name
                ?? $e->courseInstance?->courseTemplate?->name ?? '—';
            $levelName  = $e->level?->name ?? '—';
            if ($e->sublevel) $levelName .= ' · ' . $e->sublevel->name;

            $e->setAttribute('v_bundle_hours', $totalPool > 0 ? $totalPool : null);
            $e->setAttribute('v_remaining', $remaining);
            $e->setAttribute('v_used', $used);
            $e->setAttribute('v_used_pct', $usedPct);
            $e->setAttribute('v_absences', (int) ($absenceCounts[$e->enrollment_id] ?? 0));
            $e->setAttribute('v_state', $state);
            $e->setAttribute('v_course', $courseName);
            $e->setAttribute('v_level', $levelName);
            $e->setAttribute('v_lead_id', $leadByStudent[$e->student_id] ?? null);
            return $e;
        });

        $stats = [
            'total'    => $rows->count(),
            'active'   => $rows->where('v_state', 'active')->count(),
            'low'      => $rows->where('v_state', 'low')->count(),
            'depleted' => $rows->where('v_state', 'depleted')->count(),
            'restricted' => $rows->where('v_state', 'restricted')->count(),
            'leftover' => $rows->where('v_state', 'leftover')->count(),
            'postponed'=> $rows->where('v_state', 'postponed')->count(),
            'hours_left' => round($rows->sum('v_remaining'), 2),
        ];

        if (in_array($stateFilter, ['active', 'low', 'depleted', 'restricted', 'leftover', 'postponed'])) {
            $rows = $rows->where('v_state', $stateFilter)->values();
        }

        $rows = \App\Support\EnrollmentSearch::filter($rows, $search);

        $chargeId = (int) $request->query('charge', 0);
        if ($chargeId) {
            $rows = $rows->sortBy(fn($e) => (int) $e->enrollment_id === $chargeId ? 0 : 1)->values();
        }
        $rows   = \App\Support\PaginateCollection::make($rows, 25);
        $canAct = auth()->user()?->worksCs() ?? false;

        $bundles = PrivateBundle::active()->orderBy('hours')->get();

        return view('private-hours.index', compact('rows', 'stats', 'stateFilter', 'canAct', 'bundles', 'search'));
    }

    public function chargeBundle(Request $request, $enrollmentId)
    {
        $data = $request->validate([
            'bundle_id'      => 'required|exists:private_bundle,bundle_id',
            'payment_method' => 'required|in:Cash,Card,Instapay,Vodafone_Cash',
        ]);

        $enrollment = Enrollment::where('enrollment_type', 'Private')->findOrFail($enrollmentId);
        $bundle     = PrivateBundle::active()->findOrFail($data['bundle_id']);

        if (in_array($enrollment->status, ['Cancelled', 'Expired'])) {
            return back()->with('error', 'This enrollment is ' . strtolower($enrollment->status) . ' and cannot be charged.');
        }

        $courseId = $enrollment->course_template_id ?? $enrollment->courseInstance?->course_template_id;
        if ($courseId && !$bundle->availableForCourse($courseId)) {
            return back()->with('error', 'This bundle belongs to a different course. Please pick a bundle for the student\'s course.');
        }

        $methodMap = [
            'Cash'          => 'Cash',
            'Card'          => 'Card',
            'Instapay'      => 'Transfer',
            'Vodafone_Cash' => 'Online',
        ];

        $actor        = auth()->user();
        $actorEmpId   = $actor->employee?->employee_id;
        $creditEmpId  = ($actor->worksCs() && $actorEmpId) ? $actorEmpId : ($enrollment->created_by_cs_id ?? $actorEmpId);

        $currentPatch = Patch::active()->latest('start_date')->first();
        $patchId      = $currentPatch?->patch_id ?? $enrollment->patch_id;

        $hoursLabel = rtrim(rtrim(number_format($bundle->hours, 2), '0'), '.');
        $price      = round((float) $bundle->price, 2);

        $sentToWaiting = DB::transaction(function () use ($enrollment, $bundle, $data, $methodMap, $actorEmpId, $creditEmpId, $patchId, $hoursLabel, $price) {
            $enrollment = Enrollment::lockForUpdate()->find($enrollment->enrollment_id);

            $wasDepleted = (float) $enrollment->hours_remaining <= 0;

            $enrollment->hours_remaining = (float) $enrollment->hours_remaining + (float) $bundle->hours;
            $enrollment->final_price     = round((float) $enrollment->final_price + $price, 2);
            $enrollment->bundle_id       = $bundle->bundle_id;

            if ($wasDepleted && ($enrollment->status === 'Restricted' || $enrollment->restriction_flag)) {
                $enrollment->status = 'Active';
                $enrollment->restriction_flag = false;
            }

            $wasCompleted = $enrollment->status === 'Completed';
            $enrollment->save();

            $tx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $patchId,
                'branch_id'              => $enrollment->branch_id,
                'transaction_type'       => 'Payment',
                'transaction_category'   => 'Course',
                'amount'                 => $price,
                'payment_method'         => $methodMap[$data['payment_method']] ?? 'Cash',
                'notes'                  => 'Bundle top-up: ' . ($bundle->name ? $bundle->name . ' — ' : '') . $hoursLabel . ' hours',
                'created_by_employee_id' => $actorEmpId,
            ]);

            if ($creditEmpId && $patchId && $price > 0) {
                RevenueSplit::create([
                    'transaction_id'   => $tx->transaction_id,
                    'employee_id'      => $creditEmpId,
                    'branch_id'        => $enrollment->branch_id,
                    'patch_id'         => $patchId,
                    'amount_allocated' => $price,
                    'allocation_type'  => 'Direct',
                    'source'           => 'Registration',
                ]);
            }

            DB::table('deposit_payment')->insert([
                'enrollment_id'    => $enrollment->enrollment_id,
                'method'           => $data['payment_method'],
                'amount'           => $price,
                'reference_number' => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            if ($wasCompleted) {
                $this->moveToWaitingList($enrollment, 'Renewed with ' . $hoursLabel . ' hours bundle on ' . now()->format('d M Y') . '.');
                return true;
            }

            return false;
        });

        $message = 'Bundle added — ' . $hoursLabel . ' hours charged.';
        $message .= $sentToWaiting
            ? ' The student was sent to the Student Care waiting list to be placed in a new course.'
            : ' The student can continue their course.';

        return back()->with('success', $message);
    }

    public function sendToWaitingList($enrollmentId)
    {
        $enrollment = Enrollment::where('enrollment_type', 'Private')->findOrFail($enrollmentId);

        if ($enrollment->status !== 'Completed' || (float) $enrollment->hours_remaining <= 0) {
            return back()->with('error', 'Only students whose course has ended with hours left can be sent to the waiting list.');
        }

        DB::transaction(function () use ($enrollment) {
            $enrollment = Enrollment::lockForUpdate()->find($enrollment->enrollment_id);
            if ($enrollment->status !== 'Completed') {
                return;
            }
            $this->moveToWaitingList($enrollment, 'Continuing with ' . rtrim(rtrim(number_format((float) $enrollment->hours_remaining, 2), '0'), '.') . ' hours left, sent on ' . now()->format('d M Y') . '.');
        });

        return back()->with('success', 'Student sent to the Student Care waiting list with the remaining hours.');
    }

    private function moveToWaitingList(Enrollment $enrollment, string $note): void
    {
        $oldStatus = $enrollment->status;

        $nextPatch = Patch::where('status', 'Upcoming')->orderBy('start_date')->first();

        $enrollment->status             = 'Waiting';
        $enrollment->course_instance_id = null;
        $enrollment->patch_id           = $nextPatch?->patch_id ?? $enrollment->patch_id;
        $enrollment->save();

        $existing = WaitingList::where('enrollment_id', $enrollment->enrollment_id)->first();

        $waiting = WaitingList::updateOrCreate(
            ['enrollment_id' => $enrollment->enrollment_id],
            [
                'requested_patch_id'      => $nextPatch?->patch_id,
                'preferred_type'          => 'Next_Patch',
                'preferred_delivery_type' => 'Private',
                'preferred_delivery_mood' => $enrollment->delivery_mood,
                'preferred_days'          => $existing?->preferred_days,
                'status'                  => 'Active',
                'notes'                   => trim(($existing?->notes ? $existing->notes . "\n" : '') . $note),
                'created_by_cs_id'        => $existing?->created_by_cs_id ?? $enrollment->created_by_cs_id,
            ]
        );

        AuditService::updated('enrollment', $enrollment->enrollment_id, 'status', $oldStatus, 'Waiting');

        DB::afterCommit(function () use ($waiting) {
            try {
                event(new WaitingListUpdated($waiting));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WaitingListUpdated broadcast failed: ' . $e->getMessage());
            }
        });
    }
}
