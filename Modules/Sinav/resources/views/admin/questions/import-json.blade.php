@extends('app::layouts.admin')

@section('title', 'Sinav - JSON ile Soru Yukle')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>JSON ile Soru Yukleme</h2>
                <small class="text-muted">Once ornek JSON formatini indirin, sonra doldurdugunuz dosyayi secili teste yukleyin.</small>
            </div>
            <div>
                <a href="{{ route('sinav.admin.lessons.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Dersler
                </a>
                <a href="{{ route('sinav.admin.questions.import.create', request()->only(['lesson_id', 'topic_id', 'test_id'])) }}" class="btn btn-outline-secondary">
                    <i class="fa fa-file-excel-o"></i> Excel Yukle
                </a>
                <a href="{{ route('sinav.admin.questions.import.json.template') }}" class="btn btn-outline-primary">
                    <i class="fa fa-download"></i> JSON Ornegi Indir
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
                    <div class="fw-bold mb-2">Soru Hatalari</div>
                    <ul class="mb-0">
                        @foreach (session('import_errors') as $item)
                            <li>
                                Soru {{ $item['row'] }}:
                                {{ implode(' | ', $item['messages'] ?? []) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sinav.admin.questions.import.json.store') }}" enctype="multipart/form-data" class="row g-3">
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
                    <label class="form-label">JSON Dosyasi</label>
                    <input type="file" class="form-control" name="file" accept=".json,application/json" required>
                    <div class="form-text">
                        Dosya <code>{"questions": [...]}</code> formatinda olmali. Alanlar:
                        <code>question_text</code>, <code>option_a</code>, <code>option_b</code>, <code>option_c</code>, <code>option_d</code>, <code>option_e</code>, <code>correct_option</code>, <code>explanation</code>, <code>sort_order</code>, <code>is_active</code>.
                    </div>
                </div>

                <div class="col-12">
                    <pre class="bg-light border rounded p-3 mb-0"><code>{
  "questions": [
    {
      "question_text": "Ornek soru metni",
      "option_a": "A sikki",
      "option_b": "B sikki",
      "option_c": "C sikki",
      "option_d": "D sikki",
      "option_e": "E sikki",
      "correct_option": "A",
      "explanation": "Kisa aciklama (opsiyonel)",
      "sort_order": 0,
      "is_active": true
    }
  ]
}</code></pre>
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
