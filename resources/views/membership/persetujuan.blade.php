<form class="form-horizontal" id="formNew">
    {{ csrf_field() }}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
    </button>
    <div class="card-body">
        <h5 class="modal-title">{{ $title ?? '' }}</h5>
        <hr>
        <div class="form-group row">
            <label for="member" class="col-sm-2 col-form-label">Member</label>
            <div class="col-sm-4">
                <select class="form-control selectModal" id="member" name="member" placeholder="Member" required>
                    @foreach($members AS $mmbr)
                    <option selected>Pilih</option>
                    <option value="{{ $mmbr->code }}">{{ $mmbr->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="row justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-success" id="emailButton">Kirim Email</button>
    </div>
 </form>