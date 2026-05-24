@extends('layouts.admin')

@section('title', 'Yeni Sosial Post')

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Yeni Sosial Post</h3>
    </div>
</div>

<div class="clearfix"></div>

<div class="x_panel">
    <div class="x_title">
        <h2>Post Bilgileri</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form method="POST" action="{{ route('admin.sossial.posts.store') }}" enctype="multipart/form-data">
            @csrf
            @include('sossial::admin.posts._form')

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-success">Kaydet</button>
                <a href="{{ route('admin.sossial.posts.index') }}" class="btn btn-secondary">Geri Dön</a>
            </div>
        </form>
    </div>
</div>
@endsection
