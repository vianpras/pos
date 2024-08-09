@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <section class="content" style="min-height: 40vh; max-height: 40vh">
      <form id='SalesForm'>
         @csrf
         <div class="row p-2" style="min-height: 40vh; max-height: 40vh">
            <div class="col-7 col-md-7 col-sm-7">
               <div class="card">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label for="supplier_code">Supplier</label>
                              <input type="text" class="form-control form-control-sm" name="supplier_code"
                                 id="supplier_code" placeholder="ID Supplier" autofocus>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label for="supplier">Nama Supplier</label>
                              <input type="text" class="form-control form-control-sm" name="supplier" id="supplier"
                                 placeholder="Nama Supplier" required>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-6">
                           <label for="date_order">Tanggal Pembelian</label>
                           <div class="form-group" data-target="#dateSelect" data-toggle="datetimepicker">
                              <div class="input-group date" id="dateSelect" data-target-input="nearest">
                                 <div class="input-group-append">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                 </div>
                                 <input type="text" class="form-control form-control-sm  datetimepicker-input"
                                    data-target="#dateSelect" min="{{ Date('Y-m-d') }}" value="{{ Date('Y-m-d') }}"
                                    name="date_order" readonly required />
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <label for="discount">Diskon <span class="text-teal">(%)</span></label>
                           <div class="form-group">
                              <div class="input-group date" id="dateSelect" data-target-input="nearest">

                                 <div class="input-group-append">
                                    <div class="input-group-text"><i class="fas fa-percent"></i></div>
                                 </div>
                                 <input type="number" class="form-control form-control-sm" name="discount" id="discount"
                                    value="0" step="0.01" placeholder="Diskon" onkeypress="calcGrand()"
                                    onkeyup="calcGrand()" onchange="calcGrand()" min="0" max="100" maxlength="1"
                                    ondrop="return false;" onpaste="return false;" autocomplete="off" value="0"
                                    required>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-3">
                           <div class="form-group">
                              <label for="tax">Pajak</label>
                              <div class="row align-item-center justify-content-arround">
                                 <div class="col-6">
                                    <button type="button" class="btn btn-outline-success btn-xs" id="tax_item"
                                       value="10" onclick="setTax(10)"> Tax 10%</button>
                                 </div>
                                 <div class="col-6">
                                    <button type="button" class="btn btn-outline-success btn-xs" id="tax_jasa" value="2"
                                       onclick="setTax(2)">Tax 2%</button>
                                 </div>
                              </div>

                           </div>
                        </div>
                     </div>
                     {{-- <div class="row">
                        <div class="col-sm-12">
                           <div class="form-group">
                              <label for="description">Keterangan</label>
                              <textarea class="form-control" cols="1" placeholder="Keterangan"></textarea>
                           </div>
                        </div>
                     </div> --}}
                  </div>
               </div>
               <div class="card">
                  <div class="card-header">
                     <div class="input-group">
                        <input type="search_item" name="search_item" id="search_item"
                           class="form-control form-control-md" placeholder="Masukkan Nama Item" autofocus>
                        <div class="input-group-append">
                           <button type="submit" class="btn btn-md btn-default">
                              <i class="fa fa-search"></i>
                           </button>
                        </div>
                     </div>
                     {{-- list category --}}
                     <div class="scrollXMenu ">
                        <div class="d-flex align-item-center justify-content-start pt-1 pb-0">

                           <div class="mx-1 px-1 pt-3 col-sm-3 col-xs-6 ">
                              <button type="button" class="btn bg-maroon btn-flat btn-block btn-sm"
                                 onclick="getItemClick(this)" id="">Semua Item</button>
                           </div>

                           <div class="mx-1 px-1 pt-3 col-sm-3 col-xs-6 ">
                              <button type="button" class="btn bg-orange btn-flat btn-block btn-sm"
                                 onclick="getItemClick(this)" id="favorit">Favorit</button>
                           </div>
                           @foreach($categories as $key => $category)
                           <div class="mx-1 px-1 pt-3 col-sm-3 col-xs-6 ">
                              <button type="button" class="btn bg-teal btn-flat btn-block btn-sm"
                                 onclick="getItemClick(this)" id="{{$category->name}}">{{$category->name}}</button>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
                  <div class="card-body">
                     {{-- item load disini --}}
                     <div class="scrollYMenu">
                        <div class="row align-item-center justify-content-around" id="listMenu">

                           @foreach($items as $key => $item)
                           <div class="col-sm-5 col-md-5 color-palette shadow m-1"
                              style="background-color: #3F474E; border-radius: 0.5em ;"
                              onclick="addToCart('{{$item->id}}','{{$item->code}}','{{$item->name}}','{{$item->buy_price}}')">
                              <div class="row my-3 align-item-center justify-content-arround">
                                 <div class="col-sm-4 col-md-4 mx-1">
                                    <img src="/dist/img/kopi.png"
                                       class="profile-user-img img-fluid img-circle text-center">
                                 </div>
                                 <div class="col-sm-7 col-md-7 ">
                                    <p class="py-0 m-0 text-wrap text-uppercase text-bold">{{$item->name}}</p>
                                    <p class="py-0 m-0 text-wrap text-secondary">{{$item->category_name}}</p>
                                    <p class="py-0 m-0 text-wrap text-teal">
                                       {{Helper::formatNumber($item->buy_price,'rupiah')}}</p>
                                 </div>
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-5 col-md-5 col-sm-5">
               <div class="card">
                  <div class="card-header align-items-center justify-content-center">
                     <div class="row">
                        <div class="col-4">
                           <input type="hidden" name="total" id="total" value="0">
                           <span class="h6 m-0 float-left text-secondary">#{{Helper::docPrefix('purchases')}}</span>
                        </div>
                        <div class="col-8">
                           <span class="h4 m-0 float-right text-olive"
                              id="total_view">{{Helper::formatNumber('0','rupiah')}}</span>
                        </div>
                     </div>
                     <hr class="p-0 m-1">
                     <div class="row">
                        <div class="col">
                           <input type="hidden" name="sub_grand_total" id="sub_grand_total" value="0">
                           <span class="h5 m-0 float-left text-secondary">Sub Total</span>
                        </div>
                        <div class="col">
                           <span class="h5 m-0 float-right text-secondary"
                              id="sub_grand_total_view">{{Helper::formatNumber('0','rupiah')}}</span>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col">
                           <span class="h5 m-0 float-left text-secondary">Diskon</span>
                        </div>
                        <div class="col">
                           {{-- <input type="hidden" name="discount" id="discount" value="0"> --}}
                           <span class="h5 m-0 float-right text-secondary"
                              id="discount_view">{{Helper::formatNumber('0','rupiah')}}</span>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col">
                           <span class="h5 m-0 float-left text-secondary">Pajak</span>
                        </div>
                        <div class="col">
                           <input type="hidden" name="tax" id="tax" value="0">
                           <span class="h5 m-0 float-right text-secondary"
                              id="tax_view">{{Helper::formatNumber('0','rupiah')}}</span>
                        </div>
                     </div>
                  </div>
                  <div class="card-body py-0">
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group" id="pending">
                              <button type="button" onclick="saveData('bayar')"
                                 class="btn btn-flat btn-block btn-outline bg-maroon">Bayar</button>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group" id="pay">
                              <button type="button" onclick="saveData('simpan')"
                                 class="btn btn-flat btn-block bg-teal">Simpan</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer pb-0 m-0">
                     <div class="scrollYMenu pr-3" style="height:64vh" id="listCart">
                        {{-- start cart --}}
                        {{-- jquery on action --}}
                        {{-- end chart --}}
                     </div>

                  </div>
                  <div class="card-footer pb-0 m-0">
                  </div>
               </div>
            </div>



      </form>
   </section>
</div>
@endsection
@section('jScript')
<script>
   const _renderCard = (id,code,name,buy_price,category_name) =>{
      card ='<div class="col-sm-5 col-md-5 color-palette shadow m-1"'
      card +='style="background-color: #3F474E; border-radius: 0.5em ;"'
      card +='onclick="addToCart(\''+id+'\',\''+code+'\',\''+name+'\',\''+buy_price+'\')">'
      card +='<div class="row my-3 align-item-center justify-content-arround">'
      card +='<div class="col-sm-4 col-md-4 mx-1">'
      card +='<img src="/dist/img/kopi.png"'
      card +='class="profile-user-img img-fluid img-circle text-center">'
      card +='</div>'
      card +='<div class="col-sm-7 col-md-7 ">'
      card +='<p class="py-0 m-0 text-wrap text-uppercase text-bold">'+name+'</p>'
      card +='<p class="py-0 m-0 text-wrap text-secondary">'+category_name+'</p>'
      card +='<p class="py-0 m-0 text-wrap text-teal">'+maskRupiah("",buy_price)+'</p>'
      card +='</div>'
      card +='</div>'
      card +='</div>'
      $('#listMenu').append(card);
   }
   const getItemSearch = () => {
      $(document).on('keyup', 'input#search_item', function (event) {
      event.preventDefault();
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      let href = '/purchase/getItem';
      $.ajax({
         url: href,
         method: "POST",
         data: {
            search: $("input#search_item").val(),
         },
         success: function (result) {
            // setSelect2
            // console.table(result)
            // $('#search_item').val('');

            if(result.length > 0){
               $('#listMenu').empty();
               const items = result;
               for (const item of items) {
                  // console.log(item)
                  _renderCard(item.id,item.code,item.name,item.buy_price,item.category_name)
               }
            }else{
               $('#listMenu').empty();
               let $card = "<div class='align-item-center justify-content-center'> <h6>Item Tidak Ditemukan</h6> </div>";
               $('#listMenu').append($card);
            }

         },
         error: function (jqXHR, testStatus, error) {
               // console.log(jqXHR,error)
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


   const getItemClick = (value) => {
      // let search = 
      // console.log(search)
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      let href = '/purchase/getItem';
      $.ajax({
         url: href,
         method: "POST",
         data: {
            search: $(value).attr('id')
         },
         success: function (result) {
            // setSelect2
            $('#search_item').val('');

            if(result.length > 0){
               const items = result;
               $('#listMenu').empty();
               for (const item of items) {
          

                  _renderCard(item.id,item.code,item.name,item.buy_price,item.category_name)
               }
            }else{
               $('#listMenu').empty();
               let $card = "<div class='align-item-center justify-content-center'> <span class='text-muted h5'>Item Tidak Ditemukan</span> </div>";
               $('#listMenu').append($card);
            }

         },
         error: function (jqXHR, testStatus, error) {
               // console.log(jqXHR,error)
            Swal.fire(
                  jqXHR.responseJSON.status == 'success' ? 'Berhasil !' : 'Gagal !',
                  jqXHR.responseJSON.message,
                  jqXHR.responseJSON.status
               )
            // console.table(jqXHR, testStatus, error)
         },
         timeout: 8000
      })
   }

   const getDataSupplier = () => {
      $(document).on('keyup', 'input#supplier_code', function (event) {
         // console.log('getDataSupplier')
      event.preventDefault();
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      let href = '/purchase/getDataSupplier';
      $.ajax({
         url: href,
         
         method: "POST",
         data: {
            code: $("input#supplier_code").val(),
         },
         success: function (result) {
            // setSelect2
            if(result.id){
               // set form lainnya
               $('#supplier').val(result.name);
               $('#supplier_code').val(result.id);
               //scroll n focus catatan 
               $("#date_order").focus();
            }

         },
         error: function (jqXHR, testStatus, error) {
            Swal.fire(
                  jqXHR.responseJSON.status == 'success' ? 'Berhasil !' : 'Gagal !',
                  jqXHR.responseJSON.message,
                  jqXHR.responseJSON.status
               )
         },
         timeout: 8000
      })
      });
   }


   const saveData = (action) => {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         let href = '/purchase/store/'+action;
         let formData=  $('form').serialize();
         $.ajax({
            url: href,
            
            method: "POST",
            data:  formData,
            success: function (res) {
               if(res.status=="success"){
                  swal.fire({
                     title: res.status == 'success' ? 'Berhasil !' : 'Gagal !',
                     text: res.message,
                     icon: res.status,
                     // showCancelButton: true,
                     confirmButtonColor: '#3085d6',
                     cancelButtonColor: '#d33',
                     confirmButtonText: 'Ok !'
                  }).then((result) => {
                     if (result.isConfirmed) {
                        window.location = "{{url('/purchase/create')}}";
                  }
                  })
               }else{
                  Swal.fire(
                     res.status == 'success' ? 'Berhasil !' : 'Gagal !',
                     res.message,
                     res.status
                  )
               }
               
            },
            error: function (jqXHR, testStatus, error) {
               Swal.fire(
                     jqXHR.responseJSON.status == 'success' ? 'Berhasil !' : 'Gagal !',
                     jqXHR.responseJSON.message,
                     jqXHR.responseJSON.status
                  )
            },
            timeout: 8000
         })
   }

   function addToCart(id,code,name,buy_price){
         let lastClass = $('#listCart .card-cart:last ').attr('id')
         // console.log(name)
         let i= 0 
         if(lastClass === undefined){
            i=1
         }else{
            i= parseInt(lastClass.substr(lastClass.length - 1))+1;
         }

         cart = '<div class="card-cart card-cart-'+i+'" id="card-cart-'+i+'" data-id=\"'+code+'\" data-item-id=\"'+id+'\"><div class="card">';
         cart+= '<div class="card-body">';
         cart+= '<input type="hidden" name="item_id['+i+']" id="item_id_'+i+'" value="'+id+'">';
         cart+= '<input type="hidden" class="" name="sub_total['+i+']" id="sub_total_'+i+'" value="0">';
         cart+= '<div class="row align-items-center justify-content-between">';
         cart+= '<div class="col-sm-2 col-md-2">';
         cart+= '<div class="text-center">';
         cart+= '<img src="/dist/img/kopi.png" class="profile-user-img img-fluid img-circle"';
         cart+= 'alt="nama gambar">';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '<div class="col-md-6 col-sm-6">';
         cart+= '<div class="row align-items-center justify-content-between">';
         cart+= '<div class="col-md-12 col-md-12">';
         cart+= '<p class="h6 py-0 m-0 text-wrap text-uppercase"> '+name+' </p>';
         cart+= '<span class="text-bold text-olive py-0 m-0">Rp. </span>';
         cart+= '<input type="hidden" name="buy_price['+i+']" id="buy_price_'+i+'" value="'+buy_price+'">';
         cart+= '<span class="text-bold text-olive uang py-0 m-0" >'+buy_price+'</span>';
         cart+= '<span class="text-bold text-olive py-0 m-0">,00</span>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '<div class="col-4 float-right">';
         cart+= '<div class="row float-right">';
         cart+= '<div class="col-12 ">';
         cart+= '<div class="d-flex justify-content-around align-items-stretch">';
         cart+= '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty('+i+',\'minus\')" data-id="'+i+'"> <i class="fas fa-minus-circle"></i></button>';
         cart+= '<input class="form-control form-control-sm col-sm-5 col-md-5" id="quantity'+i+'" name="quantity['+i+']" value="0" readonly required>';
         cart+= '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty('+i+',\'plus\')" data-id="'+i+'"> <i class="fas fa-plus-circle"></i></button>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '<hr class="py-1 my-2">';
         cart+= '<div class="row align-items-center justify-content-between">';
         cart+= '<div class="col-sm-1 col-md-1">';
         cart+= '<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCart(\'card-cart-'+i+'\')"> <i class="fas fa-trash"></i></button>';
         cart+= '</div>';
         cart+= '<div class="col-sm-5 col-md-5">';
         cart+= '<input type="text" class="form-control form-control-sm ml-1"';
         cart+= 'name="catatan['+i+']" id="catatan_'+i+'" placeholder="Catatan">';
         cart+= '</div>';
         cart+= '<div class="col-md-6 col-sm-6 align-items-center ">';
         cart+= '<span class="h6 text-bold text-olive float-right pl-1" id="sub_total_view'+i+'">&nbsp;Rp. 0,00</span>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '</div>';
         cart+= '</div></div>';
         if(existCart(i,code)){
            let exist = $("[data-id="+code+"]")[0].id
            let id= parseInt(exist.substr(exist.length - 1));
            adjQty(id,'plus')
         }else{
            $('#listCart').append(cart);
         }



   }
   function existCart(dataLength,code){
      const listId = [];

      for (let index = 0; index <= dataLength; index++) {
         let lastClass = $("#listCart .card-cart-"+index).attr("data-id");
         if(lastClass !== undefined){
            listId[index] = lastClass
         }
      }
      let existArray = listId.includes(code);
      return existArray;
      
   }
   function removeCart(selector){
      $('#'+selector).remove();
      calcGrand()
   }
   function adjQty(id,type){
      let qty =  parseInt($('#quantity'+id).val())
      switch (type) {
         case 'minus':
            if(qty>0){
               $('#quantity'+id).val(qty-1)
               calcSum(id)
            }
            break;
         case 'plus':
            $('#quantity'+id).val(qty+1)
            calcSum(id)
            break;
        
      }
   }
   
   function calcGrand(){
      let subGrandTotal = parseInt(0);
      let lastClass = $('#listCart .card-cart:last ').attr('id')
      let i= 0 
         if(lastClass === undefined){
            i=1
         }else{
            i= parseInt(lastClass.substr(lastClass.length - 1))+1;
         }
      for (let index = 0; index <= i; index++) {
         let checkNAN =parseInt($('#sub_total_'+index).val())
         let sub_total = checkNAN ? checkNAN : 0;

         subGrandTotal = subGrandTotal+sub_total
      }
      let disc = $('#discount').val()/100;
      let tax = $('#tax').val()/100;
      let summary = subGrandTotal-(subGrandTotal*disc)
      let sumTax = summary+(summary*tax)
      $('#sub_grand_total').val(subGrandTotal);
  
      $('#total').val(sumTax);
      maskRupiah("#discount_view",subGrandTotal*disc)
      maskRupiah("#tax_view",summary*tax)
      maskRupiah("#sub_grand_total_view",subGrandTotal)
      maskRupiah("#total_view",sumTax)
   }

   function setTax(value){
      let tax = $('#tax').val(value);
      calcGrand()
   }

   function setTax(value){
      let tax = $('#tax').val(value);
      calcGrand()
   }

   function calcSum(id){
      let qty = $("input#quantity"+id).val();
      let buy_price = $("input#buy_price_"+id).unmask().val();
      let sub_total = qty*buy_price;
      $("#sub_total_"+id).val(sub_total);
      if(qty==0){
         $("#sub_total_view"+id).text('Rp. 0,00');
      }else{
         maskRupiah("#sub_total_view"+id,sub_total)
      }
      calcGrand()
   }
 

   $(document).ready(function () {
      getDataSupplier();
      getItemSearch();
      calcGrand()
   });
</script>

@endsection