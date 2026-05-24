@extends('layouts.admin')

@section('title', 'Sosial Post Düzenle')

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Post Düzenle #{{ $post->id }}</h3>
    </div>
</div>

<div class="clearfix"></div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="x_panel">
    <div class="x_title">
        <h2>Post Bilgileri</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form method="POST" action="{{ route('admin.sossial.posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('sossial::admin.posts._form')

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-success">Güncelle</button>
                <a href="{{ route('admin.sossial.posts.index') }}" class="btn btn-secondary">Listeye Dön</a>
            </div>
        </form>
    </div>
</div>
@endsection
