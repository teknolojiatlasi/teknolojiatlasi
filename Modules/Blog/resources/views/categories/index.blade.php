@extends('app::layouts.admin')

@section('title','Blog Kategorileri')

@section('content')

<a href="{{ route('blog.categories.create') }}"
   class="btn btn-primary mb-3">
    <i class="fa fa-plus"></i> Yeni Kategori
</a>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Kategori</th>
            <th>Üst Kategori</th>
            <th style="width:160px;">İşlem</th>
        </tr>
    </thead>
    <tbody>
    @foreach($categories as $category)
        <tr>
            <td>
                <i class="fa fa-folder"></i>
                {{ $category->name }}
            </td>
            <td>
                {{ $category->parent->name ?? '-' }}
            </td>
            <td>
                <a href="{{ route('blog.categories.edit',$category) }}"
                   class="btn btn-sm btn-warning">
                    <i class="fa fa-edit"></i>
                    Düzenle
                </a>

                <form action="{{ route('blog.categories.destroy', $category) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i>
                        Sil
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
