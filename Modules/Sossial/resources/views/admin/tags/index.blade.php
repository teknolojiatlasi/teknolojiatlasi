@extends('layouts.admin')

@section('title', 'Sosial Etiketler')

@push('styles')
<style>
    #sossial-tags-table_wrapper .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    #sossial-tags-table_wrapper .dt-length {
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Sosial Etiketler</h3>
    </div>
    <div class="title_right text-end">
        <a href="{{ route('admin.sossial.tags.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Yeni Etiket
        </a>
    </div>
</div>

<div class="clearfix"></div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="x_panel">
    <div class="x_title">
        <h2>Ara</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form id="tagFilterForm" method="GET" action="{{ route('admin.sossial.tags.index') }}" class="d-flex gap-2">
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Etiket adi veya slug ara...">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Ara</button>
            @if($q !== '')
                <a href="{{ route('admin.sossial.tags.index') }}" class="btn btn-secondary">Temizle</a>
            @endif
        </form>
    </div>
</div>

<div class="x_panel">
    <div class="x_title">
        <h2>Liste</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content table-responsive">
        <table id="sossial-tags-table" class="table table-striped table-bordered dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Slug</th>
                    <th>Post Sayisi</th>
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
        const tableElement = document.querySelector('#sossial-tags-table');
        const filterForm = document.getElementById('tagFilterForm');
        let dataTable = null;

        if (!tableElement) {
            return;
        }

        const bootTable = () => {
            if (typeof window.DataTable === 'undefined') {
                window.setTimeout(bootTable, 100);
                return;
            }

            dataTable = new window.DataTable(tableElement, {
                processing: true,
                serverSide: true,
                searching: false,
                responsive: true,
                pageLength: 10,
                order: [[1, 'asc']],
                dom: 'Bfrtip',
                ajax: {
                    url: '{{ route('admin.sossial.tags.index') }}',
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: function (d) {
                        const formData = new FormData(filterForm);
                        d.q = formData.get('q') || '';
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
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    { data: 'posts_count', name: 'posts_count' },
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

        filterForm?.addEventListener('submit', function (event) {
            event.preventDefault();
            dataTable?.ajax.reload();
        });

        bootTable();
    });
</script>
@endpush
