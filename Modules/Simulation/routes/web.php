<?php

use Illuminate\Support\Facades\Route;
use Modules\Simulation\Http\Controllers\Admin\SimulationAdminController;
use Modules\Simulation\Http\Controllers\Admin\SimulationCategoryController;
use Modules\Simulation\Http\Controllers\SimulationController;
use Modules\Simulation\Http\Controllers\SimulationMediaController;

Route::get('/simulasyonlar', [SimulationController::class, 'index'])
    ->name('simulation.index');

Route::get('/simulasyonlar/kategori/{category}', [SimulationController::class, 'category'])
    ->name('simulation.category');

Route::get('/simulasyonlar/media/{path}', [SimulationMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('simulation.media.show');

Route::get('/simulasyonlar/{slug}', [SimulationController::class, 'show'])
    ->name('simulation.show');

Route::get('/simulation-test', function () {
    return 'Simulation module calisiyor';
});

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])
    ->prefix('admin/simulation')
    ->name('simulation.admin.')
    ->group(function () {
        Route::get('/categories', [SimulationCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [SimulationCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [SimulationCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [SimulationCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::resource('simulations', SimulationAdminController::class)
            ->names('simulations')
            ->parameters(['simulations' => 'simulation']);
    });
