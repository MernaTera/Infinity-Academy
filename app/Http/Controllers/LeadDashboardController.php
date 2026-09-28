<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads\Lead;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\DB;

class LeadDashboardController extends Controller
{
    public function index()
    {
        $employeeId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        $base = Lead::where('owner_cs_id', $employeeId);

        $totalArchived = Lead::where('status', 'Archived')
            ->where('is_active', false)
            ->count();

        $stats = [
            'total'      => (clone $base)->count(),

            'registered' => (clone $base)->where('status', 'Registered')->count(),

            'call_again' => (clone $base)->where('status', 'Call_Again')->count(),

            'waiting'    => (clone $base)->where('status', 'Waiting')->count(),

            'scheduled'  => (clone $base)->where('status', 'Scheduled_Call')->count(),

            'not_interested' => (clone $base)->where('status', 'Not_Interested')->count(),

            'archived'   => $totalArchived,

            'public'     => Lead::whereNull('owner_cs_id')
                                ->where('is_active', true)
                                ->count(),

            'due_today'  => (clone $base)
                                ->whereNotNull('next_call_at')
                                ->whereDate('next_call_at', now()->toDateString())
                                ->whereIn('status', ['Waiting', 'Call_Again', 'Scheduled_Call'])
                                ->count(),

            'overdue'    => (clone $base)
                                ->whereNotNull('next_call_at')
                                ->whereDate('next_call_at', '<', now()->toDateString())
                                ->whereIn('status', ['Waiting', 'Call_Again', 'Scheduled_Call'])
                                ->count(),
        ];

        $today = (clone $base)
            ->whereDate('updated_at', now()->toDateString())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $week = (clone $base)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $month = (clone $base)
            ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $bySource = (clone $base)
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')->orderByDesc('count')
            ->pluck('count', 'source')->toArray();

        $byCourse = (clone $base)
            ->whereNotNull('interested_course_template_id')
            ->join('course_template', 'lead.interested_course_template_id', '=', 'course_template.course_template_id')
            ->select('course_template.name', DB::raw('count(*) as count'))
            ->groupBy('course_template.name')->orderByDesc('count')
            ->pluck('count', 'course_template.name')->toArray();

        $byCs = Lead::whereNotNull('owner_cs_id')
            ->join('employee', 'lead.owner_cs_id', '=', 'employee.employee_id')
            ->join('users', 'employee.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as count'))
            ->groupBy('users.name')->orderByDesc('count')
            ->pluck('count', 'users.name')->toArray();

        $recentLeads = (clone $base)->with('courseTemplate')->latest()->limit(10)->get();

        $upcomingFollowUps = (clone $base)
            ->whereNotNull('next_call_at')
            ->whereIn('status', ['Waiting', 'Call_Again', 'Scheduled_Call'])
            ->with('courseTemplate')
            ->orderBy('next_call_at')
            ->limit(8)
            ->get();

        return view('leads.dashboard', compact(
            'stats', 'today', 'week', 'month',
            'bySource', 'byCourse', 'byCs', 'recentLeads', 'upcomingFollowUps'
        ));
    }
}