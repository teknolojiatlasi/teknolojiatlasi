<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Simulation\Models\Simulation;
use Modules\Simulation\Models\SimulationCategory;

class DashboardController extends Controller
{
    public function index(): View
    {
        $simulationCategories = SimulationCategory::query()
            ->whereNull('parent_id')
            ->withCount([
                'simulations',
                'simulations as published_simulations_count' => fn ($query) => $query->published(),
            ])
            ->with([
                'children' => fn ($query) => $query
                    ->withCount([
                        'simulations',
                        'simulations as published_simulations_count' => fn ($simulationQuery) => $simulationQuery->published(),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(8)
            ->get();

        $simulationStats = [
            'categories' => SimulationCategory::query()->count(),
            'published' => Simulation::query()->published()->count(),
            'drafts' => Simulation::query()->where('status', 'draft')->count(),
        ];

        return view('admin.dashboard', compact('simulationCategories', 'simulationStats'));
    }
}
