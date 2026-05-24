@extends('layouts.app2')

@section('title', 'Soru Platformu')

@push('styles')
<style>
    .lesson-index-shell .lesson-hero {
        padding: 2rem;
        border-radius: 1.8rem;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, 0.18), transparent 24%),
            radial-gradient(circle at bottom left, rgba(245, 158, 11, 0.16), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #1d4ed8 62%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
    }

    .lesson-index-shell .summary-card,
    .lesson-index-shell .lesson-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.45rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    }

    .lesson-index-shell .summary-value {
        display: block;
        font-size: 1.7rem;
        line-height: 1;
        font-weight: 900;
        color: #0f172a;
    }

    .lesson-index-shell .summary-label {
        color: #64748b;
        font-size: 0.88rem;
    }

    .lesson-index-shell .lesson-card {
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .lesson-index-shell .lesson-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 26px 60px rgba(15, 23, 42, 0.12);
    }

    .lesson-index-shell .topic-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0 0.45rem 0.45rem 0;
        padding: 0.42rem 0.72rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.8rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
@php
    $lessonCount = $lessons->count();
    $topicCount = $lessons->sum('topics_count');
    $testCount = $lessons->sum(fn ($lesson) => $lesson->topics->sum('tests_count'));
@endphp

<main class="lesson-index-shell py-4 py-lg-5">
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
        <section class="lesson-hero mb-4 mb-lg-5">
            <div class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <div class="small text-uppercase fw-bold mb-2">Sınav merkezi</div>
                    <h1 class="display-6 fw-bold mb-3">Soru Platformu</h1>
                    <div class="text-white-50">
                        Dersinizi seçin, konu ağacına girin ve online çözüm moduna geçin. Tüm sınav akışı tek arayüzde ilerler.
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @auth
                        <a href="{{ route('sinav.attempts.index') }}" class="btn btn-light rounded-pill px-4">Çözümlerim</a>
                    @endif
                </div>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="summary-card p-4">
                    <span class="summary-value">{{ $lessonCount }}</span>
                    <span class="summary-label">aktif ders</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card p-4">
                    <span class="summary-value">{{ $topicCount }}</span>
                    <span class="summary-label">konu başlığı</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card p-4">
                    <span class="summary-value">{{ $testCount }}</span>
                    <span class="summary-label">çözülebilir test</span>
                </div>
            </div>
        </div>

        @if($lessons->isNotEmpty())
            <div class="row g-4">
                @foreach ($lessons as $lesson)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="lesson-card p-4">
                            <div class="small text-uppercase fw-bold text-primary mb-2">Ders</div>
                            <h2 class="h4 fw-bold mb-2">{{ $lesson->name }}</h2>
                            <p class="text-muted mb-3">
                                {{ $lesson->description ? \Illuminate\Support\Str::limit($lesson->description, 130) : 'Bu ders içinde aktif konular ve çözülebilir testler yer alır.' }}
                            </p>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge text-bg-light">{{ $lesson->topics_count }} konu</span>
                                <span class="badge text-bg-light">{{ $lesson->topics->sum('tests_count') }} test</span>
                            </div>

                            <div class="mb-4">
                                @forelse ($lesson->topics->take(5) as $topic)
                                    <span class="topic-pill">
                                        <i class="fa fa-file-text-o"></i>
                                        {{ \Illuminate\Support\Str::limit($topic->title, 24) }}
                                        @if($topic->tests_count)
                                            {{ $topic->tests_count }}
                                        @endif
                                    </span>
                                @empty
                                    <div class="text-muted small">Henüz aktif konu eklenmemiş.</div>
                                @endforelse
                            </div>

                            <a class="btn btn-primary rounded-pill px-4" href="{{ route('sinav.lessons.show', $lesson) }}">
                                Dersi Aç
                            </a>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="summary-card p-5 text-center text-muted">
                Henüz aktif ders bulunmuyor.
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
