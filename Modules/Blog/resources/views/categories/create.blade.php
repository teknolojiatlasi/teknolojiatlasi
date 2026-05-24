@extends('app::layouts.admin')

@section('title','Kategori Oluştur')

@section('content')

<form method="POST" action="{{ route('blog.categories.store') }}">
@csrf

<div class="mb-3">
    <label>
        <i class="fa fa-sitemap"></i>
        Üst Kategori
    </label>
    <select name="parent_id" class="form-control">
        <option value="">
            — <i class="fa fa-ban"></i> Yok —
        </option>
        @foreach($parents as $parent)
            <option value="{{ $parent->id }}">
                {{ $parent->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>
        <i class="fa fa-tag"></i>
        Kategori Adı
    </label>
    <input type="text"
           name="name"
           class="form-control"
           placeholder="Kategori adı giriniz"
           required>
</div>

<button type="submit" class="btn btn-success">
    <i class="fa fa-save"></i>
    Kaydet
</button>

<a href="{{ route('blog.categories.index') }}"
   class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i>
    İptal
</a>

</form>

@endsection
