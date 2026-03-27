<?php

namespace App\Http\Controllers;

use App\Services\LeadService;
use App\Interfaces\LeadRepositoryInterface;
use App\Http\Requests\StoreLeadRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Leads\Lead;


class LeadController extends Controller
{
    protected $leadService;
    protected $leadRepository;

    public function __construct(
        LeadService $leadService,
        LeadRepositoryInterface $leadRepository
    ) {
        $this->leadService = $leadService;
        $this->leadRepository = $leadRepository;

        $this->middleware('permission:leads.view')->only(['index','publicLeads','archived']);
        $this->middleware('permission:leads.create')->only(['create','store']);
        $this->middleware('permission:leads.edit')->only(['edit','update']);
        $this->middleware('permission:leads.delete')->only(['destroy']);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Leads (Follow-up list)
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $employeeId = auth()->user()->employees->first()->employee_id;

        $leads = $this->leadRepository->myLeads($employeeId);

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
            $levels    = Level::all();
            $sublevels = Sublevel::all();
        return view('leads.create', compact('courses','levels','sublevels'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store Lead
    |--------------------------------------------------------------------------
    */

    public function store(StoreLeadRequest $request)
    {
        $lead = $this->leadService->createLead($request->validated());

        return redirect()
            ->route('leads.index')
            ->with('success','Lead created successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Lead
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $lead = $this->leadRepository->find($id);
        $courses   = CourseTemplate::where('is_active', true)->get();
        $levels    = Level::all();
        $sublevels = Sublevel::all();

        return view('leads.edit', compact( 'lead','courses','levels','sublevels'));
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

            if ($request->has('status')) {
                $data['status'] = $request->status;
            }

            if ($request->has('next_call_at')) {
                $data['next_call_at'] = $request->next_call_at;
            }

            if (!$lead->owner_cs_id) {
                $data['owner_cs_id'] = auth()->user()->employees->first()->employee_id;
            }

            $this->leadRepository->update($id, $data);

            $lead->refresh();

            $this->leadService->logHistory(
                $lead,
                $old,
                $lead->status,
                'Updated from status'
            );

            return response()->json(['success' => true]);
        }

        // ── Form update (edit page) ──
        $lead = $this->leadRepository->find($id);
        $old  = $lead->status;

        $validated = app(StoreLeadRequest::class)->validated();

        $this->leadRepository->update($id, $validated);

        $lead->refresh();

        $this->leadService->logHistory(
            $lead,
            $old,
            $lead->status,
            'Updated from edit form'
        );

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead updated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Schedule Call
    |--------------------------------------------------------------------------
    */

    public function scheduleCall(Request $request, $id)
    {
        $request->validate([
            'next_call_at' => 'required|date'
        ]);

        $this->leadService->scheduleCall($id,$request->next_call_at);

        return back()->with('success','Call scheduled successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Call Again
    |--------------------------------------------------------------------------
    */

    public function callAgain(Request $request, $id)
    {
        $request->validate([
            'next_call_at' => 'required|date'
        ]);

        $this->leadService->markCallAgain($id,$request->next_call_at);

        return back()->with('success','Lead scheduled for follow-up');
    }

    /*
    |--------------------------------------------------------------------------
    | Mark Registered
    |--------------------------------------------------------------------------
    */

    public function markRegistered($id)
    {
        $this->leadService->markRegistered($id);

        return redirect()
            ->route('registration.create',['lead'=>$id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Not Interested
    |--------------------------------------------------------------------------
    */

    public function notInterested($id)
    {
        $this->leadService->markNotInterested($id);

        return back()->with('success','Lead marked as not interested');
    }

    /*
    |--------------------------------------------------------------------------
    | Archive Lead
    |--------------------------------------------------------------------------
    */

    public function archive($id)
    {
        $this->leadService->archiveLead($id);

        return back()->with('success','Lead archived');
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
            ->with('success','Lead deleted');
    }

    public function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'source' => 'required',
            'degree' => 'required',
            'location' => 'nullable|string|max:255',
            'interested_course_template_id' => 'nullable|exists:course_template,course_template_id',
            'interested_level_id' => 'nullable|exists:level,level_id',
            'interested_sublevel_id' => 'nullable|exists:sublevel,sublevel_id',
            'next_call_at' => 'nullable|date',
            'notes' => 'nullable|string'
        ];
    }

    public function assign($id)
    {
        $employeeId = auth()->user()->employees->first()->employee_id;

        $lead = Lead::findOrFail($id);
        $old  = $lead->status;

        $lead->update([
            'owner_cs_id' => $employeeId,
            'status'      => 'Waiting',
            'is_active'   => true,
        ]);

        $source = request()->input('source', 'public');

        $this->leadService->logHistory(
            $lead,
            $old,
            'Waiting',
            'Taken from ' . $source . ' leads'
        );
        return response()->json(['success' => true]);
    }

    public function history($id)
    {
        $history = \App\Models\Leads\LeadHistory::where('lead_id', $id)
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json($history);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,lead_id',
            'status' => 'required|string'
        ]);

        $lead = $this->leadRepository->find($request->lead_id);

        $oldStatus = $lead->status;

        $this->leadRepository->update($lead->lead_id, [
            'status' => $request->status
        ]);

        $lead->refresh();

        $this->leadService->logHistory(
            $lead,
            $oldStatus,
            $lead->status,
            'Status updated from dropdown'
        );

        return response()->json([
            'success' => true
        ]);
    }

}