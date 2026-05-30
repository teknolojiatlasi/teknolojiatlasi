<?php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Modules\Blog\Support\HtmlSanitizer;
use Modules\Media\Models\Media;
use Modules\Sossial\Models\Post as SocialPost;

class Blog extends Model
{
    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'content',
        'cover_image',
        'cover_media_id',
        'status',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $blog): void {
            $blog->flushPublicCaches();
        });

        static::deleted(function (self $blog): void {
            $blog->flushPublicCaches();
        });
    }

    public function setContentAttribute($value): void
    {
        $normalized = self::normalizeBlogMediaUrls(is_string($value) ? $value : null);

        $this->attributes['content'] = HtmlSanitizer::sanitize($normalized);
    }

    public function getContentAttribute($value): ?string
    {
        return self::normalizeBlogMediaUrls($value);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function images()
    {
        return $this->hasMany(BlogImage::class);
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function socialPost(): HasOne
    {
        return $this->hasOne(SocialPost::class, 'blog_id');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->coverMedia) {
            return self::blogMediaUrl($this->coverMedia->file_path) ?? $this->coverMedia->url;
        }

        if ($this->cover_image) {
            return self::blogMediaUrl($this->cover_image);
        }

        return null;
    }

    public function flushPublicCaches(): void
    {
        Cache::forget('public.home.data.v2');
        Cache::forget('public.blog.menus.v1');
        Cache::forget("public.blog.show.{$this->id}.v1");
        Cache::forget("public.blog.comments.{$this->id}.v1");
        Cache::forget("public.blog.latest-sidebar.{$this->id}.v1");
    }

    public static function normalizeBlogMediaUrls(?string $content): ?string
    {
        if ($content === null || trim($content) === '') {
            return $content;
        }

        return preg_replace_callback(
            '#(?:(?:https?:)?//(?:www\.)?teknolojiatlasi\.com\.tr)?/storage/((?:blogs/(?:covers|images|editor)|uploads/covers)/[^"\'>\s?]+)#i',
            static function (array $matches): string {
                $path = ltrim((string) ($matches[1] ?? ''), '/');

                return route('blog.media.show', ['path' => $path]);
            },
            $content
        ) ?? $content;
    }

    protected static function blogMediaUrl(?string $path): ?string
    {
        $path = ltrim((string) $path, '/');

        if ($path === '') {
            return null;
        }

        if (! str_starts_with($path, 'blogs/') && ! str_starts_with($path, 'uploads/covers/')) {
            return null;
        }

        return route('blog.media.show', ['path' => $path]);
    }
}
