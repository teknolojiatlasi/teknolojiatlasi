@php
    /** @var \Modules\Sossial\Models\Comment $comment */
    /** @var array<int, array<int, \Modules\Sossial\Models\Comment>> $children */
    $depth = (int) ($depth ?? 0);
    $childComments = $children[$comment->id] ?? [];
    $childCount = count($childComments);
    $authorName = $comment->user->name ?? $comment->author_name ?? 'Kullanıcı';
    $bodyCollapseId = 'commentBody-' . $comment->id;
    $replyCollapseId = 'replyBox-' . $comment->id;
    $threadCollapseId = 'commentThread-' . $comment->id;
@endphp

<div class="sosial-comment-card sosial-comment-card-depth-{{ min($depth, 4) }}" id="comment-{{ $comment->id }}" @if($depth > 0) style="margin-top: .85rem;" @endif>
    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap sosial-comment-card-head">
        <button
            class="sosial-comment-toggle"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#{{ $bodyCollapseId }}"
            aria-expanded="{{ $depth === 0 ? 'true' : 'false' }}"
            aria-controls="{{ $bodyCollapseId }}"
        >
            @if ($comment->user)
                <img
                    src="{{ $comment->user->avatarUrl() }}"
                    alt="{{ $authorName }}"
                    class="sosial-avatar-sm"
                >
            @else
                <div class="sosial-avatar-sm d-inline-flex align-items-center justify-content-center bg-secondary text-white fw-bold">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($authorName, 0, 1)) }}
                </div>
            @endif
            <span class="sosial-comment-toggle-copy">
                <span class="fw-semibold d-inline-flex align-items-center gap-2 flex-wrap">
                    {{ $authorName }}
                    @if ($depth > 0)
                        <span class="sosial-comment-depth-badge">Yanıt</span>
                    @endif
                    @if ($childCount > 0)
                        <span class="text-muted fw-normal small">{{ $childCount }} yanit</span>
                    @endif
                </span>
                <span class="text-muted small d-block mt-1">{{ $comment->created_at?->diffForHumans() }}</span>
            </span>
            <span class="sosial-comment-toggle-icon" aria-hidden="true">
                <i class="fa fa-angle-down"></i>
            </span>
        </button>

        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
            <button
                class="btn btn-sm btn-light rounded-pill px-3 sosial-thread-toggle @if($childCount === 0) d-none @endif"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#{{ $threadCollapseId }}"
                aria-expanded="{{ $depth === 0 && $childCount > 0 ? 'true' : 'false' }}"
                aria-controls="{{ $threadCollapseId }}"
            >
                {{ $depth === 0 && $childCount > 0 ? 'Yanıtları gizle' : 'Yanıtları göster' }}
            </button>
        </div>
    </div>

    <div class="collapse @if($depth === 0) show @endif" id="{{ $bodyCollapseId }}">
        <div class="sosial-comment-body">
            <div class="sosial-comment-text" style="white-space: pre-wrap;">{{ $comment->body }}</div>

            @auth
                <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $replyCollapseId }}" aria-expanded="false" aria-controls="{{ $replyCollapseId }}">
                        Yanıtla
                    </button>
                </div>

                <div class="collapse mt-3" id="{{ $replyCollapseId }}">
                    <div class="row g-2">
                        <div class="col-12 col-sm">
                            <input class="form-control sosial-form-control" placeholder="Yanıt yaz..." id="replyInput-{{ $comment->id }}">
                        </div>
                        <div class="col-12 col-sm-auto">
                            <button
                                class="btn sosial-btn-primary w-100"
                                type="button"
                                onclick="window.__sosialReply({{ $comment->id }}, document.getElementById('replyInput-{{ $comment->id }}'))"
                            >
                                Gönder
                            </button>
                        </div>
                    </div>
                </div>
            @endauth

            <div class="collapse sosial-comment-thread-wrap @if($depth === 0 && $childCount > 0) show @endif" id="{{ $threadCollapseId }}">
                <div class="sosial-comment-thread" data-replies="{{ $comment->id }}">
                    @foreach ($childComments as $child)
                        @include('sossial::partials.comment-node', ['comment' => $child, 'children' => $children, 'depth' => $depth + 1])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
