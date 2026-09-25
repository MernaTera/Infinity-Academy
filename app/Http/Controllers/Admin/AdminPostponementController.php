<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment\Postponement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminPostponementController extends Controller
{
    /**
     * Admin postponed board. Admin renders the SAME shared view as Student Care
     * — monitor + expire, no resume (re-registration is a CS action). Lists and
     * stats are scoped through the enrollment relation so they stay consistent.
     */
    public function index(Request $request)
    {
        $with = [
            'enrollment.student.phones',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.privateBundle',
            'enrollment.levelPackage',
            'enrollment.attendances',
            'enrollment.courseInstance.courseTemplate',
            'createdBy',
        ];

        $groupPostponed = Postponement::with($with)
            ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Group'))
            ->whereIn('status', ['Active', 'Expired'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->get();

        $privatePostponed = Postponement::with($with)
            ->whereHas('enrollment', fn($q) => $q->where('enrollment_type', 'Private'))
            ->whereIn('status', ['Active', 'Expired'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'active'        => Postponement::where('status', 'Active')
                                    ->whereHas('enrollment', fn($q) => $q)->count(),
            'expired'       => Postponement::where('status', 'Expired')
                                    ->whereHas('enrollment', fn($q) => $q)->count(),
            'returned'      => Postponement::where('status', 'Returned')
                                    ->whereHas('enrollment', fn($q) => $q)->count(),
            'expiring_soon' => Postponement::where('status', 'Active')
                                    ->whereHas('enrollment', fn($q) => $q)
                                    ->whereDate('expected_return_date', '<=', now()->addDays(7))
                                    ->count(),
        ];

        return view('student-care.postponed', array_merge(
            compact('groupPostponed', 'privatePostponed', 'stats'),
            [
                'canRegister' => false,
                'expireBase'  => url('admin/postponed'),
            ]
        ));
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

        // Note: the postponement table has no admin_note column — record the
        // reason in the existing `reason` field instead of a phantom column.
        $existingReason = $postponement->reason ? $postponement->reason . ' | ' : '';
        $postponement->update([
            'status' => 'Expired',
            'reason' => $existingReason . 'Force-cancelled by admin: ' . $request->reason,
        ]);

        if ($postponement->enrollment) {
            $postponement->enrollment->update(['status' => 'Cancelled']);
        }

        return back()->with('success', 'Postponement force-cancelled.');
    }
}