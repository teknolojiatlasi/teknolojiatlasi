@extends('layouts.app2')

@push('styles')
<style>
    .exam-shell {
        --exam-dark: #0f172a;
        --exam-blue: #2563eb;
        --exam-cyan: #06b6d4;
        --exam-emerald: #10b981;
        --exam-orange: #f59e0b;
        --exam-line: rgba(15, 23, 42, 0.08);
        --exam-surface: rgba(255, 255, 255, 0.92);
    }

    .exam-shell .exam-hero {
        position: relative;
        overflow: hidden;
        padding: 1.15rem 1.35rem;
        border-radius: 1.3rem;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, 0.14), transparent 22%),
            radial-gradient(circle at bottom left, rgba(245, 158, 11, 0.12), transparent 24%),
            linear-gradient(135deg, #0f172a 0%, #1d4ed8 62%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16);
    }

    .exam-shell .exam-hero-title {
        font-size: clamp(1.35rem, 2vw, 1.95rem);
        font-weight: 900;
        letter-spacing: -0.04em;
        line-height: 1.05;
        margin: 0.3rem 0 0;
    }

    .exam-shell .exam-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .exam-shell .exam-hero-copy {
        max-width: 56ch;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.45;
        font-size: 0.92rem;
        margin-bottom: 0;
    }

    .exam-shell .exam-stat {
        min-width: 88px;
    }

    .exam-shell .exam-stat-value {
        display: block;
        font-size: 1.18rem;
        line-height: 1;
        font-weight: 900;
    }

    .exam-shell .exam-stat-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.76rem;
    }

    .exam-shell .exam-panel,
    .exam-shell .exam-sidebar-card,
    .exam-shell .exam-empty-card {
        background: var(--exam-surface);
        border: 1px solid var(--exam-line);
        border-radius: 1.5rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(10px);
    }

    .exam-shell .exam-sidebar-card {
        position: sticky;
        top: 1rem;
        overflow: hidden;
    }

    .exam-shell .exam-sidebar-head {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.8), rgba(255, 255, 255, 0.95));
    }

    .exam-shell .exam-sidebar-body {
        padding: 1rem 1.15rem 1.15rem;
        max-height: calc(100vh - 8rem);
        overflow: auto;
    }

    .exam-shell .exam-empty-card {
        padding: 2rem;
        color: #64748b;
    }

    .exam-shell .exam-mini-note {
        color: #64748b;
        font-size: 0.9rem;
    }

    @media (min-width: 992px) {
        .exam-shell .exam-hero .row {
            flex-wrap: nowrap;
        }
    }

    @media (max-width: 991.98px) {
        .exam-shell .exam-hero {
            padding: 1rem 1.1rem;
        }
    }

    @media (max-width: 767.98px) {
        .exam-shell .exam-hero-title {
            font-size: 1.15rem;
        }

        .exam-shell .exam-hero-copy {
            font-size: 0.87rem;
        }

        .exam-shell .exam-stat {
            min-width: 76px;
        }

        .exam-shell .exam-stat-value {
            font-size: 1rem;
        }

        .exam-shell .exam-stat-label {
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $topicCount = collect($topicTree)->count();
    $testCount = collect($topicTree)->sum(function ($node) {
        $topicTests = $node['topic']->tests->count();
        $childTests = collect($node['children'] ?? [])->sum(function ($child) {
            return $child['topic']->tests->count();
        });

        return $topicTests + $childTests;
    });
@endphp

<main class="exam-shell py-4 py-lg-5">
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
        <section class="exam-hero mb-4 mb-lg-5">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <span class="exam-kicker"><i class="fa fa-laptop"></i> Online sınav modülü</span>
                    <h1 class="exam-hero-title">{{ $lesson->name }}</h1>
                    <p class="exam-hero-copy">
                        {{ $lesson->description ?: 'Konu ağacından test seçin, zaman yönetimiyle çözün ve sonuç ekranında performansınızı detaylı inceleyin.' }}
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-wrap gap-3 justify-content-lg-center">
                        <div class="exam-stat">
                            <span class="exam-stat-value">{{ $topicCount }}</span>
                            <span class="exam-stat-label">aktif konu</span>
                        </div>
                        <div class="exam-stat">
                            <span class="exam-stat-value">{{ $testCount }}</span>
                            <span class="exam-stat-label">test</span>
                        </div>
                        <div class="exam-stat">
                            <span class="exam-stat-value">{{ $activeTest ? $activeTest->questions->count() : 0 }}</span>
                            <span class="exam-stat-label">soru</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a class="btn btn-light rounded-pill px-3" href="{{ route('sinav.lessons.index') }}">Tüm Dersler</a>
                        @auth
                            <a class="btn btn-outline-light rounded-pill px-3" href="{{ route('sinav.attempts.index') }}">Çözümlerim</a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-12 d-lg-none">
                <button
                    class="btn btn-outline-primary w-100 rounded-pill py-2"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#sinavSidebar"
                    aria-controls="sinavSidebar"
                >
                    Konular ve Testler
                </button>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="sinavSidebar" aria-labelledby="sinavSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sinavSidebarLabel">Konular ve Testler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
                </div>
                <div class="offcanvas-body">
                    @if (empty($topicTree))
                        <div class="alert alert-info mb-0">Bu derse henüz konu eklenmemiş.</div>
                    @else
                        @include('sinav::public.partials.topic_tests_tree', ['nodes' => $topicTree, 'lesson' => $lesson, 'activeTestId' => $activeTestId])
                    @endif
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-block">
                <aside class="exam-sidebar-card">
                    <div class="exam-sidebar-head">
                        <div class="small text-uppercase fw-bold text-primary mb-1">Sınav ağacı</div>
                        <div class="h5 mb-1">Konular ve testler</div>
                        <div class="text-muted small">Test seçimi yaptığınız anda çözüm paneli sağ tarafta açılır.</div>
                    </div>
                    <div class="exam-sidebar-body">
                        @if (empty($topicTree))
                            <div class="alert alert-info mb-0">Bu derse henüz konu eklenmemiş.</div>
                        @else
                            @include('sinav::public.partials.topic_tests_tree', ['nodes' => $topicTree, 'lesson' => $lesson, 'activeTestId' => $activeTestId])
                        @endif
                    </div>
                </aside>
            </div>

            <div class="col-lg-8">
                <div id="solve"></div>

                @if ($activeTest)
                    <div class="exam-panel p-3 p-lg-4">
                        @include('sinav::public.tests._wizard', ['test' => $activeTest, 'backUrl' => route('sinav.lessons.show', $lesson)])
                    </div>
                @else
                    <div class="exam-empty-card">
                        <div class="small text-uppercase fw-bold text-primary mb-2">Hazır olduğunuzda başlayın</div>
                        <h2 class="h3 fw-bold mb-3">Sol taraftan bir test seçin.</h2>
                        <p class="mb-0">
                            Seçtiğiniz test burada online çözüm moduna dönüşecek. Süre takibi, soru ilerleme çubuğu ve sonuç ekranı otomatik açılacak.
                        </p>
                    </div>
                @endif

                <div class="exam-mini-note mt-3">
                    Giriş yapmadan test çözebilirsiniz. Oturum açarsanız sonuçlarınız çözümlerim ekranında saklanır.
                </div>
            </div>
        </div>
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
