<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Sossial\Models\Comment as SocialComment;

class BlogComment extends Model
{
    protected $fillable = [
        'blog_id',
        'parent_id',
        'social_comment_id',
        'author_name',
        'body',
        'ip_address',
        'user_agent',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function socialComment(): BelongsTo
    {
        return $this->belongsTo(SocialComment::class, 'social_comment_id');
    }
}
