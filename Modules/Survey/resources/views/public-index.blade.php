@extends('layouts.app2')

@section('title', 'Anketler')

@push('styles')
<style>
    .survey-hub {
        --shell-bg: linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
        --card-border: rgba(15, 23, 42, 0.08);
        --card-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }
    .survey-hub .hero-card,
    .survey-hub .section-card,
    .survey-hub .survey-list-card {
        border: 1px solid var(--card-border);
        box-shadow: var(--card-shadow);
    }
    .survey-hub .hero-card {
        background: linear-gradient(135deg, #0f172a, #1d4ed8 62%, #2563eb);
        color: #fff;
        border-radius: 2rem;
        overflow: hidden;
        position: relative;
        min-height: 252px;
    }
    .survey-hub .hero-card::after {
        content: "";
        position: absolute;
        inset: auto -80px -80px auto;
        width: 220px;
        height: 220px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        filter: blur(8px);
    }
    .survey-hub .hero-kicker,
    .survey-hub .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
    }
    .survey-hub .hero-kicker { background: rgba(245, 158, 11, .18); color: #fef3c7; }
    .survey-hub .section-card,
    .survey-hub .survey-list-card {
        background: rgba(255, 255, 255, 0.92);
        border-radius: 1.5rem;
    }
    .survey-hub .status-pill { background: #eff6ff; color: #1d4ed8; }
    .survey-hub .status-pill.muted { background: #f1f5f9; color: #475569; }
    .survey-hub .survey-list-card { transition: transform .2s ease, box-shadow .2s ease; }
    .survey-hub .survey-list-card:hover { transform: translateY(-4px); box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12); }
    .survey-hub .survey-list-card { min-height: 100%; }
    .survey-hub .metric-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .5rem .8rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #334155;
        font-size: .88rem;
        font-weight: 700;
    }
    .survey-hub .chart-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: linear-gradient(180deg, #f8fafc, #eef2ff);
    }
    .survey-hub .chart-wrap {
        max-width: 320px;
        margin: 0 auto;
    }
    .survey-hub .question-select-wrap {
        flex: 1 1 320px;
        max-width: 100%;
        min-width: 0;
    }
    .survey-hub #activeSurveyQuestionSelect {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .survey-hub .chart-wrap canvas {
        width: 100% !important;
        height: 260px !important;
    }
    .survey-hub .survey-description {
        color: #475569;
        line-height: 1.7;
        min-height: 4.8rem;
    }
    @media (max-width: 767.98px) {
        .survey-hub .hero-card { border-radius: 1.5rem; }
        .survey-hub .hero-card { min-height: 0; }
        .survey-hub .question-select-wrap { flex-basis: 100%; }
        .survey-hub .chart-wrap canvas { height: 200px !important; }
    }
</style>
@endpush

@section('content')
<div class="survey-hub py-4 py-lg-5" style="background: var(--shell-bg);">
    <div class="container">
        <section class="hero-card p-4 p-lg-5 mb-4">
            <div class="row g-4 align-items-center position-relative">
                <div class="col-lg-8">
                    <span class="hero-kicker"><i class="fa fa-bar-chart"></i> Anket Merkezi</span>
                    <h1 class="display-6 fw-bold mt-3 mb-3">Güncel ve önceki anketler</h1>
                    <p class="mb-0 text-white-50">Aktif ankete hemen katılabilir, önceki anketlerin sonuçlarını inceleyebilir ve açık kalan anketlere oy verebilirsiniz.</p>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <span class="metric-chip"><i class="fa fa-list"></i> Toplam: {{ $surveys->count() }}</span>
                        <span class="metric-chip"><i class="fa fa-bullseye"></i> Aktif: {{ $activeSurvey ? '1' : '0' }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-card p-4 p-lg-5 mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div>
                    <div class="text-uppercase small fw-bold text-primary mb-2">Güncel Anket</div>
                    <h2 class="h3 fw-bold mb-2">Şu anda yayında olan anket</h2>
                    <p class="text-secondary mb-0">Aktif anket varsa burada doğrudan oy verebilirsiniz.</p>
                </div>
                @if($activeSurvey)
                    <a href="{{ route('survey.public.show', $activeSurvey) }}" class="btn btn-outline-dark rounded-pill px-4">Anket sayfasına git</a>
                @endif
            </div>

            @if($activeSurvey)
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="status-pill"><i class="fa fa-bullseye"></i> Aktif</span>
                                <span class="metric-chip"><i class="fa fa-users"></i> Katılım: {{ $activeResponseCount }}</span>
                                <span class="metric-chip"><i class="fa fa-globe"></i> Herkese açık</span>
                            </div>
                            <h3 class="fw-bold mb-2">{{ $activeSurvey->title }}</h3>
                            @if($activeSurvey->description)
                                <p class="survey-description mb-0">{{ $activeSurvey->description }}</p>
                            @endif
                        </div>

                        <x-survey::form :survey="$activeSurvey" :action="route('survey.public.submit', $activeSurvey)" submit-text="Oyumu Gönder" />
                        @include('survey::partials.share-buttons', ['survey' => $activeSurvey])
                    </div>

                    <div class="col-lg-6">
                        <div class="chart-shell p-3 h-100">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                <div>
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Canlı Dağılım</div>
                                    <h3 class="h5 fw-bold mb-0">Seçenek bazlı sonuçlar</h3>
                                </div>
                                <div class="question-select-wrap">
                                    <select class="form-select form-select-sm" id="activeSurveyQuestionSelect"></select>
                                </div>
                            </div>
                            <p class="text-secondary small mb-3">Sorular arasında geçiş yaparak mevcut dağılımı görebilirsiniz.</p>
                            <div class="chart-wrap">
                                <canvas id="activeSurveyChart"></canvas>
                            </div>
                            <div class="small text-muted mt-3 d-none" id="activeSurveyChartEmpty">Grafik için seçenekli soru bulunamadı.</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-light border mb-0">Şu anda yayında olan bir anket bulunmuyor.</div>
            @endif
        </section>

        <section>
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div>
                    <div class="text-uppercase small fw-bold text-primary mb-2">Tüm Anketler</div>
                    <h2 class="h3 fw-bold mb-2">Güncel ve önceki anket arşivi</h2>
                    <p class="text-secondary mb-0">Açık anketlerde oy verebilir, kapanmış anketlerde sonuçları inceleyebilirsiniz.</p>
                </div>
            </div>

            <div class="row g-4">
                @forelse($surveys as $survey)
                    <div class="col-md-6 col-xl-4">
                        <article class="survey-list-card h-100 p-4">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($survey->is_open)
                                    <span class="status-pill"><i class="fa fa-check-circle"></i> Oy verilebilir</span>
                                @else
                                    <span class="status-pill muted"><i class="fa fa-clock-o"></i> Arşiv</span>
                                @endif

                                @if($survey->id === optional($activeSurvey)->id)
                                    <span class="status-pill"><i class="fa fa-star"></i> Güncel anket</span>
                                @endif
                            </div>

                            <h3 class="h5 fw-bold mb-2">{{ $survey->title }}</h3>
                            <p class="survey-description small mb-3">{{ $survey->description ?: 'Bu anket için açıklama eklenmemiş.' }}</p>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="metric-chip"><i class="fa fa-users"></i> {{ $survey->responses_count }} oy</span>
                                <span class="metric-chip"><i class="fa fa-calendar"></i> {{ optional($survey->opens_at)->format('d.m.Y') ?: 'Hemen' }}</span>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                @if($survey->is_open)
                                    <a href="{{ route('survey.public.show', $survey) }}" class="btn btn-primary rounded-pill px-4">Oy Ver</a>
                                @endif
                                <a href="{{ route('survey.public.results', $survey) }}" class="btn btn-outline-dark rounded-pill px-4">Sonuçlar</a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">Henüz yayınlanmış anket bulunmuyor.</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const activeStats = @json(($activeStats ?? collect())->values());
        const activeSelect = document.getElementById('activeSurveyQuestionSelect');
        const activeCanvas = document.getElementById('activeSurveyChart');
        const activeEmpty = document.getElementById('activeSurveyChartEmpty');
        let activeChart;

        if (!activeCanvas || !activeSelect || !window.Chart) {
            return;
        }

        const optionStats = activeStats.filter((stat) => Array.isArray(stat.options) && stat.options.length);

        if (!optionStats.length) {
            activeEmpty?.classList.remove('d-none');
            return;
        }

        optionStats.forEach((stat) => {
            const option = document.createElement('option');
            option.value = stat.id;
            option.textContent = stat.question;
            activeSelect.appendChild(option);
        });

        function renderChart(questionId) {
            const stat = optionStats.find((item) => String(item.id) === String(questionId)) || optionStats[0];
            if (!stat) {
                return;
            }

            if (activeChart) {
                activeChart.destroy();
            }

            activeChart = new Chart(activeCanvas, {
                type: 'doughnut',
                data: {
                    labels: stat.options.map((option) => option.label),
                    datasets: [{
                        data: stat.options.map((option) => option.count),
                        backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }

        activeSelect.addEventListener('change', (event) => renderChart(event.target.value));
        renderChart(activeSelect.options[0].value);
    });
</script>
@endpush
