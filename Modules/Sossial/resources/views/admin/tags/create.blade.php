@extends('layouts.admin')

@section('title', 'Yeni Sosial Tag')

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Yeni Tag</h3>
    </div>
</div>

<div class="clearfix"></div>

<div class="x_panel">
    <div class="x_title">
        <h2>Tag Bilgileri</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form method="POST" action="{{ route('admin.sossial.tags.store') }}">
            @csrf
            @include('sossial::admin.tags._form')

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-success">Kaydet</button>
                <a href="{{ route('admin.sossial.tags.index') }}" class="btn btn-secondary">Geri Dön</a>
            </div>
        </form>
    </div>
</div>
@endsection
