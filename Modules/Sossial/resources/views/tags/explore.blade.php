<x-sossial::layouts.master :title="'Keşfet'">
    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-7">
                <span class="sosial-kicker"><i class="fa fa-compass"></i> Keşfet</span>
                <h1 class="sosial-hero-title">Etiketler üzerinden içeriği daralt.</h1>
                <p class="sosial-hero-copy">Topluluğun konuştuğu başlıkları gör, ilgili etiketlerle daha hızlı keşif yap.</p>
            </div>
            <div class="col-12 col-lg-5">
                <div class="sosial-hero-metrics">
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $tags->total() }}</span>
                        <span class="sosial-hero-metric-label">Toplam etiket</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $tags->count() }}</span>
                        <span class="sosial-hero-metric-label">Bu sayfada</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $q ? 'Filtreli' : 'Genel' }}</span>
                        <span class="sosial-hero-metric-label">Arama modu</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="sosial-surface sosyal-panel">
        <div class="row g-3 align-items-center mb-4">
            <div class="col-12 col-lg">
                <h2 class="sosial-panel-title mb-1">Etiket araması</h2>
                <p class="sosial-panel-copy">Aradığın konuya yakın etiketleri bul.</p>
            </div>
            <div class="col-12 col-lg-6">
                <form method="GET" class="row g-2">
                    <div class="col-12 col-sm">
                        <div class="sosial-search">
                            <i class="fa fa-search"></i>
                            <input class="form-control sosial-form-control" name="q" value="{{ $q }}" placeholder="Etiket ara...">
                        </div>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <button class="btn sosial-btn-primary w-100" type="submit">Ara</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($tags->count())
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <a class="sosial-chip text-decoration-none" href="{{ route('sosial.tags.show', $tag) }}">
                        #{{ $tag->name }}
                        @if (isset($tag->posts_count))
                            <span>({{ $tag->posts_count }})</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="sosial-empty border rounded-4">
                <div class="sosial-empty-icon"><i class="fa fa-search"></i></div>
                <div class="fw-semibold mb-1">Sonuç bulunamadı</div>
                <p class="mb-0">Arama terimini değiştirip tekrar deneyebilirsin.</p>
            </div>
        @endif
    </div>

    <div class="mt-3">
        {{ $tags->links() }}
    </div>
</x-sossial::layouts.master>
