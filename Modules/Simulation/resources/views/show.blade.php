<div class="container py-4">
    <article>
        <header class="mb-4">
            <p class="text-muted">{{ $simulation->category?->name }}</p>
            <h1>{{ $simulation->title }}</h1>
            @if ($simulation->excerpt)
                <p>{{ $simulation->excerpt }}</p>
            @endif
        </header>

        <section class="mb-4">
            @if ($simulation->content_type === 'html')
                <div class="border rounded overflow-hidden bg-white">
                    {!! $renderParts['head'] ?? '' !!}
                    <div class="simulation-inline-render">
                        {!! $renderParts['body'] ?? '' !!}
                    </div>
                    {!! $renderParts['script'] ?? '' !!}
                </div>
            @elseif ($simulation->content_type === 'video' && $simulation->video_url)
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $simulation->video_url }}" title="{{ $simulation->title }}" allowfullscreen></iframe>
                </div>
            @elseif ($simulation->cover_image)
                <img src="{{ asset('storage/'.$simulation->cover_image) }}" alt="{{ $simulation->title }}" class="img-fluid rounded">
            @endif
        </section>

        @if ($simulation->content)
            <section>
                {!! $simulation->content !!}
            </section>
        @endif
    </article>
</div>
