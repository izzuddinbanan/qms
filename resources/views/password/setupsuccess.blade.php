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
                            <h5 class="content-group-lg">Congratulation <small class="display-block">You are now a member of QMS.</small></h5>
                        </div>

                @if(session('success-message') && session('success-message')=="success")
                <!-- <div class="modal fade" id="modal_setup_success" role="dialog">
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
                </div> -->
                @endif 

                <!-- <script>
                    @if(session('success-message') && session('success-message')=="success")
                    $("#modal_setup_success").modal('toggle');
                    @endif
                </script> -->
                <!-- <style>
                    .success{
                        color:#4BB543;
                    }
                </style> -->

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
