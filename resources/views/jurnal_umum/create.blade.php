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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nomor_jurnal">Nomor Jurnal</label>
                            <input type="text" class="form-control" id="nomor_jurnal" name="nomor_jurnal" placeholder="Nomor Jurnal" value="{{ $nomor_jurnal }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nomor_transaksi">Nomor Transaksi</label>
                            <input type="text" class="form-control" id="nomor_transaksi" name="nomor_transaksi" placeholder="Nomor Transaksi" value="{{ old('nomor_transaksi') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_transaksi">Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
                        </div>
                    </div>
                    {{-- <div class="col-md-7">
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="4"></textarea>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <table class="table" id="tableDetail">
                        <thead>
                            <tr>
                                <th style="width: 350px !important">Akun</th>
                                <th style="width: 200px !important">Debit</th>
                                <th style="width: 200px !important">Kredit</th>
                                <th style="width: 250px !important">Keterangan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <button class="btn btn-primary" id="tambahDetail">+ Tambah Data</button>
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th style="width: 350px !important;"></th>
                                <th style="width: 200px !important; font-size: 17px">
                                    Total Debit
                                </th>
                                <th style="width: 200px !important; font-size: 17px">
                                    Total Kredit
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><div class="showTotDebit" style="font-size: 17px"></div></th>
                                <th><div class="showTotKredit" style="font-size: 17px"></div></th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-danger">Batal</button>
                        <button class="btn btn-success" id="saveButton">Simpan Jurnal Umum</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('jScript')
<script>

    var html = `
        <tr>
            <td>
                <select class="form-control" id="select-akun" style="width: 300px"></select>
            </td>
            <td>
                <input type="text" class="form-control" id="debit" name="debit" placeholder="Debit" value="0" required>
            </td>
            <td>
                <input type="text" class="form-control" id="kredit" name="kredit" placeholder="Kredit" value="0" required>
            </td>
            <td>
                <textarea class="form-control" name="keterangan" id="keterangan" rows="1"></textarea>
            </td>
            <td>
                <button class="btn btn-danger remove">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </td>
        </tr>`;
    
    $("#tambahDetail").click(function(){
        $('#tableDetail tbody').append(html);
        var newSelectId = 'select' + Date.now();
        //select2
        $('#select-akun').attr('id', newSelectId).select2({tags: true});

        $.ajax({
            url: '{{ url("jurnal/get_account") }}',
            type: 'get',
            dataType: 'JSON',
            success: function(response){
                $.each(response, function (i, item) {
                    $('#'+newSelectId).append($('<option>', { 
                        value: item.id,
                        text : item.code_account+' - '+item.name 
                    }));
                });
            }
        });
    });

    $(document).on('click','.remove',function(){
        $(this).parents('tr').remove();

        // Update Debit
        var sumDebit = 0;
        $('#tableDetail > tbody > tr').each(function () {
            sumDebit += parseFloat($(this).find("input[id='debit']").val());
        });
        $('.showTotDebit').html("");
        $('.showTotDebit').html(sumDebit); 

        // Update Kredit
        var sumKredit = 0;
        $('#tableDetail > tbody > tr').each(function () {
            sumKredit += parseFloat($(this).find("input[id='kredit']").val());
        });
        $('.showTotKredit').html("");
        $('.showTotKredit').html(sumKredit);
    });

    $('#tableDetail').on("keyup", "#debit", function () {

        var table = document.getElementById('tableDetail');

        var sum = 0;
        $('#tableDetail > tbody > tr').each(function () {
            sum += parseFloat($(this).find("input[id='debit']").val().toString().replaceAll('.', ''));
        });

        $('.showTotDebit').html("");
        $('.showTotDebit').html('Rp '+formatRupiah(sum.toString(), ''));
    });

    $('#tableDetail').on("keyup", "#kredit", function () {
        var table = document.getElementById('tableDetail');

        var sum = 0;
        $('#tableDetail > tbody > tr').each(function () {
            sum += parseFloat($(this).find("input[id='kredit']").val().toString().replaceAll('.', ''));
        });

        $('.showTotKredit').html("");
        $('.showTotKredit').html('Rp '+formatRupiah(sum.toString(), ''));
    });

    // Save Data
    $("#saveButton").click(function(){
        var no_jurnal = $("#nomor_jurnal").val();
        var no_transaksi = $("#nomor_transaksi").val();
        var tgl_transaksi = $("#tanggal_transaksi").val();

        const data = [];
        $('#tableDetail > tbody > tr').each(function () {
            const akun = parseFloat($(this).find("select option:selected").val());
            const debit = parseFloat($(this).find("input[id='debit']").val().toString().replaceAll('.', ''));
            const kredit = parseFloat($(this).find("input[id='kredit']").val().toString().replaceAll('.', ''));
            const keterangan = $(this).find("textarea[id='keterangan']").val();

            data.push({ akun, debit, kredit, keterangan });
        });

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('jurnal/store') }}",
            data: {
                "nomor_jurnal": no_jurnal,
                "nomor_transaksi": no_transaksi,
                "tanggal_transaksi": tgl_transaksi,
                "detail": data
            },
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (data) {
                window.location.href = '{{ url("jurnal") }}'
            },
            complete: function () {
                $('#loader').hide();
            },
            error: function (err) {
                
            }
        });
    });

    $('#tableDetail').on("keyup", "#debit", function () {
        $(this).val(formatRupiah(this.value, ''));
    });

    $('#tableDetail').on("keyup", "#kredit", function () {
        $(this).val(formatRupiah(this.value, ''));
    });

    function formatRupiah(angka, prefix)
    {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split    = number_string.split(','),
            sisa     = split[0].length % 3,
            rupiah     = split[0].substr(0, sisa),
            ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
            
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }
</script>
@endsection