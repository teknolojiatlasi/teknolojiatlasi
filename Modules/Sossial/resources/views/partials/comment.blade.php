@php
    /** @var \Modules\Sossial\Models\Comment $comment */
@endphp

<div class="border rounded p-3" id="comment-{{ $comment->id }}">
    <div class="d-flex align-items-start justify-content-between">
        <div class="d-flex align-items-start gap-2">
            <img
                src="{{ $comment->user?->avatarUrl() }}"
                alt="{{ $comment->user->name ?? 'Kullanıcı' }}"
                class="rounded-circle flex-shrink-0"
                style="width: 31px; height: 31px; object-fit: cover;"
            >
            <div>
            <div class="fw-semibold">{{ $comment->user->name ?? 'Kullanıcı' }}</div>
            <div class="text-muted small">{{ $comment->created_at?->diffForHumans() }}</div>
        </div></div>
    </div>

    <div class="mt-2" style="white-space: pre-wrap;">{{ $comment->body }}</div>

    @auth
        <div class="mt-2">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#replyBox-{{ $comment->id }}">
                Yanıtla
            </button>
        </div>

        <div class="collapse mt-2" id="replyBox-{{ $comment->id }}">
            <div class="input-group">
                <input class="form-control" placeholder="Yanıt yaz..." id="replyInput-{{ $comment->id }}">
                <button class="btn btn-outline-primary" type="button"
                    onclick="window.__sosialReply({{ $comment->id }}, document.getElementById('replyInput-{{ $comment->id }}'))">
                    Gönder
                </button>
            </div>
        </div>
    @endauth

    <div class="mt-3 ps-3 border-start" data-replies="{{ $comment->id }}">
        @foreach ($comment->replies as $reply)
            @include('sossial::partials.reply', ['reply' => $reply])
        @endforeach
    </div>
</div>
