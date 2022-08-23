<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Quality Manage System') }}</title>
    <link rel="shortcut icon" href="{{ url('images/qms_app_logo.png') }}">

<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/vendor/bootstrap/css/bootstrap.min.css') }}">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/vendor/animate/animate.css') }}">
<!--===============================================================================================-->  
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/vendor/css-hamburgers/hamburgers.min.css') }}">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/vendor/select2/select2.min.css') }}">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('template-login/css/main.css') }}">
<!--===============================================================================================-->
</head>
<body>
    
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt style="padding-top: 50px;">
                    <img src="{{ url('template-login/images/img-01.png') }}" alt="IMG">
                </div>


                <form class="login100-form validate-form"  method="POST" action="{{ route('login') }}" >
                    @csrf
                    <span class="login100-form-title text-center">
                        <div>
                            <img src="{{ url('assets/images/logo_login.png') }}" alt="" style="height: 120px;width: 120px;">
                        </div>
                        <style type="text/css">
                            @font-face {
                                font-family: multicolore;
                                src: url(assets/css/icons/Multicolore.otf);
                                font-weight: bold;
                            }

                        </style>
                        <h4 style=" font-family: multicolore;color: #006D8D;margin-top: 0px;margin-bottom: 15px;">QMS</h4>
                        <h5 class="content-group-lg" style="color: black;">Login to Your Account </h5>
                        <hr>
                    </span>

                    @if ($errors->has('email'))
                        <div class="alert alert-danger alert-bordered">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                            <span class="text-semibold">{{ $errors->first('email') }}</span>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                            <span class="text-semibold">{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <input class="input100" type="text" name="email" placeholder="Email" autocomplete="off" value="{{ old('email') }}">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate = "Password is required">
                        <input class="input100" type="password" name="password" placeholder="Password" autocomplete="off">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                    
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn">
                            Login
                        </button>
                    </div>

                    <div class="text-center p-t-12">
                        <span class="txt1">
                            Forgot
                        </span>
                        <a class="txt2" href="{{ route('password.request') }}">
                            Password?
                        </a>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>
    
    

    
<!--===============================================================================================-->  
    <script src="{{ url('template-login/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ url('template-login/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ url('template-login/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ url('template-login/vendor/select2/select2.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ url('template-login/vendor/tilt/tilt.jquery.min.js') }}"></script>
    <script >
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
<!--===============================================================================================-->
    <script src="{{ url('template-login/js/main.js') }}"></script>

</body>
</html>