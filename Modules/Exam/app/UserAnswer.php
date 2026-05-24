<?php

namespace Modules\Exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'question_id',
        'selected_answer',
        'is_correct',
    ];

    /**
     * Get the exam session that owns the answer.
     */
    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    /**
     * Get the question that owns the answer.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Check if the answer is correct.
     */
    public function checkAnswer()
    {
        return $this->selected_answer === $this->question->correct_answer;
    }
}