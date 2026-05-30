@php
    $tagList = $post->tags->pluck('name')->implode(', ');
    $typeLabels = [
        'interview' => 'Mülakat deneyimi',
        'advice' => 'Kariyer tavsiyesi',
        'company' => 'Şirket / pozisyon deneyimi',
        'ilan' => 'Yazı paylaşımı',
    ];
@endphp

<x-sossial::layouts.master :title="'Paylaşımı Düzenle'">
    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-8">
                <span class="sosial-kicker"><i class="fa fa-pencil-square-o"></i> Düzenleme</span>
                <h1 class="sosial-hero-title">Paylaşımını güncelle.</h1>
                <p class="sosial-hero-copy">İçeriği, etiketleri ve görselleri tek ekrandan yönetebilirsin.</p>
            </div>
            <div class="col-12 col-lg-4">
                <div class="sosial-hero-metrics">
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $typeLabels[$post->type] ?? ucfirst((string) $post->type) }}</span>
                        <span class="sosial-hero-metric-label">Mevcut tur</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $post->media->count() }}</span>
                        <span class="sosial-hero-metric-label">Görsel</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <a class="btn sosial-btn-secondary w-100" href="{{ route('sosial.posts.show', $post) }}">Paylaşıma Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="sosial-surface sosial-panel">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                    <div>
                        <h2 class="sosial-panel-title mb-1">İçeriği güncelle</h2>
                        <p class="sosial-panel-copy">Degisiklikleri kaydettikten sonra paylasim detay sayfasina donersin.</p>
                    </div>
                    <span class="sosial-chip"><i class="fa fa-save"></i> Düzenleme Modu</span>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('sosial.posts.update', $post) }}" enctype="multipart/form-data" class="sosial-stack">
                    @csrf
                    @method('PUT')

                    <div>
                        <div class="sosial-composer-label">
                            <label class="mb-0">Paylaşım türü</label>
                            <span class="sosial-counter">Tek seçim</span>
                        </div>
                        <select class="form-select sosial-form-select" name="type" required>
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $post->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="sosial-composer-label">
                            <label class="mb-0" for="editPostBody">İçerik</label>
                            <span class="sosial-counter"><span id="editPostBodyCount">0</span> karakter</span>
                        </div>
                        <textarea
                            id="editPostBody"
                            class="form-control sosial-form-control"
                            name="body"
                            rows="9"
                            required
                        >{{ old('body', $post->body) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="sosial-composer-label">
                                <label class="mb-0">Etiketler</label>
                                <span class="sosial-counter">Virgülle ayır</span>
                            </div>
                            <input class="form-control sosial-form-control" name="tags" value="{{ old('tags', $tagList) }}" placeholder="Örn: laravel, mülakat, remote">
                        </div>

                        <div class="col-12">
                            <div class="sosial-composer-label">
                                <label class="mb-0">Bağlantı</label>
                                <span class="sosial-counter">İsteğe bağlı</span>
                            </div>
                            <input class="form-control sosial-form-control" name="link_url" value="{{ old('link_url', $post->link_url) }}" placeholder="https://...">
                        </div>

                        <div class="col-12">
                            <div class="sosial-composer-label">
                                <label class="mb-0">Yeni gorsel ekle</label>
                                <span class="sosial-counter">Toplam en fazla 20 resim</span>
                            </div>
                            <input class="form-control sosial-form-control" name="images[]" type="file" accept="image/*" multiple>
                        </div>
                    </div>

                    @if ($post->media->isNotEmpty())
                        <div>
                            <div class="sosial-composer-label">
                                <label class="mb-0">Mevcut gorseller</label>
                                <span class="sosial-counter">Silmek istediklerini isaretle</span>
                            </div>
                            <div class="row g-3">
                                @foreach ($post->media as $media)
                                    @php
                                        $src = $media->url ?: ($media->path ? route('sosial.media.show', $media) : null);
                                    @endphp
                                    @if ($src)
                                        <div class="col-12 col-sm-6">
                                            <label class="d-block sosial-surface p-2">
                                                <img src="{{ $src }}" alt="Post gorseli" class="w-100 rounded-4 mb-2" style="height: 170px; object-fit: cover;">
                                                <span class="form-check d-flex align-items-center gap-2 m-0">
                                                    <input class="form-check-input" type="checkbox" name="remove_media[]" value="{{ $media->id }}">
                                                    <span class="form-check-label">Bu gorseli kaldir</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn sosial-btn-primary" type="submit">Degisiklikleri Kaydet</button>
                        <a class="btn sosial-btn-ghost" href="{{ route('sosial.posts.show', $post) }}">Vazgeç</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="sosial-surface sosial-panel">
                <div class="sosial-panel-title mb-2">Düzenleme notları</div>
                <div class="sosial-helper-grid">
                    <div class="sosial-helper-card">
                        <strong>İçerik</strong>
                        <span>Kısa paragraflar ve açık cümleler kullan.</span>
                    </div>
                    <div class="sosial-helper-card">
                        <strong>Etiket</strong>
                        <span>Aramayı güçlendirmek için ilgili etiketler ekle.</span>
                    </div>
                    <div class="sosial-helper-card">
                        <strong>Görsel</strong>
                        <span>İstemediğin görselleri kaldırıp yenilerini ekleyebilirsin.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const bodyEl = document.getElementById('editPostBody');
                const countEl = document.getElementById('editPostBodyCount');
                if (!bodyEl || !countEl) return;

                function syncCounter() {
                    countEl.textContent = String((bodyEl.value || '').trim().length);
                }

                bodyEl.addEventListener('input', syncCounter);
                syncCounter();
            })();
        </script>
    @endpush
</x-sossial::layouts.master>
