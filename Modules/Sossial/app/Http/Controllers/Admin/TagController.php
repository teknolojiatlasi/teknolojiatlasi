<?php

namespace Modules\Sossial\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Sossial\Models\Tag;

class TagController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        $q = trim((string) $request->query('q', ''));

        return view('sossial::admin.tags.index', [
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('sossial::admin.tags.create', [
            'tag' => new Tag(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);

        $tag = Tag::query()->create($data);

        return redirect()
            ->route('admin.sossial.tags.edit', $tag)
            ->with('success', 'Tag olusturuldu.');
    }

    public function edit(Tag $tag): View
    {
        $tag->loadCount('posts');

        return view('sossial::admin.tags.edit', [
            'tag' => $tag,
        ]);
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $data = $this->validatePayload($request, $tag);

        $tag->update($data);

        return redirect()
            ->route('admin.sossial.tags.edit', $tag->fresh())
            ->with('success', 'Tag guncellendi.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()
            ->route('admin.sossial.tags.index')
            ->with('success', 'Tag silindi.');
    }

    protected function validatePayload(Request $request, ?Tag $tag = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'slug' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('sosial_tags', 'slug')->ignore($tag?->id),
            ],
        ]);

        $data['name'] = trim((string) $data['name']);
        $data['slug'] = Str::slug(trim((string) ($data['slug'] ?? '')) ?: $data['name']);

        abort_if($data['name'] === '', 422, 'Tag adi bos olamaz.');
        abort_if($data['slug'] === '', 422, 'Gecerli bir slug olusturulamadi.');

        return $data;
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('q', ''));

        $baseQuery = Tag::query();
        $filteredQuery = Tag::query()
            ->withCount('posts')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('slug', 'like', $like);
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'slug',
            3 => 'posts_count',
            4 => 'created_at',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 1);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';

        $tags = $filteredQuery
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $tags->map(function (Tag $tag): array {
            return [
                'id' => '#' . e((string) $tag->id),
                'name' => e($tag->name),
                'slug' => e($tag->slug),
                'posts_count' => e((string) $tag->posts_count),
                'created_at' => e(optional($tag->created_at)->timezone('Europe/Istanbul')->format('d.m.Y H:i')),
                'actions' => sprintf(
                    '<div class="d-flex gap-1"><a href="%s" target="_blank" rel="noopener" class="btn btn-xs btn-info">Ac</a><a href="%s" class="btn btn-xs btn-primary">Duzenle</a><form method="POST" action="%s" onsubmit="return confirm(\'Bu etiket silinsin mi?\');">%s%s<button type="submit" class="btn btn-xs btn-danger">Sil</button></form></div>',
                    e(route('sosial.tags.show', $tag)),
                    e(route('admin.sossial.tags.edit', $tag)),
                    e(route('admin.sossial.tags.destroy', $tag)),
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
