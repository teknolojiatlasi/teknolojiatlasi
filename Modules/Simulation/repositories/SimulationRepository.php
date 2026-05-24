<?php

namespace Modules\Simulation\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Simulation\Models\SimulationCategory;
use Modules\Simulation\Models\Simulation;

class SimulationRepository
{
    public function published(): Collection
    {
        return Simulation::query()
            ->with('category')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->get();
    }

    public function findPublishedBySlug(string $slug): Simulation
    {
        return Simulation::query()
            ->with(['category', 'media', 'versions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
    }

    public function publishedForCategoryTree(SimulationCategory $category): Collection
    {
        $category->loadMissing('childrenRecursive');
        $categoryIds = $this->collectCategoryTreeIds($category);

        return Simulation::query()
            ->with('category')
            ->published()
            ->whereIn('category_id', $categoryIds)
            ->orderByDesc('published_at')
            ->orderBy('title')
            ->get();
    }

    private function collectCategoryTreeIds(SimulationCategory $category): array
    {
        $ids = [$category->id];

        foreach ($category->childrenRecursive as $child) {
            $ids = [...$ids, ...$this->collectCategoryTreeIds($child)];
        }

        return array_values(array_unique($ids));
    }
}
