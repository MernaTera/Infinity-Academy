<?php

namespace App\Http\Controllers;

use App\Models\Enrollment\Enrollment;
use App\Models\Attendance\Attendance;
use App\Models\Finance\PrivateBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivateHoursController extends Controller
{
    /**
     * Private hours dashboard — lists every private enrolment with its
     * remaining bundle hours, current course/level, absences, and a derived
     * state. Shared by Admin, Student Care (view-only) and CS (with actions).
     */
    public function index(Request $request)
    {
        $stateFilter = $request->query('state', 'all');
        // all | active | low | depleted | leftover

        // Every private enrolment that actually tracks hours.
        $enrollments = Enrollment::with([
                'student',
                'courseTemplate',
                'level',
                'sublevel',
                'courseInstance.courseTemplate',
                'privateBundle',
            ])
            ->where('enrollment_type', 'Private')
            ->whereNotNull('hours_remaining')
            ->whereIn('status', ['Active', 'Restricted', 'Completed'])
            ->orderByDesc('updated_at')
            ->get();

        // Absence counts per enrolment (single grouped query).
        $enrollIds = $enrollments->pluck('enrollment_id')->all();
        $absenceCounts = empty($enrollIds) ? collect() :
            Attendance::whereIn('enrollment_id', $enrollIds)
                ->where('status', 'Absent')
                ->select('enrollment_id', DB::raw('COUNT(*) as c'))
                ->groupBy('enrollment_id')
                ->pluck('c', 'enrollment_id');

        // Map each student to their lead, so the action buttons can open the
        // registration form pre-filled from that lead. student_id → lead_id.
        $studentIds = $enrollments->pluck('student_id')->unique()->filter()->all();
        $leadByStudent = empty($studentIds) ? collect() :
            DB::table('lead')
                ->whereIn('student_id', $studentIds)
                ->orderByDesc('lead_id')
                ->pluck('lead_id', 'student_id');

        // Attach derived fields + classify state.
        $rows = $enrollments->map(function ($e) use ($absenceCounts, $leadByStudent) {
            $bundleHours = $e->privateBundle?->hours ? (float) $e->privateBundle->hours : null;
            $remaining   = (float) $e->hours_remaining;
            $used        = $bundleHours !== null ? max(0, $bundleHours - $remaining) : null;
            $usedPct     = ($bundleHours && $bundleHours > 0)
                ? min(100, round(($used / $bundleHours) * 100))
                : 0;

            // State classification:
            //   depleted → Restricted / no hours left (needs a new bundle)
            //   leftover → course finished (Completed) but hours remain
            //   low      → still active but running low (<= 4h)
            //   active   → healthy
            if ($e->status === 'Restricted' || $remaining <= 0) {
                $state = 'depleted';
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

            $e->setAttribute('v_bundle_hours', $bundleHours);
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

        // Headline stats (over the full set, before filtering).
        $stats = [
            'total'    => $rows->count(),
            'active'   => $rows->where('v_state', 'active')->count(),
            'low'      => $rows->where('v_state', 'low')->count(),
            'depleted' => $rows->where('v_state', 'depleted')->count(),
            'leftover' => $rows->where('v_state', 'leftover')->count(),
            'hours_left' => round($rows->sum('v_remaining'), 2),
        ];

        // Apply the state filter for display.
        if (in_array($stateFilter, ['active', 'low', 'depleted', 'leftover'])) {
            $rows = $rows->where('v_state', $stateFilter)->values();
        }

        // CS (the registration/booking role) gets action buttons; Admin & SC
        // are view-only.
        $canAct = auth()->user()?->isCS() ?? false;

        // Bundles list for the "Add Bundle" modal.
        $bundles = PrivateBundle::orderBy('hours')->get();

        return view('private-hours.index', compact('rows', 'stats', 'stateFilter', 'canAct', 'bundles'));
    }

    /**
     * Add Bundle — top up a private student's hours on the SAME enrolment.
     * Adds the bundle's hours to hours_remaining, records a Payment
     * transaction (no revenue split — it still counts toward revenue), lifts
     * any restriction, and lets the student resume the same course.
     */
    public function chargeBundle(Request $request, $enrollmentId)
    {
        $data = $request->validate([
            'bundle_id'      => 'required|exists:private_bundle,bundle_id',
            'payment_method' => 'required|in:Cash,Card,Instapay,Vodafone_Cash',
        ]);

        $enrollment = Enrollment::where('enrollment_type', 'Private')->findOrFail($enrollmentId);
        $bundle     = PrivateBundle::findOrFail($data['bundle_id']);

        $methodMap = [
            'Cash'          => 'Cash',
            'Card'          => 'Card',
            'Instapay'      => 'Transfer',
            'Vodafone_Cash' => 'Online',
        ];

        $csEmployeeId = auth()->user()->employee?->employee_id;

        DB::transaction(function () use ($enrollment, $bundle, $data, $methodMap, $csEmployeeId) {

            // 1. Add the hours to the SAME enrolment.
            $enrollment->hours_remaining = (float) $enrollment->hours_remaining + (float) $bundle->hours;

            // 2. Lift restriction — the reason (no hours) is now resolved.
            if ($enrollment->status === 'Restricted' || $enrollment->restriction_flag) {
                $enrollment->status = 'Active';
                $enrollment->restriction_flag = false;
            }

            // Point the enrolment at the newly purchased bundle.
            $enrollment->bundle_id = $bundle->bundle_id;
            $enrollment->save();

            // 3. Record a Payment transaction (counts as revenue, NO split).
            $tx = \App\Models\Finance\FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $enrollment->patch_id,
                'branch_id'              => $enrollment->branch_id,
                'transaction_type'       => 'Payment',
                'transaction_category'   => 'Course',
                'amount'                 => (float) $bundle->price,
                'payment_method'         => $methodMap[$data['payment_method']] ?? 'Cash',
                'notes'                  => 'Bundle top-up: ' . rtrim(rtrim(number_format($bundle->hours, 2), '0'), '.') . ' hours',
                'created_by_employee_id' => $csEmployeeId,
            ]);

            // 4. Audit the raw payment.
            DB::table('deposit_payment')->insert([
                'enrollment_id'    => $enrollment->enrollment_id,
                'method'           => $data['payment_method'],
                'amount'           => (float) $bundle->price,
                'reference_number' => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        });

        return back()->with('success',
            'Bundle added — ' . rtrim(rtrim(number_format($bundle->hours, 2), '0'), '.') . ' hours charged. The student can resume their course.');
    }
}