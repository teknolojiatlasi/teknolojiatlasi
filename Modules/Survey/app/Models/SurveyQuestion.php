<?php

namespace Modules\Survey\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    use HasFactory;

    public const TYPE_SINGLE_CHOICE = 'single_choice';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_TEXT = 'text';

    public const OPTIONABLE_TYPES = [
        self::TYPE_SINGLE_CHOICE,
        self::TYPE_MULTIPLE_CHOICE,
        self::TYPE_BOOLEAN,
    ];

    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'is_required',
        'help_text',
        'explanation',
        'config',
        'position',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'config' => 'array',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(SurveyOption::class)->orderBy('position');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'survey_question_id');
    }
}
