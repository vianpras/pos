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
                  <h3 class="card-title text-lightblue mt-3 col-sm-2 "data-card-widget="collapse">
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
                        {{-- Other Action --}}
                        <div class="btn-group">
                           <button type="button" class="btn btn-tool  " data-toggle="dropdown">
                              <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>

                           </button>
                           <div class="dropdown-menu dropdown-menu-right" role="menu">
                              @if(Helper::checkACL('master_user', 'c'))
                              <a href="{{ route('purchase.new') }}" class="dropdown-item"> <i
                                    class="fas fa-plus-square"></i>&nbsp;Data Baru</a>
                              @endif
                              @if(Helper::checkACL('master_user', 'i'))
                              <a href="#" class="dropdown-item"> <i class="fas fa-file-excel"></i> &nbsp; Impor Data</a>
                              @endif
                              <a href="#" class="dropdown-item" data-card-widget="collapse"><i
                                    class="fas fa-search-plus"></i> &nbsp; Tampilkan</a>
                           </div>
                        </div>
                        {{-- Collaps Action --}}
                        {{-- <button type=" button" class="btn  text-lightblue " data-card-widget="collapse"
                           title="Collapse">
                           <i class="fas fa-plus"></i>
                        </button> --}}
                     </div>
                  </div>
               </div>
               <div class="card-body" style="display: none;">
                  <form method="POST" id="filterForm" class="form columnForm" role="form">
                     <div class="row">
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="purchases_code_filter">Kode Pemesanan</label>
                              <input type="text" class="form-control form-control-sm" name="purchases_code_filter"
                                 id="purchases_code_filter" placeholder="Filter Kode Permintaan Pembelian">
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
                                    name="purchases_date_filter" id="reservation" placeholder="Range Tanggal"
                                    autocomplete="off" required>
                              </div>
                           </div>

                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="purchases_status_filter">Status Pemesanan</label>
                              <select class="form-control form-control-sm select2" style="width: 100%;"
                                 name="purchases_status_filter">
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
                     <th> Aksi </th>
                     <th>Kode</th>
                     <th>Tgl. Pemesanan</th>
                     <th>Nama Pemesan</th>
                     <th>Membership</th>
                     <th>Status</th>
               </thead>
               </tr>
               <tfoot>
                  <tr>
                     <th class='notexport'></th>
                     <th>Kode</th>
                     <th>Tgl. Pemesanan</th>
                     <th>Nama Pemesan</th>
                     <th>Membership</th>
                     <th>Status</th>
                  </tr>
               </tfoot>
               <tbody class="tBody">
               </tbody>
            </table>
         </div>
      </div>
      {{-- modal Edit Data --}}
      <div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
               <div class="modal-body" id="modalBody">
                  {{-- getByAJAX --}}
               </div>
            </div>
         </div>
         {{-- ./modal Edit Data --}}
   </section>
</div>
@endsection
@section('jScript')
<script>
   //  dTable Scripting
$(document).ready(function() {
   var resetButton = $('#resetButton').on('click', function(e){
      $('#filterForm')[0].reset();
      $('#dTable tfoot input').val('').change();
      $("#dTable").DataTable().columns().visible(true);
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust(); 
      $("#dTable").DataTable().columns().search("").draw()
      $("#dTable").DataTable().draw();
    });
   var dTable = $('#dTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 5,
        responsive: true,
        "order": [[ 1, "asc" ]],
        "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        stateSave: true,
        dom: 'lBfrtip',
        buttons: [
            
            {
               extend: 'colvis',
               text: '<i class="fas fa-eye"></i> Kolom',
               className: 'btn btn-info btn-sm',
               postfixButtons: [ {
                     extend:'colvisRestore',
                     text: 'Reset V.bility',
                  } ]
            },
            {
               extend: 'print',
               text: '<i class="fas fa-print"></i> Cetak',
               className: 'btn bg-navy btn-sm',

               exportOptions: {
                  columns: [ 1,2,3,4 ]
               },
            }, 
            {
               extend: 'copy',
               copy: 'Salin',
               className: 'btn btn-warning btn-sm',
               text: '<i class="fas fa-copy"></i>  Salin',
               exportOptions: {
                  columns: [ 1,2,3,4 ]
               },
            },  
            {
               extend: 'excel',
               className: 'btn btn-success btn-sm',
               text: '<i class="fas fa-file-excel"></i> Excel',
               exportOptions: {
                  columns: [ 1,2,3,4 ]
               },

            }, 
            {
               text: '<i class="fas fa-sync-alt"></i> Reset',
               className: 'btn btn-danger btn-sm',
               action: function ( e, dt, node, config ) {
                  $('#filterForm')[0].reset();
                  $('#dTable tfoot input').val('').change();
                  $("#dTable").DataTable().columns().visible(true);
                  $($.fn.dataTable.tables(true)).DataTable().columns.adjust(); 
                  $("#dTable").DataTable().columns().search("").draw()
                  $("#dTable").DataTable().draw();
               }, 
            },
            // {
            //    extend: 'csv',
            //    className: 'btn bg-lime btn-sm',
            //    text: 'CSV',
            //    exportOptions: {
            //       columns: [ 1,2,3,4 ]
            //    },
            // },
        ],

        language: {
            searchPlaceholder: "Pencarian Global ",
            "sLengthMenu": "_MENU_",
         },
        ajax: {
            url: '{{ route("purchase.datatable") }}',
            'type': 'POST',
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
               d.purchases_code_filter = $('input[name=purchases_code_filter]').val();
               d.purchases_code_project_filter = $('input[name=purchases_code_project_filter]').val();
               d.purchases_date_filter = $('input[name=purchases_date_filter]').val();
               d.purchases_status_filter = $('select[name=purchases_status_filter] option').filter(':selected').val()
            }
        } ,
        columns: [
            {data: 'action', name: 'action','orderable':false,'searchable':false},
            {data: 'code', name: 'purchases.code'},
            {data: 'date_order', name: 'purchases.date_order'},
            {data: 'supplier', name: 'purchases.supplier'},
            {data: 'supplier_name', name: 'suppliers.nama'},
            {data: 'status', name: 'purchases.status','orderable':false,'searchable':true}
        ],
   });
   
      //  sumbit filter and redraw
      $('#filterForm').on('submit', function (e) {
         dTable.draw();
         e.preventDefault();
      });


      //  error handling
      $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
         Swal.fire(
            'Error !',
            'Terjadi Kesalahan Server, Coba Refresh Halaman. <br>'+message,
            'error'
         )
      };
      //  handling styling button ke div Tools
      $("div#dTable_wrapper .dt-buttons").appendTo('#actionFilter');
      $("div#dTable_wrapper .dt-buttons").hide();
      $("div#dTable_wrapper .dataTables_length").appendTo('#actionLength');
      $("div#dTable_wrapper .dataTables_length").hide();
      $('div.dataTables_length select').addClass('form-control-sm');

      //  create column filter
      $('#dTable tfoot th').each(function () {
         var title = $('#dTable tfoot th').eq($(this).index()).text();
         var disabled = (title === '' || title === null) && 'disabled';
         var checkSearch = dTable.state.loaded();
         var value = '';
         if (checkSearch) {
            var value = dTable.state.loaded().columns[$(this).index()].search.search
         } else {
            setTimeout(() => {
               location.reload();
            }, 5000)
         }

         if (!disabled) {
            $(this).html('<input type="text" class="form-control form-control-sm filter" value="' + value + '" placeholder="Filter ' + title + '"' + disabled + ' />');
         }
      });

      //  aktivated column filter
      dTable.columns().every(function () {
         var that = this;
         $('input', this.footer()).on('keyup change', function () {
            that
               .search(this.value)
               .draw();

         });
      });
});
</script>

<script>
   // handling disable user
$(document).ready( function () {
   $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $('body').on('click', '#_bDelete', function () {
      var _id = $(this).data("id");

      Swal.fire({
        title: 'Yakin ingin mengubah data?',
        text: "User terkait akan keluar dari program !",
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
            url: "{{ route('disableUser') }}",
            data: { "id": _id},
            success: function (data) {
               var oTable = $('#dTable').dataTable(); 
               oTable.fnDraw(false);
               Swal.fire(
                  data.status=='success'? 'Berhasil !':'Gagal !',
                  data.message,
                  data.status
               )
            },
            error: function (err) {
               Swal.fire(
                  err.status+' !',
                  err.message,
                  err.status
               )
            }
          });
        }
      });
  }); 
}); 

</script>

@endsection