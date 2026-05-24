@extends('app::layouts.admin')

@section('title', 'Sınav - Testler')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Testler</h2>
                <small class="text-muted">
                    Ders: {{ $topic->lesson->name }} / Konu: {{ $topic->title }}
                </small>
            </div>
            <div>
                <a href="{{ route('sinav.admin.lessons.topics.index', $topic->lesson) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Konular
                </a>
                <button class="btn btn-primary" type="button" data-action="create-test">
                    <i class="fa fa-plus-circle"></i> Yeni Test
                </button>
            </div>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>Süre (dk)</th>
                            <th>Durum</th>
                            <th style="width: 300px">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="testRows">
                        @foreach ($tests as $test)
                            @include('sinav::admin.tests._row', ['test' => $test])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="testModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="testForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="testModalTitle">Test</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="testId">
                        <div class="mb-3">
                            <label class="form-label">Test Adı</label>
                            <input class="form-control" name="title" id="testTitle" required>
                            <div class="text-danger small" data-error="title"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Süre (dk)</label>
                            <input type="number" class="form-control" name="duration_minutes" id="testDuration" min="1" value="20" required>
                            <div class="text-danger small" data-error="duration_minutes"></div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="is_active" id="testActive">
                            <label class="form-check-label" for="testActive">Aktif</label>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="testFormError"></div>
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
    <script>
        (function () {
            function modalApi(el) {
                if (window.bootstrap?.Modal) {
                    const instance = window.bootstrap.Modal.getOrCreateInstance(el);
                    return { show: () => instance.show(), hide: () => instance.hide() };
                }
                if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
                    return { show: () => window.jQuery(el).modal('show'), hide: () => window.jQuery(el).modal('hide') };
                }
                return {
                    show: () => { el.style.display = 'block'; el.classList.add('show'); },
                    hide: () => { el.classList.remove('show'); el.style.display = 'none'; },
                };
            }

            const modal = modalApi(document.getElementById('testModal'));
            const rowsEl = document.getElementById('testRows');
            const formEl = document.getElementById('testForm');
            const modalTitleEl = document.getElementById('testModalTitle');
            const errorBoxEl = document.getElementById('testFormError');

            const idEl = document.getElementById('testId');
            const titleEl = document.getElementById('testTitle');
            const durationEl = document.getElementById('testDuration');
            const activeEl = document.getElementById('testActive');

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

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

            function openCreate() {
                clearErrors();
                modalTitleEl.textContent = 'Yeni Test';
                idEl.value = '';
                titleEl.value = '';
                durationEl.value = '20';
                activeEl.checked = true;
                modal.show();
            }

            function openEdit(row) {
                clearErrors();
                modalTitleEl.textContent = 'Test Güncelle';
                idEl.value = row.dataset.id;
                titleEl.value = row.dataset.title || '';
                durationEl.value = row.dataset.duration || '20';
                activeEl.checked = row.dataset.active === '1';
                modal.show();
            }

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
                if (e.target.closest('[data-action="create-test"]')) return openCreate();

                const editBtn = e.target.closest('[data-action="edit-test"]');
                if (editBtn) return openEdit(editBtn.closest('tr'));

                const deleteBtn = e.target.closest('[data-action="delete-test"]');
                if (!deleteBtn) return;

                const row = deleteBtn.closest('tr');
                if (!confirm('Testi silmek istediğinize emin misiniz?')) return;

                try {
                    await send(`{{ route('sinav.admin.tests.destroy', ['test' => '__ID__']) }}`.replace('__ID__', row.dataset.id), 'DELETE', null);
                    row.remove();
                } catch (err) {
                    alert(err.payload?.message || err.message);
                }
            });

            formEl.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();

                const payload = new FormData(formEl);
                payload.set('is_active', activeEl.checked ? '1' : '0');

                const id = idEl.value;
                const isEdit = !!id;
                const url = isEdit
                    ? `{{ route('sinav.admin.tests.update', ['test' => '__ID__']) }}`.replace('__ID__', id)
                    : `{{ route('sinav.admin.topics.tests.store', ['topic' => $topic->id]) }}`;
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
