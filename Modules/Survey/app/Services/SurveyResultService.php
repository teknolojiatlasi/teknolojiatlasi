<?php

namespace Modules\Survey\Services;

use Illuminate\Support\Facades\DB;
use Modules\Survey\Models\Survey;
use Modules\Survey\Models\SurveyAnswer;
use Modules\Survey\Models\SurveyOption;
use Modules\Survey\Models\SurveyQuestion;

class SurveyResultService
{
    public function build(Survey $survey): array
    {
        $questionIds = $survey->questions->pluck('id');

        $answerCounts = SurveyAnswer::select(
            'survey_question_id',
            'survey_option_id',
            DB::raw('count(*) as total')
        )
            ->whereIn('survey_question_id', $questionIds)
            ->groupBy('survey_question_id', 'survey_option_id')
            ->get()
            ->groupBy('survey_question_id');

        $questionStats = $survey->questions->map(function (SurveyQuestion $question) use ($answerCounts) {
            $optionStats = $question->options->map(function (SurveyOption $option) use ($answerCounts, $question) {
                $counts = $answerCounts[$question->id] ?? collect();
                $count = optional($counts->firstWhere('survey_option_id', $option->id))->total ?? 0;

                return [
                    'id' => $option->id,
                    'label' => $option->label,
                    'count' => $count,
                ];
            });

            $textAnswers = $question->type === SurveyQuestion::TYPE_TEXT
                ? $question->answers()->latest()->limit(25)->get(['answer_text', 'created_at'])
                : collect();

            return [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'is_required' => $question->is_required,
                'options' => $optionStats,
                'textAnswers' => $textAnswers,
                'totalAnswers' => $question->answers()->count(),
            ];
        });

        return [
            'survey' => $survey,
            'questionStats' => $questionStats,
            'responseCount' => $survey->responses()->count(),
        ];
    }
}
