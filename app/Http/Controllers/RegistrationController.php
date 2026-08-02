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
use App\Models\Finance\TestFeeSetting;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /*
    |------------------------------------------------------------------
    | Show Registration Form (from lead)
    |------------------------------------------------------------------
    */
    public function createFromLead($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);

        // Allow multiple registrations for the same lead if the query parameter 'renew' is present
        // if ($lead->status === 'Registered' && !request()->query('renew')) {
        //     return back()->with('error', 'This lead is already registered.');
        // }

        $courses      = CourseTemplate::where('is_active', true)->get();
        $paymentPlans = PaymentPlan::where('is_active', true)->get();
        $bundles      = PrivateBundle::all();
        $timeSlots    = TimeSlot::all();
        $testFees = TestFeeSetting::active()->orderBy('fee')->get();


        $levels = $lead->interested_course_template_id
            ? Level::where('course_template_id', $lead->interested_course_template_id)->get()
            : collect();

        $levelBelongsToCourse = $levels->contains('level_id', $lead->interested_level_id);

        $sublevels = ($lead->interested_level_id && $levelBelongsToCourse)
            ? Sublevel::where('level_id', $lead->interested_level_id)->get()
            : collect();

        if (!$levelBelongsToCourse) {
            $lead->interested_level_id    = null;
            $lead->interested_sublevel_id = null;
        }

        return view('registration.create', compact(
            'lead',
            'courses',
            'levels',
            'sublevels',
            'paymentPlans',
            'bundles',
            'timeSlots',
            'testFees',
        ));
    }

    /*
    |------------------------------------------------------------------
    | Store Registration
    |------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
                    'lead_id'            => 'required|exists:lead,lead_id',
                    'type'               => 'required|in:group,private',
                    'mode'               => 'required|in:Online,Offline',             
                    'course_template_id' => 'required|exists:course_template,course_template_id',
                    'level_id'           => 'nullable|exists:level,level_id',
                    'sublevel_id'        => 'nullable|exists:sublevel,sublevel_id',
                    'payment_plan_id'    => 'required|exists:payment_plan,payment_plan_id',
                    'patch_option'       => 'required|in:current,next,custom',
                    'patch_id'           => 'nullable|exists:patch,patch_id',
                    'teacher_id'         => 'nullable|exists:teacher,teacher_id',
                    'course_instance_id' => 'nullable|exists:course_instance,course_instance_id',
                    'day'                => 'nullable|string',
                    'custom_date'        => 'nullable|date|after:today',              
                    'final_price'        => 'required|numeric|min:0',
                    'discount_value'     => 'nullable|numeric|min:0',                
                    'test_fee'           => 'nullable|numeric|min:0',
                    'test_score'         => 'nullable|string|max:50',

                    'deposit_methods'            => 'nullable|array',
                    'deposit_methods.*.method'   => 'required_with:deposit_methods|in:Cash,Instapay,Vodafone_Cash',
                    'deposit_methods.*.amount'   => 'required_with:deposit_methods|numeric|min:0',
                ], [
                    'mode.required'                    => 'Please select a delivery mode (Online or Offline).',
                    'mode.in'                          => 'Invalid delivery mode selected.',
                    'deposit_methods.*.method.in'      => 'One of the payment methods is invalid.',
                    'deposit_methods.*.amount.numeric' => 'Payment amounts must be valid numbers.',
                    'deposit_methods.*.amount.min'     => 'Payment amounts cannot be negative.',
                    'custom_date.after'                => 'The start date must be in the future.',
                    'final_price.required'             => 'Course price could not be determined. Please re-select the course.',
                ]);
        $discountValue = (float) ($request->discount_value ?? 0);
        $finalPriceVal = (float) $request->final_price;

        if ($discountValue > 0) {
            $basePrice = $finalPriceVal + $discountValue;

            if ($discountValue >= $basePrice) {
                return back()->withInput()->withErrors([
                    'discount_value' => "Discount ({$discountValue} LE) cannot be equal to or greater than the course price ({$basePrice} LE)."
                ]);
            }
        }

        $plan          = \App\Models\Finance\PaymentPlan::find($request->payment_plan_id);
        $finalPrice    = (float) $request->final_price;
        $testFee       = (float) $request->test_fee;

        // Material total = sum of the selected materials' real prices (from DB),
        // supporting multiple materials per course. Falls back to the posted
        // material_price only if no ids were sent.
        $selectedMaterialIds = collect($request->input('material_ids', []))
            ->map(fn($id) => (int) $id)->filter()->unique();
        if ($selectedMaterialIds->isNotEmpty()) {
            $materialPrice = (float) \App\Models\Enrollment\Material::whereIn('material_id', $selectedMaterialIds)
                ->where('is_active', true)->sum('price');
        } else {
            $materialPrice = (float) $request->material_price;
        }

        if ($plan && $plan->deposit_percentage > 0 && $finalPrice > 0) {
            $depositOnCourse = round($finalPrice * $plan->deposit_percentage / 100, 2);
            $requiredDeposit = round($depositOnCourse + $materialPrice + $testFee, 2);
            $methods   = $request->input('deposit_methods', []);

            $validMethods = collect($methods)->filter(fn($m) => (float)($m['amount'] ?? 0) > 0);

            if ($validMethods->isEmpty()) {
                return back()->withInput()->withErrors([
                    'deposit_methods' => "At least one payment method with a positive amount is required (required deposit: {$requiredDeposit} LE)."
                ]);
            }

            $totalPaid = round($validMethods->sum(fn($m) => (float)($m['amount'] ?? 0)), 2);

            if (abs($totalPaid - $requiredDeposit) > 0.01) {
                return back()->withInput()->withErrors([
                    'deposit_methods' => "Deposit total ({$totalPaid} LE) must equal required ({$requiredDeposit} LE)."
                ]);
            }
        }

        try {
            $lead = Lead::find($request->lead_id);

            $oldStatus     = $lead->status;
            $oldInterests  = [
                'interested_course_template_id' => $lead->interested_course_template_id,
                'interested_level_id'           => $lead->interested_level_id,
                'interested_sublevel_id'        => $lead->interested_sublevel_id,
            ];

            $newInterests = [
                'interested_course_template_id' => $request->course_template_id,
                'interested_level_id'           => $request->level_id ?: null,
                'interested_sublevel_id'        => $request->sublevel_id ?: null,
            ];
            $lead->update($newInterests);

            $interestChanges = [];
            foreach ($newInterests as $key => $val) {
                if ((string) $oldInterests[$key] !== (string) $val) {
                    $interestChanges[$key] = [
                        'from' => $oldInterests[$key],
                        'to'   => $val,
                    ];
                }
            }
            if (!empty($interestChanges)) {
                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Interest_Updated')
                    ->changed($interestChanges)
                    ->reason('Updated during registration form')
                    ->record();
            }

            $enrollment = $this->registrationService->register($request->all());
            $lead->refresh(); 

            $courseName = \App\Models\Academic\CourseTemplate::find($request->course_template_id)?->name ?? 'course';

            if ($plan && $plan->requires_admin_approval) {
                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Note_Added')
                    ->reason("Registration submitted for admin approval in \"{$courseName}\" (Enrollment #{$enrollment->enrollment_id})")
                    ->notes('Waiting for admin approval before status changes to Registered.')
                    ->record();

                if ($request->ajax()) {
                    return response()->json([
                        'success'       => true,
                        'enrollment_id' => $enrollment->enrollment_id,
                        'redirect'      => route('registration.pending', $enrollment->enrollment_id),
                        'pending'       => true,
                    ]);
                }
                return redirect()->route('registration.pending', $enrollment->enrollment_id);
            }

            \App\Services\LeadActivityLogger::for($lead)
                ->action('Registered')
                ->status($oldStatus, 'Registered')
                ->reason("Registered in \"{$courseName}\" (Enrollment #{$enrollment->enrollment_id})")
                ->record();

            if ($request->ajax()) {
                return response()->json([
                    'success'       => true,
                    'enrollment_id' => $enrollment->enrollment_id,
                    'redirect'      => route('leads.index'),
                ]);
            }

            return redirect()->route('leads.index')->with('success', 'Student registered successfully.');

            } catch (\App\Exceptions\BusinessValidationException $e) {
                return back()
                    ->withInput()
                    ->with('error', $e->getMessage());

            } catch (\Throwable $e) {
                \Log::error('Registration failed', [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'lead_id' => $request->lead_id ?? null,
                    'trace'   => $e->getTraceAsString(),
                ]);
                return back()
                    ->withInput()
                    ->with('error', 'Something went wrong while processing the registration. Please try again, or contact support if the problem persists.');
            }
    }

    /*
    |------------------------------------------------------------------
    | AJAX Helpers
    |------------------------------------------------------------------
    */

    public function getPatchOptions(Request $request)
    {
        $validated = $request->validate([
            'course_template_id' => 'required|integer|exists:course_template,course_template_id',
            'type'               => 'nullable|string|in:Group,Private',
            'delivery_mood'      => 'nullable|string|in:Online,Offline',
            'level_id'           => 'nullable|integer|exists:level,level_id',
            'sublevel_id'        => 'nullable|integer|exists:sublevel,sublevel_id',
        ]);

        $options = app(PatchService::class)->getOptions($validated);
        return response()->json($options);
    }


    public function getPatchOptionsLegacy($courseId)
    {
        $options = app(PatchService::class)->getOptions([
            'course_template_id' => $courseId,
        ]);
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

    public function getMaterial(Request $request)
    {
        $sublevelId = $request->sublevel_id ?: null;
        $levelId    = $request->level_id    ?: null;
        $courseId   = $request->course_template_id ?: null;

        // Return ALL materials assigned at the most specific matching level.
        // Priority: sublevel → level → course. Within the first level that
        // has any assignments, return every material (mandatory + optional).
        $materials = collect();

        if ($sublevelId) {
            $materials = DB::table('material_assignment')
                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                ->where('materials.is_active', true)
                ->where('material_assignment.sublevel_id', $sublevelId)
                ->select('materials.material_id', 'materials.name', 'materials.price', 'materials.revenue_type', 'material_assignment.is_mandatory')
                ->get();
        }

        if ($materials->isEmpty() && $levelId) {
            $materials = DB::table('material_assignment')
                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                ->where('materials.is_active', true)
                ->where('material_assignment.level_id', $levelId)
                ->whereNull('material_assignment.sublevel_id')
                ->select('materials.material_id', 'materials.name', 'materials.price', 'materials.revenue_type', 'material_assignment.is_mandatory')
                ->get();
        }

        if ($materials->isEmpty() && $courseId) {
            $materials = DB::table('material_assignment')
                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                ->where('materials.is_active', true)
                ->where('material_assignment.course_template_id', $courseId)
                ->whereNull('material_assignment.level_id')
                ->whereNull('material_assignment.sublevel_id')
                ->select('materials.material_id', 'materials.name', 'materials.price', 'materials.revenue_type', 'material_assignment.is_mandatory')
                ->get();
        }

        // Normalise types (is_mandatory as bool, price as float)
        $materials = $materials->map(fn($m) => [
            'material_id'  => (int) $m->material_id,
            'name'         => $m->name,
            'price'        => (float) $m->price,
            'revenue_type' => $m->revenue_type,
            'is_mandatory' => (bool) $m->is_mandatory,
        ])->values();

        return response()->json($materials);
    }

    public function getTeacherSchedule(Request $request)
    {
        $availability = \App\Models\HR\TeacherAvailability::where('teacher_id', $request->teacher_id)->get();
        return response()->json($availability);
    }

    public function getLevelPackages($courseId)
    {
        $packages = \App\Models\Finance\LevelPackage::active()
            ->forCourse($courseId)
            ->orderBy('levels_count')
            ->get(['package_id', 'name', 'levels_count', 'package_price']);
    
        return response()->json($packages);
    }

    public function pending($enrollmentId)
    {
        $enrollment = \App\Models\Enrollment\Enrollment::with([
            'student', 'courseTemplate', 'paymentPlan'
        ])->findOrFail($enrollmentId);
    
        return view('registration.pending', compact('enrollment'));
    }
    
    public function checkApprovalStatus($enrollmentId)
    {
        $enrollment = \App\Models\Enrollment\Enrollment::find($enrollmentId);
        
        $log = \App\Models\Finance\InstallmentApprovalLog::where('enrollment_id', $enrollmentId)
            ->latest()->first();

        $note = $log?->rejection_note;
        if ($note && str_contains($note, '||')) {
            $note = explode('||', $note)[1];
        }

        if (!$enrollment) {
            return response()->json([
                'status'          => 'Cancelled',
                'approval_status' => $log?->status ?? 'Rejected',
                'rejection_note'  => $note, 
            ]);
        }

        return response()->json([
            'status'          => $enrollment->status,
            'approval_status' => $log?->status,
            'rejection_note'  => $note, 
        ]);
    }

    //--------------------------------------------------
    public function showInvoice($id)
    {
        $enrollment = \App\Models\Enrollment\Enrollment::with([
            'student.phones',
            'courseInstance.courseTemplate',
            'courseInstance.level',
            'courseInstance.patch',
            'courseInstance.teacher.employee',
            'patch', 
            'paymentPlan',
            'financialTransactions',
            'installmentSchedules',
            'waitingLists',
        ])->findOrFail($id);

        return view('registration.invoice-page', compact('enrollment'));
    }
}