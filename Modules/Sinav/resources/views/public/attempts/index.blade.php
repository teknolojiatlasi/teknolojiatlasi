@extends('layouts.app2')

@section('title', 'Çözümlerim')

@push('styles')
<style>
    .attempts-shell .attempts-hero {
        padding: 2rem;
        border-radius: 1.8rem;
        background:
            radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 24%),
            radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.18), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #1e3a8a 64%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.18);
    }

    .attempts-shell .attempt-card,
    .attempts-shell .summary-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.45rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    }

    .attempts-shell .summary-value {
        display: block;
        font-size: 1.7rem;
        line-height: 1;
        font-weight: 900;
        color: #0f172a;
    }

    .attempts-shell .summary-label {
        color: #64748b;
        font-size: 0.88rem;
    }

    .attempts-shell .attempt-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .attempts-shell .attempt-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 26px 60px rgba(15, 23, 42, 0.12);
    }

    .attempts-shell .score-pill,
    .attempts-shell .metric-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.72rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .attempts-shell .score-pill {
        background: rgba(37, 99, 235, 0.1);
        color: #00fdf6;
    }

    .attempts-shell .metric-pill.ok { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .attempts-shell .metric-pill.bad { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .attempts-shell .metric-pill.blank { background: rgba(100, 116, 139, 0.12); color: #475569; }
</style>
@endpush

@section('content')
@php
    $totalAttempts = $attempts->total();
    $avgScore = $attempts->count() ? round($attempts->getCollection()->avg('score_percent')) : 0;
    $bestScore = $attempts->count() ? $attempts->getCollection()->max('score_percent') : 0;
@endphp

<main class="attempts-shell py-4 py-lg-5">
    <div class="container">
        @include('partials.adsense.ad-unit', [
            'slot' => 'sinav_top',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])
        <section class="attempts-hero mb-4 mb-lg-5">
            <div class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <div class="small text-uppercase fw-bold mb-2">Geçmiş sınav performansı</div>
                    <h1 class="display-6 fw-bold mb-3">Çözümlerim</h1>
                    <div class="text-white-50">
                        Çözdüğünüz testlerin geçmişini, puan dağılımını ve detay ekranlarını tek yerden yönetin.
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="btn btn-light rounded-pill px-4" href="{{ route('sinav.lessons.index') }}">Yeni Test Çöz</a>
                </div>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="summary-card p-4">
                    <span class="summary-value">{{ $totalAttempts }}</span>
                    <span class="summary-label">toplam çözüm</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card p-4">
                    <span class="summary-value">{{ $avgScore }}%</span>
                    <span class="summary-label">ortalama puan</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card p-4">
                    <span class="summary-value">{{ $bestScore }}%</span>
                    <span class="summary-label">en iyi sonuç</span>
                </div>
            </div>
        </div>

        @if($attempts->count())
            <div class="d-grid gap-3">
                @foreach ($attempts as $attempt)
                    <article class="attempt-card p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <div class="small text-uppercase fw-bold text-primary mb-2">
                                    {{ $attempt->created_at->format('d.m.Y H:i') }}
                                </div>
                                <h2 class="h5 fw-bold mb-1">{{ $attempt->test->title }}</h2>
                                <div class="text-muted small mb-3">
                                    {{ $attempt->test->topic->lesson->name }} · {{ $attempt->test->topic->title }}
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="score-pill"><i class="fa fa-line-chart"></i> {{ $attempt->score_percent }}% puan</span>
                                    <span class="metric-pill ok"><i class="fa fa-check"></i> {{ $attempt->correct_count }} doğru</span>
                                    <span class="metric-pill bad"><i class="fa fa-times"></i> {{ $attempt->wrong_count }} yanlış</span>
                                    <span class="metric-pill blank"><i class="fa fa-minus"></i> {{ $attempt->blank_count }} boş</span>
                                </div>
                            </div>

                            <div class="d-flex flex-column align-items-lg-end justify-content-between gap-3">
                                <div class="text-muted small">
                                    Süre: {{ gmdate('i:s', (int) $attempt->duration_seconds) }}
                                </div>
                                <a class="btn btn-primary rounded-pill px-4" href="{{ route('sinav.attempts.show', $attempt) }}">
                                    Detayı Aç
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $attempts->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="summary-card p-5 text-center text-muted">
                Henüz çözüm geçmişiniz yok. İlk testi çözerek bu alanı doldurabilirsiniz.
            </div>
        @endif
        @include('partials.adsense.ad-unit', [
            'slot' => 'sinav_bottom',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])
    </div>
</main>
@endsection
