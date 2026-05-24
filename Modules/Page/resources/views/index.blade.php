@extends('app::layouts.admin')

@section('title', 'Sayfalar')

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Sayfalar</h3>
    </div>
    <div class="title_right">
        <div class="float-end">
            <a href="{{ route('page.create') }}" class="btn btn-success">Yeni Sayfa</a>
        </div>
    </div>
</div>

<div class="clearfix"></div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="x_panel">
    <div class="x_title">
        <h2>Sayfa Listesi</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Slug</th>
                        <th>Link</th>
                        <th>Roller</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td>{{ $page->title }}</td>
                            <td>{{ $page->slug }}</td>
                            <td>
                                <a href="{{ route('page.show', $page) }}" target="_blank">
                                    {{ route('page.show', $page) }}
                                </a>
                            </td>
                            <td>{{ $page->roles->pluck('name')->implode(', ') }}</td>
                            <td>
                                @if($page->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('page.show', $page) }}" class="btn btn-sm btn-primary">Görüntüle</a>
                                <a href="{{ route('page.edit', $page) }}" class="btn btn-sm btn-warning">Düzenle</a>
                                <form method="POST" action="{{ route('page.destroy', $page) }}" onsubmit="return confirm('Bu sayfayı silmek istiyor musunuz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Henüz sayfa yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
