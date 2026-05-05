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

    $stats = [
        'total'      => (clone $base)->count(),
        'registered' => (clone $base)->where('status', 'Registered')->count(),
        'call_again' => (clone $base)->where('status', 'Call_Again')->count(),
        'waiting'    => (clone $base)->where('status', 'Waiting')->count(),
        'archived' => Lead::where('status', 'Archived')
                  ->whereNull('owner_cs_id')
                  ->count(),
        'public'     => Lead::whereNull('owner_cs_id')->where('is_active', true)->count(),
    ];

    $today = (clone $base)->whereDate('created_at', now()->toDateString())
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')->pluck('count', 'status')->toArray();

    $week = (clone $base)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')->pluck('count', 'status')->toArray();

    $month = (clone $base)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')->pluck('count', 'status')->toArray();

    $bySource = (clone $base)->select('source', DB::raw('count(*) as count'))
        ->groupBy('source')->orderByDesc('count')
        ->pluck('count', 'source')->toArray();

    $byCourse = (clone $base)->whereNotNull('interested_course_template_id')
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

    $recentLeads = (clone $base)->latest()->limit(10)->get();

    return view('leads.dashboard', compact(
        'stats', 'today', 'week', 'month',
        'bySource', 'byCourse', 'byCs', 'recentLeads'
    ));
}
}

