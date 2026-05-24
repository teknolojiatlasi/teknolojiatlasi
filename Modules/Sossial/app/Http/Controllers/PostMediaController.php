<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Sossial\Models\PostMedia;

class PostMediaController extends Controller
{
    public function show(PostMedia $media)
    {
        abort_unless($media->type === 'image', 404);
        abort_unless((string) $media->path !== '', 404);
        abort_unless(Storage::disk('public')->exists($media->path), 404);

        return Storage::disk('public')->response($media->path, headers: [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}

