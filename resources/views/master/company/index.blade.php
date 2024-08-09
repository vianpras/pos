@extends('layouts.app')

@section('content')
    <div class="content-wrapper" id="companyWrapper">
        <section class="content">
            <form method="POST" action="{{ route('company.store') }}" id='companyForm'>
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title ?? '' }} <span class="text-bold"></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="card">
                                    <div class="card-header" data-card-widget="collapse">
                                        <h3 class="card-title" data-card-widget="collapse">
                                            Data Perusahaan / Outlet / Toko
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="owner">Pemilik</label>
                                                    <input type="text" class="form-control " id="owner" name="owner"
                                                        placeholder="Pemilik" value="{{ $company->owner }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="name">Nama Perusahaan / Outlet / Toko</label>
                                                    <input type="text" class="form-control " name="name" id="name"
                                                        placeholder="Nama Perusahaan / Outlet / Toko" autofocus required
                                                        value="{{ $company->name }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="phone">No. Telepon</label>
                                                    <input type="number" class="form-control " id="phone" name="phone"
                                                        placeholder="Nomor Telepon" value="{{ $company->phone }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="mobile">No. Handphone</label>
                                                    <input type="number" class="form-control " id="mobile" name="mobile"
                                                        placeholder="Nomor Handphone" value="{{ $company->mobile }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control " name="email" id="email"
                                                        placeholder="Email" value="{{ $company->email }}">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="website">Website</label>
                                                    <input type="text" class="form-control " name="website" id="website"
                                                        placeholder="website" value="{{ $company->website }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="address">Alamat</label>
                                                    <textarea class="form-control" id="address1" name="address1" rows="7"
                                                        placeholder="Alamat ..."> {{ $company->address1 }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="address">Logo Perusahaan</label>

                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <span for="image" style="color: #ccc">Rec: 512 X 512 Pixels
                                                        </span>
                                                        <div class="input-group">
                                                            <div class="" style="max-width:150px">
                                                                <img style="max-width:150px; margin-right:20px;"
                                                                    id="output_image" class="img-thumbnail"
                                                                    src='/img/configurations/1' />
                                                            </div>

                                                            <div class="custom-file"
                                                                style="margin-top: 5%; margin-bottom: 10%; margin-left: 25px;margin-right: 25px;">
                                                                <input type="file" class="custom-file-input" id="image"
                                                                    accept="image/*" name="image"
                                                                    onchange="preview_image(event)" required>
                                                                <label class="custom-file-label" for="image">Pilih</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="float-right">
                                            <button type="button" onclick="save()" class="btn btn-success btn-sm"
                                                id="companySave">Simpan</button>
                                            <button type="button"  class="btn btn-danger btn-sm">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header" data-card-widget="collapse">
                                        <h3 class="card-title" data-card-widget="collapse">
                                            Pengaturan Aplikasi
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- <div class="col-sm-6">
                                                <label for="change_authorization">Edit Dengan Otorisasi</label>
                                                <div class="form-group">
                                                    <input type="checkbox" name="change_authorization"
                                                        class="switchBs form-control" id="change_authorization"
                                                        value="{{$config->change_authorization }}"
                                                        data-bootstrap-switch data-off-color="danger"
                                                        data-on-color="success"  @if($config->change_authorization) checked  @endif>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="set_inventory">Aktifkan Inventaris</label>
                                                <div class="form-group">
                                                    <input type="checkbox" name="set_inventory"
                                                        class="switchBs form-control" id="set_inventory"
                                                        value="{{$config->set_inventory }}"
                                                        data-bootstrap-switch data-off-color="danger"
                                                        data-on-color="success"  @if($config->set_inventory) checked  @endif>
                                                </div>
                                            </div> --}}

                                            <div class="col-sm-12">
                                                <label for="itemShow">Thumbnail Menu Kasir</label>
                                                <div class="form-group">
                                                    <button type="button" id="itemShow" onclick="actionShowImageItem()" class="btn btn-flat btn-block btn-outline">Non-Aktifkan</button>
                                                </div>
                                            </div>
                                            {{-- <div class="col-sm-6"> --}}
                                                {{-- <label for="set_inventory">Aktifkan Inventaris</label>
                                                <div class="form-group">
                                                    <input type="checkbox" name="set_inventory"
                                                        class="switchBs form-control" id="set_inventory"
                                                        value="{{$config->set_inventory }}"
                                                        data-bootstrap-switch data-off-color="danger"
                                                        data-on-color="success"  @if($config->set_inventory) checked  @endif>
                                                </div> --}}
                                            {{-- </div> --}}
                                            <div class="col-sm-12">
                                                <label for="total_cart">Total Meja / Keranjang</label>
                                                <div class="form-group">
                                                    <input type="number" class="form-control " id="total_cart"
                                                        name="total_cart" placeholder="Total Meja / Keranjang"
                                                        value="{{ $config->total_cart == "" ? '50' :  $config->total_cart }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="print_footer1">Footer Print Out</label>
                                                <div class="form-group">
                                                    <textarea class="form-control" id="print_footer1" name="print_footer1" rows="2"
                                                        placeholder="Footer Print Out 1"> {{ $config->print_footer1 }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <textarea class="form-control" id="print_footer2" name="print_footer2" rows="2"
                                                        placeholder="Footer Print Out 2">{{ $config->print_footer2 }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <textarea class="form-control" id="print_footer3" name="print_footer3" rows="2"
                                                        placeholder="Footer Print Out 3">{{ $config->print_footer3 }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
@section('jScript')
    <script>
        const save = () => {
            // $(document).on('click', '#companySave', function(event) {
                event.preventDefault();
                var form = new FormData();
                var formTexts = $("#companyForm").serializeArray();

                formTexts.forEach(formText => {
                    form.append(formText.name, formText.value)
                });
                var files = $('#image')[0].files;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (files.length > 0) {
                    form.append('image', files[0]);
                }
                $.ajax({
                    url: "/dataInduk/perusahaan/store",
                    method: "POST",
                    cache: false,
                    data: form,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        doBeforeSend(true)
                    },
                    success: function(result) {
                        if (result.status == 'success') {
                            Swal.fire(
                                result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                                result.message,
                                result.status
                            )
                        }
                        if (result.status == 'error') {
                            Swal.fire(
                                result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                                result.message,
                                result.status
                            )
                        }
                    },
                    complete: function() {
                        doBeforeSend(false)
                    },
                    error: function(jqXHR, testStatus, error) {
                        popToast('error', 'E999 - Terjadi Kesalah Komunikasi Server');
                        doBeforeSend(false)
                    },
                    timeout: 8000
                })
            // })

        };

        $(document).ready(function() {
            // autoSave()
        });
    </script>
@endsection
