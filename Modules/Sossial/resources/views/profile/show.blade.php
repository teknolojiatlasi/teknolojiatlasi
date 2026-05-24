<x-sossial::layouts.master :title="'Profil'">
    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <img src="{{ $user?->avatarUrl() }}" alt="{{ $user->name ?? 'Kullanıcı' }}" class="sosial-avatar-lg">
                    <div>
                        <span class="sosial-kicker"><i class="fa fa-user"></i> Profil</span>
                        <h1 class="sosial-hero-title mb-2">{{ $user->name ?? ('Kullanıcı #' . $user->id) }}</h1>
                        <p class="sosial-hero-copy">Topluluk içindeki paylaşımlar, kariyer deneyimleri ve etkileşimler.</p>
                    </div>
                </div>
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
                        <div class="mb-2">
                            @include('sossial::partials.follow-button', ['profileUser' => $user, 'isFollowing' => $isFollowing])
                        </div>
                        @if ($canMessage)
                            <a class="btn sosial-btn-secondary w-100" href="{{ route('sosial.messages.show', $user) }}">
                                <i class="fa fa-envelope"></i> Mesaj Gönder
                            </a>
                        @endif
                        <span class="sosial-hero-metric-label">Bağlantıyı yönet</span>
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
                <div class="sosial-surface sosial-empty">
                    <div class="sosial-empty-icon"><i class="fa fa-folder-open-o"></i></div>
                    <div class="fw-semibold mb-1">Henüz paylaşım yok</div>
                    <p class="mb-0">Bu kullanıcı henüz içerik yayınlamamış.</p>
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

    @push('scripts')
        @include('sossial::partials.follow-script')
    @endpush
</x-sossial::layouts.master>
