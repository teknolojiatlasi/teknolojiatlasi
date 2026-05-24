<?php

namespace Modules\Sinav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'sinav_topics';

    protected $fillable = [
        'lesson_id',
        'parent_id',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $topic) {
            if ($topic->children()->exists()) {
                throw new \RuntimeException('Alt konu(lar) bulunan konu silinemez.');
            }
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'topic_id')->orderBy('created_at', 'desc');
    }
}

