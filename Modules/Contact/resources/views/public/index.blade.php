@extends('layouts.app2')

@section('title', 'İletişim')

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/leaflet-CIGW-MKW.css') }}">
    <style>
        .contact-shell .contact-hero {
            padding: 2rem;
            border-radius: 1.8rem;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, 0.18), transparent 24%),
                radial-gradient(circle at bottom left, rgba(245, 158, 11, 0.16), transparent 28%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 64%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
        }

        .contact-shell .contact-card,
        .contact-shell .contact-info-card {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1.5rem;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .contact-shell .contact-info-card {
            min-height: 100%;
        }

        .contact-shell .info-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.1);
            color: #1d4ed8;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .contact-shell .contact-info-item + .contact-info-item {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(148, 163, 184, 0.16);
        }

        .contact-shell .contact-form .form-control {
            min-height: 50px;
            border-radius: 1rem;
            border-color: rgba(148, 163, 184, 0.24);
            background: #fff;
        }

        .contact-shell .contact-form textarea.form-control {
            min-height: 180px;
            resize: vertical;
        }

        .contact-shell .contact-form .form-label {
            font-weight: 700;
            color: #0f172a;
        }

        .contact-shell .contact-map-shell {
            border-radius: 1.2rem;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: #f8fafc;
            min-height: 240px;
        }

        .contact-shell .contact-map-caption {
            color: #64748b;
            font-size: 0.88rem;
        }

        .contact-shell [data-error] {
            display: block;
            min-height: 1.25rem;
        }
    </style>
@endpush

@section('content')
<main class="contact-shell py-4 py-lg-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="contact-info-card p-4 h-100">
                    <div class="small text-uppercase fw-bold text-primary mb-2">Bilgiler</div>
                    <h2 class="h4 fw-bold mb-4">Bize ulaşın</h2>

                    <div class="contact-info-item">
                        <div class="text-muted small mb-1">Kurum</div>
                        <div class="fw-semibold">{{ $settings->contact_company_name ?: config('app.name') }}</div>
                    </div>

                    @if ($settings->displayAddress())
                        <div class="contact-info-item">
                            <div class="text-muted small mb-1">Adres</div>
                            <div class="fw-semibold">{{ $settings->displayAddress() }}</div>
                        </div>
                    @endif

                    @if ($settings->contact_phone)
                        <div class="contact-info-item">
                            <div class="text-muted small mb-1">Telefon</div>
                            <div class="fw-semibold">{{ $settings->contact_phone }}</div>
                        </div>
                    @endif

                    @if ($settings->contact_email)
                        <div class="contact-info-item">
                            <div class="text-muted small mb-1">E-posta</div>
                            <div class="fw-semibold">{{ $settings->contact_email }}</div>
                        </div>
                    @endif

                    <div class="contact-info-item">
                        <div class="text-muted small mb-3">Konum</div>
                        <div class="contact-map-shell ratio ratio-16x9">
                            <div id="contactMap" style="width: 100%; height: 100%"></div>
                        </div>
                        <div class="contact-map-caption mt-2">
                            Harita kayıtlı adres koordinatlarına göre gösterilir.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="contact-card p-4 p-lg-5">
                    <div class="small text-uppercase fw-bold text-primary mb-2">Mesaj formu</div>
                    <h2 class="h3 fw-bold mb-3">Bize yazın</h2>
                    <p class="text-muted mb-4">
                        Mesajınız doğrudan iletişim modülüne kaydedilir. Eksik alan varsa form üzerinde anında gösterilir.
                    </p>

                    <div class="alert alert-success d-none" id="contactSuccess"></div>
                    <div class="alert alert-danger d-none" id="contactError"></div>

                    <form id="contactForm" class="contact-form">
                        @csrf
                        <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="d-none">
                        @include('partials.bot-protection')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Ad Soyad</label>
                                <input class="form-control" name="contact_full_name" required>
                                <div class="text-danger small mt-1" data-error="contact_full_name"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">E-posta</label>
                                <input class="form-control" name="contact_email" type="email" required>
                                <div class="text-danger small mt-1" data-error="contact_email"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Konu</label>
                                <input class="form-control" name="contact_subject" required>
                                <div class="text-danger small mt-1" data-error="contact_subject"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mesaj</label>
                                <textarea class="form-control" name="contact_message" rows="6" required></textarea>
                                <div class="text-danger small mt-1" data-error="contact_message"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-primary rounded-pill px-4 py-2" type="submit" id="contactSubmitBtn">
                                    Mesajı Gönder
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/gentelella/js/leaflet-DPwY-ags.js') }}" defer></script>
    <script>
        (function () {
            const formEl = document.getElementById('contactForm');
            const submitBtn = document.getElementById('contactSubmitBtn');
            const successEl = document.getElementById('contactSuccess');
            const errorEl = document.getElementById('contactError');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            function clearAlerts() {
                successEl.classList.add('d-none');
                successEl.textContent = '';
                errorEl.classList.add('d-none');
                errorEl.textContent = '';
            }

            function clearErrors() {
                formEl.querySelectorAll('[data-error]').forEach(el => el.textContent = '');
            }

            function setFieldErrors(errors) {
                Object.entries(errors || {}).forEach(([key, messages]) => {
                    const el = formEl.querySelector(`[data-error="${key}"]`);
                    if (el) el.textContent = (messages || []).join(' ');
                });
            }

            async function submitForm(e) {
                e.preventDefault();
                clearAlerts();
                clearErrors();
                submitBtn.disabled = true;

                try {
                    const res = await fetch(@json(route('contact_public_store')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: new FormData(formEl),
                    });

                    const json = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        if (res.status === 422) {
                            setFieldErrors(json.errors);
                            return;
                        }
                        throw new Error(json.message || 'İşlem başarısız');
                    }

                    formEl.reset();
                    window.botProtection?.reset(formEl);
                    successEl.textContent = json.message || 'Mesajınız alındı.';
                    successEl.classList.remove('d-none');
                } catch (err) {
                    errorEl.textContent = err.message || 'Bir hata oluştu.';
                    errorEl.classList.remove('d-none');
                } finally {
                    submitBtn.disabled = false;
                }
            }

            formEl.addEventListener('submit', submitForm);

            const mapEl = document.getElementById('contactMap');
            if (mapEl && window.L) {
                const lat = Number(@json($settings->contact_lat));
                const lng = Number(@json($settings->contact_lng));
                const hasCoords = Number.isFinite(lat) && Number.isFinite(lng) && (lat !== 0 || lng !== 0);
                const center = hasCoords ? [lat, lng] : [39.0, 35.0];

                const map = L.map(mapEl, { scrollWheelZoom: false }).setView(center, hasCoords ? 15 : 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap katkıda bulunanlar',
                }).addTo(map);

                if (hasCoords) {
                    L.marker([lat, lng]).addTo(map);
                }
            }
        })();
    </script>
@endpush
