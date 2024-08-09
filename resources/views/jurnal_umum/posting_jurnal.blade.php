<form class="form-horizontal" id="formNew">
    {{ csrf_field() }}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
    </button>
    <div class="card-body">
        <h5 class="modal-title">{{ $title ?? '' }}</h5>
        <hr>
        <div class="form-group row">
            <label for="start_date" class="col-sm-2 col-form-label">Tanggal Awal</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="start_date" placeholder="start_date" value="{{ old('start_date') }}" required>
            </div>
            <label for="end_date" class="col-sm-2 col-form-label">Tanggal Akhir</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="end_date" placeholder="end_date" value="{{ old('end_date') }}" required>
            </div>
        </div>
    </div>
    <hr>
    <div class="row justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-success" id="postingButton">Posting</button>
    </div>
 </form>