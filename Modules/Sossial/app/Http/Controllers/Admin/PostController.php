<?php

namespace Modules\Sossial\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\WebpImageUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Sossial\Models\Post;
use Modules\Sossial\Models\PostMedia;

class PostController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        $q = trim((string) $request->query('q', ''));
        $type = trim((string) $request->query('type', ''));

        return view('sossial::admin.posts.index', [
            'q' => $q, 
            'type' => $type,
            'typeLabels' => $this->typeLabels(),
        ]);
    }

    public function create(): View
    {
        return view('sossial::admin.posts.create', [
            'post' => new Post(),
            'users' => $this->users(),
            'typeLabels' => $this->typeLabels(),
            'tagList' => '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);

        $post = Post::query()->create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'body' => $data['body'],
            'link_url' => $data['link_url'],
        ]);

        $post->syncTagsFromString($data['tags']);
        $this->syncMedia($request, $post);

        return redirect()
            ->route('admin.sossial.posts.edit', $post)
            ->with('success', 'Post olusturuldu.');
    }

    public function edit(Post $post): View
    {
        $post->load(['user:id,name,email', 'tags:id,name,slug', 'media']);

        return view('sossial::admin.posts.edit', [
            'post' => $post,
            'users' => $this->users(),
            'typeLabels' => $this->typeLabels(),
            'tagList' => $post->tags->pluck('name')->implode(', '),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validatePayload($request, $post);

        $post->update([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'body' => $data['body'],
            'link_url' => $data['link_url'],
        ]);

        $post->syncTagsFromString($data['tags']);
        $this->syncMedia($request, $post, true);

        return redirect()
            ->route('admin.sossial.posts.edit', $post)
            ->with('success', 'Post guncellendi.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->load('media');
        $this->deletePostMedia($post);
        $post->delete();

        return redirect()
            ->route('admin.sossial.posts.index')
            ->with('success', 'Post silindi.');
    }

    protected function validatePayload(Request $request, ?Post $post = null): array
    {
        $mediaIds = $post?->media()->pluck('id')->all() ?? [];

        $data = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'type' => ['required', Rule::in(array_keys($this->typeLabels()))],
            'body' => ['required', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:500'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer', Rule::in($mediaIds)],
        ]);

        $newImageCount = count($request->file('images', [])) + ($request->hasFile('image') ? 1 : 0);
        $existingImageCount = $post?->media()->count() ?? 0;
        $removeCount = collect($data['remove_media'] ?? [])->filter()->count();

        if (($existingImageCount - $removeCount + $newImageCount) > 20) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => 'Bir post icin en fazla 20 resim ekleyebilirsiniz.',
            ]);
        }

        $data['body'] = trim((string) $data['body']);
        $data['tags'] = trim((string) ($data['tags'] ?? '')) ?: null;
        $data['link_url'] = trim((string) ($data['link_url'] ?? '')) ?: null;

        abort_if($data['body'] === '', 422, 'Post metni bos olamaz.');

        return $data;
    }

    protected function syncMedia(Request $request, Post $post, bool $canRemove = false): void
    {
        $post->loadMissing('media');

        if ($canRemove) {
            $removeIds = collect($request->input('remove_media', []))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values();

            if ($removeIds->isNotEmpty()) {
                $mediaToDelete = $post->media->whereIn('id', $removeIds);

                foreach ($mediaToDelete as $media) {
                    if ($media->path) {
                        Storage::disk('public')->delete($media->path);
                    }

                    $media->delete();
                }

                $post->unsetRelation('media');
                $post->load('media');
            }
        }

        $files = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $files[] = [
                    'file' => $file,
                    'error_key' => 'images.' . $index,
                ];
            }
        }

        if ($request->hasFile('image')) {
            $files[] = [
                'file' => $request->file('image'),
                'error_key' => 'image',
            ];
        }

        $sortOffset = (int) ($post->media->max('sort') ?? -1) + 1;

        foreach (array_values($files) as $index => $item) {
            $file = $item['file'] ?? null;

            if (! $file) {
                continue;
            }

            $path = WebpImageUploader::store(
                file: $file,
                directory: 'sosial/posts',
                disk: 'public',
                maxWidth: 1600,
                maxHeight: 1600,
                quality: 80,
                errorKey: $item['error_key'] ?? ('images.' . $index),
            );

            PostMedia::query()->create([
                'post_id' => $post->id,
                'type' => 'image',
                'path' => $path,
                'sort' => $sortOffset + $index,
            ]);
        }
    }

    protected function deletePostMedia(Post $post): void
    {
        foreach ($post->media as $media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
        }
    }

    protected function users()
    {
        return User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    protected function typeLabels(): array
    {
        return [
            'interview' => 'Mulakat deneyimi',
            'advice' => 'Kariyer tavsiyesi',
            'company' => 'Sirket / pozisyon deneyimi',
            'ilan' => 'Yazi paylasimi',
        ];
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('q', ''));
        $type = trim((string) $request->input('type', ''));

        $baseQuery = Post::query();
        $filteredQuery = Post::query()
            ->with(['user:id,name,email', 'tags:id,name,slug'])
            ->withCount(['comments', 'media'])
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('body', 'like', $like)
                        ->orWhere('link_url', 'like', $like)
                        ->orWhereHas('user', function ($query) use ($like) {
                            $query->where('name', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        })
                        ->orWhereHas('tags', function ($query) use ($like) {
                            $query->where('name', 'like', $like)
                                ->orWhere('slug', 'like', $like);
                        });
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            0 => 'id',
            5 => 'media_count',
            6 => 'comments_count',
            7 => 'created_at',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 7);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

        $posts = $filteredQuery
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $typeLabels = $this->typeLabels();

        $data = $posts->map(function (Post $post) use ($typeLabels): array {
            return [
                'id' => '#' . e((string) $post->id),
                'user' => sprintf(
                    '<div>%s</div><small class="text-muted">%s</small>',
                    e($post->user?->name ?? 'Silinmis kullanici'),
                    e($post->user?->email ?? '-')
                ),
                'type' => e($typeLabels[$post->type] ?? $post->type),
                'body' => nl2br(e(\Illuminate\Support\Str::limit($post->body, 180))),
                'tags' => e($post->tags->pluck('name')->implode(', ') ?: '-'),
                'media_count' => e((string) $post->media_count),
                'comments_count' => e((string) $post->comments_count),
                'created_at' => e(optional($post->created_at)->timezone('Europe/Istanbul')->format('d.m.Y H:i')),
                'actions' => sprintf(
                    '<div class="d-flex gap-1"><a href="%s" target="_blank" rel="noopener" class="btn btn-xs btn-info">Ac</a><a href="%s" class="btn btn-xs btn-primary">Duzenle</a><form method="POST" action="%s" onsubmit="return confirm(\'Bu post silinsin mi?\');">%s%s<button type="submit" class="btn btn-xs btn-danger">Sil</button></form></div>',
                    e(route('sosial.posts.show', $post)),
                    e(route('admin.sossial.posts.edit', $post)),
                    e(route('admin.sossial.posts.destroy', $post)),
                    csrf_field(),
                    method_field('DELETE')
                ),
            ];
        })->all();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
