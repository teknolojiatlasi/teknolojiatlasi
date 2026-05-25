<?php

namespace Modules\Simulation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\WebpImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Simulation\Models\Simulation;
use Modules\Simulation\Models\SimulationCategory;
use Modules\Simulation\Services\SimulationEditorService;

class SimulationAdminController extends Controller
{
    public function __construct(
        private readonly SimulationEditorService $editorService
    ) {
    }

    public function index()
    {
        $simulations = Simulation::query()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('simulation::admin.simulations.index', compact('simulations'));
    }

    public function create()
    {
        return view('simulation::admin.simulations.create', [
            'simulation' => new Simulation(['content_type' => 'html', 'status' => 'draft']),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $slug = $this->uniqueSlug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeCoverImage($request->file('cover_image'), 'cover_image');
        }

        $simulation = Simulation::create([
            'category_id' => $data['category_id'] ?? null,
            'topic_path' => $data['topic_path'] ?? null,
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'content_type' => $data['content_type'],
            'html_code' => $data['html_code'] ?? null,
            'css_code' => $data['css_code'] ?? null,
            'js_code' => $data['js_code'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'video_source' => $data['video_source'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'status' => $data['status'],
            'published_at' => $data['published_at'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
        ]);

        $this->syncVersion($simulation, $data, $request);

        return redirect()
            ->route('simulation.admin.simulations.index')
            ->with('success', 'Simulasyon olusturuldu.');
    }

    public function edit(Simulation $simulation)
    {
        return view('simulation::admin.simulations.edit', [
            'simulation' => $simulation,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function show(Simulation $simulation)
    {
        $simulation->load(['category', 'media', 'versions']);

        if ($simulation->content_type === 'html') {
            return response(
                $this->editorService->buildPreviewDocument(
                    (string) ($simulation->html_code ?? ''),
                    (string) ($simulation->css_code ?? ''),
                    (string) ($simulation->js_code ?? ''),
                ),
                200,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return view('simulation::show', [
            'simulation' => $simulation,
        ]);
    }

    public function update(Request $request, Simulation $simulation)
    {
        $data = $request->validate($this->rules($simulation));

        if ($request->hasFile('cover_image')) {
            if ($simulation->cover_image) {
                Storage::disk('public')->delete($simulation->cover_image);
            }

            $data['cover_image'] = $this->storeCoverImage($request->file('cover_image'), 'cover_image');
        }

        $simulation->update([
            'category_id' => $data['category_id'] ?? null,
            'topic_path' => $data['topic_path'] ?? null,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title'], $simulation->id),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'content_type' => $data['content_type'],
            'html_code' => $data['html_code'] ?? null,
            'css_code' => $data['css_code'] ?? null,
            'js_code' => $data['js_code'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'video_source' => $data['video_source'] ?? null,
            'cover_image' => $data['cover_image'] ?? $simulation->cover_image,
            'status' => $data['status'],
            'published_at' => $data['published_at'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
        ]);

        $this->syncVersion($simulation, $data, $request);

        return redirect()
            ->route('simulation.admin.simulations.edit', $simulation)
            ->with('success', 'Simulasyon guncellendi.');
    }

    public function destroy(Simulation $simulation)
    {
        if ($simulation->cover_image) {
            Storage::disk('public')->delete($simulation->cover_image);
        }

        foreach ($simulation->media as $media) {
            if ($media->disk && $media->path) {
                Storage::disk($media->disk)->delete($media->path);
            }
        }

        $simulation->delete();

        return redirect()
            ->route('simulation.admin.simulations.index')
            ->with('success', 'Simulasyon silindi.');
    }

    private function rules(?Simulation $simulation = null): array
    {
        $statuses = config('simulation.statuses', ['draft', 'scheduled', 'published', 'archived']);
        $contentTypes = config('simulation.content_types', ['html', 'video', 'image']);
        $videoSources = config('simulation.video_sources', ['upload', 'youtube', 'vimeo']);

        $imageRules = [
            'nullable',
            'file',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'extensions:jpg,jpeg,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:4096',
        ];

        return [
            'category_id' => ['nullable', 'integer', 'exists:simulation_categories,id'],
            'topic_path' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'content_type' => ['required', Rule::in($contentTypes)],
            'html_code' => ['nullable', 'string'],
            'css_code' => ['nullable', 'string'],
            'js_code' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'video_source' => ['nullable', Rule::in($videoSources)],
            'cover_image' => $imageRules,
            'status' => ['required', Rule::in($statuses)],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'change_note' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function categoryOptions(): array
    {
        $roots = SimulationCategory::query()
            ->with('childrenRecursive')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $options = [];
        foreach ($roots as $category) {
            $this->appendCategoryOption($options, $category);
        }

        return $options;
    }

    private function appendCategoryOption(array &$options, SimulationCategory $category, int $level = 0): void
    {
        $options[$category->id] = $category->flattenedLabel($level);

        foreach ($category->childrenRecursive as $child) {
            $this->appendCategoryOption($options, $child, $level + 1);
        }
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base !== '' ? $base : 'simulasyon';
        $counter = 1;

        while (
            Simulation::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function storeCoverImage(UploadedFile $file, string $errorKey): string
    {
        return WebpImageUploader::store(
            file: $file,
            directory: 'simulations/covers',
            disk: 'public',
            maxWidth: 1920,
            maxHeight: 1920,
            quality: 82,
            errorKey: $errorKey,
        );
    }

    private function syncVersion(Simulation $simulation, array $data, Request $request): void
    {
        if (($data['content_type'] ?? null) !== 'html') {
            return;
        }

        $current = [
            'html_code' => (string) ($simulation->html_code ?? ''),
            'css_code' => (string) ($simulation->css_code ?? ''),
            'js_code' => (string) ($simulation->js_code ?? ''),
        ];

        $latest = $simulation->versions()->first();

        if ($latest !== null && $latest->html_code === $current['html_code'] && $latest->css_code === $current['css_code'] && $latest->js_code === $current['js_code']) {
            return;
        }

        $this->editorService->createVersion($simulation, [
            'html_code' => $current['html_code'],
            'css_code' => $current['css_code'],
            'js_code' => $current['js_code'],
            'change_note' => $data['change_note'] ?? null,
        ], (int) optional($request->user())->id);
    }
}
