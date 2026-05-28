<?php

namespace Modules\Survey\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Survey\Models\Survey;
use Modules\Survey\Models\SurveyAnswer;
use Modules\Survey\Models\SurveyQuestion;
use Modules\Survey\Models\SurveyResponse;

class SurveyResponseController extends Controller
{
    public function store(Survey $survey, Request $request)
    {
        abort_unless($survey->is_open, 422, 'Anket şu anda yanıt kabul etmiyor.');
        abort_unless($survey->is_public, 404);

        if (!$survey->allow_multiple_submissions && $request->user()) {
            $hasResponse = $survey->responses()
                ->where('respondent_id', $request->user()->getAuthIdentifier())
                ->where('respondent_type', $request->user()->getMorphClass())
                ->exists();

            if ($hasResponse) {
                return response()->json(['message' => 'Bu anketi zaten doldurdunuz.'], 429);
            }
        }

        if (!$survey->allow_multiple_submissions && !$request->user()) {
            $cookieKey = $this->submissionCookieKey($survey->id);
            if ($request->hasCookie($cookieKey)) {
                return response()->json(['message' => 'Bu anketi zaten doldurdunuz.'], 429);
            }
        }

        $validated = $request->validate([
            'participant_name' => 'nullable|string|max:255',
            'participant_email' => 'nullable|email|max:255',
            'answers' => 'required|array|min:1',
            'answers.*.survey_question_id' => 'required|integer|exists:survey_questions,id',
            'answers.*.option_id' => 'nullable|integer|exists:survey_options,id',
            'answers.*.option_ids' => 'array',
            'answers.*.option_ids.*' => 'integer|exists:survey_options,id',
            'answers.*.answer_text' => 'nullable|string',
        ]);

        $questions = $survey->questions()->with('options')->get()->keyBy('id');

        $response = DB::transaction(function () use ($validated, $questions, $survey, $request) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'respondent_id' => $request->user()?->getAuthIdentifier(),
                'respondent_type' => $request->user()?->getMorphClass(),
                'participant_name' => $validated['participant_name'] ?? $request->user()?->name,
                'participant_email' => $validated['participant_email'] ?? $request->user()?->email,
                'ip_address' => $request->ip(),
                'submitted_at' => now(),
            ]);

            foreach ($validated['answers'] as $answerData) {
                $question = $questions[$answerData['survey_question_id']] ?? null;

                if (!$question) {
                    throw ValidationException::withMessages([
                        'answers' => ['Bilinmeyen soru gönderildi.'],
                    ]);
                }

                $this->persistAnswer($question, $response, $answerData);
            }

            return $response;
        });
        $this->clearPublicCache($survey);

        $responsePayload = response()->json([
            'message' => 'Yanıtınız kaydedildi',
            'redirect' => route('survey.public.results', $survey),
            'response_id' => $response->id,
        ], 201);

        if (!$survey->allow_multiple_submissions && !$request->user()) {
            $cookieKey = $this->submissionCookieKey($survey->id);
            $responsePayload = $responsePayload->cookie(
                cookie()->make($cookieKey, (string) $response->id, 60 * 24 * 365)
            );
        }

        return $responsePayload;
    }

    protected function persistAnswer(SurveyQuestion $question, SurveyResponse $response, array $answerData): void
    {
        if (in_array($question->type, [SurveyQuestion::TYPE_SINGLE_CHOICE, SurveyQuestion::TYPE_BOOLEAN], true)) {
            $optionId = $answerData['option_id'] ?? null;
            $option = $question->options->firstWhere('id', $optionId);

            if (!$option) {
                if (!$question->is_required) {
                    return;
                }

                throw ValidationException::withMessages([
                    'answers' => ["{$question->question} için bir seçenek seçin."],
                ]);
            }

            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $question->id,
                'survey_option_id' => $option->id,
                'is_correct' => (bool) $option->is_correct,
            ]);

            return;
        }

        if ($question->type === SurveyQuestion::TYPE_MULTIPLE_CHOICE) {
            $optionIds = collect($answerData['option_ids'] ?? [])->filter()->values();

            if ($question->is_required && $optionIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'answers' => ["{$question->question} için en az bir seçenek seçin."],
                ]);
            }

            if (!$question->is_required && $optionIds->isEmpty()) {
                return;
            }

            foreach ($optionIds as $optionId) {
                $option = $question->options->firstWhere('id', $optionId);

                if (!$option) {
                    throw ValidationException::withMessages([
                        'answers' => ['Geçersiz bir seçenek gönderildi.'],
                    ]);
                }

                SurveyAnswer::create([
                    'survey_response_id' => $response->id,
                    'survey_question_id' => $question->id,
                    'survey_option_id' => $option->id,
                    'is_correct' => (bool) $option->is_correct,
                ]);
            }

            return;
        }

        $text = $answerData['answer_text'] ?? null;

        if ($question->is_required && blank($text)) {
            throw ValidationException::withMessages([
                'answers' => ["{$question->question} cevabı boş bırakılamaz."],
            ]);
        }

        if (!$question->is_required && blank($text)) {
            return;
        }

        SurveyAnswer::create([
            'survey_response_id' => $response->id,
            'survey_question_id' => $question->id,
            'answer_text' => $text,
        ]);
    }

    protected function submissionCookieKey(int $surveyId): string
    {
        return 'survey_submitted_'.$surveyId;
    }

    protected function clearPublicCache(Survey $survey): void
    {
        Cache::forget('public.home.active-survey.v1');
        Cache::forget('public.surveys.active.v1');
        Cache::forget("public.surveys.show.{$survey->id}.v1");
        Cache::forget("public.surveys.results.{$survey->id}.v1");
    }
}
