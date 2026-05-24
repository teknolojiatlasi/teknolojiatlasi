<x-sossial::layouts.master :title="'Sosial Akış'">
    <div class="row g-4 align-items-start">
        <aside class="col-12 col-xl-4">
            @include('sossial::partials.tag-sidebar', [
                'recentTags' => $recentTags,
                'popularTags' => $popularTags,
            ])
        </aside>

        <div class="col-12 col-xl-8">
            <div class="sosial-feed-meta">
                <div>
                    <h2 class="sosial-panel-title mb-1">Güncel akış</h2>
                    <p class="sosial-panel-copy">Topluluğun son paylaşımları.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn sosial-btn-primary" href="{{ route('sosial.posts.create') }}">
                        <i class="fa fa-pencil"></i> Gönderi oluştur
                    </a>
                    <a class="btn sosial-btn-secondary" href="{{ route('sosial.explore') }}">Etiket Keşfet</a>
                </div>
            </div>

            <div data-sosial-infinite-wrapper>
                <div id="sosialFeedList" class="sosial-feed-stack" data-sosial-infinite data-next-url="{{ $posts->nextPageUrl() ?? '' }}">
                    @forelse ($posts as $post)
                        @include('sossial::partials.post-card', ['post' => $post])
                    @empty
                        <div class="sosial-surface sosial-empty">
                            <div class="sosial-empty-icon"><i class="fa fa-comments-o"></i></div>
                            <div class="fw-semibold mb-1">Henüz paylaşım yok</div>
                            <p class="mb-0">İlk içeriği oluşturarak akışı başlat.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3" data-sosial-pagination>
                    {{ $posts->links() }}
                </div>

                <div class="mt-3 text-center d-none" data-sosial-loading>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>

                <div class="mt-3 text-center d-none text-muted small" data-sosial-end>
                    Tüm paylaşımlar yüklendi.
                </div>
            </div>
        </div>
    </div>
</x-sossial::layouts.master>
