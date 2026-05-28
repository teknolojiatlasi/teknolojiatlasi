<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WebPushService;
use App\Support\WebpImageUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\BlogImage;
use Modules\Blog\Support\BlogSocialSync;
use Modules\Media\Models\Media;

class BlogController extends Controller
{
    public function __construct(
        protected BlogSocialSync $blogSocialSync,
        protected WebPushService $webPushService,
    ) {
    }

    public function publicIndex(Request $request)
    {
        $selectedCategory = null;

        if ($request->filled('category')) {
            $selectedCategory = Cache::remember(
                'public.blog.category.' . md5((string) $request->query('category')),
                now()->addMinutes(10),
                fn () => BlogCategory::query()
                    ->where('slug', $request->string('category'))
                    ->orWhere('id', $request->input('category'))
                    ->first()
            );
        }

        $blogsQuery = Blog::with(['category', 'coverMedia'])->where('status', true);

        if ($selectedCategory) {
            $categoryIds = [$selectedCategory->id];

            if ($selectedCategory->parent_id === null) {
                $categoryIds = array_merge(
                    $categoryIds,
                    $selectedCategory->children()->pluck('id')->all(),
                );
            }

            $blogsQuery->whereIn('blog_category_id', $categoryIds);
        }

        $blogs = $blogsQuery->latest()->paginate(12)->withQueryString();

        $menus = Cache::remember('public.blog.menus.v1', now()->addMinutes(10), function () {
            return BlogCategory::query()
                ->whereNull('parent_id')
                ->withCount('blogs')
                ->with([
                    'children' => fn ($query) => $query
                        ->withCount('blogs')
                        ->orderBy('name'),
                ])
                ->orderBy('name')
                ->get();
        });

        return view('blog::public_index', compact('blogs', 'menus', 'selectedCategory'));
    }

    public function publicShow(Blog $blog)
    {
        if (! $blog->status) {
            abort(404);
        }

        $blog = Cache::remember("public.blog.show.{$blog->id}.v1", now()->addMinutes(10), function () use ($blog) {
            $blog->load(['category', 'images', 'coverMedia']);
            $blog->loadCount('comments');

            return $blog;
        });

        $comments = Cache::remember("public.blog.comments.{$blog->id}.v1", now()->addMinutes(5), function () use ($blog) {
            return $blog->comments()
                ->whereNull('parent_id')
                ->with('childrenRecursive')
                ->latest()
                ->get();
        });

        $latestBlogs = Cache::remember("public.blog.latest-sidebar.{$blog->id}.v1", now()->addMinutes(10), function () use ($blog) {
            return Blog::with(['category', 'coverMedia'])
                ->where('status', true)
                ->where('id', '!=', $blog->id)
                ->latest()
                ->take(3)
                ->get();
        });

        return view('blog::public_show', compact('blog', 'comments', 'latestBlogs'));
    }

    public function index(Request $request): \Illuminate\View\View|JsonResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        return view('blog::index');
    }

    public function show(Blog $blog)
    {
        $blog->load(['category', 'images', 'coverMedia']);
        $blog->loadCount('comments');

        $comments = $blog->comments()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->latest()
            ->get();

        $latestBlogs = Blog::with('category')
            ->where('status', true)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get();

        return view('blog::public_show', compact('blog', 'comments', 'latestBlogs'));
    }

    public function create()
    {
        $categories = BlogCategory::whereNull('parent_id')->with('children')->get();
        $mediaItems = Media::query()->latest()->take(200)->get();
        $mediaCollections = $this->mediaCollections();

        return view('blog::create', compact('categories', 'mediaItems', 'mediaCollections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $this->applyCoverImageData($request, $data);
        unset($data['cover_source'], $data['cover_media_preview_url'], $data['share_on_social']);

        $data['slug'] = Str::slug($data['title']);
        $data['status'] = 1;

        $blog = Blog::create($data);

        $this->storeGalleryImages($request, $blog);

        if ($request->boolean('share_on_social')) {
            $this->blogSocialSync->syncPost($blog->load('category'), (int) $request->user()->id);
        }

        $this->webPushService->sendNewBlogNotification($blog);

        return redirect()->route('blog.index')->with('success', 'Blog olusturuldu');
    }

    public function edit(Blog $blog)
    {
        $blog->load(['coverMedia'])->loadExists('socialPost');
        $categories = BlogCategory::whereNull('parent_id')->with('children')->get();
        $mediaItems = Media::query()->latest()->take(200)->get();
        $mediaCollections = $this->mediaCollections();

        return view('blog::edit', compact('blog', 'categories', 'mediaItems', 'mediaCollections'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate($this->rules());

        $this->applyCoverImageData($request, $data);
        unset($data['cover_source'], $data['cover_media_preview_url'], $data['share_on_social']);

        $data['slug'] = Str::slug($data['title']);

        $blog->update($data);

        $this->storeGalleryImages($request, $blog);

        if ($request->boolean('share_on_social')) {
            $this->blogSocialSync->syncPost($blog->fresh('category'), (int) $request->user()->id);
        } else {
            $this->blogSocialSync->deletePost($blog);
        }

        return redirect()
            ->route('blog.edit', $blog)
            ->with('success', 'Blog ve galeri basariyla guncellendi');
    }

    public function destroy(Blog $blog)
    {
        $blog->load('images');

        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        foreach ($blog->images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $this->blogSocialSync->deletePost($blog);
        $blog->delete();

        return back()->with('success', 'Blog silindi');
    }

    public function deleteImage(BlogImage $image)
    {
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Resim silindi');
    }

    protected function storeGalleryImages(Request $request, Blog $blog): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $index => $image) {
            BlogImage::create([
                'blog_id' => $blog->id,
                'image_path' => $this->storeBlogImage(
                    $image,
                    'blogs/images',
                    'images.' . $index,
                ),
            ]);
        }
    }

    protected function applyCoverImageData(Request $request, array &$data): void
    {
        if ($request->hasFile('cover_image')) {
            $media = $this->storeCoverMedia($request->file('cover_image'), $request->user()?->id);

            $data['cover_media_id'] = $media->id;
            $data['cover_image'] = $media->file_path;

            return;
        }

        if ($request->filled('cover_media_id')) {
            $media = Media::query()->findOrFail($request->integer('cover_media_id'));

            $data['cover_media_id'] = $media->id;
            $data['cover_image'] = $media->file_path;
        }
    }

    protected function storeCoverMedia(UploadedFile $file, ?int $userId): Media
    {
        $path = $this->storeBlogImage(
            $file,
            'uploads/covers',
            'cover_image',
        );

        return Media::create([
            'file_name' => basename($path),
            'file_path' => $path,
            'collection' => 'Kapak Resimleri',
            'mime_type' => Storage::disk('public')->mimeType($path) ?: $file->getMimeType(),
            'size' => Storage::disk('public')->size($path),
            'user_id' => $userId,
        ]);
    }

    protected function mediaCollections()
    {
        return Media::query()
            ->whereNotNull('collection')
            ->where('collection', '!=', '')
            ->distinct()
            ->orderBy('collection')
            ->pluck('collection');
    }

    protected function storeBlogImage(UploadedFile $file, string $directory, string $errorKey): string
    {
        return WebpImageUploader::store(
            file: $file,
            directory: $directory,
            disk: 'public',
            maxWidth: 1920,
            maxHeight: 1920,
            quality: 82,
            errorKey: $errorKey,
        );
    }

    protected function rules(): array
    {
        $imageRules = [
            'nullable',
            'file',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'extensions:jpg,jpeg,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:2048',
        ];

        return [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required',
            'share_on_social' => ['nullable', 'boolean'],
            'cover_source' => ['nullable', 'in:upload,library'],
            'cover_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'cover_media_preview_url' => ['nullable', 'string', 'max:2048'],
            'cover_image' => $imageRules,
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => $imageRules,
        ];
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('search.value', ''));

        $baseQuery = Blog::query();
        $filteredQuery = Blog::query()
            ->with('category:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhereHas('category', function ($query) use ($like) {
                            $query->where('name', 'like', $like);
                        });
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            0 => 'title',
            2 => 'status',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'title';

        $blogs = $filteredQuery
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $blogs->map(function (Blog $blog): array {
            $actions = sprintf(
                '<a href="%s" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Duzenle</a>
                <form action="%s" method="POST" class="d-inline" onsubmit="return confirm(\'Bu ilani silmek istiyor musunuz?\');">%s%s<button class="btn btn-sm btn-danger" type="submit"><i class="fa fa-trash"></i> Sil</button></form>',
                e(route('blog.edit', $blog)),
                e(route('blog.destroy', $blog)),
                csrf_field(),
                method_field('DELETE')
            );

            return [
                'title' => e($blog->title),
                'category' => e($blog->category->name ?? '-'),
                'status' => $blog->status
                    ? '<span class="badge bg-success">Yayinda</span>'
                    : '<span class="badge bg-secondary">Pasif</span>',
                'actions' => $actions,
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
