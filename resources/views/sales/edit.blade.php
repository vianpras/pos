@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content" style="min-height: 40vh; max-height: 40vh" id="wrapper">
            <form id='SalesForm'>
                @csrf
                <div class="row p-1 m-1" style="min-height: 1vh; max-height: 1vh">
                    <div class="col-7 col-md-7 col-sm-7 p-1">
                        <div class="card m-1">
                            <div class="card-body m-0 p-2">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="membership_code">Kode</label>
                                            <input type="text" class="form-control form-control-sm" name="code" id="code"
                                                placeholder="Kode Transaksi" value={{ $data->code }} required disabled>
                                            <input type="hidden" name="setEdit" id="setEdit" value='1'>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="table">Meja</label>
                                            <input type="text" class="form-control form-control-sm" name="table" id="table"
                                                placeholder="Meja" value="Meja {{ $data->table }}" required disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="membership_code">Member</label>
                                            <input type="text" class="form-control form-control-sm" name="membership_code"
                                                id="membership_code" placeholder="ID Member"
                                                value="{{ $data->membership_code }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="customer">Nama Pemesan</label>
                                            <input type="text" class="form-control form-control-sm" name="customer"
                                                id="customer" placeholder="Nama Pemesan" value="{{ $data->customer }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="membership_code">Diskon <span
                                                    class="text-teal">(%)</span></label>
                                            <input type="number" class="form-control form-control-sm" name="discount"
                                                id="discount" value="{{ $data->discount }}" step="0.01"
                                                placeholder="Diskon" onkeypress="calcGrand()" onkeyup="calcGrand()"
                                                onchange="calcGrand()" min="0" max="100" maxlength="1"
                                                ondrop="return false;" onpaste="return false;" autocomplete="off"
                                                value="{{ $data->discount }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="tax">Pajak</label>
                                            <div class="row align-item-center justify-content-arround">
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-outline-success btn-xs"
                                                        id="tax_item" value="10" onclick="setTax(10)"> Tax 10%</button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-outline-success btn-xs"
                                                        id="tax_jasa" value="2" onclick="setTax(2)">Tax 2%</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card m-1" style="min-height: 65.5vh; max-height: 65.5vh">
                            <div class="card-header m-0 p-2">
                                <div class="input-group">
                                    <input type="search_item" name="search_item" id="search_item"
                                        class="form-control form-control-sm" placeholder="Masukkan Nama Item" autofocus>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-sm btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                {{-- list category will be load here --}}
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
                                        @foreach ($categories as $key => $category)
                                            <div class="mx-1 px-1 pt-3 col-sm-3 col-xs-6 ">
                                                <button type="button" class="btn bg-teal btn-flat btn-block btn-sm"
                                                    onclick="getItemClick(this)"
                                                    id="{{ $category->name }}">{{ $category->name }}</button>
                                            </div>
                                        @endforeach
                                        <div class="mx-1 px-1 pt-3 col-sm-3 col-xs-6 ">
                                            <button type="button" class="btn bg-indigo btn-flat btn-block btn-sm"
                                                onclick="newItem()" id="newButton">Item Baru</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body m-0 p-2">
                                {{-- item will be load here --}}
                                <div class="scrollYMenu">
                                    <div class="row align-item-center justify-content-around" id="listMenu">
                                        @foreach ($items as $key => $item)
                                            <div class="col-sm-3 col-md-3 color-palette shadow m-1"
                                                style="background-color: #3F474E; border-radius: 0.5em;"
                                                onclick="addToCart('{{ $item->id }}','{{ $item->code }}','{{ $item->name }}','{{ $item->sell_price }}')">
                                                <div
                                                    class="row d-flex flex-column align-item-center justify-content-arround">
                                                    <div class="d-flex justify-content-center align-items-center mt-2">
                                                        <img src="/img/items/{{ $item->id }}"
                                                            class="profile-user-img  img-circle text-center">
                                                    </div>
                                                    <div class="row d-flex justify-content-center align-items-center p-2 ">
                                                        <div class="col-12 pt-2">
                                                            <p class="text-wrap text-uppercase text-bold text-center m-0">
                                                                {{ $item->name }}
                                                            </p>
                                                        </div>
                                                        <div class="col-12 text-center m-0">
                                                            <h5>
                                                                <span class="badge bg-teal text-grey">
                                                                    {{ Helper::formatNumber($item->sell_price, 'rupiah') }}</span>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 col-md-5 col-sm-5 p-1">
                        <div class="card m-1">
                            <div class="card-header  m-0 p-2 align-items-center justify-content-center">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" name="total" id="total" value="{{ $data->total }}">
                                        <span
                                            class="h6 m-0 float-left text-secondary">#{{ Helper::docPrefix('sales') }}</span>
                                    </div>
                                    <div class="col">
                                        <span class="h4 m-0 float-right text-olive"
                                            id="total_view">{{ Helper::formatNumber($data->total, 'rupiah') }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" name="sub_grand_total" id="sub_grand_total" value="0">
                                        <span class="h5 m-0 float-left text-secondary">Sub Total</span>
                                    </div>
                                    <div class="col">
                                        <span class="h5 m-0 float-right text-secondary"
                                            id="sub_grand_total_view">{{ Helper::formatNumber($data->sub_total, 'rupiah') }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <span class="h5 m-0 float-left text-secondary">Diskon</span>
                                    </div>
                                    <div class="col">
                                        <span class="h5 m-0 float-right text-secondary"
                                            id="discount_view">{{ Helper::formatNumber('0', 'rupiah') }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <span class="h5 m-0 float-left text-secondary">Pajak</span>
                                    </div>
                                    <div class="col">
                                        <input type="hidden" name="tax" id="tax" value="{{ $data->tax }}">
                                        <span class="h5 m-0 float-right text-secondary"
                                            id="tax_view">{{ Helper::formatNumber('0', 'rupiah') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body  m-0 p-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group" id="updateSales">
                                            <button type="button" onclick="updateData('simpan')"
                                                class="btn btn-flat btn-block bg-teal">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer m-0 p-2">
                                <div class="scrollYMenu pr-1" style="height:60vh" id="listCart">
                                    @foreach ($sales_details as $key => $sales_detail)
                                        <div class="card-cart card-cart-{{ $key + 1 }}"
                                            id="card-cart-{{ $key + 1 }}" data-id="{{ $sales_detail->code }}"
                                            data-item-id="{{ $sales_detail->item_id }}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <input type="hidden" name="sales_detail_id[{{ $key + 1 }}]"
                                                        id="sales_detail_id_{{ $key + 1 }}"
                                                        value="{{ $sales_detail->id }}">
                                                    <input type="hidden" name="item_id[{{ $key + 1 }}]"
                                                        id="item_id_{{ $key + 1 }}"
                                                        value="{{ $sales_detail->item_id }}">
                                                    <input type="hidden" class=""
                                                        name="sub_total[{{ $key + 1 }}]"
                                                        id="sub_total_{{ $key + 1 }}"
                                                        value="{{ $sales_detail->sub_total }}">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-2 col-md-2">
                                                            <div class="text-center">
                                                                <img src="/img/items/{{ $sales_detail->item_id }}"
                                                                    class="profile-user-img img-fluid img-circle"
                                                                    alt="nama gambar">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6">
                                                            <div class="row align-items-center justify-content-between">
                                                                <div class="col-md-12 col-md-12">
                                                                    <p class="h6 py-0 m-0 text-wrap text-uppercase">
                                                                        {{ $sales_detail->name }}
                                                                    </p>
                                                                    <input type="hidden"
                                                                        name="sell_price[{{ $key + 1 }}]"
                                                                        id="sell_price_{{ $key + 1 }}"
                                                                        value="{{ $sales_detail->sell_price }}">
                                                                    <span class="text-bold text-olive py-0 m-0"
                                                                        id="sell_price_view_{{ $key + 1 }}">{{ Helper::formatNumber($sales_detail->sell_price, 'rupiah') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 float-right">
                                                            <div class="row float-right">
                                                                <div class="col-12 ">
                                                                    <div
                                                                        class="d-flex justify-content-around align-items-stretch">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-xs mx-1"
                                                                            onClick="adjQty({{ $key + 1 }},'minus')"
                                                                            data-id="{{ $key + 1 }}"> <i
                                                                                class="fas fa-minus-circle"></i></button>
                                                                        <input
                                                                            class="form-control form-control-sm col-sm-5 col-md-5"
                                                                            id="quantity{{ $key + 1 }}"
                                                                            name="quantity[{{ $key + 1 }}]"
                                                                            value="{{ $sales_detail->quantity }}"
                                                                            readonly required>
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-xs mx-1"
                                                                            onClick="adjQty({{ $key + 1 }},'plus')"
                                                                            data-id="{{ $key + 1 }}"> <i
                                                                                class="fas fa-plus-circle"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr class="py-1 my-2">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-1 col-md-1">
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm remove-cart"
                                                                onclick="removeCart('card-cart-{{ $key + 1 }}','{{ $sales_detail->id }}','{{ $sales_detail->sales_id }}','{{ $sales_detail->item_id }}')">
                                                                <i class="fas fa-trash"></i></button>
                                                        </div>
                                                        <div class="col-sm-5 col-md-5">
                                                            <input type="text" class="form-control form-control-sm ml-1"
                                                                name="catatan[{{ $key + 1 }}]"
                                                                id="catatan_{{ $key + 1 }}" placeholder="Catatan"
                                                                value="{{ $sales_detail->description }}">
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 align-items-center ">
                                                            <span class="h6 text-bold text-olive float-right pl-1"
                                                                id="sub_total_view{{ $key + 1 }}">&nbsp;{{ Helper::formatNumber($sales_detail->sub_total, 'rupiah') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                            <div class="card-footer pb-0 m-0">
                            </div>
                        </div>
                    </div>
            </form>
        </section>
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
    </div>
    {{-- ./modal Edit Data --}}
@endsection
@section('jScript')
    <script>
        // controller render card item
        const _renderCard = (id, code, name, sell_price, category_name) => {
            card = '<div class="col-sm-3 col-md-3 color-palette shadow m-1"';
            card += 'style="background-color: #3F474E; border-radius: 0.5em ;"';
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
            card += '<div class="d-flex justify-content-center align-items-center mt-2">';
            card += '<img src="/img/items/' + id + '"';
            card += 'class="profile-user-img img-fluid img-circle text-center">';
            card += "</div>";
            card +=
                '<div class="row d-flex justify-content-center align-items-center p-2 ">';
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
        };
        //  controller get item by input search
        const getItemSearch = () => {
            $(document).on("keyup", "input#search_item", function(event) {
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
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        if (result.length > 0) {
                            $("#listMenu").empty();
                            const items = result;
                            for (const item of items) {
                                _renderCard(
                                    item.id,
                                    item.code,
                                    item.name,
                                    item.sell_price,
                                    item.category_name
                                );
                            }
                        } else {
                            $("#listMenu").empty();
                            let $card =
                                "<div class='align-item-center justify-content-center'> <h6>Item Tidak Ditemukan</h6> </div>";
                            $("#listMenu").append($card);
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
            });
        };
        // controller get per Item by click category
        const getItemClick = (value) => {
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
                    search: $(value).attr("id"),
                },
                beforeSend: function() {
                    doBeforeSend(true)
                },
                success: function(result) {
                    $("#search_item").val("");
                    if (result.length > 0) {
                        const items = result;
                        $("#listMenu").empty();
                        for (const item of items) {
                            _renderCard(
                                item.id,
                                item.code,
                                item.name,
                                item.sell_price,
                                item.category_name
                            );
                        }
                    } else {
                        $("#listMenu").empty();
                        let $card =
                            "<div class='align-item-center justify-content-center'> <span class='text-muted h5'>Item Tidak Ditemukan</span> </div>";
                        $("#listMenu").append($card);
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
        // controller get member on DB
        const getDataMember = () => {
            $(document).on("keyup", "input#membership_code", function(event) {
                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                let href = "/sales/getDataMember";
                $.ajax({
                    url: href,
                    method: "POST",
                    data: {
                        code: $("input#membership_code").val(),
                    },
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        if (result.id) {
                            $("#customer").val(result.name);
                            $("#table").focus();
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
            });
        };
        // controller set payment method
        const setPay = (e) => {
            let total = formatNumber($("#total").val());
            switch (e) {
                case "debit":
                    $("#setCashBack").val(total);
                    $("#pMethod").val("Kartu debit/kredit");
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
        // calculation cashback before print
        const setCashBack = () => {
            let pay = parseInt($("#setCashBack").unmask().val());
            let total = parseInt($("#total").val());
            let cashBack = maskRupiah("", pay - total);
            $("#cashBack").val(cashBack);
            if (pay < total) {
                $("#setCashBack").addClass("is-invalid");
                $('button.swal2-confirm').attr("disabled", true);
            } else {
                $("#setCashBack").removeClass("is-invalid");
                $('button.swal2-confirm').attr("disabled", false);
            }
        };
        // controller save Data
        const cetak = (action) => {
            swal
                .fire({
                    title: action == "simpan" ? "Apakah Ingin Menyimpan" : "Pilih Pembayaran",
                    icon: "question",
                    width: 1000,
                    // showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonColor: "#20C997",
                    denyButtonColor: "#007BFF",
                    cancelButtonColor: "#DC3545",
                    confirmButtonText: action == "simpan" ? "Simpan" : "Cetak",
                    // denyButtonText: action == "simpan" ? "Batal" : "Simpan",
                    cancelButtonText: "Batal",
                    html: `
               <span class="h5 mb-4"> Total Pembayaran ${maskRupiah(
               "",
               $("#total").val()
               )}</span>
               <div class="align-item-center justify-content-center">
                  <div class="row d-flex align-item-center justify-content-arround">
                        <div class="col-3 my-1">
                           <button class="btn bg-lime btn-sm btn-block" id="setTunai" onclick=setPay('tunai')>Tunai</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn bg-navy btn-sm btn-block" id="setBCA" onclick=setPay('debit')>BCA</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn btn-primary btn-sm btn-block" id="setMANDIRI" onclick=setPay('debit')>MANDIRI</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn btn-success btn-sm btn-block" id="setGoPay" onclick=setPay('GoPay')>GoPay</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn bg-indigo btn-sm btn-block" id="setOVO" onclick=setPay('OVO')>OVO</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn bg-orange btn-sm btn-block" id="setShopeePay" onclick=setPay('ShopeePay')>ShopeePay</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn btn-info btn-sm btn-block" id="setDana" onclick=setPay('Dana')>Dana</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn btn-danger btn-sm btn-block" id="setLinkAja" onclick=setPay('LinkAja!')>LinkAja!</button>
                        </div>


                        <div class="col-3 my-1">
                           <button class="btn bg-olive btn-sm btn-block" id="pas" onclick=setPay('pas')>Bayar ${$('#total_view').text()}</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn bg-olive btn-sm btn-block" id="set50" onclick=setPay('50000')>Rp. 50.000</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn bg-olive btn-sm btn-block" id="set75" onclick=setPay('75000')>Rp. 75.000</button>
                        </div>
                        <div class="col-3 my-1">
                           <button class="btn bg-olive btn-sm btn-block" id="set100" onclick=setPay('100000')>Rp. 100.000</button>
                        </div>
                        
                  </div>
                  <div class="row mt-2">
                     <div class="form-group col-6">
                        <input type="text" class="form-control form-control uang" oninput="setCashBack()" 
                           placeholder="Bayar" id="setCashBack" value="return ${$('#total').val()}" autofocus required>
                           <span id="setCashBack-error" class="error invalid-feedback text-left">Uang Bayar Kurang</span>
                     </div>
                     <div class="form-group col-6">
                        <input type="text" class="form-control form-control"
                           placeholder="Kembali" id="cashBack" readonly>
                     </div>
                        <input type="hidden" class="form-control form-control" id="pMethod" value="Tunai" readonly>
                  </div>
               </div>
               `,
                })
                .then((resultSwal1) => {
                    let pay = $("#setCashBack").unmask().val();
                    let total = $("#total").val();
                    if (resultSwal1.isConfirmed && pay >= total) {
                        let pay = $("#setCashBack").unmask().val();
                        let total = $("#total").unmask().val();
                        let cashBack = pay - total;
                        let pMethod = $("#pMethod").val();
                        let win = window.open(
                            "{{ url('') }}/sales/print/" +
                            $('#code').val() +
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
                    }

                    if (
                        (resultSwal1.isConfirmed && pay < total) ||
                        (resultSwal1.isDenied && pay < total)
                    ) {
                        Swal.fire("Gagal !", "Uang bayar kurang", "error");
                    }
                });
        };

        // check order
        const checkOrder = () => {
            let total = parseInt($('#total').val());
            let customer = ($('#customer').val());
            if (customer == '') {
                popToast('error', 'Harap Masukkan Nama Customer');
                return false
            }
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
                let itemName = $('div#card-cart-' + index + '>div>div>div:nth-child(3)>div:nth-child(2)>div>div>p')
                    .text()
                if (!isNaN(quantity) && quantity < 1) {
                    popToast('error', 'Harap Tambahkan Kuantitas Item ' + itemName);
                    return false;
                }

            }
            return true;
        }
        // controller save data on DB
        const updateData = (action) => {

            if ((!checkOrder()) || (!checkOrder() === 'undefiined')) {
                return checkOrder();
            }
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            let href = "/sales/update/{{ $data->code }}/" + action;
            let formData = $("form").serialize();
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
                                title: res.status == "success" ? "Berhasil !" : "Gagal !",
                                text: res.message,
                                icon: res.status,
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#20C997",
                                confirmButtonText: "Edit Ulang",
                                cancelButtonText: "Cetak & Penjualan Baru",
                            })
                            .then((result) => {
                                if (result.isConfirmed) {
                                    window.location = "{{ route('sales.edit', $data->code) }}";
                                } else {
                                    cetak()
                                    // window.location = "{{ route('sales.new') }}";
                                }
                            });
                    } else {
                        Swal.fire(
                            res.status == "success" ? "Berhasil !" : "Gagal !",
                            res.message,
                            res.status
                        );
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
        // controller add Cart item
        const addToCart = (id, code, name, sell_price) => {
            let lastClass = $("#listCart .card-cart:last ").attr("id");
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
            cart +=
                '<img src="/img/items/' +
                id +
                '" class="profile-user-img img-fluid img-circle"';
            cart += 'alt="nama gambar">';
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
                ",'minus')\" data-id=\"" +
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
                ",'plus')\" data-id=\"" +
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
                '\',' + null + ')"> <i class="fas fa-trash"></i></button>';
            cart += "</div>";
            cart += '<div class="col-sm-5 col-md-5">';
            cart += '<input type="text" class="form-control form-control-sm ml-1"';
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
            if (existCart(i, code)) {
                let exist = $("[data-id=" + code + "]")[0].id;
                let id = parseInt(exist.substr(exist.length - 1));
                adjQty(id, "plus");
            } else {
                $("#listCart").append(cart);
            }
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
            $(document).on('click', '#' + selector + ' .remove-cart', function(event) {
                swal
                    .fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin menghapus data tersebut?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Hapus !",
                        cancelButtonText: "Batal",
                    })
                    .then((result) => {
                        if (result.isConfirmed) {
                            if (id == null) {
                                popToast('success', 'S003 - Berhasil ubah data');

                                $("#" + selector).remove();
                                calcGrand();

                            } else {
                                event.preventDefault();
                                let href = '/sales/removeCart/';
                                let data = {
                                    id: id,
                                    code: code,
                                    item_id: item_id,
                                };
                                
                                $.ajaxSetup({
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                            "content"),
                                    },
                                });
                                $.ajax({
                                    url: href,
                                    method: "POST",
                                    data: data,
                                    beforeSend: function() {
                                        doBeforeSend(true)
                                    },
                                    success: function(res) {
                                        if (res.status == "success") {
                                            // jika sukses
                                            Toast.fire({
                                                icon: res.status,
                                                title: res.message
                                            });
                                            $("#" + selector).remove();
                                            calcGrand();

                                        }
                                        if (res.status == "error") {
                                            Toast.fire({
                                                icon: res.status,
                                                title: res.message
                                            });
                                        }
                                    },
                                    error: function(jqXHR, testStatus, error) {
                                        //if ajax failed
                                        Toast.fire({
                                            icon: 'error',
                                            title: '&nbsp;&nbsp; E999 - Terjadi Kesalah Komunikasi Server'
                                        });
                                        doBeforeSend(false)
                                    },

                                    complete: function() {
                                        // selesai
                                        doBeforeSend(false)
                                    },
                                    timeout: 8000,
                                });
                                calcGrand();
                            }

                        }
                    });

            });
        };
        // controller set Quantity
        const adjQty = (id, type) => {
            let qty = parseInt($("#quantity" + id).val());
            switch (type) {
                case "minus":
                    if (qty > 0) {
                        $("#quantity" + id).val(qty - 1);
                        calcSum(id);
                    } else {
                        popToast('error', 'Kuantitas sudah 0, Harap Tambahkan Kuantitas Item ');
                    }
                    break;
                case "plus":
                    $("#quantity" + id).val(qty + 1);
                    calcSum(id);
                    break;
            }
        };
        // calculation all compotition
        const calcGrand = () => {
            let subGrandTotal = parseInt(0);
            let lastClass = $("#listCart .card-cart:last ").attr("id");
            let i = 0;
            if (lastClass === undefined) {
                i = 1;
            } else {
                i = parseInt(lastClass.substr(lastClass.length - 1)) + 1;
            }
            for (let index = 0; index <= i; index++) {
                let checkNAN = parseInt($("#sub_total_" + index).val());
                let sub_total = checkNAN ? checkNAN : 0;

                subGrandTotal = subGrandTotal + sub_total;
            }
            let disc = $("#discount").val() / 100;
            let tax = $("#tax").val() / 100;
            let summary = subGrandTotal - subGrandTotal * disc;
            let sumTax = summary + summary * tax;
            $("#sub_grand_total").val(subGrandTotal);

            $("#total").val(sumTax);
            maskRupiah("#discount_view", subGrandTotal * disc);
            maskRupiah("#tax_view", summary * tax);
            maskRupiah("#sub_grand_total_view", subGrandTotal);
            maskRupiah("#total_view", sumTax);
        };
        // calculation item per cart
        const calcSum = (id) => {
            let qty = $("input#quantity" + id).val();
            let sell_price = $("input#sell_price_" + id)
                .unmask()
                .val();
            let sub_total = qty * sell_price;
            $("#sub_total_" + id).val(sub_total);
            if (qty == 0) {
                $("#sub_total_view" + id).text("Rp. 0,00");
            } else {
                maskRupiah("#sub_total_view" + id, sub_total);
            }
            calcGrand();
        };
        // controller  set tax
        const setTax = (value) => {
            let tax = $("#tax").val(value);
            calcGrand();
        };
        // controller new Item
        const newItem = () => {
            let href = '{{ route('item.new') }}';
            $.ajax({
                url: href,
                beforeSend: function() {
                    doBeforeSend(true)
                },
                // return the result
                success: function(result) {
                    if (result.status === "error") {
                        Swal.fire(
                            result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                            result.message,
                            result.status
                        )
                    } else {
                        $('#modalBlade').modal("show");
                        $('#modalBody').html(result).show();
                        storeItem();

                    }
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
        }
        // controller store new Item
        const storeItem = () => {
            $(document).on('click', '#saveButton', function(event) {
                event.preventDefault();
                var form = new FormData();
                var formTexts = $("#formNew").serializeArray();
                formTexts.forEach(formText => {
                    form.append(formText.name, formText.value)
                });
                $('button').attr("disabled", true);
                var files = $('#image')[0].files;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (files.length > 0) {
                    form.append('image', files[0]);
                }
                $.ajax({
                    url: "/dataInduk/barang/store",
                    method: "POST",
                    cache: false,
                    data: form,
                    contentType: false,
                    processData: false,
                    // return the result
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        var oTable = $('#dTable').dataTable();
                        oTable.fnDraw(false);
                        if (result.status == 'success') {
                            $('#modalBlade').modal("hide");
                            Swal.fire(
                                result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                                result.message,
                                result.status
                            )
                            // refresh all item 
                            getItemClick()
                        } else {
                            Swal.fire(
                                result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                                result.message,
                                result.status
                            )
                        }
                    },
                    complete: function(result) {
                        doBeforeSend(false)
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');
                        doBeforeSend(false)

                    },
                    timeout: 8000
                })
            });
        }
        // onReady page
        $(document).ready(function() {
            getDataMember();
            getItemSearch();
            calcGrand();
        });
    </script>
@endsection
