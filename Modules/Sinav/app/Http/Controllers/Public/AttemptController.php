<?php

namespace Modules\Sinav\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Sinav\Models\Attempt;

class AttemptController extends Controller
{
    public function index()
    {
        $attempts = Attempt::query()
            ->where('user_id', Auth::id())
            ->with('test.topic.lesson')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('sinav::public.attempts.index', compact('attempts'));
    }

    public function show(Attempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);

        $attempt->load([
            'test.topic.lesson',
            'answers.question' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $answersByQuestion = $attempt->answers->keyBy('question_id');

        return view('sinav::public.attempts.show', compact('attempt', 'answersByQuestion'));
    }
}

