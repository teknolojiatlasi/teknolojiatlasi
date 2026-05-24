<?php

namespace Modules\Sinav\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Sinav\Models\Attempt;
use Modules\Sinav\Models\AttemptAnswer;
use Modules\Sinav\Models\Question;
use Modules\Sinav\Models\Test;

class TestController extends Controller
{
    public function show(Request $request, Test $test)
    {
        abort_unless($test->is_active, 404);

        $test = Cache::remember("public.sinav.test.{$test->id}.solve.v1", now()->addMinutes(10), function () use ($test) {
            $test->load([
                'topic.lesson',
                'questions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ]);

            return $test;
        });

        $sessionKey = "sinav.test.{$test->id}.started_at";
        if (!$request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, CarbonImmutable::now()->toIso8601String());
        }

        return view('sinav::public.tests.solve', compact('test'));
    }

    public function submit(Request $request, Test $test)
    {
        abort_unless($test->is_active, 404);

        $test->load([
            'topic.lesson',
            'questions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ]);

        $answers = $request->input('answers', []);

        $correct = 0;
        $wrong = 0;
        $blank = 0;

        $computed = [];
        foreach ($test->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            $selected = is_string($selected) ? strtoupper(trim($selected)) : null;
            if (!in_array($selected, ['A', 'B', 'C', 'D', 'E'], true)) {
                $selected = null;
            }

            $isCorrect = $selected !== null && $selected === $question->correct_option;

            if ($selected === null) {
                $blank++;
            } elseif ($isCorrect) {
                $correct++;
            } else {
                $wrong++;
            }

            $computed[] = [
                'question' => $question,
                'selected' => $selected,
                'is_correct' => $isCorrect,
            ];
        }

        $total = max(1, $test->questions->count());
        $scorePercent = (int) round(($correct / $total) * 100);

        $startedAt = $this->pullStartedAt($request, $test);
        $finishedAt = CarbonImmutable::now();
        $durationSeconds = max(0, $finishedAt->diffInSeconds($startedAt));

        $attempt = null;
        if (Auth::check()) {
            $attempt = Attempt::create([
                'user_id' => Auth::id(),
                'test_id' => $test->id,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
                'duration_seconds' => $durationSeconds,
                'correct_count' => $correct,
                'wrong_count' => $wrong,
                'blank_count' => $blank,
                'score_percent' => $scorePercent,
            ]);

            $now = now();
            $rows = [];
            foreach ($computed as $item) {
                /** @var Question $question */
                $question = $item['question'];
                $rows[] = [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option' => $item['selected'],
                    'is_correct' => $item['is_correct'],
                    'created_at' => $now,
                ];
            }
            AttemptAnswer::insert($rows);
        }

        return view('sinav::public.tests.result', [
            'test' => $test,
            'attempt' => $attempt,
            'computed' => $computed,
            'correct' => $correct,
            'wrong' => $wrong,
            'blank' => $blank,
            'scorePercent' => $scorePercent,
        ]);
    }

    private function pullStartedAt(Request $request, Test $test): CarbonImmutable
    {
        $sessionKey = "sinav.test.{$test->id}.started_at";
        $value = $request->session()->pull($sessionKey);
        if (!$value) {
            return CarbonImmutable::now();
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return CarbonImmutable::now();
        }
    }
}
