<?php

use Illuminate\Support\Facades\Route;
use Modules\Sossial\Http\Controllers\Admin\CommentController as AdminCommentController;
use Modules\Sossial\Http\Controllers\Admin\PostController as AdminPostController;
use Modules\Sossial\Http\Controllers\Admin\TagController as AdminTagController;
use Modules\Sossial\Http\Controllers\CommentController;
use Modules\Sossial\Http\Controllers\FeedController;
use Modules\Sossial\Http\Controllers\FollowController;
use Modules\Sossial\Http\Controllers\MessageController;
use Modules\Sossial\Http\Controllers\PostController;
use Modules\Sossial\Http\Controllers\PostMediaController;
use Modules\Sossial\Http\Controllers\ProfileController;
use Modules\Sossial\Http\Controllers\TagController;

Route::prefix('sosial')->name('sosial.')->group(function () {
    Route::get('/', [FeedController::class, 'index'])->name('feed');
    Route::get('/media/{media}', [PostMediaController::class, 'show'])->name('media.show');
    Route::get('/explore', [TagController::class, 'explore'])->name('explore');
    Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');
    Route::get('/u/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/posts/create', [PostController::class, 'create'])
        ->middleware(['auth', 'verified'])
        ->name('posts.create');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    Route::middleware(['auth'])->group(function () {
        Route::post('/u/{user}/follow', [FollowController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('follow.store');
        Route::delete('/u/{user}/follow', [FollowController::class, 'destroy'])
            ->middleware('throttle:30,1')
            ->name('follow.destroy');
        Route::get('/messages/unread-count', [MessageController::class, 'unreadCount'])
            ->middleware('throttle:60,1')
            ->name('messages.unread-count');
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::match(['put', 'patch'], '/posts/{post}', [PostController::class, 'update'])
            ->middleware('throttle:15,1')
            ->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])
            ->middleware('throttle:15,1')
            ->name('posts.destroy');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/my', [FeedController::class, 'my'])->name('my');
        Route::get('/following', [FeedController::class, 'following'])->name('following');
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{user}', [MessageController::class, 'store'])
            ->middleware(['throttle:20,1', 'spam_protected'])
            ->name('messages.store');

        Route::post('/posts', [PostController::class, 'store'])
            ->middleware(['throttle:10,1', 'spam_protected'])
            ->name('posts.store');

        Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
            ->middleware(['throttle:20,1', 'spam_protected'])
            ->name('comments.store');
        Route::post('/comments/{comment}/replies', [CommentController::class, 'reply'])
            ->middleware(['throttle:20,1', 'spam_protected'])
            ->name('comments.reply');
    });
});

Route::middleware(['auth', 'role:superadmin|admin', 'admin_mfa'])
    ->prefix('yonetim/sosial')
    ->name('admin.sossial.')
    ->group(function () {
        Route::resource('posts', AdminPostController::class)->except(['show']);
        Route::resource('tags', AdminTagController::class)->except(['show']);
        Route::resource('comments', AdminCommentController::class)->except(['create', 'store', 'show']);
    });
