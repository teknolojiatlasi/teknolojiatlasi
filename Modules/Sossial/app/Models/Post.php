<?php

namespace Modules\Sossial\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;

class Post extends Model
{
    use HasFactory;

    protected $table = 'sosial_posts';

    protected $fillable = [
        'user_id',
        'blog_id',
        'type',
        'body',
        'link_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class, 'post_id')->orderBy('sort');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'sosial_post_tag', 'post_id', 'tag_id')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    /**
     * @return array<int, Tag>
     */
    public function syncTagsFromString(?string $tags): array
    {
        $tags = trim((string) $tags);
        if ($tags === '') {
            $this->tags()->sync([]);
            return [];
        }

        $names = collect(preg_split('/[,\n]+/', $tags))
            ->map(fn ($t) => trim((string) $t))
            ->filter()
            ->map(fn ($t) => Str::of($t)->lower()->limit(40, '')->toString())
            ->unique()
            ->values();

        $tagIds = [];
        $tagModels = [];

        foreach ($names as $name) {
            $slug = Str::slug($name);
            if ($slug === '') {
                continue;
            }

            $tag = Tag::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );

            $tagIds[] = $tag->id;
            $tagModels[] = $tag;
        }

        $this->tags()->sync($tagIds);

        return $tagModels;
    }
}
