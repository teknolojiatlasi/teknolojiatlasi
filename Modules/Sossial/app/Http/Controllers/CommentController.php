<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Blog\Support\BlogSocialSync;
use Modules\Sossial\Models\Comment;
use Modules\Sossial\Models\Post;

class CommentController extends Controller
{
    public function __construct(
        protected BlogSocialSync $blogSocialSync,
    ) {
    }

    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $body = trim((string) $data['body']);

        abort_if($body === '', 422, 'Yorum bos olamaz.');

        $dedupeKey = $this->dedupeKey(
            userId: (int) $request->user()->id,
            postId: (int) $post->id,
            parentId: null,
            body: $body,
        );

        $existingCommentId = Cache::get($dedupeKey);
        if ($existingCommentId) {
            $existingComment = Comment::query()->with('user')->find($existingCommentId);

            if ($existingComment) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => true,
                        'duplicate' => true,
                        'comment_id' => $existingComment->id,
                        'html' => null,
                    ]);
                }

                return redirect()->route('sosial.posts.show', $post);
            }
        }

        $comment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'parent_id' => null,
            'author_name' => $request->user()->name,
            'body' => $body,
        ]);

        $this->blogSocialSync->syncCommentFromSocial($comment);
        $comment->load('user');
        Cache::put($dedupeKey, $comment->id, now()->addSeconds(10));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'duplicate' => false,
                'comment_id' => $comment->id,
                'html' => view('sossial::partials.comment-node', ['comment' => $comment, 'children' => []])->render(),
            ]);
        }

        return redirect()->route('sosial.posts.show', $post);
    }

    public function reply(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $body = trim((string) $data['body']);

        abort_if($body === '', 422, 'Yanit bos olamaz.');

        $dedupeKey = $this->dedupeKey(
            userId: (int) $request->user()->id,
            postId: (int) $comment->post_id,
            parentId: (int) $comment->id,
            body: $body,
        );

        $existingReplyId = Cache::get($dedupeKey);
        if ($existingReplyId) {
            $existingReply = Comment::query()->with('user')->find($existingReplyId);

            if ($existingReply) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => true,
                        'duplicate' => true,
                        'comment_id' => $existingReply->id,
                        'parent_id' => $comment->id,
                        'html' => null,
                    ]);
                }

                return redirect()->route('sosial.posts.show', $comment->post_id);
            }
        }

        $reply = Comment::query()->create([
            'post_id' => $comment->post_id,
            'user_id' => $request->user()->id,
            'parent_id' => $comment->id,
            'author_name' => $request->user()->name,
            'body' => $body,
        ]);

        $this->blogSocialSync->syncCommentFromSocial($reply);
        $reply->load('user');
        Cache::put($dedupeKey, $reply->id, now()->addSeconds(10));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'duplicate' => false,
                'comment_id' => $reply->id,
                'parent_id' => $comment->id,
                'html' => view('sossial::partials.comment-node', ['comment' => $reply, 'children' => [], 'depth' => 1])->render(),
            ]);
        }

        return redirect()->route('sosial.posts.show', $comment->post_id);
    }

    protected function dedupeKey(int $userId, int $postId, ?int $parentId, string $body): string
    {
        return 'sosial:comment-submit:' . sha1(implode('|', [
            $userId,
            $postId,
            $parentId ?? 0,
            $body,
        ]));
    }
}
