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
                        <div class="row">
                            <div class="col-12 col-sm-3 col-lg-4">
                                <h3 class="card-title text-lightblue mt-3" data-card-widget="collapse">
                                    <i class="fas fa-filter"></i>&nbsp;&nbsp;
                                    Alat & Pencarian
                                </h3>
                            </div>
                            <div class="col-12 col-sm-9 col-lg-8">
                                <div class="d-flex flex-row justify-content-end">
                                    {{-- Filter Table --}}
                                    <div id="actionLength" class="mt-2"></div>&nbsp;
                                    {{-- Action Table --}}
                                    @if(Helper::checkACL('master_user', 'e'))
                                    <div id="actionFilter" class="mt-2"></div>&nbsp;
                                    @endif
                                    {{-- Other Action --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-tool" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                                            @if(Helper::checkACL('master_user', 'c'))
                                                <a href="{{ url('dataInduk/profitsetting/create') }}" class="dropdown-item"> <i class="fas fa-plus-square"></i>&nbsp; Data Baru</a>
                                            @endif
                
                                            @if(Helper::checkACL('master_user', 'i'))
                                                <a href="#" class="dropdown-item"> <i class="fas fa-file-excel"></i>&nbsp; Impor Data</a>
                                            @endif
                
                                            <a href="#" class="dropdown-item" data-card-widget="collapse"><i class="fas fa-search-plus"></i>&nbsp;Tampilkan</a>
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
                                    <label for="kode_item_filter">Kode</label>
                                    <input type="text" class="form-control form-control-sm" name="kode_item_filter" id="kode_item_filter" placeholder="Filter Kode">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="nama_item_filter">Nama</label>
                                    <input type="text" class="form-control form-control-sm" name="nama_item_filter" id="nama_item_filter" placeholder="Filter Nama">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right ">
                            <button type="submit" class="btn btn-info btn-sm"><i class="fab fa-searchengin"></i> Cari</button>
                        </div>
                        </form>
                    </div>
                </div>
                <!-- /.Filter Box -->    
                
                <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr align="center">
                           <th width="8%"> Aksi </th>
                           <th>Kode</th>
                           <th>Profit Type</th>
                           <th width="15%">Nominal</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($data AS $dt)
                            <tr>
                                <td>
                                    <a href="{{ url('dataInduk/profitsetting/edit').'/'.$dt->id }}" type="button" id="editButton" class="btn btn-outline-info btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" id="editButton" class="btn btn-outline-danger btn-xs" data-id="{{ $dt->id }}" onclick="removeData($(this).data('id'))">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                <td>{{ (($dt->nama_item) ? $dt->kode_item.' - '.$dt->nama_item : 'Semua') }}</td>
                                <td>
                                    <span class="badge badge-secondary" style="text-transform: capitalize">{{ $dt->profit_type }}</span>
                                </td>
                                <td class="text-right">
                                    @if($dt->profit_type == 'nominal')
                                        {{ Helper::formatNumber($dt->jumlah,'rupiah') }}
                                    @else 
                                        {{ $dt->jumlah.' %' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                     </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@section('jScript')
<script>
    function removeData(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        Swal.fire({
            title: 'Yakin ingin menghapus data?',
            //   text: "User terkait akan keluar dari program !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#38BC8C',
            cancelButtonColor: '#E74C3C',
            confirmButtonText: 'Ya, Setuju .. !',
            cancelButtonText: 'Batal'
        }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ url('dataInduk/profitsetting/delete') }}",
                        data: {"id": id},
                        beforeSend: function () {
                            $('#loader').show();
                        },
                        complete: function (result) {
                            $('#loader').hide();
                        },
                        success: function (data) {
                            Swal.fire(
                                data.status == 'success' ? 'Berhasil !' : 'Gagal !',
                                data.message,
                                data.status
                            )
                        },
                        error: function (err) {
                            Swal.fire(
                                err.status + ' !',
                                err.message,
                                err.status
                            )
                        }
                    });
                }
            });
    }
</script>
@endsection