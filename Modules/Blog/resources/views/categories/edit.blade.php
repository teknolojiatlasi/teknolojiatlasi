@extends('app::layouts.admin')

@section('title','Kategori Düzenle')

@section('content')

<form method="POST"
      action="{{ route('blog.categories.update', $category) }}">
@csrf
@method('PUT')

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
            <option value="{{ $parent->id }}"
                {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
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
           value="{{ $category->name }}"
           required>
</div>

<button type="submit" class="btn btn-success">
    <i class="fa fa-refresh"></i>
    Güncelle
</button>

<a href="{{ route('blog.categories.index') }}"
   class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i>
    İptal
</a>

</form>

@endsection
