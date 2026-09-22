<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment\Postponement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminPostponementController extends Controller
{
    public function index(Request $request)
    {
        // Admin postponed = same monitor view as Student Care (no Resume —
        // that's the CS's job). Admin keeps Mark Expired. Uses the shared blade.
        $groupPostponed = Postponement::with([
            'enrollment.student.phones',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.courseInstance.courseTemplate',
            'enrollment.attendances',
            'createdBy',
        ])
        ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Group'))
        ->whereIn('status', ['Active', 'Expired'])
        ->orderBy('status')->orderByDesc('created_at')->get();

        $privatePostponed = Postponement::with([
            'enrollment.student.phones',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.privateBundle',
            'enrollment.courseInstance.courseTemplate',
            'createdBy',
        ])
        ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Private'))
        ->whereIn('status', ['Active', 'Expired'])
        ->orderBy('status')->orderByDesc('created_at')->get();

        $stats = [
            'active'   => Postponement::where('status', 'Active')->count(),
            'expired'  => Postponement::where('status', 'Expired')->count(),
            'returned' => Postponement::where('status', 'Returned')->count(),
            'expiring_soon' => Postponement::where('status', 'Active')
                ->where('expected_return_date', '<=', now()->addDays(7))->count(),
        ];

        return view('student-care.postponed', [
            'groupPostponed'   => $groupPostponed,
            'privatePostponed' => $privatePostponed,
            'stats'            => $stats,
            'canRegister'      => false,                    // admin monitors only
            'expireBase'       => url('admin/postponed'),   // admin expire route base
        ]);
    }

    public function resume($id)
    {
        $postponement = Postponement::with('enrollment')->findOrFail($id);

        if ($postponement->status !== 'Active') {
            return back()->with('error', 'Only active postponements can be resumed.');
        }

        $postponement->update([
            'status'             => 'Returned',
            'actual_return_date' => now()->toDateString(),
        ]);
        $postponement->enrollment->update(['status' => 'Active']);

        return back()->with('success', 'Student resumed successfully.');
    }

    public function expire($id)
    {
        $postponement = Postponement::with('enrollment')->findOrFail($id);

        $postponement->update(['status' => 'Expired']);
        $postponement->enrollment->update(['status' => 'Expired']);

        return back()->with('success', 'Postponement marked as expired. Enrollment cancelled.');
    }

    public function extend(Request $request, $id)
    {
        $request->validate([
            'new_return_date' => 'required|date|after:today',
        ]);

        $postponement = Postponement::findOrFail($id);

        if ($postponement->status !== 'Active') {
            return back()->with('error', 'Only active postponements can be extended.');
        }

        $start   = Carbon::parse($postponement->start_date);
        $newDate = Carbon::parse($request->new_return_date);
        $days    = $start->diffInDays($newDate);

        if ($days > 90) {
            return back()->with('error', "Cannot extend beyond 90 days from start date (current: {$days} days).");
        }

        $postponement->update(['expected_return_date' => $newDate->toDateString()]);
        return back()->with('success', 'Postponement extended successfully.');
    }

    public function forceCancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:3']);

        $postponement = Postponement::with('enrollment')->findOrFail($id);

        $postponement->update([
            'status'         => 'Expired',
            'admin_note'     => 'Force-cancelled by admin. Reason: ' . $request->reason,
        ]);
        $postponement->enrollment->update(['status' => 'Cancelled']);

        return back()->with('success', 'Postponement force-cancelled.');
    }
}