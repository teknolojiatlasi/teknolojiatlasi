@extends('app::layouts.admin')

@section('title', $survey->exists ? 'Anket Düzenle: '.$survey->title : 'Yeni Anket')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .question-card { border: 1px solid #e5e7eb; border-radius: 12px; }
    .question-card .card-header { background: #f8f9fa; }
    .pill { border-radius: 999px; padding: 4px 10px; font-size: 0.8rem; }
    .option-row .form-control { min-width: 0; }
    .floating-actions { position: sticky; bottom: 1rem; right: 1rem; z-index: 10; }
</style>
@endpush

@section('content')
<div id="builderAlerts"></div>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="fw-bold mb-1">{{ $survey->exists ? 'Anketi Düzenle' : 'Yeni Anket' }}</h2>
        <small class="text-muted">Başlık, zamanlama ve sorularınızı tek ekranda yönetin.</small>
    </div>
    <div class="d-flex gap-2">
        @if($survey->exists)
            <a class="btn btn-outline-success" href="{{ route('survey.public.show', $survey) }}" target="_blank"><i class="fa fa-eye me-1"></i>Önizle</a>
            <a class="btn btn-outline-secondary" href="{{ route('survey.results', $survey) }}"><i class="fa fa-chart-pie me-1"></i>Sonuçlar</a>
        @endif
        <button class="btn btn-primary" id="saveSurveyTop"><i class="fa fa-save me-1"></i>Kaydet</button>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Genel Bilgiler</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Başlık</label>
                    <input type="text" class="form-control" id="surveyTitle" value="{{ $survey->title }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Açıklama</label>
                    <textarea class="form-control" rows="3" id="surveyDescription" placeholder="Kısa açıklama">{{ $survey->description }}</textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Başlangıç</label>
                        <input type="datetime-local" class="form-control" id="surveyOpensAt" value="{{ $survey->opens_at ? $survey->opens_at->format('Y-m-d\TH:i') : '' }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Bitiş</label>
                        <input type="datetime-local" class="form-control" id="surveyClosesAt" value="{{ $survey->closes_at ? $survey->closes_at->format('Y-m-d\TH:i') : '' }}">
                    </div>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" id="surveyActive" {{ $survey->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="surveyActive">Yayında</label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="surveyPublic" {{ $survey->is_public ? 'checked' : '' }}>
                    <label class="form-check-label" for="surveyPublic">Herkese açık</label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="surveyMulti" {{ $survey->allow_multiple_submissions ? 'checked' : '' }}>
                    <label class="form-check-label" for="surveyMulti">Aynı kullanıcı birden fazla gönderebilir</label>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-primary" id="saveSurvey"><i class="fa fa-save me-1"></i>Kaydet</button>
                    <a class="btn btn-outline-secondary" href="{{ route('survey.index') }}"><i class="fa fa-arrow-left me-1"></i>Listeye Dön</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-3" id="shareCard" style="{{ $survey->exists ? '' : 'display:none' }}">
            <div class="card-header fw-semibold">Paylaş & Embed</div>
            <div class="card-body">
                <div class="mb-2 small text-muted">Anket Linki</div>
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text">Form</span>
                    <input type="text" class="form-control" readonly id="publicUrl" value="{{ $survey->exists ? route('survey.public.show', $survey) : '' }}">
                    <button class="btn btn-outline-secondary copy-link" data-target="#publicUrl">Kopyala</button>
                </div>
                <div class="mb-2 small text-muted">Sonuç Grafikleri</div>
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text">Sonuç</span>
                    <input type="text" class="form-control" readonly id="resultUrl" value="{{ $survey->exists ? route('survey.public.results', $survey) : '' }}">
                    <button class="btn btn-outline-secondary copy-link" data-target="#resultUrl">Kopyala</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm question-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <span class="fw-semibold">Soru Listesi</span>
                    <span class="badge bg-light text-dark ms-2" id="questionCount">0</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light text-dark btn-sm" id="addQuestion"><i class="fa fa-plus me-1"></i>Yeni Soru</button>
                    <button class="btn btn-primary btn-sm" id="saveSurveyInline"><i class="fa fa-save me-1"></i>Kaydet</button>
                </div>
            </div>
            <div class="card-body">
                <div id="questionList" class="d-flex flex-column gap-3"></div>
                <div class="text-center text-muted py-4" id="emptyPlaceholder" style="display:none;">
                    <i class="fa fa-circle-plus fa-2x mb-2"></i>
                    <div>Henüz soru yok. Yeni soru ekleyerek başlayın.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $initialQuestions = $survey->questions
        ->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'is_required' => (bool) $question->is_required,
                'help_text' => $question->help_text,
                'explanation' => $question->explanation,
                'options' => $question->options
                    ->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'label' => $option->label,
                            'value' => $option->value,
                            'is_correct' => (bool) $option->is_correct,
                        ];
                    })
                    ->values(),
            ];
        })
        ->values();
@endphp

@push('scripts')
<script src="{{ asset('vendor/front/company/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const alertHost = document.getElementById('builderAlerts');
    const questionList = document.getElementById('questionList');
    const placeholder = document.getElementById('emptyPlaceholder');
    const questionCount = document.getElementById('questionCount');
    const baseSurveyUrl = @json(url('/surveys').'/');

    let surveyId = {{ $survey->exists ? $survey->id : 'null' }};
    let saveUrl = @json($survey->exists ? route('survey.update', $survey) : route('survey.store'));
    let saveMethod = @json($survey->exists ? 'PUT' : 'POST');

    const initialQuestions = @json($initialQuestions);

    const typeLabels = {
        single_choice: 'Çoktan Seçmeli (Tek)',
        multiple_choice: 'Çoktan Seçmeli (Çoklu)',
        boolean: 'Doğru / Yanlış',
        text: 'Metin Girişi',
    };

    const optionableTypes = ['single_choice', 'multiple_choice', 'boolean'];
    let questions = initialQuestions.length ? [...initialQuestions] : [defaultQuestion()];

    function defaultQuestion() {
        return {
            id: null,
            question: '',
            type: 'single_choice',
            is_required: true,
            help_text: '',
            explanation: '',
            options: [
                { id: null, label: 'Seçenek 1', value: 'option-1', is_correct: false },
                { id: null, label: 'Seçenek 2', value: 'option-2', is_correct: false },
            ],
        };
    }

    function renderAlert(message, type = 'success') {
        alertHost.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        `;
    }

    function renderQuestions() {
        questionList.innerHTML = '';
        questionCount.textContent = questions.length;

        if (!questions.length) {
            placeholder.style.display = 'block';
            return;
        }

        placeholder.style.display = 'none';

        questions.forEach((question, index) => {
            const card = document.createElement('div');
            card.className = 'card shadow-sm';
            card.dataset.index = index;
            card.innerHTML = `
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">${index + 1}</span>
                        <input type="text" class="form-control form-control-sm question-text" placeholder="Soru metni" value="${question.question ?? ''}">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm question-type">
                            ${Object.entries(typeLabels).map(([value, label]) => `<option value="${value}" ${question.type === value ? 'selected' : ''}>${label}</option>`).join('')}
                        </select>
                        <div class="form-check form-switch">
                            <input class="form-check-input question-required" type="checkbox" ${question.is_required ? 'checked' : ''}>
                            <label class="form-check-label small">Zorunlu</label>
                        </div>
                        <button class="btn btn-outline-danger btn-sm remove-question" title="Sil"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm question-help" placeholder="İpucu / açıklama" value="${question.help_text ?? ''}">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm question-explanation" placeholder="Doğru cevap notu" value="${question.explanation ?? ''}">
                        </div>
                    </div>
                    ${renderOptions(question, index)}
                </div>
            `;
            questionList.appendChild(card);
        });
    }

    function renderOptions(question, index) {
        if (!optionableTypes.includes(question.type)) {
            return '<div class="text-muted small">Katılımcı serbest metin girecek.</div>';
        }

        const optionsHtml = (question.options || []).map((option, optIndex) => `
            <div class="input-group input-group-sm mb-2 option-row" data-option-index="${optIndex}">
                <span class="input-group-text">${optIndex + 1}</span>
                <input type="text" class="form-control option-label" placeholder="Seçenek" value="${option.label ?? ''}">
                <input type="text" class="form-control option-value" placeholder="Değer (isteğe bağlı)" value="${option.value ?? ''}">
                <span class="input-group-text">
                    <input class="form-check-input option-correct" type="checkbox" ${option.is_correct ? 'checked' : ''} title="Doğru cevap olarak işaretle">
                </span>
                <button class="btn btn-outline-danger remove-option" type="button"><i class="fa fa-times"></i></button>
            </div>
        `).join('');

        return `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-semibold">Seçenekler</span>
                <button type="button" class="btn btn-outline-primary btn-sm add-option" data-question-index="${index}"><i class="fa fa-plus me-1"></i>Ekle</button>
            </div>
            ${optionsHtml || '<div class="text-muted small">Seçenek ekleyin.</div>'}
        `;
    }

    function addQuestion() {
        questions.push(defaultQuestion());
        renderQuestions();
    }

    function updateQuestionField(index, key, value) {
        questions[index][key] = value;
    }

    function normalizeOptions(question) {
        if (!optionableTypes.includes(question.type)) {
            question.options = [];
            return;
        }

        if (!question.options || !question.options.length) {
            question.options = [
                { id: null, label: 'Seçenek 1', value: 'option-1', is_correct: false },
                { id: null, label: 'Seçenek 2', value: 'option-2', is_correct: false },
            ];
        }

        if (question.type === 'boolean') {
            question.options = [
                { id: null, label: 'Doğru', value: 'true', is_correct: false },
                { id: null, label: 'Yanlış', value: 'false', is_correct: false },
            ];
        }
    }

    questionList.addEventListener('input', (event) => {
        const card = event.target.closest('.card');
        if (!card) return;
        const index = Number(card.dataset.index);
        const question = questions[index];

        if (event.target.classList.contains('question-text')) {
            updateQuestionField(index, 'question', event.target.value);
        }

        if (event.target.classList.contains('question-type')) {
            updateQuestionField(index, 'type', event.target.value);
            normalizeOptions(question);
            renderQuestions();
        }

        if (event.target.classList.contains('question-required')) {
            updateQuestionField(index, 'is_required', event.target.checked);
        }

        if (event.target.classList.contains('question-help')) {
            updateQuestionField(index, 'help_text', event.target.value);
        }

        if (event.target.classList.contains('question-explanation')) {
            updateQuestionField(index, 'explanation', event.target.value);
        }

        if (event.target.classList.contains('option-label')) {
            const optIndex = Number(event.target.closest('.option-row').dataset.optionIndex);
            question.options[optIndex].label = event.target.value;
        }

        if (event.target.classList.contains('option-value')) {
            const optIndex = Number(event.target.closest('.option-row').dataset.optionIndex);
            question.options[optIndex].value = event.target.value;
        }

        if (event.target.classList.contains('option-correct')) {
            const optIndex = Number(event.target.closest('.option-row').dataset.optionIndex);
            question.options[optIndex].is_correct = event.target.checked;
        }
    });

    questionList.addEventListener('click', (event) => {
        if (event.target.closest('.remove-question')) {
            const idx = Number(event.target.closest('.card').dataset.index);
            questions.splice(idx, 1);
            renderQuestions();
            return;
        }

        if (event.target.closest('.add-option')) {
            const idx = Number(event.target.closest('.add-option').dataset.questionIndex);
            questions[idx].options = questions[idx].options || [];
            questions[idx].options.push({ id: null, label: 'Yeni seçenek', value: '', is_correct: false });
            renderQuestions();
            return;
        }

        if (event.target.closest('.remove-option')) {
            const card = event.target.closest('.card');
            const qIdx = Number(card.dataset.index);
            const optIdx = Number(event.target.closest('.option-row').dataset.optionIndex);
            questions[qIdx].options.splice(optIdx, 1);
            renderQuestions();
            return;
        }
    });

    function buildPayload() {
        return {
            title: document.getElementById('surveyTitle').value,
            description: document.getElementById('surveyDescription').value,
            is_active: document.getElementById('surveyActive').checked,
            is_public: document.getElementById('surveyPublic').checked,
            allow_multiple_submissions: document.getElementById('surveyMulti').checked,
            opens_at: document.getElementById('surveyOpensAt').value || null,
            closes_at: document.getElementById('surveyClosesAt').value || null,
            questions: questions.map((question, index) => ({
                id: question.id,
                question: question.question,
                type: question.type,
                is_required: question.is_required ?? false,
                help_text: question.help_text,
                explanation: question.explanation,
                options: optionableTypes.includes(question.type)
                    ? (question.options || []).map((option) => ({
                        id: option.id,
                        label: option.label,
                        value: option.value || option.label,
                        is_correct: option.is_correct ?? false,
                    }))
                    : [],
            })),
        };
    }

    async function saveSurvey() {
        const payload = buildPayload();

        try {
            const response = await fetch(saveUrl, {
                method: saveMethod,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                const messages = data.errors
                    ? Object.values(data.errors).flat().join('<br>')
                    : data.message || 'İşlem sırasında bir hata oluştu.';
                throw new Error(messages);
            }

            renderAlert(data.message || 'Anket kaydedildi.');

            if (!surveyId && data.survey?.id) {
                surveyId = data.survey.id;
                saveUrl = baseSurveyUrl + surveyId;
                saveMethod = 'PUT';
                updateShareLinks(data.survey);
            }

            if (data.survey?.questions) {
                questions = data.survey.questions.map((q) => ({
                    id: q.id,
                    question: q.question,
                    type: q.type,
                    is_required: q.is_required,
                    help_text: q.help_text,
                    explanation: q.explanation,
                    options: q.options || [],
                }));
                renderQuestions();
            }
        } catch (error) {
            renderAlert(error.message, 'danger');
        }
    }

    function updateShareLinks(survey) {
        const shareCard = document.getElementById('shareCard');
        const publicUrl = document.getElementById('publicUrl');
        const resultUrl = document.getElementById('resultUrl');

        if (!survey?.id) return;

        const basePublic = "{{ url('/survey') }}/" + survey.slug;
        publicUrl.value = basePublic;
        resultUrl.value = basePublic + '/results';
        shareCard.style.display = 'block';
    }

    document.getElementById('addQuestion')?.addEventListener('click', addQuestion);
    document.getElementById('saveSurvey')?.addEventListener('click', saveSurvey);
    document.getElementById('saveSurveyTop')?.addEventListener('click', saveSurvey);
    document.getElementById('saveSurveyInline')?.addEventListener('click', saveSurvey);

    document.querySelectorAll('.copy-link').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = document.querySelector(btn.dataset.target);
            if (!target) return;
            target.select();
            document.execCommand('copy');
            renderAlert('Link kopyalandı.');
        });
    });

    renderQuestions();
</script>
@endpush
