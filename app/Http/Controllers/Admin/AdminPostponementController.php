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
        return view('student-care.postponed', array_merge(
            \App\Support\PostponedListing::data($request),
            [
                'canRegister' => false,
                'expireBase'  => url('admin/postponed'),
            ]
        ));
    }

    public function resume($id)
    {
        $postponement = Postponement::with('enrollment')->whereHas('enrollment')->findOrFail($id);

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
        $postponement = Postponement::with('enrollment')->whereHas('enrollment')->findOrFail($id);

        $postponement->update(['status' => 'Expired']);
        $postponement->enrollment->update(['status' => 'Expired']);

        return back()->with('success', 'Postponement marked as expired. Enrollment cancelled.');
    }

    public function extend(Request $request, $id)
    {
        $request->validate([
            'new_return_date' => 'required|date|after:today',
        ]);

        $postponement = Postponement::whereHas('enrollment')->findOrFail($id);

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

        $postponement = Postponement::with('enrollment')->whereHas('enrollment')->findOrFail($id);

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