<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogComment;
use Modules\Blog\Support\BlogSocialSync;

class BlogCommentController extends Controller
{
    public function __construct(
        protected BlogSocialSync $blogSocialSync,
    ) {
    }

    public function store(Request $request, Blog $blog): RedirectResponse|JsonResponse
    {
        if ($request->filled('website')) {
            return $this->duplicateResponse($request, null);
        }

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:80'],
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        if ($parentId !== null) {
            $parentId = BlogComment::query()
                ->where('blog_id', $blog->id)
                ->whereKey($parentId)
                ->value('id');

            if ($parentId === null) {
                abort(404);
            }
        }

        $authorName = Str::limit(Str::of($data['author_name'])->trim()->squish()->toString(), 80, '');
        $body = Str::of($data['body'])->replaceMatches('/\R/u', "\n")->trim()->toString();

        abort_if($authorName === '' || $body === '', 422, 'Yorum bos olamaz.');

        $dedupeKey = $this->dedupeKey(
            blogId: (int) $blog->id,
            parentId: $parentId ? (int) $parentId : null,
            authorName: $authorName,
            body: $body,
            ipAddress: (string) $request->ip(),
        );

        if ($existingComment = $this->findDuplicate($dedupeKey)) {
            return $this->duplicateResponse($request, $existingComment);
        }

        $lockKey = 'blog:comment-submit:lock:' . sha1($dedupeKey);

        try {
            $comment = Cache::lock($lockKey, 10)->block(3, function () use ($blog, $parentId, $authorName, $body, $request, $dedupeKey) {
                if ($existingComment = $this->findDuplicate($dedupeKey)) {
                    return $existingComment;
                }

                $comment = $blog->comments()->create([
                    'parent_id' => $parentId,
                    'author_name' => $authorName,
                    'body' => $body,
                    'ip_address' => $request->ip(),
                    'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
                ]);

                $this->blogSocialSync->syncCommentFromBlog($comment);
                Cache::put($dedupeKey, $comment->id, now()->addSeconds(30));
                $this->forgetBlogCommentCaches($blog);

                return $comment;
            });
        } catch (LockTimeoutException) {
            return $this->duplicateResponse($request, $this->findDuplicate($dedupeKey));
        }

        $comment->setRelation('childrenRecursive', collect());

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'duplicate' => false,
                'comment_id' => $comment->id,
                'parent_id' => $comment->parent_id,
                'html' => view('blog::partials.comment', [
                    'comment' => $comment,
                    'depth' => $this->commentDepth($comment),
                    'blog' => $blog,
                ])->render(),
                'message' => 'Yorumunuz eklendi.',
                'comments_count' => $blog->comments()->count(),
            ]);
        }

        return back()
            ->withFragment('comment-' . $comment->id)
            ->with('success', 'Yorumunuz eklendi.');
    }

    protected function duplicateResponse(Request $request, ?BlogComment $comment): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'duplicate' => true,
                'comment_id' => $comment?->id,
                'parent_id' => $comment?->parent_id,
                'html' => null,
                'message' => 'Aynı yorum zaten gonderildi.',
                'comments_count' => $comment?->blog_id
                    ? BlogComment::query()->where('blog_id', $comment->blog_id)->count()
                    : null,
            ]);
        }

        return back()->with('success', 'Aynı yorum zaten gonderildi.');
    }

    protected function dedupeKey(int $blogId, ?int $parentId, string $authorName, string $body, string $ipAddress): string
    {
        return 'blog:comment-submit:' . sha1(implode('|', [
            $blogId,
            $parentId ?? 0,
            Str::lower($authorName),
            preg_replace('/\s+/u', ' ', Str::lower($body)) ?? Str::lower($body),
            $ipAddress,
        ]));
    }

    protected function findDuplicate(string $dedupeKey): ?BlogComment
    {
        $existingCommentId = Cache::get($dedupeKey);

        if (! $existingCommentId) {
            return null;
        }

        return BlogComment::query()->with('childrenRecursive')->find($existingCommentId);
    }

    protected function forgetBlogCommentCaches(Blog $blog): void
    {
        Cache::forget("public.blog.show.{$blog->id}.v1");
        Cache::forget("public.blog.comments.{$blog->id}.v1");
    }

    protected function commentDepth(BlogComment $comment): int
    {
        $depth = 0;
        $current = $comment;

        while ($current->parent_id && $depth < 5) {
            $depth++;
            $current = BlogComment::query()->select(['id', 'parent_id'])->find($current->parent_id);

            if (! $current) {
                break;
            }
        }

        return $depth;
    }
}
