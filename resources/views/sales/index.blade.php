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
                  <h3 class="card-title text-lightblue col-sm-2 "data-card-widget="collapse">
                     <i class="fas fa-filter"></i>&nbsp;&nbsp;
                     Filter
                  </h3>
                  <div class="card-tools">
                     <div class="d-flex flex-row ">
                        {{-- Filter Table --}}
                        <div id="actionLength" class="mt-2">
                        </div>&nbsp;
                        {{-- Action Table --}}
                        @if(Helper::checkACL('master_user', 'e'))
                           <div id="actionFilter" class="mt-2"></div>&nbsp;
                        @endif
                     </div>
                  </div>
               </div>
               <div class="card-body" style="display: none;">
                  <form method="POST" id="filterForm" class="form columnForm" role="form">
                     <div class="row">
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="sales_code_filter">Kode Pemesanan</label>
                              <input type="text" class="form-control form-control-sm" name="sales_code_filter"
                                 id="sales_code_filter" placeholder="Filter Kode Permintaan Pembelian">
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label>Jangkauan Tanggal Pemesanan</label>
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text">
                                       <i class="far fa-calendar-alt"></i>
                                    </span>
                                 </div>
                                 <input type="text" class="form-control form-control-sm float-right"
                                    name="sales_date_filter" id="reservation" placeholder="Range Tanggal"
                                    autocomplete="off" required>
                              </div>
                           </div>

                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="sales_status_filter">Status Pemesanan</label>
                              <select class="form-control form-control-sm select2" style="width: 100%;" name="sales_status_filter">
                                 <option value='pending' class="text-capitalize">Pending</option>
                                 <option value='confirm' class="text-capitalize">Confirm</option>
                                 <option value='cancel' class="text-capitalize">Cancel</option>
                                 <option value='close' class="text-capitalize">Close</option>
                                 <option value='-1' selected="selected">All</option>
                              </select>
                           </div>
                        </div>
                     </div>
               </div>
               <div class="card-footer">
                  <div class="float-right ">
                     {{-- <button type="button" class="btn btn-sm bg-olive my-2"> <i class="fas fa-file-excel"></i>
                        import</button>&nbsp; --}}
                     <button type="submit" class="btn btn-info btn-sm"><i class="fab fa-searchengin"></i> Cari</button>
                  </div>
               </div>
            </div>
            </form>

            <!-- /.Filter Box -->
            <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
               <thead>
                  <tr align="center">
                     <th>Aksi</th>
                     <th>Kode</th>
                     <th>Tgl. Pemesanan</th>
                     <th>Nama Pemesan</th>
                     <th>Status</th>
                  </tr>
               </thead>
               <tbody class="tBody"></tbody>
            </table>
         </div>
      </div>
      {{-- modal Edit Data --}}
      <div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
               <div class="modal-body" id="modalBody">
                  {{-- getByAJAX --}}
               </div>
            </div>
         </div>
      </div>
      {{-- ./modal Edit Data --}}
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
         url: "{{ url('sales/datatable/main') }}",
         type: 'POST',
         headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
         data: function (d) {
               d.kode_pembelian_filter = $('#sales_code_filter').val(),
               d.date = $('#reservation').val()
         }
      },
      columns: [
         {data: 'action', name: 'action'},
         {data: 'code', name: 'code'},
         {data: 'date_order', name: 'date_order'},
         {data: 'customer', name: 'customer'},
         {data: 'status', name: 'status'}
      ]
   });

   const refundProcess = (code) => {
      swal.fire({
         title: "Refund Nota " +code ,
         icon: "question",
         width: 500,
         showDenyButton: true,
         showCancelButton: true,
         confirmButtonColor: "#20C997",
         denyButtonColor: "#007BFF",
         cancelButtonColor: "#DC3545",
         confirmButtonText: "Refund",
         denyButtonText: "Refund & Cetak ulang",
         cancelButtonText: "Batal",
         html: `Apakah Anda ingin membuat ulang nota ini ?`,
      }).then((resultSwal1) => {
         // Jika isConfirm(Bayar)
         if (resultSwal1.isConfirmed) {
            $.ajax({
               type: "GET",
               url: "{{ url('refund/sales') }}/"+code+"/refund",
               cache: false,
               success: function(data){
                  popToast('success', 'Berhasil Refund');
                  
                  location.reload();
               }
            });
         }

         // Jika isDenied(Simpan)
         if (resultSwal1.isDenied) {
            $.ajax({
               type: "GET",
               url: "{{ url('refund/sales') }}/"+code+"/cetak_ulang",
               cache: false,
               success: function(data){
                  window.location = "{{ url('create/refund/sales') }}/"+code;
               }
            });
         }
      });      
   }
</script>
@endsection