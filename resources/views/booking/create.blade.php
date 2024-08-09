@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   {{-- <section class="content-header">
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
   </section> --}}
   {{-- ./Content Header --}}

   <section class="content">
      <form method="POST" action="{{ route('booking.store') }}" id='BookingForm'>
         @csrf
         <div class="card">

            <div class="card-header">
               <h3 class="card-title">{{ $title ?? '' }} <span class="text-bold">#{{Helper::docPrefix('bookings')
                     }}</span></h3>
            </div>
            <div class="card-body">
               <div class="row">

                  <div class="col-4 col-md-4 col-sm-4">
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
                           <form method="POST" id="filterForm" class="form columnForm" role="form">
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label for="member_logins">Cari Anggota</label>
                                       <select class="form-control form-control-sm selectMember" id="membership_code"
                                          name="membership_code" onclick="return _select()" id="membership_code"
                                          autocomplete="off" required>
                                       </select>

                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label for="member_id">ID Keanggotaan</label>
                                       <input type="text" class="form-control form-control-sm" name="member_id"
                                          id="member_id" placeholder="ID Anggota" autofocus required>
                                    </div>
                                 </div>

                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group"> 
                                       <label>Tanggal</label>
                                       <div class="form-group" data-target="#dateSelect" data-toggle="datetimepicker">
                                          <div class="input-group date" id="dateSelect" data-target-input="nearest">
                                             {{-- <div class="input-group-append">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                             </div> --}}
                                             <input type="text"
                                                class="form-control form-control-sm  datetimepicker-input"
                                                data-target="#dateSelect" min="{{ Date('Y-m-d') }}" value="{{ Date('Y-m-d') }}" name="date_need"
                                                on readonly required />
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label for="nik">NIK</label>
                                       <input type="text" class="form-control form-control-sm" id="nik" name="nik"
                                          placeholder="NIK">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label for="name">Nama</label>
                                       <input type="text" class="form-control form-control-sm" name="name" id="name"
                                          placeholder="Nama Pemesan">
                                    </div>
                                 </div>

                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label for="mobile">Nomor HP</label>
                                       <input type="text" class="form-control form-control-sm" name="mobile" id="mobile"
                                          placeholder="No. HP Pemesan">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-12">
                                    <div class="form-group">
                                       <label for="address">Alamat</label>
                                       <textarea class="form-control" id="address" name="address" rows="3" id="alamat"
                                          placeholder="Alamat ..."></textarea>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-12">
                                    <div class="form-group" id="necessary_div">
                                       <label for="necessary">Catatan Pemesanan</label>
                                       <textarea class="form-control" id="necessary" name="necessary" rows="4"
                                          placeholder="Catatan ..." required></textarea>
                                    </div>
                                 </div>
                              </div>

                        </div>
                     </div>
                  </div>
                  <div class="col-8 col-md-8 col-sm-8">
                     {{-- ./header card --}}
                     {{-- detail card --}}
                     <div class="card">

                        <div class="card-header">
                           <div class="btn-group col-sm-6  float-left">
                              <button type="button" class="col-sm-4 btn btn-xs btn-success add">Tambah</button>
                              <button type="button" class="col-sm-4 btn btn-xs btn-danger remove">Hapus</button>
                           </div>
                           <div class="btn-group auto float-right">
                              <button type="submit" class="btn btn-xs btn-info ">Simpan</button>
                           </div>
                        </div>
                        <div class="card-body">
                           <table class="table table-sm table-hover table-responsive-sm">
                              <thead>
                                 <tr>
                                    <th class="text-center" width="2%"><input type="checkbox" id="checkAll"></th>
                                    <th class="text-center" width="40%">Item</th>
                                    <th class="text-center" width="8%">Kuantitas</th>
                                    <th class="text-center" width="25%">Harga</th>
                                    <th class="text-center" width="25%">Jumlah</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr id="0">
                                    <td>
                                       <input type="checkbox" class="cBox"><input type="hidden" name="counting"
                                          value="0">
                                    </td>
                                    <td>
                                       <select class="form-control form-control-sm selectItem" name="nama[0]"
                                          placeholder="Nama Item" id="nama_0" autocomplete="off" required>
                                       </select>
                                    </td>
                                    <td>
                                       <input type="number" class="form-control form-control-sm" name="quantity[0]"
                                          id="quantity_0" placeholder="Qty" onkeypress="calcSum(0)" onkeyup="calcSum(0)"
                                          min=0 ondrop="return false;" onpaste="return false;" autocomplete="off" required>
                                    </td>
                                    <td>
                                       <input type="text" class="form-control form-control-sm uang" name="sell_price[0]"
                                          id="sell_price_0" placeholder="Harga"
                                          min=0 ondrop="return false;" onpaste="return false;" autocomplete="off"
                                          readonly required>
                                    </td>
                                    <td>
                                       <input type="text" class="form-control form-control-sm uang" name="sub_total[0]"
                                          id="sub_total_0" placeholder="Jumlah" onkeyup="goToFirst(0);"
                                        min=0 ondrop="return false;"
                                          onpaste="return false;" autocomplete="off" readonly required>
                                    </td>

                                 </tr>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-bold">Sub Total</td>
                                    <td>
                                       <input type="text" class="form-control form-control-sm uang"
                                          name="sub_grand_total" id="sub_grand_total" value="0" placeholder="Sub Total"
                                          min=0 ondrop="return false;" onpaste="return false;" autocomplete="off"
                                          readonly required>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-bold">Diskon(%)</td>
                                    <td>
                                       <input type="number" class="form-control form-control-sm" name="discount"
                                          id="discount" value="0" step="0.01" placeholder="Diskon"
                                          onkeypress="calcGrand()" onkeyup="calcGrand()" onchange="calcGrand()" min="0"
                                          max="100" maxlength="1" ondrop="return false;" onpaste="return false;"
                                          autocomplete="off" required>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-bold">Pajak(%)</td>
                                    <td>
                                       <input type="number" class="form-control form-control-sm" name="tax" id="tax"
                                          onkeypress="calcGrand()" onkeyup="calcGrand()" onchange="calcGrand()"
                                          value="0" step="0.01" placeholder="Pajak" min="0" max="50"
                                          ondrop="return false;" onpaste="return false;" autocomplete="off" required>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-bold">Total</td>
                                    <td>
                                       <input type="text" class="form-control form-control-sm uang" name="total"
                                          id="total" value="0" placeholder="Total" min=0 ondrop="return false;"
                                          onpaste="return false;" autocomplete="off" readonly required>
                                    </td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                        <div class="card-footer">
                           <div class="btn-group col-sm-6  float-left">
                              <button type="button" class="col-sm-4 btn btn-xs btn-success add">Tambah</button>
                              <button type="button" class="col-sm-4 btn btn-xs btn-danger remove">Hapus</button>
                           </div>
                           <div class="btn-group auto float-right">
                              <button type="submit" class="btn btn-xs btn-info ">Simpan</button>
                           </div>
                        </div>
                     </div>
                     {{-- ./detail card --}}
                  </div>
               </div>
               {{-- header card --}}
               {{-- add collapsed-card to collpase --}}


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
            var i=parseInt($('table tbody tr:last').attr('id'));
            var compare = (i-id)===0;
            // console.table(id,i, compare)
            $('#name_'+id).val(evt.params.data.name);
            $('#quantity_'+id).val(evt.params.data.quantity);
            $('#sell_price_'+id).val(evt.params.data.sell_price);
            $('#quantity_'+id).focus();
            compare && _addTr(i+1);
            // calcSum(id)

      });
   });


   }
     

   const getDataMember = () => {
      $(document).on('keyup', 'input#member_id', function (event) {
         // console.log('getDataMember')
      event.preventDefault();
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      let href = '/booking/getDataMember';
      $.ajax({
         url: href,
         
         method: "POST",
         data: {
            code: $("input#member_id").val(),
         },
         success: function (result) {
            // setSelect2
            if(result.id){
               var newOption = new Option(result.text, result.id, true, true);
               $('#membership_code').append(newOption).trigger('change');
               // set form lainnya
               $('#nik').val(result.nik);
               $('#name').val(result.name);
               $('#mobile').val(result.mobile);
               $('#address').val(result.address);
               //scroll n focus catatan 
               let necessary = document.getElementById("necessary_div");
               necessary.scrollIntoView();
               $("#necessary").focus();
            }

         },
         error: function (jqXHR, testStatus, error) {
               // console.log(jqXHR)
            Swal.fire(
                  jqXHR.responseJSON.status == 'success' ? 'Berhasil !' : 'Gagal !',
                  jqXHR.responseJSON.message,
                  jqXHR.responseJSON.status
               )
            // console.table(jqXHR, testStatus, error)
         },
         timeout: 8000
      })
      });
   }



   function goToFirst(id) {
      var e = event || id; 
      var charCode = e.which || e.keyCode;
      // var i=$('table tbody tr').length;
      var i=parseInt($('table tbody tr:last').attr('id'));

      var compare = (i-id)===1;
      if (charCode == 9 ) {
         compare && _addTr(i+1);
      }
      return false;
   };
   
   function calcGrand(){
      $('input#sub_grand_total').unmask().val();
      $("input#total").unmask().val();
      let subGrandTotal = parseInt(0);
      
      var i=parseInt($('table tbody tr:last').attr('id'));

      for (let index = 0; index <= i; index++) {
         let checkNAN =parseInt($('#sub_total_'+index).unmask().val())
         let sub_total = checkNAN ? checkNAN : 0;
         // console.table(index, i, sub_total)

         subGrandTotal = subGrandTotal+sub_total
      }
      let disc = $('#discount').val()/100;
      let tax = $('#tax').val()/100;
      let summary = subGrandTotal-(subGrandTotal*disc)
      let sumTax = summary+(summary*tax)
      $('#sub_grand_total').val(subGrandTotal);
  
      $('#total').val(sumTax);
      $('input#sub_grand_total').mask('#.##0', {reverse: true});
      $('input#total').mask('#.##0', {reverse: true});
   }

   function calcSum(id){
      let qty = $("input#quantity_"+id).val();
      // let sell_price = $("input#sell_price_"+id).val()
      let sell_price = $("input#sell_price_"+id).unmask().val();
      let sub_total = qty*sell_price;
      
      $('#sub_total_'+id).val(sub_total);

      $('input#sub_total_'+id).mask('#.##0', {reverse: true});
      calcGrand();
   }

   const _addTr = (i) =>{
      html = '<tr id="'+i+'">';
      html += '<td><input type="checkbox" class="cBox"><input type="hidden" name="counting" value="'+i+'" autocomplete="off"></td>';
      html += '<td><select class="form-control form-control-sm selectItem" name="nama['+i+']" placeholder="Nama/Kode Item" id="nama_'+i+'"></select></td>';
      html += '<td><input type="number" class="form-control form-control-sm" name="quantity['+i+']" placeholder="Qty" id="quantity_'+i+'"    onkeyup="calcSum('+i+')" min=1 ondrop="return false;" onpaste="return false;" autocomplete="off"></td>';
      html += '<td><input type="text" class="form-control form-control-sm uang" name="sell_price['+i+']" placeholder="Harga" id="sell_price_'+i+'"   min=1 ondrop="return false;" onpaste="return false;" autocomplete="off" readonly></td>';
      html += '<td><input type="text" class="form-control form-control-sm uang" name="sub_total['+i+']" placeholder="Jumlah" id="sub_total_'+i+'"  onkeyup="return goToFirst('+i+');"   min=1 ondrop="return false;" onpaste="return false;" autocomplete="off" readonly></td>';
      html += '</tr>';
      $('table').append(html);
      _select(); 
      calcSum(i)
      calcGrand()
      
   }
 


   $(document).ready(function () {
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
      getDataMember();
      _select(); //call function _select   
      var i=parseInt($('table tbody tr:last').attr('id'));
      _addTr(i+1);
      _select(); 
      calcGrand()
      // add table row
      $(".add").on('click',function(){
         // var i=$('table tbody tr').length;
         var i=parseInt($('table tbody tr:last').attr('id'));
         _addTr(i+1);
         _select(); 
         calcGrand()
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
         _select(); 
         calcGrand()
      });

      //handling tab end form
 

   });
</script>

@endsection