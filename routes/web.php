<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LeadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assign'])
        ->name('leads.assign');
    Route::get('/levels/{courseId}', function ($courseId) {
        return \App\Models\Academic\Level::where('course_template_id', $courseId)->get();
    });

    Route::get('/sublevels/{levelId}', function ($levelId) {
        return \App\Models\Academic\Sublevel::where('level_id', $levelId)->get();
    });
});


require __DIR__.'/auth.php';