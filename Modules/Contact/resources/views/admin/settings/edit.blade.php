@extends('app::layouts.admin')

@section('title', 'İletişim - Ayarlar')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <h2>Ayarlar</h2>
            <a class="btn btn-secondary" href="{{ route('contact_admin_messages_index') }}">
                <i class="fa fa-arrow-left"></i> Mesajlar
            </a>
        </div>

        <div class="x_content">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="alert alert-success d-none" id="settingsOk"></div>
            <div class="alert alert-danger d-none" id="settingsError"></div>

            <form id="settingsForm" method="POST" action="{{ route('contact_admin_settings_update') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Firma Adı</label>
                        <input class="form-control" name="contact_company_name" value="{{ old('contact_company_name', $settings->contact_company_name) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">İletişim E-postası</label>
                        <input class="form-control" name="contact_email" type="email" value="{{ old('contact_email', $settings->contact_email) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Telefon</label>
                        <input class="form-control" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">İl</label>
                        <input class="form-control" name="contact_city" id="contactCity" value="{{ old('contact_city', $settings->contact_city) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">İlçe (Semt)</label>
                        <input class="form-control" name="contact_district" id="contactDistrict" value="{{ old('contact_district', $settings->contact_district) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Adres</label>
                        <input class="form-control" name="contact_address" id="contactAddress" value="{{ old('contact_address', $settings->contact_address) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Enlem (Lat)</label>
                        <input class="form-control" name="contact_lat" id="contactLat" value="{{ old('contact_lat', $settings->contact_lat) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Boylam (Lng)</label>
                        <input class="form-control" name="contact_lng" id="contactLng" value="{{ old('contact_lng', $settings->contact_lng) }}">
                    </div>

                    <div class="col-12">
                        <div class="mb-2 d-flex gap-2">
                            <button class="btn btn-outline-primary" type="button" id="geocodeBtn">
                                <i class="fa fa-map-marker-alt"></i> Adresten Koordinat Bul
                            </button>
                            <div class="small text-muted d-flex align-items-center">
                                İl / ilçe / adres bilgisi ile OSM (Nominatim) üzerinden arama yapar.
                            </div>
                        </div>
                        <div class="ratio ratio-16x9 rounded overflow-hidden bg-light">
                            <div id="settingsMap" style="width: 100%; height: 100%"></div>
                        </div>
                        <div class="small text-muted mt-2">Haritada tıklayarak koordinat seçebilirsiniz.</div>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit" id="settingsSaveBtn">
                            <i class="fa fa-save"></i> Kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/leaflet-CIGW-MKW.css') }}">
@endpush

@push('scripts')
    <script type="module">
        import { L as Leaflet } from "{{ asset('vendor/gentelella/js/leaflet-DPwY-ags.js') }}";

        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const formEl = document.getElementById('settingsForm');
            const saveBtn = document.getElementById('settingsSaveBtn');
            const okEl = document.getElementById('settingsOk');
            const errorEl = document.getElementById('settingsError');

            const cityEl = document.getElementById('contactCity');
            const districtEl = document.getElementById('contactDistrict');
            const addressEl = document.getElementById('contactAddress');
            const latEl = document.getElementById('contactLat');
            const lngEl = document.getElementById('contactLng');
            const geocodeBtn = document.getElementById('geocodeBtn');

            function setAlert(el, text) {
                el.textContent = text || '';
                if (text) el.classList.remove('d-none');
                else el.classList.add('d-none');
            }

            async function send(url, method, payload) {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: payload,
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const err = new Error(json.message || 'İşlem başarısız');
                    err.status = res.status;
                    err.payload = json;
                    throw err;
                }
                return json;
            }

            formEl.addEventListener('submit', async (e) => {
                e.preventDefault();
                setAlert(okEl, '');
                setAlert(errorEl, '');
                saveBtn.disabled = true;

                try {
                    const json = await send(formEl.action, 'POST', new FormData(formEl));
                    setAlert(okEl, json.message || 'Ayarlar kaydedildi.');
                } catch (err) {
                    setAlert(errorEl, err.message || 'Bir hata oluştu.');
                } finally {
                    saveBtn.disabled = false;
                }
            });

            let map = null;
            let marker = null;
            const mapEl = document.getElementById('settingsMap');

            function initMap() {
                if (!mapEl || !Leaflet) return;
                const lat = Number(latEl.value);
                const lng = Number(lngEl.value);
                const hasCoords = Number.isFinite(lat) && Number.isFinite(lng) && (lat !== 0 || lng !== 0);
                const center = hasCoords ? [lat, lng] : [39.0, 35.0];
                const zoom = hasCoords ? 15 : 5;

                map = Leaflet.map(mapEl, { scrollWheelZoom: false }).setView(center, zoom);
                Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap katkıda bulunanlar',
                }).addTo(map);

                if (hasCoords) {
                    marker = Leaflet.marker(center).addTo(map);
                }

                map.on('click', (e) => {
                    const { lat, lng } = e.latlng;
                    latEl.value = lat.toFixed(7);
                    lngEl.value = lng.toFixed(7);
                    if (!marker) marker = Leaflet.marker([lat, lng]).addTo(map);
                    marker.setLatLng([lat, lng]);
                });
            }

            function updateMarkerFromInputs() {
                if (!map) return;
                const lat = Number(latEl.value);
                const lng = Number(lngEl.value);
                const hasCoords = Number.isFinite(lat) && Number.isFinite(lng);
                if (!hasCoords) return;
                const center = [lat, lng];
                map.setView(center, 15);
                if (!marker) marker = Leaflet.marker(center).addTo(map);
                marker.setLatLng(center);
            }

            async function geocode() {
                const q = [addressEl.value, districtEl.value, cityEl.value].filter(Boolean).join(', ');
                if (!q) {
                    setAlert(errorEl, 'Lütfen il / ilçe / adres bilgisini girin.');
                    return;
                }

                setAlert(errorEl, '');
                setAlert(okEl, '');
                geocodeBtn.disabled = true;
                geocodeBtn.textContent = 'Aranıyor...';

                try {
                    const url = new URL('https://nominatim.openstreetmap.org/search');
                    url.searchParams.set('format', 'json');
                    url.searchParams.set('limit', '1');
                    url.searchParams.set('q', q);

                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
                    const json = await res.json().catch(() => ([]));
                    const hit = Array.isArray(json) ? json[0] : null;
                    if (!hit) throw new Error('Sonuç bulunamadı. Adresi kontrol edin.');

                    latEl.value = Number(hit.lat).toFixed(7);
                    lngEl.value = Number(hit.lon).toFixed(7);
                    updateMarkerFromInputs();
                    setAlert(okEl, 'Koordinatlar güncellendi.');
                } catch (err) {
                    setAlert(errorEl, err.message || 'Geocoding başarısız.');
                } finally {
                    geocodeBtn.disabled = false;
                    geocodeBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i> Adresten Koordinat Bul';
                }
            }

            geocodeBtn.addEventListener('click', geocode);
            latEl.addEventListener('change', updateMarkerFromInputs);
            lngEl.addEventListener('change', updateMarkerFromInputs);

            initMap();
        })();
    </script>
@endpush
