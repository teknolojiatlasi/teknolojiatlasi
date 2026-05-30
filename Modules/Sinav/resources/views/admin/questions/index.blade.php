@extends('app::layouts.admin')

@section('title', 'Sınav - Sorular')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.min.css">
    <style>
        .question-editor-wrap .jodit-container {
            border: 1px solid #dbe3ee !important;
            border-radius: 12px !important;
            overflow: hidden;
        }
        .question-editor-wrap .jodit-container .jodit-toolbar__box:not(:empty) {
            border-bottom: 1px solid #dbe3ee !important;
            background: #f8fafc;
        }
        .question-editor-wrap .jodit-container .jodit-workplace {
            min-height: 280px;
        }
        .question-editor-wrap .jodit-container .jodit-wysiwyg,
        .question-editor-wrap .jodit-container .jodit-source {
            font-size: 1rem;
            line-height: 1.7;
        }
    </style>
@endpush

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Sorular</h2>
                <small class="text-muted">
                    Ders: {{ $test->topic->lesson->name }} / Konu: {{ $test->topic->title }} / Test: {{ $test->title }}
                </small>
            </div>
            <div>
                <a href="{{ route('sinav.admin.topics.tests.index', $test->topic) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Testler
                </a>
                <a
                    href="{{ route('sinav.admin.questions.import.create', ['lesson_id' => $test->topic->lesson_id, 'topic_id' => $test->topic_id, 'test_id' => $test->id]) }}"
                    class="btn btn-outline-primary"
                >
                    <i class="fa fa-upload"></i> Excel ile Yükle
                </a>
                <button class="btn btn-primary" type="button" data-action="create-question">
                    <i class="fa fa-plus-circle"></i> Yeni Soru
                </button>
            </div>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Soru</th>
                            <th>Doğru</th>
                            <th>Sıra</th>
                            <th>Durum</th>
                            <th style="width: 280px">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="questionRows">
                        @foreach ($questions as $question)
                            @include('sinav::admin.questions._row', ['question' => $question])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="questionForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="questionModalTitle">Soru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="questionId">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Soru Gorseli</label>
                                <input class="form-control" type="file" name="image" id="questionImage" accept="image/*">
                                <div class="form-text">Grafik, harita veya gorsel iceren sorular icin istege baglidir.</div>
                                <div class="text-danger small" data-error="image"></div>
                            </div>
                            <div class="col-12 d-none" id="questionImagePreviewWrap">
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
                                        <div>
                                            <div class="small text-muted mb-2">Mevcut Gorsel</div>
                                            <img id="questionImagePreview" src="" alt="Soru gorsel onizleme" style="max-width: 100%; max-height: 240px; border-radius: 10px;">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" name="remove_image" id="questionRemoveImage">
                                            <label class="form-check-label" for="questionRemoveImage">Gorseli kaldir</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Soru Metni</label>
                                <div class="question-editor-wrap">
                                    <textarea class="form-control" rows="3" name="question_text" id="questionText" required></textarea>
                                </div>
                                <div class="text-danger small" data-error="question_text"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">A</label>
                                <textarea class="form-control" rows="2" name="option_a" id="optionA" required></textarea>
                                <div class="text-danger small" data-error="option_a"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">B</label>
                                <textarea class="form-control" rows="2" name="option_b" id="optionB" required></textarea>
                                <div class="text-danger small" data-error="option_b"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">C</label>
                                <textarea class="form-control" rows="2" name="option_c" id="optionC" required></textarea>
                                <div class="text-danger small" data-error="option_c"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">D</label>
                                <textarea class="form-control" rows="2" name="option_d" id="optionD" required></textarea>
                                <div class="text-danger small" data-error="option_d"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E</label>
                                <textarea class="form-control" rows="2" name="option_e" id="optionE" required></textarea>
                                <div class="text-danger small" data-error="option_e"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Doğru Şık</label>
                                <select class="form-select" name="correct_option" id="correctOption" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                </select>
                                <div class="text-danger small" data-error="correct_option"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sıra</label>
                                <input type="number" class="form-control" name="sort_order" id="questionSortOrder" min="0" value="0">
                                <div class="text-danger small" data-error="sort_order"></div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_active" id="questionActive">
                                    <label class="form-check-label" for="questionActive">Aktif</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Açıklama</label>
                                <textarea class="form-control" rows="3" name="explanation" id="questionExplanation"></textarea>
                                <div class="text-danger small" data-error="explanation"></div>
                            </div>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="questionFormError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.min.js"></script>
    <script>
        (function () {
            function modalApi(el) {
                if (window.bootstrap?.Modal) {
                    const instance = window.bootstrap.Modal.getOrCreateInstance(el);
                    return {
                        show: () => {
                            el.removeAttribute('aria-hidden');
                            instance.show();
                        },
                        hide: () => {
                            if (el.contains(document.activeElement)) {
                                document.activeElement.blur();
                            }
                            instance.hide();
                        },
                    };
                }
                if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
                    return {
                        show: () => {
                            el.removeAttribute('aria-hidden');
                            window.jQuery(el).modal('show');
                        },
                        hide: () => {
                            if (el.contains(document.activeElement)) {
                                document.activeElement.blur();
                            }
                            window.jQuery(el).modal('hide');
                        },
                    };
                }
                return {
                    show: () => {
                        el.style.display = 'block';
                        el.classList.add('show');
                        el.removeAttribute('aria-hidden');
                        el.setAttribute('aria-modal', 'true');
                    },
                    hide: () => {
                        if (el.contains(document.activeElement)) {
                            document.activeElement.blur();
                        }
                        el.classList.remove('show');
                        el.style.display = 'none';
                        el.setAttribute('aria-hidden', 'true');
                        el.removeAttribute('aria-modal');
                    },
                };
            }

            const questionModalEl = document.getElementById('questionModal');
            const modal = modalApi(questionModalEl);
            const rowsEl = document.getElementById('questionRows');
            const formEl = document.getElementById('questionForm');
            const modalTitleEl = document.getElementById('questionModalTitle');
            const errorBoxEl = document.getElementById('questionFormError');
            const imageInputEl = document.getElementById('questionImage');
            const imagePreviewWrapEl = document.getElementById('questionImagePreviewWrap');
            const imagePreviewEl = document.getElementById('questionImagePreview');
            const removeImageEl = document.getElementById('questionRemoveImage');

            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const questionEditor = Jodit.make('#questionText', {
                height: 280,
                minHeight: 280,
                askBeforePasteHTML: false,
                askBeforePasteFromWord: false,
                defaultActionOnPaste: 'insert_as_html',
                buttons: [
                    'source', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'brush', 'paragraph', 'fontsize', '|',
                    'ul', 'ol', 'outdent', 'indent', '|',
                    'align', 'superscript', 'subscript', '|',
                    'table', 'link', 'image', 'video', '|',
                    'undo', 'redo', '|',
                    'hr', 'eraser', 'fullsize'
                ],
                uploader: {
                    insertImageAsBase64URI: false,
                    imagesExtensions: ['jpg', 'png', 'jpeg', 'gif', 'webp'],
                    customUploadFunction: async function (requestData, showProgress) {
                        const formData = new FormData();
                        const uploadedFiles = requestData instanceof FormData
                            ? requestData.getAll('files[0]').concat(requestData.getAll('files[]'))
                            : [];
                        const file = uploadedFiles.find(Boolean);

                        if (!file) {
                            throw new Error('Resim secilmedi.');
                        }

                        formData.append('upload', file);

                        const response = await fetch("{{ route('blog.ckeditor.upload') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json'
                            },
                            body: formData,
                            credentials: 'same-origin'
                        });

                        showProgress(100);

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(data?.error?.message || data?.message || 'Resim yuklenemedi.');
                        }

                        return data;
                    },
                    isSuccess: function (resp) {
                        return !!resp?.url;
                    },
                    getMessage: function (resp) {
                        return resp?.message || '';
                    },
                    process: function (resp) {
                        return {
                            files: resp?.url ? [resp.url] : [],
                            path: '',
                            baseurl: '',
                            error: resp?.url ? 0 : 1,
                            msg: resp?.message || ''
                        };
                    },
                    defaultHandlerSuccess: function (data) {
                        if (data.files && data.files.length) {
                            data.files.forEach((fileUrl) => {
                                this.s.insertImage(fileUrl);
                            });
                        }
                    },
                    defaultHandlerError: function (error) {
                        this.j.message.error(error?.message || error || 'Resim yuklenemedi.');
                    }
                },
                events: {
                    change: function (newValue) {
                        document.getElementById('questionText').value = newValue;
                    }
                }
            });

            questionModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    modal.hide();
                });
            });

            function clearErrors() {
                errorBoxEl.classList.add('d-none');
                errorBoxEl.textContent = '';
                formEl.querySelectorAll('[data-error]').forEach(el => el.textContent = '');
            }

            function setFieldErrors(errors) {
                Object.entries(errors || {}).forEach(([key, messages]) => {
                    const el = formEl.querySelector(`[data-error="${key}"]`);
                    if (el) el.textContent = (messages || []).join(' ');
                });
            }

            function syncImagePreview(url) {
                const hasImage = Boolean(url);
                imagePreviewWrapEl.classList.toggle('d-none', !hasImage);
                imagePreviewEl.src = url || '';
                removeImageEl.checked = false;
            }

            function fillFromRow(row) {
                document.getElementById('questionId').value = row.dataset.id;
                imageInputEl.value = '';
                syncImagePreview(row.dataset.imageUrl || '');
                questionEditor.value = row.dataset.questionText || '';
                document.getElementById('optionA').value = row.dataset.optionA || '';
                document.getElementById('optionB').value = row.dataset.optionB || '';
                document.getElementById('optionC').value = row.dataset.optionC || '';
                document.getElementById('optionD').value = row.dataset.optionD || '';
                document.getElementById('optionE').value = row.dataset.optionE || '';
                document.getElementById('correctOption').value = row.dataset.correctOption || 'A';
                document.getElementById('questionExplanation').value = row.dataset.explanation || '';
                document.getElementById('questionSortOrder').value = row.dataset.sortOrder || '0';
                document.getElementById('questionActive').checked = row.dataset.active === '1';
            }

            function openCreate() {
                clearErrors();
                modalTitleEl.textContent = 'Yeni Soru';
                formEl.reset();
                document.getElementById('questionId').value = '';
                imageInputEl.value = '';
                syncImagePreview('');
                questionEditor.value = '';
                document.getElementById('questionSortOrder').value = '0';
                document.getElementById('questionActive').checked = true;
                modal.show();
            }

            function openEdit(row) {
                clearErrors();
                modalTitleEl.textContent = 'Soru Güncelle';
                fillFromRow(row);
                modal.show();
            }

            imageInputEl.addEventListener('change', () => {
                const [file] = imageInputEl.files || [];
                if (!file) {
                    return;
                }

                removeImageEl.checked = false;
                const reader = new FileReader();
                reader.onload = (event) => syncImagePreview(event.target?.result || '');
                reader.readAsDataURL(file);
            });

            removeImageEl.addEventListener('change', () => {
                if (removeImageEl.checked) {
                    imageInputEl.value = '';
                    imagePreviewWrapEl.classList.add('d-none');
                } else if (imagePreviewEl.src) {
                    imagePreviewWrapEl.classList.remove('d-none');
                }
            });

            async function send(url, method, payload) {
                const res = await fetch(url, {
                    method,
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: payload,
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const error = new Error(json.message || 'İşlem başarısız');
                    error.status = res.status;
                    error.payload = json;
                    throw error;
                }
                return json;
            }

            document.addEventListener('click', async (e) => {
                if (e.target.closest('[data-action="create-question"]')) return openCreate();

                const editBtn = e.target.closest('[data-action="edit-question"]');
                if (editBtn) return openEdit(editBtn.closest('tr'));

                const deleteBtn = e.target.closest('[data-action="delete-question"]');
                if (!deleteBtn) return;

                const row = deleteBtn.closest('tr');
                if (!confirm('Soruyu silmek istediğinize emin misiniz?')) return;

                try {
                    await send(`{{ route('sinav.admin.questions.destroy', ['question' => '__ID__']) }}`.replace('__ID__', row.dataset.id), 'DELETE', null);
                    row.remove();
                } catch (err) {
                    alert(err.payload?.message || err.message);
                }
            });

            formEl.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();

                const payload = new FormData(formEl);
                payload.set('question_text', questionEditor.value);
                payload.set('is_active', document.getElementById('questionActive').checked ? '1' : '0');

                const id = document.getElementById('questionId').value;
                const isEdit = !!id;
                const url = isEdit
                    ? `{{ route('sinav.admin.questions.update', ['question' => '__ID__']) }}`.replace('__ID__', id)
                    : `{{ route('sinav.admin.tests.questions.store', ['test' => $test->id]) }}`;
                if (isEdit) payload.set('_method', 'PUT');

                try {
                    const json = await send(url, 'POST', payload);
                    if (isEdit) {
                        const existing = rowsEl.querySelector(`tr[data-id="${id}"]`);
                        if (existing) existing.outerHTML = json.html;
                    } else {
                        rowsEl.insertAdjacentHTML('afterbegin', json.html);
                    }
                    modal.hide();
                } catch (err) {
                    if (err.status === 422) {
                        setFieldErrors(err.payload?.errors);
                        return;
                    }
                    errorBoxEl.textContent = err.payload?.message || err.message;
                    errorBoxEl.classList.remove('d-none');
                }
            });
        })();
    </script>
@endpush
