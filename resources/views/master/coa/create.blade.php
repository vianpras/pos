<form class="form-horizontal" id="formNew" enctype="multipart/form-data">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
    </button>
    {{ csrf_field() }}
    <div class="card-body">
        <h5 class="modal-title">{{ $title ?? '' }}</h5>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="code">Akun Induk</label>
                    <select class="form-control selectModal" id="code_parent" name="code_parent" placeholder="Akun Induk" onchange="conditionKode(this);">
                        <option value="null" selected>Akun Induk</option>
                        @foreach($parentCode AS $pCode)
                        <option value="{{ $pCode->code_account_default }}">{{ $pCode->code_account_default." (".$pCode->name.")" }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="name">Nama Akun</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nama Akun" value="{{ old('name') }}">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="code">Kode Akun</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="addon-kodeAkun"></span>
                        </div>
                        <input type="text" class="form-control" id="code_account_default" name="code_account_default" placeholder="Kode Akun (contoh 01.00.000.00)" aria-label="Kode Akun" aria-describedby="addon-kodeAkun">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="description">Grup Akun</label>
                    <select class="form-control selectModal" id="group_of_account" name="group_of_account" placeholder="Grup Akun">
                        <option value="null" selected>Grup Akun</option>
                        <option value="aktiva">Aktiva</option>
                        <option value="hutang">Hutang</option>
                        <option value="modal">Modal</option>
                        <option value="pendapatan">Pendapatan</option>
                        <option value="harga_pokok_penjualan">Harga Pokok Penjualan</option>
                        <option value="biaya_operasional">Biaya Operasional</option>
                        <option value="biaya_dan_pendapatan_lainnya">Biaya dan Pendapatan Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="parent">Tipe Akun</label>
                    <select class="form-control selectModal" id="type_of_account" name="type_of_account" placeholder="Tipe Akun">
                        <option value="null" selected>Tipe Akun</option>
                        <option value="header">Header</option>
                        <option value="detail">Detail</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="description">Tipe Bisnis</label>
                    <select class="form-control selectModal" id="type_of_business" name="type_of_business" placeholder="Tipe Bisnis">
                        <option value="null" selected>Tipe Bisnis</option>
                        <option value="dagang">Dagang</option>
                        <option value="jasa">Jasa</option>
                        <option value="manufaktur">Manufaktur</option>
                        <option value="proyek">Proyek</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="parent">Deskripsi</label>
                    <textarea class="form-control" name="description" id="description" rows="5"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row justify-content-between">
           <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
           <button type="button" class="btn btn-success" id="saveButton">Simpan</button>
        </div>
    </div>
</form> 