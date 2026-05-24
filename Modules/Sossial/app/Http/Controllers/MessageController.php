<?php

namespace Modules\Sossial\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Sossial\Models\Follow;
use Modules\Sossial\Models\Message;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $contacts = $this->mutualContacts($user->id);
        $selectedUser = $contacts->first();

        $messages = $selectedUser
            ? $this->conversationMessages($user->id, $selectedUser->id)
            : collect();

        if ($selectedUser) {
            $this->markConversationAsRead($selectedUser->id, $user->id);
            $this->clearUnreadCount($contacts, $selectedUser->id);
        }

        return view('sossial::messages.index', compact('contacts', 'messages', 'selectedUser'));
    }

    public function show(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 404);
        abort_unless($this->canMessage($request->user()->id, $user->id), 403, 'Mesajlaşmak için iki kullanıcının birbirini takip etmesi gerekir.');

        $contacts = $this->mutualContacts($request->user()->id, $user->id);
        $selectedUser = $user;
        $messages = $this->conversationMessages($request->user()->id, $user->id);

        $this->markConversationAsRead($user->id, $request->user()->id);
        $this->clearUnreadCount($contacts, $user->id);

        return view('sossial::messages.index', compact('contacts', 'messages', 'selectedUser'));
    }

    public function store(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'Kendinize mesaj gönderemezsiniz.');
        abort_unless($this->canMessage($request->user()->id, $user->id), 403, 'Mesajlaşmak için iki kullanıcının birbirini takip etmesi gerekir.');

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $body = trim($data['body']);

        if ($body === '') {
            return back()
                ->withErrors(['body' => 'Mesaj boş olamaz.'])
                ->withInput();
        }

        Message::query()->create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $user->id,
            'body' => $body,
        ]);

        return redirect()
            ->route('sosial.messages.show', $user)
            ->with('status', 'Mesaj gönderildi.');
    }

    public function unreadCount(Request $request)
    {
        $query = Message::query()
            ->where('receiver_id', $request->user()->id)
            ->whereNull('read_at');

        $latest = (clone $query)
            ->with('sender:id,name,avatar')
            ->latest()
            ->first();

        return response()->json([
            'count' => (clone $query)->count(),
            'latest_sender' => $latest?->sender?->name,
        ]);
    }

    private function mutualContacts(int $userId, ?int $preferredUserId = null)
    {
        $followingIds = Follow::query()
            ->where('follower_id', $userId)
            ->pluck('following_id');

        $followerIds = Follow::query()
            ->where('following_id', $userId)
            ->pluck('follower_id');

        $mutualIds = $followingIds->intersect($followerIds)->values();

        if ($preferredUserId && !$mutualIds->contains($preferredUserId)) {
            $mutualIds->push($preferredUserId);
        }

        return User::query()
            ->whereIn('id', $mutualIds)
            ->withCount([
                'sentSosialMessages as unread_messages_count' => fn ($query) => $query
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at'),
            ])
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$preferredUserId ?: 0])
            ->orderBy('name')
            ->get();
    }

    private function conversationMessages(int $userId, int $contactId)
    {
        return Message::query()
            ->where(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $userId)->where('receiver_id', $contactId);
            })
            ->orWhere(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $contactId)->where('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();
    }

    private function markConversationAsRead(int $senderId, int $receiverId): void
    {
        Message::query()
            ->where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
    }

    private function clearUnreadCount($contacts, int $contactId): void
    {
        $contact = $contacts->firstWhere('id', $contactId);
        if ($contact) {
            $contact->unread_messages_count = 0;
        }
    }

    private function canMessage(int $userId, int $contactId): bool
    {
        $isFollowing = Follow::query()
            ->where('follower_id', $userId)
            ->where('following_id', $contactId)
            ->exists();

        $isFollowedBack = Follow::query()
            ->where('follower_id', $contactId)
            ->where('following_id', $userId)
            ->exists();

        return $isFollowing && $isFollowedBack;
    }
}
