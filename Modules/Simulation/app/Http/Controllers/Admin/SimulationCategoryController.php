<?php

namespace Modules\Simulation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Simulation\Models\SimulationCategory;

class SimulationCategoryController extends Controller
{
    public function index()
    {
        $categories = $this->rootCategories();
        $allCategories = $this->allCategories();

        return view('simulation::admin.categories.index', compact('categories', 'allCategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:simulation_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = SimulationCategory::create([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return $this->successResponse($category->id);
    }

    public function update(Request $request, SimulationCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:simulation_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['parent_id'])) {
            if ((int) $data['parent_id'] === $category->id) {
                return response()->json(['ok' => false, 'message' => 'Kategori kendisine baglanamaz.'], 422);
            }

            $category->load('childrenRecursive');
            if ($category->descendantIds()->contains((int) $data['parent_id'])) {
                return response()->json(['ok' => false, 'message' => 'Kategori kendi alt dugumune baglanamaz.'], 422);
            }
        }

        $category->update([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name'], $category->id),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return $this->successResponse($category->id);
    }

    public function destroy(SimulationCategory $category)
    {
        if ($category->simulations()->exists()) {
            return response()->json(['ok' => false, 'message' => 'Bu kategoriye bagli simulasyonlar oldugu icin silinemez.'], 422);
        }

        try {
            $category->delete();
        } catch (\Throwable $exception) {
            return response()->json(['ok' => false, 'message' => $exception->getMessage()], 422);
        }

        return $this->successResponse();
    }

    private function successResponse(?int $id = null)
    {
        $categories = $this->rootCategories();
        $allCategories = $this->allCategories();

        return response()->json([
            'ok' => true,
            'id' => $id,
            'tree_html' => view('simulation::admin.categories._tree', compact('categories'))->render(),
            'parent_options_html' => view('simulation::admin.categories._parent_options', compact('allCategories'))->render(),
        ]);
    }

    private function rootCategories()
    {
        return SimulationCategory::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function allCategories()
    {
        return SimulationCategory::query()
            ->with('childrenRecursive')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'kategori';
        $counter = 1;

        while (
            SimulationCategory::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
