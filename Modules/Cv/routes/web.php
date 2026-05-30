<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Modules\Cv\Http\Controllers\CvController;

Route::get('/cv/create', [CvController::class, 'create'])->name('cv.create');
Route::post('/cv', [CvController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('cv.store');
Route::get('/cv/{cv}/edit', [CvController::class, 'edit'])->name('cv.edit');
Route::put('/cv/{cv}', [CvController::class, 'update'])
    ->middleware('throttle:15,1')
    ->name('cv.update');
Route::get('/cv/{cv}', [CvController::class, 'show'])->name('cv.show');
Route::get('/cv/{cv}/pdf', [CvController::class, 'pdf'])->name('cv.pdf');
Route::delete('/cv/{cv}', [CvController::class, 'destroy'])
    ->middleware('throttle:10,1')
    ->name('cv.destroy');

Route::get('/storage/cv/photos/{filename}', function (string $filename) {
    abort_if(str_contains($filename, '/') || str_contains($filename, '\\'), 404);

    $path = 'cv/photos/' . $filename;

    if (! Storage::disk('public')->exists($path)) {
        $path = $filename;
    }

    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})->where('filename', '[A-Za-z0-9._-]+');
