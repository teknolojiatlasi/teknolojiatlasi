@extends('layouts.app2')

@section('title', $simulation->title)

@section('content')
<main class="py-4 py-lg-5">
    <div class="container">
        <article class="bg-white border rounded-3 shadow-sm overflow-hidden">
            <div class="p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <p class="text-muted mb-2">{{ $simulation->category?->name ?: 'Genel' }}</p>
                        <h1 class="h2 fw-bold mb-2">{{ $simulation->title }}</h1>
                        @if ($simulation->excerpt)
                            <p class="text-muted mb-0">{{ $simulation->excerpt }}</p>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('simulation.index') }}" class="btn btn-outline-dark rounded-pill">
                            <i class="fa fa-arrow-left"></i> Listeye Don
                        </a>
                    </div>
                </div>

                @if ($simulation->content_type === 'video' && $simulation->video_url)
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-dark">
                        <iframe src="{{ $simulation->video_url }}" title="{{ $simulation->title }}" allowfullscreen></iframe>
                    </div>
                @elseif ($simulation->cover_image)
                    <img src="{{ $simulation->cover_image_url }}" alt="{{ $simulation->title }}" class="img-fluid rounded-3">
                @endif

                @if ($simulation->content)
                    <section class="mt-4">
                        {!! $simulation->content !!}
                    </section>
                @endif
            </div>
        </article>
    </div>
</main>
@endsection
