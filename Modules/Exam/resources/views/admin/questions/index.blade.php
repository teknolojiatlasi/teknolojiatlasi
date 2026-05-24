@extends('exam::components.layouts.master')

@section('title', 'Sorular')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sınav Soruları: {{ $exam->title }}</h3>
                    <a href="{{ route('exam.exams.questions.create', $exam->id) }}" class="btn btn-primary">Soru Oluştur</a>
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
                                    <th>#</th>
                                    <th>Soru</th>
                                    <th>Görsel</th>
                                    <th>Doğru Cevap</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $question)
                                    <tr>
                                        <td>{{ $question->order }}</td>
                                        <td>{{ Str::limit($question->question_text, 100) }}</td>
                                        <td>
                                            @if($question->image_path)
                                                <span class="badge bg-info">Var</span>
                                            @else
                                                <span class="badge bg-secondary">Yok</span>
                                            @endif
                                        </td>
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
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Soru bulunamadı.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('exam.exams.index') }}" class="btn btn-secondary">Sınavlara Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
