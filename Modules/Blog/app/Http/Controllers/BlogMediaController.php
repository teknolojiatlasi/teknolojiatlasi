<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogMediaController extends Controller
{
    public function show(string $path)
    {
        $path = ltrim($path, '/');

        abort_unless($path !== '', 404);
        abort_unless(! Str::contains($path, ['..', '\\']), 404);
        abort_unless(Str::startsWith($path, 'blogs/'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, headers: [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
