<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Auth\ModuleSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RolePermissionSeeder;
use Database\Seeders\Auth\AdminUserSeeder;
use Database\Seeders\Auth\UserSeeder;

use Database\Seeders\Core\BranchSeeder;

use Database\Seeders\Academic\EnglishLevelSeeder;
use Database\Seeders\Academic\CourseTemplateSeeder;
use Database\Seeders\Academic\CourseSessionSeeder;
use Database\Seeders\Academic\BreakSlotSeeder;
use Database\Seeders\Academic\TimeSlotSeeder;
use Database\Seeders\Academic\CourseInstanceSeeder;
use Database\Seeders\Academic\InstanceScheduleSeeder;
use Database\Seeders\Academic\PatchSeeder;
use Database\Seeders\Academic\SublevelSeeder;
use Database\Seeders\Academic\LevelSeeder;
use Database\Seeders\Academic\RoomSeeder;

use Database\Seeders\Enrollment\CsTargetSeeder;
use Database\Seeders\Enrollment\PlacementTestSeeder;
use Database\Seeders\Enrollment\EnrollmentSeeder;
use Database\Seeders\Enrollment\PostponementSeeder;
use Database\Seeders\Enrollment\RestrictionLogSeeder;
use Database\Seeders\Enrollment\WaitingListSeeder;

use Database\Seeders\Finance\PaymentPlanSeeder;
use Database\Seeders\Finance\PrivateBundleSeeder;
use Database\Seeders\Finance\OfferSeeder;
use Database\Seeders\Finance\FinancialTransactionSeeder;
use Database\Seeders\Finance\InstallmentScheduleSeeder;
use Database\Seeders\Finance\InstallmentApprovalLogSeeder;
use Database\Seeders\Finance\RefundRequestSeeder;
use Database\Seeders\Finance\RevenueSplitSeeder;
use Database\Seeders\Finance\BundleUsageLogSeeder;

use Database\Seeders\HR\EmployeeSeeder;
use Database\Seeders\HR\TeacherSeeder;
use Database\Seeders\HR\TeacherAvailabilitySeeder;
use Database\Seeders\HR\ContractTypeSeeder;

use Database\Seeders\Attendance\AttendanceSeeder;

use Database\Seeders\Leads\LeadSeeder;
use Database\Seeders\Leads\LeadCallLogSeeder;
use Database\Seeders\Leads\LeadHistorySeeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Auth\RoleSeeder::class,
            \Database\Seeders\Auth\ModuleSeeder::class,
            \Database\Seeders\Auth\PermissionSeeder::class,
            \Database\Seeders\Auth\RolePermissionSeeder::class,
            \Database\Seeders\Auth\AdminUserSeeder::class,
            \Database\Seeders\Auth\UserSeeder::class
        ]);

        $this->call([
            \Database\Seeders\Core\BranchSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\Academic\EnglishLevelSeeder::class,
            \Database\Seeders\Academic\CourseTemplateSeeder::class,
            \Database\Seeders\Academic\CourseSessionSeeder::class,
            \Database\Seeders\Academic\BreakSlotSeeder::class,
            \Database\Seeders\Academic\TimeSlotSeeder::class,
            \Database\Seeders\Academic\CourseInstanceSeeder::class,
            \Database\Seeders\Academic\InstanceScheduleSeeder::class,
            \Database\Seeders\Academic\PatchSeeder::class,
            \Database\Seeders\Academic\SublevelSeeder::class,
            \Database\Seeders\Academic\LevelSeeder::class,
            \Database\Seeders\Academic\RoomSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\Attendance\AttendanceSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\Enrollment\CsTargetSeeder::class,
            \Database\Seeders\Enrollment\PlacementTestSeeder::class,
            \Database\Seeders\Enrollment\EnrollmentSeeder::class,
            \Database\Seeders\Enrollment\PostponementSeeder::class,
            \Database\Seeders\Enrollment\RestrictionLogSeeder::class,
            \Database\Seeders\Enrollment\WaitingListSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\Finance\PaymentPlanSeeder::class,
            \Database\Seeders\Finance\PrivateBundleSeeder::class,
            \Database\Seeders\Finance\OfferSeeder::class,
            \Database\Seeders\Finance\FinancialTransactionSeeder::class,
            \Database\Seeders\Finance\InstallmentScheduleSeeder::class,
            \Database\Seeders\Finance\InstallmentApprovalLogSeeder::class,
            \Database\Seeders\Finance\RefundRequestSeeder::class,
            \Database\Seeders\Finance\RevenueSplitSeeder::class,
            \Database\Seeders\Finance\BundleUsageLogSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\HR\EmployeeSeeder::class,
            \Database\Seeders\HR\TeacherSeeder::class,
            \Database\Seeders\HR\TeacherAvailabilitySeeder::class,
            \Database\Seeders\HR\ContractTypeSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\Leads\LeadSeeder::class,
            \Database\Seeders\Leads\LeadCallLogSeeder::class,
            \Database\Seeders\Leads\LeadHistorySeeder::class,
        ]);
    }
}
