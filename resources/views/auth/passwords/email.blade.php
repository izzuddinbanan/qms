<!DOCTYPE html>
<html lang="en">
<head>

    @include('layouts.header')

</head>


<body class="navbar-bottom login-container">

    <!-- Main navbar -->
    <div class="navbar navbar-inverse">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ url('assets/images/qms_logo.png') }}" alt=""></a>

        </div>
    </div>
    <!-- /main navbar -->


    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">

                <!-- Password recovery -->
                <form method="POST" action="{{ route('password.email') }}" aria-label="{{ __('Reset Password') }}">
                        @csrf
                    <div class="panel panel-body login-form">
                        <div class="text-center">
                            <!-- <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div> -->
                            <div>
                                <img src="{{ url('assets/images/logo_login.png') }}" alt="" style="height: 120px;width: 120px;">
                            </div>
                            <style type="text/css">
                                @font-face {
                                    font-family: multicolore;
                                    src: url({{ url('assets/css/icons/Multicolore.otf') }});
                                    font-weight: bold;
                                }

                            </style>
                            <h4 style=" font-family: multicolore;color: #006D8D;margin-top: 0px;margin-bottom: 15px;">QMS</h4>
                            <h5 class="content-group">Password recovery <small class="display-block">We'll send you instructions in email</small></h5>
                        </div>
                        @if ($errors->has('email'))
                            <div class="alert alert-danger alert-bordered">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">{{ $errors->first('email') }}
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="form-group has-feedback">
                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Your email" id="email" name="email" value="{{ old('email') }}" require>
                            <div class="form-control-feedback">
                                <i class="icon-mail5 text-muted"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn bg-blue btn-block">Reset password <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                </form>
                <!-- /password recovery -->

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->


    <!-- Footer -->
    
    @include('layouts.footer')
    
    <!-- /footer -->

</body>
</html>
