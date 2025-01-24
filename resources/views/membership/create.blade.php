@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <section class="content" style="padding-top: .5rem;">
      <form class="form-horizontal" id="formNew">
         {{ csrf_field() }}
         <div class="card m-1">
            <div class="card-body">
               <h5 class="modal-title">{{ $title ?? '' }}</h5>
               <hr>
               <input type="hidden" name="member_category" value="retail">
               <div class="form-group row">
                  <label for="nama" class="col-sm-2 col-form-label">Nama Lengkap</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama Lengkap" value="{{ old('nama') }}" required>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="mobile" class="col-sm-2 col-form-label">Nomor HP</label>
                  <div class="col-sm-4">
                     <input type="number" class="form-control" name="mobile" id="mobile"  minlength="10" maxlength="13" placeholder="Nomor HP" value="{{ old('mobile') }}" required>
                  </div>
                  <label for="gender" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                  <div class="col-sm-4">
                     <select class="form-control selectModal" id="gender" name="gender" placeholder="Jenis Kelamin" required>
                        <option value="l" selected>Laki-laki</option>
                        <option value="p">Perempuan</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="kota" class="col-sm-2 col-form-label">Kota</label>
                  <div class="col-sm-4">
                     <input type="text" class="form-control " name="kota" id="kota" placeholder="Kota" value="{{ old('kota') }}" required>
                  </div>
                  <label for="provinsi" class="col-sm-2 col-form-label">Provinsi</label>
                  <div class="col-sm-4">
                     <input type="text" class="form-control" name="provinsi" id="provinsi" placeholder="Provinsi" value="{{ old('provinsi') }}" required>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="place_birth" class="col-sm-2 col-form-label">Tempat Lahir</label>
                  <div class="col-sm-4">
                     <input type="text" class="form-control" name="place_birth" id="place_birth" placeholder="Tempat Lahir" value="{{ old('place_birth') }}" required>
                  </div>
                  <label for="date_birth" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                  <div class="col-sm-4">
                     <input type="date" class="form-control form-control-sm" data-target="#startDateSelectModal" name="date_birth" id="date_birth" required/>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="email" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-4">
                     <input type="email" class="form-control" name="email" id="email" placeholder="email" value="{{ old('email') }}" required>
                  </div>
                  <label for="status" class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-4">
                     <select class="form-control selectModal" id="status" name="status" placeholder="Jenis Kelamin" required>
                        <option value="active" selected>Active</option>
                        <option value="suspend">Suspend</option>
                        <option value="close">Close</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="address" class="col-sm-2 col-form-label">Alamat</label>
                  <div class="col-sm">
                     <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat ..." required></textarea>
                  </div>
               </div>
               <hr>
               <div class="row justify-content-end">
                  <button type="button" class="btn btn-success" id="saveButton" onclick="saveData()">Simpan</button>
               </div>
            </div>
         </div> 
      </form>
   </section>
</div>
@endsection
@section('jScript')
<script>
   const saveData = () => {
      let formData = $("form").serialize();
      console.log(formData);
      swal.fire({
         title: "Apakah Ingin Menyimpan",
         icon: "question",
         width: 1000,
         showDenyButton: false,
         showCancelButton: true,
         confirmButtonColor: "#20C997",
         denyButtonColor: "#007BFF",
         cancelButtonColor: "#DC3545",
         confirmButtonText: "Simpan",
         cancelButtonText: "Batal",
         html: ``,
      }).then((resultSwal1) => {

         // Jika isConfirm(Bayar)
         if (resultSwal1.isConfirmed) {
               let href = "/keanggotaan/store/";
               $.ajax({
                  url: href,
                  method: "POST",
                  data: formData,
                  beforeSend: function() {
                     doBeforeSend(true)
                  },
                  success: function(res) {
                     console.log(res);
                     popToast('success', 'Berhasil insert member');

                     window.location.href = "{{ url('keanggotaan') }}";
                  },
                  error: function(jqXHR, testStatus, error) {
                     popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');
                     doBeforeSend(false);
                  },

                  complete: function() {
                     // selesai
                     doBeforeSend(false);
                  },
                  timeout: 8000,
               });
         }
      });
   };
</script>
@endsection