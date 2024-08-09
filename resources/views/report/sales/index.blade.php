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
                  <form method="POST" id="ReportForm" class="form columnForm" role="form">
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
                                 <div class="col-sm-5">
                                    <label>Grafik</label>
                                    <div class="row">
                                       <input type="checkbox" name="showChart" class="switchBs" id="showChart" value="1" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Ya" data-off-text="Tidak" checked>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label for="typeReport">Jenis</label>
                              <div class="row">
                                 <div class="col-sm-3">
                                    <input type="checkbox" name="typeReport" class="switchBs" id="typeReport" value="0" data-bootstrap-switch data-off-color="success" data-on-text="Item" data-off-text="Nota" data-on-color="info">
                                 </div>
                                 <div class="col-sm-9" id="byItem" style="display: none">
                                    <select class="form-control form-control-sm select2" style="width: 100%;" name="item_id[]" multiple="multiple" data-placeholder="Select a State">
                                       <option value='all' selected="selected">Semua</option>
                                       @foreach ($items as $option)
                                       <option value="{{ $option->id }}">{{$option->code.' - '.$option->name }}</option>
                                       @endforeach
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="card-footer filterCard">
                  <div class="float-right ">
                     <button type="button" class="btn btn-sm bg-olive my-2"> <i class="fas fa-file-excel"></i>
                        &nbsp;Export</button>&nbsp;
                     <button type="button" class="btn btn-info btn-sm" id="filterButton"><i class="fab fa-searchengin"></i>
                        &nbsp;Filter</button>
                  </div>
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
            <div class="card" id="resultReportGlobal" style="display: none">
               <div class="card-footer">
                  <span class="h5 text-secondary float-left">LAPORAN PENJUALAN GLOBAL</span>
                  <div class="float-right">
                     <span class="h5 text-secondary text-bold text-teal" id="tittleTotalSales"></span>
                     <span class="h5 text-secondary text-bold text-teal" id="totalSales"></span>
                  </div>
               </div>
               <div class="card-body" id="errorMessage">
               </div>
               {{-- jenis report global --}}
               <div class="card-body">
                  <table class="table table-hover text-nowrap table-head-fixed">
                     <thead>
                        <tr>
                           <th>NOTA</th>
                           <th>TOTAL</th>
                           <th>PAJAK</th>
                           <th>DISKON</th>
                           <th>GRAND TOTAL</th>
                        </tr>
                     </thead>
                     <tbody id=tResultReportGlobal>
                     </tbody>
                  </table>
               </div>
            </div>
            {{-- jenis report by item --}}
            <div class="card" id="resultReportItem" style="display: none">
               <div class="card-footer">
                  <span class="h5 text-secondary float-left">LAPORAN PENJUALAN ITEM</span>
                  <div class="float-right">
                     <span class="h5 text-secondary text-bold text-teal" id="tittleTotalSalesItem"></span>
                     <span class="h5 text-secondary text-bold text-teal" id="totalSalesItem"></span>
                  </div>
               </div>
               <div class="card-body">
                  <table class="table table-hover text-nowrap table-head-fixed">
                     <thead>
                        <tr>
                           <th>KODE ITEM</th>
                           <th>NAMA ITEM</th>
                           <th>KATEGORI</th>
                           <th>KUANTITAS</th>
                        </tr>
                     </thead>
                     <tbody id=tResultReportItem>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
</div>
</div>

</section>
</div>
@endsection

@section('jScript')
<script>
   const postReport = () => {
   $(document).on("click", "#filterButton", function (event) {
      // console.log('getDataMember')
      event.preventDefault();
      $.ajaxSetup({
         headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
      });
      let href = "/salesReport";
      let showChart = $("#showChart").val();
      let typeReport = $("#typeReport").val();
      let chartReport = $("#chartReport");
      let resultReportGlobal = $("#resultReportGlobal");
      let tResultReportGlobal = $("#tResultReportGlobal");
      let resultReportItem = $("#resultReportItem");
      let tResultReportItem = $("#tResultReportItem");

      chartReport.hide();
      resultReportGlobal.hide();
      tResultReportGlobal.empty();
      
      resultReportItem.hide();
      tResultReportItem.empty();
      $.ajax({
         url: href,
         method: "POST",
         data: $("#ReportForm").serialize(),
         beforeSend: function () {
            $("#loader").removeClass("hidden");
         },
         success: function (result) {
            if (result.status == "success") {
               let reports = result.report;
               let chart = result.chart;

               //tampilkan report default global
               if (typeReport == 0) {
                  //tampilkan chart
                  if (showChart == 1) {
                     // hapus canvas
                     canvasDestroy('#chartDiv','salesChart');
                     let _datasets = chart.datasets;
                     let datasets = [];
                     _datasets.forEach(function callback(dataset, index) {
                        datasets.push(JSON.parse(dataset));
                     });
                     chartReport.show();
                     $('#footerReport').show()

                     $("#dateSalesChart").text(chart.dateSalesChart);
                     maskRupiah("#totalSalesChart", chart.totalSalesChart);
                     $("#transactionSalesChart").text(
                        formatNumber(chart.transactionSalesChart) + " Nota"
                     );
                     $("#itemSalesChart").text(
                        formatNumber(chart.itemSalesChart) + " Item"
                     );
                     chartLine("#salesChart", chart.label, datasets, true);
                  }
                  // tampilkan card
                  resultReportGlobal.show();
                  // looping data
                  reports.forEach((report) => {
                     let amountTax = ((report.sub_total - report.discount) * (report.tax/100))
                     let htmlResultReport = '';
                     htmlResultReport = "<tr>";
                     htmlResultReport += "<td>" + report.code + "</td>";
                     htmlResultReport +=
                        "<td>" + maskRupiah("", report.sub_total) + "</td>";
                     htmlResultReport += "<td>" + maskRupiah("", amountTax) + "</td>";
                     htmlResultReport +=
                        "<td>" + maskRupiah("", report.discount) + "</td>";
                     htmlResultReport +=
                        "<td>" + maskRupiah("", report.total) + "</td>";
                     htmlResultReport += "</tr>";
                     tResultReportGlobal.append(htmlResultReport);
                     $("#tittleTotalSales").text(
                        // "TOTAL PENJUALAN [ " + report.count_sales + " Nota ] : "
                        "TOTAL PENJUALAN : "
                     );
                     maskRupiah("#totalSales", report.total_sales);
                  });
               }

               // tampilkan report by item
               if (typeReport == 1) {
                  // tampilkan chart
                  if (showChart == 1) {
                     // hapus canvas
                     canvasDestroy('#chartDiv','salesChart');

                     let _datasets = chart.datasets;
                     let datasets = [];
                     _datasets.forEach(function callback(dataset, index) {
                        datasets.push(JSON.parse(dataset));
                     });
                     chartReport.show();
                     $("#dateSalesChart").text(chart.dateSalesChart);
                     $('#footerReport').hide()
                     chartLine("#salesChart", chart.label, datasets, false);
                  }

                  // tampilkan card
                  resultReportItem.show();

                  // looping data
                  reports.forEach((report) => {
                     let amountTax = ((report.sub_total - report.discount) * (report.tax/100))

                     let htmlResultReport = '';
                     htmlResultReport = "<tr>";
                     htmlResultReport += "<td class='text-uppercase'>" + report.code_item + "</td>";
                     htmlResultReport += "<td class='text-uppercase'>" + report.name_item + "</td>";
                     htmlResultReport += "<td class='text-uppercase'>" + report.name_category + "</td>";
                     htmlResultReport += "<td class='text-uppercase'>" + report.total_quantity + "</td>";
                     htmlResultReport += "</tr>";
                     tResultReportItem.append(htmlResultReport);
                     $("#tittleTotalSalesItem").text(
                        // "TOTAL PENJUALAN ITEM [ " + report.count_sales + " Nota ] : "
                        "TOTAL PENJUALAN ITEM: "
                     );
                     // console.log(chart.itemSalesChart)
                     $("#totalSalesItem").text(formatNumber(chart.itemSalesChart));
                  });
               }
            }
            
            if (result.status == "error") {
               // tampilan jika error respons json
               let html = '<p class="h3 text-secondary text-center">'+result.message+'</p>';
               $("#tittleTotalSales").hide()
               $("#totalSales").hide();
               $('#errorMessage').empty();
               $('#resultReportGlobal table').hide();
               resultReportGlobal.show();
               $('#errorMessage').append(html);
            }

         },
         error: function (jqXHR, testStatus, error) {
            // tampilkan jika error respon server
            let html = '<p class="h3 text-secondary text-center">Data Tidak Ditemukan / Terjadi Kesalahan Server</p></div>';
            $('#errorMessage').empty()
            $('#resultReportGlobal table').hide()
            resultReportGlobal.show();
            $('#errorMessage').append(html);
         },
         complete: function () {
            $("#loader").addClass("hidden");
         },
         timeout: 8000,
      });
   });
   };

   $(document).ready(function () {
      postReport()
      // switchBS
      $("input[data-bootstrap-switch]").each(function(){
         $('input#showChart.switchBs').bootstrapSwitch({
            onSwitchChange: function(e, state) { 
               _state = state?1:0
               $(this).val(_state)
            }
         });
         $('input#typeReport.switchBs').bootstrapSwitch({
            onSwitchChange: function(e, state) { 
               if(state==0 ){
                  _state = 0;
                  $('#typeReport').text('Jenis Laporan');
                  $('#byItem').hide();
               }else{
                  _state = 1;
                  $('#typeReport').text('Jenis Laporan');
                  $('#byItem').show();
               }
               $('#typeReport').val(_state)
               $(this).val(_state)
            }
         });
      })
      // .switchBS
      $('body').on('keydown', 'input, select', function(e) {
         if (e.key === "Enter") {
            var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
            focusable = form.find('input,a,select,button,textarea').filter(':visible');
            next = focusable.eq(focusable.index(this)+1);
            if (next.length) {
               next.focus();
            } else {
               form.submit();
            }
            return false;
         }
      });
   });
</script>

@endsection