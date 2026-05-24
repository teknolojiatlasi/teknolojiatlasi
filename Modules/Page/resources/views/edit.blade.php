@extends('app::layouts.admin')

@section('title', 'Sayfa Düzenle')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Sayfa Düzenle</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form method="POST" action="{{ route('page.update', $page) }}">
            @method('PUT')
            @include('page::_form')

            <button type="submit" class="btn btn-success">Güncelle</button>
            <a href="{{ route('page.index') }}" class="btn btn-secondary">İptal</a>
        </form>
    </div>
</div>
@endsection
