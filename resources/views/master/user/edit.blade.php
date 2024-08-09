<form class="form-horizontal" id="formUpdate">
   {{ csrf_field() }}
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="form-group row">
         <label for="name" class="col-sm-2 col-form-label">Nama User</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="name" placeholder="name" value="{{ $data->name }}">
         </div>
         <label for="username" class="col-sm-2 col-form-label">Nama Unik</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="username" placeholder="username" value="{{ $data->username }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="mobile" class="col-sm-2 col-form-label">Nomor HP</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="mobile" placeholder="mobile" value="{{ $data->mobile }}">
         </div>
         <label for="email" class="col-sm-2 col-form-label">Email</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="email" placeholder="Email" value="{{ $data->email }}">
         </div>
      </div>
      <div class="form-group row">
         <label for="users_acls_id" class="col-sm-2 col-form-label">Hak Akses</label>
         <div class="col-sm-4">
            {{-- <input type="text" class="form-control" id="users_acls_id" placeholder="users_acls_id"
               value="{{ $data->users_acls_id }}"> --}}
            <select class="form-control selectModal" id="users_acls_id" name="users_acls_id" placeholder="Hak Akses">
               @foreach ($user_acl as $option)
               <option value="{{ $option->id }}" @if($option->id === $data->users_acls_id) selected @endif >{{
                  $option->name }}</option>
               @endforeach
            </select>
         </div>
         <label for="created_at" class="col-sm-2 col-form-label">Di buat</label>
         <div class="col-sm-4">
            <input type="text" class="form-control" id="created_at" placeholder="created_at"
               value="{{ $data->created_at }}" readonly>
         </div>
      </div>
      <div class="form-group row">
         <label for="status" class="col-sm-2 col-form-label">Status</label>
         <div class="col-sm-4">
            <input type="checkbox" name="status" class="switchBs" id="status" value={{ $data->status }}
            @if($data->status)checked @endif data-bootstrap-switch data-off-color="danger" data-on-color="success">
         </div>
         <label for="sudo" class="col-sm-2 col-form-label">Super User</label>
         <div class="col-sm-4">
            <input type="checkbox" name="sudo" class="switchBs" id="sudo" value={{ $data->sudo }}
            @if($data->sudo)checked @endif data-bootstrap-switch data-off-color="danger" data-on-color="success">
         </div>
      </div>

   </div>
   <hr>
   <div class="row justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-primary" id="updateButton" data-id="{{ $data->id }}">Perbaharui</button>
   </div>
</form>