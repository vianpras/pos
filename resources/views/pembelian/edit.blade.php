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
            <div class="card-body">
                <div class="row">
                    <input type="hidden" class="form-control" id="id" name="id" value="{{ $data->id }}" required readonly>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="kode_pembelian">Kode Pembelian</label>
                            <input type="text" class="form-control" id="kode_pembelian" name="kode_pembelian" placeholder="Kode Pembelian" value="{{ $data->kode_pembelian }}" required readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_transaksi">Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="{{ $data->tanggal }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Detail</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="kode_item">Tipe</label>
                            <select class="form-control select2" name="tipe" id="tipe" onchange="itemByTipe(this.value)">
                                <option value="" selected>Pilih</option>
                                <option value="Bahan Baku" data-value="Bahan Baku">Bahan Baku</option>
                                <option value="Item Jadi" data-value="Item Jadi">Item Jadi</option>
                                <option value="Lain-lain" data-value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="kode_item">Kode Bahan Baku</label>
                            <select class="form-control select2" name="kode_item" id="kode_item" onchange="bahanBaku(this.value)">
                                <option value="" selected>Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nama_item">Nama Item</label>
                            <input type="text" class="form-control" id="nama_item" name="nama_item" placeholder="Nama Item" value="{{ old('nama_item') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="nama_satuan">Satuan</label>
                            <select class="form-control select2 satuan" name="nama_satuan" id="nama_satuan" style="cursor: not-allowed;pointer-events: none;touch-action: none;">
                                <option value="" selected>Pilih</option>
                                @foreach($unit AS $unt)
                                    <option value="{{ $unt->id }}" data-value="{{ $unt->name }}">{{ $unt->name }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control" id="nama_satuan" name="nama_satuan" placeholder="Satuan" value="{{ old('satuan') }}" readonly> --}}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="text" class="form-control" id="harga" name="harga" placeholder="Harga" value="{{ old('harga') }}" onkeyup="count()" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="qty">Qty</label>
                            <input type="text" class="form-control" id="qty" name="qty" placeholder="Qty" value="{{ old('qty') }}" onkeyup="count()" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input type="text" class="form-control" id="total" name="total" placeholder="Total" value="{{ old('total') }}" required>
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
                                    <th>Tipe</th>
                                    <th>Kode Bahan Baku</th>
                                    <th>Nama Item</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data_details AS $dd)
                                    <tr>
                                        <td id="{{ $dd->tipe }}">{{ $dd->tipe }}</td>
                                        <td id="{{ $dd->kode_item }}">{{ $dd->kode_item }}</td>
                                        <td>{{ $dd->nama }}</td>
                                        <td style="display: none;">{{ $dd->satuan_item }}</td>
                                        <td>{{ $dd->nama_satuan }}</td>
                                        <td>{{ $dd->harga }}</td>
                                        <td>{{ $dd->qty }}</td>
                                        <td class="totals">{{ $dd->total }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="clearDetail($(this).closest('tr'))">x</button>
                                        </td>
                                    </tr>
                                @endforeach
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
                            <label for="total_biaya">Total</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="total_biaya1">Rp</span>
                                </div>
                                <input type="text" class="form-control text-right" name="total_biaya" id="total_biaya" readonly aria-describedby="total_biaya1">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-success" id="saveButton">Update Data</button>
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

$(document).ready(function(){
    var sum = 0
    $('#table-temporary-detail tbody tr').each(function () {
        //the value of sum needs to be reset for each row, so it has to be set inside the row loop
        //find the combat elements in the current row and sum it 
        $(this).find('.totals').each(function () {
            var nominal = $(this).text();
            if (!isNaN(nominal) && nominal.length !== 0) {
                sum += parseFloat(nominal);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
    });
    $('#total_biaya').val(sum);
});

$("#total_biaya").val(0);

function itemByTipe(tipe) {
    if(tipe == "Lain-lain"){
        $('select[id="kode_item"]').html(`<option value="" selected>Pilih</option>`);

        $("#nama_item").attr("readonly", false);
        $("select[name=nama_satuan]").prop("disabled", false);

    } else {
        $("#nama_item").attr("readonly", true);
        $("select[name=nama_satuan]").prop("disabled", true);

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('dataInduk/item/by_tipe') }}",
            data: {
                "tipe": tipe
            },
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (data) {
                console.log(data)
                $('select[id="kode_item"]').html(`<option value="" selected>Pilih</option>`);

                $.each(data, function(key, value) {
                    $('select[id="kode_item"]').append(
                        `<option value="`+value.id+`" data-value="`+value.kode_item+`">`+value.kode_item+`</option>`
                    );
                });
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
    }
}

function bahanBaku(id) {
    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('dataInduk/item/details') }}",
        data: {
            "id": id
        },
        beforeSend: function () {
            $('#loader').show();
        },
        success: function (data) {
            console.log(data);
            $('#nama_item').val(data.nama_item);
            $('#nama_satuan option[value='+data.satuan+']').attr('selected','selected').trigger('change');
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
}

function count(){
    var harga = $("#harga").val();
    var qty = $("#qty").val();

    var count = harga * qty;
    $("#total").val(count);
}

$("#addButton").click(function(){
    var tipe = $("#tipe").find(':selected').attr('data-value');
    var val_kode_item = $("#kode_item").find(':selected').attr('data-value');
    var val_nama_item = $("#nama_item").val();
    var val_satuan_item = $("#nama_satuan").find(':selected').val();
    var val_nama_satuan = $("#nama_satuan").find(':selected').attr('data-value');
    var val_harga = $("#harga").val();
    var val_qty = $("#qty").val();
    var val_total = $("#total").val();

    if(tipe != "" && val_kode_item != "" && val_nama_item != "" && val_harga != "" && val_qty != "" && val_total != ""){
        $("#table-temporary-detail tbody").append(
            '<tr>'+
                '<td id='+tipe+'>'+tipe+'</td>'+
                '<td id='+val_kode_item+'>'+val_kode_item+'</td>'+
                '<td class="nama_item">'+val_nama_item+'</td>'+
                '<td style="display:none;">'+val_satuan_item+'</td>'+
                '<td class="satuan_item">'+val_nama_satuan+'</td>'+
                '<td class="harga">'+val_harga+'</td>'+
                '<td class="qty">'+val_qty+'</td>'+
                '<td class="totals">'+val_total+'</td>'+
                '<td><button type="button" class="btn btn-sm btn-danger" onclick="clearDetail($(this).closest('+"'tr'"+'))">x</button></td>'+
            '</tr>'
        );
    } else {
        alert("Mohon lengkapi form terlebih dahulu");
    }
    $("#kode_item").val("");
    $("#nama_item").val("");
    $("#nama_satuan").val("");
    $("#harga").val("");
    $("#qty").val("");
    $("#total").val("");

    var sum = 0
    $('#table-temporary-detail tbody tr').each(function () {
        //the value of sum needs to be reset for each row, so it has to be set inside the row loop
        //find the combat elements in the current row and sum it 
        $(this).find('.totals').each(function () {
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
        $(this).find('.totals').each(function () {
            var nominal = $(this).text();
            if (!isNaN(nominal) && nominal.length !== 0) {
                sum += parseFloat(nominal);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
    });
    $('#total_biaya').val(sum);    
}

$("#saveButton").click(function(){
    $id = $("#id").val();
    $kode_pembelian = $("#kode_pembelian").val();
    $item_jadi = $("#item_jadi").val();
    $tanggal_transaksi = $("#tanggal_transaksi").val();
    $qty_jual = $("#qty_jual").val();
    $satuan = $("#satuan").val();
    $total_biaya = $("#total_biaya").val();

    const tableRows = document.querySelectorAll('#table-temporary-detail tbody tr');
    const data = [];
    // Ambil data dari setiap baris tabel dan tambahkan ke array "data"
    tableRows.forEach((row) => {
        const tipe = row.cells[0].innerHTML;
        const kode_bahan_baku = row.cells[1].attributes.id.value;
        const nama_item = row.cells[2].innerHTML;
        const satuan_item = row.cells[3].innerHTML;
        const harga = row.cells[5].innerHTML;
        const qty = row.cells[6].innerHTML;
        const subtotal = row.cells[7].innerHTML;
        data.push({ tipe, kode_bahan_baku, nama_item, satuan_item, harga, qty, subtotal });
    });

    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('purchase/store/update') }}",
        data: {
            "id": $id,
            "kode_pembelian": $kode_pembelian,
            "item_jadi": $item_jadi,
            "tgl_transaksi": $tanggal_transaksi,
            "tanggal_transaksi": $tanggal_transaksi,
            "qty_jual": $qty_jual,
            "satuan": $satuan,
            "total_biaya": $total_biaya,
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
                    window.location.href = '{{ url("/purchase") }}'
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