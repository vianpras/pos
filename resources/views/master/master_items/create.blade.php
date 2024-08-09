<form class="form-horizontal" id="formNew">
    {{ csrf_field() }}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="card-body">
        <h5 class="modal-title">{{ $title ?? '' }}</h5>
        <hr>
        <div class="form-group row">
            <label for="kode_item" class="col-sm-2 col-form-label">Kode Item</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="kode_item" name="kode_item" placeholder="Kode Item" value="{{ old('kode_item') }}">
            </div>
 
            <label for="nama_item" class="col-sm-2 col-form-label">Nama Item</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="nama_item" name="nama_item" placeholder="Nama Item" value="{{ old('nama_item') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="buy_price" class="col-sm-2 col-form-label">Harga Beli</label>
            <div class="col-sm-4">
               <input type="text" class="form-control uang" id="buy_price" name="buy_price" placeholder="Harga Beli" value="{{ old('buy_price') }}">
            </div>
   
            <label for="sell_price" class="col-sm-2 col-form-label">Harga Jual</label>
            <div class="col-sm-4">
               <input type="text" class="form-control uang" id="sell_price" name="sell_price" placeholder="Harga Jual" value="{{ old('sell_price') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="satuan" class="col-sm-2 col-form-label">Satuan</label>
            <div class="col-sm-4">
                <select class="form-control selectModal" id="satuan" name="satuan">
                    @foreach ($unit as $option)
                        <option value="{{ $option->id }}" @if($option->id === old('satuan')) selected @endif >{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <label for="tipe" class="col-sm-2 col-form-label">Tipe</label>
            <div class="col-sm-4">
                <select class="form-control selectModal" id="tipe" name="tipe" onchange="changeType(this.value)">
                    <option selected>Pilih tipe</option>
                    <option value="Item Jadi">Item Jadi</option>
                    <option value="Bahan Baku">Bahan Baku</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="category_id" class="col-sm-2 col-form-label">Kategori</label>
            <div class="col-sm-4">
               <select class="form-control selectModal" id="category_id" name="category_id" placeholder="Hak Akses">
                  @foreach ($category as $option)
                  <option value="{{ $option->id }}" @if($option->id === old('category_id')) selected @endif >{{ $option->name }}</option>
                  @endforeach
               </select>
            </div>

            <div class="switch col-sm-6">
                <div class="row">
                    <label for="status_bahan_baku" class="col-sm-4 col-form-label">Bahan baku</label>
                    <div class="col-sm-8">
                       <input type="checkbox" name="status_bahan_baku" class="switchBs" id="status_bahan_baku" value={{ old('status_bahan_baku')?old('status_bahan_baku'):1 }} data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Ya" data-off-text="Tidak" checked onchange="getSwitch(this.checked)">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
            <div class="col-sm-4">
               <textarea class="form-control" id="description" name="description" rows="4" placeholder="Deskripsi ..."></textarea>
            </div>
        </div>

        <div class="form-group row" id="detail-bahan">
            <div class="col-sm-12 text-right mb-2">
                <button type="button" class="btn btn-success btn-sm" id="add">Tambah +</button>
            </div>
            <div class="col-sm-12">
                <table class="table" id="dynamicTable"> 
                    <thead>
                        <tr>
                            <th>Kode Bahan Baku</th>
                            <th>Satuan</th>
                            <th>Qty Bahan Baku</th>
                            <th>Harga Beli</th>
                            <th>Action</th>
                        </tr>
                    </thead> 
                    <tbody>
                        <tr>  
                            <td width="30%">
                                <select data-placeholder="Pilih bahan baku" class="form-control select2" name="id_bahan_baku[]">
                                    <option value="" selected>Pilih</option>
                                    @foreach ($bahan_baku as $bb)
                                        <option value="{{ $bb->id }}" @if($bb->id === old('id_bahan_baku')) selected @endif >{{ $bb->kode_item.'-'.$bb->nama_item }}</option>
                                    @endforeach
                                </select>
                            </td>  
                            <td width="15%">
                                <select data-placeholder="Pilih satuan" class="form-control select2" name="satuan_bahan[]">
                                    @foreach ($unit as $option)
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            </td>  
                            <td><input type="text" name="qty_bahan[]" id="qty" placeholder="Qty" class="form-control" /></td>  
                            <td><input type="text" name="harga_beli_bahan[]" id="harga_beli" placeholder="Harga Beli" class="form-control harga_beli" /></td>  
                            <td><button type="button" class="btn btn-danger btn-sm remove-tr">Hapus</button></td>  
                        </tr>  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="row justify-content-between">
       <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
       <button type="button" class="btn btn-success" id="saveButton">Simpan</button>
    </div>
 </form>