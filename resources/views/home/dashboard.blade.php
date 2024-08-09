@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-wrapper">
        {{-- <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div> --}}
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                {{-- default View --}}
                <div id="sectionDefault">
                    <div class="d-flex align-items-center justify-content-center flex-column" style="height: 100vh;">
                        <div class="login-logo">
                            <a href="/"><img src="/dist/img/logo.png" height="50px" /></a>
                        </div>
                        <span class="p-2 h5 text-dark ">POS Web Base System</span>
                    </div>
                </div>
                {{-- end Default View --}}
                <!-- Section 1-->
                <div class="row pt-2" id="sectionOne" style="display: none">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Penjualan Harian</span>
                                <span class="info-box-number" id="dailySales"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-boxes"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Item Harian</span>
                                <span class="info-box-number" id="countDailyItemSales"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <!-- fix for small devices only -->
                    <div class="clearfix hidden-md-up"></div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i
                                    class="fas fa-file-invoice-dollar"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Nota Harian</span>
                                <span class="info-box-number" id="countDailySales"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    {{-- member --}}
                    <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Member Harian</span>
                            <span class="info-box-number" id="dailyMember"></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                    <!-- /.col -->
                </div>
                <!-- /.Section 1 -->
                {{-- Section 2 --}}
                <div class="row" id="sectionTwo" style="display: none">
                    {{-- left col --}}
                    <section class="col connectedSortable">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Data Grafik </h5>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    {{-- line CHart --}}
                                    <div class="col-md-8 border-right pr-4">
                                        <p class="text-center">
                                            <strong class="text-maroon">#Chart</strong>
                                            <small>{{ Helper::setDate(date('m', strtotime("-1 months")), 'bulan') }}</small>
                                            <small>VS</small>
                                            <small>{{ Helper::setDate(date('m'), 'bulan') }}</small>
                                        </p>
                                        <div class="chart" id="chartLineDiv">
                                            <!-- Sales Chart Canvas -->
                                            <canvas id="chartLine" height="200" style="height: 200px;"></canvas>
                                        </div>
                                        <!-- /.chart-responsive -->
                                    </div>
                                    {{-- end line Chart --}}

                                    {{-- pie chart --}}
                                    <div class="col-md-4">
                                        <p class="text-center">
                                            <strong class="text-danger">#Top10ITEM</strong>
                                            <small>in {{ Helper::setDate(date('m'), 'bulan') }}</small>
                                        </p>
                                        <div class="chart">
                                            <!-- Sales Chart Canvas -->
                                            <canvas id="chartPie" height="200" style="height: 200px;"></canvas>
                                        </div>
                                    </div>
                                    <!-- /end pie chart -->
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <div class="row py-2">
                                    <div class="col-sm-3 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header text-success" id="monthlySales"></h5>
                                            <span class="description-text">TOTAL PENJUALAN </span>
                                            <p class="text-secondary">{{ Helper::setDate(date('m'), 'bulan') }}</p>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-3 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header text-orange" id="monthlyPurchases"></h5>
                                            <span class="description-text">TOTAL PEMBELIAN</span>
                                            <p class="text-secondary">{{ Helper::setDate(date('m'), 'bulan') }}</p>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-3 col-6">
                                        <div class="description-block ">

                                            <h5 class="description-header text-info" id="countMonthlyItemSales"></h5>
                                            <span class="description-text">Total Item Terjual</span>
                                            <p class="text-secondary">{{ Helper::setDate(date('m'), 'bulan') }}</p>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    {{-- member --}}
                                    <div class="col-sm-3 col-6">
                                    <div class="description-block">
                                        <h5 class="description-header text-olive" id="monthlyMember"></h5>
                                        <span class="description-text">Penambahan Member</span>
                                        <p class="text-secondary">{{date("F", strtotime("this month"))}}</p>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </section>
                    {{-- Section 2 --}}

                </div><!-- /.container-fluid -->
        </section>
    </div>
@endsection
<!-- /.content -->
@section('jScript')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            let href = "/dashboardAjax";

            $.ajax({
                url: href,
                beforeSend: function() {
                    $("#loader").removeClass("hidden");
                },
                success: function(result) {
                    if (result.status == "success") {
                        $('#sectionDefault').hide();
                        $('#sectionOne').show();
                        $('#sectionTwo').show();

                        maskRupiah('#dailySales', result.sectionOne.dailySales)
                        $('#countDailyItemSales').text(formatNumber(result.sectionOne
                            .countDailyItemSales));
                        $('#countDailySales').text(formatNumber(result.sectionOne.countDailySales));
                        $('#dailyMember').text(formatNumber(result.sectionOne.dailyMember));


                        maskRupiah('#monthlySales', result.sectionTwo.monthlySales);
                        maskRupiah('#monthlyPurchases', result.sectionTwo.monthlyPurchases);
                        $('#countMonthlyItemSales').text(formatNumber(result.sectionTwo
                            .countMonthlyItemSales));
                        $('#monthlyMember').text(formatNumber(result.sectionTwo.monthlyMember));

                        // generate chartLine
                        canvasDestroy('#chartLineDiv', 'chartLine');
                        let _datasets = result.sectionTwo.chartLine.datasets;
                        let datasets = [];
                        _datasets.forEach(function callback(dataset, index) {
                            datasets.push(JSON.parse(dataset));
                        });
                        chartLine("#chartLine", result.sectionTwo.chartLine.label, datasets, true);

                        // generate Donut
                        let _datasetsDonuts = result.sectionTwo.chartDonut.datasets;

                        let datasetsDonuts = [];
                        _datasetsDonuts.forEach(function callback(datasetDonut, index) {
                            datasetsDonuts.push(JSON.parse(datasetDonut));
                        });

                        let xlabel = [
                            'Chrome',
                            'IE',
                            'FireFox',
                            'Safari',
                            'Opera',
                            'Navigator',
                            'FireFoxx',
                            'Safarix',
                            'Operax',
                            'Navigatorx',
                        ];
                        let xdatasets = [{
                            data: [700, 500, 400, 600, 300, 100, 400, 600, 3100, 100],
                            backgroundColor: [
                                '#b56054',
                                '#c0a65a',
                                '#afec12',
                                '#6fcdef',
                                '#f56954',
                                '#00a65a',
                                '#f39c12',
                                '#00c0ef',
                                '#3c8dbc',
                                '#d2d6de'
                            ],
                            borderColor: [
                                '#b56054c0',
                                '#c0a65ac0',
                                '#afec12c0',
                                '#6fcdefc0',
                                '#f56954c0',
                                '#00a65ac0',
                                '#f39c12c0',
                                '#00c0efc0',
                                '#3c8dbcc0',
                                '#d2d6dec0'
                            ],
                        }]
                        chartDonut('#chartPie', result.sectionTwo.chartDonut.label, datasetsDonuts,
                            'number');

                    } else {
                        // abaikan error tampilan ajax/chart
                        // Swal.fire(
                        //     'Error !',
                        //     'Terjadi Kesalahan Server, Gagal Menampilkan Chart Coba Refresh Halaman. <br>'+result.message,
                        //     'error'
                        // )
                    }
                },

                error: function(jqXHR, testStatus, error) {
                    Swal.fire(
                        'Error !',
                        'Terjadi Kesalahan Server, Hubungi Penyedia Sistem',
                        'error'
                    )
                },
                complete: function() {
                    $("#loader").addClass("hidden");
                },
            });
        });
    </script>
@endsection
