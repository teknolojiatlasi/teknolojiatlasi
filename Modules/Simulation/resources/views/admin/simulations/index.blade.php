@extends('app::layouts.admin')

@section('title', 'Simulasyonlar')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Simulasyon Yonetimi</h2>
                <small class="text-muted">HTML, video ve gorsel tabanli icerikleri yonetin.</small>
            </div>
            <div>
                <a href="{{ route('simulation.admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fa fa-sitemap"></i> Kategoriler
                </a>
                <a href="{{ route('simulation.admin.simulations.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus-circle"></i> Yeni Simulasyon
                </a>
            </div>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Baslik</th>
                            <th>Kategori</th>
                            <th>Tur</th>
                            <th>Durum</th>
                            <th>Yayin</th>
                            <th style="width: 220px">Islem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($simulations as $simulation)
                            <tr>
                                <td>
                                    <strong>{{ $simulation->title }}</strong>
                                    @if ($simulation->excerpt)
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($simulation->excerpt, 90) }}</div>
                                    @endif
                                </td>
                                <td>{{ $simulation->category?->name ?: '-' }}</td>
                                <td><span class="badge bg-info">{{ strtoupper($simulation->content_type) }}</span></td>
                                <td><span class="badge bg-secondary">{{ $simulation->status }}</span></td>
                                <td>{{ optional($simulation->published_at)->format('d.m.Y H:i') ?: '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('simulation.show', $simulation->slug) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fa fa-eye"></i> Gor
                                    </a>
                                    <a href="{{ route('simulation.admin.simulations.edit', $simulation) }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i> Duzenle
                                    </a>
                                    <form action="{{ route('simulation.admin.simulations.destroy', $simulation) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Simulasyon silinsin mi?')">
                                            <i class="fa fa-trash"></i> Sil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Kayitli simulasyon yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $simulations->links() }}
            </div>
        </div>
    </div>
@endsection
