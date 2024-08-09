@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Data {{ $title ?? '' }}</h1>
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
            <div class="card-header">
                <h3 class="card-title">Informasi Detail</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nomor_dokumen">Nomor Dokumen</label>
                            <input type="text" class="form-control" id="nomor_dokumen" name="nomor_dokumen" placeholder="Nomor Dokumen" value="{{ old('nomor_dokumen') }}" required>
                         </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="akun_kasbank">Akun Kas/Bank</label>
                            <select class="form-control select2" name="akun_kasbank" id="akun_kasbank">
                                <option value="" selected>Pilih</option>
                                @foreach($akun AS $akn)
                                <option value="{{ $akn->id }}" data-value="{{ $akn->name }}">{{ $akn->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_transaksi">Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="total_nominal">Total Nominal</label>
                            <input type="text" class="form-control" id="total_nominal" name="total_nominal" value="{{ old('total_nominal') }}" placeholder="Total nominal" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="terima_dari">Diterima dari</label>
                            <input type="text" class="form-control" id="terima_dari" name="terima_dari" value="{{ old('terima_dari') }}" placeholder="Diterima dari" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="akun_pendapatan">Akun Pendapatan</label>
                            <select class="form-control select2" name="akun_pendapatan" id="akun_pendapatan">
                                <option value="" selected>Pilih</option>
                                @foreach($akunPendapatan AS $ab)
                                <option value="{{ $ab->id }}" data-value="{{ $ab->name }}">{{ $ab->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tgl_pelaksanaan">Tanggal Pelaksanaan</label>
                            <input type="date" class="form-control" id="tgl_pelaksanaan" name="tgl_pelaksanaan" value="{{ old('tgl_pelaksanaan') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nominal_pendapatan">Nominal Pendapatan</label>
                            <input type="text" class="form-control" id="nominal_pendapatan" name="nominal_pendapatan" placeholder="Nominal pendapatan" value="{{ old('nominal_pendapatan') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value="{{ old('keterangan') }}" required>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-success" id="addButton">Add</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <p>Detail</p>
                    </div>
                    <div class="card-body">
                        <table class="table" id="table-temporary-detail">
                            <thead>
                                <tr>
                                    <th>Akun Biaya</th>
                                    <th>Tanggal Pelaksanaan</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <p>Akumulasi</p>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="total_pembayaran">Total Pembayaran</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="total_pembayaran1">Rp</span>
                                </div>
                                <input type="text" class="form-control text-right" name="total_pembayaran" id="total_pembayaran" readonly aria-describedby="total_pembayaran1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_biaya">Total Biaya</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="total_biaya1">Rp</span>
                                </div>
                                <input type="text" class="form-control text-right" name="total_biaya" id="total_biaya" readonly aria-describedby="total_biaya1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="balance">Balance</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="balance1">Rp</span>
                                </div>
                                <input type="text" class="form-control text-right" name="balance" id="balance" readonly aria-describedby="balance1">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-success" id="saveButton">Simpan Data</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('jScript')
<script>

$("#total_nominal").val(0);
$("#total_pembayaran").val(0);
$("#total_biaya").val(0);
$("#balance").val(0);

$("#total_nominal").keyup(function(){
    $("#total_pembayaran").val($(this).val());

    var tot_biaya = $("#total_biaya").val();
    var balance = parseFloat($(this).val()) - parseFloat(tot_biaya);

    $("#balance").val(balance);
});

$("#addButton").click(function(){
    var val_akun = $("#akun_pendapatan option:selected").text();
    var val_tgl_pelaksanaan = $("#tgl_pelaksanaan").val();
    var val_pendapatan = $("#nominal_pendapatan").val();
    var val_keterangan = $("#keterangan").val();
    if(val_akun != "" && val_pendapatan != "" && val_keterangan != ""){
        $("#table-temporary-detail tbody").append(
            '<tr>'+
                '<td id='+$("#akun_pendapatan").val()+'>'+val_akun+'</td>'+
                '<td class="tgl_pelaksanaan">'+val_tgl_pelaksanaan+'</td>'+
                '<td class="nominal">'+val_pendapatan+'</td>'+
                '<td>'+val_keterangan+'</td>'+
                '<td><button type="button" class="btn btn-sm btn-danger" onclick="clearDetail($(this).closest('+"'tr'"+'))">x</button></td>'+
            '</tr>'
        );
    } else {
        alert("Mohon lengkapi form terlebih dahulu");
    }
    $("#akun_pendapatan").val("");
    $("#tgl_pelaksanaan").val("");
    $("#nominal_pendapatan").val("");
    $("#keterangan").val("");

    var sum = 0
    $('#table-temporary-detail tbody tr').each(function () {
        //the value of sum needs to be reset for each row, so it has to be set inside the row loop
        //find the combat elements in the current row and sum it 
        $(this).find('.nominal').each(function () {
            var nominal = $(this).text();
            if (!isNaN(nominal) && nominal.length !== 0) {
                sum += parseFloat(nominal);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
    });
    $('#total_biaya').val(sum);

    var tot_pembayaran = $("#total_pembayaran").val();
    var balance = parseFloat(tot_pembayaran) - parseFloat(sum);

    $("#balance").val(balance);
})

function clearDetail(det) {
    det.remove();

    var sum = 0
    $('#table-temporary-detail tbody tr').each(function () {
        //the value of sum needs to be reset for each row, so it has to be set inside the row loop
        //find the combat elements in the current row and sum it 
        $(this).find('.nominal').each(function () {
            var nominal = $(this).text();
            if (!isNaN(nominal) && nominal.length !== 0) {
                sum += parseFloat(nominal);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
    });
    $('#total_biaya').val(sum);

    var tot_pembayaran = $("#total_pembayaran").val();
    var balance = parseFloat(tot_pembayaran) - parseFloat(sum);

    $("#balance").val(balance);    
}

$("#saveButton").click(function(){
    $no_doc = $("#nomor_dokumen").val();
    $kasbank = $("#akun_kasbank").val();
    $tanggal_transaksi = $("#tanggal_transaksi").val();
    $total_nominal = $("#total_nominal").val();
    $terima_dari = $("#terima_dari").val();
    $total_pembayaran = $("#total_pembayaran").val();
    $total_biaya = $("#total_biaya").val();
    $balance = $("#balance").val();

    const tableRows = document.querySelectorAll('#table-temporary-detail tbody tr');
    const data = [];
    // Ambil data dari setiap baris tabel dan tambahkan ke array "data"
    tableRows.forEach((row) => {
        const akun_pendapatan = row.cells[0].attributes.id.value;
        const tgl_pelaksanaan = row.cells[1].innerHTML;
        const nominal = row.cells[2].innerHTML;
        const keterangan = row.cells[3].innerHTML;
        data.push({ akun_pendapatan, tgl_pelaksanaan, nominal, keterangan });
    });

    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('cash/in/store') }}",
        data: {
            "nomor_dokumen": $no_doc,
            "akun_kasbank": $kasbank,
            "tgl_transaksi": $tanggal_transaksi,
            "total_nominal": $total_nominal,
            "terima_dari": $terima_dari,
            "total_pembayaran": $total_pembayaran,
            "total_biaya": $total_biaya,
            "balance": $balance,
            "detail": data,
        },
        beforeSend: function () {
            $('#loader').show();
        },
        success: function (data) {
            Swal.fire({
                title: data.status=='success'? 'Berhasil !':'Gagal !',
                text: data.message,
                icon: data.status,
                confirmButtonText: 'Ok',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = '{{ url("/cash/in") }}'
                }
            })
        },
        complete: function (result) {
            $('#loader').hide();
        },
        error: function (err) {
            Swal.fire(
                err.status+' !',
                err.message,
                err.status
            )
        }
    });
});

/* Fungsi */
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