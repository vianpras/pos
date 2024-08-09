@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $title ?? '' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active">Form {{ $title ?? '' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    {{-- ./Content Header --}}

    <section class="content">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kode_item" class="form-label">Kode Item Jadi</label>
                            <select class="form-control selectModal" id="kode_item" name="kode_item">
                                <option value="" selected>Pilih</option>
                                <option value="Semua" {{ ($data->itemcode == 'Semua') ? 'selected' : '' }}>Semua</option>
                                @foreach($items AS $itm)
                                    <option value="{{ $itm->id }}" {{ ($itm->id == $data->itemcode) ? 'selected' : '' }}>{{ $itm->kode_item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="kode_item" class="form-label">Tipe Profit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_profit" id="tipe_profit" value="persentase" {{ ($data->profit_type == 'persentase') ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_profit">
                                Persentase (%)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_profit" id="tipe_profit" value="nominal" {{ ($data->profit_type == 'nominal') ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_profit">
                                Nominal (Rp)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0" value="{{ $data->jumlah }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-success" id="saveButton">Simpan Data</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('jScript')
<script>
    // Save Data
    $("#saveButton").click(function(){
        var id = $("#id").val();
        var kode_item = $("#kode_item").val();
        var tipe_profit = $('input[name="tipe_profit"]:checked').val();
        var jumlah = $("#jumlah").val();

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('dataInduk/profitsetting/update') }}",
            data: {
                "id": id,
                "kode_item": kode_item,
                "tipe_profit": tipe_profit,
                "jumlah": jumlah
            },
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (data) {
                window.location.href = '{{ url("dataInduk/profitsetting") }}'
            },
            complete: function () {
                $('#loader').hide();
            },
            error: function (err) {
                
            }
        });
    });    
</script>
@endsection