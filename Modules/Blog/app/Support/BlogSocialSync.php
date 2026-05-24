<?php

namespace Modules\Blog\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogComment;
use Modules\Sossial\Models\Comment as SocialComment;
use Modules\Sossial\Models\Post as SocialPost;
use Modules\Sossial\Models\PostMedia;

class BlogSocialSync
{
    public function syncPost(Blog $blog, int $userId): SocialPost
    {
        return DB::transaction(function () use ($blog, $userId) {
            $post = SocialPost::query()->firstOrNew([
                'blog_id' => $blog->id,
            ]);

            $post->fill([
                'user_id' => $post->exists ? $post->user_id : $userId,
                'type' => 'ilan',
                'body' => $this->makePostBody($blog),
                'link_url' => route('blog.public.show', $blog),
            ]);
            $post->save();

            $post->syncTagsFromString($blog->category?->name);
            $this->syncCoverMedia($post, $blog);

            return $post;
        });
    }

    public function deletePost(Blog $blog): void
    {
        $post = SocialPost::query()
            ->with('media')
            ->where('blog_id', $blog->id)
            ->first();

        if (! $post) {
            return;
        }

        foreach ($post->media as $media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
        }

        $post->delete();
    }

    public function syncCommentFromBlog(BlogComment $blogComment): ?SocialComment
    {
        $blogComment->loadMissing('blog.category');

        $post = SocialPost::query()
            ->where('blog_id', $blogComment->blog_id)
            ->first();

        if (! $post) {
            return null;
        }

        return DB::transaction(function () use ($blogComment, $post) {
            $parentSocialId = null;

            if ($blogComment->parent_id) {
                $parentSocialId = BlogComment::query()
                    ->whereKey($blogComment->parent_id)
                    ->value('social_comment_id');
            }

            $comment = SocialComment::query()->updateOrCreate(
                ['id' => $blogComment->social_comment_id],
                [
                    'post_id' => $post->id,
                    'user_id' => null,
                    'parent_id' => $parentSocialId,
                    'author_name' => $blogComment->author_name,
                    'body' => $blogComment->body,
                ],
            );

            if ($blogComment->social_comment_id !== $comment->id) {
                $blogComment->forceFill(['social_comment_id' => $comment->id])->save();
            }

            return $comment;
        });
    }

    public function syncCommentFromSocial(SocialComment $socialComment): ?BlogComment
    {
        $socialComment->loadMissing(['post.blog', 'user']);

        $blog = $socialComment->post?->blog;
        if (! $blog) {
            return null;
        }

        return DB::transaction(function () use ($socialComment, $blog) {
            $blogComment = BlogComment::query()
                ->where('social_comment_id', $socialComment->id)
                ->first();

            $parentBlogId = null;
            if ($socialComment->parent_id) {
                $parentBlogId = BlogComment::query()
                    ->where('social_comment_id', $socialComment->parent_id)
                    ->value('id');
            }

            $authorName = trim((string) ($socialComment->author_name ?: $socialComment->user?->name ?: 'Misafir'));

            if ($blogComment) {
                $blogComment->update([
                    'parent_id' => $parentBlogId,
                    'author_name' => Str::limit($authorName, 80, ''),
                    'body' => $socialComment->body,
                ]);

                return $blogComment;
            }

            return BlogComment::query()->create([
                'blog_id' => $blog->id,
                'parent_id' => $parentBlogId,
                'social_comment_id' => $socialComment->id,
                'author_name' => Str::limit($authorName, 80, ''),
                'body' => $socialComment->body,
                'ip_address' => null,
                'user_agent' => null,
            ]);
        });
    }

    protected function syncCoverMedia(SocialPost $post, Blog $blog): void
    {
        $post->loadMissing('media');

        foreach ($post->media as $media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
        }

        $post->media()->delete();

        if (! $blog->cover_image) {
            return;
        }

        PostMedia::query()->create([
            'post_id' => $post->id,
            'type' => 'image',
            'url' => route('blog.media.show', ['path' => $blog->cover_image]),
            'sort' => 0,
        ]);
    }

    protected function makePostBody(Blog $blog): string
    {
        return $blog->title;
    }
}
