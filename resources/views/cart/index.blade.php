@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Keranjang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active">Keranjang</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    {{-- ./Content Header --}}
 
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Keranjang</h3>
            </div>
            <div class="card-body">
                <!-- Filter Box -->
                <div class="card collapsed-card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 col-sm-3 col-lg-4">
                                <h3 class="card-title text-lightblue" style="padding-right: 50%" data-card-widget="collapse">
                                    <i class="fas fa-filter"></i>&nbsp;&nbsp;
                                    Alat & Pencarian
                                </h3>
                            </div>
                            <div class="col-12 col-sm-9 col-lg-8">
                                <div class="d-flex flex-row justify-content-end">
                                    {{-- Filter Table --}}
                                    <div id="actionLength" class="mt-2">
                                    </div>&nbsp;
                                    {{-- Action Table --}}
                                    @if(Helper::checkACL('master_unit', 'e'))
                                    <div id="actionFilter" class="mt-2"></div>&nbsp;
                                    @endif
                                    {{-- Other Action --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-tool  " data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>
                
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                                            @if(Helper::checkACL('master_unit', 'c'))
                                                <a href="{{ url('sales/cart/create') }}" id="newButton" class="dropdown-item"> <i class="fas fa-plus-square"></i>&nbsp; Data Baru</a>
                                            @endif
                                            @if(Helper::checkACL('master_unit', 'i'))
                                                <a href="#" class="dropdown-item"> <i class="fas fa-file-excel"></i> &nbsp; Impor Data</a>
                                            @endif
                                            <a href="#" class="dropdown-item" data-card-widget="collapse">
                                                <i class="fas fa-search-plus"></i> &nbsp; Tampilkan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="display: none;">
                    <form method="POST" id="filterForm" class="form columnForm" role="form">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                <label for="unit_name_filter">Nama</label>
                                <input type="text" class="form-control form-control-sm" name="unit_name_filter" id="name"
                                    placeholder="Filter Nama">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                <label for="unit_code_filter">Kode</label>
                                <input type="text" class="form-control form-control-sm" name="unit_code_filter" id="code"
                                    placeholder="Filter Code">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                <label for="unit_status_filter">Status</label>
                                <select class="form-control form-control-sm select2" style="width: 100%;"
                                    name="unit_status_filter">
                                    <option value='1'>Aktif</option>
                                    <option value='0'>Non-Aktif</option>
                                    <option value='-1' selected="selected">All</option>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right ">
                            <button type="submit" class="btn btn-info btn-sm"><i class="fab fa-searchengin"></i> Cari</button>
                        </div>
                    </div>
                </div>
                    </form>
                <!-- /.Filter Box -->
                <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr align="center">
                            <th> Aksi </th>
                            <th> Nomor Keranjang </th>
                            <th> Sales </th>
                            <th> Business Partner </th>
                            <th> Grandtotal </th>
                        </tr>
                    </thead>
                    <tbody class="tBody">
                    </tbody>
                </table>
            </div>
        </div>
        {{-- modal Edit Data --}}
        <div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-body" id="modalBody">
                    {{-- getByAJAX --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- ./modal Edit Data --}}
    </section>
 </div>
@endsection

@section('jScript')
<script>
    $(document).ready(function () {
        var dTable = $('#dTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 5,
            responsive: true,
            "order": [[1, "asc"]],
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            stateSave: true,
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-eye"></i> Kolom',
                    className: 'btn btn-info btn-sm',
                    postfixButtons: [{
                        extend: 'colvisRestore',
                        text: 'Reset V.bility',
                    }]
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Cetak',
                    className: 'btn bg-navy btn-sm',

                    exportOptions: {
                        columns: [1, 2]
                    },
                },
                {
                    extend: 'copy',
                    copy: 'Salin',
                    className: 'btn btn-warning btn-sm',
                    text: '<i class="fas fa-copy"></i>  Salin',
                    exportOptions: {
                        columns: [1, 2]
                    },
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    exportOptions: {
                        columns: [1, 2]
                    },

                },
                {
                    text: '<i class="fas fa-sync-alt"></i> Reset',
                    className: 'btn btn-danger btn-sm',
                    action: function (e, dt, node, config) {
                        $('#filterForm')[0].reset();
                        $('#dTable tfoot input').val('').change();
                        $("#dTable").DataTable().columns().visible(true);
                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                        $("#dTable").DataTable().columns().search("").draw()
                        $("#dTable").DataTable().draw();
                    },
                },
            ],

            language: {
            searchPlaceholder: "Pencarian Global ",
                "sLengthMenu": "_MENU_",
            },
            ajax: {
                url: '{{ route("sales.cart.datatable") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    d.unit_name_filter = $('input[name=unit_name_filter]').val();
                    d.unit_kode_filter = $('input[name=unit_kode_filter]').val();
                    d.unit_date_filter = $('input[name=unit_date_filter]').val();
                    d.unit_status_filter = $('select[name=unit_status_filter] option').filter(':selected').val()
                }
            },
            columns: [
                {data: 'action', name: 'action', 'orderable': false, 'searchable': false},
                {data: 'docnum', name: 'docnum'},
                {data: 'full_name', name: 'full_name'},
                {data: 'cardname', name: 'cardname'},
                {
                    mData: "grandtotal",
                    className: 'text-right',
                    mRender: function (data, type, row) {
                        return maskRupiah("", data);
                    }
                }
            ],
        });
    });
</script>
@endsection