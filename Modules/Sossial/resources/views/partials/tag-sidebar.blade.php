<div class="sosial-sidebar">
    <div class="sosial-surface sosial-panel">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
                <h2 class="sosial-panel-title mb-1">Son Konuşulanlar</h2>
                <p class="sosial-panel-copy">En son paylaşım yapılan etiketler.</p>
            </div>
            <span class="sosial-chip"><i class="fa fa-clock-o"></i> Yeni</span>
        </div>

        <div class="sosial-trend-list">
            @forelse ($recentTags as $tag)
                @php
                    $lastUsedAt = $tag->last_used_at
                        ? \Illuminate\Support\Carbon::parse($tag->last_used_at)->diffForHumans()
                        : null;
                @endphp
                <a class="sosial-trend-item" href="{{ route('sosial.tags.show', $tag) }}">
                    <span class="sosial-trend-main">
                        <span class="sosial-trend-name">#{{ $tag->name }}</span>
                        <span class="sosial-trend-meta">{{ $lastUsedAt ?: 'Yeni paylaşım' }}</span>
                    </span>
                    <span class="sosial-trend-count">{{ $tag->posts_count }}</span>
                </a>
            @empty
                <div class="sosial-empty py-3">
                    <div class="sosial-empty-icon"><i class="fa fa-tags"></i></div>
                    <p class="mb-0">Henüz tag yok.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="sosial-surface sosial-panel">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
                <h2 class="sosial-panel-title mb-1">En Çok Konuşulan Konular</h2>
                <p class="sosial-panel-copy">En çok paylaşım yapılan etiketler.</p>
            </div>
            <span class="sosial-chip sosial-chip-soft"><i class="fa fa-fire"></i> Popüler</span>
        </div>

        <div class="sosial-trend-list">
            @forelse ($popularTags as $tag)
                <a class="sosial-trend-item" href="{{ route('sosial.tags.show', $tag) }}">
                    <span class="sosial-trend-main">
                        <span class="sosial-trend-name">#{{ $tag->name }}</span>
                        <span class="sosial-trend-meta">{{ $tag->posts_count }} paylaşımda kullanıldı</span>
                    </span>
                    <span class="sosial-trend-count">{{ $tag->posts_count }}</span>
                </a>
            @empty
                <div class="sosial-empty py-3">
                    <div class="sosial-empty-icon"><i class="fa fa-hashtag"></i></div>
                    <p class="mb-0">Henüz popüler tag yok.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
