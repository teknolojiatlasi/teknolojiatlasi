<?php

namespace Modules\Sinav\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Sinav\Models\Lesson;
use Modules\Sinav\Models\Test;
use Modules\Sinav\Models\Topic;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Cache::remember('public.sinav.lessons.index.v1', now()->addMinutes(10), function () {
            return Lesson::query()
                ->where('is_active', true)
                ->withCount([
                    'topics' => fn ($query) => $query->where('is_active', true),
                ])
                ->with([
                    'topics' => fn ($query) => $query
                        ->where('is_active', true)
                        ->whereNull('parent_id')
                        ->withCount([
                            'tests' => fn ($testQuery) => $testQuery->where('is_active', true),
                        ])
                        ->orderBy('sort_order'),
                ])
                ->orderBy('name')
                ->get();
        });

        return view('sinav::public.lessons.index', compact('lessons'));
    }

    public function show(Request $request, Lesson $lesson)
    {
        abort_unless($lesson->is_active, 404);

        $topics = Cache::remember("public.sinav.lesson.{$lesson->id}.topics.v1", now()->addMinutes(10), function () use ($lesson) {
            return Topic::query()
                ->where('lesson_id', $lesson->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->with([
                    'tests' => fn ($q) => $q
                        ->where('is_active', true)
                        ->withCount(['questions' => fn ($q) => $q->where('is_active', true)]),
                ])
                ->get();
        });

        $topicTree = $this->buildTopicTree($topics);

        $activeTest = null;
        $activeTestId = $request->integer('test_id');
        if ($activeTestId) {
            $activeTest = Cache::remember("public.sinav.test.{$activeTestId}.show.v1", now()->addMinutes(10), function () use ($activeTestId, $lesson) {
                return Test::query()
                    ->whereKey($activeTestId)
                    ->where('is_active', true)
                    ->whereHas('topic', fn ($q) => $q
                        ->where('lesson_id', $lesson->id)
                        ->where('is_active', true))
                    ->with([
                        'topic.lesson',
                        'questions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                    ])
                    ->first();
            });

            abort_unless($activeTest, 404);

            $sessionKey = "sinav.test.{$activeTest->id}.started_at";
            if (!$request->session()->has($sessionKey)) {
                $request->session()->put($sessionKey, CarbonImmutable::now()->toIso8601String());
            }
        }

        return view('sinav::public.lessons.show', compact('lesson', 'topicTree', 'activeTest', 'activeTestId'));
    }

    private function buildTopicTree($topics): array
    {
        $ids = $topics->pluck('id')->all();
        $ids = array_fill_keys($ids, true);

        $byParent = [];
        foreach ($topics as $topic) {
            $parentId = $topic->parent_id;
            if ($parentId && !isset($ids[$parentId])) {
                $parentId = null;
            }

            $byParent[$parentId ?? 0][] = $topic;
        }

        $make = function ($parentId) use (&$make, $byParent) {
            $items = $byParent[$parentId] ?? [];
            return array_map(function ($topic) use (&$make) {
                return [
                    'topic' => $topic,
                    'children' => $make($topic->id),
                ];
            }, $items);
        };

        return $make(0);
    }
}
