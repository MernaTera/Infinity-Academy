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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


////////////    LEADS       ////////////////    
Route::middleware('auth')->group(function () {
    Route::get('/leads/dashboard', [LeadDashboardController::class, 'index']) ->middleware(['auth', 'verified']) ->name('leads.dashboard');
    Route::get('/leads/public', [LeadController::class, 'publicLeads']) ->name('leads.public');
    Route::get('/leads/archived', [LeadController::class, 'archived']) ->name('leads.archived');
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{id}/assign', [LeadController::class, 'assign']) ->name('leads.assign');
    Route::get('/levels/{courseId}', function ($courseId) { return \App\Models\Academic\Level::where('course_template_id', $courseId)->get(); });
    Route::get('/sublevels/{levelId}', function ($levelId) { return \App\Models\Academic\Sublevel::where('level_id', $levelId)->get(); });
    Route::get('/leads/{leadId}/history', [LeadController::class, 'history']);
    Route::post('/leads/update-status', [LeadController::class, 'updateStatus']) ->name('leads.update.status');
});

////// registeration //////
Route::middleware('auth')->group(function () {
    Route::get('/registration/from-lead/{lead_id}', [RegistrationController::class, 'createFromLead']) ->name('registration.from.lead');
    Route::post('/registration/store', [RegistrationController::class, 'store']) ->name('registration.store');
    Route::put('/leads/{id}', [LeadController::class, 'update']);
    Route::get('/patch-options/{courseId}', [RegistrationController::class, 'getPatchOptions']);
    Route::post('/calculate-price', [RegistrationController::class, 'calculatePrice']);
    Route::post('/available-teachers', [RegistrationController::class, 'getAvailableTeachers']);
    Route::post('/teacher-schedule', [RegistrationController::class, 'getTeacherSchedule']);
    Route::post('/get-material', [RegistrationController::class, 'getMaterial']);
});


////// Sales Table //////
Route::middleware('auth')
    ->prefix('sales')
    ->name('sales.')
    ->group(function () {

        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/daily', [SalesController::class, 'dailyBreakdown'])->name('daily');
        Route::get('/patch/{patch}', [SalesController::class, 'byPatch'])->name('by-patch');
    });

Route::middleware('auth')
    ->prefix('outstanding')
    ->name('outstanding.')
    ->group(function () {
        Route::get('/', [OutstandingController::class, 'index'])->name('index');
        Route::post('/{enrollment}/pay', [OutstandingController::class, 'recordPayment'])->name('pay');
    });

////// Student Care //////
Route::middleware(['auth', 'permission:enrollment.view'])
    ->prefix('student-care')
    ->name('student-care.')
    ->group(function () {

        Route::get('/dashboard', function () { return view('student-care.dashboard'); })->name('dashboard');
        Route::get('/waiting-list', [StudentCareController::class, 'waitingList'])->name('waiting-list');
        Route::post('/assign', [StudentCareController::class, 'assign'])->name('assign');
        Route::get('/course-instances', [CourseInstanceController::class, 'index'])->name('instances');
        Route::get('/course-instances/{id}', [CourseInstanceController::class, 'show'])->name('instances.show');
        Route::post('/course-instances/store', [CourseInstanceController::class, 'storeInstance'])->name('instance.store');
        Route::get('/teachers/by-course/{courseId}', [CourseInstanceController::class, 'getTeachersByCourse'])->name('teachers.by-course');
        Route::get('/teachers/by-course-level/{levelId}', [CourseInstanceController::class, 'getTeachersByLevel'])->name('teachers.by-level');
        Route::get('/attendance/{session}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/instance/{id}/schedule-data', [CourseInstanceController::class, 'getScheduleData'])->name('instance.schedule.data');
        Route::post('/instance/{id}/schedule', [CourseInstanceController::class, 'storeSchedule'])->name('instance.schedule.store');
    });
require __DIR__.'/auth.php';