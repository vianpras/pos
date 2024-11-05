<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="-1" />
    <title>{{ $title ?? '' }}</title>
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="/dist/img/sasoicon.png" />
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    {{-- <link rel="stylesheet" href="/plugins/daterangepicker/daterangepickerDark.css"> --}}
    <!-- summernote -->
    <link rel="stylesheet" href="/plugins/summernote/summernote-bs4.min.css">
    <!-- Swalert -->
    <link rel="stylesheet" href="/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="/plugins/select2/css/select2.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="/dist/css/custom.css">
</head>

<body class="layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-mini-xs text-sm sidebar-collapse">
    <div id="loader" class="lds-dual-ring hidden overlay"></div>
    <div class="wrapper">

        @if (session('logging'))
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="/dist/img/sasoicon.png" alt="Glo.POS" height="100">
            </div>
        @endif

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #0A6EBD; border:none;">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item" id="menu-icon">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" id="setFullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt "></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="window.location.reload(true);" role="button">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="d-none d-md-block">
                    <span class="liveDateTime nav-link" id="liveDateTime"></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout" role="button">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        @extends('layouts.nav')

        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->

        <!-- modal user -->
        <div class="modal fade" id="modalUser">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Profile Pengguna</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- body modal user -->
                        <form method='post' action='{{ route('updateProfile') }}' enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="Ffoto">Foto Profile</label>
                                    <span for="Fgambar" class="text-secondary">( 512x512px | 2MB) </span>
                                    <div class="input-group">
                                        <div class="" style="max-width:100px">
                                            <img style="max-width:100px; margin-right:20px;" id="_oPreviewImg" alt="/gambar/user/{{ Auth::id() }}" class="img-thumbnail" src='/gambar/user/{{ Auth::id() }}' />
                                        </div>
                                        <div class="custom-file" style="margin-top: 5%; margin-left: 25px;margin-right: 25px;">
                                            <input type="file" class="custom-file-input" id="Ffoto" accept="image/*" name="file" onchange="_previewImg(event)">
                                            <label class="custom-file-label" for="Ffoto">Pilih Foto Profile</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="FUser">Nama Pengguna</label>
                                            <input type="text" class="form-control" name='name' id="FUser" value="{{ Auth::user()->full_name }}" require placeholder="Masukkan Nama Pengguna">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Alamat Email</label>
                                            <input type="email" class="form-control" name='email' id="exampleInputEmail1" value="{{ Auth::user()->email }}" require disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password:</label>
                                            <div class="input-group">
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" data-toggle="password" placeholder=" Masukkan Passoword Anda" autocomplete>
                                                <div class="input-group-append">
                                                    <span class="input-group-text input-password-hide" style="cursor: pointer;"><i class="fa fa-eye"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password">Konfirmasi Password</label>
                                            <div class="input-group">
                                                <input id="password-confirm" type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" data-toggle="password" placeholder=" Konfirmasi Passoword" autocomplete>
                                                <div class="input-group-append">
                                                    <span class="input-group-text input-password-hide" style="cursor: pointer;"><i class="fa fa-eye"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <!--./ body modal user -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" onclick="reset()" class="btn btn-danger"
                                    data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- ./ modal user -->
            @if (!$nav == 'sales' || !$nav == 'booking')
                <footer class="main-footer">
                    <strong>Copyright &copy; {{ Date('Y') }} <a href="/">{{ config('app.name') }}</a>.</strong>
                    All rights reserved.
                    <div class="float-right d-none d-sm-inline-block">
                        <b>Version</b> {{ config('app.version') }}
                    </div>
                </footer>
            @endif
            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>

        {{-- button floating --}}
        <div class="position-sticky sticky-top text-right floating-action-menu col-2 float-right" style="bottom: 30px; right: 50px;">
            <div class="action-menu">
                <div class="floating-action mb-2">
                    <div class="badge badge-pill bg-teal">Kasir</div>
                    <a href="/sales/create" class="shadow btn btn-floating bg-teal rounded-circle ml-2" style="width: 45px; height: 45px" role="button" aria-pressed="true">
                        <i class="fas fa-cash-register mt-2"></i>
                    </a>
                </div>
            </div>
            <div class="d-block action-button mr-2">
                <a class="shadow btn btn-floating btn-success rounded-circle" style="width: 45px; height: 45px" role="button" aria-pressed="true" onclick="$(this).closest('div.floating-action-menu').toggleClass('active')">
                    <i class="fas fa-bars mt-2"></i>
                </a>
            </div>
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src="/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="/dist/js/jquery.mask.js"></script>

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script src="/plugins/moment/moment.min.js"></script>

    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="/plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="/plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="/plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- InputMask -->
    <script src="/plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/dist/js/adminlte.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="/plugins/toastr/toastr.min.js"></script>
    <!-- jquery-validation -->
    <script src="/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- DataTables & Plugins -->
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="/plugins/jszip/jszip.min.js"></script>
    <script src="/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- daterangepicker -->
    <script src="/plugins/daterangepicker/daterangepicker.js"></script>
    {{-- <script
         src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
         integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
         crossorigin="anonymous" referrerpolicy="no-referrer"></script>
      <script
         src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"
         integrity="sha512-zHDWtKP91CHnvBDpPpfLo9UsuMa02/WgXDYcnFp5DFs8lQvhCe2tx56h2l7SqKs/+yQCx4W++hZ/ABg8t3KH/Q=="
         crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="/dist/js/demo.js"></script> --}}
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    {{-- <script src="/dist/js/pages/dashboard.js"></script> --}}
    <script src="/dist/js/custom.js"></script>
    <script src="/dist/js/html5-qrcode.min.js"></script>
    {{-- firebase --}}
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
    {{-- <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js"></script> --}}
    {{-- <script src="/firebase-messaging-sw.js"></script> --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/firebase-messaging-sw.js');
            });
        }
    </script>

    {{-- <script>
        // importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
        // importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');
        var firebaseConfig = {
            apiKey: "AIzaSyCulO2IJ7xL3Rg3AWm29wQZZdGIre2q4fM",
            authDomain: "glo-pos.firebaseapp.com",
            projectId: "glo-pos",
            storageBucket: "glo-pos.appspot.com",
            messagingSenderId: "505224913463",
            appId: "1:505224913463:web:ff555ac651c28b644395b2",
            measurementId: "G-LFT7YFVV4B"
        };

        firebase.initializeApp(firebaseConfig);
        const messagings = firebase.messaging();

        function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken()
                })
                .then(function(token) {
                    console.log(token);

                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });

                    $.ajax({
                        url: '{{ route('saveToken') }}',
                        type: 'POST',
                        data: {
                            tokenFCM: token
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            console.log
                            alert('Token saved successfully.');
                        },
                        error: function(err) {
                            console.log('User Chat Token Error' + err);
                        },
                    });

                }).catch(function(err) {
                    console.log('User Chat Token Error' + err);
                });
        }

        messagings.onMessage(function(payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
                url: "https://sahretech.com"
            };
            new Notification(noteTitle, noteOptions);
        });
    </script> --}}
    <script>
        var firebaseConfig = {
            apiKey: "AIzaSyCulO2IJ7xL3Rg3AWm29wQZZdGIre2q4fM",
            authDomain: "glo-pos.firebaseapp.com",
            projectId: "glo-pos",
            storageBucket: "glo-pos.appspot.com",
            messagingSenderId: "505224913463",
            appId: "1:505224913463:web:ff555ac651c28b644395b2",
            measurementId: "G-LFT7YFVV4B",
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken()
                })
                .then(function(token) {
                    console.log(token);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '{{ route('saveToken') }}',
                        type: 'POST',
                        data: {
                            tokenFCM: token
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            alert('Token saved successfully.');
                        },
                        error: function(err) {
                            console.log('User Chat Token Error' + err);
                        },
                    });

                }).catch(function(err) {
                    console.log('User Chat Token Error' + err);
                });
        }

        messaging.onMessage(function(payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
                url: "https://sahretech.com"
            };
            new Notification(noteTitle, noteOptions);
        });
    </script>
    <!-- Bootstrap Switch -->
    <script src="/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script>
        // script turn on select2 on modal
        $.fn.modal.Constructor.prototype._enforceFocus = function() {
            $(".selectModal").select2({});
            //Date picker
            $('#startDateSelectModal').datetimepicker({
                setDate: 'today',
                format: 'YYYY-MM-DD'
            });
            $('#endDateSelectModal').datetimepicker({
                setDate: 'today',
                format: 'YYYY-MM-DD'
            });

            // switch BS
            $("input[data-bootstrap-switch]").each(function() {
                // $(this).bootstrapSwitch('state', $(this).prop('checked'));
                $(this).bootstrapSwitch({
                    onSwitchChange: function(e, state) {
                        _state = state ? 1 : 0
                        $(this).val(_state)
                        // $(".switchBs").val(_state)
                    }
                });
            })

            function preview_image(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById("output_image");
                    output.src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }

            $(document).ready(function() {

                // Format mata uang.
                $('.uang').mask('000.000.000.000.000', {
                    reverse: true
                });

            })
            $("input[data-bootstrap-checkbox]").each(function() {
                $(this).bootstrapSwitch({
                    onSwitchChange: function(e, state) {
                        _state = state ? 1 : 0
                        $(this).val(_state)
                        // $(".switchBs").val(_state)
                    }
                });
            })

            $("input[data-type='currency']").on({
                keyup: function() {
                    formatCurrency($(this));
                },
                blur: function() {
                    formatCurrency($(this), "blur");
                },
                load: function() {
                    formatCurrency($(this), "blur");
                }
            });
        };
    </script>
    @yield('jScript')

    <script>
        $(function() {
            $('.select2').select2({
                // theme: 'bootstrap4'
            });
            $("input[data-bootstrap-switch]").each(function() {
                // $(this).bootstrapSwitch('state', $(this).prop('checked'));
                $(this).bootstrapSwitch({
                    onSwitchChange: function(e, state) {
                        _state = state ? 1 : 0
                        $(this).val(_state)
                        // $(".switchBs").val(_state)
                    }
                });
            })

            $('#reservation').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#reservation').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            //Date picker
            $('#dateSelect').datetimepicker({
                minDate: -1,
                setDate: 'today',
                format: 'YYYY-MM-DD',
            });
            // live time
            liveTime();

            // scroll mouseX
            scrollXMenu(100);
        });
    </script>
    @if (session('status'))
        <script>
            $(function() {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: '{{ session('status')->status }}',
                    title: '&nbsp;&nbsp; {{ session('status')->message }}'
                })
            });
        </script>
    @endif
    @if (session('notifikasi'))
        <script type="text/javascript">
            Swal.fire({
                icon: '{{ session()->get('notifikasi.icon') }}',
                title: '{{ session()->get('notifikasi.title') }}',
                text: '{{ session()->get('notifikasi.message') }}'
            });
        </script>
    @endif

</body>

</html>
