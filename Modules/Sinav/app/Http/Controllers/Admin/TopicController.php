<?php

namespace Modules\Sinav\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sinav\Models\Lesson;
use Modules\Sinav\Models\Topic;

class TopicController extends Controller
{
    public function index(Lesson $lesson)
    {
        $topics = $this->rootTopics($lesson->id);
        $allTopics = $this->allTopics($lesson->id);

        return view('sinav::admin.topics.index', compact('lesson', 'topics', 'allTopics'));
    }

    public function store(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:sinav_topics,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['parent_id'])) {
            $parent = Topic::query()->where('lesson_id', $lesson->id)->findOrFail($data['parent_id']);
            $data['parent_id'] = $parent->id;
        }

        $topic = Topic::create([
            'lesson_id' => $lesson->id,
            'parent_id' => $data['parent_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $lessonTopics = $this->rootTopics($lesson->id);
        $allTopics = $this->allTopics($lesson->id);

        return response()->json([
            'ok' => true,
            'id' => $topic->id,
            'tree_html' => view('sinav::admin.topics._tree', ['topics' => $lessonTopics])->render(),
            'parent_options_html' => view('sinav::admin.topics._parent_options', compact('allTopics'))->render(),
        ]);
    }

    public function update(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:sinav_topics,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['parent_id'])) {
            $parent = Topic::query()->where('lesson_id', $topic->lesson_id)->findOrFail($data['parent_id']);
            if ($parent->id === $topic->id) {
                return response()->json(['ok' => false, 'message' => 'Konu kendisine bağlanamaz.'], 422);
            }
            $data['parent_id'] = $parent->id;
        }

        $topic->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $lessonTopics = $this->rootTopics($topic->lesson_id);
        $allTopics = $this->allTopics($topic->lesson_id);

        return response()->json([
            'ok' => true,
            'id' => $topic->id,
            'tree_html' => view('sinav::admin.topics._tree', ['topics' => $lessonTopics])->render(),
            'parent_options_html' => view('sinav::admin.topics._parent_options', compact('allTopics'))->render(),
        ]);
    }

    public function destroy(Topic $topic)
    {
        try {
            $topic->delete();
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        $lessonTopics = $this->rootTopics($topic->lesson_id);
        $allTopics = $this->allTopics($topic->lesson_id);

        return response()->json([
            'ok' => true,
            'tree_html' => view('sinav::admin.topics._tree', ['topics' => $lessonTopics])->render(),
            'parent_options_html' => view('sinav::admin.topics._parent_options', compact('allTopics'))->render(),
        ]);
    }

    private function rootTopics(int $lessonId)
    {
        return Topic::query()
            ->where('lesson_id', $lessonId)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }

    private function allTopics(int $lessonId)
    {
        return Topic::query()
            ->where('lesson_id', $lessonId)
            ->orderBy('title')
            ->get();
    }
}
