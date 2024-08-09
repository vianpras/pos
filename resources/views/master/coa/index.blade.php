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
                  <div class="row d-flex flex-row justify-content-between">
                     <div class="col-5  mt-1">
                        <span class="text-lightblue" data-card-widget="collapse">
                           <i class="fas fa-filter"></i>&nbsp;&nbsp;
                           Alat & Pencarian
                        </span>
                     </div>
                     <div class="col-7">
                        <div class="d-flex flex-row justify-content-end">
                           {{-- Filter Table --}}
                           <div id="actionLength" class="">
                           </div>&nbsp;
                           {{-- Action Table --}}
                           @if(Helper::checkACL('master_coa', 'e'))
                           <div id="actionFilter" class=""></div>&nbsp;
                           @endif
                           {{-- Other Action --}}
                           <div class="btn-group">
                              <button type="button" class="btn btn-tool  " data-toggle="dropdown">
                                 <i class="fas fa-ellipsis-v fa-lg  text-secondary"></i>

                              </button>
                              <div class="dropdown-menu dropdown-menu-right" role="menu">
                                 @if(Helper::checkACL('master_coa', 'c'))
                                 <a href="#" id="newButton" class="dropdown-item"> <i
                                       class="fas fa-plus-square"></i>&nbsp;Data Baru</a>
                                 @endif
                                 @if(Helper::checkACL('master_coa', 'i'))
                                 <a href="#" class="dropdown-item"> <i class="fas fa-file-excel"></i> &nbsp; Impor
                                    Data</a>
                                 @endif
                                 <a href="#" class="dropdown-item" data-card-widget="collapse"><i
                                       class="fas fa-search-plus"></i> &nbsp; Tampilkan</a>
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
                              <label for="code_account_default">Kode Akun Default</label>
                              <input type="text" class="form-control form-control-sm" name="code_account_default" id="code_account_default" placeholder="Kode Akun">
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="name">Nama</label>
                              <input type="text" class="form-control form-control-sm" name="name" id="name" placeholder="Filter Nama">
                           </div>
                        </div>
                        {{-- <div class="col-sm-3">
                           <div class="form-group">
                              <label for="name">Nama</label>
                              <input type="text" class="form-control form-control-sm" name="name" id="name" placeholder="Filter Nama">
                           </div>
                        </div> --}}
                        {{-- <div class="col-sm-3">
                           <div class="form-group">
                              <label for="unit_status_filter">Status</label>
                              <select class="form-control form-control-sm select2" style="width: 100%;"
                                 name="unit_status_filter">
                                 <option value='1'>Aktif</option>
                                 <option value='0'>Non-Aktif</option>
                                 <option value='-1' selected="selected">All</option>
                              </select>
                           </div>
                        </div> --}}
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
                     <th></th>
                     <th> Aksi </th>
                     <th> Kode Akun </th>
                     <th> Nama </th>
                     {{-- <th> Status </th> --}}
               </thead>
               </tr>
               <tfoot>
                  <tr>
                     <th></th>
                     <th class='notexport'></th>
                     <th>Kode Akun</th>
                     <th>Nama</th>
                     {{-- <th></th> --}}
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
                  columns: [1, 2]
               },
            },
            {
               extend: 'copy',
               copy: 'Salin',
               className: 'btn btn-warning btn-sm',
               text: '<i class="fas fa-copy"></i>  Salin',
               exportOptions: {
                  columns: [1, 2]
               },
            },
            {
               extend: 'excel',
               className: 'btn btn-success btn-sm',
               text: '<i class="fas fa-file-excel"></i> Excel',
               exportOptions: {
                  columns: [1, 2]
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
            "sLengthMenu": "_MENU_",
         },
         ajax: {
            url: '{{ route("coa.datatable") }}',
            'type': 'POST',
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
               d.coa_code_filter = $('input[name=category_code_filter]').val();
               d.coa_name_filter = $('input[name=name]').val();
               // d.category_status_filter = $('select[name=category_status_filter] option').filter(':selected').val()
            }
         },
         columns: [
            {
               "className"       : 'details-control',
               "orderable"       : false,
               "searchable"      : false,
               "data"            : null,
               "defaultContent"  : ''
            },
            {data: 'action', name: 'action', 'orderable': false, 'searchable': false},
            {data: 'code_account_default', name: 'code_account_default'},
            {data: 'name', name: 'name'},
            // {data: 'group_of_account', name: 'group_of_account'},
            // {data: 'status', name: 'status', 'orderable': false, 'searchable': false}
         ],
      });

      //  sumbit filter and redraw
      $('#filterForm').on('submit', function (e) {
         dTable.draw();
         e.preventDefault();
      });

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

      //  error handling
      $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
         Swal.fire(
            'Error !',
            'Terjadi Kesalahan Server, Coba Refresh Halaman',
            'error'
         )
      };
   });
</script>

<script>
   // new Data Modal
   $(document).on('click', '#newButton', function (event) {

      event.preventDefault();
      let href = '{{ route("coa.new") }}';
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
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      $.ajax({
         url: "/dataInduk/coa/store/",
         method: "POST",
         cache:false,
         data: form,
         contentType: false,
         processData: false,
         beforeSend: function () {
            $('#loader').show();
         },
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
         url: "/dataInduk/kategori/update/" + _id,
         method: "POST",
         cache:false,
         data: form,
         contentType: false,
         processData: false,
         beforeSend: function () {
            $('#loader').show();
         },
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
            //   text: "Kategori tidak akan bisa di pakai kembali",
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
                  url: "{{ route('disableCategory') }}",
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

   // conditional kode akun
   function conditionKode(item) {
      $("#addon-kodeAkun").html("");

      var parentValue = item.value;
      if(parentValue && parentValue !== "null"){
         var splitKode = parentValue.split(".");
         $("#addon-kodeAkun").html(splitKode[0]);
      }
   }


</script>

@endsection