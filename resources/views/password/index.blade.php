@extends('components.template-limitless.main')

@section('main')
<div class="row">
    
    <div class="col-md-12">
        
        <div class="panel panel-flat">

            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon-lock"></i> Change Password
                </h3>
            </div>

            <div class="panel-body">

                <form method="POST" action="{{ route('update-password.store') }}" enctype="multipart/form-data">
                    
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel-body">

            
                                <div class="form-group">

                                    <!-- EMAIL FIELD -->
                                    <div class="col-md-12 col-xs-12">
                                        <label>Current Password:</label>
                                        <input type="password" class="form-control" name="old_password" value=""  autofocus="" required="">
                                    </div>
                                    <!-- EMAIL FIELD -->

                                    <!-- NAME FIELD -->
                                    <div class="col-md-12 col-xs-12">
                                        <label>New Password:</label>
                                        <input type="password" class="form-control" name="password" value="" required="" autocomplete="off" required="">
                                    </div>
                                    <!-- NAME FIELD -->

                                    <!-- CONTACT NO FIELD -->
                                    <div class="col-md-12 col-xs-12" style="margin-bottom: 10px;">
                                        <label>Confirm New Password:</label>
                                        <input type="password" class="form-control" name="password_confirmation" value="" autocomplete="off" required="">
                                    </div>
                                    <!-- CONTACT FIELD -->
                                </div>

                                <div class="text-right">
                                    <div class="col-md-12 col-xs-12">
                                        <button type="submit" class="btn btn-primary">Submit<i class="icon-circle-right2 position-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            
            </div>

        </div>
    </div>
</div>
@endsection
