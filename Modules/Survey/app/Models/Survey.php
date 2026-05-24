<?php

namespace Modules\Survey\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
        'is_public',
        'allow_multiple_submissions',
        'opens_at',
        'closes_at',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'allow_multiple_submissions' => 'boolean',
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
        'settings' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('position');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class)->latest();
    }

    public function getIsOpenAttribute(): bool
    {
        $now = now();
        $withinWindow = (!$this->opens_at || $this->opens_at <= $now) &&
            (!$this->closes_at || $this->closes_at >= $now);

        return $this->is_active && $withinWindow;
    }
}
