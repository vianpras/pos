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
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-body" style="display: none;">
               <form method="POST" id="filterForm" class="form columnForm" role="form">
                  <div class="row">
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label for="item_code_filter">Kode</label>
                           <input type="text" class="form-control form-control-sm" name="itemcode_filter" id="code" placeholder="Filter Kode">
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label for="item_name_filter">Nama</label>
                           <input type="text" class="form-control form-control-sm" name="itemname_filter" id="name" placeholder="Filter Nama">
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <label for="item_category_filter">Kategori Harga</label>
                        <select class="form-control form-control-sm select2" style="width: 100%;" name="pricelist_filter">
                           <option value='-1' selected="selected">Semua</option>
                           @foreach ($category as $option)
                           <option value="{{ $option->listnum }}">{{ $option->listname }}</option>
                           @endforeach
                        </select>
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
                     <th width="8%">Aksi</th>
                     <th width="12%">Kode</th>
                     <th>Nama</th>
                     <th>Kategori Harga</th>
                     <th>Harga</th>
                  </tr>
               </thead>
               {{-- <tfoot>
                  <tr>
                     <th class='notexport'></th>
                     <th>Kode</th>
                     <th>Nama</th>
                     <th>Kategori Harga</th>
                     <th>Harga</th>
                  </tr>
               </tfoot> --}}
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
   //  dTable Scripting
   $(document).ready(function () {
      var resetButton = $('#resetButton').on('click', function (e) {
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
         "order": [[1, "asc"]],
         "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
         stateSave: true,
         dom: 'lBfrtip',
         buttons: [

            {
               extend: 'colvis',
               text: '<i class="fas fa-eye"></i> Kolom',
               className: 'btn btn-info btn-sm',
               postfixButtons: [{
                  extend: 'colvisRestore',
                  text: 'Reset V.bility',
               }]
            },
            {
               extend: 'print',
               text: '<i class="fas fa-print"></i> Cetak',
               className: 'btn bg-navy btn-sm',

               exportOptions: {
                  columns: [1, 2, 3, 4]
               },
            },
            {
               extend: 'copy',
               copy: 'Salin',
               className: 'btn btn-warning btn-sm',
               text: '<i class="fas fa-copy"></i>  Salin',
               exportOptions: {
                  columns: [1, 2, 3, 4]
               },
            },
            {
               extend: 'excel',
               className: 'btn btn-success btn-sm',
               text: '<i class="fas fa-file-excel"></i> Excel',
               exportOptions: {
                  columns: [1, 2, 3, 4]
               },

            },
            {
               text: '<i class="fas fa-sync-alt"></i> Reset',
               className: 'btn btn-danger btn-sm',
               action: function (e, dt, node, config) {
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
            sLengthMenu: "_MENU_",
         },
         ajax: {
            url: '{{ route("item.datatable") }}',
            type: 'POST',
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
               d.itemcode_filter = $('input[name=itemcode_filter]').val();
               d.itemname_filter = $('input[name=itemname_filter]').val();
               d.pricelist_filter = $('select[name=pricelist_filter] option').filter(':selected').val()
            }
         },
         columns: [
            {data: 'action', name: 'action', 'orderable': false, 'searchable': false},
            {data: 'itemcode', name: 'itemcode'},
            {data: 'itemname', name: 'itemname'},
            {data: 'listname', name: 'listname'},
            {data: 'pricing', name: 'price'}
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
            'Terjadi Kesalahan Server, Coba Refresh Halaman',
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

         // if (!disabled) {
         //    $(this).html('<input type="text" class="form-control form-control-sm filter" value="' + value + '" placeholder="Filter ' + title + '"' + disabled + ' />');
         // }
      });

      //  aktivated column filter
      // dTable.columns().every(function () {
      //    var that = this;
      //    $('input', this.footer()).on('keyup change', function () {
      //       that.search(this.value).draw();
      //    });
      // });


   });
</script>

<script>
   // new Data Modal
   $(document).on('click', '#newButton', function (event) {
      event.preventDefault();
      let href = '{{ route("item.new") }}';
      $.ajax({
         url: href,
         beforeSend: function () {
            // $('#loader').show();
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
            // $('#loader').hide();
         },
         error: function (jqXHR, testStatus, error) {
            // console.log(error);
            alert("Page " + href + " cannot open. Error:" + error);
            $('#loader').hide();
         },
         timeout: 8000
      })
   });
   // ./new Modal

   // edit Data Modal
   $(document).on('click', '#editButton', function (event) {

      event.preventDefault();
      let href = $(this).attr('data-attr');
      $.ajax({
         url: href,
         beforeSend: function () {
            // $('#loader').show();
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

   // store Data Modal
   $(document).on('click', '#saveButton', function (event) {
      event.preventDefault();
      var form = new FormData();
      var formTexts = $("#formNew").serializeArray();
      formTexts.forEach(formText => {
         form.append(formText.name,formText.value)
      });
      var files = $('#image')[0].files;
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      
      if(files.length > 0 ){
           form.append('image',files[0]);
      }
      $.ajax({
         url: "/dataInduk/barang/store",
         method: "POST",
         cache:false,
         data: form,
         contentType: false,
         processData: false,
         // return the result
         success: function (result) {
            var oTable = $('#dTable').dataTable();
            oTable.fnDraw(false);
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
            // $('#loader').hide();
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
   // ./store Data Modal
   // update Data Modal
   $(document).on('click', '#updateButton', function (event) {
      event.preventDefault();
      var form = new FormData();
      var formTexts = $("#formUpdate").serializeArray();
      formTexts.forEach(formText => {
         form.append(formText.name,formText.value)
      });
      var files = $('#image')[0].files;
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      if(files.length > 0 ){
           form.append('image',files[0]);
           console.log('ada gambar')

      }
      let _id = $(this).attr('data-id');
      $.ajax({
         url: "/dataInduk/barang/update/" + _id,
         method: "POST",
         cache:false,
         data: form,
         contentType: false,
         processData: false,
         
         // return the result
         success: function (result) {
            var oTable = $('#dTable').dataTable();
            oTable.fnDraw(false);
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
            // $('#loader').hide();
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
   // ./update Data Modal

   // handling disable user
   $(document).ready(function () {
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      $('body').on('click', '#_bDelete', function () {
         var _id = $(this).data("id");

         Swal.fire({
            title: 'Yakin ingin mengubah data?',
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
                  url: "{{ route('disableItem') }}",
                  data: {"id": _id},
                  success: function (data) {
                     var oTable = $('#dTable').dataTable();
                     oTable.fnDraw(false);
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
      });
   });

</script>

@endsection