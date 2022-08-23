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
            {{ session('client_name') }}
            <div class="content-wrapper">

                <!-- Advanced login -->
                <form method="POST" action="{{ route('password.update', $token) }}">
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
                                    src: url(assets/css/icons/Multicolore.otf);
                                    font-weight: bold;
                                }

                            </style>
                            <h4 style=" font-family: multicolore;color: #006D8D;margin-top: 0px;margin-bottom: 15px;">QMS</h4>
                            <h5 class="content-group-lg">Setup Password <small class="display-block">Enter password to complete the setup</small></h5>
                        </div>
                        

                         
                       

                        <div class="form-group has-feedback has-feedback-left">
                            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }} input-lg" placeholder="Password" name="password" required>
                            <div class="form-control-feedback">
                                <i class="icon-lock2 text-muted"></i>
                            </div>
                        </div>

                        <div class="form-group has-feedback has-feedback-left">
                            <input type="password" class="form-control input-lg" placeholder="Confirm Password" name="password_confirmation" required>
                            <div class="form-control-feedback">
                                <i class="icon-lock2 text-muted"></i>
                            </div>
                        </div>

                        

                        <div class="form-group">
                            <button type="submit" class="btn bg-blue btn-block btn-lg">Set Password <i class="icon-arrow-right14 position-right"></i></button>
                        </div>

                        
                    </div>
                </form>
                <!-- /advanced login -->

                @if(session('success-message') && session('success-message')=="success")
                <div class="modal fade" id="modal_setup_success" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title success"><i class="fa fa-check-circle"></i> Setup Complete<hr></h5>
                            </div>

                            <div class="panel-body">
                                <p>Congratulation! You are now a member of QMS.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif 

                <script>
                    @if(session('success-message') && session('success-message')=="success")
                    $("#modal_setup_success").modal('toggle');
                    @endif
                </script>
                <style>
                    .success{
                        color:#4BB543;
                    }
                </style>

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->

    

    <!-- Footer -->

    @include('layouts.footer')
    
    <!-- Footer -->
</body>
</html>
