<?php

namespace Modules\Sossial\Http\Controllers;

use App\Support\WebpImageUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\Sossial\Models\Comment;
use Modules\Sossial\Models\Post;
use Modules\Sossial\Models\PostMedia;
use Modules\Sossial\Models\Tag;

class PostController extends Controller
{
    public function create()
    {
        return view('sossial::posts.create');
    }

    public function edit(Request $request, Post $post)
    {
        abort_unless($request->user()->id === $post->user_id, 403);

        $post->load(['user', 'media', 'tags', 'blog']);

        return view('sossial::posts.edit', compact('post'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'media', 'tags', 'blog']);

        $isFollowing = false;
        if (auth()->check() && auth()->id() !== $post->user_id) {
            $isFollowing = \Modules\Sossial\Models\Follow::query()
                ->where('follower_id', auth()->id())
                ->where('following_id', $post->user_id)
                ->exists();
        }

        $comments = Comment::query()
            ->where('post_id', $post->id)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $post->loadCount('comments');

        $children = [];
        foreach ($comments as $comment) {
            $parentKey = $comment->parent_id ?: 0;
            $children[$parentKey][] = $comment;
        }

        $rootComments = $children[0] ?? [];

        $recentTags = Tag::query()
            ->select('sosial_tags.*')
            ->selectSub(function ($query) {
                $query->from('sosial_post_tag')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('sosial_post_tag.tag_id', 'sosial_tags.id');
            }, 'last_used_at')
            ->withCount('posts')
            ->whereHas('posts')
            ->orderByDesc('last_used_at')
            ->limit(8)
            ->get();

        $popularTags = Tag::query()
            ->withCount('posts')
            ->whereHas('posts')
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('sossial::posts.show', compact(
            'post',
            'rootComments',
            'children',
            'isFollowing',
            'recentTags',
            'popularTags',
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $dedupeKey = 'sosial:post-submit:' . sha1(implode('|', [
            (string) $request->user()->id,
            (string) ($data['type'] ?? ''),
            trim((string) ($data['body'] ?? '')),
            trim((string) ($data['tags'] ?? '')),
            trim((string) ($data['link_url'] ?? '')),
        ]));

        $existingPostId = Cache::get($dedupeKey);
        if ($existingPostId) {
            $existingPost = Post::query()->with(['user', 'media', 'tags'])->find($existingPostId);

            if ($existingPost) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => true,
                        'post_id' => $existingPost->id,
                        'html' => null,
                        'duplicate' => true,
                        'message' => 'Aynı paylaşım zaten oluşturuldu.',
                    ]);
                }

                return redirect()->route('sosial.posts.show', $existingPost);
            }
        }

        $post = Post::query()->create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'body' => $data['body'],
            'link_url' => $data['link_url'] ?? null,
        ]);

        $post->syncTagsFromString($data['tags'] ?? null);
        $this->syncMedia($request, $post);

        $post->load(['user', 'media', 'tags']);
        Cache::put($dedupeKey, $post->id, now()->addSeconds(15));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'post_id' => $post->id,
                'html' => view('sossial::partials.post-card', compact('post'))->render(),
            ]);
        }

        return redirect()->route('sosial.posts.show', $post);
    }

    public function update(Request $request, Post $post)
    {
        abort_unless($request->user()->id === $post->user_id, 403);

        $data = $this->validatePayload($request, $post);

        $post->update([
            'type' => $data['type'],
            'body' => $data['body'],
            'link_url' => $data['link_url'] ?? null,
        ]);

        $post->syncTagsFromString($data['tags'] ?? null);
        $this->syncMedia($request, $post, true);

        return redirect()
            ->route('sosial.posts.show', $post)
            ->with('status', 'Paylaşım güncellendi.');
    }

    public function destroy(Request $request, Post $post)
    {
        abort_unless($request->user()->id === $post->user_id, 403);

        $post->load('media');
        foreach ($post->media as $media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
        }

        $post->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('sosial.feed');
    }

    protected function validatePayload(Request $request, ?Post $post = null): array
    {
        $mediaIds = $post?->media()->pluck('id')->all() ?? [];

        $data = $request->validate([
            'type' => ['required', Rule::in(['interview', 'advice', 'company', 'ilan'])],
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
            $message = 'Bir post icin en fazla 20 resim ekleyebilirsiniz.';

            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => $message,
            ]);
        }

        $data['body'] = trim((string) $data['body']);
        abort_if($data['body'] === '', 422, 'Paylasim metni bos olamaz.');

        if (array_key_exists('tags', $data)) {
            $data['tags'] = trim((string) $data['tags']) ?: null;
        }

        if (array_key_exists('link_url', $data)) {
            $data['link_url'] = trim((string) $data['link_url']) ?: null;
        }

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
}
