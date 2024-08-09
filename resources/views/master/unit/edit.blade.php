<form class="form-horizontal" id="formUpdate">
   {{ csrf_field() }}
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <div class="form-group row">
         <label for="name" class="col-sm-2 col-form-label">Nama Satuan</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="name" placeholder="Nama Satuan" value="{{ $data->name }}">
         </div>
         <label for="code" class="col-sm-2 col-form-label">Kode Satuan</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="code" placeholder="Kode Satuan" value="{{ $data->code }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="status" class="col-sm-2 col-form-label">Status</label>
         <div class="col-sm-4">
            <input type="checkbox" name="status" class="switchBs" id="status" value={{ $data->status }}
            @if($data->status)checked @endif data-bootstrap-switch data-off-color="danger" data-on-color="success">
         </div>
      </div>

   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-primary" id="updateButton" data-id="{{ $data->id }}">Perbaharui</button>
   </div>
</form>