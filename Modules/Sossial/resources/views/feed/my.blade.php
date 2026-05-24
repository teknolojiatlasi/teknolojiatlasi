<x-sossial::layouts.master :title="'Paylaşımlarım'">
    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-8">
                <span class="sosial-kicker"><i class="fa fa-pencil-square-o"></i> Kendi Alanın</span>
                <h1 class="sosial-hero-title">Ürettiğin tüm içerikleri tek yerde yönet.</h1>
                <p class="sosial-hero-copy">Kendi paylaşımlarını gözden geçir, detay sayfasına geç ve akışını kontrol et.</p>
            </div>
            <div class="col-12 col-lg-4">
                <div class="sosial-hero-metrics">
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $posts->total() }}</span>
                        <span class="sosial-hero-metric-label">Toplam paylaşım</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $posts->count() }}</span>
                        <span class="sosial-hero-metric-label">Bu sayfada</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <a class="btn sosial-btn-secondary w-100" href="{{ route('sosial.feed') }}">Akışa Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div data-sosial-infinite-wrapper>
        <div class="sosial-feed-stack" data-sosial-infinite data-next-url="{{ $posts->nextPageUrl() ?? '' }}">
            @forelse ($posts as $post)
                @include('sossial::partials.post-card', ['post' => $post, 'showActions' => true])
            @empty
                <div class="sosial-surface sosial-empty">
                    <div class="sosial-empty-icon"><i class="fa fa-file-text-o"></i></div>
                    <div class="fw-semibold mb-1">Henüz paylaşım yok</div>
                    <p class="mb-0">Yeni bir içerik oluşturup görünürlüğünü artır.</p>
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
</x-sossial::layouts.master>
