@extends('layouts.admin')

@section('title', 'Sosial Etiket Düzenle')

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Etiket Düzenle #{{ $tag->id }}</h3>
    </div>
</div>

<div class="clearfix"></div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-8 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Etiket Bilgileri</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form method="POST" action="{{ route('admin.sossial.tags.update', $tag) }}">
                    @csrf
                    @method('PUT')
                    @include('sossial::admin.tags._form')

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-success">Güncelle</button>
                        <a href="{{ route('admin.sossial.tags.index') }}" class="btn btn-secondary">Listeye Dön</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Özet</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <p><strong>Bağlı post:</strong> {{ $tag->posts_count }}</p>
                <p><strong>Sayfa:</strong> <a href="{{ route('sosial.tags.show', $tag) }}" target="_blank" rel="noopener">Etiketi aç</a></p>
                <form method="POST" action="{{ route('admin.sossial.tags.destroy', $tag) }}" onsubmit="return confirm('Bu etiket silinsin mi?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Etiketi Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
