<form class="form-horizontal" id="formUpdate">
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
            <input type="text" class="form-control" id="code" readonly value="{{$data->code}}">
         </div>
         <label for="name" class="col-sm-2 col-form-label">Status Proyek</label>
         <div class="col-sm-4">
            <select class="form-control selectModal" id="status" name="status">
               <option value='open' @if($data->status === 'open') selected @endif >Open</option>
               <option value='clear' @if($data->status === 'clear') selected @endif >Clear</option>
               <option value='cancel' @if($data->status === 'cancel') selected @endif >Cancel</option>
               <option value='close' @if($data->status === 'close') selected @endif >Close</option>
            </select>
         </div>
      </div>
      <div class="form-group row">
         <label class="col-sm-2 col-form-label">Tanggal Mulai</label>
         <div class="form-group col-sm-4" data-target="#startDateSelectModal" data-toggle="datetimepicker">
            <div class="input-group date" id="startDateSelectModal" data-target-input="nearest">
               <input type="text" class="form-control  datetimepicker-input "
                  data-target="#startDateSelectModal" value="{{ $data->start_project }}" name="start_project"
                  id="start_project" readonly required />
               <div class="input-group-append">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
               </div>
            </div>
         </div>
         <label class="col-sm-2 col-form-label">Tanggal Selesai</label>
         <div class="form-group col-sm-4" data-target="#endDateSelectModal" data-toggle="datetimepicker">
            <div class="input-group date" id="endDateSelectModal" data-target-input="nearest">
               <input type="text" class="form-control  datetimepicker-input "
                  data-target="#endDateSelectModal" value="{{ $data->end_project }}" name="end_project" id="end_project"
                  readonly required />
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
               placeholder="Deskripsi ...">{{ $data->description }}</textarea>
         </div>
      </div>
   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-primary" id="updateButton" data-id="{{ $data->code }}">Perbaharui</button>
   </div>
</form>