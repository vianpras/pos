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
                                <div id="actionLength" class="mt-2">
                                </div>&nbsp;
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
                                        @if(Helper::checkACL('cash_in', 'c'))
                                        <a href="{{ url('/cash/in/create') }}" id="newButton" class="dropdown-item"> <i class="fas fa-plus-square"></i>&nbsp; Data Baru</a>
                                        @endif
                                        @if(Helper::checkACL('cash_in', 'i'))
                                        <a href="#" class="dropdown-item"> <i class="fas fa-file-excel"></i>&nbsp; Impor Data</a>
                                        @endif
                                        <a href="#" class="dropdown-item" data-card-widget="collapse"><i class="fas fa-search-plus"></i>&nbsp;Tampilkan</a>
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
                                    <label for="nomor_dokumen_filter">Nomor Dokumen</label>
                                    <input type="text" class="form-control form-control-sm" name="nomor_dokumen_filter" id="nomor_dokumen_filter" placeholder="Filter Nomor Dokumen">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Cakupan Tanggal</label>
                                    <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm float-right" name="tgl_transaksi_filter" id="reservation" placeholder="Range Tanggal" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right ">
                        {{-- <button type="button" class="btn btn-sm bg-olive my-2"> <i class="fas fa-file-excel"></i>
                            import</button>&nbsp; --}}
                        <button type="button" class="btn btn-info btn-sm" id="btnFilter"><i class="fab fa-searchengin"></i> Cari</button>
                        </div>
                    </div>
                </div>
                </form>
    
                <!-- /.Filter Box -->
                <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr align="center">
                            <th>Aksi</th>
                            <th>Nomor Dokumen</th>
                            <th>Tanggal Transaksi</th>
                            <th>Diterima dari</th>
                            <th>Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="tBody"></tbody>
                </table>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="modalDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="detailTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr align="center">
                                    <th>Akun Pendapatan</th>
                                    <th>Tanggal Pelaksanaan</th>
                                    <th>Keterangan</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="tBody"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">Total Nominal</th>
                                    <th style="text-align:right"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
        "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        ajax: {
            url: "{{ url('cash/in/datatable') }}",
            data: function (d) {
                d.nomor_dokumen = $('#nomor_dokumen_filter').val(),
                d.date = $('#reservation').val()
            }
        },
        columns: [
            {data: 'action', name: 'action'},
            {data: 'nomor_dokumen', name: 'nomor_dokumen'},
            {data: 'tgl_transaksi', name: 'tgl_transaksi'},
            {data: 'terima_dari', name: 'terima_dari'},
            {data: null,  render: function ( data, type, row ) {
                return maskRupiah("",data.total_nominal)
                }
            },
        ]
    });  
    
    $("#btnFilter").click(function(){
        dTable.draw();
    });

    function showDetails(id, kode) {

        $(".modal-title").html('')
        $(".modal-title").append('Detail '+kode)

        var detailTable = $('#detailTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 5,
            responsive: true,
            destroy: true,
            "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            dom: '',
            ajax: {
                url: "{{ url('cash/in/datatable_details') }}",
                data: function (d) {
                    d.id = id
                }
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                // Total over all pages
                total = api.column(3).data().reduce( function (a, b) {
                        return intVal(a) + intVal(b.nominal);
                    }, 0 );

                // Update footer
                $( api.column(3).footer() ).html(maskRupiah("", total));
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan'},
                {data: 'keterangan', name: 'keterangan'},
                {data: null, class: 'text-right',  
                    render: function ( data, type, row ) {
                        return maskRupiah("",data.nominal)
                    }
                },
            ]
        });
    }    
</script>
@endsection