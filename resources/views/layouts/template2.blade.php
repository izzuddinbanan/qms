<!DOCTYPE html>
<html lang="en">

<head>

    @include('layouts.header')
    
</head>

<body class="navbar-bottom navbar-top {{ (Route::currentRouteName() == 'plan.index' ? 'sidebar-xs' : '') }}" >

    <div class="loader" style="display: none;"></div>

    <!-- Main navbar -->
    @include('layouts.navbar')
    <!-- /main navbar -->


    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main sidebar -->
            @include('layouts.sidebar')
            <!-- /main sidebar -->


            <!-- Main content -->
            <div class="content-wrapper">

                @yield('main')

            </div>
            <!-- /main content -->
        </div>
        <!-- /page content -->
    </div>
    <!-- /page container -->

    <!-- Footer -->
    @include('layouts.footer')
    <!-- Footer -->

    <!-- Global Modal -->
    @include('layouts.modal')
    <!-- /Global Modal-->

    <!-- GLOBAL SCRIPT -->
    @include('layouts.script')
    <!-- /GLOBAL SCRIPT -->
    
    
    <!-- NOTIFY MESSAGE -->
    @include('layouts.notify_message')
    <!-- /NOTIFY MESSAGE -->

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('script')


    
</body>
</html>
