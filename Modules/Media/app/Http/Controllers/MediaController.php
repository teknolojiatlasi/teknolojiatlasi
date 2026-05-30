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
        $search = trim((string) $request->query('q', ''));
        $selectedCollection = trim((string) $request->query('collection', ''));

        $mediaItems = Media::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('file_name', 'like', $like)
                        ->orWhere('file_path', 'like', $like)
                        ->orWhere('collection', 'like', $like);
                });
            })
            ->when($selectedCollection !== '', fn ($query) => $query->where('collection', $selectedCollection))
            ->latest()
            ->paginate(36)
            ->withQueryString();

        $collections = Media::query()
            ->whereNotNull('collection')
            ->where('collection', '!=', '')
            ->distinct()
            ->orderBy('collection')
            ->pluck('collection');

        return view('media::index', compact('mediaItems', 'collections', 'search', 'selectedCollection'));
    }

    public function create()
    {
        return redirect()->route('media.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'max:20'],
            'collection' => ['nullable', 'string', 'max:80'],
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
            $this->storeImage($image, $request->user()?->id, $request->string('collection')->trim()->toString() ?: 'Kapak Resimleri');
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
                ->withErrors(['media' => 'Bu resim bir yazida kapak resmi olarak kullaniliyor. Once yazidaki kapak resmini degistirin.']);
        }

        Storage::disk('public')->delete($medium->file_path);
        $medium->delete();

        return redirect()
            ->route('media.index')
            ->with('success', 'Resim silindi.');
    }

    protected function storeImage(UploadedFile $file, ?int $userId, string $collection): Media
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
            'collection' => $collection,
            'mime_type' => Storage::disk('public')->mimeType($path) ?: $file->getMimeType(),
            'size' => Storage::disk('public')->size($path),
            'user_id' => $userId,
        ]);
    }
}
