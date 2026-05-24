<?php

use Illuminate\Support\Facades\Route;
use Modules\Exam\Http\Controllers\ExamController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('exams', ExamController::class)->names('exam');
});
