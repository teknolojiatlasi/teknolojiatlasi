<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            if (blank($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'page_role');
    }
}
