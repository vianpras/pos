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
                                @if(Helper::checkACL('jurnal_umum', 'e'))
                                <div id="actionFilter" class="mt-2">
                                    <button class="btn btn-primary btn-sm" id="btnPosting">Posting Jurnal</button>
                                </div>&nbsp;
                                @endif
                                {{-- Other Action --}}
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool  " data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Helper::checkACL('jurnal_umum', 'c'))
                                            <a href="{{ url('/jurnal/create') }}" id="newButton" class="dropdown-item"> <i class="fas fa-plus-square"></i>&nbsp; Data Baru</a>
                                        @endif
                                        @if(Helper::checkACL('jurnal_umum', 'i'))
                                            <a href="#" class="dropdown-item"> <i class="fas fa-file-excel"></i> &nbsp; Impor Data</a>
                                        @endif
                                        <a href="#" class="dropdown-item" data-card-widget="collapse"><i class="fas fa-search-plus"></i>&nbsp; Tampilkan</a>
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
                                    <label for="nomor_jurnal_filter">Nomor Jurnal</label>
                                    <input type="text" class="form-control form-control-sm" name="nomor_jurnal_filter" id="nomor_jurnal_filter" placeholder="Filter Nomor Jurnal">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="nomor_transaksi_filter">Nomor Transaksi</label>
                                    <input type="text" class="form-control form-control-sm" name="nomor_transaksi_filter" id="nomor_transaksi_filter" placeholder="Filter Nomor Transaksi">
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
                                        <input type="text" class="form-control form-control-sm float-right" name="transaksi_date_filter" id="reservation" placeholder="Range Tanggal" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="membership_status_filter">Status</label>
                                    <select class="form-control form-control-sm select2" style="width: 100%;" name="membership_status_filter">
                                        <option value='active'>Aktif</option>
                                        <option value='suspend'>Suspend</option>
                                        <option value='close'>Close</option>
                                        <option value='-1' selected="selected">Semua</option>
                                    </select>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right ">
                            <button type="button" class="btn btn-info btn-sm" id="btnFilter"><i class="fab fa-searchengin"></i> Cari</button>
                        </div>
                    </div>
                </div>
                </form>
    
                <!-- /.Filter Box -->
                <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr align="center">
                            <th>Tipe</th>
                            <th>Nama Akun</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="tBody"></tbody>
                </table>
            </div>
        </div>
        {{-- modal Posting Data --}}
        <div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body" id="modalBody">
                    {{-- getByAJAX --}}
                    </div>
                </div>
            </div>
        {{-- ./modal Posting Data --}}
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
            url: "{{ url('jurnal/buku_besar') }}"
        },
        columns: [
            {data: 'tipe', name: 'tipe'},
            {data: 'name', name: 'name'},
            {data: null,  render: function ( data, type, row ) {
                return maskRupiah("", data.debit)
                }
            },
            {data: null,  render: function ( data, type, row ) {
                return maskRupiah("", data.kredit)
                }
            },
            {data: 'saldo', name: 'saldo'},
        ]
    });

    $("#btnFilter").click(function(){
        dTable.draw();
    });

    // Modal Form Posting
    $(document).on('click', '#btnPosting', function (event) {
        event.preventDefault();
        let href = '{{ route("jurnal.form_posting") }}';
        $.ajax({
            url: href,
            beforeSend: function () {
                $('#loader').show();
            },
            // return the result
            success: function (result) {
                if (result.status === "error") {
                Swal.fire(
                    result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                    result.message,
                    result.status
                )
                } else {
                    $('#modalBlade').modal("show");
                    $('#modalBody').html(result).show();
                }
            },
            complete: function () {
                $('#loader').hide();
            },
            error: function (jqXHR, testStatus, error) {
                // console.log(error);
                alert("Page " + href + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        })
    });
    // ./Modal Form Posting

    // kirim Email
    $(document).on('click', '#postingButton', function (event) {
        event.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "/jurnal/posting",
            method: "POST",
            data: {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            },
            beforeSend: function () {
                $('#loader').show();
            },
            // return the result
            success: function (result) {
                // console.log(result)
                if (result.status == 'success') {
                    $('#modalBlade').modal("hide");
                    Swal.fire(
                        result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                        result.message,
                        result.status
                    )
                } else {
                    Swal.fire(
                        result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                        result.message,
                        result.status
                    )
                }
            },
            complete: function (result) {
                $('#loader').hide();
            },
            error: function (jqXHR, testStatus, error) {
                // console.log(error)
                Swal.fire(
                error.status + ' !',
                error.message,
                error.status
                )
                $('#loader').hide();
            },
            timeout: 8000
        })
    });
    // ./kirim Email
</script>
@endsection