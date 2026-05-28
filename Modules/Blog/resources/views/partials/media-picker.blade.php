@php
    $selectedMediaId = old('cover_media_id', $selectedMediaId ?? null);
    $previewUrl = old('cover_media_preview_url', $previewUrl ?? null);
@endphp

<div class="mb-3">
    <label class="form-label">Kapak Resmi</label>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="border rounded p-3 h-100">
                <div class="form-check mb-2">
                    <input class="form-check-input"
                           type="radio"
                           name="cover_source"
                           id="coverSourceUpload"
                           value="upload"
                           @checked(old('cover_source', 'upload') === 'upload')>
                    <label class="form-check-label fw-semibold" for="coverSourceUpload">
                        Yeni kapak resmi yukle
                    </label>
                </div>
                <input type="file"
                       name="cover_image"
                       id="coverImageInput"
                       class="form-control @error('cover_image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">JPG, JPEG, PNG veya WebP. Maksimum 2 MB.</div>
                @error('cover_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="border rounded p-3 h-100">
                <div class="form-check mb-2">
                    <input class="form-check-input"
                           type="radio"
                           name="cover_source"
                           id="coverSourceLibrary"
                           value="library"
                           @checked(old('cover_source') === 'library' || filled($selectedMediaId))>
                    <label class="form-check-label fw-semibold" for="coverSourceLibrary">
                        Daha once yuklenen resimlerden sec
                    </label>
                </div>

                <input type="hidden" name="cover_media_id" id="coverMediaId" value="{{ $selectedMediaId }}">
                <input type="hidden" name="cover_media_preview_url" id="coverMediaPreviewUrl" value="{{ $previewUrl }}">

                <button type="button"
                        class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#mediaLibraryModal">
                    Medya Kutuphanesinden Sec
                </button>

                @error('cover_media_id')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mt-3" id="coverPreviewWrap" @if(! $previewUrl) style="display:none;" @endif>
        <div class="small text-muted mb-2">Secilen kapak onizlemesi</div>
        <img src="{{ $previewUrl }}"
             alt="Kapak onizlemesi"
             id="coverPreviewImage"
             class="img-thumbnail"
             style="max-height: 180px; object-fit: cover;">
    </div>
</div>

<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-labelledby="mediaLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaLibraryModalLabel">Medya Kutuphanesi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                @if($mediaItems->count())
                    <div class="row g-3">
                        @foreach($mediaItems as $media)
                            <div class="col-6 col-md-4 col-lg-3">
                                <button type="button"
                                        class="media-library-item btn p-0 border w-100 text-start {{ (string) $selectedMediaId === (string) $media->id ? 'border-primary border-3' : '' }}"
                                        data-media-id="{{ $media->id }}"
                                        data-media-url="{{ $media->url }}">
                                    <img src="{{ $media->url }}"
                                         alt="{{ $media->file_name }}"
                                         class="w-100 rounded-top"
                                         style="height: 140px; object-fit: cover;">
                                    <span class="d-block small text-truncate p-2">{{ $media->file_name }}</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">Medya kutuphanesinde henuz resim yok.</div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="confirmMediaSelection" data-bs-dismiss="modal">
                    Secimi Kullan
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .media-library-item {
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .media-library-item:hover,
    .media-library-item.is-selected {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mediaIdInput = document.getElementById('coverMediaId');
    const previewUrlInput = document.getElementById('coverMediaPreviewUrl');
    const previewWrap = document.getElementById('coverPreviewWrap');
    const previewImage = document.getElementById('coverPreviewImage');
    const uploadInput = document.getElementById('coverImageInput');
    const uploadRadio = document.getElementById('coverSourceUpload');
    const libraryRadio = document.getElementById('coverSourceLibrary');
    let selectedMediaId = mediaIdInput?.value || '';
    let selectedMediaUrl = previewUrlInput?.value || '';

    document.querySelectorAll('.media-library-item').forEach(function (button) {
        if (button.dataset.mediaId === selectedMediaId) {
            button.classList.add('is-selected');
        }

        button.addEventListener('click', function () {
            selectedMediaId = button.dataset.mediaId || '';
            selectedMediaUrl = button.dataset.mediaUrl || '';
            document.querySelectorAll('.media-library-item').forEach(function (item) {
                item.classList.remove('is-selected', 'border-primary', 'border-3');
            });
            button.classList.add('is-selected', 'border-primary', 'border-3');
        });
    });

    document.getElementById('confirmMediaSelection')?.addEventListener('click', function () {
        if (! selectedMediaId || ! mediaIdInput || ! previewImage || ! previewWrap) {
            return;
        }

        mediaIdInput.value = selectedMediaId;
        if (previewUrlInput) {
            previewUrlInput.value = selectedMediaUrl;
        }
        previewImage.src = selectedMediaUrl;
        previewWrap.style.display = '';
        if (libraryRadio) {
            libraryRadio.checked = true;
        }
        if (uploadInput) {
            uploadInput.value = '';
        }
    });

    uploadInput?.addEventListener('change', function () {
        if (! uploadInput.files || ! uploadInput.files[0]) {
            return;
        }

        if (uploadRadio) {
            uploadRadio.checked = true;
        }
        if (mediaIdInput) {
            mediaIdInput.value = '';
        }
        if (previewUrlInput) {
            previewUrlInput.value = '';
        }
        const reader = new FileReader();
        reader.onload = function (event) {
            if (previewImage && previewWrap) {
                previewImage.src = event.target.result;
                previewWrap.style.display = '';
            }
        };
        reader.readAsDataURL(uploadInput.files[0]);
    });
});
</script>
@endpush
