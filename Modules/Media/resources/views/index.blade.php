@extends('app::layouts.admin')

@section('title', 'Medya Kutuphanesi')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Medya Kutuphanesi</h1>
            <p class="text-muted mb-0">Daha once yuklenen resimleri gorun ve yeni resim ekleyin.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label fw-semibold">Medya kutuphanesine resim ekle</label>
                <div class="row g-2 align-items-start">
                    <div class="col-12 col-lg-4">
                        <input type="file"
                               name="images[]"
                               class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.webp"
                               multiple
                               required>
                        <div class="form-text">JPG, JPEG, PNG veya WebP. Her dosya maksimum 2 MB. Tek seferde en fazla 20 dosya.</div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <input type="text"
                               name="collection"
                               class="form-control"
                               list="mediaCollectionList"
                               placeholder="Grup adi: Blog Kapaklari, Slider, Urunler...">
                        <datalist id="mediaCollectionList">
                            @foreach($collections as $collection)
                                <option value="{{ $collection }}"></option>
                            @endforeach
                        </datalist>
                        <div class="form-text">Resimleri sonradan kolay bulmak icin bir grup adi yazin.</div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <button type="submit" class="btn btn-primary w-100">Yukle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="GET" action="{{ route('media.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-6">
                    <label class="form-label">Ara</label>
                    <input type="search"
                           name="q"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Dosya adi, yol veya grup ara">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">Grup</label>
                    <select name="collection" class="form-control">
                        <option value="">Tum gruplar</option>
                        @foreach($collections as $collection)
                            <option value="{{ $collection }}" @selected($selectedCollection === $collection)>{{ $collection }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill" type="submit">Filtrele</button>
                    <a href="{{ route('media.index') }}" class="btn btn-outline-secondary">Temizle</a>
                </div>
            </div>
        </div>
    </form>

    @if($mediaItems->count())
        <div class="row g-3">
            @foreach($mediaItems as $media)
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card h-100">
                        <img src="{{ $media->url }}"
                             class="card-img-top"
                             alt="{{ $media->file_name }}"
                             style="height: 140px; object-fit: cover;">
                        <div class="card-body p-2">
                            <div class="small text-truncate" title="{{ $media->file_name }}">
                                {{ $media->file_name }}
                            </div>
                            <div class="text-muted small">
                                {{ number_format(($media->size ?? 0) / 1024, 1) }} KB
                            </div>
                            @if($media->collection)
                                <span class="badge bg-light text-dark border mt-1">{{ $media->collection }}</span>
                            @endif
                        </div>
                        <div class="card-footer bg-white p-2 d-flex gap-2">
                            <a href="{{ $media->url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary flex-fill">
                                Ac
                            </a>
                            <form method="POST"
                                  action="{{ route('media.destroy', $media) }}"
                                  class="flex-fill"
                                  onsubmit="return confirm('Bu resmi silmek istiyor musunuz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Sil</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $mediaItems->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="alert alert-info">Medya kutuphanesinde henuz resim yok.</div>
    @endif
@endsection
