<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Sossial\Models\Comment;
use Modules\Sossial\Models\Follow;
use Modules\Sossial\Models\Post;

class ProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        $posts = Post::query()
            ->where('user_id', $user->id)
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

        $isFollowing = false;
        $canMessage = false;
        if ($request->user()) {
            $isFollowing = Follow::query()
                ->where('follower_id', $request->user()->id)
                ->where('following_id', $user->id)
                ->exists();

            $isFollowedBack = Follow::query()
                ->where('follower_id', $user->id)
                ->where('following_id', $request->user()->id)
                ->exists();

            $canMessage = $request->user()->id !== $user->id && $isFollowing && $isFollowedBack;
        }

        return view('sossial::profile.show', compact('user', 'posts', 'isFollowing', 'canMessage'));
    }
}
