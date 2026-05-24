@extends('exam::components.layouts.master')

@section('title', 'Sınav Detayı')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sınav Detayı: {{ $exam->title }}</h3>
                    <div>
                        <a href="{{ route('exam.exam-taking.show', $exam->id) }}" class="btn btn-primary">Sınava Gir</a>
                        <a href="{{ route('exam.exams.edit', $exam->id) }}" class="btn btn-warning">Düzenle</a>
                        <a href="{{ route('exam.exams.questions.index', $exam->id) }}" class="btn btn-secondary">Soruları Yönet</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Başlık:</th>
                                    <td>{{ $exam->title }}</td>
                                </tr>
                                <tr>
                                    <th>Menü:</th>
                                    <td>{{ $exam->menu->name }}</td>
                                </tr>
                                <tr>
                                    <th>Durum:</th>
                                    <td>
                                        @if($exam->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sorular:</th>
                                    <td>{{ $exam->question_count }}</td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma:</th>
                                    <td>{{ $exam->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme:</th>
                                    <td>{{ $exam->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Açıklama:</h5>
                            <p>{{ $exam->description ?? 'Açıklama girilmedi.' }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h4>Sorular ({{ $exam->question_count }})</h4>
                    @if($exam->questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Soru</th>
                                        <th>Doğru Cevap</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->questions as $question)
                                        <tr>
                                            <td>{{ $question->order }}</td>
                                            <td>{{ Str::limit($question->question_text, 100) }}</td>
                                            <td>{{ $question->correct_answer }}. {{ Str::limit($question->getCorrectOptionText(), 50) }}</td>
                                            <td>
                                                <a href="{{ route('exam.exams.questions.edit', [$exam->id, $question->id]) }}" class="btn btn-sm btn-warning">Düzenle</a>
                                                <form action="{{ route('exam.exams.questions.destroy', [$exam->id, $question->id]) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Bu soruyu silmek istediğinizden emin misiniz?')">
                                                        Sil
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Bu sınav için soru bulunamadı.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
