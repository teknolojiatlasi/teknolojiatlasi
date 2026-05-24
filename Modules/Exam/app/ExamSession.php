<?php

namespace Modules\Exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'finished_at',
        'time_limit_minutes',
        'is_completed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Get the exam that owns the session.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the user answers for this session.
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Get the unanswered questions count.
     */
    public function getUnansweredQuestionsCount()
    {
        $answeredQuestionIds = $this->userAnswers()->pluck('question_id')->toArray();
        return $this->exam->questions()->whereNotIn('id', $answeredQuestionIds)->count();
    }

    /**
     * Get the answered questions count.
     */
    public function getAnsweredQuestionsCount()
    {
        return $this->userAnswers()->count();
    }

    /**
     * Get the correct answers count.
     */
    public function getCorrectAnswersCount()
    {
        return $this->userAnswers()->where('is_correct', true)->count();
    }

    /**
     * Get the score percentage.
     */
    public function getScorePercentage()
    {
        $totalQuestions = $this->exam->questions()->count();
        if ($totalQuestions === 0) {
            return 0;
        }

        $correctAnswers = $this->getCorrectAnswersCount();
        return round(($correctAnswers / $totalQuestions) * 100, 2);
    }

    /**
     * Check if the session is timed out.
     */
    public function isTimedOut()
    {
        if (!$this->started_at || !$this->time_limit_minutes) {
            return false;
        }

        $endTime = $this->started_at->copy()->addMinutes($this->time_limit_minutes);
        return now()->isPast($endTime);
    }
}