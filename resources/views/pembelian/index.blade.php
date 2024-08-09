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
                                    <button type="button" class="btn btn-tool" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Helper::checkACL('pembelian', 'c'))
                                        <a href="{{ url('purchase/create') }}" id="newButton" class="dropdown-item"> <i class="fas fa-plus-square"></i>&nbsp; Data Baru</a>
                                        @endif
                                        @if(Helper::checkACL('pembelian', 'i'))
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
                                    <label for="kode_pembelian_filter">Kode Pembelian</label>
                                    <input type="text" class="form-control form-control-sm" name="kode_pembelian_filter" id="kode_pembelian_filter" placeholder="Filter Kode Pembelian">
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
                            <th>Kode Pembelian</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="tBody"></tbody>
                </table>
            </div>
        </div>
        {{-- Modal Detail Data --}}
        <div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <table id="dTable_detail" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Item</th>
                                    <th>Nama Item</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- ./Modal Detail Data --}}
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
            url: "{{ url('purchase/datatable') }}",
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
                d.kode_pembelian_filter = $('#kode_pembelian_filter').val(),
                d.date = $('#reservation').val()
            }
        },
        columns: [
            {data: 'action', name: 'action'},
            {data: 'kode_pembelian', name: 'kode_pembelian'},
            {data: 'tanggal', name: 'tanggal'},
            {data: null,  render: function ( data, type, row ) {
                return maskRupiah("",data.total)
                }
            },
        ]
    });    

    // edit Data Modal
    $(document).on('click', '#detailButton', function (event) {
        event.preventDefault();
        let href = $(this).attr('data-attr');
        $.ajax({
            url: href,
            beforeSend: function () {
                // $('#loader').show();
            },
            // return the result
            success: function (result) {
                $('#modalBody table tbody').html("");
                $.each(result, function(index, item) {
                    var item_code = "";
                    if(item.kode_item != null){
                        item_code = item.kode_item
                    } else {
                        item_code = "-"
                    }
                    $('#modalBody table tbody').append(
                        `<tr>
                            <td>`+item_code+`</td>
                            <td>`+item.nama+`</td>
                            <td>`+item.satuan+`</td>
                            <td class="text-right">`+maskRupiah("",item.harga)+`</td>
                            <td class="text-right">`+item.qty+`</td>
                            <td class="text-right">`+maskRupiah("",item.total)+`</td>
                        </tr>`
                    );
                })

                // $('#dTable_detail').DataTable({
                //     processing: true,
                //     serverSide: true,
                //     pageLength: 5,
                //     responsive: true
                // })
                // if (result.status === "error") {
                //     Swal.fire(
                //         result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                //         result.message,
                //         result.status
                //     )
                // } else {
                //     $('#modalBlade').modal("show");
                //     $('#modalBody').html(result).show();
                // }
            },
            complete: function () {
                // $('#loader').hide();
            },
            error: function (jqXHR, testStatus, error) {
                Swal.fire(
                    error.status + ' !',
                    error.message,
                    error.status
                )
                // $('#loader').hide();
            },
            timeout: 8000
        })
    });
    // ./edit Modal
</script>
@endsection