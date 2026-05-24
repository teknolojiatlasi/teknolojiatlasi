@extends('app::layouts.admin')

@section('title', 'Sinav - Excel ile Soru Yukle')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Excel ile Soru Yukleme</h2>
                <small class="text-muted">Once sablonu indirip doldurun, sonra dosyayi yukleyin.</small>
            </div>
            <div>
                <a href="{{ route('sinav.admin.lessons.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Dersler
                </a>
                <a href="{{ route('sinav.admin.questions.import.json.create', request()->only(['lesson_id', 'topic_id', 'test_id'])) }}" class="btn btn-outline-secondary">
                    <i class="fa fa-code"></i> JSON Yukle
                </a>
                <a href="{{ route('sinav.admin.questions.import.template') }}" class="btn btn-outline-primary">
                    <i class="fa fa-download"></i> Sablonu Indir
                </a>
            </div>
        </div>

        <div class="x_content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('import_errors'))
                <div class="alert alert-warning">
                    <div class="fw-bold mb-2">Satir Hatalari</div>
                    <ul class="mb-0">
                        @foreach (session('import_errors') as $item)
                            <li>
                                Satir {{ $item['row'] }}:
                                {{ implode(' | ', $item['messages'] ?? []) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sinav.admin.questions.import.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Ders</label>
                    <select class="form-select" name="lesson_id" id="lessonSelect" required>
                        <option value="">Seciniz</option>
                        @foreach ($lessons as $lesson)
                            <option value="{{ $lesson->id }}" @selected((string) old('lesson_id', $prefill['lesson_id']) === (string) $lesson->id)>
                                {{ $lesson->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Konu</label>
                    <select class="form-select" name="topic_id" id="topicSelect" required disabled>
                        @if (old('topic_id', $prefill['topic_id']) && $prefill['topic_title'])
                            <option value="{{ old('topic_id', $prefill['topic_id']) }}" selected>{{ $prefill['topic_title'] }}</option>
                        @else
                            <option value="">Once ders secin</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Test</label>
                    <select class="form-select" name="test_id" id="testSelect" required disabled>
                        @if (old('test_id', $prefill['test_id']) && $prefill['test_title'])
                            <option value="{{ old('test_id', $prefill['test_id']) }}" selected>{{ $prefill['test_title'] }}</option>
                        @else
                            <option value="">Once konu secin</option>
                        @endif
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Excel Dosyasi</label>
                    <input type="file" class="form-control" name="file" accept=".xlsx,.xls" required>
                    <div class="form-text">
                        Kolon basliklari sablondakiyle birebir ayni olmali: <code>question_text</code>, <code>option_a</code>... <code>is_active</code>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-upload"></i> Yukle ve Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const topicUrl = @json(route('sinav.admin.questions.import.topics'));
            const testUrl = @json(route('sinav.admin.questions.import.tests'));

            const prefillTopicId = @json(old('topic_id', $prefill['topic_id']));
            const prefillTestId = @json(old('test_id', $prefill['test_id']));
            const prefillTopicTitle = @json($prefill['topic_title']);
            const prefillTestTitle = @json($prefill['test_title']);

            const lessonSelect = document.getElementById('lessonSelect');
            const topicSelect = document.getElementById('topicSelect');
            const testSelect = document.getElementById('testSelect');

            function setOptions(select, items, placeholder) {
                select.innerHTML = '';
                const first = document.createElement('option');
                first.value = '';
                first.textContent = placeholder;
                select.appendChild(first);

                (items || []).forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.title;
                    select.appendChild(opt);
                });
            }

            function ensureSelectedOption(select, value, label) {
                if (!value || !label) return;
                const exists = Array.from(select.options).some(option => option.value === String(value));
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = value;
                    opt.textContent = label;
                    select.appendChild(opt);
                }
                select.value = value;
            }

            async function fetchJson(url) {
                const res = await fetch(url, {
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                    },
                });
                if (!res.ok) throw new Error('Veri alinamadi');
                return res.json();
            }

            async function loadTopics(lessonId, selectedTopicId) {
                topicSelect.disabled = true;
                testSelect.disabled = true;
                setOptions(topicSelect, [], 'Yukleniyor...');
                setOptions(testSelect, [], 'Once konu secin');

                if (!lessonId) {
                    setOptions(topicSelect, [], 'Once ders secin');
                    return;
                }

                const json = await fetchJson(`${topicUrl}?lesson_id=${encodeURIComponent(lessonId)}&_=${Date.now()}`);
                setOptions(topicSelect, json.topics || [], 'Seciniz');
                topicSelect.disabled = false;

                if (selectedTopicId) {
                    ensureSelectedOption(topicSelect, selectedTopicId, prefillTopicTitle);
                    await loadTests(selectedTopicId, prefillTestId);
                }
            }

            async function loadTests(topicId, selectedTestId) {
                testSelect.disabled = true;
                setOptions(testSelect, [], 'Yukleniyor...');

                if (!topicId) {
                    setOptions(testSelect, [], 'Once konu secin');
                    return;
                }

                const json = await fetchJson(`${testUrl}?topic_id=${encodeURIComponent(topicId)}&_=${Date.now()}`);
                setOptions(testSelect, json.tests || [], 'Seciniz');
                testSelect.disabled = false;

                if (selectedTestId) {
                    ensureSelectedOption(testSelect, selectedTestId, prefillTestTitle);
                }
            }

            lessonSelect.addEventListener('change', async () => {
                try {
                    await loadTopics(lessonSelect.value, null);
                } catch (e) {
                    setOptions(topicSelect, [], 'Hata olustu');
                    topicSelect.disabled = true;
                }
            });

            topicSelect.addEventListener('change', async () => {
                try {
                    await loadTests(topicSelect.value, null);
                } catch (e) {
                    setOptions(testSelect, [], 'Hata olustu');
                    testSelect.disabled = true;
                }
            });

            if (lessonSelect.value) {
                loadTopics(lessonSelect.value, prefillTopicId).catch(() => {});
            }
        })();
    </script>
@endpush
