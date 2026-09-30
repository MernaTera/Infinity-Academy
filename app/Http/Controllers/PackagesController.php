<?php

namespace App\Http\Controllers;

use App\Support\BranchContext;

use App\Models\Enrollment\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackagesController extends Controller
{

    public function index(Request $request)
    {
        $stateFilter = $request->query('state', 'all');
        $enrollments = Enrollment::with([
                'student',
                'courseTemplate',
                'level',
                'sublevel',
                'levelPackage',
                'courseInstance',
            ])
            ->whereNotNull('package_id')
            ->where(function ($q) {
                $q->whereIn('status', ['Active', 'Restricted', 'Waiting', 'Completed', 'Postponed']);
            })
            ->orderByDesc('enrollment_id')
            ->get();

        $latestPerPackage = $enrollments
            ->groupBy(fn($e) => $e->student_id . '-' . $e->package_id)
            ->map(fn($group) => $group->first()) // already ordered desc by id
            ->values();

        $studentIds = $latestPerPackage->pluck('student_id')->unique()->filter()->all();
        $leadByStudent = empty($studentIds) ? collect() :
            DB::table('lead')
                ->whereIn('student_id', $studentIds)
                ->when(BranchContext::currentBranchId() !== null, fn($q) => $q->where('branch_id', BranchContext::currentBranchId()))
                ->orderByDesc('lead_id')
                ->pluck('lead_id', 'student_id');

        $rows = $latestPerPackage->map(function ($e) use ($leadByStudent) {
            $package    = $e->levelPackage;
            $nominal    = $package ? (int) $package->levels_count : 0;
            $remaining  = (int) ($e->package_units_remaining ?? 0);
            $totalUnits = max($nominal, $remaining);
            $doneUnits  = $totalUnits > 0 ? max(0, $totalUnits - $remaining) : null;
            $donePct    = $totalUnits > 0
                ? min(100, round(($doneUnits / $totalUnits) * 100))
                : 0;

            if ($e->status === 'Postponed') {
                $state = 'postponed';
            } elseif ($e->status === 'Completed' && $remaining > 0) {
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

        $stats = [
            'total'     => $rows->count(),
            'active'    => $rows->where('v_state', 'active')->count(),
            'available' => $rows->where('v_state', 'available')->count(),
            'done'      => $rows->where('v_state', 'done')->count(),
            'postponed' => $rows->where('v_state', 'postponed')->count(),
            'units_left'=> (int) $rows->sum('v_remaining'),
        ];

        if (in_array($stateFilter, ['active', 'available', 'done', 'postponed'])) {
            $rows = $rows->where('v_state', $stateFilter)->values();
        }

        $canAct = auth()->user()?->worksCs() ?? false;

        return view('packages-tracking.index', compact('rows', 'stats', 'stateFilter', 'canAct'));
    }
}