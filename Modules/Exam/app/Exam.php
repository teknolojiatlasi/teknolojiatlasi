<?php

namespace Modules\Exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'menu_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the menu that owns the exam.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the questions for this exam.
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Get the exam sessions for this exam.
     */
    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    /**
     * Get the count of questions for this exam.
     */
    public function getQuestionCountAttribute()
    {
        return $this->questions()->count();
    }
}
