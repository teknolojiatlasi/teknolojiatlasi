@php
    /** @var \Modules\Sinav\Models\Test $test */
    /** @var string|null $backUrl */
    $backUrl ??= route('sinav.lessons.show', $test->topic->lesson) . '?test_id=' . $test->id;
    $questionCount = $test->questions->count();
@endphp

<style>
    .exam-wizard-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 1rem;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    }

    .exam-wizard-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .exam-wizard-title {
        font-size: clamp(1.35rem, 2vw, 2rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        margin: 0.8rem 0;
    }

    .exam-timer-card {
        min-width: 170px;
        padding: 1rem 1.1rem;
        border-radius: 1.2rem;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        text-align: center;
    }

    .exam-timer-value {
        font-size: 1.8rem;
        line-height: 1;
        font-weight: 900;
    }

    .exam-progress-shell {
        margin-bottom: 1.25rem;
    }

    .exam-progress-shell .progress {
        height: 12px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.16);
    }

    .exam-progress-shell .progress-bar {
        border-radius: 999px;
        background: linear-gradient(90deg, #06b6d4, #2563eb);
    }

    .exam-step-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.4rem;
        background: #fff;
        overflow: hidden;
    }

    .exam-step-head {
        padding: 1rem 1.25rem;
        background: linear-gradient(180deg, #eff6ff, #ffffff);
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
    }

    .exam-step-body {
        padding: 1.25rem;
    }

    .exam-question-text {
        color: #0f172a;
        font-size: 1.08rem;
        line-height: 1.8;
        margin-bottom: 1rem;
    }

    .exam-question-media {
        margin-bottom: 1rem;
        border-radius: 1.2rem;
        overflow: hidden;
        background: #f8fafc;
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .exam-question-media img {
        display: block;
        width: 100%;
        max-height: 420px;
        object-fit: contain;
        background: #fff;
    }

    .exam-option-card {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 0.9rem;
        height: 100%;
        border: 1px solid rgba(59, 130, 246, 0.12);
        border-radius: 1.15rem;
        padding: 1rem 1rem 1rem 0.95rem;
        background:
            linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(6, 182, 212, 0.03)),
            #f8fafc;
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
    }

    .exam-option-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .exam-option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.08);
        border-color: rgba(37, 99, 235, 0.28);
    }

    .exam-option-card:has(.exam-option-input:checked) {
        background:
            linear-gradient(135deg, rgba(37, 99, 235, 0.18), rgba(16, 185, 129, 0.12)),
            #eff6ff;
        border-color: rgba(37, 99, 235, 0.5);
        box-shadow: 0 18px 34px rgba(37, 99, 235, 0.16);
    }

    .exam-option-key {
        width: 2.55rem;
        height: 2.55rem;
        flex: 0 0 auto;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1d4ed8, #06b6d4);
        color: #fff;
        font-weight: 800;
        font-size: 0.98rem;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.2);
    }

    .exam-option-content {
        min-width: 0;
        flex: 1 1 auto;
    }

    .exam-option-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.35rem;
        color: #0f172a;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .exam-option-text {
        color: #334155;
        line-height: 1.6;
        font-size: 0.98rem;
    }

    .exam-option-check {
        width: 1.8rem;
        height: 1.8rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: rgba(255, 255, 255, 0.82);
        color: transparent;
        transition: all 0.18s ease;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
    }

    .exam-option-card:has(.exam-option-input:checked) .exam-option-check {
        background: linear-gradient(135deg, #2563eb, #10b981);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 14px 24px rgba(37, 99, 235, 0.24);
    }

    .exam-option-card:has(.exam-option-input:checked) .exam-option-text {
        color: #0f172a;
    }

    .exam-option-card:has(.exam-option-input:focus-visible) {
        outline: 3px solid rgba(37, 99, 235, 0.2);
        outline-offset: 2px;
    }

    .exam-wizard-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        margin-top: 1.2rem;
    }

    .exam-question-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 1rem;
    }

    .exam-question-chip {
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: #fff;
        color: #334155;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .exam-question-chip.is-current {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }

    .exam-question-chip.is-answered {
        background: #ecfdf5;
        border-color: rgba(16, 185, 129, 0.35);
        color: #047857;
    }

    .exam-inline-next-shell {
        min-height: 3.5rem;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .exam-inline-next-btn {
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s ease;
    }

    .exam-inline-next-btn.is-visible {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
    }

    @media (max-width: 767.98px) {
        .exam-wizard-head {
            grid-template-columns: 1fr;
        }

        .exam-timer-card {
            min-width: 0;
        }

        .exam-step-head,
        .exam-step-body {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .exam-question-text {
            font-size: 1rem;
            line-height: 1.7;
        }

        .exam-option-card {
            padding: 0.9rem;
            border-radius: 1rem;
        }

        .exam-option-key {
            width: 2.35rem;
            height: 2.35rem;
            font-size: 0.92rem;
        }

        .exam-option-title {
            font-size: 0.9rem;
        }

        .exam-option-text {
            font-size: 0.95rem;
        }

        .exam-question-nav {
            justify-content: center;
        }

        .exam-inline-next-shell {
            justify-content: stretch;
        }

        .exam-inline-next-btn {
            width: 100%;
        }

        .exam-wizard-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .exam-wizard-actions .ms-auto {
            margin-left: 0 !important;
            width: 100%;
        }

        .exam-wizard-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="exam-wizard-head">
    <div>
        <span class="exam-wizard-pill"><i class="fa fa-shield"></i> Online çözüm modu</span>
        <h2 class="exam-wizard-title">{{ $test->title }}</h2>
        <div class="text-muted">
            Ders: {{ $test->topic->lesson->name }} · Konu: {{ $test->topic->title }} · {{ $questionCount }} soru
        </div>
    </div>

    <div class="exam-timer-card">
        <div class="small text-uppercase fw-bold mb-2">Kalan süre</div>
        <div class="exam-timer-value" id="timer">--:--</div>
    </div>
</div>

<div class="exam-progress-shell">
    <div class="progress">
        <div class="progress-bar" role="progressbar" id="wizardProgress" style="width: 0%"></div>
    </div>
    <div class="d-flex justify-content-between small text-muted mt-2">
        <div id="wizardStepLabel">Soru -- / --</div>
        <div id="wizardAnsweredLabel">Cevaplanan: 0 / {{ $questionCount }}</div>
    </div>
</div>

<form method="POST" action="{{ route('sinav.tests.submit', $test) }}" id="wizardForm">
    @csrf

    @foreach ($test->questions as $index => $question)
        <div class="exam-step-card wizard-step {{ $index === 0 ? '' : 'd-none' }}" data-step="{{ $index }}">
            <div class="exam-step-head">
                <div class="small text-uppercase fw-bold text-primary">Soru {{ $index + 1 }}</div>
            </div>

            <div class="exam-step-body">
                @if ($question->image_url)
                    <div class="exam-question-media">
                        <img src="{{ $question->image_url }}" alt="Soru gorseli {{ $index + 1 }}">
                    </div>
                @endif
                <div class="exam-question-text">{{ $question->question_text }}</div>

                <div class="row g-3">
                    @foreach ($question->options() as $key => $text)
                        <div class="col-12 col-md-6">
                            <label class="exam-option-card">
                                <input
                                    class="exam-option-input"
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $key }}"
                                >
                                <span class="exam-option-key">{{ $key }}</span>
                                <span class="exam-option-content">
                                    <span class="exam-option-title">

                                        <span class="exam-option-check" aria-hidden="true">
                                            <i class="fa fa-check"></i>
                                        </span>
                                    </span>
                                    <span class="exam-option-text">{{ $text }}</span>
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="exam-inline-next-shell">
                    <button
                        type="button"
                        class="btn btn-primary rounded-pill px-4 exam-inline-next-btn"
                        data-inline-next
                        data-step-index="{{ $index }}"
                    >
                        Sonraki Soru
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    <div class="exam-question-nav" id="wizardQuestionNav">
        @foreach ($test->questions as $index => $question)
            <button
                type="button"
                class="exam-question-chip {{ $index === 0 ? 'is-current' : '' }}"
                data-jump-step="{{ $index }}"
                aria-label="Soru {{ $index + 1 }}"
            >
                {{ $index + 1 }}
            </button>
        @endforeach
    </div>

    <div class="exam-wizard-actions">
        <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ $backUrl }}">Derse Dön</a>
        <div class="ms-auto d-flex flex-wrap gap-2">
            <button class="btn btn-outline-secondary rounded-pill px-4" type="button" id="wizardPrev">Geri</button>
            <button class="btn btn-primary rounded-pill px-4" type="button" id="wizardNext">İleri</button>
            <button class="btn btn-success rounded-pill px-4 d-none" type="submit" id="wizardFinish">Testi Bitir</button>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        (function () {
            const steps = Array.from(document.querySelectorAll('.wizard-step'));
            const chips = Array.from(document.querySelectorAll('[data-jump-step]'));
            const progressEl = document.getElementById('wizardProgress');
            const stepLabelEl = document.getElementById('wizardStepLabel');
            const answeredLabelEl = document.getElementById('wizardAnsweredLabel');
            const prevBtn = document.getElementById('wizardPrev');
            const nextBtn = document.getElementById('wizardNext');
            const finishBtn = document.getElementById('wizardFinish');
            const formEl = document.getElementById('wizardForm');
            const inlineNextButtons = Array.from(document.querySelectorAll('[data-inline-next]'));

            if (!steps.length || !formEl) return;

            let index = 0;
            const total = steps.length;

            function countAnswered() {
                let answered = 0;
                for (const step of steps) {
                    if (step.querySelector('input[type="radio"]:checked')) {
                        answered += 1;
                    }
                }
                return answered;
            }

            function syncNav() {
                chips.forEach((chip, chipIndex) => {
                    chip.classList.toggle('is-current', chipIndex === index);
                    const step = steps[chipIndex];
                    const answered = Boolean(step.querySelector('input[type="radio"]:checked'));
                    chip.classList.toggle('is-answered', answered && chipIndex !== index);
                });
            }

            function syncInlineNextButtons() {
                inlineNextButtons.forEach((button) => {
                    const stepIndex = Number(button.dataset.stepIndex || 0);
                    const step = steps[stepIndex];
                    const hasAnswer = Boolean(step?.querySelector('input[type="radio"]:checked'));
                    const isLast = stepIndex === total - 1;

                    button.classList.toggle('is-visible', hasAnswer && !isLast);
                });
            }

            function setStep(i) {
                index = Math.min(Math.max(i, 0), total - 1);
                steps.forEach((el, idx) => el.classList.toggle('d-none', idx !== index));

                const pct = Math.round(((index + 1) / total) * 100);
                progressEl.style.width = `${pct}%`;
                progressEl.setAttribute('aria-valuenow', String(pct));

                stepLabelEl.textContent = `Soru ${index + 1} / ${total}`;
                answeredLabelEl.textContent = `Cevaplanan: ${countAnswered()} / ${total}`;

                prevBtn.disabled = index === 0;
                const isLast = index === total - 1;
                nextBtn.classList.toggle('d-none', isLast);
                finishBtn.classList.toggle('d-none', !isLast);

                syncNav();
                syncInlineNextButtons();
            }

            prevBtn.addEventListener('click', () => setStep(index - 1));
            nextBtn.addEventListener('click', () => setStep(index + 1));
            formEl.addEventListener('change', () => setStep(index));

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    setStep(Number(chip.dataset.jumpStep || 0));
                });
            });

            inlineNextButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const stepIndex = Number(button.dataset.stepIndex || 0);
                    setStep(stepIndex + 1);
                });
            });

            setStep(0);

            const totalSeconds = {{ (int) $test->duration_minutes }} * 60;
            const timerEl = document.getElementById('timer');
            let remaining = totalSeconds;
            const pad = (n) => String(n).padStart(2, '0');

            function tick() {
                const mm = Math.floor(remaining / 60);
                const ss = remaining % 60;
                timerEl.textContent = `${pad(mm)}:${pad(ss)}`;

                if (remaining <= 0) {
                    formEl.submit();
                    return;
                }

                remaining -= 1;
                setTimeout(tick, 1000);
            }

            if (totalSeconds > 0) {
                tick();
            } else {
                timerEl.textContent = 'Süresiz';
            }
        })();
    </script>
@endpush
