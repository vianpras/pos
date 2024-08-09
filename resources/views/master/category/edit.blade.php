<form class="form-horizontal" id="formUpdate">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   {{ csrf_field() }}
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="code" class="col-sm-2 col-form-label">Kode Kategori</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="code" name="code" placeholder="Kode Kategori" value="{{ $data->code }}">
         </div>
         <label for="name" class="col-sm-2 col-form-label">Nama Kategori</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="name" name="name" placeholder="Nama Kategori" value="{{ $data->name }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="parent" class="col-sm-2 col-form-label">Sebagai Induk</label>
         <div class="col-sm-4">
            <select class="form-control selectModal" id="parent" name="parent" placeholder="Sebagai Induk">
               <option value="null" @if($data->parent === null) selected @endif>Sebagai Induk</option>
               @foreach ($categories as $option)
               <option value="{{ $option->id }}" @if($option->id === $data->parent) selected @endif >{{
                  $option->code }}-{{
                  $option->name }}</option>
               @endforeach
            </select>
         </div>
         <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="description" placeholder="Deskripsi"
               value="{{ $data->description }}">
         </div>
      </div>
      {{-- <div class="form-group row">
         <label for="image">Gambar</label><br>
         <span for="image" style="color: #ccc">Rec: 512 X 512 Pixels </span>
         <div class="input-group">
           <div class="" style="max-width:150px">
             <img style="max-width:150px; margin-right:20px;" id="output_image"  class="img-thumbnail" src='/img/categories/{{$data->id}}'/>
           </div>
           <div class="custom-file"  style="margin-top: 5%; margin-bottom: 10%; margin-left: 25px;margin-right: 25px;">
             <input type="file" class="custom-file-input" id="image" accept="image/*" name="image" onchange="preview_image(event)" >
             <label class="custom-file-label" for="image">Upload Gambar</label>
           </div>
         </div>
       </div> --}}
      {{-- <div class="form-group row">
         <label for="as_parent" class="col-sm-2 col-form-label">Sebagai Induk</label>
         <div class="col-sm-4">
            <input type="checkbox" name="as_parent" class="switchBs" id="as_parent" value={{ $data->as_parent }}
            @if($data->as_parent)checked @endif data-bootstrap-switch data-off-color="danger" data-on-color="success">
         </div>
      </div> --}}
   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-primary" id="updateButton" data-id="{{ $data->id }}">Perbaharui</button>
   </div>
</form>