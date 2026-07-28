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

class RegistrationService
{
    public function register($data)
    {
        $pendingNotifications = [];

        $enrollment = DB::transaction(function () use ($data, &$pendingNotifications) {

            $this->validateBusiness($data);
            $this->validateTeacherAvailability($data);
            $this->validateNoConflict($data);
            $this->validatePatch($data);
            $this->validatePricing($data);

            $lead = Lead::findOrFail($data['lead_id']);

            $student = Student::create([
                'full_name'     => $lead->full_name,
                'email'         => $lead->email,
                'birthdate'     => $lead->birthdate,
                'degree'        => $lead->degree,
                'location'      => $lead->location,
                'global_status' => 'Active',
                'is_active'     => true,
            ]);

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

            $pricing        = app(\App\Services\PricingService::class)->calculate($data);
            $formFinalPrice = (float) ($data['final_price'] ?? 0);
            if ($formFinalPrice > 0) {
                $pricing['final_price'] = $formFinalPrice;
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
                'type' => 'direct'
            ];
        }

        if ($data['patch_option'] === 'next') {
            return [
                'patch_id' => null,
                'type' => 'waiting',
                'date' => now()->addWeeks(2)
            ];
        }

        if ($data['patch_option'] === 'custom') {

            if (empty($data['custom_date'])) {
                throw new \Exception('Please select a date');
            }

            return [
                'patch_id' => null,
                'type' => 'waiting',
                'date' => $data['custom_date']
            ];
        }

        throw new \Exception('Invalid patch option');
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
                throw new \Exception('Invalid patch selection');
            }

            if ($data['patch_option'] === 'custom' && empty($data['custom_date'])) {
                throw new \Exception('Custom date required');
            }
        }


        if (!empty($data['custom_date'])) {

            if ($data['custom_date'] < now()->toDateString()) {
                throw new \Exception('Date must be in future');
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
            throw new \Exception('Teacher not available on this day');
        }
    }

    private function validateNoConflict($data)
    {
        if (empty($data['teacher_id'])) return;

        $conflict = \App\Models\Academic\CourseInstance::where('teacher_id', $data['teacher_id'])
            ->where('patch_id', $data['patch_id'])
            ->whereHas('instanceSchedules', function ($q) use ($data) { // ← instanceSchedules
                $q->where('day_of_week', $data['day'] ?? null)
                ->where('time_slot_id', $data['time_slot_id'] ?? null);
            })
            ->exists();

        if ($conflict) {
            throw new \Exception('Teacher already booked');
        }
    }

    private function validatePatch($data)
    {
        if ($data['patch_option'] !== 'custom') return;

        $lastPatch = \App\Models\Academic\Patch::orderByDesc('end_date')->first();

        if ($lastPatch && $data['custom_date'] <= $lastPatch->end_date) {
            throw new \Exception('Date must be after current patch');
        }
    }

    private function validatePricing($data)
    {
        if (!empty($data['discount_value']) && $data['discount_value'] < 0) {
            throw new \Exception('Invalid discount');
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

        $materialAssignment = null;
    
        if (!empty($data['sublevel_id'])) {
            $materialAssignment = \App\Models\Enrollment\MaterialAssignment::where('sublevel_id', $data['sublevel_id'])
                ->with('material')
                ->first();
        }
    
        if (!$materialAssignment && !empty($data['level_id'])) {
            $materialAssignment = \App\Models\Enrollment\MaterialAssignment::where('level_id', $data['level_id'])
                ->whereNull('sublevel_id')
                ->with('material')
                ->first();
        }
    
        if (!$materialAssignment && !empty($data['course_template_id'])) {
            $materialAssignment = \App\Models\Enrollment\MaterialAssignment::where('course_template_id', $data['course_template_id'])
                ->whereNull('level_id')
                ->whereNull('sublevel_id')
                ->with('material')
                ->first();
        }
    
        if (!$materialAssignment || !$materialAssignment->material) {
            return; 
        }
    
        $materialPrice = floatval($data['material_price'] ?? 0);
        if ($materialPrice <= 0) {
            return; 
        }
    
        \App\Models\Enrollment\EnrollmentMaterial::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'material_id'   => $materialAssignment->material_id,
            'price'         => $materialAssignment->material->price ?? 0,
            'status'        => 'Pending',
        ]);
    }

    private function createFinancialRecords($enrollment, $data, $pricing, $patch)
    {
        $csEmployee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();

        $branchId = $csEmployee?->branch_id 
            ?? $patch?->branch_id 
            ?? \App\Models\Core\Branch::first()?->branch_id;

        if (!$branchId) {
            throw new \Exception('Branch not found for this employee');
        }
        $patchId    = $patch?->patch_id;

$depositAmount = ($pricing['final_price'] * $this->getDepositPct($data)) / 100;


        $methodMap = [
            'Instapay'      => 'Transfer',
            'Vodafone_Cash' => 'Online',
            'Cash'          => 'Cash',
            'Card'          => 'Card',
            'Transfer'      => 'Transfer',
            'Online'        => 'Online',
        ];

        $methods = $data['deposit_methods'] ?? [];

        foreach ($methods as $method) {
            $amt = (float) ($method['amount'] ?? 0);
            if ($amt <= 0) continue;

            $mappedMethod = $methodMap[$method['method']] ?? 'Cash';

            $tx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $patchId,
                'branch_id'              => $branchId,
                'transaction_type'       => 'Payment',
                'transaction_category'   => 'Course',
                'amount'                 => $amt,
                'payment_method'         => $mappedMethod,
                'created_by_employee_id' => $csEmployee->employee_id,
            ]);

            RevenueSplit::create([
                'transaction_id'   => $tx->transaction_id,
                'employee_id'      => $csEmployee->employee_id,
                'branch_id'        => $branchId,
                'patch_id'         => $patchId,
                'amount_allocated' => $amt,
                'allocation_type'  => 'Direct',
            ]);

            \Illuminate\Support\Facades\DB::table('deposit_payment')->insert([
                'enrollment_id'    => $enrollment->enrollment_id,
                'method'           => $method['method'],
                'amount'           => $amt,
                'reference_number' => $method['reference'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $testFee = floatval($data['test_fee'] ?? 0);
        if ($testFee > 0) {
            $primaryMethod = collect($methods)
                ->filter(fn($m) => (float) ($m['amount'] ?? 0) > 0)
                ->sortByDesc(fn($m) => (float) ($m['amount'] ?? 0))
                ->first();

            $testMethodRaw = $primaryMethod['method'] ?? 'Cash';
            $testMethod    = $methodMap[$testMethodRaw] ?? 'Cash';

            $testTx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $patchId,
                'branch_id'              => $branchId,
                'transaction_type'       => 'Payment',
                'transaction_category'   => 'Test',
                'amount'                 => $testFee,
                'payment_method'         => $testMethod,
                'created_by_employee_id' => $csEmployee->employee_id,
            ]);

            RevenueSplit::create([
                'transaction_id'   => $testTx->transaction_id,
                'employee_id'      => $csEmployee->employee_id,
                'branch_id'        => $branchId,
                'patch_id'         => $patchId,
                'amount_allocated' => $testFee,
                'allocation_type'  => 'Direct',
            ]);
        }

        $materialPrice = floatval($data['material_price'] ?? 0);
        if ($materialPrice > 0) {

            $materialTx = FinancialTransaction::create([
                'enrollment_id'         => $enrollment->enrollment_id,
                'patch_id'              => $patchId,
                'branch_id'             => $branchId,
                'transaction_type'      => 'Payment',
                'transaction_category'  => 'Material',
                'amount'                => $materialPrice,
                'payment_method'        => 'Cash',
                'created_by_employee_id'=> $csEmployee->employee_id,
            ]);

            $enrollmentMaterial = \App\Models\Enrollment\EnrollmentMaterial::where('enrollment_id', $enrollment->enrollment_id)
                ->first();

            $material = $enrollmentMaterial
                ? \App\Models\Enrollment\Material::find($enrollmentMaterial->material_id)
                : null;

            $revenueType = $material?->revenue_type ?? 'Shared';

            if ($revenueType === 'Individual') {
                RevenueSplit::create([
                    'transaction_id'    => $materialTx->transaction_id,
                    'employee_id'       => $csEmployee->employee_id,
                    'branch_id'         => $branchId,
                    'patch_id'          => $patchId,
                    'amount_allocated'  => $materialPrice,
                    'allocation_type'   => 'Direct',
                ]);
            } else {
                $allCS = Employee::whereHas('user', fn($q) =>
                        $q->whereHas('role', fn($q2) =>
                            $q2->where('role_name', 'Customer Service')
                        )
                    )
                    ->where('branch_id', $branchId)
                    ->get();

                $share = $allCS->count() > 0
                    ? round($materialPrice / $allCS->count(), 2)
                    : $materialPrice;

                foreach ($allCS as $cs) {
                    RevenueSplit::create([
                        'transaction_id'    => $materialTx->transaction_id,
                        'employee_id'       => $cs->employee_id,
                        'branch_id'         => $branchId,
                        'patch_id'          => $patchId,
                        'amount_allocated'  => $share,
                        'allocation_type'   => 'Shared',
                    ]);
                }
            }
        }

        $plan = PaymentPlan::find($data['payment_plan_id']);

               
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

                $remaining  = $pricing['final_price'] - $depositAmount;
                $instAmount = round($remaining / $plan->installment_count, 2);

                for ($i = 1; $i <= $plan->installment_count; $i++) {

                    $instTx = FinancialTransaction::create([
                        'enrollment_id'          => $enrollment->enrollment_id,
                        'patch_id'               => $patchId,
                        'branch_id'              => $branchId,
                        'transaction_type'       => 'Installment',
                        'transaction_category'   => 'Course',
                        'amount'                 => $instAmount,
                        'payment_method'         => 'Cash',
                        'created_by_employee_id' => $csEmployee->employee_id,
                    ]);

                    \App\Models\Finance\InstallmentSchedule::create([
                        'enrollment_id'      => $enrollment->enrollment_id,
                        'transaction_id'     => $instTx->transaction_id,
                        'installment_number' => $i,
                        'due_date'           => null,
                        'amount'             => $instAmount,
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