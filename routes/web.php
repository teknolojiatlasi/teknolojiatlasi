<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\Admin\UserRoleController;
use Modules\Survey\Models\Survey;


/*
Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', [HomeController::class, 'index'])->name('anasayfa');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/site/haberdetay/{id}', function ($id) {
    return redirect('/sosial', 301);
});



Route::get('/ilansayfasi', function () {
    return view('welcome');
});

Route::prefix('push')->middleware('throttle:20,1')->group(function () {
    Route::get('/vapid-public-key', [PushSubscriptionController::class, 'vapidPublicKey'])
        ->name('push.vapid-public-key');
    Route::post('/subscriptions', [PushSubscriptionController::class, 'store'])
        ->name('push.subscriptions.store');
    Route::delete('/subscriptions', [PushSubscriptionController::class, 'destroy'])
        ->name('push.subscriptions.destroy');
});




Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->middleware('guest')
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->middleware('guest')
    ->name('social.callback');




Route::get('/dashboard', function () {
    $now = now();

    $activeSurvey = Survey::query()
        ->where('is_public', true)
        ->where('is_active', true)
        ->where(function ($query) use ($now) {
            $query->whereNull('opens_at')->orWhere('opens_at', '<=', $now);
        })
        ->where(function ($query) use ($now) {
            $query->whereNull('closes_at')->orWhere('closes_at', '>=', $now);
        })
        ->latest()
        ->first();

    return view('dashboard', compact('activeSurvey'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/yonetim', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'role:superadmin|admin', 'admin_mfa'])
    ->name('admin.dashboard');

Route::middleware(['auth', 'role:superadmin|admin', 'admin_mfa'])->prefix('yonetim')->name('admin.')->group(function () {
    Route::get('/kullanicilar', [UserRoleController::class, 'index'])->name('users.index');
    Route::get('/kullanicilar/{user}/roller', [UserRoleController::class, 'edit'])->name('users.edit');
    Route::put('/kullanicilar/{user}/roller', [UserRoleController::class, 'update'])->name('users.update');
    Route::delete('/kullanicilar/{user}', [UserRoleController::class, 'destroy'])->name('users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
