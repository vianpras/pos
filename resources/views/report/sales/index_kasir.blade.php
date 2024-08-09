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
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="card-footer filterCard">
                  <div class="float-right ">
                     {{-- <button type="button" class="btn btn-sm bg-olive my-2"> <i class="fas fa-file-excel"></i>
                        &nbsp;Export</button>&nbsp; --}}
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
            <div class="card" id="resultReportGlobal">
               <div class="card-footer">
                  <span class="h5 text-secondary float-left">LAPORAN PENJUALAN GLOBAL</span>
                  <div class="float-right">
                     <span class="h5 text-secondary text-bold text-teal" id="tittleTotalSales"></span>
                     <span class="h5 text-secondary text-bold text-teal" id="totalSales"></span>
                  </div>
               </div>
               {{-- jenis report global --}}
               <div class="card-body">
                  <table class="table table-hover text-nowrap table-head-fixed" id="tResultReportItem">
                     <thead>
                        <tr>
                           <th>Kasir</th>
                           <th>Tanggal Transaksi</th>
                           <th>Nomor Nota</th>
                           <th>Jenis Pembayaran</th>
                           <th>Total</th>
                        </tr>
                     </thead>
                     <tbody id=tResultReportGlobal>
                     </tbody>
                     <tfoot>
                        <tr>
                           <th colspan="4" style="text-align:right">Total Penjualan</th>
                           <th style="text-align:right"></th>
                        </tr>
                     </tfoot>
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

   $(document).on("click", "#filterButton", function (event) {
      var table = $('#tResultReportItem').DataTable({
         info: false,
         ordering: false,
         paging: false,
         processing: true,
         serverSide: true,
         dom: 'B',
         buttons: [
            {
               extend: 'excel',
               className: 'btn btn-success btn-sm',
               text: '<i class="fas fa-file-excel"></i> Excel',
               footer: true
            }
         ],
         ajax: {
            url: "{{ url('kasirReport') }}",
            data: function (d) {
                  d.range_date = $('#reservation').val()
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
            total = api.column(4).data().reduce( function (a, b) {
                     return intVal(a) + intVal(b.total);
                  }, 0 );

            // Update footer
            $( api.column(4).footer() ).html(maskRupiah("", total));
         },
         columns: [
            {data: 'kasir', name: 'kasir'},
            {data: 'date_order', name: 'user_created'},
            {data: 'code', name: 'user_created'},
            {data: 'pMethod', name: 'user_created'},
            {data: null, class: 'text-right',
               render: function (data, type, row, meta) {
                  return maskRupiah("", data.total);
               }
            }
         ]
      });

      table.draw();
   });
</script>
@endsection