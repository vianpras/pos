@extends('layouts.app')

@section('content')
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
                                    <div class="form-group">
                                        <label for="membership_code"><sup class="text-red">*</sup>Business Partner Code</label>
                                        <select class="form-control form-control-sm select2" name="custcode" id="custcode" onchange="setBusinessPartner(this.value)">
                                            <option value="" disabled selected hidden>Choose</option>
                                            @foreach($customer AS $cust)
                                                <option value="{{ $cust->cardcode }}">{{ (($cust->phoneCode) ? $cust->phoneCode : '0000').' | '.$cust->cardname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code"><sup class="text-red">*</sup>Business Partner Name</label>
                                        <input type="text" class="form-control form-control-sm" name="custname" id="custname" placeholder="Business Partner Name" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code"><sup class="text-red">*</sup>Telephone Number</label>
                                        <input type="text" class="form-control form-control-sm" name="custphone" id="custphone" placeholder="Telephone Number" readonly>
                                    </div>
                                </section>
                                <section class="col-sm-6">
                                    <div class="form-group">
                                        <label for="membership_code">Sales</label>
                                        <input type="text" class="form-control form-control-sm" name="code" id="code" placeholder="Sales" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code">Checker</label>
                                        <input type="text" class="form-control form-control-sm" name="code" id="code" placeholder="Checker" readonly>
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
                                    <input type="hidden" name="total" id="total" value="0">
                                    <span class="h6 m-0 float-left text-secondary">
                                        #{{ Helper::docPrefix('sales') }}
                                    </span>
                                </div>
                                <div class="col align-self-center">
                                    <span class="h4 m-0 float-right text-olive" id="total_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col align-self-center">
                                    <input type="hidden" name="sub_grand_total" id="sub_grand_total" value="0">
                                    <span class="h5 m-0 float-left text-secondary">Sub Total</span>
                                </div>
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-right text-secondary" id="sub_grand_total_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-left text-secondary">Diskon</span>
                                </div>
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-right text-secondary" id="discount_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col align-self-center">
                                    <span class="h5 m-0 float-left text-secondary">Pajak</span>
                                </div>
                                <div class="col align-self-center">
                                    <input type="hidden" name="tax" id="tax" value="0">
                                    <span class="h5 m-0 float-right text-secondary" id="tax_view">
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
                            <div class="row gx-3">
                                <div class="scrollYMenu col-12 col-sm-12 col-xl-12" style="max-height: 35vh;">
                                    <table class="table table-sm mt-2">
                                        <thead>
                                            <tr>
                                                <th width="18%">Item Info</th>
                                                <th>Token Remarks</th>
                                                <th width="5.7%">Cart</th>
                                                <th width="5.7%">Actual</th>
                                                <th width="13%">Price List</th>
                                                <th>Price</th>
                                                <th>Price Status</th>
                                                <th width="5.7%">Disc. 1</th>
                                                <th width="5.7%">Disc. 2</th>
                                                <th width="5.7%">Disc. 3</th>
                                                <th>Final Discount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-list">
                                            
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
        console.log(cardcode);
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
                console.log(result);
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
        let itemcode = $('#itemcode_modal').val();
        let itemname = $('#itemname_modal').val();
        let pricelist = "{{ $pricelist }}";
        let price = $('#price_modal_input').val();
        let qty = $('#qty_modal').val();

        var rowCount = $('#item-list tr').length;
        var i = rowCount + 1;
        
        var output = [];
        $.each(JSON.parse(pricelist.replace(/&quot;/g,'"')), function(key, value){
            output.push('<option value="'+ value.listnum +'">'+ value.listname +'</option>');
        });

        $('#item-list').append(`
            <tr id="row-item-detail`+i+`" data-id="`+i+`">
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="hidden" id="itemcode`+i+`" name="itemcode">
                    <dt>`+itemcode+`</dt>
                    <dd style="width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">`+itemname+`</dd>
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="text" name="token_remarks">
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="number" name="cart" id="cart`+i+`" value="`+qty+`" readonly>
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="number" name="actual" id="actual`+i+`" onchange="calcGrand()" value="`+qty+`">
                </td>
                <td class="align-middle">
                    <select class="form-control form-control-sm" name="price_list" id="price_list`+i+`" onchange="priceList('`+itemcode+`', this.value, `+i+`)" style="appearance: none;">
                        `+output.join('')+`
                    </select>
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="hidden" name="price" id="price`+i+`" value="`+price+`" readonly>
                    <span id="price_show`+i+`">`+maskRupiah("", price)+`</span>
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="text" name="price_status" value="0">
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="number" name="disc1" value="0">
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="number" name="disc2" value="0">
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="number" name="disc3" value="0">
                </td>
                <td class="align-middle">
                    <input class="form-control form-control-sm" type="text" name="final_disc" value="0">
                </td>
                <td class="align-middle">
                    <button type="button" name="remove" id="`+i+`" class="btn btn-sm btn-danger btn_remove"><span class="fas fa-trash"></span></button>
                </td>
            </tr>
        `);

        $('#modalBlade').modal("hide");
        $("#item-details-price").html("");
        $('#itemcode').val('').select2('destroy').select2();
        calcGrand()
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
                calcGrand()
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

    const calcGrand = () => {
        let subGrandTotalPrice = parseInt(0);

        $('table > #item-list > tr').each(function() { 
            let rowIndex = $(this).data("id");
            let actual = $("input#actual" + rowIndex).val();
            let price = $("input#price" + rowIndex).val();
    
            subGrandTotalPrice += parseInt(actual)*parseInt(price);
        });

        $("#sub_grand_total").val(subGrandTotalPrice);
        maskRupiah("#sub_grand_total_view", subGrandTotalPrice);
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
                    payM = `'`+value.code+`'`;
                    console.log(payM)
                    htmlOption += `
                        <button class="btn bg-lime btn-sm btn-block button-mthd" id=`+payM.replace(/\s/g, '')+` onclick="setPay(`+payM.replace(/\s/g, '')+`,'','')" style="padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781">
                            <b>`+value.name+`</b>
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

        console.log(e)
        let total = formatNumber($("#total").val());
        switch (e) {
            case "Cash":
                $("#payment").html("");
                $("#payment").html(`
                    <div class="row">
                        <input type="hidden" class="form-control" id="operator" value="+">
                        <div class="col-sm-2 col-md-2">
                            <button type="button" class="btn btn-success btn-block btn-xs" id="plusOperator" onclick="setOperator('+')" style="font-size: 20px !important;">+</button>
                            <button type="button" class="btn btn-outline-success btn-block btn-xs" id="minOperator" onclick="setOperator('-')" style="font-size: 20px !important;">-</button>
                        </div>
                        <div class="col-sm-10 col-md-10">
                            <div class="row justify-content-md-center">
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(500)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">500</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(10000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">10.000</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(1000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">1.000</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(20000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">20.000</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(2000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">2.000</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(50000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">50.000</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(5000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">5.000</button>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(100000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">100.000</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                break;
        }
        // if(e == 'tunai'){
        //     $("#payment").html("");
        //     $("#payment").html(`
        //         <div class="row">
        //             <input type="hidden" class="form-control" id="operator" value="+">
        //             <div class="col-sm-2 col-md-2">
        //                 <button type="button" class="btn btn-success btn-block btn-xs" id="plusOperator" onclick="setOperator('+')" style="font-size: 20px !important;">+</button>
        //                 <button type="button" class="btn btn-outline-success btn-block btn-xs" id="minOperator" onclick="setOperator('-')" style="font-size: 20px !important;">-</button>
        //             </div>
        //             <div class="col-sm-10 col-md-10">
        //                 <div class="row justify-content-md-center">
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(500)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">500</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(10000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">10.000</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(1000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">1.000</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(20000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">20.000</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(2000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">2.000</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(50000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">50.000</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(5000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">5.000</button>
        //                     </div>
        //                     <div class="col-sm-6 col-md-6">
        //                         <button type="button" class="btn btn-outline-success btn-block btn-xs" id="tax_item" onclick="setBayarTunai(100000)" style="font-size: 20px !important;height: 70px;margin-bottom: 10px; font-weight: bold;">100.000</button>
        //                     </div>
        //                 </div>
        //             </div>
        //         </div>
        //     `);

        //     $("#setNonTunai").attr('style', 'padding: 25px; height: 14vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');
        //     $("#setOnline").attr('style', 'padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');

        //     $("#setTunai").attr('style', 'padding: 25px;height: 15vh;color: white !important;background-color: #2e5781 !important;');
        // }

        // let total = formatNumber($("#total").val());
        // switch (e) {
        //     case "debit":
        //         $("#setCashBack").val(total);
        //         $("#pMethod").val("Kartu debit/kredit "+f);
        //         $("#setCashBack").attr("readonly", true);
        //         break;
        //     case "ewallet":
        //         $("#setCashBack").val(total);
        //         $("#pMethod").val("ewallet");
        //         $("#setCashBack").attr("readonly", true);
        //         break;
        //     case "25000":
        //         $("#pMethod").val("tunai");
        //         $("#setCashBack").val("25.000");
        //         break;
        //     case "50000":
        //         $("#pMethod").val("tunai");
        //         $("#setCashBack").val("50.000");
        //         break;

        //     case "75000":
        //         $("#setCashBack").val("75.000");
        //         $("#pMethod").val("tunai");
        //         $("#setCashBack").attr("readonly", true);
        //         break;
        //     case "100000":
        //         $("#setCashBack").val("100.000");
        //         $("#pMethod").val("tunai");
        //         $("#setCashBack").attr("readonly", true);
        //         break;
        //     case "tunai":
        //         $("#setCashBack").val("");
        //         $("#setCashBack").focus();
        //         $("#setCashBack").attr("readonly", false);
        //         break;

        //     case "pas":
        //         $("#setCashBack").val(total);
        //         $("#pMethod").val("tunai");
        //         $("#setCashBack").attr("readonly", false);
        //         break;
        //     default:
        //         $("#setCashBack").val(total);
        //         $("#pMethod").val(e);
        //         $("#setCashBack").attr("readonly", true);
        //         break;
        // }
        setCashBack();
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
        let nominalNow = $("#setCashBack").val().toString().replace('.', "");

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
    
    const nonTunai = () => { 
        $("#payment").html("");

        $("#setTunai").attr('style', 'padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');
        $("#setOnline").attr('style', 'padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');

        $("#setNonTunai").attr('style', 'padding: 25px; height: 14vh;color: white !important;background-color: #2e5781 !important;');

        $("#payment").html(`
            <div class="row d-flex align-item-center justify-content-arround">
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setBCA" onclick=setPay('debit','BCA',this.id) style="padding: 34px">
                        <img src="{{ asset('dist/img/logos/bca.png') }}" alt="" style="max-width: 80px;">
                    </button>
                </div>
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setMANDIRI" onclick=setPay('debit','MANDIRI',this.id) style="padding: 33px">
                        <img src="{{ asset('dist/img/logos/mandiri.png') }}" alt="" style="max-width: 100px;">
                    </button>
                </div>
            </div>
        `);
    }
    
    const onlinePayment = () => { 
        $("#payment").html("");

        $("#setTunai").attr('style', 'padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');
        $("#setNonTunai").attr('style', 'padding: 25px; height: 14vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');

        $("#setOnline").attr('style', 'padding: 25px;height: 15vh;color: white !important;background-color: #2e5781 !important;');

        $("#payment").html(`
            <div class="row d-flex align-item-center justify-content-arround">
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setGoPay" onclick=setPay('GoPay','',this.id)>
                        <img src="{{ asset('dist/img/logos/gopay.png') }}" alt="" style="max-width: 150px;">
                    </button>
                </div>
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setOVO" onclick=setPay('OVO','',this.id) style="padding:43px;">
                        <img src="{{ asset('dist/img/logos/ovo.png') }}" alt="" style="max-width: 56px;">
                    </button>
                </div>
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setShopeePay" onclick=setPay('ShopeePay','',this.id) style="padding:36px;">
                        <img src="{{ asset('dist/img/logos/shopeepay.png') }}" alt="" style="max-width: 80px;">
                    </button>
                </div>
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setDana" onclick=setPay('Dana','',this.id) style="padding:42px;">
                        <img src="{{ asset('dist/img/logos/dana.png') }}" alt="" style="max-width: 80px;">    
                    </button>
                </div>
                <div class="col-3 my-1">
                    <button class="btn bg-light btn-sm btn-block" id="setLinkAja" onclick=setPay('LinkAja','',this.id) style="padding:29px;">
                        <img src="{{ asset('dist/img/logos/linkaja.png') }}" alt="" style="max-width: 50px;">
                    </button>
                </div>
            </div>
        `);
    }    
    
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

    const saveData = (action) => {
        paymentMethod()
        let href = "/sales/store/" + action;
        let formData = $("form").serialize();
        let pay = $("#setCashBack").unmask().val();
        let total = $("#total").unmask().val();
        let cashBack = pay - total;
        let pMethod = $("#pMethod").val();
        formData = formData + '&pay='+ pay;
        formData = formData + '&cashBack='+ cashBack;
        formData = formData + '&pMethod='+ pMethod;

    swal
        .fire({
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
                        <h1 class="description-header text-white" id="monthlySales">${maskRupiah("", $("#total").val())}</h1>
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
                <div class="col-sm-3 scrollYMenu" id="payment_method" style="background-color: #c5c5c521; padding: 12px; height: 50vh">
                </div>
                <div class="col-sm-9" id="payment" style="padding: 12px; height: 50vh">
                </div>
            </div>
            <input type="hidden" class="form-control form-control" id="pMethod"  name="pMethod" value="Tunai" readonly>
        `,
        })
        .then((resultSwal1) => {
            let pay = $("#setCashBack").unmask().val();
            let total = $("#total").unmask().val();
            let cashBack = pay - total;
            let pMethod = $("#pMethod").val();
            formData = formData + '&pay='+ pay;
            formData = formData + '&cashBack='+ cashBack;
            formData = formData + '&pMethod='+ pMethod;
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
                                    "{{ url('') }}/sales/print/" +
                                    res.code_sales +
                                    "?pay=" +
                                    pay +
                                    "&cashback=" +
                                    cashBack +
                                    "&pMethod=" +
                                    pMethod
                                );
                                let timer = setInterval(function() {
                                    if (win.closed) {
                                        clearInterval(timer);
                                        window.location =
                                            "{{ url('/sales/create') }}";
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
                if (action == "bayar") {
                    $.ajax({
                        url: href,
                        method: "POST",
                        data: formData,
                        beforeSend: function() {
                            doBeforeSend(true)
                        },
                        success: function(res) {
                            if (res.status == "success") {
                                swal
                                    .fire({
                                        title: res.status == "success" ? "Berhasil !" :
                                            "Gagal !",
                                        text: res.message,
                                        icon: res.status,
                                        confirmButtonColor: "#3085d6",
                                        cancelButtonColor: "#d33",
                                        confirmButtonText: "Ok !",
                                    })
                                    .then((resultSwal2) => {
                                        if (resultSwal2.isConfirmed) {
                                            window.location =
                                                "{{ url('/sales/create') }}";
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
            if (
                (resultSwal1.isConfirmed && pay < total) ||
                (resultSwal1.isDenied && pay < total)
            ) {
                Swal.fire("Gagal !", "Uang bayar kurang", "error");
            }
        });
    };
</script>
@endsection