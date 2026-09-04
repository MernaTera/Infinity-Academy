<?php

namespace App\Http\Controllers;

use App\Services\LeadService;
use App\Interfaces\LeadRepositoryInterface;
use App\Http\Requests\StoreLeadRequest;
use Illuminate\Http\Request;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Leads\Lead;
use App\Models\Leads\LeadHistory;

class LeadController extends Controller
{
    protected $leadService;
    protected $leadRepository;

    public function __construct(
        LeadService $leadService,
        LeadRepositoryInterface $leadRepository
    ) {
        $this->leadService    = $leadService;
        $this->leadRepository = $leadRepository;

        $this->middleware('permission:leads.view')->only(['index', 'publicLeads', 'archived']);
        $this->middleware('permission:leads.create')->only(['create', 'store']);
        $this->middleware('permission:leads.edit')->only(['edit', 'update']);
        $this->middleware('permission:leads.delete')->only(['destroy']);
    }

    private function currentEmployeeId(): int
    {
        $employee = auth()->user()->employee;
        if (!$employee) abort(403, 'No employee profile found.');
        return $employee->employee_id;
    }

    public function index()
    {
        $employeeId = $this->currentEmployeeId();
        $base = Lead::where('owner_cs_id', $employeeId);

        $stats = [
            'total'      => (clone $base)->count(),
            'registered' => (clone $base)->where('status', 'Registered')->count(),
            'call_again' => (clone $base)->where('status', 'Call_Again')->count(),
            'waiting'    => (clone $base)->where('status', 'Waiting')->count(),
            'archived' => Lead::where('status', 'Archived')
                  ->whereNull('owner_cs_id')
                  ->count(),
        ];

        $leads = $this->leadRepository->myLeads($employeeId);

        $studentIdsOnPage = $leads->pluck('student_id')->filter()->unique()->all();
        $pendingApprovalStudentIds = empty($studentIdsOnPage) ? [] :
            \App\Models\Enrollment\Enrollment::withoutGlobalScope('branch')
                ->whereIn('student_id', $studentIdsOnPage)
                ->where('status', 'Pending_Approval')
                ->pluck('student_id')
                ->unique()
                ->all();

        $registeredRows = collect();
        $registeredLeads = $leads->where('status', 'Registered')
            ->filter(fn($l) => $l->student_id);

        if ($registeredLeads->isNotEmpty()) {
            $studentIds = $registeredLeads->pluck('student_id')->unique()->all();

            $enrollments = \App\Models\Enrollment\Enrollment::whereIn('student_id', $studentIds)
                ->whereIn('status', ['Active', 'Pending_Approval', 'Waiting', 'Completed', 'Restricted'])
                ->with(['courseTemplate', 'level', 'sublevel'])
                ->orderByDesc('enrollment_id')
                ->get()
                ->groupBy('student_id');

            foreach ($registeredLeads as $lead) {
                $studentEnrollments = $enrollments->get($lead->student_id, collect());
                if ($studentEnrollments->isEmpty()) {
                    if ($lead->student_id) {
                        $registeredRows->push(['lead' => $lead, 'enrollment' => null]);
                    }
                } else {
                    foreach ($studentEnrollments as $enr) {
                        $registeredRows->push(['lead' => $lead, 'enrollment' => $enr]);
                    }
                }
            }
        }

        return view('leads.index', compact('leads', 'stats', 'registeredRows', 'pendingApprovalStudentIds'));
    }

    public function publicLeads()
    {
        $leads = $this->leadRepository->publicLeads();
        return view('leads.public', compact('leads'));
    }

    public function archived()
    {
        $leads = $this->leadRepository->archivedLeads();
        return view('leads.archived', compact('leads'));
    }

    public function create()
    {
        $courses = CourseTemplate::where('is_active', true)->get();

        return view('leads.create', compact('courses'));
    }

    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'Waiting';

        $this->leadService->createLead($data);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead added successfully.');
    }

    public function edit($id)
    {
        $lead    = $this->leadRepository->find($id);

        if ($lead->is_pending_approval) {
            return redirect()->route('leads.index')
                ->with('error', 'This lead is awaiting admin approval and cannot be edited until it is approved or rejected.');
        }

        $courses = CourseTemplate::where('is_active', true)->get();

        $levels    = $lead->interested_course_template_id
            ? Level::where('course_template_id', $lead->interested_course_template_id)->get()
            : collect();

        $sublevels = $lead->interested_level_id
            ? Sublevel::where('level_id', $lead->interested_level_id)->get()
            : collect();

        return view('leads.edit', compact('lead', 'courses', 'levels', 'sublevels'));
    }

    public function update(Request $request, $id)
    {
        $lead = $this->leadRepository->find($id);

        if ($lead->is_pending_approval) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This lead is awaiting admin approval and cannot be edited until it is approved or rejected.',
                ], 422);
            }
            return redirect()->route('leads.index')
                ->with('error', 'This lead is awaiting admin approval and cannot be edited until it is approved or rejected.');
        }

        if ($request->expectsJson()) {
            $lead = $this->leadRepository->find($id);
            $old  = $lead->status;
            $data = [];

            if ($request->has('status'))       $data['status']      = $request->status;
            if ($request->has('next_call_at')) {
                $data['next_call_at'] = $request->next_call_at
                    ? \Carbon\Carbon::parse($request->next_call_at)->format('Y-m-d H:i:s')
                    : null;
            }

            if (!$lead->owner_cs_id) {
                $data['owner_cs_id'] = $this->currentEmployeeId();
            }

            $this->leadRepository->update($id, $data);
            $lead->refresh();

            \App\Services\LeadActivityLogger::for($lead)
                ->action('Status_Changed')
                ->status($old, $lead->status)
                ->notes('Updated from status dropdown')
                ->record();

            return response()->json(['success' => true]);
        }

        $lead      = $this->leadRepository->find($id);
        $oldStatus = $lead->status;
        $validated = app(\App\Http\Requests\StoreLeadRequest::class)->validated();

        $trackedFields = [
            'full_name', 'phone_1', 'phone_2', 'source',
            'interested_course_template_id', 'interested_level_id', 'interested_sublevel_id',
            'notes', 'branch_id',
        ];
        $changedFields = \App\Services\LeadActivityLogger::detectChanges($lead, $validated, $trackedFields);

        $this->leadRepository->update($id, $validated);
        $lead->refresh();

        if (!empty($changedFields)) {
            \App\Services\LeadActivityLogger::for($lead)
                ->action('Data_Updated')
                ->changed($changedFields)
                ->notes('Lead edited from form')
                ->record();
        }

        if ($oldStatus !== $lead->status) {
            \App\Services\LeadActivityLogger::for($lead)
                ->action('Status_Changed')
                ->status($oldStatus, $lead->status)
                ->notes('Changed while editing lead')
                ->record();
        }

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function assign($id)
    {
        $employeeId = $this->currentEmployeeId();
        $lead       = Lead::findOrFail($id);
        $oldOwner   = $lead->owner_cs_id;
        $oldStatus  = $lead->status;

        $lead->update([
            'owner_cs_id' => $employeeId,
            'status'      => 'Waiting',
            'is_active'   => true,
        ]);

        \App\Services\LeadActivityLogger::for($lead)
            ->action('Owner_Changed')
            ->changed(['owner_cs_id' => ['from' => $oldOwner, 'to' => $employeeId]])
            ->reason('Taken from ' . request()->input('source', 'public') . ' leads')
            ->record();

        if ($oldStatus !== 'Waiting') {
            \App\Services\LeadActivityLogger::for($lead)
                ->action('Status_Changed')
                ->status($oldStatus, 'Waiting')
                ->notes('Auto-reset to Waiting on assignment')
                ->record();
        }

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:lead,lead_id',
            'status'  => 'required|string',
        ]);

        $lead      = $this->leadRepository->find($request->lead_id);

        if ($lead->is_pending_approval) {
            return response()->json([
                'success' => false,
                'message' => 'This lead is awaiting admin approval and cannot be edited until it is approved or rejected.',
            ], 422);
        }

        $oldStatus = $lead->status;

        $this->leadRepository->update($lead->lead_id, ['status' => $request->status]);
        $lead->refresh();

        $this->leadService->logHistory($lead, $oldStatus, $lead->status, 'Status updated from dropdown');

        return response()->json(['success' => true]);
    }

    public function history($id)
    {
        $history = LeadHistory::where('lead_id', $id)
            ->orderBy('changed_at', 'desc')
            ->get()
            ->map(function ($h) {
                $employee = \App\Models\HR\Employee::find($h->changed_by);
                
                $enrichedFields = $this->enrichChangedFields($h->changed_fields ?? []);

                return [
                    'history_id'      => $h->history_id,
                    'action_type'     => $h->action_type ?? 'Status_Changed',
                    'old_status'      => $h->old_status,
                    'new_status'      => $h->new_status,
                    'changed_fields'  => $enrichedFields,
                    'reason'          => $h->reason,
                    'call_outcome'    => $h->call_outcome,
                    'notes'           => $h->notes,
                    'ip_address'      => $h->ip_address,
                    'changed_at'      => $h->changed_at,
                    'changed_by_id'   => $h->changed_by,
                    'changed_by_name' => $employee?->full_name ?? 'System',
                ];
            });

        return response()->json($history);
    }


    private function enrichChangedFields(array $fields): array
    {
        $labels = [
            'full_name'                     => 'Full Name',
            'phone_1'                       => 'Primary Phone',
            'phone_2'                       => 'Secondary Phone',
            'source'                        => 'Source',
            'notes'                         => 'Notes',
            'branch_id'                     => 'Branch',
            'owner_cs_id'                   => 'Owner (CS)',
            'interested_course_template_id' => 'Interested Course',
            'interested_level_id'           => 'Interested Level',
            'interested_sublevel_id'        => 'Interested Sublevel',
            'status'                        => 'Status',
            'next_call_at'                  => 'Next Call At',
        ];

        $enriched = [];
        foreach ($fields as $key => $change) {
            $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));

            $from = $change['from'] ?? null;
            $to   = $change['to']   ?? null;

            if ($key === 'interested_course_template_id') {
                $from = $from ? (\App\Models\Academic\CourseTemplate::find($from)?->name ?? "ID:{$from}") : '—';
                $to   = $to   ? (\App\Models\Academic\CourseTemplate::find($to)?->name   ?? "ID:{$to}")   : '—';
            } elseif ($key === 'interested_level_id') {
                $from = $from ? (\App\Models\Academic\Level::find($from)?->name ?? "ID:{$from}") : '—';
                $to   = $to   ? (\App\Models\Academic\Level::find($to)?->name   ?? "ID:{$to}")   : '—';
            } elseif ($key === 'interested_sublevel_id') {
                $from = $from ? (\App\Models\Academic\Sublevel::find($from)?->name ?? "ID:{$from}") : '—';
                $to   = $to   ? (\App\Models\Academic\Sublevel::find($to)?->name   ?? "ID:{$to}")   : '—';
            } elseif ($key === 'branch_id') {
                $from = $from ? (\App\Models\Academic\Branch::find($from)?->name ?? "ID:{$from}") : '—';
                $to   = $to   ? (\App\Models\Academic\Branch::find($to)?->name   ?? "ID:{$to}")   : '—';
            } elseif ($key === 'owner_cs_id') {
                $from = $from ? (\App\Models\HR\Employee::find($from)?->full_name ?? "ID:{$from}") : 'Unassigned';
                $to   = $to   ? (\App\Models\HR\Employee::find($to)?->full_name   ?? "ID:{$to}")   : 'Unassigned';
            } else {
                $from = $from === null || $from === '' ? '—' : (string) $from;
                $to   = $to   === null || $to   === '' ? '—' : (string) $to;
            }

            $enriched[] = [
                'label' => $label,
                'from'  => $from,
                'to'    => $to,
            ];
        }

        return $enriched;
    }

    public function destroy($id)
    {
        $this->leadRepository->delete($id);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead deleted.');
    }

    public function showInvoice($leadId)
    {
        $lead = Lead::findOrFail($leadId);

        if (!$lead->student_id) {
            return back()->with('error', 'This lead has not been registered yet.');
        }

        $enrollment = \App\Models\Enrollment\Enrollment::where('student_id', $lead->student_id)
            ->whereIn('status', ['Active', 'Pending_Approval', 'Waiting', 'Completed'])
            ->latest('enrollment_id')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'No enrollment found for this lead.');
        }

        return app(\App\Http\Controllers\RegistrationController::class)
            ->showInvoice($enrollment->enrollment_id);
    }

    public function showReceipt($leadId)
    {
        $lead = Lead::findOrFail($leadId);

        if (!$lead->student_id) {
            return back()->with('error', 'This lead has not been registered yet.');
        }

        $enrollment = \App\Models\Enrollment\Enrollment::where('student_id', $lead->student_id)
            ->whereIn('status', ['Active', 'Pending_Approval', 'Waiting', 'Completed'])
            ->latest('enrollment_id')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'No enrollment found for this lead.');
        }

        return app(\App\Http\Controllers\RegistrationController::class)
            ->showReceipt($enrollment->enrollment_id);
    }
}