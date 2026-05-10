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

        // ✅ Base = leads مملوكة للـ CS دي (حالياً أو كانت)
        // الـ archived فقدوا الـ owner_cs_id — نجيبهم من الـ lead_history
        $base = Lead::where('owner_cs_id', $employeeId);

        // ✅ Leads اللي كانت بتاعت الـ CS دي + اتأرشفت
        $myArchivedIds = DB::table('lead_history')
            ->where('changed_by', $employeeId)  // employee_id مش user_id
            ->where('new_status', 'Archived')   // new_status مش new_value
            ->pluck('lead_id');

        $stats = [
            'total'      => (clone $base)->count() +
                            Lead::whereIn('lead_id', $myArchivedIds)->count(),

            'registered' => (clone $base)->where('status', 'Registered')->count(),

            'call_again' => (clone $base)->where('status', 'Call_Again')->count(),

            'waiting'    => (clone $base)->where('status', 'Waiting')->count(),

            // ✅ Archived = اللي أرشفتهم أنا
            'archived'   => Lead::whereIn('lead_id', $myArchivedIds)->count(),

            // ✅ Public = active + بدون owner + مش archived
            'public'     => Lead::whereNull('owner_cs_id')
                                ->where('is_active', true)
                                ->whereNotIn('status', ['Archived', 'Registered'])
                                ->count(),
        ];

        // ── Period stats (my leads only) ─────────────────────────────
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

        // ── Distribution ──────────────────────────────────────────────
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

        // ── Recent leads ──────────────────────────────────────────────
        $recentLeads = (clone $base)->with('courseTemplate')->latest()->limit(10)->get();

        return view('leads.dashboard', compact(
            'stats', 'today', 'week', 'month',
            'bySource', 'byCourse', 'byCs', 'recentLeads'
        ));
    }
}