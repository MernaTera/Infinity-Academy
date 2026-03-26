<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadDashboardController;
use App\Http\Controllers\LeadController;



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
    Route::get('/leads/dashboard', [LeadDashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('leads.dashboard');
    Route::get('/leads/public', [LeadController::class, 'publicLeads'])
        ->name('leads.public');
    Route::get('/leads/archived', [LeadController::class, 'archived'])
        ->name('leads.archived');
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{id}/assign', [LeadController::class, 'assign'])
        ->name('leads.assign');
    Route::get('/levels/{courseId}', function ($courseId) {
        return \App\Models\Academic\Level::where('course_template_id', $courseId)->get();
    });

    Route::get('/sublevels/{levelId}', function ($levelId) {
        return \App\Models\Academic\Sublevel::where('level_id', $levelId)->get();
    });

    Route::get('/leads/{leadId}/history', [LeadController::class, 'history']);
});


require __DIR__.'/auth.php';