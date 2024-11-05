@extends('layouts.app')

@section('content')
@php
    $url = parse_url($_SERVER['REQUEST_URI']);
    $queryParam = "";
    if(isset($url['query'])){
        $queryParam = $url['query'];
    }
@endphp
<div class="content-wrapper">
    <section class="content">
        <form id='SalesForm'>
            @csrf
            <div class="row p-1 m-1">
                <section class="col-7 col-md-7 col-sm-7 p-1">
                    <div class="card m-1" style="height: 100%">
                        <div class="card-body">
                            <div class="row">
                                <section class="col-sm-6">
                                    <input type="hidden" id="docnum" name="docnum" value="{{ ($cart) ? $cart->docnum : 0 }}">
                                    <div class="form-group">
                                        <label for="membership_code"><sup class="text-red">*</sup>Business Partner Code</label>
                                        <select class="form-control form-control-sm select2" name="custcode" id="custcode" onchange="setBusinessPartner(this.value)">
                                            <option value="" disabled selected hidden>Choose</option>
                                            @foreach($customer AS $cust)
                                                <option value="{{ $cust->cardcode }}" {{ (($cart) ? (($cart->bussiness_partner == $cust->cardcode) ? "selected" : "") : "") }}>{{ (($cust->phoneCode) ? $cust->phoneCode : '0000').' | '.$cust->cardname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code"><sup class="text-red">*</sup>Business Partner Name</label>
                                        <input type="text" class="form-control form-control-sm" name="custname" id="custname" placeholder="Business Partner Name" value="{{ (($cart) ? $cart->cardname : "") }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code"><sup class="text-red">*</sup>Telephone Number</label>
                                        <input type="text" class="form-control form-control-sm" name="custphone" id="custphone" placeholder="Telephone Number" value="{{ (($cart) ? $cart->phone : "") }}" readonly>
                                    </div>
                                </section>
                                <section class="col-sm-6">
                                    <div class="form-group">
                                        <label for="membership_code">Sales</label>
                                        <input type="text" class="form-control form-control-sm" name="sales" id="sales" placeholder="Sales" value="{{ Auth::user()->full_name }}" data-id="{{ Auth::user()->id }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code">Checker</label>
                                        <input type="text" class="form-control form-control-sm" name="checker" id="checker" placeholder="Checker" readonly>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="membership_code">Discount(%)</label>
                                                <input type="text" class="form-control form-control-sm" onchange="calcGrand()" value="0" name="discount" id="discount" placeholder="Discount">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="membership_code">Tax(%)</label><br>
                                                {{-- <input type="checkbox" name="tax" class="switchBs" id="tax" value="1" checked data-bootstrap-switch data-off-color="secondary" data-on-text="ON" data-off-text="OFF" data-on-color="success"> --}}
                                                <input type="text" class="form-control form-control-sm" onchange="calcGrand()" value="0" name="tax" id="tax" placeholder="Tax">
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="col-5 col-md-5 col-sm-5 p-1">
                    <div class="card m-1" style="height: 100%">
                        <div class="card-body m-0 p-2 align-items-center justify-content-center">
                            <div class="row">
                                <div class="col align-self-center">
                                    <input type="hidden" name="total" id="total" value="{{ (($cart) ? $cart->grandtotal : 0) }}">
                                    <span class="h6 m-0 float-left text-secondary">
                                        #{{ Helper::docPrefix('sales') }}
                                    </span>
                                </div>
                                <div class="col align-self-center">
                                    <span class="h4 m-0 float-right text-olive" id="total_view">
                                        {{ (($cart) ? Helper::formatNumber($cart->grandtotal, 'rupiah') : Helper::formatNumber('0', 'rupiah')) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col align-self-center">
                                    <input type="hidden" name="sub_grand_total" id="sub_grand_total" value="{{ (($cart) ? $cart->grandtotal : 0) }}">
                                    {{-- <input type="text" name="payment_charge" id="payment_charge" value="0"> --}}
                                    <span class="h5 m-0 float-left text-secondary">Sub Total</span>
                                </div>
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-right text-secondary" id="sub_grand_total_view">
                                        {{ (($cart) ? Helper::formatNumber($cart->grandtotal, 'rupiah') : Helper::formatNumber('0', 'rupiah')) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-left text-secondary">Diskon</span>
                                </div>
                                <div class="col align-self-center">
                                    <input type="hidden" name="grand_discount" id="grand_discount" value="0">
                                    <span class="h5 m-0 float-right text-secondary" id="grand_discount_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-left text-secondary">Pajak</span>
                                </div>
                                <div class="col align-self-center">
                                    <input type="hidden" name="grand_tax" id="grand_tax" value="0">
                                    <span class="h5 m-0 float-right text-secondary" id="grand_tax_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer m-0 p-2">
                            <div class="row">
                                <div class="col-12">
                                    <div id="pending">
                                        <button type="button" onclick="saveData('bayar')" class="btn btn-flat btn-block btn-outline bg-maroon">Bayar</button>
                                    </div>
                                    <div id="pay">
                                        <button type="button" onclick="saveData('simpan')" style="display: none" class="btn btn-flat btn-block bg-teal">Simpan</button>
                                    </div>
                                    <div id="updateSales" style="display: none">
                                        <button type="button" onclick="updateData('simpan')" class="btn btn-flat btn-block bg-teal">Ubah</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="col-12 col-md-12 col-sm-12 p-1">
                    <div class="card m-1">
                        <div class="card-body">
                            <button type="button" class="btn btn-flat bg-cyan" onclick="selectItem()">+ Add Items</button>
                            @if($cart_details)
                                @php
                                    $parameter =[
                                        'userId' => Auth::user()->id,
                                        'cartNumber' => ($cart) ? $cart->docnum : ''
                                    ];
                                    $enkripsi= Crypt::encrypt($parameter);
                                @endphp
                                <a href="http://127.0.0.1:8002/auth/loginBySwitch?param={{ $enkripsi }}" class="btn btn-flat btn-outline-warning" target="_blank"><span class="fas fa-exchange-alt"></span> Switch</a>
                                {{-- <button type="button" class="btn btn-flat btn-outline-info" onclick="selectItem()"></button> --}}
                            @endif
                            <div class="row gx-3">
                                <div class="scrollYMenu col-12 col-sm-12 col-xl-12" style="max-height: 35vh;">
                                    <table class="table table-sm mt-2">
                                        <thead>
                                            <tr>
                                                <th width="24%">Item Info</th>
                                                <th>Token Remarks</th>
                                                <th width="5.7%">Qty</th>
                                                <th width="13%">Price List</th>
                                                <th>Price</th>
                                                <th width="5.7%">Disc. 1(%)</th>
                                                <th width="5.7%">Disc. 2(%)</th>
                                                <th width="5.7%">Disc. 3(%)</th>
                                                <th>Subtotal</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-list">
                                            @if($cart_details)
                                                @foreach($cart_details AS $key => $value)
                                                <tr id="row-item-detail{{ $key+1 }}" data-id="{{ $key+1 }}">
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="hidden" id="itemcode{{ $key+1 }}" name="itemcode[]" value="{{ $value->itemcode }}">
                                                        <dt>{{ $value->itemcode }}</dt>
                                                        <dd style="width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $value->itemname }}</dd>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="text" name="token_remarks[]">
                                                    </td>
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="number" name="qty[]" id="qty{{ $key+1 }}" onchange="calcSum({{ $key+1 }})" value="{{ $value->qty }}">
                                                    </td>
                                                    <td class="align-middle">
                                                        <select class="form-control form-control-sm" name="price_list[]" id="price_list{{ $key+1 }}" onchange="priceList('{{ $value->itemcode }}', this.value, {{ $key+1 }})" style="appearance: none;">
                                                            <option value="" disabled selected hidden>Choose</option>
                                                            @foreach($pricelist AS $pricenum)
                                                                <option value="{{ $pricenum->listnum }}" {{ (($value->pricelist_id == $pricenum->listnum) ? "selected" : "") }}>{{ $pricenum->listname }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="hidden" name="price[]" id="price{{ $key+1 }}" value="{{ $value->price }}" readonly>
                                                        <span id="price_show{{ $key+1 }}">{{ Helper::formatNumber($value->price, 'rupiah') }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="number" name="disc1[]" id="disc1{{ $key+1 }}" onchange="calcSum({{ $key+1 }})" value="0">
                                                    </td>
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="number" name="disc2[]" id="disc2{{ $key+1 }}" onchange="calcSum({{ $key+1 }})" value="0" readonly>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input class="form-control form-control-sm" type="number" name="disc3[]" id="disc3{{ $key+1 }}" onchange="calcSum({{ $key+1 }})" value="0" readonly>
                                                    </td>
                                                    <td class="align-middle text-right">
                                                        <input class="form-control form-control-sm" type="hidden" name="subtotal[]" id="subtotal{{ $key+1 }}" value="{{ $value->subtotal }}" readonly>
                                                        <span id="subtotal_show{{ $key+1 }}">{{ Helper::formatNumber($value->subtotal, 'rupiah') }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <button type="button" name="remove" id="{{ $key+1 }}" class="btn btn-sm btn-danger btn_remove"><span class="fas fa-trash"></span></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    </section>
</div>

{{-- Modal Item --}}
<div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <select class="form-control select2" name="itemcode" id="itemcode" style="width: 100%;" onchange="setItem(this.value, $('option:selected',this).data('id'))">
                    <option value="">Choose Item</option>
                    @foreach($items AS $itm)
                        <option value="{{ $itm->itemcode }}" data-id="{{ $itm->itemname }}">{{ $itm->itemcode.' | '.$itm->itemname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="row align-items-center" style="max-height: 280px; min-height: 280px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="item-details-price" style="text-align:center;">
                        Mohon Pilih Item Terlebih Dahulu!
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-flat btn-danger" data-dismiss="modal">Cancel</button>
                <button class="btn btn-flat btn-primary" id="button-add-items" onclick="addItem()">Add</button>
            </div>
        </div>
    </div>
</div>
{{-- ./Modal Item --}}

@endsection
@section('jScript')
<script>

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const setBusinessPartner = (cardcode) => {
        let href = '{{ route("master.customer.bycode") }}';
        $.ajax({
            url: href,
            method: "POST",
            data: {
                cust_code:  cardcode
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                $("#custname").val(result.cust_details.cardname);
                $("#custphone").val((result.cust_details.phone) ? result.cust_details.phone : '0000');
            },
            error: function(jqXHR, testStatus, error) {
                popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                doBeforeSend(false)
            },
            complete: function() {
                // selesai
                doBeforeSend(false)
            },
            timeout: 8000,
        });        
    }

    const selectItem = () => {
        $('#modalBlade').modal("show");
        let qty_modal = $("#qty_modal").val();
        if(qty_modal == "" || qty_modal <= 0 || qty_modal == undefined){
            $("#button-add-items").prop('disabled', true);
        } else {
            $("#button-add-items").prop('disabled', false);            
        }
    }    

    const setItem = (itemcode, itemname) => {
        $("#modalBody").html(`
            <div class="row align-items-center" style="max-height: 280px; min-height: 280px;">
                <div class="col-sm-12 col-md-12 col-lg-12" id="item-details-price">
                    <div class="row align-items-center">
                        <section class="col-sm-12 col-md-5 col-lg-5 text-center">
                            <img src="{{ asset('dist/img/kopi.png') }}" alt="" style="max-width: 16rem;">
                        </section>
                        <section class="col-sm-12 col-md-7 col-lg-7">
                            <dl>
                                <input type="hidden" id="itemcode_modal" value="`+itemcode+`">
                                <input type="hidden" id="itemname_modal" value="`+itemname+`">
                                <dt>`+itemcode+`</dt>
                                <dd>`+itemname+`</dd>
                            </dl>
                            <div class="item-group mb-3">
                                <label for="qty_modal">Qty</label>
                                <input class="form-control" type="number" id="qty_modal" value="0" onchange="itemPricing('`+itemcode+`', this.value, '')" style="width: 100px;">
                            </div>
                            <div class="item-group mb-3">
                                <label for="pricelist_modal">Price List</label>
                                <select class="form-control form-control-sm" name="pricelist_modal" id="pricelist_modal" onchange="itemPricing('`+itemcode+`', $('#qty').val(), this.value)" style="appearance: none;">
                                </select>
                            </div>
                            <dl>
                                <dt>Price</dt>
                                <dd style="font-size: 1.3rem; font-weight: bold;" id="price_modal"></dd>
                                <input class="form-control" type="hidden" id="price_modal_input" value="0" style="width: 100px;">
                            </dl>
                        </section>
                    </div>
                </div>
            </div>
        `);
    }

    const itemPricing = (itemcode, qty, pricelist) => {
        let href = '{{ route("master.item.pricing") }}';
        $.ajax({
            url: href,
            method: "POST",
            data: {
                itemcode:  itemcode,
                qty:  qty,
                pricelist: pricelist
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                console.log(result);
                let qty_modal = $("#qty_modal").val();

                if(result.itemprice){
                    $("#price_modal").html(maskRupiah("", result.itemprice.price));
                    $("#price_modal_input").val(result.itemprice.price);

                    $('#pricelist_modal').html("");
                    $.each(result.pricelist, function (i, elem) {
                        $('#pricelist_modal').append(`<option value="`+elem.listnum+`" `+ ((result.itemprice.pricelist == elem.listnum) ? `selected` : ``) +`>`+elem.listname+`</option>`);
                    });

                    if(qty_modal == "" || qty_modal <= 0){
                        $("#button-add-items").prop('disabled', true);
                    } else {
                        $("#button-add-items").prop('disabled', false);            
                    }
                } else {
                    $("#price_modal").html(maskRupiah("", 0));
                    $("#price_modal_input").val(0);                    
                }
            },
            error: function(jqXHR, testStatus, error) {
                popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                doBeforeSend(false)
            },
            complete: function() {
                // selesai
                doBeforeSend(false)
            },
            timeout: 8000,
        });
    }

    const addItem = () => {
        let docnum = $("#docnum").val();
        let itemcode = $('#itemcode_modal').val();
        let itemname = $('#itemname_modal').val();
        let pricelist = "{{ $pricelist }}";
        let pricelist_modal = $('#pricelist_modal').val();
        let price = $('#price_modal_input').val();
        let qty = $('#qty_modal').val();
        var custcode = $("#custcode").find(":selected").val();
        var sales = $("#sales").data("id");

        var rowCount = $('#item-list tr').length;
        var i = rowCount + 1;
        
        var output = [];
        $.each(JSON.parse(pricelist.replace(/&quot;/g,'"')), function(key, value){
            output.push('<option value="'+ value.listnum +'" '+((value.listnum == pricelist_modal) ? "selected":"")+'>'+ value.listname +'</option>');
        });

        $.ajax({
            type:'POST',
            url:"{{ url('sales/cart/store') }}",
            data:{
                docnum      : docnum,
                itemcode    : itemcode,
                itemname    : itemname,
                qty         : qty,
                price       : price,
                subtotal    : qty*price,
                pricelist   : pricelist_modal,
                sales       : sales,
                custcode    : custcode,
                stage       : '1'
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success:function(data){
                console.log(data)
                if(data.status == "success"){
                    
                    $('#item-list').append(`
                        <tr id="row-item-detail`+i+`" data-id="`+i+`">
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="hidden" id="itemcode`+i+`" name="itemcode[]" value="`+itemcode+`">
                                <dt>`+itemcode+`</dt>
                                <dd style="width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">`+itemname+`</dd>
                            </td>
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="text" name="token_remarks[]">
                            </td>
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="number" name="qty[]" id="qty`+i+`" onchange="calcSum(`+i+`)" value="`+qty+`">
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm" name="price_list[]" id="price_list`+i+`" onchange="priceList('`+itemcode+`', this.value, `+i+`)" style="appearance: none;">
                                    `+output.join('')+`
                                </select>
                            </td>
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="hidden" name="price[]" id="price`+i+`" value="`+price+`" readonly>
                                <span id="price_show`+i+`">`+maskRupiah("", price)+`</span>
                            </td>
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="number" name="disc1[]" id="disc1`+i+`" onchange="calcSum(`+i+`)" value="0">
                            </td>
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="number" name="disc2[]" id="disc2`+i+`" onchange="calcSum(`+i+`)" value="0" readonly>
                            </td>
                            <td class="align-middle">
                                <input class="form-control form-control-sm" type="number" name="disc3[]" id="disc3`+i+`" onchange="calcSum(`+i+`)" value="0" readonly>
                            </td>
                            <td class="align-middle text-right">
                                <input class="form-control form-control-sm" type="hidden" name="subtotal[]" id="subtotal`+i+`" value="0" readonly>
                                <span id="subtotal_show`+i+`"></span>
                            </td>
                            <td class="align-middle">
                                <button type="button" name="remove" id="`+i+`" class="btn btn-sm btn-danger btn_remove"><span class="fas fa-trash"></span></button>
                            </td>
                        </tr>
                    `);

                    if(docnum == 0){
                        $("#docnum").val(data.docnum);
                        window.location.href = '{{ url("sales/create?cartCode=") }}'+data.docnum;
                    }

                    calcSum(i)
                }
                doBeforeSend(false)
            }
        });

        $('#modalBlade').modal("hide");
        $("#item-details-price").html("");
        $('#itemcode').val('').select2('destroy').select2();
    }

    $(document).on('click', '.btn_remove', function(){
        var button_id = $(this).attr("id");
        $('#row-item-detail'+button_id+'').remove();

        calcGrand();
    });

    const priceList = (itemcode, pricelist, index) => {
        let href = '{{ route("master.item.pricing") }}';
        $.ajax({
            url: href,
            method: "POST",
            data: {
                itemcode:  itemcode,
                qty:  0,
                pricelist: pricelist
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                console.log(result);
                if(result.itemprice){
                    $("#price_show"+index).html(maskRupiah("", result.itemprice.price));
                    $("#price"+index).val(result.itemprice.price);
                } else {
                    $("#price_show"+index).html(maskRupiah("", 0));
                    $("#price"+index).val(0);                    
                }
                calcSum(index)
            },
            error: function(jqXHR, testStatus, error) {
                popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                doBeforeSend(false)
            },
            complete: function() {
                // selesai
                doBeforeSend(false)
            },
            timeout: 8000,
        });
    }

    const calcSum = (i) => {
        let qty = $("input#qty"+i).val();
        let price = $("input#price"+i).val();
    
        $("input#subtotal"+i).val(parseInt(qty)*parseInt(price));

        let subtotal = $("input#subtotal"+i).val();
        let disc1 = $("input#disc1"+i).val();
        let disc2 = $("input#disc2"+i).val();
        let disc3 = $("input#disc3"+i).val();

        if(disc1 != 0){
            $("input#disc2"+i).attr("readonly", false);
            let disc = disc1/100 * subtotal;

            subtotal = subtotal - disc;
        } else{
            $("input#disc2"+i).val(0);
            $("input#disc2"+i).attr("readonly", true);
            $("input#disc3"+i).val(0);
            $("input#disc3"+i).attr("readonly", true);
        }

        if(disc2 != 0) {
            $("input#disc3"+i).attr("readonly", false);
            let disc = disc2/100 * subtotal;

            subtotal = subtotal - disc;
        } else{
            $("input#disc3"+i).val(0);
            $("input#disc3"+i).attr("readonly", true);
        }

        if(disc3 != 0) {
            $("input#disc3"+i).attr("readonly", false);
            let disc = disc3/100 * subtotal;

            subtotal = subtotal - disc;
        }

        $("input#subtotal"+i).val(subtotal);
        $("#subtotal_show"+i).html(maskRupiah("", subtotal));
        calcGrand();
    }

    const calcGrand = () => {
        let subGrandTotalPrice = parseInt(0);
        let grandTotalPrice = parseInt(0);
        let grandDiscount = 0
        let grandTax = 0;
        let Discount = $("input#discount").val();
        let Tax = $("input#tax").val();

        $('table > #item-list > tr').each(function() { 
            let rowIndex = $(this).data("id");
            let subtotal = $("input#subtotal"+rowIndex).val();

            subGrandTotalPrice = parseInt(subGrandTotalPrice) + parseInt(subtotal);
        });

        $("#sub_grand_total").val(subGrandTotalPrice);
        maskRupiah("#sub_grand_total_view", subGrandTotalPrice);

        grandDiscount = subGrandTotalPrice * Discount/100;
        $("#grand_discount").val(grandDiscount);
        maskRupiah("#grand_discount_view", grandDiscount);

        grandTax = (subGrandTotalPrice-grandDiscount) * Tax/100;
        $("#grand_tax").val(grandTax);
        maskRupiah("#grand_tax_view", grandTax);

        grandTotalPrice = parseInt(subGrandTotalPrice) - parseInt(grandDiscount) + parseInt(grandTax);
        $("#total").val(grandTotalPrice);
        maskRupiah("#total_view", grandTotalPrice);
    }

    const paymentMethod = () => {
        let href = '{{ route("sales.paymentMethod") }}';
        $.ajax({
            url: href,
            beforeSend: function() {
                doBeforeSend(true)
            },
            // return the result
            success: function(result) {
                let htmlOption = "";
                $.each(result.paymentMethod, function(key, value) {
                    payM = `'`+value.type_payment+`'`;
                    htmlOption += `
                        <button type="button" class="btn bg-lime btn-sm btn-block button-mthd" id=`+payM.replace(/\s/g, '')+` onclick="setPay(`+payM.replace(/\s/g, '')+`,`+payM+`,'')" style="padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781">
                            <b>`+value.type_payment+`</b>
                        </button>
                    `;
                });
                
                $("#payment_method").html(htmlOption);
            },
            complete: function() {
                doBeforeSend(false)
            },
            error: function(jqXHR, testStatus, error) {
                popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');
                doBeforeSend(false)
            },
            timeout: 8000
        })
    };

    const setPay = (e, f, g) => {
        
        $(".button-mthd").attr('style', 'padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');
        $("#"+e).attr('style', 'padding: 25px;height: 15vh;color: white !important;background-color: #2e5781 !important;');

        let total = formatNumber($("#total").val());
        let href = '{{ route("sales.paymentMethod.details") }}';
        switch (e) {
            case "Cash":
                // $("#payment").html("");
                // $("#payment").html(`
                //     <div class="row">
                //         <input type="hidden" class="form-control" id="operator" value="+">
                //         <div class="col-sm-2 col-md-2">
                //             <button type="button" class="btn btn-success btn-block btn-xs" id="plusOperator" onclick="setOperator('+')" style="font-size: 20px !important;">+</button>
                //             <button type="button" class="btn btn-outline-success btn-block btn-xs" id="minOperator" onclick="setOperator('-')" style="font-size: 20px !important;">-</button>
                //         </div>
                //         <div class="col-sm-10 col-md-10">
                //             <div class="row justify-content-md-center">
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(500)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">500</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(10000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">10.000</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(1000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">1.000</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(20000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">20.000</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(2000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">2.000</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(50000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">50.000</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(5000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">5.000</button>
                //                 </div>
                //                 <div class="col-sm-6 col-md-6">
                //                     <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(100000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">100.000</button>
                //                 </div>
                //             </div>
                //         </div>
                //     </div>
                // `);

                let rowIndexPayment = $("#table-payments tbody tr").length;

                $("#table-payments tbody").append(`
                    <tr style="background-color: aliceblue;">
                        <td class="align-middle" style="text-align: left;">
                            <span>Cash</span><br>
                            <small><b>Cash</b></small>
                            <input type="hidden" name="payment_method[]" value="Cash">
                            <input type="hidden" name="payment_method_details[]" value="Cash">
                            <input type="hidden" name="payment_charge[]" value="0">
                        </td>
                        <td class="align-middle">0</td>
                        <td><input class="form-control text-right" id="detail_nominal`+(rowIndexPayment+1)+`" name="detail_nominal[]" value="0" style="border:none;" onchange="paymentSum()"></td>
                        <td class="align-middle" style="width: 4%;">
                            <button class="btn btn-flat btn-outline bg-red btn-sm" onclick="$(this).closest('tr').remove(); paymentSum();"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `);

                break;
            case "CreditCard":
                $.ajax({
                    url: href,
                    method: "POST",
                    data: {
                        payCode: f
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        console.log(result);
                        let htmlOption = "";
                        $.each(result.paymentMethodDetails, function(key, value) {
                            payM = `'`+value.type_payment+`'`;
                            htmlOption += `
                                <div class="col-12 col-sm-4 col-md-3 col-lg-3 my-1">
                                    <button class="btn bg-light btn-sm btn-block btn-payment" id="`+value.type_payment.replace(/\s/g, '')+``+value.nm_payment.replace(/\s/g, '')+`" onclick="paymentSet(`+payM+`,'`+value.nm_payment+`', '`+value.charge+`', this.id)" style="min-height: 100px; max-height: 100px;">
                                        <img src="{{ asset('dist/img/bank/`+value.image_details+`') }}" alt="" style="max-width: 100px;">
                                    </button>
                                </div>
                            `;
                        });
                        $("#payment").html(`
                            <div class="row d-flex justify-content-arround">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 my-1">
                                    <div class="form-group">
                                        <label for="card_number">Card Number</label>
                                        <input class="form-control text-center" id="card_number" placeholder="0000 0000 0000 0000">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex align-item-center justify-content-arround">
                                `+htmlOption+`
                            </div>
                        `);
                        var cards = [{ "mask": "#### #### #### ####"}, { "mask": "#### #### #### ####"}];
                        $('#card_number').inputmask({ 
                            mask: cards, 
                            greedy: false, 
                            definitions: { '#': { validator: "[0-9]", cardinality: 1}} 
                        });
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                        doBeforeSend(false)
                    },
                    complete: function() {
                        // selesai
                        doBeforeSend(false)
                    },
                    timeout: 8000,
                });
                break;
            case "DebitCard":
                $.ajax({
                    url: href,
                    method: "POST",
                    data: {
                        payCode: f
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        console.log(result);
                        let htmlOption = "";
                        $.each(result.paymentMethodDetails, function(key, value) {
                            payM = `'`+value.type_payment+`'`;
                            htmlOption += `
                                <div class="col-12 col-sm-4 col-md-3 col-lg-3 my-1">
                                    <button class="btn bg-light btn-sm btn-block btn-payment" id="`+value.type_payment+``+value.nm_payment+`" onclick="paymentSet(`+payM+`,'`+value.nm_payment+`', '`+value.charge+`', this.id)" style="min-height: 100px; max-height: 100px;">
                                        <img src="{{ asset('dist/img/bank/`+value.image_details+`') }}" alt="" style="max-width: 100px;">
                                    </button>
                                </div>
                            `;
                        });
                        $("#payment").html(`
                            <div class="row d-flex justify-content-arround">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 my-1">
                                    <div class="form-group">
                                        <label for="card_number">Card Number</label>
                                        <input class="form-control text-center" id="card_number" placeholder="0000 0000 0000 0000">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex align-item-center justify-content-arround">
                                `+htmlOption+`
                            </div>
                        `);
                        var cards = [{ "mask": "#### #### #### ####"}, { "mask": "#### #### #### ####"}];
                        $('#card_number').inputmask({ 
                            mask: cards, 
                            greedy: false, 
                            definitions: { '#': { validator: "[0-9]", cardinality: 1}} 
                        });
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                        doBeforeSend(false)
                    },
                    complete: function() {
                        // selesai
                        doBeforeSend(false)
                    },
                    timeout: 8000,
                });
                break;
            case "E-Wallet":
                $.ajax({
                    url: href,
                    method: "POST",
                    data: {
                        payCode: f
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        console.log(result);
                        let htmlOption = "";
                        $.each(result.paymentMethodDetails, function(key, value) {
                            payM = `'`+value.type_payment+`'`;
                            htmlOption += `
                                <div class="col-12 col-sm-4 col-md-3 col-lg-3 my-1">
                                    <button class="btn bg-light btn-sm btn-block btn-payment" id="`+value.type_payment+``+value.nm_payment+`" onclick="paymentSet(`+payM+`,'`+value.nm_payment+`', '`+value.charge+`', this.id)" style="min-height: 100px; max-height: 100px;">
                                        <img src="{{ asset('dist/img/online/`+value.image_details+`') }}" alt="" style="max-width: 100px;">
                                    </button>
                                </div>
                            `;
                        });
                        $("#payment").html(`
                            <div class="row d-flex align-item-center justify-content-arround">
                                `+htmlOption+`
                            </div>
                        `);
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                        doBeforeSend(false)
                    },
                    complete: function() {
                        // selesai
                        doBeforeSend(false)
                    },
                    timeout: 8000,
                });
                break;
            case "Transfer":
                $.ajax({
                    url: href,
                    method: "POST",
                    data: {
                        payCode: f
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        console.log(result);
                        let htmlOption = "";
                        $.each(result.paymentMethodDetails, function(key, value) {
                            payM = `'`+value.type_payment+`'`;
                            htmlOption += `
                                <div class="col-12 col-sm-4 col-md-3 col-lg-3 my-1">
                                    <button class="btn bg-light btn-sm btn-block btn-payment" id="`+value.type_payment+``+value.nm_payment+`" onclick="paymentSet(`+payM+`,'`+value.nm_payment+`', '`+value.charge+`', this.id)" style="min-height: 100px; max-height: 100px;">
                                        <img src="{{ asset('dist/img/bank/`+value.image_details+`') }}" alt="" style="max-width: 100px;">
                                    </button>
                                </div>
                            `;
                        });
                        $("#payment").html(`
                            <div class="row d-flex align-item-center justify-content-arround">
                                `+htmlOption+`
                            </div>
                        `);
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                        doBeforeSend(false)
                    },
                    complete: function() {
                        // selesai
                        doBeforeSend(false)
                    },
                    timeout: 8000,
                });
                break;
            case "TransferCash":
                $.ajax({
                    url: href,
                    method: "POST",
                    data: {
                        payCode: f
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        let htmlOption = "";
                        $.each(result.paymentMethodDetails, function(key, value) {
                            payM = `'`+value.type_payment+`'`;
                            htmlOption += `
                                <div class="col-12 col-sm-4 col-md-3 col-lg-3 my-1">
                                    <button class="btn bg-light btn-sm btn-block btn-payment" id="`+value.type_payment+``+value.nm_payment+`" onclick="paymentSet(`+payM+`,'`+value.type_payment+`', '`+value.charge+`', this.id)" style="min-height: 100px; max-height: 100px;">
                                        <img src="{{ asset('dist/img/bank/`+value.image_details+`') }}" alt="" style="max-width: 100px;">
                                    </button>
                                </div>
                            `;
                        });

                        $("#payment").html(`
                            <div class="row d-flex justify-content-arround">
                                <div class="col-12 col-sm-6 col-md-6 col-lg-6 my-1">
                                    <div class="form-group">
                                        <label for="cash_ammount">Cash Amount</label>
                                        <input class="form-control text-right" id="cash_amount" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6 col-lg-6 my-1">
                                    <div class="form-group">
                                        <label for="transfer_ammount">Transfer Amount</label>
                                        <input class="form-control text-right" id="transfer_ammount" placeholder="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex align-item-center justify-content-arround">
                                `+htmlOption+`
                            </div>
                        `);
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                        doBeforeSend(false)
                    },
                    complete: function() {
                        // selesai
                        doBeforeSend(false)
                    },
                    timeout: 8000,
                });
                break;
        }

        $("#pMethod").val(e);
    };   

    const setOperator = (param) => {
        if(param == '+'){
            $('#operator').val('+');
            $("#plusOperator").attr("class", "btn btn-success btn-block btn-xs");
            $("#minOperator").attr("class", "btn btn-outline-success btn-block btn-xs");
        } else {
            $('#operator').val('-');
            $("#plusOperator").attr("class", "btn btn-outline-success btn-block btn-xs");
            $("#minOperator").attr("class", "btn btn-success btn-block btn-xs");
        }
    };    

    const setBayarTunai = (nominal) => {
        let total = parseInt($("#total").val());
        let operator = $("#operator").val();
        let nominalNow = $("#setCashBack").val().replace(/(\d)[\s.]+(?=\d)/g, '$1');
        let bayar = 0;

        if(operator == "+"){
            if(nominalNow == ""){
                bayar = 0 + parseInt(nominal)
            } else {
                bayar = parseInt(nominalNow) + parseInt(nominal)
            }
        } else {
            if(nominalNow == ""){
                bayar = 0 - parseInt(nominal)
            } else {
                bayar = parseInt(nominalNow) - parseInt(nominal)
            }            
        }

        $("#setCashBack").val(formatNumber(bayar));
        let cashBack = maskRupiah("", parseInt(total) - parseInt(bayar));
        $("#cashBack").val(cashBack);
        if (bayar < total) {
            $("#setCashBack").addClass("is-invalid");
            $('button.swal2-confirm').attr("disabled", true);
            $('button.swal2-deny').attr("disabled", true);
        } else {
            $("#setCashBack").removeClass("is-invalid");
            $('button.swal2-confirm').attr("disabled", false);
            $('button.swal2-deny').attr("disabled", false);
        }
    };    
    
    // calculation cashback before print
    const setCashBack = () => {
        let pay = parseInt($("#setCashBack").unmask().val());
        let total = parseInt($("#total").val());
        let cashBack = maskRupiah("", pay - total);
        $("#cashBack").val(cashBack);
        if (pay < total) {
            $("#setCashBack").addClass("is-invalid");
            $('button.swal2-confirm').attr("disabled", true);
            $('button.swal2-deny').attr("disabled", true);
        } else {
            $("#setCashBack").removeClass("is-invalid");
            $('button.swal2-confirm').attr("disabled", false);
            $('button.swal2-deny').attr("disabled", false);
        }
    }; 
    
    const paymentSet = (method, methodDetails, charge, buttonId) => {
        let total = parseInt($("#sub_grand_total").val());
        total = total - parseInt($("#charge").val());
        let rowIndexPayment = $("#table-payments tbody tr").length;

        $("#table-payments tbody").append(`
            <tr style="background-color: aliceblue;">
                <td class="align-middle" style="text-align: left;">
                    <span>`+method+`</span><br>
                    <small><b>`+methodDetails+`</b></small>
                    <input type="hidden" name="payment_method[]" value="`+method+`">
                    <input type="hidden" name="payment_method_details[]" value="`+methodDetails+`">
                    <input type="hidden" name="payment_charge[]" value="`+charge+`">
                </td>
                <td class="align-middle">`+charge+`</td>
                <td><input class="form-control text-right" id="detail_nominal`+(rowIndexPayment+1)+`" name="detail_nominal[]" value="0" style="border:none;" onchange="paymentSum()"></td>
                <td class="align-middle" style="width: 4%;">
                    <button class="btn btn-flat btn-outline bg-red btn-sm" onclick="$(this).closest('tr').remove(); paymentSum();"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `);

        // let paymentCharge =  total * parseFloat(charge)/100;
        // var newTotal = total + paymentCharge;
        // $("#charge").val(parseInt(paymentCharge));
        // $("#total").val(newTotal); 

        // if(method == "TransferCash"){
        //     let cash_ammount = parseInt($("#cash_amount").val());
        //     if(cash_ammount == 0 || cash_ammount == ""){
        //         alert("Please fill cash amount first");
        //     } else {
        //         let transfer_ammount = newTotal - cash_ammount;
    
        //         $("#transfer_ammount").val(transfer_ammount);
        //         $("#setCashBack").val(formatNumber(cash_ammount + transfer_ammount));
        //     }
        // } else {
        //     $("#setCashBack").val(formatNumber(newTotal));
        // }
        // maskRupiah("#total_view", newTotal);
        // maskRupiah("#total_penjualan", newTotal);
        
        // $("#pMethodDetails").val(methodDetails);
        // $(".btn-payment").removeClass("btn-dark");
        // $("#"+buttonId).addClass("btn-dark");
        setCashBack();
    }

    const paymentSum = () => { 
        let rowIndexPayment = $("#table-payments tbody tr").length;
        let totalPayment = 0;

        for (let index = 1; index <= rowIndexPayment; index++) {
            let pay = $("#detail_nominal"+index).val();

            totalPayment = parseFloat(totalPayment) + parseFloat(pay);
        }
        console.log(totalPayment);

        $("#setCashBack").val(formatNumber(totalPayment));

        setCashBack();
    }

    const saveData = (action) => {
        paymentMethod()
        let href = "/sales/store/" + action;
        let formData = $("form").serialize();

        swal.fire({
                title: action == "simpan" ? "Apakah Ingin Menyimpan" : "Pilih Pembayaran",
                icon: "question",
                width: 1000,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: "#20C997",
                denyButtonColor: "#007BFF",
                cancelButtonColor: "#DC3545",
                confirmButtonText: action == "simpan" ? "Simpan" : "Simpan & Cetak",
                denyButtonText: action == "simpan" ? "Batal" : "Simpan",
                cancelButtonText: "Batal",
                html: `
                <div class="row py-2" style="background-color: #2e5781; border-radius: 6px;">
                    <div class="col-sm-4 col-4">
                        <div class="description-block border-right">
                            <span class="description-text text-white">TOTAL PENJUALAN</span>
                            <p class="text-secondary"></p>
                            <input type="hidden" name="grandtotal" id="grandtotal" value="0">
                            <input type="hidden" name="charge" id="charge" value="0">
                            <h1 class="description-header text-white" id="total_penjualan">${maskRupiah("", $("#total").val())}</h1>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 col-4">
                        <div class="description-block border-right">
                            <span class="description-text text-white">TOTAL DIBAYAR</span>
                            <p class="text-secondary"></p>
                            <input type="text" class="form-control form-control uang text-white" oninput="setCashBack()" placeholder="Bayar" id="setCashBack" name="setCashBack" style="background-color: transparent; font-size: 17px; font-weight: bold; text-align: center; margin-top: -10px; border: none;" autofocus required>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col --> 
                    <div class="col-sm-4 col-4">
                        <div class="description-block">
                            <span class="description-text text-white">TOTAL KEMBALIAN</span>
                            <p class="text-secondary"></p>
                            <input type="text" class="form-control form-control text-white" placeholder="Kembali" id="cashBack" style="background-color: transparent; font-size: 17px; font-weight: bold; text-align: center; margin-top: -10px; border: none;" readonly>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <div class="row py-2">
                    <div class="col-md-12">
                        <form id="records-table">
                            <table class="table table-borderless table-sm fs-9" id="table-payments" style="font-size: 15px;">
                                <tbody>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="row py-2">
                    <div class="col-sm-3 scrollYMenu" id="payment_method" style="background-color: #c5c5c521; padding: 12px; height: 50vh"></div>
                    <div class="col-sm-9 scrollYMenu" id="payment" style="padding: 12px; height: 50vh"></div>
                </div>
                <input type="hidden" class="form-control form-control" id="pMethod" name="pMethod" readonly>
                <input type="hidden" class="form-control form-control" id="pMethodDetails" name="pMethodDetails" readonly>`,
            })
            .then((resultSwal1) => {
                let pay = $("#setCashBack").unmask().val();
                let total = $("#total").unmask().val();
                let grandtotal = $("#grandtotal").unmask().val();
                let charge = $("#charge").unmask().val();
                let cashBack = pay - total;
                let pMethod = $("#pMethod").val();
                let pMethodDetails = $("#pMethodDetails").val();
                let paymentDetails = $("#records-table").serialize();
                formData = formData + '&grandtotal='+ total;
                formData = formData + '&charge='+ charge;
                if(pMethod == "TransferCash"){
                    let cash = $("#cash_amount").unmask().val();
                    let nonCash = $("#transfer_ammount").unmask().val();
                    
                    formData = formData + '&payCash='+ cash;
                    formData = formData + '&payNonCash='+ nonCash;
                }else if(pMethod == "Cash"){
                    formData = formData + '&payCash='+ pay;
                    formData = formData + '&payNonCash=0';
                } else {
                    formData = formData + '&payCash=0';
                    formData = formData + '&payNonCash='+ pay;
                }
                formData = formData + '&cashBack='+ cashBack;
                formData = formData + '&'+ paymentDetails;

                if (resultSwal1.isConfirmed && pay >= total) {
                    $.ajax({
                        url: href,
                        method: "POST",
                        data: formData,
                        beforeSend: function() {
                            doBeforeSend(true)
                        },
                        success: function(res) {
                            if (res.status == "success") {
                                if (action == "bayar") {
                                    // forward ke cetak
                                    let pay = $("#setCashBack").unmask().val();
                                    let total = $("#total").unmask().val();
                                    let cashBack = pay - total;
                                    let pMethod = $("#pMethod").val();
                                    let win = window.open(
                                        "{{ url('') }}/sales/print/" + res.code_sales
                                    );
                                    let timer = setInterval(function() {
                                        if (win.closed) {
                                            clearInterval(timer);
                                            window.location = "{{ url('/sales/create') }}";
                                        }
                                    }, 500);
                                } else {
                                    //  forward ke new sales
                                    window.location = "{{ url('/sales/create') }}";
                                }
                            } else {
                                if (res.code == "E017") {} else {
                                    Swal.fire(
                                        res.status == "success" ? "Berhasil !" : "Gagal !",
                                        res.message,
                                        res.status
                                    );
                                }
                            }
                        },
                        error: function(jqXHR, testStatus, error) {
                            popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');
                            doBeforeSend(false);
                        },

                        complete: function() {
                            // selesai
                            doBeforeSend(false);
                        },
                        timeout: 8000,
                    });
                }
                if (resultSwal1.isDenied && pay >= total) {
                    if(action == "bayar"){
                        $.ajax({
                            url: href,
                            method: "POST",
                            data: formData,
                            beforeSend: function() {
                                doBeforeSend(true)
                            },
                            success: function(res) {
                                if (res.status == "success") {
                                    swal.fire({
                                            title: res.status == "success" ? "Berhasil !" : "Gagal !",
                                            text: res.message,
                                            icon: res.status,
                                            confirmButtonColor: "#3085d6",
                                            cancelButtonColor: "#d33",
                                            confirmButtonText: "Ok !",
                                        })
                                        .then((resultSwal2) => {
                                            if(resultSwal2.isConfirmed) {
                                                window.location = "{{ url('/sales/create') }}";
                                            }
                                        });
                                } else {
                                    if (res.code == "E017") {} else {
                                        Swal.fire(
                                            res.status == "success" ? "Berhasil !" :
                                            "Gagal !",
                                            res.message,
                                            res.status
                                        );
                                    }
                                }
                            },
                            error: function(jqXHR, testStatus, error) {
                                popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');
                                doBeforeSend(false)
                            },

                            complete: function() {
                                // selesai
                                doBeforeSend(false)
                            },
                            timeout: 8000,
                        });
                    }
                }
                
                if((resultSwal1.isConfirmed && pay < total) || (resultSwal1.isDenied && pay < total)) {
                    Swal.fire("Gagal !", "Uang bayar kurang", "error");
                }
            });
    };
</script>
@endsection