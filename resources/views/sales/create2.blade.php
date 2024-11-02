@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\DB;
@endphp
<div class="content-wrapper">
    <section class="content" style="min-height: 40vh; max-height: 40vh" id="wrapper">
        <form id='SalesForm'>
            @csrf
            <div class="row p-1 m-1" style="min-height: 1vh; max-height: 1vh">
                <div class="col-7 col-md-7 col-sm-7 p-1">
                    <div class="card m-1">
                        <div class="card-header" data-card-widget="collapse">
                            <h3 class="card-title">Nomor Meja : <span id="meja_show">-</span> <span id="type_show"></span></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <section class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="membership_code"><sup class="text-red">*</sup>Business Partner Code</label>
                                                <select class="form-control form-control-sm select2" name="custcode" id="custcode">
                                                    <option value="" disabled selected hidden>Choose</option>
                                                    @foreach($customer AS $cust)
                                                        <option value="{{ $cust->cardcode }}" {{ (($cart) ? (($cart->bussiness_partner == $cust->cardcode) ? "selected" : "") : "") }}>{{ (($cust->phoneCode) ? $cust->phoneCode : '0000').' | '.$cust->cardname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="membership_code">Diskon <span class="text-teal">(%)</span></label>
                                                <input type="number" class="form-control form-control-sm" name="discount" id="discount" value="0" step="0.01" placeholder="Diskon" onkeypress="calcGrand()" onkeyup="calcGrand()" onchange="calcGrand()" min="0" max="100" maxlength="1" ondrop="return false;" onpaste="return false;" autocomplete="off" value="0" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="tax">Pajak</label>
                                                <div class="row align-item-center justify-content-arround">
                                                    <div class="col-6 gx-1">
                                                        <button type="button" class="btn btn-outline-success btn-xs btn-block btn-tax" id="tax_item" value="10" onclick="setTax(this.id, 10)">10%</button>
                                                    </div>
                                                    <div class="col-6 gx-1">
                                                        <button type="button" class="btn btn-outline-success btn-xs btn-block btn-tax" id="tax_jasa" value="2" onclick="setTax(this.id, 2)">2%</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 mb-1">
                                            <label for="tax">Kategori Penjualan</label>
                                            <input type="hidden" id="tipe_penjualan" name="tipe_penjualan" value="UM">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-flat btn-primary btn-block btn-sm" id="no_meja_um" value="UM" onclick="setTipePenjualan(this.id, 'UM')">Umum</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-flat btn-outline-success btn-block btn-sm" id="no_meja_go" value="GO" onclick="setTipePenjualan(this.id, 'GO')">Gojek</button>
                                                </div>
                                                <div class="col-sm-3 mb-1">
                                                    <button type="button" class="btn btn-flat btn-outline-info btn-block btn-sm" id="no_meja_gr" value="GR" onclick="setTipePenjualan(this.id, 'GR')">Grab</button>
                                                </div>
                                                <div class="col-sm-3 mb-1">
                                                    <button type="button" class="btn btn-flat btn-outline-warning btn-block btn-sm" id="no_meja_sp" value="SP" onclick="setTipePenjualan(this.id, 'SP')">Shopee</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="table">Meja</label>
                                            <input type="hidden" name="table" id="table">
                                            <div class="row">
                                                {{-- @for($i = 1; $i <= $getTotalCart; $i++) 
                                                <div class="col-sm-2 mb-1">
                                                    <button type="button" class="btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja meja" id="no_meja_{{ $i }}" value="{{ $i }}" onclick="setMeja(this.id, {{ $i }}, 'non active')">{{ $i }}</button>
                                                </div>
                                                @endfor --}}
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                    <div class="card m-1">
                        <div class="card-body m-0">
                            <div class="input-group">
                                <input type="hidden" name="search_item" id="search_item" class="form-control form-control-sm" placeholder="Masukkan Nama Item" value="semua_item">
                            </div>
                            {{-- List Category Will be Load Here --}}
                            <div class="scrollXMenu mb-2" id="cButton">
                                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                    <button type="button" class="btn btn-primary mr-2 btn-sm btn_category" onclick="getItemClick(this)" id="semua_item" data-id="semua_item">Semua Item</button>
                                    <button type="button" class="btn mr-2 btn-sm btn_category" onclick="getItemClick(this)" id="favorit" data-id="favorit" style="border: 1px solid #48474767; color: #48474767;">Favorit</button>
                                    {{-- @foreach ($categories as $key => $category)
                                        <button type="button" class="btn mr-2 btn-sm btn_category" onclick="getItemClick(this)" id="{{ $category->name }}" data-id="{{ $category->id }}" style="border: 1px solid #48474767; color: #48474767;">{{ $category->name }}</button>
                                    @endforeach --}}
                                    {{-- <button type="button" class="btn btn-secondary mr-2 btn-sm" onclick="newItem()" id="item_baru">+ Item Baru</button> --}}
                                </div>
                            </div>

                            <table id="dTable1" class="table table-bordered table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Item</th>
                                        <th>Harga</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items AS $itm)
                                    <tr>
                                        <td>{{ $itm->itemname }}</td>
                                        <td>{{ Helper::formatNumber($itm->price, 'rupiah') }}</td>
                                        <td><button type="button" class="btn btn-flat bg-cyan btn-block btn-xs" onclick="addToCart('{{ $itm->itemcode }}','{{ $itm->itemcode }}','{{ $itm->itemname }}','{{ $itm->price }}')">Add</button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-5 col-md-5 col-sm-5 p-1">
                    <div class="card m-1">
                        <div class="card-header  m-0 p-2 align-items-center justify-content-center">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="total" id="total" value="0">
                                    <span class="h6 m-0 float-left text-secondary">
                                        #{{ Helper::docPrefix('sales') }}
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="h4 m-0 float-right text-olive" id="total_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="sub_grand_total" id="sub_grand_total" value="0">
                                    <span class="h5 m-0 float-left text-secondary">Sub Total</span>
                                </div>
                                <div class="col">
                                    <span class="h5 m-0 float-right text-secondary" id="sub_grand_total_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <span class="h5 m-0 float-left text-secondary">Diskon</span>
                                </div>
                                <div class="col">
                                    <span class="h5 m-0 float-right text-secondary" id="discount_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <span class="h5 m-0 float-left text-secondary">Pajak</span>
                                </div>
                                <div class="col">
                                    <input type="hidden" name="tax" id="tax" value="0">
                                    <span class="h5 m-0 float-right text-secondary" id="tax_view">
                                        {{ Helper::formatNumber('0', 'rupiah') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body  m-0 p-2">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group" id="pending">
                                        <button type="button" onclick="saveData('bayar')" class="btn btn-flat btn-block btn-outline bg-maroon">Bayar</button>
                                    </div>
                                    <div class="form-group" id="pay">
                                        <button type="button" onclick="saveData('simpan')" style="display: none" class="btn btn-flat btn-block bg-teal">Simpan</button>
                                    </div>
                                    <div class="form-group" id="updateSales" style="display: none">
                                        <button type="button" onclick="updateData('simpan')" class="btn btn-flat btn-block bg-teal">Ubah</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer m-0 p-2">
                            <div class="scrollYMenu pr-1" style="height:60vh" id="listCart"></div>
                        </div>
                        <div class="card-footer pb-0 m-0"></div>
                    </div>
                </div>
            </div>
        </form>
    </section>
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

@endsection

@section('jScript')
<script>
    
    // $(document).ready(function() {
    //     var tipe_penjualan = $("#tipe_penjualan").val();
    //     getCartByType(tipe_penjualan)
    // });

    var dTable = $('#dTable1').dataTable({
        pageLength: 25,
        lengthMenu: [[5, 10, 15, 25, -1], [5, 10, 15, 25, "All"]],
    });

    function getCartByType(tipe_penjualan) {
        $(".meja").attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja meja');

        let href = "/sales/getCartByType/";
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: href,
            method: "POST",
            data: {
                type: tipe_penjualan
            },
            cache: false,
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                if (result.cart.length > 0) {
                    $.each(result.cart, function (i, elem) {
                        $("#no_meja_"+elem.table).attr('class', 'btn btn-flat btn-warning btn-block btn-sm btn-active meja');
                        $("#no_meja_"+elem.table).attr('onclick', 'setMeja("no_meja_'+elem.table+'", '+elem.table+', "active")');
                    })
                }
            },
            error: function(jqXHR, testStatus, error) {
                // tampilkan jika error respon server
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

    // controller render card item
    const _renderCard = (id, code, name, sell_price, category_name) => {
        card = '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 color-palette shadow m-1 mb-2"';
        card += 'style="border-radius: 0.5em ;"';
        card +=
            "onclick=\"addToCart('" +
            id +
            "','" +
            code +
            "','" +
            name +
            "','" +
            sell_price +
            "')\">";
        card +=
            '<div class="row d-flex flex-column align-item-center justify-content-arround">';
        card += '<div class="d-flex col-sm-12 flex-column d-block justify-content-center align-items-center mt-2">';
        card += '<div src="#" style="background-image: url(/img/items/' + id + ');background-repeat: no-repeat;background-size: cover; aspect-ratio: 1 / 1;" class="profile-user-img  img-circle text-center img-fluid img-items"></div>';
        card += "</div>";
        card +=
            '<div class="col-sm-12 d-flex flex-column justify-content-center align-items-center p-2">';
        card += '<div class="col-12 pt-2">';
        card +=
            '<p class="text-wrap text-uppercase text-bold text-center m-0">' +
            name +
            "</p>";
        // card +='<p class="py-0 m-0 text-wrap text-secondary">'+category_name+'</p>'
        card += "</div>";
        card += '<div class="col-12 text-center m-0">';
        card +=
            '<h5><span class="badge bg-teal text-grey">' +
            maskRupiah("", sell_price) +
            "</span></h5>";
        card += "</div>";
        card += "</div>";
        card += "</div>";
        card += "</div>";
        $("#listMenu").append(card);
        showImageItem();
    };

    // controller get item by input search
    const getItemSearch = () => {
	    var tipe_penjualan = $("#tipe_penjualan").val();
        event.preventDefault();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        let href = "/sales/getItem";
        $.ajax({
            url: href,
            method: "POST",
            data: {
                search: $("input#search_item").val(),
                sales_category:  $("select#sales_category").val(),
                type:  tipe_penjualan,
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                $('#dTable1').dataTable().fnDestroy();

                if (result.length > 0) {
                    const items = result;
                    $("#listMenu").empty();
                    $("#dTable1 tbody").html("");

                    for (const item of items) {
                        let $row = 
                            `<tr>
                                <td>`+item.item_name+`</td>
                                <td>`+maskRupiah("", item.sell_price)+`</td>
                                <td><button type="button" class="btn btn-flat bg-cyan btn-block btn-xs" onclick="addToCart('`+item.id+`','`+item.code+`','`+item.item_name+`','`+item.sell_price+`')">Add</button></td>
                            </tr>`;

                        $("#dTable1 tbody").append($row);
                    }
                    $('#dTable1').dataTable({
                        pageLength: 25,
                        lengthMenu: [[5, 10, 15, 25, -1], [5, 10, 15, 25, "All"]]
                    });
                } else {
                    $("#dTable1 tbody").html("");

                    let $row = 
                            `<tr>
				                <td class='text-center'></td>
                                <td class='text-center'>Data Tidak Ditemukan</td>
				                <td class='text-center'></td>
                            </tr>`;

                    $("#dTable1 tbody").append($row);
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
    };

    // controller get per Item by click category
    const getItemClick = (value) => {
	    var tipe_penjualan = $("#tipe_penjualan").val();
        let id = $(value).attr("data-id");
        $("input#search_item").val(id)

        let name = $(value).attr("id");
        $(".btn_category").attr("class", "btn mr-2 btn-sm btn_category");
        $(".btn_category").attr("style", "border: 1px solid #48474767; color: #48474767;");

        $("#"+name).attr("class", "btn btn-primary mr-2 btn-sm btn_category");
        $("#"+name).removeAttr('style');

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        let href = "/sales/getItem";
        $.ajax({
            url: href,
            method: "POST",
            data: {
                search: id,
                sales_category:  $("select#sales_category").val(),
		        type:  tipe_penjualan,
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
		        $('#dTable1').dataTable().fnDestroy();

                if (result.length > 0) {
                    const items = result;
                    $("#listMenu").empty();
                    $("#dTable1 tbody").html("");

                    for (const item of items) {
                        let $row = 
                            `<tr>
                                <td>`+item.item_name+`</td>
                                <td>`+maskRupiah("", item.sell_price)+`</td>
                                <td><button type="button" class="btn btn-flat bg-cyan btn-block btn-xs" onclick="addToCart('`+item.id+`','`+item.code+`','`+item.item_name+`','`+item.sell_price+`')">Add</button></td>
                            </tr>`;

                        $("#dTable1 tbody").append($row);
                    }
                    $('#dTable1').dataTable({
                        pageLength: 25,
                        lengthMenu: [[5, 10, 15, 25, -1], [5, 10, 15, 25, "All"]]
                    });
                } else {
                    $("#dTable1 tbody").html("");

                    let $row = 
                            `<tr>
				                <td class='text-center'></td>
                                <td class='text-center'>Data Tidak Ditemukan</td>
				                <td class='text-center'></td>
                            </tr>`;

                    $("#dTable1 tbody").append($row);
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
    };

    // controller set payment method
    const setPay = (e, f, g) => {
        if(e == 'tunai'){
            $("#payment").html("");

            $("#payment").html(`
                <div class="row">
                    <input type="hidden" class="form-control" id="operator" value="+">
                    <div class="col-sm-2 col-md-2">
                        <button type="button" class="btn btn-success btn-block btn-xs" id="plusOperator" onclick="setOperator('+')" style="font-size: 20px !important;">+</button>
                        <button type="button" class="btn btn-outline-success btn-block btn-xs" id="minOperator" onclick="setOperator('-')" style="font-size: 20px !important;">-</button>
                        <button type="button" class="btn btn-outline-danger btn-block btn-xs" id="clearOperator" onclick="setOperator('clear')" style="font-size: 14px !important;">Clear</button>
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

            $("#setNonTunai").attr('style', 'padding: 25px; height: 14vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');
            $("#setOnline").attr('style', 'padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781');
            $("#setTunai").attr('style', 'padding: 25px;height: 15vh;color: white !important;background-color: #2e5781 !important;');
        }

        let total = formatNumber($("#total").val());
        switch (e) {
            case "debit":
                $("#setCashBack").val(total);
                $("#pMethod").val("Kartu debit/kredit "+f);
                $("#setCashBack").attr("readonly", true);
                break;

            case "ewallet":
                $("#setCashBack").val(total);
                $("#pMethod").val("ewallet");
                $("#setCashBack").attr("readonly", true);
                break;

            case "25000":
                $("#pMethod").val("tunai");
                $("#setCashBack").val("25.000");
                break;

            case "50000":
                $("#pMethod").val("tunai");
                $("#setCashBack").val("50.000");
                break;

            case "75000":
                $("#setCashBack").val("75.000");
                $("#pMethod").val("tunai");
                $("#setCashBack").attr("readonly", true);
                break;

            case "100000":
                $("#setCashBack").val("100.000");
                $("#pMethod").val("tunai");
                $("#setCashBack").attr("readonly", true);
                break;

            case "tunai":
                $("#setCashBack").val("");
                $("#setCashBack").focus();
                $("#setCashBack").attr("readonly", false);
                break;

            case "pas":
                $("#setCashBack").val(total);
                $("#pMethod").val("tunai");
                $("#setCashBack").attr("readonly", false);
                break;

            default:
                $("#setCashBack").val(total);
                $("#pMethod").val(e);
                $("#setCashBack").attr("readonly", true);
                break;
        }
        setCashBack();
    };

    const setOperator = (param) => {
        if(param == '+'){
            $('#operator').val('+');
            $("#plusOperator").attr("class", "btn btn-success btn-block btn-xs");
            $("#minOperator").attr("class", "btn btn-outline-success btn-block btn-xs");
        } else if(param == '+') {
            $('#operator').val('-');
            $("#plusOperator").attr("class", "btn btn-outline-success btn-block btn-xs");
            $("#minOperator").attr("class", "btn btn-success btn-block btn-xs");
        } else {
            $('#setCashBack').val(0);
            $("#cashBack").val(0);
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
        let cashBack = maskRupiah("", parseInt(bayar) - parseInt(total));
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

    // check order 
    const checkOrder = () => {

        let total = parseInt($('#total').val());
        if (total < 1) {
            popToast('error', 'Harap Tambahkan Item');
            return false;
        }

        let lastClass = $("#listCart .card-cart:last ").attr("id");
        let i = 0;

        if (lastClass === undefined) {
            i = 1;
        } else {
            i = parseInt(lastClass.substr(lastClass.length - 1)) + 1;
        }

        for (let index = 0; index <= i; index++) {
            let quantity = parseInt($("#quantity" + index).val())
            let itemName = $('div#card-cart-' + index + '>div>div>div:nth-child(3)>div:nth-child(2)>div>div>p').text()
            if (!isNaN(quantity) && quantity < 1) {
                popToast('error', 'Harap Tambahkan Kuantitas Item ' + itemName);
                return false;
            }
        }

        return true;
    }

    // controller get data on DB to be edit
    const editSales = () => {
        $("#code").on("keyup", function(e) {
            let code = this.value;
            console.log(code)
            let href = "/sales/editSales/" + code + "/false";
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: href,
                method: "POST",
                beforeSend: function() {
                    doBeforeSend(true)
                },
                success: function(result) {
                    if ((result.code == 'S000') && (!!result.sales)) {
                        window.location = "{{ url('/sales/edit/') }}/" + result.sales
                            .code;
                    } else {
                        // aktifkan ajax ambil meja
                        $("#setEdit").val("0");
                        // member
                        $("#membership_code").val('');
                        // nama pemesan
                        // $("#customer").val('');
                        // discount
                        $("#discount").val('');
                        // pajak
                        $("#tax").val('');
                        // kosongkan sales
                        $("#listCart").empty();
                    }

                },
                error: function(jqXHR, testStatus, error) {
                    popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                    doBeforeSend(false)

                    // aktifkan ajax ambil meja
                    $("#setEdit").val("0");
                    // member
                    $("#membership_code").val('');
                    // nama pemesan
                    // $("#customer").val('');
                    // discount
                    $("#discount").val('');
                    // pajak
                    $("#tax").val('');
                    // kosongkan sales
                    $("#listCart").empty();
                    doBeforeSend(false)
                },
                complete: function() {
                    // selesai
                    doBeforeSend(false)
                },
                timeout: 8000,
            });
        });
    };

    // controller getData Cart on DB
    // const selectTable = () => {
    //     $("#table").on("change", function(e) {
    //         if ($("#setEdit").val() == 0) {
    //             let href = "/sales/getCart/";
    //             $.ajaxSetup({
    //                 headers: {
    //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    //                 },
    //             });
    //             $.ajax({
    //                 url: href,
    //                 method: "POST",
    //                 data: {
    //                     id: this.value,
    //                 },
    //                 beforeSend: function() {
    //                     doBeforeSend(true)
    //                 },
    //                 success: function(result) {
    //                     if (result.status == "success") {
    //                         // if need confirmation alert
    //                         // swal
    //                         //    .fire({
    //                         //    title: "Warning",
    //                         //    text: "Status Meja terpakai, apakah anda yakin menampilkan data, data yang sebelumnya terinput akan hilang?",
    //                         //    icon: "warning",
    //                         //    showCancelButton: true,
    //                         //    confirmButtonColor: "#20C997",
    //                         //    cancelButtonColor: "#d33",
    //                         //    confirmButtonText: "Tampilkan Data",
    //                         //    cancelButtonText: "Abaikan !",
    //                         //    })
    //                         //    .then((res) => {
    //                         //    if (res.isConfirmed) {

    //                         //    }
    //                         // });
    //                         let cart = result.cart;
    //                         let cart_details = result.cart_details;
    //                         // member
    //                         $("#membership_code").val(cart.membership_code);
    //                         // nama pemesan
    //                         // $("#customer").val(cart.customer);
    //                         // discount
    //                         $("#discount").val(cart.discount);
    //                         // pajak
    //                         $("#tax").val(cart.tax);
    //                         // kosongkan cart
    //                         $("#listCart").empty();
    //                         //
    //                         console.log(cart_detail);
    //                         // addToCart(cart.item_id, null,'nama','sell_price')
    //                         cart_details.forEach((cart_detail) => {
    //                             let lastClass = $("#listCart .card-cart:last ")
    //                                 .attr(
    //                                     "id");
    //                             let i = 0;
    //                             if (lastClass === undefined) {
    //                                 i = 1;
    //                             } else {
    //                                 i = parseInt(lastClass.substr(lastClass
    //                                     .length -
    //                                     1)) + 1;
    //                             }
    //                             addToCart(
    //                                 cart_detail.item_id,
    //                                 cart_detail.item_code,
    //                                 cart_detail.item_name,
    //                                 cart_detail.sell_price
    //                             );
    //                             // quantity
    //                             $("#quantity" + i).val(cart_detail.quantity);

    //                             // sub_total
    //                             $("#sub_total_" + i).val(cart_detail.sub_total);
    //                             calcSum(i);
    //                         });
    //                         calcGrand();
    //                     }
    //                     if (result.status == "error") {
    //                         $("#membership_code").val("");
    //                         // nama pemesan
    //                         // $("#customer").val("");
    //                         // discount
    //                         $("#discount").val("0");
    //                         // pajak
    //                         $("#tax").val("0");
    //                         $("#listCart").empty();
    //                         calcGrand();
    //                         // tampilan jika error respons json
    //                     }
    //                 },
    //                 error: function(jqXHR, testStatus, error) {
    //                     // tampilkan jika error respon server
    //                     popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

    //                     doBeforeSend(false)
    //                 },
    //                 complete: function() {
    //                     // selesai
    //                     doBeforeSend(false)
    //                 },
    //                 timeout: 8000,
    //             });
    //         }
    //     });
    // };
    // controller setPrice on selectSalesCategory
    const selectSalesCategory = () => {
        $("#sales_category").on("change", function(e) {
            if ($("#setEdit").val() == 0) {
                $('.card-cart').remove();
                calcGrand();
                getItemClick();

            }
        });
    };

    // controller autosave
    const autoSave = (delay) => {
        // setInterval(() => {
        if (
            $("#table").val() != "-1" &&
            // $("#customer").val() != "" &&
            $("#setEdit").val() == "0"
        ) {
            let action = "simpan";
            let href = "/sales/store/" + action;
            let formData = $("form").serialize();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: href,
                method: "POST",
                data: formData,
                beforeSend: function() {
                    doBeforeSend(true)
                },
                success: function(res) {
                    if (res.status == "success") {
                        // jika sukses
                        popToast('success', 'Tersimpan Otomatis');

                    } else {
                        //jika respon gagal
                        if (res.code == "E017") {
                            Swal.fire(
                                res.status == "success" ? "Berhasil !" : "Gagal !",
                                res.message,
                                res.status
                            );
                        } else {
                            Swal.fire(
                                res.status == "success" ? "Berhasil !" : "Gagal !",
                                res.message,
                                res.status
                            );
                        }
                    }
                },
                error: function(jqXHR, testStatus, error) {
                    //if ajax failed

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
        // }, delay);
    };
        
    // controller add Cart item
    const addToCart = (id, code, name, sell_price) => {
        let lastClass = $("#listCart .card-cart:last ").attr("id");
        let checkTable = $("#table").val();
        var tipe_penjualan = $("#tipe_penjualan").val();

        // if(checkTable){

            let i = 0;
            if (lastClass === undefined) {
                i = 1;
            } else {
                i = parseInt(lastClass.substr(lastClass.length - 1)) + 1;
            }

            cart =
                '<div class="card-cart card-cart-' +
                i +
                '" id="card-cart-' +
                i +
                '" data-id="' +
                code +
                '" data-item-id="' +
                id +
                '"><div class="card">';
            cart += '<div class="card-body">';
            cart +=
                '<input type="hidden" name="item_id[' +
                i +
                ']" id="item_id_' +
                i +
                '" value="' +
                id +
                '">';
            cart +=
                '<input type="hidden" class="" name="sub_total[' +
                i +
                ']" id="sub_total_' +
                i +
                '" value="0">';
            cart += '<div class="row align-items-center justify-content-between">';
            cart += '<div class="col-sm-2 col-md-2">';
            cart += '<div class="text-center">';
            cart += '<div src="#" style="background-image: url(/img/items/' + id + ');background-repeat: no-repeat;background-size: cover; aspect-ratio: 1 / 1;" class="profile-user-img  img-circle text-center img-fluid img-items p-3 m-1"></div>';
            cart += "</div>";
            cart += "</div>";
            cart += '<div class="col-md-6 col-sm-6">';
            cart += '<div class="row align-items-center justify-content-between">';
            cart += '<div class="col-md-12 col-md-12">';
            cart += '<p class="h6 py-0 m-0 text-wrap text-uppercase"> ' + name + " </p>";
            cart += '<span class="text-bold text-olive py-0 m-0">Rp. </span>';
            cart +=
                '<input type="hidden" name="sell_price[' +
                i +
                ']" id="sell_price_' +
                i +
                '" value="' +
                sell_price +
                '">';
            cart +=
                '<span class="text-bold text-olive uang py-0 m-0" >' +
                sell_price +
                "</span>";
            cart += '<span class="text-bold text-olive py-0 m-0">,00</span>';
            cart += "</div>";
            cart += "</div>";
            cart += "</div>";
            cart += '<div class="col-4 float-right">';
            cart += '<div class="row float-right">';
            cart += '<div class="col-12 ">';
            cart += '<div class="d-flex justify-content-around align-items-stretch">';
            cart +=
                '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty(' +
                i +
                ",'minus','"+id+"',"+sell_price+")\" data-id=\"" +
                i +
                '"> <i class="fas fa-minus-circle"></i></button>';
            cart +=
                '<input class="form-control form-control-sm col-sm-5 col-md-5" id="quantity' +
                i +
                '" name="quantity[' +
                i +
                ']" value="0" readonly required>';
            cart +=
                '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty(' +
                i +
                ",'plus','"+id+"',"+sell_price+")\" data-id=\"" +
                i +
                '"> <i class="fas fa-plus-circle"></i></button>';
            cart += "</div>";
            cart += "</div>";
            cart += "</div>";
            cart += "</div>";
            cart += "</div>";
            cart += '<hr class="py-1 my-2">';
            cart += '<div class="row align-items-center justify-content-between">';
            cart += '<div class="col-sm-1 col-md-1">';
            cart +=
                '<button type="button" class="btn btn-outline-danger btn-sm remove-cart" onclick="removeCart(\'card-cart-' +
                i +
                '\',' + i + ',\''+code+'\',\''+id+'\')"> <i class="fas fa-trash"></i></button>';
            cart += "</div>";
            cart += '<div class="col-sm-5 col-md-5">';
            cart += '<input type="text" class="form-control form-control-sm ml-1" onchange="setCatatan('+id+', '+parseInt($("#quantity" + i).val())+', '+sell_price+', '+i+')"';
            cart +=
                'name="catatan[' + i + ']" id="catatan_' + i + '" placeholder="Catatan">';
            cart += "</div>";
            cart += '<div class="col-md-6 col-sm-6 align-items-center ">';
            cart +=
                '<span class="h6 text-bold text-olive float-right pl-1" id="sub_total_view' +
                i +
                '">&nbsp;Rp. 0,00</span>';
            cart += "</div>";
            cart += "</div>";
            cart += "</div>";
            cart += "</div></div>";

            var qty_now = 0;
            var item_id = id;
            if (existCart(i, code)) {
                let exist = $("[data-id=" + code + "]")[0].id;
                let id = parseInt(exist.substr(exist.length - 1));
                let desc = $("#catatan_" + id).val();
                adjQty(id, "plus", "'"+item_id+"'", sell_price, desc);
                var qty = parseInt($("#quantity" + id).val());
            } else {
                $("#listCart").append(cart);
                let exist = $("[data-id=" + code + "]")[0].id;
                let id = parseInt(exist.substr(exist.length - 1));
                let desc = $("#catatan_" + id).val();
                adjQty(id, "plus", "'"+item_id+"'", sell_price, desc);
                var qty = parseInt($("#quantity" + id).val());
            }

            $('#no_meja_'+checkTable).attr('class', 'btn btn-flat btn-primary btn-sm btn-block btn-active');
            $('#no_meja_'+checkTable).attr('onclick', 'setMeja(this.id, '+checkTable+', \'active\')');

        // } else {
        //     popToast('error', 'Mohon pilih nomor meja terlebih dahulu');
        // }
    };

    // controller merge cart Item has been seet
    const existCart = (dataLength, code) => {
        const listId = [];

        for (let index = 0; index <= dataLength; index++) {
            let lastClass = $("#listCart .card-cart-" + index).attr("data-id");
            if (lastClass !== undefined) {
                listId[index] = lastClass;
            }
        }
        let existArray = listId.includes(code);
        return existArray;
    };

    // controller remove cart perItam
    const removeCart = (selector, id, code, item_id) => {
        let table = $("#table").val();
        var tipe_penjualan = $("#tipe_penjualan").val();

        $(document).on('click', '#' + selector + ' .remove-cart', function(event) {
            swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin menghapus data tersebut?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Hapus !",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    let href = "/sales/removeCart/";
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: href,
                        method: "POST",
                        data: {
                            type    : tipe_penjualan,
                            table   : table,
                            item_id : item_id
                        },
                        beforeSend: function() {
                            doBeforeSend(true)
                        },
                        success: function(result) {
                            popToast('success', 'S003 - Berhasil hapus data');

                            $("#" + selector).remove();
                            calcGrand();

                            $('#no_meja_'+table).attr('class', 'btn btn-flat btn-primary btn-sm btn-block btn_no_meja');
                        },
                        error: function(jqXHR, testStatus, error) {
                            // tampilkan jika error respon server
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
            });
        });
    };

    // controller set Quantity
    const adjQty = (id, type, item_id, sell_price, desc) => {
        let qty = parseInt($("#quantity" + id).val());
        let checkTable = $("#table").val();
        var tipe_penjualan = $("#tipe_penjualan").val();

        var new_qty = 0;
        switch (type) {
            case "minus":
                if (qty > 0) {
                    $("#quantity" + id).val(qty - 1);
                    new_qty = qty - 1;
                    calcSum(id);
                } else {
                    popToast('error', 'Kuantitas sudah 0, Harap Tambahkan Kuantitas Item ');
                }
            break;

            case "plus":
                $("#quantity" + id).val(qty + 1);
                new_qty = qty + 1;
                calcSum(id);
            break;
        }

        // let href = "/sales/storeCart/";
        // $.ajaxSetup({
        //     headers: {
        //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        //     },
        // });
        // $.ajax({
        //     url: href,
        //     method: "POST",
        //     data: {
        //         type        : tipe_penjualan,
        //         table       : checkTable,
        //         item_id     : item_id,
        //         quantity    : new_qty,
        //         sell_price  : sell_price,
        //         sub_total   : sell_price*new_qty,
        //         description : desc
        //     },
        //     beforeSend: function() {
        //         doBeforeSend(true)
        //     },
        //     success: function(result) {
        //         console.log('Success')
        //     },
        //     error: function(jqXHR, testStatus, error) {
        //         // tampilkan jika error respon server
        //         popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

        //         doBeforeSend(false)
        //     },
        //     complete: function() {
        //         // selesai
        //         doBeforeSend(false)
        //     },
        //     timeout: 8000,
        // });
    };

    // calculation all compotition
    const calcGrand = () => {
        let table = $("#table").val();
        let subGrandTotal = parseInt(0);
        let lastClass = $("#listCart .card-cart:last ").attr("id");

        let i = 0;
        if(lastClass === undefined){
            i = 1;
        } else {
            i = parseInt(lastClass.substr(lastClass.length - 1)) + 1;
        }

        for(let index = 0; index <= i; index++){
            let checkNAN = parseInt($("#sub_total_" + index).val());
            let sub_total = checkNAN ? checkNAN : 0;

            subGrandTotal = subGrandTotal + sub_total;
        }

        let disc = $("#discount").val() / 100;
        let tax = $("#tax").val() / 100;
        let summary = subGrandTotal - subGrandTotal * disc;
        let sumTax = summary + summary * tax;
        $("#sub_grand_total").val(subGrandTotal);

        if(table){
            let href = "/sales/chart/tax_discount";
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: href,
                method: "POST",
                data: {
                    table   : table,
                    disc    : $("#discount").val(),
                    tax     : $("#tax").val(),
                    disc_rp : subGrandTotal * disc,
                    tax_rp  : summary * tax
                },
                beforeSend: function() {
                    doBeforeSend(true)
                },
                success: function(result) {
                    console.log('Success')
                },
                error: function(jqXHR, testStatus, error) {
                    // tampilkan jika error respon server
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

        $("#total").val(sumTax);
        maskRupiah("#discount_view", subGrandTotal * disc);
        maskRupiah("#tax_view", summary * tax);
        maskRupiah("#sub_grand_total_view", subGrandTotal);
        maskRupiah("#total_view", sumTax);
    };

    // calculation item per cart
    const calcSum = (id) => {
        let qty = $("input#quantity" + id).val();
        let sell_price = $("input#sell_price_" + id).val();
        let sub_total = parseInt(qty) * parseInt(sell_price);

        $("#sub_total_" + id).val(sub_total);
        if (qty == 0) {
            $("#sub_total_view" + id).text("Rp. 0,00");
        } else {
            maskRupiah("#sub_total_view" + id, sub_total);
        }
        calcGrand();
    };

    // controller  set tax
    var countTaxItem = 0;
    var countTaxJasa = 0;
    const setTax = (id, value) => {
        if(id == 'tax_item'){
            if(countTaxItem > 0){
                $('#tax_item').attr('class', 'btn btn-outline-success btn-xs btn-block btn-tax');

                let tax = $("#tax").val(0);
                calcGrand();

                countTaxItem = 0;
            }else {
                $('#tax_jasa').attr('class', 'btn btn-outline-success btn-xs btn-block btn-tax');
                $('#'+id).attr('class', 'btn btn-success btn-xs btn-block btn-tax');

                let tax = $("#tax").val(value);
                calcGrand();

                countTaxJasa = 0;
                countTaxItem += 1;
            }
        } else {
            if(countTaxJasa > 0){
                $('#tax_jasa').attr('class', 'btn btn-outline-success btn-xs btn-block btn-tax');

                let tax = $("#tax").val(0);
                calcGrand();
                
                countTaxJasa = 0;
            }else {
                $('#tax_item').attr('class', 'btn btn-outline-success btn-xs btn-block btn-tax');
                $('#'+id).attr('class', 'btn btn-success btn-xs btn-block btn-tax');

                let tax = $("#tax").val(value);
                calcGrand();

                countTaxItem = 0;
                countTaxJasa += 1;
            }
        }
    };

    const setTipePenjualan = (id, value) => {
        $("#tipe_penjualan").val(value);

        if(value == 'GO'){
            $('#no_meja_um').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm');
            $('#no_meja_gr').attr('class', 'btn btn-flat btn-outline-info btn-block btn-sm');
            $('#no_meja_sp').attr('class', 'btn btn-flat btn-outline-warning btn-block btn-sm');
            $('.btn_no_meja').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja');
            $('#'+id).attr('class', 'btn btn-flat btn-success btn-block btn-sm');
        } else if(value == 'GR') {
            $('#no_meja_um').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm');
            $('#no_meja_go').attr('class', 'btn btn-flat btn-outline-success btn-block btn-sm');
            $('#no_meja_sp').attr('class', 'btn btn-flat btn-outline-warning btn-block btn-sm');
            $('.btn_no_meja').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja');
            $('#'+id).attr('class', 'btn btn-flat btn-info btn-sm btn-block');
        } else if(value == 'SP') {
            $('#no_meja_um').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm');
            $('#no_meja_go').attr('class', 'btn btn-flat btn-outline-success btn-block btn-sm');
            $('#no_meja_gr').attr('class', 'btn btn-flat btn-outline-info btn-block btn-sm');
            $('.btn_no_meja').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja');
            $('#'+id).attr('class', 'btn btn-flat btn-warning btn-sm btn-block');
        } else {
            $('#no_meja_sp').attr('class', 'btn btn-flat btn-outline-warning btn-block btn-sm');
            $('#no_meja_go').attr('class', 'btn btn-flat btn-outline-success btn-block btn-sm');
            $('#no_meja_gr').attr('class', 'btn btn-flat btn-outline-info btn-block btn-sm');
            $('.btn_no_meja').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja');
            $('#'+id).attr('class', 'btn btn-flat btn-primary btn-sm btn-block');
        }

        $('.btn_no_meja').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja');
        getCartByType(value)
        getItemSearch();

        $("#meja_show").html("-");
        $("#type_show").html("");
        $("#listCart").html("");
        calcGrand();

        let nota = $("#code").val();
        checkNota(nota);
    }

    // controller  set tax
    const setMeja = (id, value, status) => {
        var tipe_penjualan = $("#tipe_penjualan").val();
        $("#meja_show").html(value);

        let typeShow = "";
        if(tipe_penjualan == 'UM'){
            typeShow = "Umum";
        } else if(tipe_penjualan == 'GO'){
            typeShow = "Gojek";
        } else if(tipe_penjualan == 'GR'){
            typeShow = "Grab";
        } else{
            typeShow = "Shopee";
        }

        $("#type_show").html(typeShow);
        $('.btn_no_meja').attr('class', 'btn btn-flat btn-outline-primary btn-block btn-sm btn_no_meja meja');
        $('.btn-active').attr('class', 'btn btn-flat btn-warning btn-block btn-sm btn-active meja');

        if(status == 'active'){
            $('#'+id).attr('class', 'btn btn-flat btn-primary btn-sm btn-block btn-active meja');
        } else {
            $('#'+id).attr('class', 'btn btn-flat btn-primary btn-sm btn-block btn_no_meja meja');
        }   

        $("#listCart").html("");
        $("#discount").val('');
        $("#tax").val('');
        $(".btn-tax").attr('class','btn btn-outline-success btn-xs btn-block btn-tax');
        // $("#tax-jasa").attr('class','btn btn-outline-success btn-xs btn-block btn-tax');

        let href = "/sales/getCart/";
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: href,
            method: "POST",
            data: {
                id: value,
                type: tipe_penjualan
            },
            cache: false,
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                if(result.status == 'success'){
                    $.each(result.cart_details, function (i, elem) {
                        cart =
                            '<div class="card-cart card-cart-' + i +
                            '" id="card-cart-' + i +
                            '" data-id="' + elem.item_code +
                            '" data-item-id="' + elem.item_id +
                            '"><div class="card">';
                        cart += '<div class="card-body">';
                        cart +=
                            '<input type="hidden" name="item_id[' + i +
                            ']" id="item_id_' + i +
                            '" value="' + elem.item_id +
                            '">';
                        cart +=
                            '<input type="hidden" class="" name="sub_total[' + i +
                            ']" id="sub_total_' + i +
                            '" value="0">';
                        cart += '<div class="row align-items-center justify-content-between">';
                        cart += '<div class="col-sm-2 col-md-2">';
                        cart += '<div class="text-center">';
                        cart += '<div src="#" style="background-image: url(/img/items/' + elem.item_id + ');background-repeat: no-repeat;background-size: cover; aspect-ratio: 1 / 1;" class="profile-user-img  img-circle text-center img-fluid img-items p-3 m-1"></div>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += '<div class="col-md-6 col-sm-6">';
                        cart += '<div class="row align-items-center justify-content-between">';
                        cart += '<div class="col-md-12 col-md-12">';
                        cart += '<p class="h6 py-0 m-0 text-wrap text-uppercase"> ' + elem.item_name + " </p>";
                        cart += '<span class="text-bold text-olive py-0 m-0">Rp. </span>';
                        cart +=
                            '<input type="hidden" name="sell_price[' +
                            i +
                            ']" id="sell_price_' +
                            i +
                            '" value="' +
                            elem.sell_price +
                            '">';
                        cart +=
                            '<span class="text-bold text-olive uang py-0 m-0" >' +
                            elem.sell_price +
                            "</span>";
                        cart += '<span class="text-bold text-olive py-0 m-0">,00</span>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += '<div class="col-4 float-right">';
                        cart += '<div class="row float-right">';
                        cart += '<div class="col-12 ">';
                        cart += '<div class="d-flex justify-content-around align-items-stretch">';
                        cart +=
                            '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty(' +
                            i +
                            ",'minus',"+elem.item_id+", "+elem.sell_price+")\" data-id=\"" +
                            i +
                            '"> <i class="fas fa-minus-circle"></i></button>';
                        cart +=
                            '<input class="form-control form-control-sm col-sm-5 col-md-5" id="quantity' +
                            i +
                            '" name="quantity[' +
                            i +
                            ']" value="'+elem.quantity+'" readonly required>';
                        cart +=
                            '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty(' +
                            i +
                            ",'plus', "+elem.item_id+", "+elem.sell_price+")\" data-id=\"" +
                            i +
                            '"> <i class="fas fa-plus-circle"></i></button>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += '<hr class="py-1 my-2">';
                        cart += '<div class="row align-items-center justify-content-between">';
                        cart += '<div class="col-sm-1 col-md-1">';
                        cart +=
                            '<button type="button" class="btn btn-outline-danger btn-sm remove-cart" onclick="removeCart(\'card-cart-' +
                            i +
                            '\',' + i + ',\''+elem.item_code+'\','+elem.item_id+')"> <i class="fas fa-trash"></i></button>';
                        cart += "</div>";
                        cart += '<div class="col-sm-5 col-md-5">';
                        cart += '<input type="text" class="form-control form-control-sm ml-1" value="'+elem.description+'" onchange="setCatatan('+elem.item_id+', '+elem.quantity+', '+elem.sell_price+', '+i+')"';
                        cart +=
                            'name="catatan[' + i + ']" id="catatan_' + i + '" placeholder="Catatan">';
                        cart += "</div>";
                        cart += '<div class="col-md-6 col-sm-6 align-items-center ">';
                        cart +=
                            '<span class="h6 text-bold text-olive float-right pl-1" id="sub_total_view' +
                            i +
                            '">&nbsp;Rp. 0,00</span>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div></div>";

                        $("#listCart").append(cart);
                        let exist = $("[data-id=" + elem.item_code + "]")[0].id;
                        let id = parseInt(exist.substr(exist.length - 1));
                        calcSum(i);
                    })

                    if(result.cart.disc){
                        $("#discount").val(result.cart.disc);
                    }
                    
                    if(result.cart.tax){
                        $("#tax").val(result.cart.tax);
                        $(".btn-tax[value="+result.cart.tax+"]").attr('class','btn btn-success btn-xs btn-block btn-tax');
                    }

                    calcGrand();
                }
            },
            error: function(jqXHR, testStatus, error) {
                // tampilkan jika error respon server
                popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');

                doBeforeSend(false)
            },
            complete: function() {
                // selesai
                doBeforeSend(false)
            },
            timeout: 8000,
        });

        let nota = $("#code").val();
        checkNota(nota);

        let tax = $("#table").val(value);
    };

    // controller store new Item
    const storeItem = () => {
        $(document).on('click', '#saveButton', function(event) {
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
                url: "/dataInduk/item/store/",
                method: "POST",
                cache:false,
                data: form,
                contentType: false,
                processData: false,
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
                    location.reload();
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
    }

    function changeType(val) {
        if(val != 'Item Jadi'){
            $(".switch").css("display", "none");
            $("#detail-bahan").css("display", "none");
        } else {
            $(".switch").css("display", "inline");
            $("#detail-bahan").css("display", "inline");
        }
    }

    function getSwitch(params) {
        if(params == false){
            $("#detail-bahan").css("display", "none");
        } else {
            $("#detail-bahan").css("display", "inline");
        }
    }

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

    const setCatatan = (item_id, quantity, sell_price, id) => {
        let value = $("input#catatan_" + id).val();
        let checkTable = $("#table").val();

        let href = "/sales/storeCart/";
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: href,
            method: "POST",
            data: {
                table       : checkTable,
                item_id     : item_id,
                quantity    : quantity,
                sell_price  : sell_price,
                sub_total   : sell_price*quantity,
                description : value
            },
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                console.log('Success')
            },
            error: function(jqXHR, testStatus, error) {
                // tampilkan jika error respon server
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

    const checkNota = (nota) => {
        let href = "/sales/checkNota/";
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: href,
            method: "POST",
            data: {
                nota: nota
            },
            cache: false,
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                console.log(result);
                if(result.status == "success"){
                    $("#listCart").html("");
                    $("#discount").val('');
                    $("#tax").val('');

                    $("#append-revisi-button").html(`<button type="button" class="btn btn-sm btn-info" onclick="revisiNota()">Revisi</button>`);
                    $.each(result.sales_details, function (i, elem) {
                        console.log(i)
                        cart =
                            '<div class="card-cart card-cart-' +
                            i +
                            '" id="card-cart-' +
                            i +
                            '" data-id="' +
                            elem.item_code +
                            '" data-item-id="' +
                            elem.item_id +
                            '"><div class="card">';
                        cart += '<div class="card-body">';
                        cart +=
                            '<input type="hidden" name="item_id[' +
                            i +
                            ']" id="item_id_' +
                            i +
                            '" value="' +
                            elem.item_id +
                            '">';
                        cart +=
                            '<input type="hidden" class="" name="sub_total[' +
                            i +
                            ']" id="sub_total_' +
                            i +
                            '" value="0">';
                        cart += '<div class="row align-items-center justify-content-between">';
                        cart += '<div class="col-sm-2 col-md-2">';
                        cart += '<div class="text-center">';
                        cart += '<div src="#" style="background-image: url(/img/items/' + elem.item_id + ');background-repeat: no-repeat;background-size: cover; aspect-ratio: 1 / 1;" class="profile-user-img  img-circle text-center img-fluid img-items p-3 m-1"></div>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += '<div class="col-md-6 col-sm-6">';
                        cart += '<div class="row align-items-center justify-content-between">';
                        cart += '<div class="col-md-12 col-md-12">';
                        cart += '<p class="h6 py-0 m-0 text-wrap text-uppercase"> ' + elem.item_name + " </p>";
                        cart += '<span class="text-bold text-olive py-0 m-0">Rp. </span>';
                        cart +=
                            '<input type="hidden" name="sell_price[' +
                            i +
                            ']" id="sell_price_' +
                            i +
                            '" value="' +
                            elem.sell_price +
                            '">';
                        cart +=
                            '<span class="text-bold text-olive uang py-0 m-0" >' +
                            elem.sell_price +
                            "</span>";
                        cart += '<span class="text-bold text-olive py-0 m-0">,00</span>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += '<div class="col-4 float-right">';
                        cart += '<div class="row float-right">';
                        cart += '<div class="col-12 ">';
                        cart += '<div class="d-flex justify-content-around align-items-stretch">';
                        cart +=
                            '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty(' +
                            i +
                            ",'minus',"+elem.item_id+", "+elem.sell_price+")\" data-id=\"" +
                            i +
                            '"> <i class="fas fa-minus-circle"></i></button>';
                        cart +=
                            '<input class="form-control form-control-sm col-sm-5 col-md-5" id="quantity' +
                            i +
                            '" name="quantity[' +
                            i +
                            ']" value="'+elem.quantity+'" readonly required>';
                        cart +=
                            '<button type="button" class="btn btn-danger btn-xs mx-1" onClick="adjQty(' +
                            i +
                            ",'plus', "+elem.item_id+", "+elem.sell_price+")\" data-id=\"" +
                            i +
                            '"> <i class="fas fa-plus-circle"></i></button>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += '<hr class="py-1 my-2">';
                        cart += '<div class="row align-items-center justify-content-between">';
                        cart += '<div class="col-sm-1 col-md-1">';
                        cart +=
                            '<button type="button" class="btn btn-outline-danger btn-sm remove-cart" onclick="removeCart(\'card-cart-' +
                            i +
                            '\',' + i + ',\''+elem.item_code+'\','+elem.item_id+')"> <i class="fas fa-trash"></i></button>';
                        cart += "</div>";
                        cart += '<div class="col-sm-5 col-md-5">';
                        cart += '<input type="text" class="form-control form-control-sm ml-1" value="'+elem.description+'" onchange="setCatatan('+elem.item_id+', '+elem.quantity+', '+elem.sell_price+', '+i+')"';
                        cart +=
                            'name="catatan[' + i + ']" id="catatan_' + i + '" placeholder="Catatan">';
                        cart += "</div>";
                        cart += '<div class="col-md-6 col-sm-6 align-items-center ">';
                        cart +=
                            '<span class="h6 text-bold text-olive float-right pl-1" id="sub_total_view' +
                            i +
                            '">&nbsp;Rp. 0,00</span>';
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div>";
                        cart += "</div></div>";

                        $("#listCart").append(cart);
                        calcSum(i);
                    })

                    if(result.sales.disc){
                        $("#discount").val(result.sales.disc);
                    }
                    
                    if(result.sales.tax){
                        $("#tax").val(result.sales.tax);
                        $(".btn-tax[value="+result.sales.tax+"]").attr('class','btn btn-success btn-xs btn-block btn-tax');
                    }

                    if(result.sales.table){
                        $("#table").val(result.sales.table)
                    }
                    calcGrand();
                } else {
                    $("#append-revisi-button").html(``);
                }
            },
            error: function(jqXHR, testStatus, error) {
                // tampilkan jika error respon server
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

    const revisiNota = () => {
        let href = "/sales/revisiNota/";
        let nota = $("#code").val();
        
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: href,
            method: "POST",
            data: {
                nota: nota
            },
            cache: false,
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(result) {
                console.log(result);
                if(result.status == "success"){
                    let newVal = "{{ Helper::docPrefix('sales') }}";

                    popToast('success', 'Berhasil revisi nota');
                    $("#append-revisi-button").html(``);
                    $("#code").val(newVal)

                    $("button#no_meja_"+result.table).click();
                }
            },
            error: function(jqXHR, testStatus, error) {
                // tampilkan jika error respon server
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

    // Save Data
    const saveData = (action) => {

        if ((!checkOrder()) || (!checkOrder() === 'undefiined')) {
            return checkOrder();
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        let checkTable = $("#table").val();
        let href       = "/sales/store/" + action;
        let formData   = $("form").serialize();
        let pay        = $("#setCashBack").unmask().val();
        let total      = $("#total").unmask().val();
        let pMethod    = $("#pMethod").val();
        let cashBack   = pay - total;
        formData       = formData + '&pay='+ pay;
        formData       = formData + '&cashBack='+ cashBack;
        formData       = formData + '&pMethod='+ pMethod;

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
            denyButtonText: action == "simpan" ? "Batal" : "Cetak Sementara",
            cancelButtonText: "Batal",
            onBeforeOpen () {
                setPay('tunai','')
            },
            html: `
            <div class="row py-2" style="background-color: #2e5781; border-radius: 6px;">
                <div class="col-sm-3 col-6"></div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                        <span class="description-text text-white">TOTAL PENJUALAN</span>
                        <p class="text-secondary"></p>
                        <h1 class="description-header text-white" id="monthlySales">${maskRupiah("", $("#total").val())}</h1>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->

                <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                        <span class="description-text text-white">TOTAL DIBAYAR</span>
                        <p class="text-secondary"></p>
                        <input type="text" class="form-control form-control uang text-white" oninput="setCashBack()" placeholder="Bayar" id="setCashBack" name="setCashBack" style="background-color: transparent; font-size: 17px; font-weight: bold; text-align: center; margin-top: -10px; border: none;" autofocus required>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                                
                <div class="col-sm-3 col-6">
                    <div class="description-block">
                        <span class="description-text text-white">TOTAL KEMBALIAN</span>
                        <p class="text-secondary"></p>
                        <input type="text" class="form-control form-control text-white" placeholder="Kembali" id="cashBack" style="background-color: transparent; font-size: 17px; font-weight: bold; text-align: center; margin-top: -10px; border: none;" readonly>
                    </div>
                    <!-- /.description-block -->
                </div>
            </div>

            <div class="row py-2">
                <div class="col-sm-3" style="background-color: #c5c5c521; padding: 12px; height: 50vh">
                    <button class="btn bg-lime btn-sm btn-block" id="setTunai" onclick=setPay('tunai','') style="padding: 25px;height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781">
                        <b>Tunai</b>
                    </button>
                    <button class="btn bg-lime btn-sm btn-block" id="setNonTunai" onclick=nonTunai() style="padding: 25px; height: 14vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781">
                        <b>Non Tunai</b>
                    </button>
                    <button class="btn bg-lime btn-sm btn-block" id="setOnline" onclick=onlinePayment() style="padding: 25px; height: 15vh;color: #2e5781 !important;background-color: #dbdbdb17 !important; border: 1px solid #2e5781">
                        <b>Pembayaran Online</b>
                    </button>
                </div>
                <div class="col-sm-9" id="payment" style="padding: 12px; height: 50vh"></div>
            </div>
            <input type="hidden" class="form-control form-control" id="pMethod"  name="pMethod" value="Tunai" readonly>`,
        }).then((resultSwal1) => {
            let pay = $("#setCashBack").unmask().val();
            let total = $("#total").unmask().val();
            let cashBack = parseInt(pay) - parseInt(total);
            let pMethod = $("#pMethod").val();
            formData = formData + '&pay='+ pay;
            formData = formData + '&cashBack='+ cashBack;
            formData = formData + '&pMethod='+ pMethod;
            if (resultSwal1.isConfirmed && parseInt(pay) >= parseInt(total)) {
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

            if (resultSwal1.isDenied) {
                if (action == "bayar") {
                    window.open("{{ url('') }}/sales/print/sementara/" +checkTable);
                }
            }

            if (
                (resultSwal1.isConfirmed && parseInt(pay) < parseInt(total)) ||
                (resultSwal1.isDenied && parseInt(pay) < parseInt(total))
            ) {
                Swal.fire("Gagal !", "Uang bayar kurang", "error");
            }

            if(parseInt(pay) < parseInt(total)){
                console.log("Bayar kurang dari total")
            }
        });
    };

</script>
@endsection