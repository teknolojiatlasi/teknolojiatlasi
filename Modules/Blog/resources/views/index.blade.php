@extends('app::layouts.admin')

@section('title', 'Yazilar')

@push('styles')
<style>
    #blogs-table_wrapper .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    #blogs-table_wrapper .dt-search,
    #blogs-table_wrapper .dt-length {
        margin-bottom: 1rem;
    }

    #blogs-table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<a href="{{ route('blog.create') }}" class="btn btn-primary mb-3"><i class="fa fa-plus-circle"></i> Yeni Yazi</a>

@if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

<div class="x_panel">
    <div class="x_content">
        <div class="table-responsive">
            <table id="blogs-table" class="table table-striped table-bordered dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>Baslik</th>
                        <th>Kategori</th>
                        <th>Durum</th>
                        <th>Islem</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.min.js"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/tables_dynamic-CsH_0klH.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableElement = document.querySelector('#blogs-table');

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
                order: [[0, 'asc']],
                dom: 'Bfrtip',
                ajax: {
                    url: '{{ route('blog.index') }}',
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
                            columns: [0, 1, 2]
                        }
                    },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-info btn-sm' }
                ],
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'category', name: 'category', orderable: false },
                    { data: 'status', name: 'status' },
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
