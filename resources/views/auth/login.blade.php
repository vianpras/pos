<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>{{ Helper::title($title) }}</title>

   <!-- Google Font: Source Sans Pro -->
   <link rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
   <!-- icheck bootstrap -->
   <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
   <!-- Theme style -->
   <link rel="stylesheet" href="/dist/css/adminlte.min.css">
   <!-- Swalert -->
   <link rel="stylesheet" href="/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
   <!-- Toastr -->
   <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
</head>

<body class="hold-transition login-page" style="background: #8A2387;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #F27121, #E94057, #8A2387);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #F27121, #E94057, #8A2387); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
   <div class="login-box">

      <!-- /.login-logo -->
      <div class="card" style="border-radius: 10px; ">
         <div class="card-body login-card-body" style="border-radius: 10px ">
            <div class="login-logo">
               <a href="/"><img src="/dist/img/logosaso.png" height="150px" /></a>
            </div>
            <hr>
            <p class="login-box-msg">Masuk Aplikasi </p>
            <p class="login-box-msg">u/p : superuser@admin.com / tidaktahu </p>

            <form method="POST" action="/authenticate" id="Validating">
               @csrf
               <div class="form-group input-group mb-3">
                  <input type="email" name="email" class="form-control" placeholder="Email" value="superuser@admin.com">
                  <div class="input-group-append">
                     <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                     </div>
                  </div>
               </div>
               <div class="form-group input-group form mb-3">
                  <input type="password" name="password" id="password" class="form-control" placeholder="Kata Sandi"
                     data-toggle="password" value="tidaktahu" >
                  <div class="input-group-append">
                     <div class="input-group-text">
                        <a><i class="fas fa-eye-slash" aria-hidden="true"></i></a>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-8">
                     <div class="icheck-navy">
                        <input type="checkbox" id="remember" name="remember" value="1">
                        <label for="remember">
                           Ingat Saya
                        </label>
                     </div>
                  </div>
                  <!-- /.col -->
                  <div class="col-4">
                     <button type="submit" class="btn bg-navy btn-block">Masuk</button>
                  </div>
                  <!-- /.col -->
               </div>
            </form>

            <span class="mb-1 ">
               {{-- <hr> --}}
               {{-- <a href="/auth/forgot" class=" text-danger">Lupa Kata Sandi</a> --}}
            </span>

         </div>
         <!-- /.login-card-body -->
      </div>
   </div>
   <!-- /.login-box -->


   <!-- jQuery -->
   <script src="/plugins/jquery/jquery.min.js"></script>

   <!-- Bootstrap 4 -->
   <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
   <!-- AdminLTE App -->
   <script src="/dist/js/adminlte.min.js"></script>
   <!-- SweetAlert2 -->
   <script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
   <!-- Toastr -->
   <script src="/plugins/toastr/toastr.min.js"></script>
   <!-- jquery-validation -->
   <script src="/plugins/jquery-validation/jquery.validate.min.js"></script>
   <script src="/plugins/jquery-validation/additional-methods.min.js"></script>
   <script src="/plugins/jquery-validation/additional-methods.min.js"></script>
   {{-- customJS --}}
   <script src="/dist/js/custom.js"></script>
   {{-- Validation JS --}}
   <script src="/dist/js/validation.js"></script>
   <script>
      //validation
   $('#Validating').validate({
      rules: {
         email: {
            required: true,
            email: true,
         },
         password: {
            required: true,
            minlength: 5
         },

      },
      messages: {
         email: {
            required: "Harap mengisi form email!",
            email: "Format email salah"
         },
         password: {
            required: "Harap mengisi form kata sandi",
            minlength: "Kata sandi minimal 8 karakter "
         },
      },
      errorElement: 'span',
      errorPlacement: function (error, element) {
         error.addClass('invalid-feedback');
         element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
         $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
         $(element).removeClass('is-invalid');
      }
   });
   </script>
   {{-- Custom JS --}}
   @if (session('status'))
   <script>
      $(function() {
      // toast
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



</body>

</html>