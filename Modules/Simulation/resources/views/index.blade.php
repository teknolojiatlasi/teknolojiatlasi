@extends('layouts.app2')

@section('title', 'Simulasyonlar')

@push('styles')
<style>
    .simulation-index-shell {
        padding: 2rem 0 3rem;
    }

    .simulation-index-shell .simulation-hero {
        padding: 1.6rem;
        border-radius: 1.4rem;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, 0.16), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #0891b2 58%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 24px 64px rgba(15, 23, 42, 0.16);
    }

    .simulation-index-shell .simulation-card {
        position: relative;
        display: block;
        height: 100%;
        overflow: hidden;
        color: #fff;
        text-decoration: none;
        background: #0f172a;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 0.85rem;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .simulation-index-shell .simulation-card:hover {
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 22px 54px rgba(15, 23, 42, 0.12);
    }

    .simulation-index-shell .simulation-cover,
    .simulation-index-shell .simulation-cover-placeholder {
        width: 100%;
        aspect-ratio: 10 / 3;
        object-fit: cover;
        display: block;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, 0.22), transparent 30%),
            linear-gradient(135deg, #0f172a, #0e7490);
        transition: transform 0.25s ease;
    }

    .simulation-index-shell .simulation-card:hover .simulation-cover,
    .simulation-index-shell .simulation-card:hover .simulation-cover-placeholder {
        transform: scale(1.04);
    }

    .simulation-index-shell .simulation-card-body {
        position: absolute;
        inset: auto 0 0 0;
        z-index: 1;
        padding: 1rem;
        background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.86) 46%, rgba(15, 23, 42, 0.96) 100%);
    }

    .simulation-index-shell .simulation-title {
        margin: 0 0 0.45rem;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.3;
        color: #fff;
    }

    .simulation-index-shell .simulation-meta {
        color: rgba(255, 255, 255, 0.78);
        font-size: 0.86rem;
    }

    .simulation-index-shell .simulation-excerpt {
        display: -webkit-box;
        margin: 0.75rem 0 0;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.92rem;
        line-height: 1.55;
        overflow: hidden;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
</style>
@endpush

@section('content')
<main class="simulation-index-shell">
    <div class="container">
        <section class="simulation-hero mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
                <div>
                    <div class="small text-uppercase fw-bold opacity-75 mb-2">Public liste</div>
                    <h1 class="h2 fw-bold mb-2">Simulasyonlar</h1>
                    <p class="mb-0 opacity-75">Yayinlanmis simulasyonlari giris yapmadan goruntuleyin.</p>
                </div>
                <span class="badge text-bg-light">{{ $simulations->count() }} simulasyon</span>
            </div>
        </section>

        @if ($simulations->isEmpty())
            <div class="alert alert-info mb-0">Henuz yayinlanmis simulasyon bulunmuyor.</div>
        @else
            <div class="row g-4">
                @foreach ($simulations as $simulation)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('simulation.show', $simulation->slug) }}" class="simulation-card">
                            @if ($simulation->cover_image)
                                <img
                                    src="{{ asset('storage/'.$simulation->cover_image) }}"
                                    alt="{{ $simulation->title }}"
                                    class="simulation-cover"
                                    width="1200"
                                    height="675"
                                >
                            @else
                                <div class="simulation-cover-placeholder d-flex align-items-center justify-content-center">
                                    <i class="fa fa-flask fa-2x text-primary"></i>
                                </div>
                            @endif

                            <div class="simulation-card-body">
                                <div class="simulation-meta mb-2">
                                    {{ $simulation->category?->name ?: 'Genel' }}
                                </div>
                                <h2 class="simulation-title">{{ $simulation->title }}</h2>
                                @if ($simulation->excerpt)
                                    <p class="simulation-excerpt">{{ $simulation->excerpt }}</p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
@endsection
