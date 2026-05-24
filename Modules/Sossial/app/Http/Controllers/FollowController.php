<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Sossial\Models\Follow;

class FollowController extends Controller
{
    public function store(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'Kendinizi takip edemezsiniz.');

        Follow::query()->firstOrCreate([
            'follower_id' => $request->user()->id,
            'following_id' => $user->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'following' => true]);
        }

        return back();
    }

    public function destroy(Request $request, User $user)
    {
        Follow::query()
            ->where('follower_id', $request->user()->id)
            ->where('following_id', $user->id)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'following' => false]);
        }

        return back();
    }
}

