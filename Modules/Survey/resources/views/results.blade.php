@extends(($publicView ?? false) ? 'layouts.app2' : 'app::layouts.admin')

@section('title', 'Anket Sonuclari: '.$survey->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
<style>
    .stat-card { border-radius: 12px; }
    .progress { height: 10px; }
    .text-answer { background: #f8f9fa; border-radius: 8px; }
    .chart-card {
        border-radius: 12px;
    }
    .chart-card .card-header {
        gap: 0.75rem;
    }
    .chart-shell {
        max-width: 420px;
        margin: 0 auto;
    }
    .chart-shell canvas {
        width: 100% !important;
        height: min(320px, 52vw) !important;
    }
    @media (max-width: 991.98px) {
        .chart-shell {
            max-width: 360px;
        }
        .chart-shell canvas {
            height: min(280px, 60vw) !important;
        }
    }
    @media (max-width: 575.98px) {
        .chart-card .card-header {
            align-items: stretch !important;
            flex-direction: column;
        }
        .chart-card .form-select {
            width: 100% !important;
        }
        .chart-shell {
            max-width: 100%;
        }
        .chart-shell canvas {
            height: min(240px, 68vw) !important;
        }
    }
</style>
@endpush

@section('content')
@if($publicView ?? false)
<div class="container py-4">
@endif

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="fw-bold mb-1">Sonuçlar</h2>
        <small class="text-muted">{{ $survey->title }}</small>
    </div>
    <div class="d-flex gap-2">
        @unless($publicView ?? false)
            <a class="btn btn-outline-secondary" href="{{ route('survey.edit', $survey) }}"><i class="fa fa-pen me-1"></i>Duzenle</a>
        @endunless
        <a class="btn btn-outline-primary" target="_blank" href="{{ route('survey.public.results', $survey) }}"><i class="fa fa-share me-1"></i>GÜNCEL SONUÇ</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Yanit</span>
                    <span class="badge bg-primary">{{ $responseCount }}</span>
                </div>
                <h4 class="fw-bold">{{ $responseCount }}</h4>
                <p class="text-muted mb-0">Toplam gonderim</p>
            </div>
        </div>
        <div class="card shadow-sm stat-card mt-3">
            <div class="card-body">
                <div class="mb-2 text-muted">Durum</div>
                <div class="d-flex gap-2">
                    <span class="badge bg-{{ $survey->is_active ? 'success' : 'secondary' }}">{{ $survey->is_active ? 'Yayinda' : 'Taslak' }}</span>
                    <span class="badge bg-{{ $survey->is_public ? 'info' : 'warning' }}">{{ $survey->is_public ? 'Herkese Acik' : 'Kisitli' }}</span>
                </div>
                <small class="text-muted d-block mt-2">Baslangic: {{ $survey->opens_at?->format('d.m.Y H:i') ?? 'Belirtilmemis' }}</small>
                <small class="text-muted">Bitis: {{ $survey->closes_at?->format('d.m.Y H:i') ?? 'Belirtilmemis' }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm chart-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Grafik</span>
                <select class="form-select form-select-sm w-auto" id="chartQuestionSelect"></select>
            </div>
            <div class="card-body">
                <div class="chart-shell">
                    <canvas id="resultChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header fw-semibold">Soru Detaylari</div>
    <div class="card-body">
        <div class="accordion" id="questionAccordion">
            @foreach($questionStats as $index => $stat)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span> {{ $stat['question'] }}
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#questionAccordion">
                        <div class="accordion-body">
                            @if(count($stat['options']))
                                @foreach($stat['options'] as $option)
                                    @php
                                        $total = max(1, $stat['totalAnswers']);
                                        $percentage = round(($option['count'] / $total) * 100, 1);
                                    @endphp
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>{{ $option['label'] }}</span>
                                            <span class="text-muted">{{ $option['count'] }} yanit ({{ $percentage }}%)</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="mb-2 text-muted">Metin yanitlar (son 25):</div>
                                <div class="d-flex flex-column gap-2">
                                    @forelse($stat['textAnswers'] as $answer)
                                        <div class="p-2 text-answer">
                                            <div>{{ $answer->answer_text }}</div>
                                            <small class="text-muted">{{ $answer->created_at->format('d.m.Y H:i') }}</small>
                                        </div>
                                    @empty
                                        <div class="text-muted">Henuz yanit yok.</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@if($publicView ?? false)
</div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('vendor/front/company/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const stats = @json($questionStats->values());
        const select = document.getElementById('chartQuestionSelect');
        const chartCanvas = document.getElementById('resultChart');

        if (!select || !chartCanvas || !window.Chart) {
            return;
        }

        stats.filter(stat => stat.options.length).forEach(stat => {
            const option = document.createElement('option');
            option.value = stat.id;
            option.textContent = stat.question;
            select.appendChild(option);
        });

        let chart;

        function renderChart(questionId) {
            const stat = stats.find(item => item.id == questionId) || stats.find(item => item.options.length);
            if (!stat) return;

            const labels = stat.options.map(o => o.label);
            const data = stat.options.map(o => o.count);

            const config = {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997', '#fd7e14'],
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            };

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(chartCanvas.getContext('2d'), config);
        }

        select.addEventListener('change', (event) => renderChart(event.target.value));

        if (select.options.length) {
            renderChart(select.options[0].value);
        }
    });
</script>
@endpush
