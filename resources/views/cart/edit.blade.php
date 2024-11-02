@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3">
                    <div class="card-body">
                        <main>
                            <div id="reader"></div>
                            <div id="result"></div>
                        </main>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Input</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <section class="col-xs-6 col-sm-6 col-md-6">
                                <input type="hidden" id="docnum" name="docnum" value="{{ ($cart) ? $cart->docnum : 0 }}">
                                <div class="form-group">
                                    <label>Kode Item<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="kode-item" name="kode-item">
                                        <span class="input-group-append">
                                          <button type="button" class="btn btn-info btn-flat" onclick="getItem()"><i class="fa fa-fw fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <input type="text" class="form-control" id="deskripsi" name="deskripsi" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Sales<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control" id="sales" name="sales" value="{{ Auth::user()->full_name }}" data-id ="{{ Auth::user()->id }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Business Partner<sup class="text-danger">*</sup></label>
                                    <select class="form-control form-control-sm select2" name="custcode" id="custcode">
                                        <option value="" disabled selected hidden>Choose</option>
                                        @foreach($customer AS $cust)
                                            <option value="{{ $cust->cardcode }}" {{ (($cart) ? (($cart->bussiness_partner == $cust->cardcode) ? "selected" : "") : "") }}>{{ (($cust->phoneCode) ? $cust->phoneCode : '0000').' | '.$cust->cardname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>
                            <section class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label>Qty</label>
                                    <input type="text" class="form-control" id="qty" name="qty" value="0" onchange="calcSum()">
                                </div>
                                <div class="form-group">
                                    <label>Pricelist<sup class="text-danger">*</sup></label>
                                    <select class="form-control form-control-sm select2" name="pricelist" id="pricelist" onchange="getPrice(this.value)">
                                        <option value="" disabled selected hidden>Choose</option>
                                        @foreach($pricelist AS $list)
                                            <option value="{{ $list->listnum }}">{{ $list->listname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Price<sup class="text-danger">*</sup></label>
                                    <input type="hidden" class="form-control" id="price" name="price" readonly>
                                    <input type="text" class="form-control text-right" id="price_show" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Total<sup class="text-danger">*</sup></label>
                                    <input type="hidden" class="form-control" id="total" name="total" readonly>
                                    <input type="text" class="form-control text-right" id="total_show" readonly>
                                </div>
                            </section>
                            <section class="col-xs-12 col-sm-12 col-md-12">
                                <button class="btn btn-primary btn-flat float-right" onclick="addCart()">Add</button>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Barang</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered" id="table-details">
                            <thead>
                                <tr>
                                    <th>Kode Item</th>
                                    <th>Nama Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="bodyTable">
                                @if($cart_details)
                                    @foreach($cart_details AS $cd)
                                        <tr>
                                            <td class="kode_item">{{ $cd->itemcode }}</td>
                                            <td>{{ $cd->itemname }}</td>
                                            <td class="text-center">{{ $cd->qty }}</td>
                                            <td class="text-right">{{ Helper::formatNumber($cd->price, 'rupiah') }}</td>
                                            <td class="text-right">{{ Helper::formatNumber($cd->subtotal, 'rupiah') }}</td>
                                            <td class="text-center">
                                                <button type="button" name="remove" id="DeleteButton" class="btn btn-sm btn-danger btn_remove"><i class="fa fa-fw fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-flat float-right" onclick="saveCart()">Commit Data Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('jScript')
<script>

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const html5QrCode = new Html5Qrcode("reader");
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        document.getElementById('result').innerHTML =  `
        <h2>Success!</h2>
        <p><a href="${decodedText}">${decodedText}</a></p>
        `;
        html5QrCode.clear();
        document.getElementById('reader').remove()
    };

    const config = { fps: 15, qrbox: { width: 420, height: 420 } };
    
    //If you want to prefer back camera
    //html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

    html5QrCode.start({ facingMode: "environment" }, config,
        (decodedText, decodedResult) => {
            console.log(decodedText)
            if(decodedText){
                $("#kode-item").val(decodedText);
                getItem();
            }
        },
        (errorMessage) => {
            // parse error, ignore it.
        }
    ).catch((err) => {
        console.log(err);
    });  

    function getItem(){
        let itemcode = $("#kode-item").val();

        $("#deskripsi").val("");
        $("#qty").val(0);
        $("#price").val(0);
        $("#total").val(0);

        if(itemcode){
            let qty = $("#qty").val();
            $.ajax({
                type:'POST',
                url:"{{ url('dataInduk/items/getDetails') }}",
                data:{
                    itemcode:itemcode
                },
                beforeSend: function() {
                    doBeforeSend(true)
                },
                success:function(data){
                    console.log(data)
                    if(data.status == 'success'){
                        $("#deskripsi").val(data.data.itemname);

                        $("#pricelist").val(data.data.pricelist).trigger('change');
                        $("#price").val(data.data.price);
                        $("#price_show").val(maskRupiah("", data.data.price));

                        calcSum();
                        doBeforeSend(false)
                    }
                }
            });
        } else {
            alert("Masukkan kode item terlebih dahulu");
        }
    }  
    

    function getPrice(pricelistval){
        let itemcode = $("#kode-item").val();

        $.ajax({
            type:'POST',
            url:"{{ url('sales/cart/detailPricelist') }}",
            data:{
                itemcode:itemcode,
                listnum:pricelistval
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success:function(data){
                console.log(data)
                if(data.status == 'success'){
                    $("#price").val(data.data.price);
                    $("#price_show").val(maskRupiah("", data.data.price));

                    calcSum();
                    doBeforeSend(false)
                }
            }
        });
    }

    function calcSum(){
        var qty = $("#qty").val();
        var price = $("#price").val();

        var total = qty * price;
        $("#total").val(total);
        $("#total_show").val(maskRupiah("", total));
    }

    $("#table-details").on("click", "#DeleteButton", function() {
        var itemcode = $(this).closest('tr').find('.kode_item').text();
        let docnum = $("#docnum").val();

        $.ajax({
            type:'POST',
            url:"{{ url('sales/cart/delete') }}",
            data:{
                docnum:docnum,
                itemcode:itemcode
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success:function(data){
                alert(data.message)
                doBeforeSend(false)
            }
        });
        $(this).closest("tr").remove();
    });

    function addCart(){
        let docnum = $("#docnum").val();
        let itemcode = $("#kode-item").val();
        let itemname = $("#deskripsi").val();
        var qty = $("#qty").val();
        var price = $("#price").val();
        var subtotal = $("#total").val();
        var pricelist = $("#pricelist").find(":selected").val();
        var custcode = $("#custcode").find(":selected").val();
        var sales = $("#sales").data("id");

        $.ajax({
            type:'POST',
            url:"{{ url('sales/cart/update') }}",
            data:{
                docnum      : docnum,
                itemcode    : itemcode,
                itemname    : itemname,
                qty         : qty,
                price       : price,
                subtotal    : subtotal,
                pricelist   : pricelist,
                sales       : sales,
                custcode    : custcode
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success:function(data){
                console.log(data)
                if(data.status == "success"){
                    if(docnum == 0){
                        $("#docnum").val(data.docnum);
                    }
                    $("#bodyTable").append(`
                        <tr>
                            <td class="kode_item">`+itemcode+`</td>
                            <td>`+itemname+`</td>
                            <td class="text-center">`+qty+`</td>
                            <td class="text-right">`+maskRupiah("", price)+`</td>
                            <td class="text-right">`+maskRupiah("", subtotal)+`</td>
                            <td class="text-center"><button type="button" name="remove" id="DeleteButton" class="btn btn-sm btn-danger btn_remove"><i class="fa fa-fw fa-trash"></i></button></td>
                        </tr>
                    `);

                    $("#kode-item").val("");
                    $("#deskripsi").val("");
                    $("#qty").val(0);
                    $("#price").val(0);
                    $("#price_show").val(maskRupiah("", 0));
                    $("#total").val(0);
                    $("#total_show").val(maskRupiah("", 0));
                    $("#pricelist").select2().val("").trigger("change");

                }
                doBeforeSend(false)
            }
        });
    }

    function saveCart() {
        let docnum = $("#docnum").val();
        var custcode = $("#custcode").find(":selected").val();

        $.ajax({
            type:'POST',
            url:"{{ url('sales/cart/commit') }}",
            data:{
                docnum:docnum,
                custcode:custcode
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success:function(data){
                if(data.status == 'success'){
                    alert(data.message);
                    window.location.href = '{{ url("sales/cart") }}';
                    // location.reload();
                }
            }
        });
    }
</script>
@endsection