@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                  <li class="breadcrumb-item"><a href="./">Permintaan Pembelian</a></li>
                  <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
               </ol>
            </div>
         </div>
      </div>
   </section>
   {{-- ./Content Header --}}

   <section class="content">
      <form method="POST" action="{{ route('requistion.update', $requistion->code) }}">
         @csrf
         <input type="hidden" name="requistion_id" value="{{ $requistion->code }}">
         <div class="card">
            <div class="card-header">
               <h3 class="card-title">{{ $title ?? '' }} </h3>
            </div>
            <div class="card-body">
               {{-- header card --}}
               {{-- add collapsed-card to collpase --}}
               <div class="card">
                  <div class="card-header" data-card-widget="collapse">
                     <h3 class="card-title" data-card-widget="collapse">
                        Header Data {{ $title ?? '' }}
                     </h3>
                     <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                           <i class="fas fa-minus"></i>
                        </button>
                     </div>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label>Tanggal Dibutuhkan</label>
                              <div class="form-group" data-target="#dateSelect" data-toggle="datetimepicker">
                                 <div class="input-group date" id="dateSelect" data-target-input="nearest">
                                    <input type="text" class="form-control form-control-sm  datetimepicker-input"
                                       data-target="#dateSelect" value="{{ $requistion->date_need }}" name="date_need"
                                       readonly required />
                                    <div class="input-group-append">
                                       <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="code">Kode</label>
                              <input type="text" class="form-control form-control-sm" name="code" placeholder="Kode"
                                 readonly value="{{ $requistion->code }}">
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="project_id">Proyek</label>
                              <select class="form-control form-control-sm select2" style="width: 100%;"
                                 name="project_id">
                                 <option value="0" @if($requistion->project_id === null) selected @endif> --- </option>
                                 @foreach ($project as $option)
                                 <option value="{{ $option->code }}" @if($option->code === $requistion->project_id) selected
                                    @endif >{{
                                    $option->code }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="status">Status</label>
                              <input type="text" class="form-control form-control-sm" name="status" placeholder="Status"
                                 value="{{ $requistion->status }}" readonly>
                           </div>
                        </div>
                        <div class="col-sm">
                           <div class="form-group">
                              <label for="note">Deskripsi</label>
                              <textarea class="form-control" id="note" name="note" rows="2"
                                 placeholder="Deskripsi ...">{{ $requistion->note }}</textarea>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               {{-- ./header card --}}
               {{-- detail card --}}
               <div class="card">
                  <div class="card-body">
                     <table class="table table-sm table-hover table-responsive-sm">
                        <thead>
                           <tr>
                              <th class="text-center" width="2%"><input type="checkbox" id="checkAll"></th>
                              <th class="text-center" width="50%">Item</th>
                              <th class="text-center" width="8%">Kuantitas</th>
                              <th class="text-center" width="40%">Deskripsi</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($requistion_detail as $key => $item)
                           <tr id="0">
                              <td>
                                 <input type="checkbox" class="cBox">
                                 <input type="hidden" name="counting" value="{{ $key }}">
                              </td>
                              <td>
                                 <select class="form-control form-control-sm selectItem" name="nama[{{ $key }}]"
                                    placeholder="Nama Item" id="nama_{{ $key }}" autocomplete="off" >
                                    <option value="{{ $item->item_id }}" selected >{{ $item->item_name.' | '.$item->item_code }}</option>
                                 </select>
                              </td>
                              <td>
                                 <input type="number" class="form-control form-control-sm" name="quantity[{{ $key }}]"
                                    id="quantity_{{ $key }}" placeholder="Kuantitas Item" onkeypress="return IsNumeric(event);"
                                    min={{ $key }} ondrop="return false;" onpaste="return false;" autocomplete="off" value="{{ $item->quantity }}">
                              </td>
                              <td>
                                 <input type="text" class="form-control form-control-sm" name="description[{{ $key }}]"
                                    placeholder="Deskripsi Item" onkeyup="goToFirst({{ $key }});" autocomplete="off" value="{{ $item->description }}">
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
                  <div class="card-footer">
                     <div class="btn-group col-md-2  float-left">
                        <button type="button" class="col-sm-8 btn btn-xs btn-success add">Tambah</button>
                        <button type="button" class="col-sm-8 btn btn-xs btn-danger remove">Hapus</button>
                     </div>
                     <div class="btn-group auto float-right">
                        <a class="btn btn-xs btn-danger" href="../" role="button">Kembali</a>
                        <button type="submit" class="btn btn-xs btn-info ">Simpan</button>
                     </div>
                  </div>
               </div>
               {{-- ./detail card --}}
            </div>
         </div>
      </form>
   </section>
</div>
@endsection
@section('jScript')
<script>
   const _select = () => {
      $(function(){
         $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
         });
         $('.selectItem').select2({
            
            minimumInputLength: 2,
            placeholder: 'Masukkan Nama / Kode Item',
            ajax: {
               dataType: 'json',
               method: 'POST', 
               url: "{{ route('getItem') }}",
               delay: 100,
               data: function(params) {
                  return {
                     data: params.term
                  }
               },
               processResults: function (data, page) {
                  return {
                     results: data
                  };
               },
            }
            }).on('select2:select', function (evt) {
               // var data = $(".select2 option:selected").text();
               var id = this.parentNode.parentNode.id;
               var i=$('table tbody tr').length;
               var compare = (i-id)===1;
               $('#name_'+id).val(evt.params.data.name);
               $('#quantity_'+id).val(evt.params.data.quantity);
               $('#quantity_'+id).focus();
               compare && _addTr(i);
         });
      });

   }

   function goToFirst(id) {
      var e = event || id; 
      var charCode = e.which || e.keyCode;
      var i=$('table tbody tr').length;
      var compare = (i-id)===1;
      if (charCode == 9 ) {
         compare && _addTr(i);
      }
      return false;
   };
   
   const _addTr = (i) =>{
      html = '<tr id="'+i+'">';
      html += '<td><input type="checkbox" class="cBox"><input type="hidden" name="counting" value="'+i+'" autocomplete="off"></td>';
      html += '<td><select class="form-control form-control-sm selectItem" name="nama['+i+']" placeholder="Nama/Kode Item" id="nama_'+i+'"></select></td>';
      html += '<td><input type="number" class="form-control form-control-sm" name="quantity['+i+']" placeholder="Qty Item" id="quantity_'+i+'"  onkeypress="return IsNumeric(event);" min=1 ondrop="return false;" onpaste="return false;" autocomplete="off"></td>';
      html += '<td><input type="text" class="form-control form-control-sm" name="description['+i+']" placeholder="Deskripsi Item" onkeyup="return goToFirst('+i+');" autocomplete="off"></td>';
      html += '</tr>';
      $('table').append(html);
      _select(); 
      
   }
 


   $(document).ready(function () {

      _select(); //call function _select   
      // add table row
      $(".add").on('click',function(){
         var i=$('table tbody tr').length;
         _addTr(i);
         _select(); 
         i++;
      });


      //to check all checkboxes
      $(document).on('change','#checkAll',function(){
         $('input[class=cBox]:checkbox').prop("checked", $(this).is(':checked'));
      });

      //deletes the selected table rows
      $(".remove").on('click', function() {
         $('.cBox:checkbox:checked').parents("tr").remove();
         $('#check_all').prop("checked", false); 
      });

      //handling tab end form
 

   });
</script>

@endsection