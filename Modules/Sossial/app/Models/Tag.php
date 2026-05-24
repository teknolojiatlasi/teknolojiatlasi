<?php

namespace Modules\Sossial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'sosial_tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'sosial_post_tag', 'tag_id', 'post_id')
            ->withTimestamps();
    }
}
