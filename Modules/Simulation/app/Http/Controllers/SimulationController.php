<?php

namespace Modules\Simulation\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Routing\Controller;
use Modules\Simulation\Models\SimulationCategory;
use Modules\Simulation\Repositories\SimulationRepository;
use Modules\Simulation\Services\SimulationEditorService;

class SimulationController extends Controller
{
    public function __construct(
        private readonly SimulationRepository $simulations,
        private readonly SimulationEditorService $editorService,
    ) {
    }

    public function index()
    {
        return view('simulation::index', [
            'simulations' => $this->simulations->published(),
        ]);
    }

    public function category(SimulationCategory $category): View
    {
        $category->load(['childrenRecursive', 'parent.parent']);

        $rootCategories = SimulationCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->get();

        return view('simulation::category', [
            'category' => $category,
            'rootCategories' => $rootCategories,
            'categoryTrail' => $this->buildCategoryTrail($category),
            'simulations' => $this->simulations->publishedForCategoryTree($category),
        ]);
    }

    private function buildCategoryTrail(SimulationCategory $category): Collection
    {
        $trail = collect([$category]);
        $parent = $category->parent;

        while ($parent) {
            $trail->prepend($parent);
            $parent = $parent->parent;
        }

        return $trail->values();
    }

    public function show(string $slug)
    {
        $simulation = $this->simulations->findPublishedBySlug($slug);

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
}
