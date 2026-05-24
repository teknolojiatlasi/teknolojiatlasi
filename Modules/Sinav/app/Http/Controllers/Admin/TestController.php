<?php

namespace Modules\Sinav\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sinav\Models\Test;
use Modules\Sinav\Models\Topic;

class TestController extends Controller
{
    public function index(Topic $topic)
    {
        $topic->load('lesson');
        $tests = Test::query()->where('topic_id', $topic->id)->orderBy('created_at', 'desc')->get();

        return view('sinav::admin.tests.index', compact('topic', 'tests'));
    }

    public function store(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $test = Test::create([
            'topic_id' => $topic->id,
            'title' => $data['title'],
            'duration_minutes' => $data['duration_minutes'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $test->id,
            'html' => view('sinav::admin.tests._row', compact('test'))->render(),
        ]);
    }

    public function update(Request $request, Test $test)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $test->update([
            'title' => $data['title'],
            'duration_minutes' => $data['duration_minutes'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $test->id,
            'html' => view('sinav::admin.tests._row', compact('test'))->render(),
        ]);
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return response()->json(['ok' => true]);
    }
}

