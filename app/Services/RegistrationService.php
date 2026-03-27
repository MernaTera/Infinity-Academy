<?php

namespace App\Services;

use DB;
use Carbon\Carbon;

use App\Models\Student\Student;
use App\Models\Student\StudentPhone;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\PlacementTest;
use App\Models\Enrollment\WaitingList;

use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\PaymentPlan;

use App\Models\Leads\Lead;

use App\Services\PricingService;
use App\Services\GroupDetectionService;
use App\Services\TeacherAvailabilityService;
use App\Services\PaymentService;
use App\Services\RestrictionService;

use Illuminate\Support\Facades\Auth;

class RegistrationService
{
    /*
    |--------------------------------------------------------------------------
    | Main Registration Entry
    |--------------------------------------------------------------------------
    */
    

    public function register(array $data, $leadId = null)
    {
        return DB::transaction(function () use ($data, $leadId) {

            // 1. Create Student
            $student = $this->createStudent($data);

            // 2. Create Phones
            $this->createPhones($student, $data);

            // 3. Placement Test
            $placement = $this->createPlacementTest($student, $data);


            // pricing
            $data['final_price'] = app(PricingService::class)->calculatePrice($data);

            //group detection
            if ($data['type'] === 'group') {
                $decision = app(GroupDetectionService::class)->detect($data);
            } else {
                $slot = app(TeacherAvailabilityService::class)->findAvailableSlot($data);

                $decision = $slot
                    ? ['type' => 'direct', 'teacher' => $slot->teacher]
                    : ['type' => 'waiting'];
            }

            // Patch Decision
            $patchData = $this->handlePatchSelection($data);

            // 4. Create Enrollment
            $enrollment = $this->createEnrollment($student, $placement, $data, $decision, $patchData);

            // 5. Waiting List Logic
            if ($patchData['type'] === 'waiting') {
                $this->addToWaitingList($enrollment, $data, $patchData);
            }

            // 6. Payment (Deposit)
            app(\App\Services\PaymentService::class)->createPayment($enrollment);
            app(RestrictionService::class)->evaluateEnrollment($enrollment);
            // 7. Lead Conversion (if exists)
            if ($leadId) {
                $this->convertLead($leadId, $student);
            }

            return $enrollment;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Create Student
    |--------------------------------------------------------------------------
    */

    private function createStudent($data)
    {
        return Student::create([
            'full_name' => $data['full_name'],
            'birthdate' => $data['birthdate'] ?? null,
            'degree' => $data['degree'],
            'location' => $data['location'] ?? null,
            'email' => $data['email'] ?? null,
            'global_status' => 'Active',
            'is_active' => true
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Phones
    |--------------------------------------------------------------------------
    */

    private function createPhones($student, $data)
    {
        foreach ($data['phones'] as $index => $phone) {
            StudentPhone::create([
                'student_id' => $student->student_id,
                'phone_number' => $phone,
                'is_primary' => $index === 0
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Placement Test
    |--------------------------------------------------------------------------
    */

    private function createPlacementTest($student, $data)
    {
        return PlacementTest::create([
            'student_id' => $student->student_id,
            'score' => $data['test_score'] ?? 0,
            'assigned_level_id' => $data['level_id'],
            'test_fee' => $data['test_fee'] ?? 0,
            'fee_paid' => true,
            'deducted_from_course' => true,
            'created_by_cs_id' => $this->employeeId()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Enrollment
    |--------------------------------------------------------------------------
    */

    
    private function createEnrollment($student, $placement, $data, $decision)
    {
        return Enrollment::create([
            'student_id' => $student->student_id,
            'placement_test_id' => $placement->test_id,

            'level_id' => $data['level_id'],

            'course_instance_id' => optional($decision['instance'])->course_instance_id,

            'teacher_id' => optional($decision['teacher'])->id,

            'patch_id' => $patchData['patch_id'],

            'enrollment_type' => $data['type'],
            'delivery_mode' => $data['mode'],

            'final_price' => $data['final_price'],
            'payment_plan_id' => $data['payment_plan_id'],

            'status' => 'Active',
            'restriction_flag' => false,

            'created_by_cs_id' => $this->employeeId()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Waiting List Logic
    |--------------------------------------------------------------------------
    */

    private function addToWaitingList($enrollment, $data, $patchData)
    {
        WaitingList::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'requested_patch_id' => $data['patch_id'] ?? null,
            'preferred_start_type' => $data['patch_option'],
            'preferred_date' => $patchData['date'] ?? null,
            'created_by_cs_id' => $this->employeeId(),
            'status' => 'Active'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Lead Conversion
    |--------------------------------------------------------------------------
    */

    private function convertLead($leadId, $student)
    {
        $lead = Lead::findOrFail($leadId);

        $lead->update([
            'status' => 'Registered',
            'student_id' => $student->student_id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private function employeeId()
    {
        return Auth::user()->employees->first()->employee_id;
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
            return [
                'patch_id' => null,
                'type' => 'waiting',
                'date' => $data['custom_date']
            ];
        }

        return null;
    }
}
