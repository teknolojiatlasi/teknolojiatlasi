<div class="container py-4">
    <h1>Simulasyonlar</h1>

    @if ($simulations->isEmpty())
        <p>Henüz yayınlanmış simülasyon bulunmuyor.</p>
    @else
        <div class="row g-3">
            @foreach ($simulations as $simulation)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ $simulation->title }}</h2>
                            <p class="text-muted mb-2">{{ $simulation->category?->name }}</p>
                            <p>{{ $simulation->excerpt }}</p>
                            <a href="{{ route('simulation.show', $simulation->slug) }}">Detay</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
</div>
