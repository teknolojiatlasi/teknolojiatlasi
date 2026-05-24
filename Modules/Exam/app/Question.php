<?php

namespace Modules\Exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_text',
        'image_path',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_answer',
        'explanation',
        'order',
    ];

    /**
     * Get the exam that owns the question.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user answers for this question.
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Get the options as an array.
     */
    public function getOptionsArray()
    {
        return [
            'A' => $this->option_a,
            'B' => $this->option_b,
            'C' => $this->option_c,
            'D' => $this->option_d,
            'E' => $this->option_e,
        ];
    }

    /**
     * Get the correct option text.
     */
    public function getCorrectOptionText()
    {
        $options = $this->getOptionsArray();
        return $options[$this->correct_answer] ?? null;
    }
}