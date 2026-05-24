<?php

use Illuminate\Support\Facades\Route;
use Modules\Contact\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use Modules\Contact\Http\Controllers\Admin\ContactSettingController as AdminContactSettingController;
use Modules\Contact\Http\Controllers\Public\ContactPublicController;

Route::get('/iletisim', [ContactPublicController::class, 'index'])->name('contact_public_index');
Route::post('/iletisim', [ContactPublicController::class, 'store'])
    ->middleware(['throttle:5,1', 'spam_protected'])
    ->name('contact_public_store');

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])
    ->prefix('admin/contact')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('contact_admin_messages_index'))->name('contact_admin_home');

        Route::get('/messages', [AdminContactMessageController::class, 'index'])->name('contact_admin_messages_index');
        Route::get('/messages/{message}', [AdminContactMessageController::class, 'show'])->name('contact_admin_messages_show');
        Route::put('/messages/{message}/read', [AdminContactMessageController::class, 'markRead'])->name('contact_admin_messages_mark_read');
        Route::put('/messages/{message}/unread', [AdminContactMessageController::class, 'markUnread'])->name('contact_admin_messages_mark_unread');
        Route::post('/messages/{message}/reply', [AdminContactMessageController::class, 'reply'])->name('contact_admin_messages_reply');

        Route::get('/settings', [AdminContactSettingController::class, 'edit'])->name('contact_admin_settings_edit');
        Route::put('/settings', [AdminContactSettingController::class, 'update'])->name('contact_admin_settings_update');
    });
