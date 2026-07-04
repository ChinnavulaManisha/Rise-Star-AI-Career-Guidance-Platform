<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Student Routes
Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/tests', [\App\Http\Controllers\TestController::class, 'index'])->name('tests.index');
    Route::post('/tests/ai-generate', [\App\Http\Controllers\TestController::class, 'generateAiQuiz'])->name('tests.ai-generate');
    Route::get('/tests/{id}', [\App\Http\Controllers\TestController::class, 'show'])->name('tests.show');
    Route::post('/tests/{id}/submit', [\App\Http\Controllers\TestController::class, 'submit'])->name('tests.submit');
    Route::get('/tests/result/{attempt}', [\App\Http\Controllers\TestController::class, 'result'])->name('tests.result');
    Route::get('/tests/result/{attempt}/download', [\App\Http\Controllers\TestController::class, 'downloadReport'])->name('tests.result.download');
    Route::get('/careers', [\App\Http\Controllers\CareerController::class, 'index'])->name('careers.index');
    Route::get('/careers/explore/{title}', [\App\Http\Controllers\CareerController::class, 'explore'])->name('careers.explore');
    Route::post('/careers/{id}/bookmark', [\App\Http\Controllers\CareerController::class, 'toggleBookmark'])->name('careers.bookmark');
    Route::post('/careers/explain-step', [\App\Http\Controllers\CareerController::class, 'explainStep'])->name('careers.explain-step');

    // AI Blueprint
    Route::get('/blueprint', [\App\Http\Controllers\CareerController::class, 'blueprint'])->name('blueprint');
    Route::post('/blueprint/regenerate', [\App\Http\Controllers\CareerController::class, 'regenerateBlueprint'])->name('blueprint.regenerate');

    // AI Resume Builder
    Route::get('/resume', [\App\Http\Controllers\ResumeController::class, 'index'])->name('resume.index');
    Route::post('/resume/generate', [\App\Http\Controllers\ResumeController::class, 'generate'])->name('resume.generate');
    Route::post('/resume/update', [\App\Http\Controllers\ResumeController::class, 'update'])->name('resume.update');

    // Skill-Gap Analysis
    Route::get('/skill-gap', function () {
        return view('student.skill-gap.index');
    })->name('skill-gap.index');

    // AI Mock Interview
    Route::get('/mock-interview', function () {
        return view('student.interview.index');
    })->name('interview.index');
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // After login always go to student dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('student.dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';
