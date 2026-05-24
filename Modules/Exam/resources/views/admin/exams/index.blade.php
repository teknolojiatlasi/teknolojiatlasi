@extends('exam::components.layouts.master')

@section('title', 'Sınavlar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sınavlar</h3>
                    <a href="{{ route('exam.exams.create') }}" class="btn btn-primary">Sınav Oluştur</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Başlık</th>
                                    <th>Menü</th>
                                    <th>Soru</th>
                                    <th>Durum</th>
                                    <th>Oluşturulma</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $exam)
                                    <tr>
                                        <td>{{ $exam->title }}</td>
                                        <td>{{ $exam->menu->name }}</td>
                                        <td>{{ $exam->question_count }}</td>
                                        <td>
                                            @if($exam->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $exam->created_at ? $exam->created_at->format('d.m.Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('exam.exam-taking.show', $exam->id) }}" class="btn btn-sm btn-primary">Sınava Gir</a>
                                            <a href="{{ route('exam.exams.show', $exam->id) }}" class="btn btn-sm btn-info">Görüntüle</a>
                                            <a href="{{ route('exam.exams.edit', $exam->id) }}" class="btn btn-sm btn-warning">Düzenle</a>
                                            <a href="{{ route('exam.exams.questions.index', $exam->id) }}" class="btn btn-sm btn-secondary">Sorular</a>
                                            <form action="{{ route('exam.exams.destroy', $exam->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Bu sınavı silmek istediğinizden emin misiniz?')">
                                                    Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Sınav bulunamadı.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
