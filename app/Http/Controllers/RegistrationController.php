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

        if ($lead->status === 'Registered' && $lead->student_id) {
            return back()->with('error', 'This lead is already registered.');
        }

        $courses      = CourseTemplate::where('is_active', true)->get();
        $paymentPlans = PaymentPlan::where('is_active', true)->get();
        $bundles      = PrivateBundle::all();
        $timeSlots    = TimeSlot::all();

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
        ));
    }

    /*
    |------------------------------------------------------------------
    | Store Registration
    |------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'lead_id'            => 'required|exists:lead,lead_id',
            'type'               => 'required|in:group,private',
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'payment_plan_id'    => 'required',
            'patch_option'       => 'required|in:current,next,custom',
            'teacher_id'         => 'nullable',
            'day'                => 'nullable',
            'custom_date'        => 'nullable|date',
        ]);

        $plan          = \App\Models\Finance\PaymentPlan::find($request->payment_plan_id);
        $finalPrice    = (float) $request->final_price;
        $materialPrice = (float) $request->material_price;
        $testFee       = (float) $request->test_fee;

        if ($plan && $plan->deposit_percentage > 0 && $finalPrice > 0) {
            $depositOnCourse = round($finalPrice * $plan->deposit_percentage / 100, 2);
            $requiredDeposit = round($depositOnCourse + $materialPrice + $testFee, 2);
            $methods   = $request->input('deposit_methods', []);
            $totalPaid = round(collect($methods)->sum(fn($m) => (float)($m['amount'] ?? 0)), 2);

            if (abs($totalPaid - $requiredDeposit) > 0.01) {
                return back()->withInput()->withErrors([
                    'deposit_methods' => "Deposit total ({$totalPaid} LE) must equal required ({$requiredDeposit} LE)."
                ]);
            }
        }

        try {
            // ✅ مرة واحدة بس
            $enrollment = $this->registrationService->register($request->all());

            if ($plan && $plan->requires_admin_approval) {
                return redirect()->route('registration.pending', $enrollment->enrollment_id);
            }

            return redirect()->route('leads.index')->with('success', 'Student registered successfully.');

        } catch (\Throwable $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage() . ' — Please try again or contact support.');
        }
    }

    /*
    |------------------------------------------------------------------
    | AJAX Helpers
    |------------------------------------------------------------------
    */
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

    public function getMaterial(Request $request)
    {
        $sublevelId = $request->sublevel_id ?: null;
        $levelId    = $request->level_id    ?: null;
        $courseId   = $request->course_template_id ?: null;

        $material = null;

        if ($sublevelId) {
            $material = DB::table('material_assignment')
                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                ->where('materials.is_active', true)
                ->where('material_assignment.sublevel_id', $sublevelId)
                ->select('materials.material_id', 'materials.name', 'materials.price', 'material_assignment.is_mandatory')
                ->first();
        }

        if (!$material && $levelId) {
            $material = DB::table('material_assignment')
                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                ->where('materials.is_active', true)
                ->where('material_assignment.level_id', $levelId)
                ->whereNull('material_assignment.sublevel_id')
                ->select('materials.material_id', 'materials.name', 'materials.price', 'material_assignment.is_mandatory')
                ->first();
        }

        if (!$material && $courseId) {
            $material = DB::table('material_assignment')
                ->join('materials', 'materials.material_id', '=', 'material_assignment.material_id')
                ->where('materials.is_active', true)
                ->where('material_assignment.course_template_id', $courseId)
                ->whereNull('material_assignment.level_id')
                ->whereNull('material_assignment.sublevel_id')
                ->select('materials.material_id', 'materials.name', 'materials.price', 'material_assignment.is_mandatory')
                ->first();
        }

        return response()->json($material);
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
        if (!$enrollment) return response()->json(['status' => 'not_found'], 404);
    
        $log = \App\Models\Finance\InstallmentApprovalLog::where('enrollment_id', $enrollmentId)
            ->latest()
            ->first();
    
        return response()->json([
            'status'          => $enrollment->status,
            'approval_status' => $log?->status,
            'rejection_note'  => $log?->rejection_note,
        ]);
    }
 
}