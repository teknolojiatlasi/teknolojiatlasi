<?php

namespace Modules\Survey\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Survey\Models\Survey;
use Modules\Survey\Models\SurveyQuestion;
use Modules\Survey\Services\SurveyResultService;

class SurveyController extends Controller
{
    public function __construct(private readonly SurveyResultService $results)
    {
    }

    public function index()
    {
        $surveys = Survey::withCount(['questions', 'responses'])->latest()->paginate(10);

        $activeSurvey = Survey::with(['questions.options', 'responses'])
            ->where('is_active', true)
            ->latest()
            ->first();

        $activeStats = null;
        $activeResponseCount = 0;

        if ($activeSurvey) {
            $resultPayload = $this->results->build($activeSurvey);
            $activeStats = $resultPayload['questionStats'];
            $activeResponseCount = $resultPayload['responseCount'];
        }

        return view('survey::index', compact('surveys', 'activeSurvey', 'activeStats', 'activeResponseCount'));
    }

    public function create()
    {
        $survey = new Survey([
            'is_active' => false,
            'is_public' => true,
            'allow_multiple_submissions' => false,
        ]);

        $survey->setRelation('questions', collect());

        return view('survey::edit', compact('survey'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSurvey($request);

        $survey = DB::transaction(function () use ($validated) {
            $payload = $this->mapSurveyPayload($validated);

            $survey = Survey::create($payload);

            $this->syncQuestions($survey, $validated['questions'] ?? []);

            return $survey;
        });

        return response()->json([
            'message' => 'Anket oluşturuldu',
            'survey' => $survey->fresh(['questions.options']),
            'redirect' => route('survey.edit', $survey),
        ], 201);
    }

    public function edit(Survey $survey)
    {
        $survey->load(['questions.options', 'responses']);

        return view('survey::edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $this->validateSurvey($request);

        $survey = DB::transaction(function () use ($validated, $survey) {
            $survey->update($this->mapSurveyPayload($validated, $survey));

            $this->syncQuestions($survey, $validated['questions'] ?? []);

            return $survey->fresh(['questions.options']);
        });

        return response()->json([
            'message' => 'Anket güncellendi',
            'survey' => $survey,
        ]);
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return response()->json([
            'message' => 'Anket silindi',
        ]);
    }

    public function results(Survey $survey)
    {
        $survey->load(['questions.options', 'responses']);
        $resultData = $this->results->build($survey);

        return view('survey::results', $resultData);
    }

    protected function validateSurvey(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'allow_multiple_submissions' => 'nullable|boolean',
            'opens_at' => 'nullable|date',
            'closes_at' => 'nullable|date|after_or_equal:opens_at',
            'questions' => 'array',
            'questions.*.id' => 'nullable|integer|exists:survey_questions,id',
            'questions.*.question' => 'required|string|max:1000',
            'questions.*.type' => 'required|in:single_choice,multiple_choice,boolean,text',
            'questions.*.is_required' => 'nullable|boolean',
            'questions.*.help_text' => 'nullable|string',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'array',
            'questions.*.options.*.id' => 'nullable|integer|exists:survey_options,id',
            'questions.*.options.*.label' => 'required_with:questions.*.options|string|max:255',
            'questions.*.options.*.value' => 'nullable|string|max:255',
            'questions.*.options.*.is_correct' => 'nullable|boolean',
        ]);
    }

    protected function mapSurveyPayload(array $validated, ?Survey $survey = null): array
    {
        $slug = Str::slug($validated['title']);

        if (!$survey || $survey->slug !== $slug) {
            $uniqueSlug = $slug;
            $suffix = 1;

            while (Survey::where('slug', $uniqueSlug)->when($survey, fn ($q) => $q->where('id', '!=', $survey->id))->exists()) {
                $uniqueSlug = $slug.'-'.$suffix;
                $suffix++;
            }

            $slug = $uniqueSlug;
        }

        return [
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'is_public' => (bool) ($validated['is_public'] ?? true),
            'allow_multiple_submissions' => (bool) ($validated['allow_multiple_submissions'] ?? false),
            'opens_at' => $validated['opens_at'] ?? null,
            'closes_at' => $validated['closes_at'] ?? null,
            'settings' => [],
        ];
    }

    protected function syncQuestions(Survey $survey, array $questions): void
    {
        $questionIds = [];

        foreach ($questions as $index => $questionData) {
            $questionPayload = [
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'is_required' => (bool) ($questionData['is_required'] ?? false),
                'help_text' => $questionData['help_text'] ?? null,
                'explanation' => $questionData['explanation'] ?? null,
                'config' => $questionData['config'] ?? [],
                'position' => $index + 1,
            ];

            $question = isset($questionData['id'])
                ? tap($survey->questions()->where('id', $questionData['id'])->firstOrFail())
                    ->update($questionPayload)
                : $survey->questions()->create($questionPayload);

            $questionIds[] = $question->id;

            $this->syncOptions($question, $questionData['options'] ?? []);
        }

        if (!empty($questionIds)) {
            $survey->questions()->whereNotIn('id', $questionIds)->delete();
        } else {
            $survey->questions()->delete();
        }
    }

    protected function syncOptions(SurveyQuestion $question, array $options): void
    {
        if (!in_array($question->type, SurveyQuestion::OPTIONABLE_TYPES, true)) {
            $question->options()->delete();
            return;
        }

        if (empty($options) && $question->type === SurveyQuestion::TYPE_BOOLEAN) {
            $options = [
                ['label' => 'Doğru', 'value' => 'true', 'is_correct' => false],
                ['label' => 'Yanlış', 'value' => 'false', 'is_correct' => false],
            ];
        }

        $optionIds = [];

        foreach ($options as $idx => $optionData) {
            $payload = [
                'label' => $optionData['label'] ?? $optionData['value'] ?? 'Seçenek '.($idx + 1),
                'value' => $optionData['value'] ?? $optionData['label'] ?? null,
                'is_correct' => (bool) ($optionData['is_correct'] ?? false),
                'position' => $idx + 1,
            ];

            $option = isset($optionData['id'])
                ? tap($question->options()->where('id', $optionData['id'])->firstOrFail())
                    ->update($payload)
                : $question->options()->create($payload);

            $optionIds[] = $option->id;
        }

        if (!empty($optionIds)) {
            $question->options()->whereNotIn('id', $optionIds)->delete();
        } else {
            $question->options()->delete();
        }
    }
}
