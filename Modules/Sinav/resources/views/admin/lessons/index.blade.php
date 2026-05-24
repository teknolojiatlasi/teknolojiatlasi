@extends('app::layouts.admin')

@section('title', 'Sınav - Dersler')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <h2>Dersler</h2>
            <button class="btn btn-primary" type="button" data-action="create-lesson">
                <i class="fa fa-plus-circle"></i> Yeni Ders
            </button>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>Durum</th>
                            <th>Oluşturulma</th>
                            <th style="width: 260px">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="lessonRows">
                        @foreach ($lessons as $lesson)
                            @include('sinav::admin.lessons._row', ['lesson' => $lesson])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="lessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="lessonForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="lessonModalTitle">Ders</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="lessonId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ders Adı</label>
                                <input class="form-control" name="name" id="lessonName" required>
                                <div class="text-danger small" data-error="name"></div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_active" id="lessonActive">
                                    <label class="form-check-label" for="lessonActive">Aktif</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Açıklama</label>
                                <textarea class="form-control" rows="3" name="description" id="lessonDescription"></textarea>
                                <div class="text-danger small" data-error="description"></div>
                            </div>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="lessonFormError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary" id="lessonSaveBtn">Kaydet</button>
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

            const lessonModalEl = document.getElementById('lessonModal');
            const lessonModal = modalApi(lessonModalEl);

            const rowsEl = document.getElementById('lessonRows');
            const formEl = document.getElementById('lessonForm');
            const modalTitleEl = document.getElementById('lessonModalTitle');
            const errorBoxEl = document.getElementById('lessonFormError');

            const idEl = document.getElementById('lessonId');
            const nameEl = document.getElementById('lessonName');
            const descriptionEl = document.getElementById('lessonDescription');
            const activeEl = document.getElementById('lessonActive');

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
                modalTitleEl.textContent = 'Yeni Ders';
                idEl.value = '';
                nameEl.value = '';
                descriptionEl.value = '';
                activeEl.checked = true;
                lessonModal.show();
            }

            function openEdit(row) {
                clearErrors();
                modalTitleEl.textContent = 'Ders Güncelle';
                idEl.value = row.dataset.id;
                nameEl.value = row.dataset.name || '';
                descriptionEl.value = row.dataset.description || '';
                activeEl.checked = row.dataset.active === '1';
                lessonModal.show();
            }

            async function send(url, method, payload) {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
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
                const createBtn = e.target.closest('[data-action="create-lesson"]');
                if (createBtn) return openCreate();

                const editBtn = e.target.closest('[data-action="edit-lesson"]');
                if (editBtn) return openEdit(editBtn.closest('tr'));

                const deleteBtn = e.target.closest('[data-action="delete-lesson"]');
                if (!deleteBtn) return;

                const row = deleteBtn.closest('tr');
                if (!confirm('Dersi silmek istediğinize emin misiniz?')) return;

                try {
                    await send(`{{ route('sinav.admin.lessons.destroy', ['lesson' => '__ID__']) }}`.replace('__ID__', row.dataset.id), 'DELETE', null);
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
                    ? `{{ route('sinav.admin.lessons.update', ['lesson' => '__ID__']) }}`.replace('__ID__', id)
                    : `{{ route('sinav.admin.lessons.store') }}`;
                if (isEdit) payload.set('_method', 'PUT');

                try {
                    const json = await send(url, 'POST', payload);

                    if (isEdit) {
                        const existing = rowsEl.querySelector(`tr[data-id="${json.id}"]`);
                        if (existing) {
                            existing.outerHTML = json.html;
                        }
                    } else {
                        rowsEl.insertAdjacentHTML('afterbegin', json.html);
                    }

                    lessonModal.hide();
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
