<?php

namespace App\Http\Controllers;

use App\Models\Leads\Lead;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Patch;
use App\Models\Finance\PaymentPlan;
use App\Models\Finance\PrivateBundle;
use App\Services\PatchService;
use App\Services\PricingService;
use App\Models\Academic\TimeSlot;

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
            return back()->with('error', 'Already registered');
        }

        $courses   = CourseTemplate::where('is_active', true)->get();
        $levels    = Level::all();
        $sublevels = Sublevel::all();
        $timeSlots = TimeSlot::all();

        $instances = CourseInstance::all();
        $patches = Patch::all();
        $paymentPlans = PaymentPlan::all();
        $bundles = PrivateBundle::all();

        return view('registration.create', compact(
            'lead',
            'courses',
            'levels',
            'sublevels',
            'instances',
            'patches',
            'paymentPlans',
            'timeSlots',
            'bundles',
        ));
    }
    

    /*
    |------------------------------------------------------------------
    | STORE 
    |------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([

            'lead_id' => 'required|exists:lead,lead_id',
            'type' => 'required|in:group,private',

            'course_instance_id' => 'required',

            'payment_plan_id' => 'required',

            'patch_option' => 'required|in:current,next,custom',

            'teacher_id' => 'nullable',
            'day' => 'required_if:type,private',
            'time_slot_id' => 'required_if:type,private',

            'custom_date' => 'nullable|date'
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

    public function getPatchOptions($courseId)
    {
        $options = app(PatchService::class)->getOptions($courseId);

        return response()->json($options);
    }

    public function calculatePrice(Request $request)
    {
        $result = app(PricingService::class)->calculate($request->all());

        return response()->json($result); 
    }

    public function getAvailableTeachers(Request $request)
    {
        $teachers = app(\App\Services\TeacherAvailabilityService::class)
            ->getAvailableTeachers($request->all());

        return response()->json($teachers);
    }
    
}