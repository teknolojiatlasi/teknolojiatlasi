<?php

namespace Modules\Sinav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $table = 'sinav_tests';

    protected $fillable = [
        'topic_id',
        'title',
        'duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'test_id')->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'test_id');
    }
}

