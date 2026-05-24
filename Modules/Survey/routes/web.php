<?php

use Illuminate\Support\Facades\Route;
use Modules\Survey\Http\Controllers\PublicSurveyController;
use Modules\Survey\Http\Controllers\SurveyController;
use Modules\Survey\Http\Controllers\SurveyResponseController;

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])
    ->prefix('surveys')
    ->name('survey.')
    ->group(function () {
        Route::get('/', [SurveyController::class, 'index'])->name('index');
        Route::get('/create', [SurveyController::class, 'create'])->name('create');
        Route::post('/', [SurveyController::class, 'store'])->name('store');
        Route::get('/{survey}/edit', [SurveyController::class, 'edit'])->name('edit');
        Route::get('/{survey}/results', [SurveyController::class, 'results'])->name('results');
        Route::put('/{survey}', [SurveyController::class, 'update'])->name('update');
        Route::delete('/{survey}', [SurveyController::class, 'destroy'])->name('destroy');
    });

Route::name('survey.public.')->group(function () {
    Route::get('/anketler', [PublicSurveyController::class, 'index'])->name('index');

    Route::prefix('survey')->group(function () {
    Route::get('/{survey:slug}', [PublicSurveyController::class, 'show'])->name('show');
    Route::post('/{survey:slug}/responses', [SurveyResponseController::class, 'store'])
        ->middleware(['throttle:8,1', 'spam_protected'])
        ->name('submit');
    Route::get('/{survey:slug}/results', [PublicSurveyController::class, 'results'])->name('results');
    });
});
