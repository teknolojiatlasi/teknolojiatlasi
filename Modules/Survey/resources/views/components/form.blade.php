@props([
    'survey',
    'action' => route('survey.public.submit', $survey),
    'submitText' => 'Gönder',
    'redirect' => true
])

@php($formId = 'survey-form-'.uniqid())

<div class="card shadow-sm survey-form-card">
    <div class="card-body">
        <form id="{{ $formId }}" class="survey-ajax-form" data-submit-url="{{ $action }}">
            @include('partials.bot-protection')
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="text" name="participant_name" class="form-control" placeholder="İsim (isteğe bağlı)">
                </div>
                <div class="col-md-6">
                    <input type="email" name="participant_email" class="form-control" placeholder="E-posta (isteğe bağlı)">
                </div>
            </div>

            @foreach($survey->questions as $question)
                <div class="mb-3" data-question-id="{{ $question->id }}" data-question-type="{{ $question->type }}">
                    <label class="form-label fw-semibold">
                        {{ $question->question }}
                        @if($question->is_required)<span class="text-danger">*</span>@endif
                    </label>
                    @if($question->help_text)
                        <div class="text-muted small mb-1">{{ $question->help_text }}</div>
                    @endif

                    @if(in_array($question->type, ['single_choice', 'boolean']))
                        @foreach($question->options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_{{ $question->id }}" value="{{ $option->id }}" id="q{{ $question->id }}_{{ $option->id }}">
                                <label class="form-check-label" for="q{{ $question->id }}_{{ $option->id }}">{{ $option->label }}</label>
                            </div>
                        @endforeach
                    @elseif($question->type === 'multiple_choice')
                        @foreach($question->options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $option->id }}" id="q{{ $question->id }}_{{ $option->id }}">
                                <label class="form-check-label" for="q{{ $question->id }}_{{ $option->id }}">{{ $option->label }}</label>
                            </div>
                        @endforeach
                    @else
                        <textarea class="form-control" rows="3" placeholder="Yanıtınızı yazın"></textarea>
                    @endif
                </div>
            @endforeach

            <div class="alert alert-danger d-none" id="{{ $formId }}-alert"></div>
            <button class="btn btn-primary w-100" type="submit">{{ $submitText }}</button>
        </form>
    </div>
</div>

@once
    @push('styles')
    <style>
        .survey-form-card [data-question-id] {
            scroll-margin-top: 120px;
        }

        .survey-form-card .form-check {
            min-height: 46px;
        }

        .survey-form-card [id$='-alert'] {
            min-height: 54px;
        }
    </style>
    @endpush
@endonce

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('{{ $formId }}');
        if (!form) return;

        const alertBox = document.getElementById('{{ $formId }}-alert');
        const submitUrl = form.dataset.submitUrl;
        const allowRedirect = {{ $redirect ? 'true' : 'false' }};
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            alertBox.classList.add('d-none');

            const payload = {
                participant_name: form.querySelector('input[name="participant_name"]')?.value,
                participant_email: form.querySelector('input[name="participant_email"]')?.value,
                answers: [],
            };
            payload._trap = form.querySelector('input[name="_trap"]')?.value || '';
            payload._started_at = form.querySelector('input[name="_started_at"]')?.value || '';
            payload['cf-turnstile-response'] = form.querySelector('input[name="cf-turnstile-response"]')?.value || '';

            form.querySelectorAll('[data-question-id]').forEach((wrapper) => {
                const questionId = Number(wrapper.dataset.questionId);
                const type = wrapper.dataset.questionType;

                if (type === 'text') {
                    const text = wrapper.querySelector('textarea')?.value || '';
                    payload.answers.push({ survey_question_id: questionId, answer_text: text });
                    return;
                }

                if (type === 'multiple_choice') {
                    const optionIds = Array.from(wrapper.querySelectorAll('input[type="checkbox"]:checked')).map((input) => Number(input.value));
                    payload.answers.push({ survey_question_id: questionId, option_ids: optionIds });
                    return;
                }

                const selected = wrapper.querySelector('input[type="radio"]:checked');
                if (selected) {
                    payload.answers.push({ survey_question_id: questionId, option_id: Number(selected.value) });
                }
            });

            try {
                const response = await fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok) {
                    const messages = data.errors ? Object.values(data.errors).flat().join('<br>') : data.message || 'Gönderim başarısız.';
                    throw new Error(messages);
                }

                form.reset();
                window.botProtection?.reset(form);
                const startedAtInput = form.querySelector('input[name="_started_at"]');
                if (startedAtInput) {
                    startedAtInput.value = String(Math.floor(Date.now() / 1000));
                }
                const widget = form.querySelector('.cf-turnstile');
                if (widget && window.turnstile) {
                    try {
                        window.turnstile.reset(widget);
                    } catch (error) {
                        // Ignore widget reset failures.
                    }
                }
                alertBox.classList.remove('d-none');
                alertBox.classList.replace('alert-danger', 'alert-success');
                alertBox.innerHTML = data.message || 'Yanıtınız kaydedildi.';

                if (allowRedirect && data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 600);
                }
            } catch (error) {
                alertBox.classList.remove('d-none');
                alertBox.classList.replace('alert-success', 'alert-danger');
                alertBox.innerHTML = error.message;
            }
        });
    });
</script>
@endpush
