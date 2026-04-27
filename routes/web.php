<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadDashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentCareController;
use App\Http\Controllers\CourseInstanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OutstandingController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\PatchAdminController;
use App\Http\Controllers\Admin\PaymentPolicyController;
use App\Http\Controllers\Admin\InstallmentApprovalController;
use App\Http\Controllers\Admin\OutstandingAdminController;
use App\Http\Controllers\Admin\OffersController;
use App\Http\Controllers\Admin\AdminSalesController;
use App\Http\Controllers\Admin\AuditController;

use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherReportController;

// ─────────────────────────────────────────────────────────────────
// Public
// ─────────────────────────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
});

// ─────────────────────────────────────────────────────────────────
// Authenticated — General
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─────────────────────────────────────────────────────────────────
// Customer Service — Leads
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'permission:leads.view'])
    ->group(function () {

        Route::get('/leads/dashboard', [LeadDashboardController::class, 'index'])->name('leads.dashboard');
        Route::get('/leads/public',    [LeadController::class, 'publicLeads'])->name('leads.public');
        Route::get('/leads/archived',  [LeadController::class, 'archived'])->name('leads.archived');

        Route::get('/leads/{leadId}/history', [LeadController::class, 'history'])->name('leads.history');

        // Dropdowns (used in forms — view permission is enough)
        Route::get('/levels/{courseId}',   fn($id) => \App\Models\Academic\Level::where('course_template_id', $id)->get());
        Route::get('/sublevels/{levelId}', fn($id) => \App\Models\Academic\Sublevel::where('level_id', $id)->get());
    });

Route::middleware(['auth', 'permission:leads.create'])
    ->group(function () {
        Route::post('/leads',                  [LeadController::class, 'store'])->name('leads.store');
        Route::post('/leads/{id}/assign',      [LeadController::class, 'assign'])->name('leads.assign');
        Route::post('/leads/update-status',    [LeadController::class, 'updateStatus'])->name('leads.update.status');
    });

Route::middleware(['auth', 'permission:leads.edit'])
    ->group(function () {
        Route::get('/leads',          [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/create',   [LeadController::class, 'create'])->name('leads.create');
        Route::get('/leads/{lead}',   [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}',   [LeadController::class, 'update'])->name('leads.update');
        Route::patch('/leads/{lead}', [LeadController::class, 'update']);
    });

Route::middleware(['auth', 'permission:leads.delete'])
    ->group(function () {
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    });

// ─────────────────────────────────────────────────────────────────
// Customer Service — Registration
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'permission:enrollment.create'])
    ->group(function () {
        Route::get('/registration/from-lead/{lead_id}', [RegistrationController::class, 'createFromLead'])->name('registration.from.lead');
        Route::post('/registration/store',              [RegistrationController::class, 'store'])->name('registration.store');
        Route::get('/registration/check-status/{enrollmentId}', [RegistrationController::class, 'checkApprovalStatus'])->name('registration.check-status');
        Route::get('/registration/pending/{enrollmentId}', [RegistrationController::class, 'pending'])->name('registration.pending');

        // AJAX helpers used inside the registration form
        Route::get('/patch-options/{courseId}',         [RegistrationController::class, 'getPatchOptions']);
        Route::post('/calculate-price',                 [RegistrationController::class, 'calculatePrice']);
        Route::post('/available-teachers',              [RegistrationController::class, 'getAvailableTeachers']);
        Route::post('/teacher-schedule',                [RegistrationController::class, 'getTeacherSchedule']);
        Route::post('/get-material',                    [RegistrationController::class, 'getMaterial']);
        Route::get('/level-packages/{courseId}', [RegistrationController::class, 'getLevelPackages']);
    });

// ─────────────────────────────────────────────────────────────────
// Customer Service — Sales & Outstanding
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'permission:financial.view'])
    ->prefix('sales')
    ->name('sales.')
    ->group(function () {
        Route::get('/',              [SalesController::class, 'index'])->name('index');
        Route::get('/daily',         [SalesController::class, 'dailyBreakdown'])->name('daily');
        Route::get('/patch/{patch}', [SalesController::class, 'byPatch'])->name('by-patch');
    });

Route::middleware(['auth', 'permission:financial.view'])
    ->prefix('outstanding')
    ->name('outstanding.')
    ->group(function () {
        Route::get('/', [OutstandingController::class, 'index'])->name('index');
    });

Route::middleware(['auth', 'permission:financial.create'])
    ->prefix('outstanding')
    ->name('outstanding.')
    ->group(function () {
        Route::post('/{enrollment}/pay', [OutstandingController::class, 'recordPayment'])->name('pay');
    });

// ─────────────────────────────────────────────────────────────────
// Student Care
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'permission:enrollment.view'])
    ->prefix('student-care')
    ->name('student-care.')
    ->group(function () {

        Route::get('/dashboard',     [StudentCareController::class, 'dashboard'])->name('dashboard');
        Route::get('/waiting-list',  [StudentCareController::class, 'waitingList'])->name('waiting-list');
        Route::get('/outstanding',   [StudentCareController::class, 'outstanding'])->name('outstanding');
        Route::get('/postponed',     [StudentCareController::class, 'postponed'])->name('postponed');

        Route::get('/course-instances',      [CourseInstanceController::class, 'index'])->name('instances');
        Route::get('/course-instances/{id}', [CourseInstanceController::class, 'show'])->name('instances.show');

        Route::get('/teachers/by-course/{courseId}',       [CourseInstanceController::class, 'getTeachersByCourse'])->name('teachers.by-course');
        Route::get('/teachers/by-course-level/{levelId}',  [CourseInstanceController::class, 'getTeachersByLevel'])->name('teachers.by-level');

        Route::get('/attendance/{session}',  [AttendanceController::class, 'show'])->name('attendance.show');

        Route::get('/instance/{id}/schedule-data', [CourseInstanceController::class, 'getScheduleData'])->name('instance.schedule-data');
        Route::get('/time-slots',                  fn() => \App\Models\Academic\TimeSlot::all())->name('time-slots');
    });

Route::middleware(['auth', 'permission:enrollment.create'])
    ->prefix('student-care')
    ->name('student-care.')
    ->group(function () {
        Route::post('/assign',                        [StudentCareController::class, 'assign'])->name('assign');
        Route::post('/course-instances/store',        [CourseInstanceController::class, 'storeInstance'])->name('instance.store');
        Route::post('/enrollments/{id}/postpone',     [CourseInstanceController::class, 'postponeEnrollment'])->name('instance.postpone');
        Route::post('/attendance',                    [AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('/instance/{id}/preview',         [CourseInstanceController::class, 'previewSchedule'])->name('instance.preview');
        Route::post('/instance/{id}/schedule',        [CourseInstanceController::class, 'storeSchedule'])->name('instance.schedule');
    });

Route::middleware(['auth', 'permission:enrollment.edit'])
    ->prefix('student-care')
    ->name('student-care.')
    ->group(function () {
        Route::patch('/postponed/{id}/resume', [StudentCareController::class, 'resumePostponement'])->name('postponed.resume');
        Route::patch('/postponed/{id}/expire', [StudentCareController::class, 'expirePostponement'])->name('postponed.expire');
    });

// ─────────────────────────────────────────────────────────────────
// Admin
// ─────────────────────────────────────────────────────────────────

// Admin: all routes — canDo() returns true for Admin automatically
Route::middleware(['auth', 'permission:hr.view'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Employees
        Route::get('/employees',               [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',        [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',              [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}',          [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{id}/edit',     [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{id}',          [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('/employees/{id}/toggle', [EmployeeController::class, 'toggle'])->name('employees.toggle');
        Route::post('/employees/{id}/profile', [EmployeeController::class, 'updateProfile'])->name('employees.update-profile');        
        
        // Courses
        Route::get('/courses',                 [CourseAdminController::class, 'index'])->name('courses.index');
        Route::get('/courses/create',          [CourseAdminController::class, 'create'])->name('courses.create');
        Route::post('/courses',                [CourseAdminController::class, 'store'])->name('courses.store');
        Route::get('/courses/{id}/edit',       [CourseAdminController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{id}',            [CourseAdminController::class, 'update'])->name('courses.update');
        Route::patch('/courses/{id}/archive',  [CourseAdminController::class, 'archive'])->name('courses.archive');

        // Materials
        Route::get('/materials/levels/{courseId}',   [MaterialController::class, 'getLevels'])->name('materials.levels');
        Route::get('/materials/sublevels/{levelId}', [MaterialController::class, 'getSublevels'])->name('materials.sublevels');
        Route::post('/materials/assign',             [MaterialController::class, 'assign'])->name('materials.assign');
        Route::delete('/materials/unassign/{id}',    [MaterialController::class, 'unassign'])->name('materials.unassign');
        Route::get('/materials',               [MaterialController::class, 'index'])->name('materials.index');
        Route::post('/materials',              [MaterialController::class, 'store'])->name('materials.store');
        Route::put('/materials/{id}',          [MaterialController::class, 'update'])->name('materials.update');
        Route::patch('/materials/{id}/toggle', [MaterialController::class, 'toggle'])->name('materials.toggle');


        // Patches & Time
        Route::get('/patches',                      [PatchAdminController::class, 'index'])->name('patches.index');
        Route::patch('/patches/{id}/status',        [PatchAdminController::class, 'updateStatus'])->name('patches.status');
        Route::post('/patches/time-slots',          [PatchAdminController::class, 'storeTimeSlot'])->name('patches.timeslots.store');
        Route::patch('/patches/time-slots/{id}',    [PatchAdminController::class, 'toggleTimeSlot'])->name('patches.timeslots.toggle');
        Route::post('/patches/break-slots',         [PatchAdminController::class, 'storeBreakSlot'])->name('patches.breakslots.store');
        Route::patch('/patches/break-slots/{id}',   [PatchAdminController::class, 'toggleBreakSlot'])->name('patches.breakslots.toggle');

        // Payment Policy
        Route::get('/payment-policy',              [PaymentPolicyController::class, 'index'])->name('payment-policy.index');
        Route::post('/payment-plans',              [PaymentPolicyController::class, 'storePlan'])->name('payment-plans.store');
        Route::put('/payment-plans/{id}',          [PaymentPolicyController::class, 'updatePlan'])->name('payment-plans.update');
        Route::patch('/payment-plans/{id}/toggle', [PaymentPolicyController::class, 'togglePlan'])->name('payment-plans.toggle');

        // Installments
        Route::get('/installments',                    [InstallmentApprovalController::class, 'index'])->name('installments.index');
        Route::patch('/installments/{id}/approve',     [InstallmentApprovalController::class, 'approve'])->name('installments.approve');
        Route::patch('/installments/{id}/reject',      [InstallmentApprovalController::class, 'reject'])->name('installments.reject');
        Route::get('/installments/check/{enrollmentId}', [InstallmentApprovalController::class, 'checkStatus'])->name('installments.check-status');

        // Outstanding
        Route::get('/outstanding',                     [OutstandingAdminController::class, 'index'])->name('outstanding.index');
        Route::patch('/outstanding/{id}/override',     [OutstandingAdminController::class, 'override'])->name('outstanding.override');

        // Offers
        Route::get('/offers',                  [OffersController::class, 'index'])->name('offers.index');
        Route::post('/offers',                 [OffersController::class, 'store'])->name('offers.store');
        Route::put('/offers/{id}',             [OffersController::class, 'update'])->name('offers.update');
        Route::patch('/offers/{id}/toggle',    [OffersController::class, 'toggle'])->name('offers.toggle');

        //Sales Tables
        Route::get('/sales', [AdminSalesController::class, 'index'])->name('sales.index');

        // Audit
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });

// ─────────────────────────────────────────────────────────────────
// Teacher
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'permission:academic.view'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedule',  [TeacherController::class, 'schedule'])->name('schedule');
        Route::get('/courses',   [TeacherController::class, 'courses'])->name('courses');
        Route::get('/courses/{id}', [TeacherController::class, 'courseShow'])->name('courses.show');

        Route::get('/reports',                         [TeacherReportController::class, 'index'])->name('reports.index');
        Route::get('/attendance/{sessionId}',          [TeacherAttendanceController::class, 'show'])->name('attendance.show');
    });

Route::middleware(['auth', 'permission:attendance.create'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::post('/attendance/{sessionId}', [TeacherAttendanceController::class, 'store'])->name('attendance.store');
    });

Route::middleware(['auth', 'permission:reports.create'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/reports/create/{instanceId}', [TeacherReportController::class, 'create'])->name('reports.create');
        Route::post('/reports',                    [TeacherReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{id}/edit',           [TeacherReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{id}',                [TeacherReportController::class, 'update'])->name('reports.update');
    });

// ─────────────────────────────────────────────────────────────────
// Notifications
// ─────────────────────────────────────────────────────────────────
Route::post('/notifications/mark-all-read', function() {
    $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
    if ($employee) {
        \DB::table('user_notification')
            ->where('employee_id', $employee->employee_id)
            ->update(['is_read' => true]);
    }
    return back();
})->middleware('auth');

Route::post('/notifications/{id}/read', function($id) {
    $employee = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
    if ($employee) {
        \DB::table('user_notification')
            ->where('user_notification_id', $id)
            ->where('employee_id', $employee->employee_id)
            ->update(['is_read' => true]);
    }
    return response()->json(['ok' => true]);
})->middleware('auth');

// ─────────────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';