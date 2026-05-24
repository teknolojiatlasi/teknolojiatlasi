<?php

use App\Support\WebpImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Admin\BlogCommentController as AdminBlogCommentController;
use Modules\Blog\Http\Controllers\BlogCategoryController;
use Modules\Blog\Http\Controllers\BlogCommentController;
use Modules\Blog\Http\Controllers\BlogController;
use Modules\Blog\Http\Controllers\BlogMediaController;

Route::get('/is-ilani', [BlogController::class, 'publicIndex'])->name('blog.public.index');

Route::get('/is-ilani/{blog}', [BlogController::class, 'publicShow'])->name('blog.public.show');
Route::get('/blog/{blog}', [BlogController::class, 'publicShow'])->name('blog.public.show');

Route::get('/blog-media/{path}', [BlogMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('blog.media.show');

Route::post('/blog/{blog}/comments', [BlogCommentController::class, 'store'])
    ->middleware(['throttle:12,1', 'spam_protected'])
    ->name('blog.comments.store');

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])->group(function () {
    Route::resource('blogs', BlogController::class)->names('blog');

    Route::get('/admin/blog-comments', [AdminBlogCommentController::class, 'index'])
        ->name('blog.comments.index');
    Route::delete('/admin/blog-comments/{comment}', [AdminBlogCommentController::class, 'destroy'])
        ->name('blog.comments.destroy');
});

Route::get('/blog-test', function () {
    return 'Blog module calisiyor';
});

Route::delete('/admin/blogs/images/{image}', [BlogController::class, 'deleteImage'])
    ->name('blog.image.delete')
    ->middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa']);

Route::post('/admin/blogs/ckeditor/upload', function (Request $request) {
    if (! $request->hasFile('upload')) {
        return response()->json([
            'error' => [
                'message' => 'Dosya bulunamadi',
            ],
        ], 400);
    }

    $request->validate([
        'upload' => 'required|file|image|mimes:jpg,jpeg,png,webp|extensions:jpg,jpeg,png,webp|mimetypes:image/jpeg,image/png,image/webp|max:4096',
    ]);

    $path = WebpImageUploader::store(
        file: $request->file('upload'),
        directory: 'blogs/editor',
        disk: 'public',
        maxWidth: 1920,
        maxHeight: 1920,
        quality: 82,
        errorKey: 'upload',
    );

    return response()->json([
        'uploaded' => 1,
        'fileName' => basename($path),
        'url' => route('blog.media.show', ['path' => $path]),
    ]);
})->middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])
    ->name('blog.ckeditor.upload');

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])->group(function () {
    Route::resource('blog-categories', BlogCategoryController::class)
        ->names('blog.categories')
        ->parameters([
            'blog-categories' => 'category',
        ]);
});
