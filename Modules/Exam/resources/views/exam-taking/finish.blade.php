@extends('exam::components.layouts.master')

@section('title', 'Sınav Sonucu: ' . $exam->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sınav Sonucu: {{ $exam->title }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-4">
                                <h4>Puanınız</h4>
                                <div class="score-circle mx-auto mb-3">
                                    <span class="score-percentage">{{ $scorePercentage }}%</span>
                                </div>
                                <p class="lead">
                                    @if($scorePercentage >= 70)
                                        <span class="text-success">Tebrikler, başarılı oldunuz!</span>
                                    @else
                                        <span class="text-danger">Üzgünüz, başarılı olamadınız.</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Sınav Özeti</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Toplam Soru:</th>
                                    <td>{{ $totalQuestions }}</td>
                                </tr>
                                <tr>
                                    <th>Cevaplanan Soru:</th>
                                    <td>{{ $answeredQuestions }}</td>
                                </tr>
                                <tr>
                                    <th>Doğru Cevap:</th>
                                    <td>{{ $correctAnswers }}</td>
                                </tr>
                                <tr>
                                    <th>Yanlış Cevap:</th>
                                    <td>{{ $answeredQuestions - $correctAnswers }}</td>
                                </tr>
                                <tr>
                                    <th>Puan:</th>
                                    <td>{{ $scorePercentage }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('exam.exams.index') }}" class="btn btn-primary">Sınavlara Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: conic-gradient(
            {{ $scorePercentage >= 70 ? '#28a745' : '#dc3545' }} {{ $scorePercentage }}%, 
            #e9ecef {{ $scorePercentage }}%
        );
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .score-percentage {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
    }
</style>
@endsection
