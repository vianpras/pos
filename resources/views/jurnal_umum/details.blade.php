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
            {{-- <div class="card-header">
                <h3 class="card-title">Daftar {{ $title ?? '' }} </h3>
            </div> --}}
            <div class="card-body">
                <!-- /.Filter Box -->
                <p>No. Transaksi : {{ $master->no_transaksi }}</p>
                <p>Tanggal : {{ date('d/m/Y', strtotime($master->tgl_transaksi)) }}</p>
                <table id="dTable" class="table table-borderles table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr class="bg-teal">
                            <th style="width: 350px">Akun</th>
                            <th style="width: 390px">Keterangan</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="tBody">
                        @php
                            $total_debet = 0;
                            $total_kredit = 0;
                        @endphp
                        @foreach($details AS $dtl)
                        <tr>
                            <td>{{ $dtl->code_account_default.' - '.$dtl->name }}</td>
                            <td>{{ $dtl->keterangan }}</td>
                            <td>{{ "Rp " . number_format($dtl->debit,2,',','.') }}</td>
                            <td>{{ "Rp " . number_format($dtl->kredit,2,',','.') }}</td>
                        </tr>
                        @php
                            $total_debet += $dtl->debit;
                            $total_kredit += $dtl->kredit;
                        @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-teal">
                            <th colspan="2">TOTAL</th>
                            <th>{{ "Rp " . number_format($total_debet,2,',','.') }}</th>
                            <th>{{ "Rp " . number_format($total_kredit,2,',','.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@section('jScript')
@endsection