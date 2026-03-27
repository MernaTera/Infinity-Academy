<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RegistrationService; 
use App\Http\Requests\StoreRegistrationRequest; 
use App\DTOs\RegistrationDTO;
use App\Models\Academic\Level;
use App\Models\Finance\PaymentPlan;
use App\Services\PatchService;
use App\Models\Academic\CourseTemplate;

class RegistrationController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /*
    |--------------------------------------------------------------------------
    | Register Student (from Lead or Manual)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->all();

        $leadId = $request->input('lead_id');

        $enrollment = $this->registrationService->register($data, $leadId);

        return response()->json([
            'message' => 'Student registered successfully',
            'data' => $enrollment
        ]);
    }

    public function create()
    {
        
        $levels = Level::all();
        $plans = PaymentPlan::all();
        $courses = CourseTemplate::all();
        $patchOptions = app(PatchService::class)->getAvailableOptions([
            'course_template_id' => $courses->first()->course_template_id ?? null
        ]);

        return view('registration.create', compact('levels', 'plans', 'courses', 'patchOptions'));
    }
}
