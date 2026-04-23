<?php

namespace Database\Seeders;

use App\Models\Auth\User;
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
use Database\Seeders\Enrollment\MaterialSeeder;
use Database\Seeders\Enrollment\MaterialAssignmentSeeder;


use Database\Seeders\Finance\PaymentPlanSeeder;
use Database\Seeders\Finance\PrivateBundleSeeder;
use Database\Seeders\Finance\OfferSeeder;
use Database\Seeders\Finance\FinancialTransactionSeeder;
use Database\Seeders\Finance\InstallmentScheduleSeeder;
use Database\Seeders\Finance\InstallmentApprovalLogSeeder;
use Database\Seeders\Finance\RefundRequestSeeder;
use Database\Seeders\Finance\RevenueSplitSeeder;
use Database\Seeders\Finance\BundleUsageLogSeeder;
use Database\Seeders\Finance\OutstandingBalanceSeeder;
use Database\Seeders\Finance\LevelPackageSeeder;

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
        //  AUTH
        $this->call([
            RoleSeeder::class,
            ModuleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
        ]);

        //  CORE
        $this->call([
            BranchSeeder::class,
        ]);

        // HR 
        $this->call([
            EmployeeSeeder::class,
            EnglishLevelSeeder::class,
            TeacherSeeder::class,
        ]);

        //  ACADEMIC 
        $this->call([
            CourseTemplateSeeder::class,
            LevelSeeder::class,
            SublevelSeeder::class,
            PatchSeeder::class,
            RoomSeeder::class,
            TimeSlotSeeder::class,
            BreakSlotSeeder::class,
            CourseInstanceSeeder::class,
            InstanceScheduleSeeder::class,
            CourseSessionSeeder::class,
        ]);

        //  ENROLLMENT
        $this->call([
            PlacementTestSeeder::class,
            EnrollmentSeeder::class,
            CsTargetSeeder::class,
            PostponementSeeder::class,
            RestrictionLogSeeder::class,
            WaitingListSeeder::class,
            MaterialSeeder::class,
            MaterialAssignmentSeeder::class,
        ]);

        //  FINANCE
        $this->call([
            PaymentPlanSeeder::class,
            PrivateBundleSeeder::class,
            OfferSeeder::class,
            FinancialTransactionSeeder::class,
            InstallmentScheduleSeeder::class,
            InstallmentApprovalLogSeeder::class,
            RefundRequestSeeder::class,
            RevenueSplitSeeder::class,
            BundleUsageLogSeeder::class,
            OutstandingBalanceSeeder::class,
            LevelPackageSeeder::class,
        ]);

        //  ATTENDANCE
        $this->call([
            AttendanceSeeder::class,
        ]);

        //  HR EXTENSIONS 
        $this->call([
            TeacherAvailabilitySeeder::class,
            ContractTypeSeeder::class,
        ]);

        //  LEADS 
        $this->call([
            LeadSeeder::class,
            LeadCallLogSeeder::class,
            LeadHistorySeeder::class,
        ]);
    }
}
