<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sossial\Models\Tag;
use Modules\Sossial\Models\Comment;
use Modules\Sossial\Models\Follow;
use Modules\Sossial\Models\Post;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->with(['user', 'media', 'tags', 'blog'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        $posts->getCollection()->each(function (Post $post) {
            $preview = Comment::query()
                ->where('post_id', $post->id)
                ->where(function ($q) {
                    $q->whereNull('parent_id')->orWhere('parent_id', 0);
                })
                ->with(['user', 'repliesRecursive'])
                ->withCount('replies')
                ->orderBy('created_at')
                ->get();

            $post->setRelation('previewComments', $preview);
        });

        $recentTags = Tag::query()
            ->select('sosial_tags.*')
            ->selectSub(function ($query) {
                $query->from('sosial_post_tag')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('sosial_post_tag.tag_id', 'sosial_tags.id');
            }, 'last_used_at')
            ->withCount('posts')
            ->whereHas('posts')
            ->orderByDesc('last_used_at')
            ->limit(8)
            ->get();

        $popularTags = Tag::query()
            ->withCount('posts')
            ->whereHas('posts')
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('sossial::feed.index', compact('posts', 'recentTags', 'popularTags'));
    }

    public function my(Request $request)
    {
        $posts = Post::query()
            ->where('user_id', $request->user()->id)
            ->with(['user', 'media', 'tags', 'blog'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        $posts->getCollection()->each(function (Post $post) {
            $preview = Comment::query()
                ->where('post_id', $post->id)
                ->where(function ($q) {
                    $q->whereNull('parent_id')->orWhere('parent_id', 0);
                })
                ->with(['user', 'repliesRecursive'])
                ->withCount('replies')
                ->orderBy('created_at')
                ->get();

            $post->setRelation('previewComments', $preview);
        });

        return view('sossial::feed.my', compact('posts'));
    }

    public function following(Request $request)
    {
        $followingIds = Follow::query()
            ->where('follower_id', $request->user()->id)
            ->pluck('following_id');

        $posts = Post::query()
            ->whereIn('user_id', $followingIds)
            ->with(['user', 'media', 'tags', 'blog'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        $posts->getCollection()->each(function (Post $post) {
            $preview = Comment::query()
                ->where('post_id', $post->id)
                ->where(function ($q) {
                    $q->whereNull('parent_id')->orWhere('parent_id', 0);
                })
                ->with(['user', 'repliesRecursive'])
                ->withCount('replies')
                ->orderBy('created_at')
                ->get();

            $post->setRelation('previewComments', $preview);
        });

        return view('sossial::feed.following', compact('posts'));
    }
}
