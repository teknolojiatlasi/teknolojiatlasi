<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('pages/{page}', [PageController::class, 'show'])->name('page.show');
});

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])->group(function () {
    Route::resource('pages', PageController::class)
        ->except(['show'])
        ->names('page');
});
