
<form class="form-horizontal" id="formNew">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   {{ csrf_field() }}
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="docType" class="col-sm-2 col-form-label">Tipe Dockumen</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="docType" placeholder="Tipe DOkumen / Pastikan sesuai dengan nama table" value="{{ old('docType') }}">
         </div>
         <label for="prefix" class="col-sm-2 col-form-label">Kode Prefix</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="prefix" placeholder="Kode Prefix" value="{{ old('prefix') }}">
         </div>
      </div>

   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-success" id="saveButton">Simpan</button>
   </div>
</form>

