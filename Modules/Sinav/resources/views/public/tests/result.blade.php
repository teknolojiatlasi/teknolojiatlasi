@push('scripts')
    <script>
        (function () {
            const selectEl = document.getElementById('resultChartType');
            const canvas = document.getElementById('resultChart');
            if (!selectEl || !canvas) return;

            const data = [
                { label: 'Doğru', value: Number(@json($correct)), color: '#10b981' },
                { label: 'Yanlış', value: Number(@json($wrong)), color: '#ef4444' },
                { label: 'Boş', value: Number(@json($blank)), color: '#64748b' },
            ];

            function getSize() {
                const rect = canvas.getBoundingClientRect();
                return {
                    width: rect.width || canvas.parentElement?.clientWidth || 320,
                    height: rect.height || 240,
                };
            }

            function setupCanvas() {
                const { width, height } = getSize();
                const dpr = window.devicePixelRatio || 1;
                canvas.width = Math.max(1, Math.floor(width * dpr));
                canvas.height = Math.max(1, Math.floor(height * dpr));
                const ctx = canvas.getContext('2d');
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                return ctx;
            }

            function drawPie(ctx) {
                const { width: w, height: h } = getSize();
                const cx = w / 2;
                const cy = h / 2;
                const r = Math.min(w, h) / 2 - 14;
                const total = data.reduce((sum, item) => sum + Math.max(0, item.value), 0) || 1;
                let start = -Math.PI / 2;

                data.forEach((item) => {
                    const angle = (Math.max(0, item.value) / total) * Math.PI * 2;
                    ctx.beginPath();
                    ctx.moveTo(cx, cy);
                    ctx.arc(cx, cy, r, start, start + angle);
                    ctx.closePath();
                    ctx.fillStyle = item.color;
                    ctx.fill();
                    start += angle;
                });
            }

            function drawBar(ctx) {
                const { width: w, height: h } = getSize();
                const padding = 18;
                const chartHeight = h - padding * 2;
                const baseY = h - padding;
                const maxVal = Math.max(1, ...data.map((item) => Math.max(0, item.value)));
                const barW = Math.max(24, (w - padding * 2) / (data.length * 1.8));
                const gap = barW * 0.6;
                const startX = (w - (data.length * barW + (data.length - 1) * gap)) / 2;

                ctx.font = '12px system-ui, sans-serif';
                ctx.textAlign = 'center';

                data.forEach((item, index) => {
                    const barHeight = (Math.max(0, item.value) / maxVal) * chartHeight;
                    const x = startX + index * (barW + gap);
                    const y = baseY - barHeight;

                    ctx.fillStyle = item.color;
                    ctx.fillRect(x, y, barW, barHeight);

                    ctx.fillStyle = '#0f172a';
                    ctx.fillText(String(item.value), x + barW / 2, y - 6);
                    ctx.fillText(item.label, x + barW / 2, baseY + 16);
                });
            }

            function render() {
                const ctx = setupCanvas();
                const { width, height } = getSize();
                ctx.clearRect(0, 0, width, height);

                if (selectEl.value === 'bar') drawBar(ctx);
                else drawPie(ctx);
            }

            selectEl.addEventListener('change', render);
            window.addEventListener('resize', render);
            requestAnimationFrame(() => requestAnimationFrame(render));
        })();
    </script>
@endpush

@extends('layouts.app2')

@push('styles')
<style>
    .exam-result-shell .result-hero {
        padding: 2rem;
        border-radius: 1.8rem;
        background:
            radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
            radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.18), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #1e3a8a 64%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
    }

    .exam-result-shell .result-card,
    .exam-result-shell .result-question {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.45rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    }

    .exam-result-shell .result-metric {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.8rem 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    }

    .exam-result-shell .result-metric:last-child {
        border-bottom: 0;
    }

    .exam-result-shell .result-score-ring {
        width: 140px;
        height: 140px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        margin: 0 auto 1.2rem;
        background:
            radial-gradient(circle at center, #fff 56%, transparent 57%),
            conic-gradient(#10b981 {{ $scorePercent }}%, #e2e8f0 0);
    }

    .exam-result-shell .result-score-value {
        font-size: 2rem;
        font-weight: 900;
        color: #0f172a;
    }

    .exam-result-shell .answer-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .exam-result-shell .answer-pill.ok { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .exam-result-shell .answer-pill.bad { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .exam-result-shell .answer-pill.blank { background: rgba(100, 116, 139, 0.12); color: #475569; }

    .exam-result-shell .question-media {
        margin-bottom: 1rem;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: #f8fafc;
    }

    .exam-result-shell .question-media img {
        display: block;
        width: 100%;
        max-height: 420px;
        object-fit: contain;
        background: #fff;
    }
</style>
@endpush

@section('content')
<main class="exam-result-shell py-4 py-lg-5">
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
        <section class="result-hero mb-4 mb-lg-5">
            <div class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <div class="small text-uppercase fw-bold mb-2">Test tamamlandı</div>
                    <h1 class="display-6 fw-bold mb-3">{{ $test->title }}</h1>
                    <div class="text-white-50">
                        Ders: {{ $test->topic->lesson->name }} · Konu: {{ $test->topic->title }}
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="small text-uppercase fw-bold text-white-50 mb-2">Başarı oranı</div>
                    <div class="display-5 fw-bold">{{ $scorePercent }}%</div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="result-card p-4 h-100">
                    <div class="result-score-ring">
                        <span class="result-score-value">{{ $scorePercent }}</span>
                    </div>

                    <div class="text-center text-muted small mb-4">Toplam performans puanı</div>

                    <div class="result-metric">
                        <span>Doğru</span>
                        <strong class="text-success">{{ $correct }}</strong>
                    </div>
                    <div class="result-metric">
                        <span>Yanlış</span>
                        <strong class="text-danger">{{ $wrong }}</strong>
                    </div>
                    <div class="result-metric">
                        <span>Boş</span>
                        <strong class="text-secondary">{{ $blank }}</strong>
                    </div>

                    @auth
                        @if ($attempt)
                            <div class="alert alert-success mt-4 mb-0">
                                Sonuç kaydedildi. <a href="{{ route('sinav.attempts.show', $attempt) }}">Detaya git</a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mt-4 mb-0">
                            Giriş yapmadığınız için sonuç kaydedilmedi.
                        </div>
                    @endauth

                    <div class="mt-4">
                        <label for="resultChartType" class="form-label small fw-semibold">Grafik türü</label>
                        <select id="resultChartType" class="form-select form-select-sm" style="max-width: 220px;">
                            <option value="pie">Pasta grafik</option>
                            <option value="bar">Çubuk grafik</option>
                        </select>
                        <div class="mt-3">
                            <canvas id="resultChart" class="w-100 d-block" style="height: 240px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="d-grid gap-3">
                    @foreach ($computed as $index => $item)
                        @php
                            $q = $item['question'];
                            $selected = $item['selected'];
                            $pillClass = $selected === null ? 'blank' : ($item['is_correct'] ? 'ok' : 'bad');
                            $pillText = $selected === null ? 'Boş bırakıldı' : ($item['is_correct'] ? 'Doğru cevaplandı' : 'Yanlış cevaplandı');
                        @endphp

                        <article class="result-question p-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="small text-uppercase fw-bold text-primary mb-2">Soru {{ $index + 1 }}</div>
                                    @if ($q->image_url)
                                        <div class="question-media">
                                            <img src="{{ $q->image_url }}" alt="Soru gorseli {{ $index + 1 }}">
                                        </div>
                                    @endif
                                    <div class="fw-semibold fs-5">{{ $q->question_text }}</div>
                                </div>
                                <span class="answer-pill {{ $pillClass }}">
                                    <i class="fa {{ $pillClass === 'ok' ? 'fa-check' : ($pillClass === 'bad' ? 'fa-times' : 'fa-minus') }}"></i>
                                    {{ $pillText }}
                                </span>
                            </div>

                            <div class="row g-3 small">
                                <div class="col-md-6">
                                    <div class="text-muted mb-1">Seçiminiz</div>
                                    <div class="fw-semibold">{{ $selected ?? 'Boş' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted mb-1">Doğru cevap</div>
                                    <div class="fw-semibold">{{ $q->correct_option }}</div>
                                </div>
                            </div>

                            @if ($q->explanation)
                                <div class="alert alert-secondary mt-3 mb-0">
                                    <div class="fw-semibold mb-1">Açıklama</div>
                                    <div>{{ $q->explanation }}</div>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('sinav.lessons.show', $test->topic->lesson) }}">Derse Dön</a>
                    @auth
                        <a class="btn btn-outline-primary rounded-pill px-4" href="{{ route('sinav.attempts.index') }}">Çözümlerim</a>
                    @endauth
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
