<form class="form-horizontal" id="formUpdate">
   {{ csrf_field() }}
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="code" class="col-sm-2 col-form-label">NIK</label>
         <div class="col-sm-4">
            <input type="number" class="form-control" id="nik" placeholder="NIK" value="{{ $data->nik }}">
         </div>
         <label for="nama" class="col-sm-2 col-form-label">Nama Anggota</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="nama" placeholder="Nama Anggota" value="{{ $data->nama }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="mobile" class="col-sm-2 col-form-label">Nomor HP</label>
         <div class="col-sm-4">
            <input type="number" class="form-control" id="mobile" placeholder="Nomor HP" value="{{ $data->mobile }}">
         </div>
         <label for="gender" class="col-sm-2 col-form-label">Jenis Kelamin</label>
         <div class="col-sm-4">
            <select class="form-control selectModal" id="gender" name="gender" placeholder="Jenis Kelamin">
               <option value="l" selected>Laki-laki</option>
               <option value="p">Perempuan</option>
            </select>
         </div>
      </div>
      <div class="form-group row">
         <label for="kota" class="col-sm-2 col-form-label">Kota</label>
         <div class="col-sm-4">
            <input type="text" class="form-control " id="kota" placeholder="Kota" value="{{ $data->kota }}">
         </div>
         <label for="provinsi" class="col-sm-2 col-form-label">Provinsi</label>
         <div class="col-sm-4">
            <input type="text" class="form-control " id="provinsi" placeholder="Provinsi"
               value="{{ $data->provinsi }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="place_birth" class="col-sm-2 col-form-label">Tempat Lahir</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="place_birth" placeholder="Tempat Lahir"
               value="{{ $data->place_birth }}">
         </div>
         <label for="date_birth" class="col-sm-2 col-form-label">Tanggal Lahir</label>
         <div class="form-group col-sm-4" data-target="#startDateSelectModal" data-toggle="datetimepicker">
            <div class="input-group date" id="startDateSelectModal" data-target-input="nearest">
               <input type="text" class="form-control form-control-sm  datetimepicker-input "
                  data-target="#startDateSelectModal" value="{{ Date('Y-m-d') }}" name="date_birth" id="date_birth"
                  readonly required />
               <div class="input-group-append">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
               </div>
            </div>
         </div>

      </div>
      <div class="form-group row">
         <label for="email" class="col-sm-2 col-form-label">Email</label>
         <div class="col-sm-4">
            <input type="email" class="form-control" id="email" placeholder="email" value="{{ $data->email }}">
         </div>
         <label for="status" class="col-sm-2 col-form-label">Status</label>
         <div class="col-sm-4">
            <select class="form-control selectModal" id="status" name="status" placeholder="Jenis Kelamin">
               <option value="active"   @if($data->status === 'active') selected @endif >Active</option>
               <option value="suspend"  @if($data->status === 'suspend') selected @endif >Suspend</option>
               <option value="close"  @if($data->status === 'suspend') selected @endif >Close</option>
            </select>
         </div>
      </div>
      <div class="form-group row">
         <label for="address" class="col-sm-2 col-form-label">Alamat</label>
         <div class="col-sm">
            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat ...">{{$data->address}}</textarea>
         </div>
      </div>
   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-primary" id="updateButton" data-id="{{ $data->code }}">Perbaharui</button>
   </div>
</form>