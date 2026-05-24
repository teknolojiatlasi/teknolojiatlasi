<?php

namespace Modules\Sinav\Http\Controllers\Admin;

use App\Support\WebpImageUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Sinav\Models\Question;
use Modules\Sinav\Models\Test;

class QuestionController extends Controller
{
    public function index(Test $test)
    {
        $test->load('topic.lesson');
        $questions = Question::query()->where('test_id', $test->id)->orderBy('sort_order')->get();

        return view('sinav::admin.questions.index', compact('test', 'questions'));
    }

    public function store(Request $request, Test $test)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'image' => $this->imageRules(),
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'option_e' => ['required', 'string'],
            'correct_option' => ['required', 'in:A,B,C,D,E'],
            'explanation' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $this->storeQuestionImage($request->file('image'), 'image')
            : null;

        $question = Question::create([
            'test_id' => $test->id,
            'topic_id' => $test->topic_id,
            'question_text' => $data['question_text'],
            'image_path' => $imagePath,
            'option_a' => $data['option_a'],
            'option_b' => $data['option_b'],
            'option_c' => $data['option_c'],
            'option_d' => $data['option_d'],
            'option_e' => $data['option_e'],
            'correct_option' => $data['correct_option'],
            'explanation' => $data['explanation'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $question->id,
            'html' => view('sinav::admin.questions._row', compact('question'))->render(),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'image' => $this->imageRules(),
            'remove_image' => ['nullable', 'boolean'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'option_e' => ['required', 'string'],
            'correct_option' => ['required', 'in:A,B,C,D,E'],
            'explanation' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $question->image_path;
        $removeImage = (bool) ($data['remove_image'] ?? false);

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $this->storeQuestionImage($request->file('image'), 'image');
        } elseif ($removeImage && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        $question->update([
            'question_text' => $data['question_text'],
            'image_path' => $imagePath,
            'option_a' => $data['option_a'],
            'option_b' => $data['option_b'],
            'option_c' => $data['option_c'],
            'option_d' => $data['option_d'],
            'option_e' => $data['option_e'],
            'correct_option' => $data['correct_option'],
            'explanation' => $data['explanation'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $question->id,
            'html' => view('sinav::admin.questions._row', compact('question'))->render(),
        ]);
    }

    public function destroy(Question $question)
    {
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();
        return response()->json(['ok' => true]);
    }

    protected function imageRules(): array
    {
        return [
            'nullable',
            'file',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'extensions:jpg,jpeg,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:4096',
        ];
    }

    protected function storeQuestionImage(UploadedFile $file, string $errorKey): string
    {
        return WebpImageUploader::store(
            file: $file,
            directory: 'sinav/questions',
            disk: 'public',
            maxWidth: 1920,
            maxHeight: 1920,
            quality: 82,
            errorKey: $errorKey,
        );
    }
}
