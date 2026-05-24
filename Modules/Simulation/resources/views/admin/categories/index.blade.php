@extends('app::layouts.admin')

@section('title', 'Simulasyon Kategorileri')

@push('styles')
    <style>
        .categories-table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .categories-table thead th {
            border: 0;
            color: #667085;
            font-size: 12px;
            text-transform: uppercase;
        }

        .categories-table tbody tr {
            background: #fff;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
        }

        .categories-table tbody tr.category-child-row {
            background: #f8fbff;
        }

        .categories-table tbody td {
            border-bottom: 1px solid #e7edf5;
            border-top: 1px solid #e7edf5;
            vertical-align: middle;
        }

        .categories-table tbody td:first-child {
            border-left: 4px solid #2a6fdb;
        }

        .categories-table tbody tr.category-child-row td:first-child {
            border-left-color: #21a67a;
        }

        .category-tree-item {
            align-items: center;
            display: flex;
            gap: 10px;
            min-height: 38px;
            padding-left: calc(var(--category-level, 0) * 34px);
            position: relative;
        }

        .category-tree-item::before {
            background: #c6d4e6;
            content: "";
            display: none;
            height: 1px;
            left: calc((var(--category-level, 0) * 34px) - 18px);
            position: absolute;
            top: 50%;
            width: 18px;
        }

        .category-tree-item::after {
            background: #c6d4e6;
            content: "";
            display: none;
            height: 38px;
            left: calc((var(--category-level, 0) * 34px) - 18px);
            position: absolute;
            top: -19px;
            width: 1px;
        }

        .category-tree-item.is-child::before,
        .category-tree-item.is-child::after {
            display: block;
        }

        .category-icon {
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

        .category-title-text {
            color: #263238;
            font-weight: 600;
        }

        .category-meta {
            color: #7b8794;
            display: block;
            font-size: 12px;
            margin-top: 3px;
        }

        .category-level-badge {
            background: #eef4ff;
            border: 1px solid #d6e4ff;
            border-radius: 999px;
            color: #2a5caa;
            font-size: 12px;
            padding: 5px 8px;
        }

        #categoryRows tr {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Simulasyon Kategori Agaci</h2>
                <small class="text-muted">Ders, alt kategori, konu ve alt konu yapisi ayni agac uzerinden yonetilir.</small>
            </div>
            <div>
                <a href="{{ route('simulation.admin.simulations.index') }}" class="btn btn-secondary">
                    <i class="fa fa-cubes"></i> Simulasyonlar
                </a>
                <button class="btn btn-primary" type="button" data-action="create-category">
                    <i class="fa fa-plus-circle"></i> Yeni Dugum
                </button>
            </div>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table class="table categories-table align-middle">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>Slug</th>
                            <th>Durum</th>
                            <th>Sira</th>
                            <th style="width: 220px">Islem</th>
                        </tr>
                    </thead>
                    <tbody id="categoryRows">
                        @include('simulation::admin.categories._tree', ['categories' => $categories])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="categoryForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalTitle">Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="categoryId">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Ad</label>
                                <input class="form-control" name="name" id="categoryName" required>
                                <div class="text-danger small" data-error="name"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sira</label>
                                <input type="number" class="form-control" name="sort_order" id="categorySortOrder" min="0" value="0">
                                <div class="text-danger small" data-error="sort_order"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ust Dugum</label>
                                <select class="form-select" name="parent_id" id="categoryParentId">
                                    @include('simulation::admin.categories._parent_options', ['allCategories' => $allCategories])
                                </select>
                                <div class="text-danger small" data-error="parent_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ikon Sinifi</label>
                                <input class="form-control" name="icon" id="categoryIcon" placeholder="fa-flask">
                                <div class="text-danger small" data-error="icon"></div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_active" id="categoryActive">
                                    <label class="form-check-label" for="categoryActive">Aktif</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Aciklama</label>
                                <textarea class="form-control" rows="3" name="description" id="categoryDescription"></textarea>
                                <div class="text-danger small" data-error="description"></div>
                            </div>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="categoryFormError"></div>
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

            const modalEl = document.getElementById('categoryModal');
            const modal = modalApi(modalEl);
            const rowsEl = document.getElementById('categoryRows');
            const formEl = document.getElementById('categoryForm');
            const parentEl = document.getElementById('categoryParentId');
            const errorBoxEl = document.getElementById('categoryFormError');

            const idEl = document.getElementById('categoryId');
            const titleEl = document.getElementById('categoryName');
            const descEl = document.getElementById('categoryDescription');
            const iconEl = document.getElementById('categoryIcon');
            const orderEl = document.getElementById('categorySortOrder');
            const activeEl = document.getElementById('categoryActive');
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
                idEl.value = '';
                titleEl.value = '';
                descEl.value = '';
                iconEl.value = '';
                parentEl.value = '';
                orderEl.value = '0';
                activeEl.checked = true;
                document.getElementById('categoryModalTitle').textContent = 'Yeni Dugum';
                modal.show();
            }

            function openEdit(row) {
                clearErrors();
                idEl.value = row.dataset.id;
                titleEl.value = row.dataset.name || '';
                descEl.value = row.dataset.description || '';
                iconEl.value = row.dataset.icon || '';
                parentEl.value = row.dataset.parentId || '';
                orderEl.value = row.dataset.sortOrder || '0';
                activeEl.checked = row.dataset.active === '1';
                document.getElementById('categoryModalTitle').textContent = 'Dugum Duzenle';
                modal.show();
            }

            async function send(url, method, payload) {
                const response = await fetch(url, {
                    method,
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: payload,
                });
                const json = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const error = new Error(json.message || 'Islem basarisiz');
                    error.status = response.status;
                    error.payload = json;
                    throw error;
                }
                return json;
            }

            document.addEventListener('click', async (event) => {
                const row = event.target.closest('#categoryRows tr[data-public-url]');
                if (
                    row &&
                    !event.target.closest('button') &&
                    !event.target.closest('a') &&
                    !event.target.closest('[data-action]')
                ) {
                    window.open(row.dataset.publicUrl, '_blank');
                    return;
                }

                if (event.target.closest('[data-action="create-category"]')) return openCreate();

                const editBtn = event.target.closest('[data-action="edit-category"]');
                if (editBtn) return openEdit(editBtn.closest('tr'));

                const deleteBtn = event.target.closest('[data-action="delete-category"]');
                if (!deleteBtn) return;

                const deleteRow = deleteBtn.closest('tr');
                if (!confirm('Bu dugumu silmek istediginize emin misiniz?')) return;

                try {
                    const json = await send(`{{ route('simulation.admin.categories.destroy', ['category' => '__ID__']) }}`.replace('__ID__', deleteRow.dataset.id), 'DELETE', null);
                    rowsEl.innerHTML = json.tree_html || rowsEl.innerHTML;
                    parentEl.innerHTML = json.parent_options_html || parentEl.innerHTML;
                } catch (error) {
                    alert(error.payload?.message || error.message);
                }
            });

            formEl.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearErrors();

                const payload = new FormData(formEl);
                payload.set('is_active', activeEl.checked ? '1' : '0');

                const id = idEl.value;
                const isEdit = !!id;
                const url = isEdit
                    ? `{{ route('simulation.admin.categories.update', ['category' => '__ID__']) }}`.replace('__ID__', id)
                    : `{{ route('simulation.admin.categories.store') }}`;

                if (isEdit) payload.set('_method', 'PUT');

                try {
                    const json = await send(url, 'POST', payload);
                    rowsEl.innerHTML = json.tree_html || rowsEl.innerHTML;
                    parentEl.innerHTML = json.parent_options_html || parentEl.innerHTML;
                    modal.hide();
                } catch (error) {
                    if (error.status === 422) {
                        setFieldErrors(error.payload?.errors);
                        errorBoxEl.textContent = error.payload?.message || '';
                        if (errorBoxEl.textContent) errorBoxEl.classList.remove('d-none');
                        return;
                    }

                    errorBoxEl.textContent = error.payload?.message || error.message;
                    errorBoxEl.classList.remove('d-none');
                }
            });
        })();
    </script>
@endpush
