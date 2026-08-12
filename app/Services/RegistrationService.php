<?php

namespace App\Services;

use App\Models\Leads\Lead;
use App\Models\Student\Student;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\WaitingList;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\PaymentPlan;
use App\Models\Finance\PrivateBundle;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseInstance;
use App\Models\HR\TeacherAvailability;
use App\Models\Enrollment\PlacementTest;
use App\Models\Enrollment\Material;
use App\Models\Enrollment\MaterialAssignment;
use App\Models\Enrollment\EnrollmentMaterial;
use App\Events\WaitingListUpdated;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Student\StudentPhone;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\TestFeeSetting;
use App\Models\HR\Employee;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\InstallmentApprovalLog;
use App\Exceptions\BusinessValidationException;

class RegistrationService
{
    public function register($data)
    {
        $pendingNotifications = [];

        $enrollment = DB::transaction(function () use ($data, &$pendingNotifications) {

            $this->validateBusiness($data);
            $this->validatePatchOptionAvailability($data); 
            $this->validateTeacherAvailability($data);
            $this->validateNoConflict($data);
            $this->validatePatch($data);
            $this->validatePricing($data);

            $lead = Lead::findOrFail($data['lead_id']);

            // Reuse the student this lead is already linked to (e.g. a private
            // leftover "New Course", a package continuation, or any re-
            // registration). Only create a brand-new student the first time.
            // Creating a new one each time would split the same person across
            // multiple student_ids and scatter their enrolments/hours.
            $student = $lead->student_id ? Student::find($lead->student_id) : null;

            if ($student) {
                // Keep the student's details fresh from the lead, without
                // touching their identity or history.
                $student->update([
                    'full_name' => $lead->full_name,
                    'email'     => $lead->email,
                    'birthdate' => $lead->birthdate,
                    'degree'    => $lead->degree,
                    'location'  => $lead->location,
                    'is_active' => true,
                ]);
            } else {
                $student = Student::create([
                    'full_name'     => $lead->full_name,
                    'email'         => $lead->email,
                    'birthdate'     => $lead->birthdate,
                    'degree'        => $lead->degree,
                    'location'      => $lead->location,
                    'global_status' => 'Active',
                    'is_active'     => true,
                ]);
            }

            StudentPhone::where('phone_number', $lead->phone)->delete();
            StudentPhone::create([
                'student_id'   => $student->student_id,
                'phone_number' => $lead->phone,
                'is_primary'   => true,
            ]);

            $patchData    = $this->handlePatchSelection($data);
            $currentPatch = Patch::where('status', 'Active')->first();

            $availabilities = collect();
            if (!empty($data['day']) && !empty($data['time_slot_id'])) {
                $availabilities = TeacherAvailability::where('day_of_week', $data['day'])
                    ->where('time_slot_id', $data['time_slot_id'])
                    ->get();
            }

            // ── Level-package continuation ─────────────────────────────
            // If this student is already on a level package with prepaid units
            // remaining and is now registering their next GROUP level, that
            // level is already paid for. Force the price to zero and remember
            // the source enrolment so createEnrollment can carry the package
            // over and decrement the units. (A brand-new package purchase comes
            // through data['package_id'] instead and is handled separately.)
            $data['_pkg_continue_from'] = null;
            if (empty($data['package_id']) && strtolower($data['type'] ?? '') === 'group') {
                $priorPkg = Enrollment::with('levelPackage')
                    ->where('student_id', $student->student_id)
                    ->whereNotNull('package_id')
                    ->where('package_units_remaining', '>', 0)
                    ->orderByDesc('enrollment_id')
                    ->first();

                if ($priorPkg) {
                    $data['final_price'] = 0;
                    $data['_pkg_continue_from'] = $priorPkg->enrollment_id;
                }
            }

            $pricing        = app(\App\Services\PricingService::class)->calculate($data);
            $formFinalPrice = (float) ($data['final_price'] ?? 0);
            if ($formFinalPrice > 0) {
                $pricing['final_price'] = $formFinalPrice;
            }
            // A package continuation is explicitly free — don't let pricing
            // put a course price back on it.
            if (!empty($data['_pkg_continue_from'])) {
                $pricing['final_price'] = 0;
            }
            $data['final_price'] = $pricing['final_price'];

            $enrollment = $this->createEnrollment($student, $data, $patchData);
            $this->attachMaterials($enrollment, $data);
            $this->createFinancialRecords($enrollment, $data, $pricing, $currentPatch);

            $testScore = $data['test_score'] ?? null;
            $testFee   = floatval($data['test_fee'] ?? 0);

            if ($testScore !== null && $testScore !== '' && $testFee > 0) {
                $test = \App\Models\Enrollment\PlacementTest::create([
                    'student_id'       => $student->student_id,
                    'score'            => $testScore,
                    'test_fee'         => $testFee,
                    'fee_paid'         => true,
                    'created_by_cs_id' => auth()->user()?->employee?->employee_id,
                ]);

                $enrollment->update(['placement_test_id' => $test->test_id]);
            }

            $plan             = PaymentPlan::find($data['payment_plan_id']);
            $requiresApproval = $plan && $plan->requires_admin_approval;

            if ($requiresApproval) {
                \App\Models\Finance\InstallmentApprovalLog::create([
                    'enrollment_id'    => $enrollment->enrollment_id,
                    'payment_plan_id'  => $data['payment_plan_id'],
                    'request_by_cs_id' => auth()->user()?->employee?->employee_id,
                    'status'           => 'Pending',
                ]);

                // ── Collect notifications instead of sending immediately ──
                $admins = \App\Models\Auth\User::whereHas('role', fn($q) =>
                    $q->where('role_name', 'Admin')
                )->get();

                $csName      = auth()->user()->name ?? 'CS';
                $studentName = $lead->full_name ?? 'a student';

                foreach ($admins as $admin) {
                    $adminEmployee = Employee::where('user_id', $admin->id)->first();
                    if ($adminEmployee) {
                        $courseName = \App\Models\Academic\CourseTemplate::find($data['course_template_id'])?->name ?? 'a course';
                        $planName   = $plan->name ?? 'installment plan';
                        $deposit    = (float) collect($data['deposit_methods'] ?? [])->sum(fn($m) => (float) ($m['amount'] ?? 0));
                        $remaining  = max(0, ($data['final_price'] ?? 0) - $deposit);

                        $metadata = [
                            'cs_name'           => $csName,
                            'student_name'      => $studentName,
                            'course_name'       => $courseName,
                            'plan_name'         => $planName,
                            'total_price'       => number_format((float) ($data['final_price'] ?? 0), 0),
                            'deposit_amount'    => number_format($deposit, 0),
                            'remaining_amount'  => number_format($remaining, 0),
                            'installment_count' => (int) ($plan->installment_count ?? 0),
                            'enrollment_id'     => $enrollment->enrollment_id,
                            'submitted_at'      => now()->format('H:i · d M'),
                        ];

                        $richMessage = "CS {$csName} requested \"{$planName}\" for {$studentName} in \"{$courseName}\".\n"
                                    . "Total: {$metadata['total_price']} LE · Deposit: {$metadata['deposit_amount']} LE · "
                                    . "{$metadata['installment_count']} installments";

                        $pendingNotifications[] = [
                            'employee_id' => (int) $adminEmployee->employee_id,
                            'title'       => '💰 New Installment Request',
                            'message'     => $richMessage,
                            'entity_type' => 'installment_request',
                            'entity_id'   => $enrollment->enrollment_id,
                            'metadata'    => $metadata,
                            'priority'    => 'high',
                        ];
                    }
                }
            }

            $preferredTypeMap = [
                'current' => 'Current_Patch',
                'next'    => 'Next_Patch',
                'custom'  => 'Specific_Date',
            ];

            $preferredType    = $preferredTypeMap[$data['patch_option']] ?? null;
            $requestedPatchId = null;
 
            if ($data['patch_option'] !== 'custom') {
                $requestedPatchId = $patchData['patch_id'] ?? $data['patch_id'] ?? null;
            }
 
            if (!$requiresApproval) {
                $waiting = WaitingList::create([
                    'enrollment_id'           => $enrollment->enrollment_id,
                    'requested_patch_id'      => $requestedPatchId,
                    'preferred_type'          => $preferredType,
                    'preferred_delivery_type' => $enrollment->enrollment_type,
                    'preferred_delivery_mood' => $enrollment->delivery_mood,
                    'preferred_start_date'    => $preferredType === 'Specific_Date'
                        ? ($patchData['date'] ?? $data['custom_date'] ?? null)
                        : null,
                    // Private students pick a day-pair (e.g. sat_tue); keep it so
                    // Student Care sees which days they asked for. Group entries
                    // have no day preference.
                    'preferred_days'   => ($enrollment->enrollment_type === 'Private')
                        ? ($data['day'] ?? null)
                        : null,
                    'status'           => 'Active',
                    'notes'            => $data['notes'] ?? null,
                    'created_by_cs_id' => auth()->user()?->employee?->employee_id,
                ]);
                event(new WaitingListUpdated($waiting));

                $lead->update([
                    'status'     => 'Registered',
                    'student_id' => $student->student_id,
                ]);
            } else {
                $lead->update([
                    'student_id' => $student->student_id,
                ]);

                $waitingMeta = json_encode([
                    'requested_patch_id'      => $requestedPatchId,
                    'preferred_type'          => $preferredType,
                    'preferred_delivery_type' => $enrollment->enrollment_type,
                    'preferred_delivery_mood' => $enrollment->delivery_mood,
                    'preferred_start_date'    => $preferredType === 'Specific_Date'
                        ? ($patchData['date'] ?? $data['custom_date'] ?? null)
                        : null,
                    'notes'                   => $data['notes'] ?? null,
                ]);

                \App\Models\Finance\InstallmentApprovalLog::where('enrollment_id', $enrollment->enrollment_id)
                    ->where('status', 'Pending')
                    ->latest()
                    ->update(['waiting_list_meta' => $waitingMeta]);
            }
 
            return $enrollment;
        });


            foreach ($pendingNotifications as $notif) {
                try {
                    \App\Services\NotificationService::send(
                        $notif['employee_id'],
                        $notif['title'],
                        $notif['message'],
                        $notif['entity_type'],
                        $notif['entity_id'],
                        $notif['metadata'] ?? [],
                        $notif['priority'] ?? 'normal'
                    );
                } catch (\Throwable $e) {
                \Log::warning('Post-commit notification failed: ' . $e->getMessage(), [
                    'notification' => $notif,
                ]);
            }
        }

        try {
            $waiting = WaitingList::where('enrollment_id', $enrollment->enrollment_id)
                ->latest()->first();
            if ($waiting) {
                event(new WaitingListUpdated($waiting));
            }
        } catch (\Throwable $e) {
            \Log::warning('Post-commit waiting-list event failed: ' . $e->getMessage());
        }

        return $enrollment;
    }

    private function handlePatchSelection($data)
    {
        if ($data['patch_option'] === 'current') {
            return [
                'patch_id' => $data['patch_id'],
                'type'     => 'direct'
            ];
        }

        if ($data['patch_option'] === 'next') {
            $nextPatch = \App\Models\Academic\Patch::where('status', 'Upcoming')
                ->orderBy('start_date')
                ->first();

            return [
                'patch_id' => $nextPatch?->patch_id,   
                'type'     => 'waiting',
                'date'     => $nextPatch?->start_date ?? now()->addWeeks(2), 
            ];
        }

        if ($data['patch_option'] === 'custom') {
            if (empty($data['custom_date'])) {
                throw new \App\Exceptions\BusinessValidationException('Please select a start date.');
            }

            return [
                'patch_id' => null,
                'type'     => 'waiting',
                'date'     => $data['custom_date']
            ];
        }

        throw new \App\Exceptions\BusinessValidationException('Invalid start option selected.');
    }

    /*
    |------------------------------------------------------------------
    | Create Enrollment
    |------------------------------------------------------------------
    */
    private function createEnrollment($student, $data, $patchData)
    {
        
        $status = $this->determineStatus($data, $patchData);
        $csEmployee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')->first();
        $branchId = $csEmployee?->branch_id
            ?? $currentPatch?->branch_id
            ?? \App\Models\Core\Branch::first()?->branch_id;

        // ── Private hours: seed hours_remaining ────────────────────────
        // Private = free pool of hours. A private enrolment starts with:
        //   (carried-over leftover hours from finished courses) + (new bundle,
        //    if one was chosen — the bundle is optional when leftover exists).
        // Group enrollments don't use hours, so this stays null for them.
        $hoursRemaining = null;
        if (strtolower($data['type']) === 'private') {

            // Carry over any leftover hours from this student's completed
            // private courses, then zero those out so they aren't double-counted.
            $carried = (float) Enrollment::where('student_id', $student->student_id)
                ->where('enrollment_type', 'Private')
                ->where('status', 'Completed')
                ->where('hours_remaining', '>', 0)
                ->sum('hours_remaining');

            if ($carried > 0) {
                Enrollment::where('student_id', $student->student_id)
                    ->where('enrollment_type', 'Private')
                    ->where('status', 'Completed')
                    ->where('hours_remaining', '>', 0)
                    ->update(['hours_remaining' => 0]);
            }

            // Add the new bundle's hours if one was selected (optional).
            $bundleHours = 0;
            if (!empty($data['bundle_id'])) {
                $bundle = PrivateBundle::find($data['bundle_id']);
                $bundleHours = $bundle ? (float) $bundle->hours : 0;
            }

            $hoursRemaining = $carried + $bundleHours;
        }

        // ── Level package: record the package + how many prepaid units remain.
        // A package covers `levels_count` units (sublevels when the level has
        // sublevels, otherwise levels). This first enrolment consumes one unit,
        // so the rest are prepaid and become free in later enrolments.
        $packageId    = null;
        $packageUnits = null;
        if (!empty($data['package_id'])) {
            // Brand-new package purchase — first level of the package.
            $package = \App\Models\Finance\LevelPackage::find($data['package_id']);
            if ($package) {
                $packageId    = $package->package_id;
                $packageUnits = max(0, (int) $package->levels_count - 1);
            }
        } elseif (!empty($data['_pkg_continue_from'])) {
            // Continuation — the student already owns this package and is taking
            // their next prepaid level. Carry the same package onto the new
            // enrolment with one fewer unit, and zero out the source enrolment's
            // remaining units so it drops off the "ready to continue" list.
            $source = Enrollment::find($data['_pkg_continue_from']);
            if ($source && $source->package_id) {
                $packageId    = $source->package_id;
                $packageUnits = max(0, (int) $source->package_units_remaining - 1);

                $source->package_units_remaining = 0;
                $source->save();
            }
        }

        return Enrollment::create([

            'student_id' => $student->student_id,

            'course_template_id' => $data['course_template_id'],
            'course_instance_id' => $data['course_instance_id'] ?? null,

            'level_id' => $data['level_id'] ?? null,        
            'sublevel_id' => $data['sublevel_id'] ?? null,  

            'patch_id' => $data['patch_id'] ?? null,
            'branch_id'          => $branchId, 
            'teacher_id' => $data['teacher_id'] ?? null,

            'enrollment_type' => ucfirst($data['type']),
            'delivery_mood' => ucfirst($data['mode']),

            'final_price' => $data['final_price'],
            'payment_plan_id' => $data['payment_plan_id'],

            'bundle_id' => $data['bundle_id'] ?? null,
            'package_id' => $packageId,
            'package_units_remaining' => $packageUnits,
            'hours_remaining' => $hoursRemaining,
            'discount_value' => $data['discount_value'] ?? 0,

            'status' => $this->determineStatus($data, $patchData),

            'created_by_cs_id' => auth()->user()->employee?->employee_id ?? null
        ]);
    }

    private function validateBusiness($data)
    {
        $lead = \App\Models\Leads\Lead::find($data['lead_id']);

        // if ($lead->status === 'Registered') {
        //     throw new \Exception('Lead already registered');
        // }

        if ($data['type'] === 'group') {

            if ($data['patch_option'] === 'current' && empty($data['patch_id'])) {
                throw new \App\Exceptions\BusinessValidationException('Invalid patch selection. Please refresh and choose a start option.');
            }

            if ($data['patch_option'] === 'custom' && empty($data['custom_date'])) {
                throw new \App\Exceptions\BusinessValidationException('A custom start date is required.');
            }
        }


        if (!empty($data['custom_date'])) {
            if ($data['custom_date'] <= now()->toDateString()) {
                throw new \App\Exceptions\BusinessValidationException('The start date must be a future date (tomorrow or later).');
            }
        }
    }

    private function validatePatchOptionAvailability($data)
    {
        $patchOption = $data['patch_option'] ?? null;

        if ($patchOption !== 'current') {
            return;
        }

        $type = ucfirst(strtolower($data['type'] ?? ''));
        $mode = $data['mode'] ?? $data['delivery_mood'] ?? null;

        $options = app(\App\Services\PatchService::class)->getOptions([
            'course_template_id' => $data['course_template_id'] ?? null,
            'type'               => $type,
            'delivery_mood'      => $mode,
            'level_id'           => $data['level_id']    ?? null,
            'sublevel_id'        => $data['sublevel_id'] ?? null,
        ]);

        $currentOption = collect($options)->firstWhere('type', 'current');

        if (!$currentOption) {
            throw new \Exception(
                'The Current Patch option is no longer available for this course/level/mode. '
                . 'Please refresh and choose Next Patch or a specific date.'
            );
        }

        if (($currentOption['case'] ?? null) === 'A') {
            $expectedInstanceId = $currentOption['course_instance_id'] ?? null;

            $submittedInstanceId = $data['course_instance_id'] ?? null;

            if ($expectedInstanceId && $submittedInstanceId
                && (int) $submittedInstanceId !== (int) $expectedInstanceId) {
                throw new \Exception(
                    'The selected course group has changed. Please refresh the form and try again.'
                );
            }
        }

        if (($currentOption['case'] ?? null) === 'B') {
            $type = ucfirst(strtolower($data['type'] ?? ''));

            if ($type === 'Private') {
                if (empty($data['teacher_id'])) {
                    throw new \App\Exceptions\BusinessValidationException('Please select a teacher for this private registration.');
                }

                $availableTeachers = app(\App\Services\TeacherAvailabilityService::class)
                    ->getAvailableTeachers([
                        'patch_option'       => 'current',
                        'patch_id'           => $data['patch_id'] ?? null,
                        'course_template_id' => $data['course_template_id'] ?? null,
                        'level_id'           => $data['level_id']    ?? null,
                        'sublevel_id'        => $data['sublevel_id'] ?? null,
                        'delivery_mood'      => $data['mode'] ?? null,
                    ]);

                $availableIds = collect($availableTeachers)->pluck('teacher_id')->map(fn($id) => (int) $id);

                if (!$availableIds->contains((int) $data['teacher_id'])) {
                    throw new \App\Exceptions\BusinessValidationException(
                        'The selected teacher is no longer available for this patch. Please refresh and pick another.'
                    );
                }
            } else {
                $availableTeachers = app(\App\Services\TeacherAvailabilityService::class)
                    ->getAvailableTeachers([
                        'patch_option'       => 'current',
                        'patch_id'           => $data['patch_id'] ?? null,
                        'course_template_id' => $data['course_template_id'] ?? null,
                        'level_id'           => $data['level_id']    ?? null,
                        'sublevel_id'        => $data['sublevel_id'] ?? null,
                        'delivery_mood'      => $data['mode'] ?? null,
                    ]);

                if (empty($availableTeachers)) {
                    throw new \App\Exceptions\BusinessValidationException(
                        'No teacher is available to start a new group in the current patch. Please choose Next Patch or a specific date.'
                    );
                }
            }
        }
    }

    private function validateTeacherAvailability($data)
    {
        if (empty($data['teacher_id'])) return;

        $exists = \App\Models\HR\TeacherAvailability::where('teacher_id', $data['teacher_id'])
            ->where('day_of_week', $data['day'] ?? null)
            ->exists();

        if (!$exists) {
            throw new \App\Exceptions\BusinessValidationException('The selected teacher is not available on this day.');
        }
    }

    private function validateNoConflict($data)
    {
        if (empty($data['teacher_id'])) return;
        if (($data['patch_option'] ?? '') !== 'current') return;
        if (empty($data['patch_id'])) return;

        // A private pair (e.g. sat_tue) recurs weekly across the patch, and a
        // teacher can hold many instances in the same pair — so there is no
        // per-pair clash. The only real limit is the teacher's contract cap on
        // TOTAL sessions in the patch. If they've already reached that cap,
        // block it; otherwise it's fine. (Same rule the teacher dropdown uses
        // to decide who is selectable, so the two stay consistent.)
        $teacherId = (int) $data['teacher_id'];
        $patchId   = (int) $data['patch_id'];

        $contract = \App\Models\HR\TeacherContract::with('contractType')
            ->where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->where('is_active', true)
            ->first();

        $maxAllowed = (int) ($contract?->contractType?->max_sessions_allowed ?? 0);
        if ($maxAllowed <= 0) return; // no readable cap → nothing to enforce here

        $existing = \App\Models\Academic\CourseInstance::where('teacher_id', $teacherId)
            ->where('patch_id', $patchId)
            ->whereIn('status', ['Active', 'Upcoming', 'Completed'])
            ->get();

        $used = 0;
        foreach ($existing as $inst) {
            if ($inst->session_duration > 0) {
                $used += (int) ceil((float) $inst->total_hours / (float) $inst->session_duration);
            }
        }

        if ($used >= $maxAllowed) {
            throw new \App\Exceptions\BusinessValidationException(
                'The selected teacher has reached their maximum number of sessions for this patch. Please pick another teacher.'
            );
        }
    }

    private function validatePatch($data)
    {
        if ($data['patch_option'] !== 'custom') return;

        $lastPatch = \App\Models\Academic\Patch::whereIn('status', ['Active', 'Upcoming', 'Completed'])
            ->orderByDesc('end_date')
            ->first();

        if ($lastPatch && $data['custom_date'] <= $lastPatch->end_date) {
            $endFormatted = \Carbon\Carbon::parse($lastPatch->end_date)->format('d M Y');
            throw new \App\Exceptions\BusinessValidationException(
                "The custom start date must be after {$endFormatted} (the end of the last scheduled patch)."
            );
        }
    }

    private function validatePricing($data)
    {
        $discount = (float) ($data['discount_value'] ?? 0);

        if ($discount < 0) {
            throw new \App\Exceptions\BusinessValidationException('Discount value cannot be negative.');
        }
        $finalPrice = (float) ($data['final_price'] ?? 0);
        if ($discount > 0 && $finalPrice <= 0) {
            throw new \App\Exceptions\BusinessValidationException('Invalid pricing: discount applied but final price is zero or negative.');
        }
    }

    private function storeTest($data)
    {
        return \App\Models\Enrollment\PlacementTest::create([
            'score' => $data['test_score'],
            'fee' => $data['test_fee'] ?? 0
        ])->test_id;
    }

    private function determineStatus($data, $patchData)
    {
        if (!empty($data['payment_plan_id'])) {
            $plan = PaymentPlan::find($data['payment_plan_id']);
            if ($plan && $plan->requires_admin_approval) {
                return 'Pending_Approval';
            }
        }

        return $patchData['type'] === 'direct' ? 'Active' : 'Waiting';
    }

    private function attachMaterials($enrollment, $data)
    {
        // Selected material ids come from the form as material_ids[] (mandatory
        // ones are always included). Fall back gracefully if none provided.
        $selectedIds = collect($data['material_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($selectedIds->isEmpty()) {
            return;
        }

        // Load the actual materials (price + revenue_type) from the DB — never
        // trust prices from the request.
        $materials = \App\Models\Enrollment\Material::whereIn('material_id', $selectedIds)
            ->where('is_active', true)
            ->get();

        foreach ($materials as $material) {
            \App\Models\Enrollment\EnrollmentMaterial::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'material_id'   => $material->material_id,
                'price'         => $material->price ?? 0,
                'status'        => 'Pending',
            ]);
        }
    }

private function createFinancialRecords($enrollment, $data, $pricing, $patch)
    {
        $csEmployee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();

        $branchId = $csEmployee?->branch_id ?? $patch?->branch_id;

        if (!$branchId) {
            throw new \Exception(
                'Branch resolution failed: the CS employee has no branch and no patch branch is available. '
                . 'Please assign a branch to the CS account.'
            );
        }

        $patchId = $patch?->patch_id;

        // ─── Compute each component's correct amount ───
        $courseFinal   = (float) $pricing['final_price'];
        $depositPct    = $this->getDepositPct($data);
        $courseDeposit = round($courseFinal * $depositPct / 100, 2);
        $testFee       = floatval($data['test_fee'] ?? 0);

        // ─── Materials: load the selected ones with their real prices + types ───
        // Material component total = sum of all selected material prices.
        // Revenue for each material is split by ITS OWN revenue_type:
        //   Individual → Direct to the registering CS
        //   Shared     → split equally among all Active CS in the branch
        $selectedMaterialIds = collect($data['material_ids'] ?? [])
            ->map(fn($id) => (int) $id)->filter()->unique()->values();

        $selectedMaterials = $selectedMaterialIds->isNotEmpty()
            ? \App\Models\Enrollment\Material::whereIn('material_id', $selectedMaterialIds)
                ->where('is_active', true)->get()
            : collect();

        $materialPrice = (float) $selectedMaterials->sum('price');

        $methodMap = [
            'Instapay'      => 'Transfer',
            'Vodafone_Cash' => 'Online',
            'Cash'          => 'Cash',
            'Card'          => 'Card',
            'Transfer'      => 'Transfer',
            'Online'        => 'Online',
        ];

        // Active CS in the branch (for Shared material revenue)
        $activeBranchCsIds = \App\Models\HR\Employee::whereHas('user.role',
                fn($q) => $q->where('role_name', 'Customer Service'))
            ->where('status', 'Active')
            ->where('branch_id', $branchId)
            ->pluck('employee_id')
            ->all();
        if (empty($activeBranchCsIds)) {
            $activeBranchCsIds = [$csEmployee->employee_id]; // fallback: at least the registrar
        }

        // Split totals per material type across ALL selected materials
        $individualMaterialTotal = (float) $selectedMaterials->where('revenue_type', 'Individual')->sum('price');
        $sharedMaterialTotal     = (float) $selectedMaterials->where('revenue_type', 'Shared')->sum('price');

        // Components in FILL ORDER (Course first, then Test, then Material)
        $components = [
            ['category' => 'Course',   'remaining' => $courseDeposit],
            ['category' => 'Test',     'remaining' => $testFee],
            ['category' => 'Material', 'remaining' => $materialPrice],
        ];

        // Payment methods the CS entered (queue to consume in order)
        $methodQueue = collect($data['deposit_methods'] ?? [])
            ->map(fn($m) => [
                'method' => $m['method'] ?? 'Cash',
                'amount' => (float) ($m['amount'] ?? 0),
            ])
            ->filter(fn($m) => $m['amount'] > 0)
            ->values()
            ->toArray();

        // ─── Helper: create a Payment ft + revenue split(s) ───
        // Course/Test → Direct to the registering CS.
        // Material    → split within this chunk proportionally between the
        //   Individual portion (Direct) and the Shared portion (divided equally
        //   among all Active branch CS), based on the overall material mix.
        $createPaymentTx = function ($category, $amount, $rawMethod) use (
            $enrollment, $patchId, $branchId, $csEmployee, $methodMap,
            $materialPrice, $individualMaterialTotal, $sharedMaterialTotal, $activeBranchCsIds
        ) {
            if ($amount <= 0.001) return;

            $tx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $patchId,
                'branch_id'              => $branchId,
                'transaction_type'       => 'Payment',
                'transaction_category'   => $category,
                'amount'                 => round($amount, 2),
                'payment_method'         => $methodMap[$rawMethod] ?? 'Cash',
                'created_by_employee_id' => $csEmployee->employee_id,
            ]);

            if ($category !== 'Material' || $materialPrice <= 0.001) {
                // Course / Test → single Direct split to the registrar.
                RevenueSplit::create([
                    'transaction_id'   => $tx->transaction_id,
                    'employee_id'      => $csEmployee->employee_id,
                    'branch_id'        => $branchId,
                    'patch_id'         => $patchId,
                    'amount_allocated' => round($amount, 2),
                    'allocation_type'  => 'Direct',
                ]);
                return;
            }

            // ── Material chunk → split by type ──
            // What fraction of the whole material total is Individual vs Shared,
            // applied to this chunk's amount.
            $indivShare  = $materialPrice > 0 ? ($individualMaterialTotal / $materialPrice) : 0;
            $indivAmount = round($amount * $indivShare, 2);
            $sharedAmount = round($amount - $indivAmount, 2);

            // Individual portion → Direct to the registrar
            if ($indivAmount > 0.001) {
                RevenueSplit::create([
                    'transaction_id'   => $tx->transaction_id,
                    'employee_id'      => $csEmployee->employee_id,
                    'branch_id'        => $branchId,
                    'patch_id'         => $patchId,
                    'amount_allocated' => $indivAmount,
                    'allocation_type'  => 'Direct',
                ]);
            }

            // Shared portion → divided equally among all Active branch CS
            if ($sharedAmount > 0.001 && !empty($activeBranchCsIds)) {
                $n     = count($activeBranchCsIds);
                $each  = round($sharedAmount / $n, 2);
                $acc   = 0;
                foreach ($activeBranchCsIds as $i => $empId) {
                    // Give the rounding remainder to the last person so the
                    // splits sum exactly to the shared amount.
                    $alloc = ($i === $n - 1) ? round($sharedAmount - $acc, 2) : $each;
                    $acc  += $alloc;
                    RevenueSplit::create([
                        'transaction_id'   => $tx->transaction_id,
                        'employee_id'      => $empId,
                        'branch_id'        => $branchId,
                        'patch_id'         => $patchId,
                        'amount_allocated' => $alloc,
                        'allocation_type'  => 'Shared',
                    ]);
                }
            }
        };

        // ─── Fill each component from the method queue, in order ───
        // Each method is consumed across one or more components. Categories stay
        // correct (for balance) AND payment_method reflects the real split.
        $mIdx = 0;
        foreach ($components as &$comp) {
            while ($comp['remaining'] > 0.001 && $mIdx < count($methodQueue)) {
                $avail = $methodQueue[$mIdx]['amount'];

                if ($avail <= 0.001) { $mIdx++; continue; }

                $take = min($comp['remaining'], $avail);

                $createPaymentTx($comp['category'], $take, $methodQueue[$mIdx]['method']);

                $comp['remaining']            -= $take;
                $methodQueue[$mIdx]['amount'] -= $take;

                if ($methodQueue[$mIdx]['amount'] <= 0.001) $mIdx++;
            }
        }
        unset($comp);

        // ─── Record the raw payment-method split for audit (once) ───
        foreach (($data['deposit_methods'] ?? []) as $m) {
            $amt = (float) ($m['amount'] ?? 0);
            if ($amt <= 0) continue;
            \Illuminate\Support\Facades\DB::table('deposit_payment')->insert([
                'enrollment_id'    => $enrollment->enrollment_id,
                'method'           => $m['method'] ?? 'Cash',
                'amount'           => $amt,
                'reference_number' => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // ═══════════════════════════════════════════════════════════
        // INSTALLMENT SCHEDULE (no ft until actually paid)
        // ═══════════════════════════════════════════════════════════
        $plan = PaymentPlan::find($data['payment_plan_id']);

        // Clean up any existing schedules first (defensive)
        $existingSchedules = \App\Models\Finance\InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)->get();
        foreach ($existingSchedules as $sched) {
            FinancialTransaction::where('transaction_id', $sched->transaction_id)
                ->where('transaction_type', 'Installment')
                ->delete();
        }
        \App\Models\Finance\InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)->delete();

        if (!$plan || $plan->installment_count <= 0) {
            return;
        }

        if ($plan->requires_admin_approval) {
            return;
        }

        $remaining  = $courseFinal - $courseDeposit;
        $instAmount = round($remaining / $plan->installment_count, 2);

        // Each installment schedule links to its own Installment transaction,
        // created up-front (schema requires transaction_id). The schedule stays
        // Pending, so BalanceCalculator/Outstanding do NOT count it as paid
        // until its schedule is marked Paid at payment time.
        for ($i = 1; $i <= $plan->installment_count; $i++) {
            // Spread any rounding remainder onto the last installment so the
            // installments sum exactly to the remaining balance.
            $thisAmount = ($i === $plan->installment_count)
                ? round($remaining - ($instAmount * ($plan->installment_count - 1)), 2)
                : $instAmount;

            $instTx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $patchId,
                'branch_id'              => $branchId,
                'transaction_type'       => 'Installment',
                'transaction_category'   => 'Course',
                'amount'                 => $thisAmount,
                'payment_method'         => 'Cash',
                'created_by_employee_id' => $csEmployee->employee_id,
            ]);

            InstallmentSchedule::create([
                'enrollment_id'      => $enrollment->enrollment_id,
                'transaction_id'     => $instTx->transaction_id,
                'installment_number' => $i,
                'due_date'           => null,
                'amount'             => $thisAmount,
                'status'             => 'Pending',
            ]);
        }
    }

    private function getDepositPct($data): float
    {
        $plan = \App\Models\Finance\PaymentPlan::find($data['payment_plan_id']);
        return $plan ? (float) $plan->deposit_percentage : 100.0;
    }
}