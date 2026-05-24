<x-sossial::layouts.master :title="'Takip Ettiklerim'">
    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-8">
                <span class="sosial-kicker"><i class="fa fa-users"></i> Takip Akışı</span>
                <h1 class="sosial-hero-title">Takip ettiğin kişilerin güncel paylaşımları.</h1>
                <p class="sosial-hero-copy">İlgi duyduğun kullanıcıların içeriklerini ayrı bir akışta takip et.</p>
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
                        <a class="btn sosial-btn-secondary w-100" href="{{ route('sosial.feed') }}">Ana Akış</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div data-sosial-infinite-wrapper>
        <div class="sosial-feed-stack" data-sosial-infinite data-next-url="{{ $posts->nextPageUrl() ?? '' }}">
            @forelse ($posts as $post)
                @include('sossial::partials.post-card', ['post' => $post])
            @empty
                <div class="sosial-surface sosyal-empty">
                    <div class="sosial-empty-icon"><i class="fa fa-user-plus"></i></div>
                    <div class="fw-semibold mb-1">Takip akışı boş</div>
                    <p class="mb-0">Takip ettiğin kullanıcılar henüz paylaşım yapmamış.</p>
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
