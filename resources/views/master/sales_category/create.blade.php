
<form class="form-horizontal" id="formNew">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   {{ csrf_field() }}
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="name" class="col-sm-2 col-form-label">Nama Kategori</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="name" placeholder="Nama Kategori" value="{{ old('name') }}">
         </div>
         <label for="mark_up" class="col-sm-2 col-form-label ">Mark Up  <span class="text-teal">(%)</span></label>
         <div class="col-sm-4">
            <input type="text" class="form-control uang" id="mark_up" placeholder="Mark Up" value="{{ old('mark_up') }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="status" class="col-sm-2 col-form-label">Status</label>
         <div class="col-sm-4">
            <input type="checkbox" name="status" class="switchBs"  id="status" value="{{ old('status')?old('status'):1 }}" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">
         </div>
      </div>

   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-success" id="saveButton">Simpan</button>
   </div>
</form>

