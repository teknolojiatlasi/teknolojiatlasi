@extends('app::layouts.admin')

@section('title', 'Sınav - Konular')

@push('styles')
    <style>
        .topics-table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .topics-table thead th {
            border: 0;
            color: #667085;
            font-size: 12px;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .topics-table tbody tr {
            background: #fff;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
        }

        .topics-table tbody tr.topic-child-row {
            background: #f8fbff;
        }

        .topics-table tbody td {
            border-bottom: 1px solid #e7edf5;
            border-top: 1px solid #e7edf5;
            vertical-align: middle;
        }

        .topics-table tbody td:first-child {
            border-left: 4px solid #2a6fdb;
        }

        .topics-table tbody tr.topic-child-row td:first-child {
            border-left-color: #21a67a;
        }

        .topic-title-cell {
            min-width: 360px;
        }

        .topic-tree-item {
            align-items: center;
            display: flex;
            gap: 10px;
            min-height: 38px;
            padding-left: calc(var(--topic-level, 0) * 34px);
            position: relative;
        }

        .topic-tree-item::before {
            background: #c6d4e6;
            content: "";
            display: none;
            height: 1px;
            left: calc((var(--topic-level, 0) * 34px) - 18px);
            position: absolute;
            top: 50%;
            width: 18px;
        }

        .topic-tree-item::after {
            background: #c6d4e6;
            content: "";
            display: none;
            height: 38px;
            left: calc((var(--topic-level, 0) * 34px) - 18px);
            position: absolute;
            top: -19px;
            width: 1px;
        }

        .topic-tree-item.is-child::before,
        .topic-tree-item.is-child::after {
            display: block;
        }

        .topic-icon {
            align-items: center;
            background: #eaf2ff;
            border: 1px solid #cfe0f8;
            border-radius: 6px;
            color: #2a6fdb;
            display: inline-flex;
            flex: 0 0 34px;
            height: 34px;
            justify-content: center;
            width: 34px;
        }

        .topic-child-row .topic-icon {
            background: #e8f7f1;
            border-color: #c5eadc;
            color: #16845f;
        }

        .topic-title-text {
            color: #263238;
            font-weight: 600;
            line-height: 1.25;
        }

        .topic-meta {
            color: #7b8794;
            display: block;
            font-size: 12px;
            margin-top: 3px;
        }

        .topic-level-badge {
            background: #eef4ff;
            border: 1px solid #d6e4ff;
            border-radius: 999px;
            color: #2a5caa;
            font-size: 12px;
            line-height: 1;
            padding: 5px 8px;
            white-space: nowrap;
        }

        .topic-child-row .topic-level-badge {
            background: #ecfdf3;
            border-color: #c8f1d8;
            color: #087443;
        }
    </style>
@endpush

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Konular</h2>
                <small class="text-muted">Ders: {{ $lesson->name }}</small>
            </div>
            <div>
                <a href="{{ route('sinav.admin.lessons.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Dersler
                </a>
                <button class="btn btn-primary" type="button" data-action="create-topic">
                    <i class="fa fa-plus-circle"></i> Yeni Konu
                </button>
            </div>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table class="table topics-table align-middle">
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Durum</th>
                            <th>Sıra</th>
                            <th style="width: 300px">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="topicRows">
                        @include('sinav::admin.topics._tree', ['topics' => $topics])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="topicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="topicForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="topicModalTitle">Konu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="topicId">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Başlık</label>
                                <input class="form-control" name="title" id="topicTitle" required>
                                <div class="text-danger small" data-error="title"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sıra</label>
                                <input type="number" class="form-control" name="sort_order" id="topicSortOrder" min="0" value="0">
                                <div class="text-danger small" data-error="sort_order"></div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Üst Konu</label>
                                <select class="form-select" name="parent_id" id="topicParentId">
                                    @include('sinav::admin.topics._parent_options', ['allTopics' => $allTopics])
                                </select>
                                <div class="text-danger small" data-error="parent_id"></div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_active" id="topicActive">
                                    <label class="form-check-label" for="topicActive">Aktif</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Açıklama</label>
                                <textarea class="form-control" rows="3" name="description" id="topicDescription"></textarea>
                                <div class="text-danger small" data-error="description"></div>
                            </div>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="topicFormError"></div>
                        <div class="alert alert-info mt-3">
                            Not: Alt konu(lar) bulunan bir konu silinemez.
                        </div>
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

            const topicModalEl = document.getElementById('topicModal');
            const topicModal = modalApi(topicModalEl);
            const rowsEl = document.getElementById('topicRows');
            const formEl = document.getElementById('topicForm');
            const modalTitleEl = document.getElementById('topicModalTitle');
            const errorBoxEl = document.getElementById('topicFormError');

            const idEl = document.getElementById('topicId');
            const titleEl = document.getElementById('topicTitle');
            const descEl = document.getElementById('topicDescription');
            const parentEl = document.getElementById('topicParentId');
            const orderEl = document.getElementById('topicSortOrder');
            const activeEl = document.getElementById('topicActive');

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
                modalTitleEl.textContent = 'Yeni Konu';
                idEl.value = '';
                titleEl.value = '';
                descEl.value = '';
                parentEl.value = '';
                orderEl.value = '0';
                activeEl.checked = true;
                topicModal.show();
            }

            function openEdit(row) {
                clearErrors();
                modalTitleEl.textContent = 'Konu Güncelle';
                idEl.value = row.dataset.id;
                titleEl.value = row.dataset.title || '';
                descEl.value = row.dataset.description || '';
                parentEl.value = row.dataset.parentId || '';
                orderEl.value = row.dataset.sortOrder || '0';
                activeEl.checked = row.dataset.active === '1';
                topicModal.show();
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
                if (e.target.closest('[data-action="create-topic"]')) return openCreate();

                const editBtn = e.target.closest('[data-action="edit-topic"]');
                if (editBtn) return openEdit(editBtn.closest('tr'));

                const deleteBtn = e.target.closest('[data-action="delete-topic"]');
                if (!deleteBtn) return;

                const row = deleteBtn.closest('tr');
                if (!confirm('Konuyu silmek istediğinize emin misiniz?')) return;

                try {
                    const json = await send(`{{ route('sinav.admin.topics.destroy', ['topic' => '__ID__']) }}`.replace('__ID__', row.dataset.id), 'DELETE', null);
                    rowsEl.innerHTML = json.tree_html || rowsEl.innerHTML;
                    parentEl.innerHTML = json.parent_options_html || parentEl.innerHTML;
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
                    ? `{{ route('sinav.admin.topics.update', ['topic' => '__ID__']) }}`.replace('__ID__', id)
                    : `{{ route('sinav.admin.lessons.topics.store', ['lesson' => $lesson->id]) }}`;
                if (isEdit) payload.set('_method', 'PUT');

                try {
                    const json = await send(url, 'POST', payload);

                    rowsEl.innerHTML = json.tree_html || rowsEl.innerHTML;
                    parentEl.innerHTML = json.parent_options_html || parentEl.innerHTML;

                    topicModal.hide();
                } catch (err) {
                    if (err.status === 422) {
                        setFieldErrors(err.payload?.errors);
                        errorBoxEl.textContent = err.payload?.message || '';
                        if (errorBoxEl.textContent) errorBoxEl.classList.remove('d-none');
                        return;
                    }
                    errorBoxEl.textContent = err.payload?.message || err.message;
                    errorBoxEl.classList.remove('d-none');
                }
            });
        })();
    </script>
@endpush
