<?php

namespace Modules\Sinav\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    use HasFactory;

    protected $table = 'sinav_attempts';

    protected $fillable = [
        'user_id',
        'test_id',
        'started_at',
        'finished_at',
        'duration_seconds',
        'correct_count',
        'wrong_count',
        'blank_count',
        'score_percent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_seconds' => 'integer',
        'correct_count' => 'integer',
        'wrong_count' => 'integer',
        'blank_count' => 'integer',
        'score_percent' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }
}

