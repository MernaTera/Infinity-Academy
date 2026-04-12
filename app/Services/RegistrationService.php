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
use App\Models\PlacementTest;
use App\Models\Enrollment\Material;
use App\Models\Enrollment\MaterialAssignment;
use App\Models\Enrollment\EnrollmentMaterial;

class RegistrationService
{
    public function register($data)
    {
        return DB::transaction(function () use ($data) {



            $this->validateBusiness($data);
            $this->validateTeacherAvailability($data);
            $this->validateNoConflict($data);
            $this->validatePatch($data);
            $this->validatePricing($data);
            $this->attachMaterials($enrollment, $data);

            $lead = Lead::findOrFail($data['lead_id']);

            $student = Student::create([
                'full_name' => $lead->full_name,
                'birthdate' => $lead->birthdate,
                'degree' => $lead->degree,
                'location' => $lead->location,
                'global_status' => 'Active',
                'is_active' => true
            ]);

            $patchData = $this->handlePatchSelection($data);
            $currentPatch = Patch::where('status', 'Active')->first();
            $availabilities = TeacherAvailability::where('day_of_week', $data['day'])
                ->where('time_slot_id', $data['time_slot_id'])
                ->get();
            $pricing = app(\App\Services\PricingService::class)->calculate($data);
            $data['final_price'] = $pricing['final_price'];

            $enrollment = $this->createEnrollment($student, $data, $patchData);

            $availableTeachers = [];

            foreach ($availabilities as $availability) {

                $isBusy = CourseInstance::where('teacher_id', $availability->teacher_id)
                    ->where('patch_id', $currentPatch->patch_id)
                    ->whereHas('schedules', function ($q) use ($data) {
                        $q->where('day_of_week', $data['day'])
                        ->where('time_slot_id', $data['time_slot_id']);
                    })
                    ->exists();

                if (!$isBusy) {
                    $availableTeachers[] = $availability->teacher;
                }
            }
            if ($patchData['type'] === 'waiting') {

                WaitingList::create([
                    'enrollment_id' => $enrollment->enrollment_id,
                    'preferred_date' => $patchData['date'],
                    'status' => 'Active'
                ]);
            }

            $lead->update([
                'status' => 'Registered',
                'student_id' => $student->student_id
            ]);

            return $enrollment;
        });
    }

    /*
    |------------------------------------------------------------------
    | Patch Logic
    |------------------------------------------------------------------
    */
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
        return Enrollment::create([

            'student_id' => $student->student_id,

            'level_id' => $data['interested_level_id'] ?? null,
            'sublevel_id' => $data['interested_sublevel_id'] ?? null,

            'course_instance_id' => $data['course_instance_id'] ?? null,

            'patch_id' => $patchData['patch_id'],

            'teacher_id' => $data['teacher_id'] ?? null,

            'enrollment_type' => ucfirst($data['type']),
            'delivery_mood' => $data['mode'] ?? 'Offline',

            'payment_plan_id' => $data['payment_plan_id'],

            'final_price' => $data['final_price'],

            'status' => $patchData['type'] === 'direct'
                ? 'Active'
                : 'Pending_Approval',

            'created_by_cs_id' => auth()->user()->employees->first()->employee_id
        ]);
    }

    private function validateBusiness($data)
    {
        $lead = \App\Models\Leads\Lead::find($data['lead_id']);

        if ($lead->status === 'Registered') {
            throw new \Exception('Lead already registered');
        }

        if ($data['type'] === 'group') {

            if ($data['patch_option'] === 'current' && empty($data['patch_id'])) {
                throw new \Exception('Invalid patch selection');
            }

            if ($data['patch_option'] === 'custom' && empty($data['custom_date'])) {
                throw new \Exception('Custom date required');
            }
        }

        if ($data['type'] === 'private') {

            if (empty($data['teacher_id']) && empty($data['recommended_date'])) {
                throw new \Exception('Choose teacher or date');
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
            ->where('day_of_week', $data['day'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->exists();

        if (!$exists) {
            throw new \Exception('Teacher not available anymore');
        }
    }

    private function validateNoConflict($data)
    {
        if (empty($data['teacher_id'])) return;

        $conflict = \App\Models\Academic\CourseInstance::where('teacher_id', $data['teacher_id'])
            ->where('patch_id', $data['patch_id'])
            ->whereHas('schedules', function ($q) use ($data) {
                $q->where('day_of_week', $data['day'])
                ->where('time_slot_id', $data['time_slot_id']);
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
        return \App\Models\PlacementTest::create([
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

        return $patchData['type'] === 'direct'
            ? 'Active'
            : 'Pending_Approval';
    }

    private function attachMaterials($enrollment, $data)
    {
        $materials = \App\Models\MaterialAssignment::where(function ($q) use ($data) {

            $q->where('course_template_id', $data['course_template_id'])
            ->orWhere('level_id', $data['level_id'])
            ->orWhere('sublevel_id', $data['sublevel_id']);

        })->with('material')->get();

        foreach ($materials as $m) {

            \App\Models\EnrollmentMaterial::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'material_id' => $m->material_id,
                'price' => $m->material->price,
                'status' => 'Pending'
            ]);
        }
    }
}