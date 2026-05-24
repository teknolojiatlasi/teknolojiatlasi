<?php

use Illuminate\Support\Facades\Route;
use Modules\Cv\Http\Controllers\CvController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cvs', CvController::class)->names('cv');
});
