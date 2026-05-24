@php
    /** @var \Modules\Sossial\Models\Comment $comment */
    $depth = (int) ($depth ?? 0);
    $authorName = $comment->user->name ?? $comment->author_name ?? 'Kullanıcı';
    $replies = $comment->relationLoaded('repliesRecursive') ? $comment->repliesRecursive : collect();
@endphp

<div @class(['small', 'mt-3 ps-3 border-start' => $depth > 0])>
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <div class="fw-semibold">{{ $authorName }}</div>
        <div class="text-muted">
            @if ($depth === 0 && ($comment->replies_count ?? 0) > 0)
                <span class="me-2">{{ $comment->replies_count }} yanıt</span>
            @endif
            <span>{{ $comment->created_at?->diffForHumans() }}</span>
        </div>
    </div>
    <div class="mt-1" style="white-space: pre-wrap;">{{ \Illuminate\Support\Str::limit($comment->body, $depth === 0 ? 180 : 120) }}</div>

    @if ($replies->isNotEmpty())
        <div class="mt-3 d-grid gap-2">
            @foreach ($replies as $reply)
                @include('sossial::partials.comment-preview-thread', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
