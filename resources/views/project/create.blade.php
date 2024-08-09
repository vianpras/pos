<form class="form-horizontal" id="formNew">
   {{ csrf_field() }}
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="code" class="col-sm-2 col-form-label">Kode Proyek</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="code" readonly value="{{  Helper::docPrefix('projects') }}">
         </div>
         <label for="name" class="col-sm-2 col-form-label">Status Proyek</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="status" value="open" readonly>
         </div>
      </div>
      <div class="form-group row">
         <label class="col-sm-2 col-form-label">Tanggal Mulai</label>
         <div class="form-group col-sm-4" data-target="#startDateSelectModal" data-toggle="datetimepicker">
            <div class="input-group date" id="startDateSelectModal" data-target-input="nearest">
               <input type="text" class="form-control form-control-sm  datetimepicker-input " data-target="#startDateSelectModal"
                  value="{{ Date('Y-m-d') }}" name="start_project"  id="start_project" readonly required />
               <div class="input-group-append">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
               </div>
            </div>
         </div>
         <label class="col-sm-2 col-form-label">Tanggal Selesai</label>
         <div class="form-group col-sm-4" data-target="#endDateSelectModal" data-toggle="datetimepicker">
            <div class="input-group date" id="endDateSelectModal" data-target-input="nearest">
               <input type="text" class="form-control form-control-sm  datetimepicker-input " data-target="#endDateSelectModal"
                  value="{{ Date('Y-m-d') }}" name="end_project" id="end_project"  readonly required />
               <div class="input-group-append">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
               </div>
            </div>
         </div>
      </div>
      <div class="form-group row">
         <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
         <div class="col-sm">
            <textarea class="form-control" id="description" name="description" rows="3"
               placeholder="Deskripsi ..."></textarea>
         </div>
      </div>
   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-success" id="saveButton">Simpan</button>
   </div>
</form>