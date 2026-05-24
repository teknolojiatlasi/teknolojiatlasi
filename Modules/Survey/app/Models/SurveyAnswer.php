<?php

namespace Modules\Survey\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_response_id',
        'survey_question_id',
        'survey_option_id',
        'answer_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SurveyOption::class, 'survey_option_id');
    }
}
