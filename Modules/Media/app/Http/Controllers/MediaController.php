<?php

namespace Modules\Media\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\WebpImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Blog\Models\Blog;
use Modules\Media\Models\Media;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $mediaItems = Media::query()
            ->latest()
            ->paginate(36)
            ->withQueryString();

        return view('media::index', compact('mediaItems'));
    }

    public function create()
    {
        return redirect()->route('media.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'max:20'],
            'images.*' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'extensions:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:2048',
            ],
        ]);

        foreach ($request->file('images', []) as $image) {
            $this->storeImage($image, $request->user()?->id);
        }

        return redirect()
            ->route('media.index')
            ->with('success', 'Resimler medya kutuphanesine eklendi.');
    }

    public function show(Media $medium)
    {
        abort_unless(Storage::disk('public')->exists($medium->file_path), 404);

        return Storage::disk('public')->response($medium->file_path, headers: [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function edit(Media $medium)
    {
        return redirect()->route('media.index');
    }

    public function update(Request $request, Media $medium)
    {
        return redirect()->route('media.index');
    }

    public function destroy(Media $medium)
    {
        if (Blog::query()->where('cover_media_id', $medium->id)->exists()) {
            return redirect()
                ->route('media.index')
                ->withErrors(['media' => 'Bu resim bir ilanda kapak resmi olarak kullaniliyor. Once ilandaki kapak resmini degistirin.']);
        }

        Storage::disk('public')->delete($medium->file_path);
        $medium->delete();

        return redirect()
            ->route('media.index')
            ->with('success', 'Resim silindi.');
    }

    protected function storeImage(UploadedFile $file, ?int $userId): Media
    {
        $path = WebpImageUploader::store(
            file: $file,
            directory: 'uploads/covers',
            disk: 'public',
            maxWidth: 1920,
            maxHeight: 1920,
            quality: 82,
            errorKey: 'images',
        );

        return Media::create([
            'file_name' => basename($path),
            'file_path' => $path,
            'mime_type' => Storage::disk('public')->mimeType($path) ?: $file->getMimeType(),
            'size' => Storage::disk('public')->size($path),
            'user_id' => $userId,
        ]);
    }
}
