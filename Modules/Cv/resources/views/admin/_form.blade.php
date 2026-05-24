@php
    $cv = $cv ?? null;
    $isEdit = (bool) $cv;
    $action = $isEdit ? route('cv.update', $cv) : route('cv.store');
    $educations = old('educations', $isEdit ? $cv->educations->map(fn ($item) => [
        'school' => $item->school,
        'degree' => $item->degree,
        'year' => $item->year,
        'description' => $item->description,
    ])->toArray() : []);
    $experiences = old('experiences', $isEdit ? $cv->experiences->map(fn ($item) => [
        'company' => $item->company,
        'position' => $item->position,
        'start_date' => $item->start_date,
        'end_date' => $item->end_date,
        'description' => $item->description,
    ])->toArray() : []);
    $skills = old('skills', $isEdit ? $cv->skills->map(fn ($item) => [
        'name' => $item->name,
        'level' => $item->level,
    ])->toArray() : []);
@endphp

<style>
    .cv-builder .builder-hero {
        padding: 2rem;
        border-radius: 1.8rem;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, 0.18), transparent 24%),
            radial-gradient(circle at bottom left, rgba(245, 158, 11, 0.14), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
    }

    .cv-builder .builder-card,
    .cv-builder .repeat-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.45rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    }

    .cv-builder .section-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .cv-builder .form-label {
        font-weight: 700;
        color: #0f172a;
    }

    .cv-builder .form-control,
    .cv-builder .form-select {
        min-height: 50px;
        border-radius: 1rem;
        border-color: rgba(148, 163, 184, 0.24);
    }

    .cv-builder textarea.form-control {
        min-height: 140px;
        resize: vertical;
    }

    .cv-builder .repeat-card {
        padding: 1rem;
        position: relative;
    }

    .cv-builder .remove-item {
        position: absolute;
        top: 0.85rem;
        right: 0.85rem;
    }

    .cv-builder .photo-preview {
        width: 96px;
        height: 96px;
        border-radius: 1rem;
        object-fit: cover;
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: #f8fafc;
    }
</style>

<div class="cv-builder">
    <section class="builder-hero mb-4 mb-lg-5">
        <div class="row g-4 align-items-end">
            <div class="col-lg-8">
                <div class="small text-uppercase fw-bold mb-2">{{ $isEdit ? 'CV düzenleme' : 'CV oluşturucu' }}</div>
                <h1 class="display-6 fw-bold mb-3">{{ $isEdit ? 'CV bilgilerinizi güncelleyin.' : 'Modern ve uyumlu CV’nizi oluşturun.' }}</h1>
                <div class="text-white-50">
                    Temel bilgiler, eğitim, deneyim ve yetenek alanlarını tek formda yönetin. Form alanları artık backend ile birebir uyumlu çalışır.
                </div>
            </div>
            @if($isEdit)
                <div class="col-lg-4 text-lg-end d-flex flex-wrap justify-content-lg-end gap-2">
                    <a href="{{ route('cv.show', $cv) }}" class="btn btn-light rounded-pill px-4">Önizle</a>
                    <a href="{{ route('cv.pdf', $cv) }}" class="btn btn-outline-light rounded-pill px-4">PDF İndir</a>
                </div>
            @endif
        </div>
    </section>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="fw-semibold mb-2">Form hataları:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="cvBuilderForm">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="builder-card p-4 p-lg-5 mb-4">
            <div class="section-head">
                <div>
                    <div class="small text-uppercase fw-bold text-primary mb-1">Temel bilgiler</div>
                    <h2 class="h4 fw-bold mb-0">Profil alanı</h2>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Profil fotoğrafı</label>
                    @if($isEdit && $cv->photo)
                        <div class="mb-3">
                            <img src="{{ $cv->photo_url }}" alt="{{ $cv->full_name }}" class="photo-preview">
                        </div>
                    @endif
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $cv->full_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ünvan</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $cv->title ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-posta</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $cv->email ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $cv->phone ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Adres</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $cv->address ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Hakkımda</label>
                    <textarea name="about" class="form-control" rows="6">{{ old('about', $cv->about ?? '') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Şablon</label>
                    <select name="template" class="form-select">
                        @foreach (['modern' => 'Modern', 'classic' => 'Classic', 'blue' => 'Blue'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('template', $cv->template ?? 'modern') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="builder-card p-4 p-lg-5 mb-4">
            <div class="section-head">
                <div>
                    <div class="small text-uppercase fw-bold text-primary mb-1">Eğitim</div>
                    <h2 class="h4 fw-bold mb-0">Akademik geçmiş</h2>
                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-add="education">Eğitim Ekle</button>
            </div>
            <div id="educations-wrapper" class="d-grid gap-3"></div>
        </div>

        <div class="builder-card p-4 p-lg-5 mb-4">
            <div class="section-head">
                <div>
                    <div class="small text-uppercase fw-bold text-primary mb-1">Deneyim</div>
                    <h2 class="h4 fw-bold mb-0">İş deneyimleri</h2>
                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-add="experience">Deneyim Ekle</button>
            </div>
            <div id="experiences-wrapper" class="d-grid gap-3"></div>
        </div>

        <div class="builder-card p-4 p-lg-5 mb-4">
            <div class="section-head">
                <div>
                    <div class="small text-uppercase fw-bold text-primary mb-1">Yetenek</div>
                    <h2 class="h4 fw-bold mb-0">Uzmanlık alanları</h2>
                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-add="skill">Yetenek Ekle</button>
            </div>
            <div id="skills-wrapper" class="d-grid gap-3"></div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            @if($isEdit)
                <a href="{{ route('cv.show', $cv) }}" class="btn btn-outline-secondary rounded-pill px-4">Önizle</a>
            @endif
            <button class="btn btn-primary rounded-pill px-4 py-2">{{ $isEdit ? 'CV Güncelle' : 'CV Oluştur' }}</button>
        </div>
    </form>
</div>

<template id="education-template">
    <div class="repeat-card" data-item="education">
        <button type="button" class="btn-close remove-item" aria-label="Sil"></button>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Okul</label>
                <input type="text" class="form-control" data-field="school">
            </div>
            <div class="col-md-6">
                <label class="form-label">Bölüm / Derece</label>
                <input type="text" class="form-control" data-field="degree">
            </div>
            <div class="col-md-4">
                <label class="form-label">Yıl</label>
                <input type="text" class="form-control" data-field="year" placeholder="2024">
            </div>
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea class="form-control" data-field="description" rows="3"></textarea>
            </div>
        </div>
    </div>
</template>

<template id="experience-template">
    <div class="repeat-card" data-item="experience">
        <button type="button" class="btn-close remove-item" aria-label="Sil"></button>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Şirket</label>
                <input type="text" class="form-control" data-field="company">
            </div>
            <div class="col-md-6">
                <label class="form-label">Pozisyon</label>
                <input type="text" class="form-control" data-field="position">
            </div>
            <div class="col-md-6">
                <label class="form-label">Başlangıç</label>
                <input type="text" class="form-control" data-field="start_date" placeholder="01.2023">
            </div>
            <div class="col-md-6">
                <label class="form-label">Bitiş</label>
                <input type="text" class="form-control" data-field="end_date" placeholder="Devam ediyor">
            </div>
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea class="form-control" data-field="description" rows="4"></textarea>
            </div>
        </div>
    </div>
</template>

<template id="skill-template">
    <div class="repeat-card" data-item="skill">
        <button type="button" class="btn-close remove-item" aria-label="Sil"></button>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Yetenek</label>
                <input type="text" class="form-control" data-field="name">
            </div>
            <div class="col-md-4">
                <label class="form-label">Seviye</label>
                <select class="form-select" data-field="level">
                    <option value="1">1 / Başlangıç</option>
                    <option value="2">2 / Temel</option>
                    <option value="3">3 / İyi</option>
                    <option value="4">4 / İleri</option>
                </select>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    (() => {
        const existingData = {
            education: @json(array_values($educations)),
            experience: @json(array_values($experiences)),
            skill: @json(array_values($skills)),
        };

        const configs = {
            education: {
                wrapper: document.getElementById('educations-wrapper'),
                template: document.getElementById('education-template'),
                fields: ['school', 'degree', 'year', 'description'],
                blank: { school: '', degree: '', year: '', description: '' },
            },
            experience: {
                wrapper: document.getElementById('experiences-wrapper'),
                template: document.getElementById('experience-template'),
                fields: ['company', 'position', 'start_date', 'end_date', 'description'],
                blank: { company: '', position: '', start_date: '', end_date: '', description: '' },
            },
            skill: {
                wrapper: document.getElementById('skills-wrapper'),
                template: document.getElementById('skill-template'),
                fields: ['name', 'level'],
                blank: { name: '', level: 3 },
            },
        };

        const counters = { education: 0, experience: 0, skill: 0 };

        function syncNames(type) {
            const config = configs[type];
            Array.from(config.wrapper.querySelectorAll('[data-item]')).forEach((card, index) => {
                config.fields.forEach((field) => {
                    const input = card.querySelector(`[data-field="${field}"]`);
                    if (input) input.name = `${type}s[${index}][${field}]`;
                });
            });
            counters[type] = config.wrapper.querySelectorAll('[data-item]').length;
        }

        function addItem(type, values = null) {
            const config = configs[type];
            const fragment = config.template.content.firstElementChild.cloneNode(true);
            const payload = values || config.blank;

            config.fields.forEach((field) => {
                const input = fragment.querySelector(`[data-field="${field}"]`);
                if (!input) return;
                input.value = payload[field] ?? config.blank[field] ?? '';
            });

            fragment.querySelector('.remove-item').addEventListener('click', () => {
                fragment.remove();
                syncNames(type);
            });

            config.wrapper.appendChild(fragment);
            syncNames(type);
        }

        document.querySelectorAll('[data-add]').forEach((button) => {
            button.addEventListener('click', () => addItem(button.dataset.add));
        });

        Object.entries(existingData).forEach(([type, items]) => {
            if (items.length) {
                items.forEach((item) => addItem(type, item));
                return;
            }

            addItem(type);
        });
    })();
</script>
@endpush
