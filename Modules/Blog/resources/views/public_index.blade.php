@extends('layouts.app2')

@section('content')
<main class="py-5">
    <div class="container">
        @include('partials.adsense.ad-unit', [
            'slot' => 'blog_top',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'variant' => 'hero',
            'minHeight' => '120px',
            'label' => null,
        ])

        <div class="row g-4">
            @forelse($blogs as $blog)
                @php
                    $cover = $blog->cover_image ? route('blog.media.show', ['path' => $blog->cover_image]) : null;
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('blog.public.show', $blog) }}" class="text-decoration-none text-reset d-block h-100">
                        <div class="card blog-card h-100 shadow-sm border-0">
                            @if($cover)
                                <img src="{{ $cover }}" class="card-img-top" alt="{{ $blog->title }}" width="1200" height="675">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2 text-muted small">
                                    <span>{{ $blog->category->name ?? 'Genel' }}</span>
                                    <span>{{ optional($blog->created_at)->format('d.m.Y') }}</span>
                                </div>
                                <h5 class="card-title">{{ $blog->title }}</h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit(strip_tags($blog->content), 140) }}
                                </p>
                                <div class="mt-auto">
                                    <span class="btn btn-sm btn-outline-primary">
                                        Oku
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @if($loop->iteration % 6 === 0 && ! $loop->last)
                    <div class="col-12">
                        @include('partials.adsense.ad-unit', [
                            'slot' => 'blog_feed',
                            'class' => 'mx-auto',
                            'style' => 'max-width: 100%;',
                            'insStyle' => 'display:block; text-align:center;',
                            'layout' => 'in-article',
                            'format' => 'fluid',
                            'variant' => 'feed',
                            'minHeight' => '110px',
                            'label' => null,
                        ])
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">Henüz ilan yok.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $blogs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</main>

@endsection
