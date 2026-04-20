<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\System\AuditLog;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('employee.user')
            ->orderByDesc('created_at');

        // Filters
        if ($request->filled('table')) {
            $query->where('table_name', $request->table);
        }

        if ($request->filled('action')) {
            $query->where('action_type', $request->action);
        }

        if ($request->filled('employee')) {
            $query->where('changed_by', $request->employee);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('table_name', 'like', "%{$s}%")
                  ->orWhere('field_name', 'like', "%{$s}%")
                  ->orWhere('old_value',  'like', "%{$s}%")
                  ->orWhere('new_value',  'like', "%{$s}%");
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        // Stats
        $stats = [
            'total'   => AuditLog::count(),
            'today'   => AuditLog::whereDate('created_at', today())->count(),
            'creates' => AuditLog::where('action_type', 'Create')->count(),
            'updates' => AuditLog::where('action_type', 'Update')->count(),
            'deletes' => AuditLog::where('action_type', 'Delete')->count(),
        ];

        // Filter options
        $tables    = AuditLog::distinct()->pluck('table_name')->sort()->values();
        $employees = Employee::with('user')->orderBy('full_name')->get();

        return view('admin.audit.index', compact('logs', 'stats', 'tables', 'employees'));
    }
}