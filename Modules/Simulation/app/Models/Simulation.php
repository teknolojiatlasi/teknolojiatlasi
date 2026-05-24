<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Simulation extends Model
{
    protected $fillable = [
        'category_id',
        'topic_path',
        'title',
        'slug',
        'excerpt',
        'content',
        'content_type',
        'html_code',
        'css_code',
        'js_code',
        'video_url',
        'video_source',
        'cover_image',
        'status',
        'published_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SimulationCategory::class, 'category_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(SimulationMedia::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(SimulationVersion::class)->latest('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
