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

    // ─────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────
    private function currentEmployeeId(): int
    {
        $employee = auth()->user()->employee;
        if (!$employee) abort(403, 'No employee profile found.');
        return $employee->employee_id;
    }

    /*
    |--------------------------------------------------------------------------
    | My Leads (Follow-up list)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $leads = $this->leadRepository->myLeads($this->currentEmployeeId());
        return view('leads.index', compact('leads'));
    }

    /*
    |--------------------------------------------------------------------------
    | Public Leads
    |--------------------------------------------------------------------------
    */
    public function publicLeads()
    {
        $leads = $this->leadRepository->publicLeads();
        return view('leads.public', compact('leads'));
    }

    /*
    |--------------------------------------------------------------------------
    | Archived Leads
    |--------------------------------------------------------------------------
    */
    public function archived()
    {
        $leads = $this->leadRepository->archivedLeads();
        return view('leads.archived', compact('leads'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Lead Form
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $courses = CourseTemplate::where('is_active', true)->get();

        // levels & sublevels start empty — JS fetches them dynamically on course/level change
        return view('leads.create', compact('courses'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store Lead
    |--------------------------------------------------------------------------
    */
    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'Waiting';

        $this->leadService->createLead($data);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Lead
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $lead    = $this->leadRepository->find($id);
        $courses = CourseTemplate::where('is_active', true)->get();

        // Pre-load existing levels/sublevels for edit mode
        $levels    = $lead->interested_course_template_id
            ? Level::where('course_template_id', $lead->interested_course_template_id)->get()
            : collect();

        $sublevels = $lead->interested_level_id
            ? Sublevel::where('level_id', $lead->interested_level_id)->get()
            : collect();

        return view('leads.edit', compact('lead', 'courses', 'levels', 'sublevels'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update Lead
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
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

            $this->leadService->logHistory($lead, $old, $lead->status, 'Updated from status dropdown');

            return response()->json(['success' => true]);
        }

        $lead      = $this->leadRepository->find($id);
        $old       = $lead->status;
        $validated = app(StoreLeadRequest::class)->validated();

        $this->leadRepository->update($id, $validated);
        $lead->refresh();

        $this->leadService->logHistory($lead, $old, $lead->status, 'Updated from edit form');

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Assign (from public list)
    |--------------------------------------------------------------------------
    */
    public function assign($id)
    {
        $employeeId = $this->currentEmployeeId();
        $lead       = Lead::findOrFail($id);
        $old        = $lead->status;

        $lead->update([
            'owner_cs_id' => $employeeId,
            'status'      => 'Waiting',
            'is_active'   => true,
        ]);

        $this->leadService->logHistory($lead, $old, 'Waiting', 'Taken from ' . request()->input('source', 'public') . ' leads');

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Status (inline dropdown)
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:lead,lead_id',
            'status'  => 'required|string',
        ]);

        $lead      = $this->leadRepository->find($request->lead_id);
        $oldStatus = $lead->status;

        $this->leadRepository->update($lead->lead_id, ['status' => $request->status]);
        $lead->refresh();

        $this->leadService->logHistory($lead, $oldStatus, $lead->status, 'Status updated from dropdown');

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Lead History (AJAX)
    |--------------------------------------------------------------------------
    */
    public function history($id)
    {
        $history = LeadHistory::where('lead_id', $id)
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json($history);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Lead
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $this->leadRepository->delete($id);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead deleted.');
    }
}