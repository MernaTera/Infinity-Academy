<?php

namespace App\Http\Controllers;

use App\Support\BranchContext;

use App\Models\Enrollment\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackagesController extends Controller
{
    /**
     * Level-package dashboard — one row per student on a level package,
     * showing the course/level they're currently on, how many prepaid units
     * (levels/sublevels) remain, and a derived state. Mirrors the Private
     * Hours screen: shared by Admin & Student Care (view-only) and CS
     * (with the "Continue Package" action).
     */
    public function index(Request $request)
    {
        $stateFilter = $request->query('state', 'all');
        // all | active | available | done

        // Every enrolment that belongs to a level package. A student can have
        // several (one per level already taken); we keep only the most recent
        // per (student, package) so each package shows as a single row — the
        // level they're on now.
        $enrollments = Enrollment::with([
                'student',
                'courseTemplate',
                'level',
                'sublevel',
                'levelPackage',
                'courseInstance',
            ])
            ->whereNotNull('package_id')
            ->orderByDesc('enrollment_id')
            ->get();

        // Collapse to the latest enrolment per (student + package).
        $latestPerPackage = $enrollments
            ->groupBy(fn($e) => $e->student_id . '-' . $e->package_id)
            ->map(fn($group) => $group->first()) // already ordered desc by id
            ->values();

        // Map each student to their lead, so the action button can open the
        // registration form pre-filled from that lead. student_id → lead_id.
        $studentIds = $latestPerPackage->pluck('student_id')->unique()->filter()->all();
        $leadByStudent = empty($studentIds) ? collect() :
            DB::table('lead')
                ->whereIn('student_id', $studentIds)
                ->when(BranchContext::currentBranchId() !== null, fn($q) => $q->where('branch_id', BranchContext::currentBranchId()))
                ->orderByDesc('lead_id')
                ->pluck('lead_id', 'student_id');

        // Attach derived fields + classify state.
        $rows = $latestPerPackage->map(function ($e) use ($leadByStudent) {
            $package    = $e->levelPackage;
            $totalUnits = $package ? (int) $package->levels_count : null;
            $remaining  = (int) ($e->package_units_remaining ?? 0);
            $doneUnits  = $totalUnits !== null ? max(0, $totalUnits - $remaining) : null;
            $donePct    = ($totalUnits && $totalUnits > 0)
                ? min(100, round(($doneUnits / $totalUnits) * 100))
                : 0;

            // State classification:
            //   available → current level finished (Completed) and prepaid
            //               units remain → CS can open the next (free) course
            //   done      → package fully consumed (no units left)
            //   active    → currently studying a level in the package
            if ($e->status === 'Completed' && $remaining > 0) {
                $state = 'available';
            } elseif ($remaining <= 0) {
                $state = 'done';
            } else {
                $state = 'active';
            }

            $courseName = $e->courseTemplate?->name
                ?? $e->courseInstance?->courseTemplate?->name ?? '—';
            $levelName  = $e->level?->name ?? '—';
            if ($e->sublevel) $levelName .= ' · ' . $e->sublevel->name;

            $e->setAttribute('v_package_name', $package?->name ?? '—');
            $e->setAttribute('v_total_units', $totalUnits);
            $e->setAttribute('v_remaining', $remaining);
            $e->setAttribute('v_done_units', $doneUnits);
            $e->setAttribute('v_done_pct', $donePct);
            $e->setAttribute('v_state', $state);
            $e->setAttribute('v_course', $courseName);
            $e->setAttribute('v_level', $levelName);
            $e->setAttribute('v_lead_id', $leadByStudent[$e->student_id] ?? null);
            return $e;
        });

        // Headline stats (over the full set, before filtering).
        $stats = [
            'total'     => $rows->count(),
            'active'    => $rows->where('v_state', 'active')->count(),
            'available' => $rows->where('v_state', 'available')->count(),
            'done'      => $rows->where('v_state', 'done')->count(),
            'units_left'=> (int) $rows->sum('v_remaining'),
        ];

        // Apply the state filter for display.
        if (in_array($stateFilter, ['active', 'available', 'done'])) {
            $rows = $rows->where('v_state', $stateFilter)->values();
        }

        // CS (the registration/booking role) gets the action button; Admin & SC
        // are view-only.
        $canAct = auth()->user()?->isCS() ?? false;

        return view('packages-tracking.index', compact('rows', 'stats', 'stateFilter', 'canAct'));
    }
}