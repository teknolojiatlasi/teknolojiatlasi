<?php

namespace Modules\Sinav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $table = 'sinav_attempt_answers';

    public $timestamps = false;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option',
        'is_correct',
        'created_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}

