@php
    /** @var \Modules\Sossial\Models\Post $post */
    $showActions = $showActions ?? false;
    $showCommentsPreview = $showCommentsPreview ?? true;
    $images = $post->media
        ->where('type', 'image')
        ->filter(fn ($m) => (bool) $m->url || (bool) $m->path)
        ->values();
    $carouselId = 'post-' . $post->id . '-carousel';

    $previewComments = $post->relationLoaded('previewComments') ? $post->previewComments : collect();
    $commentsCount = (int) ($post->comments_count ?? 0);
    $postTypeLabels = [
        'interview' => 'Mülakat Deneyimi',
        'advice' => 'Kariyer Tavsiyesi',
        'company' => 'Şirket Deneyimi',
        'ilan' => 'Yazı Paylaşımı',
    ];
    $postTypeLabel = $postTypeLabels[$post->type] ?? ucfirst((string) $post->type);
    $isOwner = auth()->check() && auth()->user()?->is($post->user);
    $postOwnerName = $post->user->name ?? 'Kullanıcı';
    $isListingPost = $post->type === 'ilan' && $post->blog;
    $listingTitle = $isListingPost ? $post->blog->title : null;
@endphp

<article class="sosial-surface sosial-post-card" id="post-{{ $post->id }}">
    @if ($images->count() === 1)
        @php
            $image = $images->first();
            $src = $image?->url ?: ($image?->path ? route('sosial.media.show', $image) : null);
        @endphp
        @if ($src)
            <div class="sosial-post-media">
                <img
                    class="d-block w-100 sosial-zoomable"
                    src="{{ $src }}"
                    alt="{{ $listingTitle ?: $postOwnerName }}"
                    data-sosial-zoom-src="{{ $src }}"
                    data-sosial-zoom-group="post-{{ $post->id }}"
                    data-sosial-zoom-index="0"
                >
            </div>
        @endif
    @elseif ($images->count() > 1)
        <div class="sosial-post-media sosial-post-media--carousel">
            <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2600" data-bs-pause="hover">
                <div class="carousel-indicators sosial-post-carousel-indicators">
                    @foreach ($images as $index => $image)
                        <button
                            type="button"
                            data-bs-target="#{{ $carouselId }}"
                            data-bs-slide-to="{{ $index }}"
                            @class(['active' => $index === 0])
                            @if ($index === 0) aria-current="true" @endif
                            aria-label="Slide {{ $index + 1 }}"
                        >{{ $index + 1 }}</button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach ($images as $index => $image)
                        @php
                            $src = $image->url ?: ($image->path ? route('sosial.media.show', $image) : null);
                        @endphp
                        @if ($src)
                            <div @class(['carousel-item', 'active' => $index === 0])>
                                <img
                                    class="d-block w-100 sosial-zoomable"
                                    src="{{ $src }}"
                                    alt="{{ ($listingTitle ?: $postOwnerName) . ' ' . ($index + 1) }}"
                                    data-sosial-zoom-src="{{ $src }}"
                                    data-sosial-zoom-group="post-{{ $post->id }}"
                                    data-sosial-zoom-index="{{ $index }}"
                                >
                            </div>
                        @endif
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Önceki</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sonraki</span>
                </button>
            </div>
        </div>
    @endif

    <div class="sosial-panel">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div class="d-flex align-items-start gap-3">
                <a href="{{ route('sosial.profile.show', $post->user) }}">
                    <img
                        src="{{ $post->user?->avatarUrl() }}"
                        alt="{{ $postOwnerName }}"
                        class="sosial-avatar"
                    >
                </a>
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <a class="fw-bold text-dark" href="{{ route('sosial.profile.show', $post->user) }}">
                            {{ $postOwnerName }}
                        </a>
                        <span class="sosial-chip">{{ $postTypeLabel }}</span>
                    </div>
                    <div class="text-muted small d-flex align-items-center flex-wrap gap-2">
                        <span>{{ $post->created_at?->diffForHumans() }}</span>
                        <span>&bull;</span>
                        <span>{{ $commentsCount }} yorum</span>
                    </div>
                </div>
            </div>

            <div class="sosial-post-actions">
                <a class="sosial-btn-ghost btn btn-sm" href="{{ route('sosial.posts.show', $post) }}">Detay</a>
                @if ($showActions && $isOwner)
                    <a class="btn btn-sm btn-outline-primary rounded-pill px-3" href="{{ route('sosial.posts.edit', $post) }}">Düzenle</a>
                    <form method="POST" action="{{ route('sosial.posts.destroy', $post) }}" onsubmit="return confirm('Paylaşımı silmek istiyor musun?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" type="submit">Sil</button>
                    </form>
                @endif
            </div>
        </div>

        @if ($isListingPost)
            <div class="mt-3">
                <div class="fw-bold fs-5">{{ $listingTitle }}</div>
                @if ($post->link_url)
                    <div class="mt-3">
                        <a href="{{ $post->link_url }}" target="_blank" rel="noopener" class="btn btn-primary rounded-pill px-4">
                            Yazı detay linki
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="sosial-post-body mt-3">{{ $post->body }}</div>

            @if ($post->link_url)
                <div class="mt-3">
                    <a href="{{ $post->link_url }}" target="_blank" rel="noopener" class="sosial-chip sosial-chip-soft">
                        <i class="fa fa-link"></i> {{ $post->link_url }}
                    </a>
                </div>
            @endif
        @endif

        @if ($post->tags->isNotEmpty())
            <div class="mt-3 d-flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <a class="sosial-chip text-decoration-none" href="{{ route('sosial.tags.show', $tag) }}">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($showCommentsPreview && $commentsCount > 0)
            <div class="mt-4 pt-4 border-top">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                    <div class="fw-bold">Yorumlar ({{ $commentsCount }})</div>
                    <a class="small fw-semibold text-primary" href="{{ route('sosial.posts.show', $post) }}#comments">Tümünü görüntüle</a>
                </div>

                @if ($previewComments->isNotEmpty())
                    <div class="d-grid gap-2">
                        @foreach ($previewComments as $comment)
                            @php
                                $commentAuthorName = $comment->user->name ?? $comment->author_name ?? 'Kullanıcı';
                            @endphp
                            <div class="sosial-comment-preview p-3">
                                <div class="d-flex align-items-start gap-2">
                                    @if ($comment->user)
                                        <img
                                            src="{{ $comment->user->avatarUrl() }}"
                                            alt="{{ $commentAuthorName }}"
                                            class="sosial-avatar-sm"
                                        >
                                    @else
                                        <div class="sosial-avatar-sm d-inline-flex align-items-center justify-content-center bg-secondary text-white fw-bold">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($commentAuthorName, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        @include('sossial::partials.comment-preview-thread', ['comment' => $comment, 'depth' => 0])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</article>
