@extends('layouts.admin')

@section('title', 'Anketler')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body.nav-md .container.body {
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
    }
    body.nav-md .right_col {
        padding-left: 0;
        padding-right: 0;
    }
    .card-header {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        color: #fff;
    }
    .survey-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .active-survey-card .chart-card {
        background: #f8f9fb;
        border-radius: 12px;
    }
    .active-survey-card canvas {
        max-height: 240px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="fw-bold mb-0">Anket Yönetimi</h2>
        <small class="text-muted">Yeni anket oluşturun, düzenleyin ve sonuçları tek ekranda yönetin.</small>
    </div>
    <a href="{{ route('survey.create') }}" class="btn btn-outline-light text-dark border"><i class="fa fa-wand-magic-sparkles me-2"></i>Gelişmiş Oluşturucu</a>
    </div>

<div id="alertPlaceholder"></div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Hızlı Anket Oluştur</span>
                <i class="fa fa-bolt"></i>
            </div>
            <div class="card-body">
                <form id="quickSurveyForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Başlık</label>
                        <input type="text" name="title" class="form-control" placeholder="Örn: Müşteri Memnuniyeti" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Açıklama</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Anket hakkında kısa bilgi"></textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="quickActive" checked>
                        <label class="form-check-label" for="quickActive">Yayında</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_public" id="quickPublic" checked>
                        <label class="form-check-label" for="quickPublic">Herkese Açık</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fa fa-plus-circle me-1"></i> Kaydet ve Sorulara Geç
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Mevcut Anketler</span>
                <span class="badge bg-light text-dark">{{ $surveys->total() }} kayıt</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Başlık</th>
                                <th>Soru</th>
                                <th>Yanıt</th>
                                <th>Durum</th>
                                <th class="text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody id="surveyTableBody">
                            @forelse($surveys as $survey)
                                <tr data-survey-row="{{ $survey->id }}">
                                    <td>
                                        <div class="fw-semibold">{{ $survey->title }}</div>
                                        <div class="survey-meta">{{ $survey->description }}</div>
                                    </td>
                                    <td>{{ $survey->questions_count }}</td>
                                    <td>{{ $survey->responses_count }}</td>
                                    <td>
                                        <span class="badge bg-{{ $survey->is_active ? 'success' : 'secondary' }} me-1">
                                            {{ $survey->is_active ? 'Yayında' : 'Taslak' }}
                                        </span>
                                        <span class="badge bg-{{ $survey->is_public ? 'info' : 'warning' }}">
                                            {{ $survey->is_public ? 'Herkese Açık' : 'Kısıtlı' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a class="btn btn-outline-primary" href="{{ route('survey.edit', $survey) }}"><i class="fa fa-pen"></i></a>
                                            <a class="btn btn-outline-success" href="{{ route('survey.results', $survey) }}"><i class="fa fa-chart-pie"></i></a>
                                            <a class="btn btn-outline-secondary" target="_blank" href="{{ route('survey.public.show', $survey) }}"><i class="fa fa-eye"></i></a>
                                            <button class="btn btn-outline-danger btn-delete-survey" data-url="{{ route('survey.destroy', $survey) }}"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Henüz anket yok, hemen oluşturun.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($surveys->hasPages())
                <div class="card-footer">
                    {{ $surveys->links() }}
                </div>
            @endif
        </div>
</div>
</div>

<div class="card shadow-sm mt-4 active-survey-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>Aktif Anket</span>
        @if($activeSurvey)
            <span class="badge bg-success">Yayinda</span>
        @endif
    </div>
    <div class="card-body">
        @if($activeSurvey)
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <h5 class="fw-semibold mb-1">{{ $activeSurvey->title }}</h5>
                        <div class="text-muted mb-2">{{ $activeSurvey->description }}</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-{{ $activeSurvey->is_public ? 'info' : 'warning' }}">{{ $activeSurvey->is_public ? 'Herkese Acik' : 'Kisitli' }}</span>
                            <span class="badge bg-light text-dark">Katilim: {{ $activeResponseCount }}</span>
                        </div>
                    </div>
                    <x-survey::form :survey="$activeSurvey" :action="route('survey.public.submit', $activeSurvey)" submit-text="Oyumu Gonder" />
                </div>
                <div class="col-lg-6">
                    <div class="chart-card p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-semibold">Katilim Oranlari</span>
                            <select class="form-select form-select-sm w-auto" id="activeSurveyQuestionSelect"></select>
                        </div>
                        <div class="text-muted small mb-2">Secilen soruya gore oranlar gosterilir.</div>
                        <canvas id="activeSurveyChart" height="180"></canvas>
                        <div class="small text-muted mt-2 d-none" id="activeSurveyChartEmpty">Grafik icin secenekli soru bulunamadi.</div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-muted">Aktif anket bulunamadi.</div>
        @endif
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/front/company/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const baseUpdateUrl = "{{ url('/surveys') }}/";

    const quickForm = document.getElementById('quickSurveyForm');
    const alertPlaceholder = document.getElementById('alertPlaceholder');
    const surveyTableBody = document.getElementById('surveyTableBody');

    function showAlert(message, type = 'success') {
        const wrapper = document.createElement('div');
        wrapper.className = `alert alert-${type} alert-dismissible fade show`;
        wrapper.innerHTML = `
            <div class="d-flex align-items-center">
                <span class="me-2 status-dot bg-${type === 'success' ? 'success' : 'danger'}"></span>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        `;
        alertPlaceholder.innerHTML = '';
        alertPlaceholder.appendChild(wrapper);
    }

    function appendSurveyRow(survey) {
        const row = document.createElement('tr');
        row.dataset.surveyRow = survey.id;
        row.innerHTML = `
            <td>
                <div class="fw-semibold">${survey.title}</div>
                <div class="survey-meta">${survey.description ?? ''}</div>
            </td>
            <td>${survey.questions ? survey.questions.length : 0}</td>
            <td>${survey.responses_count ?? 0}</td>
            <td>
                <span class="badge bg-${survey.is_active ? 'success' : 'secondary'} me-1">${survey.is_active ? 'Yayında' : 'Taslak'}</span>
                <span class="badge bg-${survey.is_public ? 'info' : 'warning'}">${survey.is_public ? 'Herkese Açık' : 'Kısıtlı'}</span>
            </td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-primary" href="${baseUpdateUrl}${survey.id}/edit"><i class="fa fa-pen"></i></a>
                    <a class="btn btn-outline-success" href="${baseUpdateUrl}${survey.id}/results"><i class="fa fa-chart-pie"></i></a>
                    <a class="btn btn-outline-secondary" target="_blank" href="/survey/${survey.slug}"><i class="fa fa-eye"></i></a>
                    <button class="btn btn-outline-danger btn-delete-survey" data-url="${baseUpdateUrl}${survey.id}"><i class="fa fa-trash"></i></button>
                </div>
            </td>
        `;
        surveyTableBody.prepend(row);
    }

    quickForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(quickForm);
        const payload = {
            title: formData.get('title'),
            description: formData.get('description'),
            is_active: formData.get('is_active') === 'on',
            is_public: formData.get('is_public') === 'on',
            allow_multiple_submissions: false,
            questions: [],
        };

        try {
            const response = await fetch("{{ route('survey.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Anket kaydedilemedi.');
            }

            appendSurveyRow(data.survey);
            quickForm.reset();
            showAlert('Anket oluşturuldu. Sorular için düzenle butonuna tıklayın.');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    surveyTableBody?.addEventListener('click', async (e) => {
        if (!e.target.closest('.btn-delete-survey')) {
            return;
        }

        const button = e.target.closest('.btn-delete-survey');
        const url = button.dataset.url;

        if (!confirm('Anketi silmek istediğinize emin misiniz?')) {
            return;
        }

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Silme işlemi başarısız.');
            }

            button.closest('tr')?.remove();
            showAlert('Anket silindi.');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    const activeStats = @json($activeStats ? $activeStats->values() : []);
    const activeSelect = document.getElementById('activeSurveyQuestionSelect');
    const activeCanvas = document.getElementById('activeSurveyChart');
    const activeEmpty = document.getElementById('activeSurveyChartEmpty');
    let activeChart;

    function initActiveSurveyChart() {
        if (!activeCanvas || !activeSelect || !activeStats.length || !window.Chart) {
            if (activeEmpty) {
                activeEmpty.classList.remove('d-none');
            }
            return;
        }

        const optionStats = activeStats.filter(stat => stat.options && stat.options.length);
        if (!optionStats.length) {
            if (activeEmpty) {
                activeEmpty.classList.remove('d-none');
            }
            return;
        }

        optionStats.forEach(stat => {
            const option = document.createElement('option');
            option.value = stat.id;
            option.textContent = stat.question;
            activeSelect.appendChild(option);
        });

        function renderActiveChart(questionId) {
            const stat = optionStats.find(item => item.id == questionId) || optionStats[0];
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

            if (activeChart) {
                activeChart.destroy();
            }

            activeChart = new Chart(activeCanvas, config);
        }

        activeSelect.addEventListener('change', (event) => renderActiveChart(event.target.value));
        renderActiveChart(activeSelect.options[0].value);
    }

    initActiveSurveyChart();
</script>
@endpush
