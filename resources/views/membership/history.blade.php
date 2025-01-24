@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data {{ $title ?? '' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data {{ $title ?? '' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    {{-- ./Content Header --}}  
    
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar {{ $title ?? '' }} </h3>
            </div>
            <div class="card-body">
                <!-- Filter Box -->
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title text-lightblue" style="padding-right: 50%" data-card-widget="collapse">
                            <i class="fas fa-filter"></i>&nbsp;&nbsp;
                            Alat & Pencarian
                        </h3>
                        <div class="card-tools">
                            <div class="d-flex flex-row ">
                                {{-- Filter Table --}}
                                <div id="actionLength" class="mt-2"></div>&nbsp;
                                {{-- Action Table --}}
                                @if(Helper::checkACL('membership', 'e'))
                                    <div id="actionFilter" class="mt-2"></div>&nbsp;
                                @endif
                                {{-- Other Action --}}
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool  " data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                                        <a href="#" class="dropdown-item" data-card-widget="collapse">
                                            <i class="fas fa-search-plus"></i> &nbsp; Tampilkan
                                        </a>
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
                                        <label for="membership_nama_filter">Nama</label>
                                        <input type="text" class="form-control form-control-sm" name="membership_name" id="name" placeholder="Filter Nama">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="membership_code_filter">Nomor Handphone</label>
                                        <input type="text" class="form-control form-control-sm" name="membership_phone" id="code" placeholder="Filter Nomor Handphone">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-footer">
                            <div class="float-right ">
                                <button type="button" class="btn btn-info btn-sm" id="btnFilter"><i class="fab fa-searchengin"></i> Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table id="dTable" class="table table-bordered table-sm table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Kode Member</th>
                            <th>Member</th>
                            <th>Nomor Handphone</th>
                            <th>Waktu</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody class="tBody">
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@section('jScript')
<script>
    var dTable = $('#dTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 5,
        responsive: true,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
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
                    columns: [1, 2, 3, 4]
                },
            },
            {
                extend: 'copy',
                copy: 'Salin',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-copy"></i>  Salin',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                },
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel"></i> Excel',
                exportOptions: {
                    columns: [1, 2, 3, 4]
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
            sLengthMenu: "_MENU_",
        },
        ajax: {
            url: '{{ route("keanggotaan.history.datatable") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
                d.membership_name = $('input[name=membership_name]').val();
                d.membership_phone = $('input[name=membership_phone]').val();
            }
        },
        columns: [
            {data: 'member_id', name: 'member_id'},
            {data: 'full_name', name: 'full_name'},
            {data: 'nomor_handphone', name: 'nomor_handphone'},
            {
                mData: "created_at",
                mRender: function (data, type, row) {
                    return data;
                }
            },
            {
                mData: "point",
                className: 'text-right',
                mRender: function (data, type, row) {
                    var point = "";
                    if(row.type == 'masuk'){
                        point = "<small class='text-success'>+"+row.point+" poin</small>";
                    } else {
                        point = "<small class='text-danger'>-"+row.point+" poin</small>";
                    }

                    var display = "<span>"+maskRupiah("", row.transaction_amount)+"</span><br>"+point;

                    return display;
                }
            }
        ],
    });
    
          //  sumbit filter and redraw
    $('#btnFilter').on('click', function (e) {
        dTable.draw();
        e.preventDefault();
    });
</script>
@endsection