@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-12  m-1">
                <div class="card">
                    <div class="card-header" data-card-widget="collapse">
                        <h3 class="card-title" data-card-widget="collapse">
                            Cari {{ $title ?? '' }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body filterCard">
                        <form action="{{ url('salesReport/pendapatan') }}" method="POST" id="ReportForm" class="form columnForm" role="form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <label>Cakupan Tanggal</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm float-right" name="range_date" id="reservation" placeholder="Range Tanggal" autocomplete="off" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="float-right ">
                                        {{-- <button type="button" class="btn btn-sm bg-olive my-2"> <i class="fas fa-file-excel"></i>
                                            &nbsp;Export</button>&nbsp; --}}
                                        <input type="submit" class="btn btn-info btn-sm" name="SubmitButton" value="Filter">
                                        {{-- <button type="button" class="btn btn-info btn-sm" id="filterButton"><i class="fab fa-searchengin"></i>
                                                &nbsp;Filter</button> --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer filterCard">
                        
                    </div>
                </div>
            </div>
            <div class="col-12 m-1">
                <div class="card" id="chartReport" style="display:none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-center">
                                    <strong class="text-secondary" id="dateSalesChart"></strong>
                                </p>
                                <div class="chart" id="chartDiv">
                                    <canvas id="salesChart" height="300" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" id=footerReport>
                        <div class="row">
                            <div class="col-sm-4 col-6">
                                <div class="description-block border-right">
                                    {{-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                    17%</span> --}}
                                    <h4 class="description-header text-teal" id="totalSalesChart">Rp. 0</h4>
                                    <span class="description-text">PENJUALAN</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 col-6">
                                <div class="description-block border-right">
                                    {{-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                    0%</span> --}}
                                    <h4 class="description-header text-orange" id="transactionSalesChart">0 Nota</h4>
                                    <span class="description-text">TRANSAKSI</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 col-6">
                                <div class="description-block">
                                    {{-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                    20%</span> --}}
                                    <h4 class="description-header text-info" id="itemSalesChart">0 Item</h4>
                                    <span class="description-text">TERJUAL</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="card" id="resultReportGlobal">
                    <div class="card-footer">
                        <span class="h5 text-secondary float-left">LAPORAN PENDAPATAN PENJUALAN</span>
                        <div class="float-right">
                            <span class="h5 text-secondary text-bold text-teal" id="tittleTotalSales"></span>
                            <span class="h5 text-secondary text-bold text-teal" id="totalSales"></span>
                        </div>
                    </div>
                    {{-- jenis report global --}}
                    <div class="card-body">
                        @php
                            $total_pembelian = 0;
                            $total_penjualan = 0;
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                @if(isset($_POST['SubmitButton']))
                                <table class="table table-hover text-nowrap table-head-fixed" id="tResultReportItem">
                                    <thead>
                                        <tr>
                                            <th>Pembelian</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($pembelian) > 0)
                                            @foreach($pembelian AS $pb)
                                                <tr>
                                                    <td>{{ $pb->kode_pembelian }}</td>
                                                    <td style="text-align:right">{{Helper::formatNumber($pb->total,'rupiah')}}</td>
                                                </tr>
                                                @php
                                                    $total_pembelian += $pb->total;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-center">Data tidak ditemukan</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align:right">Total Pembelian</th>
                                            <th style="text-align:right">{{Helper::formatNumber($total_pembelian,'rupiah')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if(isset($_POST['SubmitButton']))
                                <table class="table table-hover text-nowrap table-head-fixed" id="tResultReportItem">
                                    <thead>
                                        <tr>
                                            <th>Penjualan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($sales) > 0)
                                            @foreach($sales AS $sl)
                                                <tr>
                                                    <td>{{ $sl->code }}</td>
                                                    <td style="text-align:right">{{Helper::formatNumber($sl->total,'rupiah')}}</td>
                                                </tr>
                                                @php
                                                    $total_penjualan += $sl->total;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-center">Data tidak ditemukan</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align:right">Total Penjualan</th>
                                            <th style="text-align:right">{{Helper::formatNumber($total_penjualan,'rupiah')}}</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align:right">Total Pendapatan</th>
                                            <th style="text-align:right">{{Helper::formatNumber(($total_penjualan - $total_pembelian),'rupiah')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection