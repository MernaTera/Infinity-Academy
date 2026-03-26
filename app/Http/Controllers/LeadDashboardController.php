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
        // ── TOTAL STATS ──
        $stats = [
            'total'        => Lead::count(),
            'registered'   => Lead::where('status', 'Registered')->count(),
            'call_again'   => Lead::where('status', 'Call_Again')->count(),
            'waiting'      => Lead::where('status', 'Waiting')->count(),
            'archived'     => Lead::where('status', 'Archived')->count(),
            'public'       => Lead::whereNull('owner_cs_id')->where('is_active', true)->count(),
        ];

        // ── TODAY ──
        $today = Lead::whereDate('created_at', now()->toDateString())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── THIS WEEK ──
        $week = Lead::where('created_at', '>=', now()->startOfWeek()->toDateTimeString())
            ->where('created_at', '<=', now()->endOfWeek()->toDateTimeString())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── THIS MONTH ──
        $month = Lead::where('created_at', '>=', now()->startOfMonth()->toDateTimeString())
            ->where('created_at', '<=', now()->endOfMonth()->toDateTimeString())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── BY SOURCE ──
        $bySource = Lead::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->orderByDesc('count')
            ->pluck('count', 'source')
            ->toArray();

        // ── BY COURSE ──
        $byCourse = Lead::whereNotNull('interested_course_template_id')
            ->join('course_template', 'lead.interested_course_template_id', '=', 'course_template.course_template_id')
            ->select('course_template.name', DB::raw('count(*) as count'))
            ->groupBy('course_template.name')
            ->orderByDesc('count')
            ->pluck('count', 'course_template.name')
            ->toArray();

        // ── BY CS EMPLOYEE ──
        $byCs = Lead::whereNotNull('owner_cs_id')
            ->join('employee', 'lead.owner_cs_id', '=', 'employee.employee_id')
            ->join('users', 'employee.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as count'))
            ->groupBy('users.name')
            ->orderByDesc('count')
            ->pluck('count', 'users.name')
            ->toArray();

        // ── RECENT LEADS ──
        $recentLeads = Lead::latest()->limit(10)->get();

        
            return view('leads.dashboard', compact(
                        'stats', 'today', 'week', 'month',
                        'bySource', 'byCourse', 'byCs',
                        'recentLeads'
                    ));
        
    }
}

