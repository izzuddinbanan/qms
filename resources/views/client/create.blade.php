@extends('components.template-limitless.main')

@section('main')

<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-user-tie"></i> New Client</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('client.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>
            </div>
        </div>
    </div>


    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- POWER USER FORM -->
                    <fieldset class="content-group">
                        
                        <!-- CLIENT/COMPANY COLUMN -->
                        <div class="col-md-6">
                            <legend class="text-bold">@lang('client.clientHead')</legend>
                            

                            <!-- Client Name -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.name') :</label>
                                <input type="text" class="form-control" name="client_name" value="{{ old('client_name') }}" autocomplete="off" autofocus="" required="" placeholder="e.g UEM Group">
                            </div>

                            <!-- Client Abbreviation Name -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.abvName') :</label>
                                <input type="text" class="form-control" name="abbreviation_name" autocomplete="off" autofocus="" required="" placeholder="e.g UEM" value="{{ old('abbreviation_name') }}">
                            </div>

                            <!--Logo -->
                            <div class="form-group">
                                <label class="text-semibold">Logo :</label>
                                <div class="media no-margin-top">
                                    <div class="media-left">
                                        <a href="jacascript:void(0)"><img src="{{ url('assets/images/placeholder.jpg') }}" style="width: 58px; height: 58px; border-radius: 2px;" alt=""  id = "preview_logo"></a>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" class="file-styled-primary image_upload" name="logo" value="" accept="image/*" id="logo">
                                        <span class="help-block">Accepted formats: gif, png, jpg.</span>
                                    </div>
                                </div>
                            </div>

                            <!--APP Logo -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.appLogo') :</label>
                                <div class="media no-margin-top">
                                    <div class="media-left">
                                        <a href="jacascript:void(0)"><img src="{{ url('assets/images/placeholder.jpg') }}" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id = "preview_app_logo"></a>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" class="file-styled-primary image_upload" name="app_logo" value="" accept="image/*" id="app_logo">
                                        <span class="help-block">Accepted formats: gif, png, jpg.</span>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- // col-md-6 -->

                        <!-- SUPER USER COLUMN -->
                        <div class="col-md-6">

                            <legend class="text-bold">@lang('client.powerUserHead')</legend>

                            <!-- User Name -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.name') :</label>
                                <input type="text" class="form-control" name="user_name" value="{{ old('user_name') }}"  autocomplete="off" placeholder="e.g Samantha">
                            </div>

                            <!-- User Email -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.email') :</label>
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}" required="" placeholder="e.g samantha@gmail.com" autocomplete="off">
                            </div>


                            <!-- User Password -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.password') :</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" type="button" data-popup="tooltip" title="@lang('client.genPass')" id="btn-random"><i class="icon-lock"></i></button>
                                    </span>
                                    <input type="text" class="form-control" placeholder="e.g abc123" name="password" id="password" value="{{ old('password') }}" required="" autocomplete="off">
                                    <!-- <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="show-pass"  onclick="return show_hidePass(this.id)" ><i class="icon-eye"></i></button>
                                    </span> -->
                                </div>
                            </div>

                            <!-- Contact No -->
                            <div class="form-group">
                                <label class="text-semibold">@lang('client.contact') :</label>
                                <input type="text" class="form-control" name="contact" value="{{ old('contact') }}" autocomplete="off" placeholder="012571152">
                            </div>
                        </div>

                        <!-- BUTTON SUBMIT/BACK -->
                        <div class="col-md-12 col-xs-12 right">
                            <div class="text-right">
                                <a href="{{ route('client.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>

                                <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                            </div>
                        </div>
                    </fieldset>

                </form>
            
            </div>

        </div>  
    </div>
</div>
@endsection
