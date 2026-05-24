<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sossial\Models\Comment;
use Modules\Sossial\Models\Post;
use Modules\Sossial\Models\Tag;

class TagController extends Controller
{
    public function explore(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $tags = Tag::query()
            ->withCount('posts')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('sossial::tags.explore', compact('tags', 'q'));
    }

    public function show(Tag $tag)
    {
        $posts = Post::query()
            ->whereHas('tags', fn ($q) => $q->where('sosial_tags.id', $tag->id))
            ->with(['user', 'media', 'tags'])
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

        return view('sossial::tags.show', compact('tag', 'posts', 'recentTags', 'popularTags'));
    }
}
