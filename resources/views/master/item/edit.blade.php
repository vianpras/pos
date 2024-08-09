<form class="form-horizontal" id="formUpdate">
   {{ csrf_field() }}
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="code" class="col-sm-2 col-form-label">Kode Barang</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="code" name="code" placeholder="Kode Barang" value="{{ $data->code }}">
         </div>
         
         <label for="name" class="col-sm-2 col-form-label">Nama Barang</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="name" name="name" placeholder="Nama Barang" value="{{ $data->name }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="small_quantity" class="col-sm-2 col-form-label">Kuantitas </label>
         <div class="col-sm-4">
            <input type="number" class="form-control" id="small_quantity" name="small_quantity" placeholder="Kuantitas " value="{{ $data->small_quantity }}">
         </div>

         <label for="small_unit_id" class="col-sm-2 col-form-label">Satuan </label>
         <div class="col-sm-4">
            <select class="form-control selectModal" id="small_unit_id" name="small_unit_id" placeholder="Satuan ">
               @foreach ($unit as $option)
               <option value="{{ $option->id }}" @if($option->id === $data->small_unit_id) selected @endif >{{ $option->name }}</option>
               @endforeach
            </select>
         </div>
      </div>

      <div class="form-group row">
         <label for="buy_price" class="col-sm-2 col-form-label">Harga Beli</label>
         <div class="col-sm-4">
            <input type="text" class="form-control uang" id="buy_price" name="buy_price" placeholder="Harga Beli" value="{{ Helper::formatNumber($data->buy_price,'') }}">
         </div>

         <label for="sell_price" class="col-sm-2 col-form-label">Harga Jual</label>
         <div class="col-sm-4">
            <input type="text" class="form-control uang" id="sell_price" name="sell_price" placeholder="Harga Jual" value="{{ Helper::formatNumber($data->sell_price,'') }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="category_id" class="col-sm-2 col-form-label">Kategori</label>
         <div class="col-sm-4">
            <select class="form-control selectModal" id="category_id" name="category_id" placeholder="Hak Akses">
               @foreach ($category as $option)
               <option value="{{ $option->id }}" @if($option->id === $data->category_id) selected @endif >{{ $option->name }}</option>
               @endforeach
            </select>
         </div>

         <label for="status" class="col-sm-2 col-form-label">Status</label>
         <div class="col-sm-4">
            <input type="checkbox" name="status" class="switchBs" id="status" value={{ $data->status?$data->status:1 }} data-bootstrap-switch data-off-color="danger" data-on-color="success" checked>
         </div>
      </div>
      <div class="form-group row">
         <label for="image" class="col-sm-2 col-form-label">Gambar</label>
         <div class="col-sm-4">
            <span for="image" style="color: #ccc">Rec: 512 X 512 Pixels </span>
            <div class="input-group row">
               <div class="" style="max-width:150px">
                  <img style="max-width:150px; margin-right:20px;" id="output_image" class="img-thumbnail" src='/img/items/{{$data->id}}' />
               </div>
               <div class="custom-file" style="margin-top: 5%; margin-bottom: 10%; margin-left: 25px;margin-right: 25px;">
                  <input type="file" class="custom-file-input" id="image" accept="image/*" name="image" onchange="preview_image(event)">
                  <label class="custom-file-label" for="image">Pilih</label>
               </div>
            </div>
         </div>

         <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
         <div class="col-sm-4">
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Deskripsi ...">{{$data->description}}</textarea>
         </div>
      </div>
   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-primary" id="updateButton" data-id="{{ $data->id }}">Perbaharui</button>
   </div>
</form>