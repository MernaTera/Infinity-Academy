<?php

namespace App\Http\Controllers;

use App\Models\Leads\Lead;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Services\RegistrationService;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /*
    |------------------------------------------------------------------
    | Show Form (from lead)
    |------------------------------------------------------------------
    */
    public function createFromLead($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);

        if ($lead->status === 'Registered' && $lead->student_id) {
            return redirect()->back()->with('error', 'Already registered');
        }

        $courses   = CourseTemplate::where('is_active', true)->get();
        $levels    = Level::all();
        $sublevels = Sublevel::all();

        return view('registration.create', [
            'lead' => $lead,
            'courses' => $courses,
            'levels' => $levels,
            'sublevels' => $sublevels,
            'isRegistration' => true
        ]);
    }

    /*
    |------------------------------------------------------------------
    | STORE 
    |------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required',
            'interested_course_template_id' => 'required',
            'type' => 'required|in:group,private',
        ]);

        try {

            $this->registrationService->register($request->all());

            return redirect()
                ->route('leads.index')
                ->with('success', 'Student registered successfully');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }
}