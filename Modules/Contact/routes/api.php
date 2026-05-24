<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
    ->prefix('v1/contact')
    ->name('contact_api_')
    ->group(function () {
        // API endpoints can be added here if needed.
    });
