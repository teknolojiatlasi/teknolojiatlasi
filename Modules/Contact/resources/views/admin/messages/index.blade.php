@extends('app::layouts.admin')

@section('title', 'Iletisim - Mesajlar')

@push('styles')
<style>
    #contact-messages-table_wrapper .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    #contact-messages-table_wrapper .dt-search,
    #contact-messages-table_wrapper .dt-length {
        margin-bottom: 1rem;
    }

    #contact-messages-table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center">
            <div>
                <h2>Mesajlar</h2>
                <div class="small text-muted">Okunmamis: <span id="unreadCount">{{ $unreadCount }}</span></div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-secondary" href="{{ route('contact_admin_settings_edit') }}">
                    <i class="fa fa-gear"></i> Ayarlar
                </a>
            </div>
        </div>

        <div class="x_content">
            <div class="table-responsive">
                <table id="contact-messages-table" class="table table-bordered align-middle dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Durum</th>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Konu</th>
                            <th>Gonderim</th>
                            <th style="width: 240px">Islem</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mesaj Detayi</h5>
                    <button type="button"
                        class="btn-close"
                        data-role="message-modal-close"
                        data-bs-dismiss="modal"
                        data-dismiss="modal"
                        aria-label="Kapat"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                    <div class="text-muted">Yukleniyor...</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.min.js"></script>
    <script type="module" src="{{ asset('vendor/gentelella/js/tables_dynamic-CsH_0klH.js') }}"></script>
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

            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const unreadCountEl = document.getElementById('unreadCount');
            const modalEl = document.getElementById('messageModal');
            const modal = modalApi(modalEl);
            const modalBodyEl = document.getElementById('messageModalBody');
            const tableElement = document.getElementById('contact-messages-table');
            let dataTable = null;

            modalEl.querySelector('[data-role="message-modal-close"]')?.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
            });

            function updateUnreadCount(delta) {
                const current = Number.parseInt(unreadCountEl.textContent || '0', 10) || 0;
                unreadCountEl.textContent = String(Math.max(0, current + delta));
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
                    const error = new Error(json.message || 'Islem basarisiz');
                    error.status = res.status;
                    error.payload = json;
                    throw error;
                }

                return json;
            }

            function bootTable() {
                if (typeof window.DataTable === 'undefined') {
                    window.setTimeout(bootTable, 100);
                    return;
                }

                dataTable = new window.DataTable(tableElement, {
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 10,
                    order: [[4, 'desc']],
                    dom: 'Bfrtip',
                    ajax: {
                        url: '{{ route('contact_admin_messages_index') }}',
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    },
                    buttons: [
                        { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-secondary btn-sm' },
                        { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-success btn-sm' },
                        { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-primary btn-sm' },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'btn btn-danger btn-sm',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-info btn-sm' }
                    ],
                    columns: [
                        { data: 'status', name: 'status', orderable: false, searchable: false },
                        { data: 'full_name', name: 'full_name' },
                        { data: 'email', name: 'email' },
                        { data: 'subject', name: 'subject' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    language: {
                        search: 'Ara:',
                        lengthMenu: '_MENU_ kayit goster',
                        info: '_TOTAL_ kayittan _START_ - _END_ arasi',
                        infoEmpty: 'Kayit bulunamadi',
                        infoFiltered: '(_MAX_ kayit icinden filtrelendi)',
                        zeroRecords: 'Eslesen kayit bulunamadi',
                        processing: 'Yukleniyor...',
                        paginate: {
                            first: 'Ilk',
                            last: 'Son',
                            next: 'Sonraki',
                            previous: 'Onceki'
                        }
                    }
                });
            }

            tableElement.addEventListener('click', async (e) => {
                const btn = e.target.closest('button[data-action]');
                if (!btn) return;

                const action = btn.dataset.action;
                const row = btn.closest('tr[data-id]');
                const isRead = row?.dataset.isRead === '1';

                if (action === 'view') {
                    modalBodyEl.innerHTML = '<div class="text-muted">Yukleniyor...</div>';
                    modal.show();
                    try {
                        const json = await send(btn.dataset.url, 'GET');
                        modalBodyEl.innerHTML = json.html || '<div class="text-muted">Icerik bulunamadi.</div>';
                        if (!isRead) {
                            updateUnreadCount(-1);
                        }
                        dataTable?.ajax.reload(null, false);
                    } catch (err) {
                        modalBodyEl.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
                    }
                    return;
                }

                if (action === 'mark-unread') {
                    try {
                        await send(btn.dataset.url, 'PUT');
                        if (isRead) {
                            updateUnreadCount(1);
                        }
                        dataTable?.ajax.reload(null, false);
                    } catch (err) {
                        alert(err.message);
                    }
                    return;
                }

                if (action === 'mark-read') {
                    try {
                        await send(btn.dataset.url, 'PUT');
                        if (!isRead) {
                            updateUnreadCount(-1);
                        }
                        dataTable?.ajax.reload(null, false);
                    } catch (err) {
                        alert(err.message);
                    }
                }
            });

            modalEl.addEventListener('submit', async (e) => {
                const form = e.target.closest('form[data-action="reply"]');
                if (!form) return;

                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const errorBox = form.querySelector('[data-role="form-error"]');
                const okBox = form.querySelector('[data-role="form-ok"]');
                form.querySelectorAll('[data-error]').forEach(el => el.textContent = '');
                if (errorBox) { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
                if (okBox) { okBox.classList.add('d-none'); okBox.textContent = ''; }
                if (btn) btn.disabled = true;

                try {
                    const json = await send(form.action, 'POST', new FormData(form));
                    if (okBox) { okBox.textContent = json.message || 'Yanit gonderildi.'; okBox.classList.remove('d-none'); }
                    dataTable?.ajax.reload(null, false);
                } catch (err) {
                    if (err.status === 422 && err.payload?.errors) {
                        Object.entries(err.payload.errors).forEach(([key, messages]) => {
                            const el = form.querySelector(`[data-error="${key}"]`);
                            if (el) el.textContent = (messages || []).join(' ');
                        });
                        return;
                    }
                    if (errorBox) { errorBox.textContent = err.message; errorBox.classList.remove('d-none'); }
                    else alert(err.message);
                } finally {
                    if (btn) btn.disabled = false;
                }
            });

            bootTable();
        })();
    </script>
@endpush
