<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Http\Controllers\MediaController;


Route::get('/media', [MediaController::class, 'index'])->name('media.index');
/*Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('media', MediaController::class)->names('media');
});

Route::get('/media-test', function () {
    return 'Media module çalışıyor ✅';
});
    Route::resource('media', MediaController::class)->names('media');
*/
