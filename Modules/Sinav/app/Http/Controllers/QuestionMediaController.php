<?php

namespace Modules\Sinav\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionMediaController extends Controller
{
    public function show(string $path)
    {
        $path = ltrim($path, '/');

        abort_unless($path !== '', 404);
        abort_unless(! Str::contains($path, ['..', '\\']), 404);
        abort_unless(Str::startsWith($path, 'sinav/questions/'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, headers: [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
