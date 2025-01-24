@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content" style="padding-top: 0.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Pengaturan Keanggotaan</h3>
            </div>
            <div class="card-body">
            <form id="formAction">
                <div class="row">
                    <div class="col-md-6" style="border-right: 0.2px solid #d7d7d7">
                        <div class="form-group row" style="margin-bottom: 0px;">
                            <label class="col-form-label col-md-12" for="min_payment">Retail</label>
                        </div>
                        <hr style="margin-bottom: 12px; margin-top: 0px;">
                        <input type="hidden" class="form-control" id="member_category_retail" name="member_category[]" value="retail">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="min_payment">Minimum Transaksi(Rp)</label>
                            <input type="hidden" class="form-control col-md-4" id="min_payment_retail" name="min_payment[]" value="{{ ($data_retail) ? $data_retail->min_transaksi : 0 }}">
                            <input type="text" class="form-control col-md-4" id="min_payment_show_retail" name="min_payment_show[]" onchange="minPaymentRetailSet(this.value)" value="{{ ($data_retail) ? Helper::formatNumber($data_retail->min_transaksi, 'rupiah') : 0 }}" placeholder="50,000">
                            <p class="col-md-5 mb-0">*Minimal jumlah transaksi per member untuk mendapatkan 1 poin</p>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="poin_transaction">Poin Transaksi</label>
                            <input type="text" class="form-control col-md-4" id="poin_transaction_retail" name="poin_transaction[]" value="{{ ($data_retail) ? $data_retail->poin_per_min_transaksi : 0 }}">
                            <p class="col-md-5 mb-0">*Poin yang didapat member setiap pembelian diatas minimum transaksi</p>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="">Konversi Poin(Rp)</label>
                            <input type="hidden" class="form-control col-md-4" id="konversi_poin_retail" name="konversi_poin[]" value="{{ ($data_retail) ? $data_retail->konversi_per_poin : 0 }}">
                            <input type="text" class="form-control col-md-4" id="konversi_poin_show_retail" name="konversi_poin_show[]" onchange="konversiPaymentRetailSet(this.value)" value="{{ ($data_retail) ? Helper::formatNumber($data_retail->konversi_per_poin, 'rupiah') : 0 }}" placeholder="500">
                            <p class="col-md-5 mb-0">*Konversi setiap 1 poin menjadi rupiah</p>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="">Batas Periode</label>
                            <input type="number" class="form-control col-md-2" id="date_exp_retail" name="date_exp[]" value="{{ ($data_retail) ? $data_retail->date_exp : 0 }}" placeholder="Tanggal">
                            <input type="number" class="form-control col-md-2" id="month_exp_retail" name="month_exp[]" value="{{ ($data_retail) ? $data_retail->month_exp : 0 }}" placeholder="Bulan">
                            <p class="col-md-5 mb-0">*Batas hangus poin member</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row" style="margin-bottom: 0px;">
                            <label class="col-form-label col-md-12" for="min_payment">Grosir</label>
                        </div>
                        <hr style="margin-bottom: 12px; margin-top: 0px;">
                        <input type="hidden" class="form-control" id="member_category_grosir" name="member_category[]" value="grosir">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="min_payment">Minimum Transaksi(Rp)</label>
                            <input type="hidden" class="form-control col-md-4" id="min_payment_grosir" name="min_payment[]" value="{{ ($data_grosir) ? $data_grosir->min_transaksi : 0 }}">
                            <input type="text" class="form-control col-md-4" id="min_payment_show_grosir" name="min_payment_show[]" onchange="minPaymentGrosirSet(this.value)" value="{{ ($data_grosir) ? Helper::formatNumber($data_grosir->min_transaksi, 'rupiah') : 0 }}" placeholder="50,000">
                            <p class="col-md-5 mb-0">*Minimal jumlah transaksi per member untuk mendapatkan 1 poin</p>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="poin_transaction">Poin Transaksi</label>
                            <input type="text" class="form-control col-md-4" id="poin_transaction_grosir" name="poin_transaction[]" value="{{ ($data_grosir) ? $data_grosir->poin_per_min_transaksi : 0 }}">
                            <p class="col-md-5 mb-0">*Poin yang didapat member setiap pembelian diatas minimum transaksi</p>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="">Konversi Poin(Rp)</label>
                            <input type="hidden" class="form-control col-md-4" id="konversi_poin_grosir" name="konversi_poin[]" value="{{ ($data_grosir) ? $data_grosir->konversi_per_poin : 0 }}">
                            <input type="text" class="form-control col-md-4" id="konversi_poin_show_grosir" name="konversi_poin_show[]" onchange="konversiPaymentGrosirSet(this.value)" value="{{ ($data_grosir) ? Helper::formatNumber($data_grosir->konversi_per_poin, 'rupiah') : 0 }}" placeholder="500">
                            <p class="col-md-5 mb-0">*Konversi setiap 1 poin menjadi rupiah</p>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-3" for="">Batas Periode</label>
                            <input type="number" class="form-control col-md-2" id="date_exp_grosir" name="date_exp[]" value="{{ ($data_retail) ? $data_retail->date_exp : 0 }}" placeholder="Tanggal">
                            <input type="number" class="form-control col-md-2" id="month_exp_grosir" name="month_exp[]" value="{{ ($data_retail) ? $data_retail->month_exp : 0 }}" placeholder="Bulan">
                            <p class="col-md-5 mb-0">*Batas hangus poin member</p>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="button" class="btn btn-success btn-block" onclick="savePengaturan()">Simpan Pengaturan</button>
                    </div>
                </div>
            </form>
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

    const minPaymentRetailSet = (value) => {
        $("#min_payment_retail").val(value);
        $("#min_payment_show_retail").val(maskRupiah("", value));
    }

    const konversiPaymentRetailSet = (value) => {
        $("#konversi_poin_retail").val(value);
        $("#konversi_poin_show_retail").val(maskRupiah("", value));
    }

    const minPaymentGrosirSet = (value) => {
        $("#min_payment_grosir").val(value);
        $("#min_payment_show_grosir").val(maskRupiah("", value));
    }

    const konversiPaymentGrosirSet = (value) => {
        $("#konversi_poin_grosir").val(value);
        $("#konversi_poin_show_grosir").val(maskRupiah("", value));
    }

    const savePengaturan = (value) => {
        let formData = $("form").serialize();

        let href = "/keanggotaan/settings/store";
        $.ajax({
            url: href,
            method: "POST",
            data: formData,
            beforeSend: function() {
                doBeforeSend(true)
            },
            success: function(res) {
                popToast('success', 'Berhasil mengubah pengaturan membership');
                
                console.log(res)
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
</script>
@endsection