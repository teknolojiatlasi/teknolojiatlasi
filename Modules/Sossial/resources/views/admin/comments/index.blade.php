@extends('layouts.admin')

@section('title', 'Sosial Yorumlar')

@push('styles')
<style>
    #sossial-comments-table_wrapper .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    #sossial-comments-table_wrapper .dt-search,
    #sossial-comments-table_wrapper .dt-length {
        margin-bottom: 1rem;
    }

    #sossial-comments-table td {
        vertical-align: middle;
    }

    #sossial-comments-table .comment-body,
    #sossial-comments-table .post-body {
        white-space: normal;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Sosial Yorumlar</h3>
    </div>
</div>

<div class="clearfix"></div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="x_panel">
    <div class="x_title">
        <h2>Liste</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content table-responsive">
        <table id="sossial-comments-table" class="table table-striped table-bordered dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Yazar</th>
                    <th>Tur</th>
                    <th>Post</th>
                    <th>Yorum</th>
                    <th>Tarih</th>
                    <th>Islem</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.min.js"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/tables_dynamic-CsH_0klH.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableElement = document.querySelector('#sossial-comments-table');

        if (!tableElement) {
            return;
        }

        const bootTable = () => {
            if (typeof window.DataTable === 'undefined') {
                window.setTimeout(bootTable, 100);
                return;
            }

            new window.DataTable(tableElement, {
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                order: [[5, 'desc']],
                dom: 'Bfrtip',
                ajax: {
                    url: '{{ route('admin.sossial.comments.index') }}',
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
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-info btn-sm' }
                ],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user', name: 'user', orderable: false },
                    { data: 'type', name: 'type', orderable: false, searchable: false },
                    { data: 'post', name: 'post', orderable: false, className: 'post-body' },
                    { data: 'body', name: 'body', orderable: false, className: 'comment-body' },
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
        };

        bootTable();
    });
</script>
@endpush
