@php
    $statuses = config('simulation.statuses', ['draft', 'scheduled', 'published', 'archived']);
    $contentTypes = config('simulation.content_types', ['html', 'video', 'image']);
    $videoSources = config('simulation.video_sources', ['upload', 'youtube', 'vimeo']);
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="mb-3">
            <label class="form-label">Baslik</label>
            <input
                type="text"
                name="title"
                class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $simulation->title) }}"
                required
            >
            @error('title')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ozet</label>
            <textarea
                name="excerpt"
                rows="3"
                class="form-control @error('excerpt') is-invalid @enderror"
            >{{ old('excerpt', $simulation->excerpt) }}</textarea>
            @error('excerpt')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Konu Anlatimi</label>
            <textarea
                name="content"
                rows="8"
                class="form-control @error('content') is-invalid @enderror"
            >{{ old('content', $simulation->content) }}</textarea>
            @error('content')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <strong>Kod Editoru</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">HTML</label>
                    <textarea
                        name="html_code"
                        rows="10"
                        class="form-control font-monospace @error('html_code') is-invalid @enderror"
                    >{{ old('html_code', $simulation->html_code) }}</textarea>
                    @error('html_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">CSS</label>
                    <textarea
                        name="css_code"
                        rows="10"
                        class="form-control font-monospace @error('css_code') is-invalid @enderror"
                    >{{ old('css_code', $simulation->css_code) }}</textarea>
                    @error('css_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">JavaScript</label>
                    <textarea
                        name="js_code"
                        rows="10"
                        class="form-control font-monospace @error('js_code') is-invalid @enderror"
                    >{{ old('js_code', $simulation->js_code) }}</textarea>
                    @error('js_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">Versiyon Notu</label>
                    <input
                        type="text"
                        name="change_note"
                        class="form-control @error('change_note') is-invalid @enderror"
                        value="{{ old('change_note') }}"
                        placeholder="Bu duzende ne degisti?"
                    >
                    @error('change_note')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <strong>Yayin Ayarlari</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Kategori secin</option>
                        @foreach ($categories as $id => $label)
                            <option value="{{ $id }}" @selected((string) old('category_id', $simulation->category_id) === (string) $id)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Topic Path</label>
                    <input
                        type="text"
                        name="topic_path"
                        class="form-control @error('topic_path') is-invalid @enderror"
                        value="{{ old('topic_path', $simulation->topic_path) }}"
                        placeholder="fizik/hareket/newton-yasalari"
                    >
                    @error('topic_path')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Icerik Turu</label>
                    <select name="content_type" class="form-select @error('content_type') is-invalid @enderror">
                        @foreach ($contentTypes as $type)
                            <option value="{{ $type }}" @selected(old('content_type', $simulation->content_type) === $type)>
                                {{ strtoupper($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('content_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Durum</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $simulation->status) === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Yayin Tarihi</label>
                    <input
                        type="datetime-local"
                        name="published_at"
                        class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', optional($simulation->published_at)->format('Y-m-d\TH:i')) }}"
                    >
                    @error('published_at')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-0">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="1"
                        name="is_featured"
                        id="is_featured"
                        @checked(old('is_featured', $simulation->is_featured))
                    >
                    <label class="form-check-label" for="is_featured">One cikar</label>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <strong>Video ve Gorsel</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Video Kaynagi</label>
                    <select name="video_source" class="form-select @error('video_source') is-invalid @enderror">
                        <option value="">Secin</option>
                        @foreach ($videoSources as $source)
                            <option value="{{ $source }}" @selected(old('video_source', $simulation->video_source) === $source)>
                                {{ ucfirst($source) }}
                            </option>
                        @endforeach
                    </select>
                    @error('video_source')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Video URL</label>
                    <input
                        type="url"
                        name="video_url"
                        class="form-control @error('video_url') is-invalid @enderror"
                        value="{{ old('video_url', $simulation->video_url) }}"
                    >
                    @error('video_url')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kapak Gorseli</label>
                    <input
                        type="file"
                        name="cover_image"
                        class="form-control @error('cover_image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp"
                    >
                    @error('cover_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                @if ($simulation->cover_image)
                    <div class="mb-0">
                        <img src="{{ asset('storage/'.$simulation->cover_image) }}" alt="{{ $simulation->title }}" class="img-fluid rounded border">
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <strong>SEO</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">SEO Baslik</label>
                    <input
                        type="text"
                        name="seo_title"
                        class="form-control @error('seo_title') is-invalid @enderror"
                        value="{{ old('seo_title', $simulation->seo_title) }}"
                    >
                    @error('seo_title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">SEO Aciklama</label>
                    <textarea
                        name="seo_description"
                        rows="4"
                        class="form-control @error('seo_description') is-invalid @enderror"
                    >{{ old('seo_description', $simulation->seo_description) }}</textarea>
                    @error('seo_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-0">
                    <label class="form-label">SEO Anahtar Kelimeler</label>
                    <input
                        type="text"
                        name="seo_keywords"
                        class="form-control @error('seo_keywords') is-invalid @enderror"
                        value="{{ old('seo_keywords', $simulation->seo_keywords) }}"
                    >
                    @error('seo_keywords')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
