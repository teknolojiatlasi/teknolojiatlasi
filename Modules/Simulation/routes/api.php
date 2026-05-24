<?php

use Illuminate\Support\Facades\Route;

Route::prefix('simulation')->name('simulation.')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'module' => 'Simulation',
            'status' => 'ok',
        ]);
    })->name('health');
});
