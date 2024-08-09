<form class="form-horizontal" id="formNew">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
   <div class="card-body">
      <h5 class="modal-title">{{ $title ?? '' }}</h5>
      <hr>
      <div class="row d-flex flex-column justify-content-center">
         {{-- content modul --}}
         <label for="name" class="col-sm-2 col-form-label">Nama Hak Akses</label>
         <div class="col-auto mb-4">
            <input type="text" class="form-control" id="name" placeholder="Nama" value="{{ old('name') }}">
         </div>
         @foreach ($columns as $column)
         <div class="form-group">
            <div class="form-group" id="{{ $column }}">
               <div class="row" onclick="bsCheck(this.parentNode.id)">
                  <button type="button" class="btn btn-xs btn-circle"><i class="fas fa-check-double fa-xs"></i></button>&nbsp;
                  <label for="{{ $column }}" class="pr-4 text-olive">{{ Helper::trans($column) }} </label>
               </div>
               <div class="d-flex justify-content-around" >
                  <div class="icheck-lime d-inline" id="div_{{ $column }}_c">
                     <input type="checkbox"  name="{{ $column }}" id="{{ $column }}_c" >
                     <label for="{{ $column }}_c" style="font-weight: normal;">Buat</label>
                  </div>
                  <div class="icheck-lime d-inline">
                     <input type="checkbox" name="{{ $column }}" id="{{ $column }}_r">
                     <label for="{{ $column }}_r" style="font-weight: normal;">Baca</label>
                  </div>
                  <div class="icheck-lime d-inline">
                     <input type="checkbox" name="{{ $column }}" id="{{ $column }}_u">
                     <label for="{{ $column }}_u" style="font-weight: normal;">Ubah</label>
                  </div>
                  <div class="icheck-lime d-inline">
                     <input type="checkbox" name="{{ $column }}" id="{{ $column }}_d">
                     <label for="{{ $column }}_d" style="font-weight: normal;">Hapus</label>
                  </div>
                  <div class="icheck-lime d-inline">
                     <input type="checkbox" name="{{ $column }}" id="{{ $column }}_i">
                     <label for="{{ $column }}_i" style="font-weight: normal;">Impor</label>
                  </div>
                  <div class="icheck-lime d-inline">
                     <input type="checkbox" id="{{ $column }}_e">
                     <label for="{{ $column }}_e" style="font-weight: normal;">Expor</label>
                  </div>
               </div>
            </div>
         </div>
         @endforeach
         {{-- ./content modul --}}
      </div>
   </div>

   <hr>
   <div class="d-flex  justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      <button type="button" class="btn btn-success" id="saveButton">Simpan</button>
   </div>
</form>