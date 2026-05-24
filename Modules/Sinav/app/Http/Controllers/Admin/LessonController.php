<?php

namespace Modules\Sinav\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sinav\Models\Lesson;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::query()->orderBy('created_at', 'desc')->get();

        return view('sinav::admin.lessons.index', compact('lessons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $lesson = Lesson::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $lesson->id,
                'html' => view('sinav::admin.lessons._row', compact('lesson'))->render(),
            ]);
        }

        return redirect()->route('sinav.admin.lessons.index');
    }

    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $lesson->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $lesson->id,
                'html' => view('sinav::admin.lessons._row', compact('lesson'))->render(),
            ]);
        }

        return redirect()->route('sinav.admin.lessons.index');
    }

    public function destroy(Request $request, Lesson $lesson)
    {
        $lesson->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('sinav.admin.lessons.index');
    }
}

