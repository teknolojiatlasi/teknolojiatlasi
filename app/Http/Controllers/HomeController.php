<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogCategory;
use Modules\Sinav\Models\Lesson;
use Modules\Simulation\Models\Simulation;
use Modules\Simulation\Models\SimulationCategory;
use Modules\Survey\Models\Survey;
use Modules\Survey\Services\SurveyResultService;

class HomeController extends Controller
{
    public function index(Request $request, SurveyResultService $results): View
    {
        $now = now();

        $homeData = Cache::remember('public.home.data.v4', now()->addMinutes(5), function () {
            $baseQuery = Blog::query()
                ->with(['category', 'images'])
                ->where('status', true)
                ->latest();

            $latestBlog = (clone $baseQuery)->first();

            $secondaryQuery = Blog::query()
                ->with(['category', 'images'])
                ->where('status', true)
                ->latest();

            if ($latestBlog) {
                $secondaryQuery->whereKeyNot($latestBlog->id);
            }

            return [
                'latestBlog' => $latestBlog,
                'carouselBlogs' => (clone $baseQuery)->take(8)->get(),
                'subBlogs' => (clone $secondaryQuery)->take(3)->get(),
                'recentBlogs' => (clone $baseQuery)->take(4)->get(),
                'blogs' => (clone $secondaryQuery)->take(6)->get(),
                'latestSimulations' => Simulation::query()
                    ->with('category')
                    ->published()
                    ->orderByDesc('published_at')
                    ->orderByDesc('created_at')
                    ->take(8)
                    ->get(),
                'menus' => BlogCategory::query()
                    ->whereNull('parent_id')
                    ->withCount('blogs')
                    ->with([
                        'children' => fn ($query) => $query
                            ->withCount('blogs')
                            ->orderBy('name'),
                    ])
                    ->orderBy('name')
                    ->get(),
                'lessons' => Lesson::query()
                    ->with([
                        'topics' => fn ($query) => $query
                            ->where('is_active', true)
                            ->whereNull('parent_id')
                            ->withCount([
                                'tests' => fn ($testQuery) => $testQuery->where('is_active', true),
                            ])
                            ->orderBy('sort_order'),
                    ])
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),
                'simulationCategories' => SimulationCategory::query()
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->withCount([
                        'simulations' => fn ($query) => $query->published(),
                    ])
                    ->with([
                        'children' => fn ($query) => $query
                            ->where('is_active', true)
                            ->withCount([
                                'simulations' => fn ($simulationQuery) => $simulationQuery->published(),
                            ])
                            ->orderBy('sort_order')
                            ->orderBy('name'),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->take(8)
                    ->get(),
            ];
        });

        $activeSurvey = Cache::remember('public.home.active-survey.v1', now()->addMinutes(3), function () use ($now) {
            return Survey::with(['questions.options', 'responses'])
                ->where('is_public', true)
                ->where('is_active', true)
                ->where(function ($query) use ($now) {
                    $query->whereNull('opens_at')->orWhere('opens_at', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('closes_at')->orWhere('closes_at', '>=', $now);
                })
                ->latest()
                ->first();
        });

        $activeStats = null;
        $activeResponseCount = 0;

        if ($activeSurvey) {
            $resultPayload = $results->build($activeSurvey);
            $activeStats = $resultPayload['questionStats'];
            $activeResponseCount = $resultPayload['responseCount'];
        }

        return view('welcome', compact(
            'activeSurvey',
            'activeStats',
            'activeResponseCount',
        ) + $homeData);
    }
}
