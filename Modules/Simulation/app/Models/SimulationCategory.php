<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class SimulationCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function simulations(): HasMany
    {
        return $this->hasMany(Simulation::class, 'category_id');
    }

    public function flattenedLabel(int $level = 0): string
    {
        return str_repeat('— ', $level).$this->name;
    }

    public function descendantIds(): Collection
    {
        $ids = collect();

        foreach ($this->children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($child->descendantIds());
        }

        return $ids->unique()->values();
    }

    public function subtreeIds(): Collection
    {
        return collect([$this->id])->merge($this->descendantIds())->unique()->values();
    }
}
