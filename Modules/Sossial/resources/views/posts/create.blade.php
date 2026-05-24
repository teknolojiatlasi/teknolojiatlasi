@php
    $typeLabels = [
        'interview' => 'Mülakat deneyimi',
        'advice' => 'Kariyer tavsiyesi',
        'company' => 'Şirket / pozisyon deneyimi',
        'ilan' => 'İlan paylaşım',
    ];
@endphp

<x-sossial::layouts.master :title="'Gönderi Oluştur'">
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="sosial-surface sosial-panel">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                    <div>
                        <h2 class="sosial-panel-title mb-1">Gönderi oluştur</h2>
                        <p class="sosial-panel-copy">Paylaşımını yaz, etiketlerini ekle ve akışta yayınla.</p>
                    </div>
                    <a class="btn sosial-btn-secondary" href="{{ route('sosial.feed') }}">Akışa Dön</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('sosial.posts.store') }}" enctype="multipart/form-data" class="sosial-stack">
                    @csrf
                    @include('partials.bot-protection')

                    <div>
                        <div class="sosial-composer-label">
                            <label class="mb-0">Paylaşım türü</label>
                            <span class="sosial-counter">Tek seçim</span>
                        </div>
                        <select class="form-select sosial-form-select" name="type" required>
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', 'interview') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <div class="sosial-composer-label">
                            <label class="mb-0" for="createPostBody">İçerik</label>
                            <span class="sosial-counter"><span id="createPostBodyCount">0</span> karakter</span>
                        </div>
                        <textarea
                            id="createPostBody"
                            class="form-control sosial-form-control"
                            name="body"
                            rows="9"
                            required
                            placeholder="Deneyimini net, okunur ve faydalı şekilde yaz."
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="sosial-composer-label">
                                <label class="mb-0">Etiketler</label>
                                <span class="sosial-counter">Virgülle ayır</span>
                            </div>
                            <input class="form-control sosial-form-control" name="tags" value="{{ old('tags') }}" placeholder="ör: laravel, mülakat, remote">
                            @error('tags')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="sosial-composer-label">
                                <label class="mb-0">Bağlantı</label>
                                <span class="sosial-counter">İsteğe bağlı</span>
                            </div>
                            <input class="form-control sosial-form-control" name="link_url" value="{{ old('link_url') }}" placeholder="https://...">
                            @error('link_url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="sosial-composer-label">
                                <label class="mb-0">Görsel</label>
                                <span class="sosial-counter">En fazla 20 resim</span>
                            </div>
                            <input class="form-control sosial-form-control" name="images[]" type="file" accept="image/*" multiple>
                            @error('images')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn sosial-btn-primary" type="submit">Gönderiyi Yayınla</button>
                        <a class="btn sosial-btn-ghost" href="{{ route('sosial.feed') }}">Vazgeç</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const bodyEl = document.getElementById('createPostBody');
                const countEl = document.getElementById('createPostBodyCount');
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
