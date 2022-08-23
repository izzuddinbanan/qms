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
                <form action="{{ route('client.update', [$data->id]) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')
                    <!-- POWER USER FORM -->
                    <fieldset class="content-group">
                        <div class="col-md-6">
                            <legend class="text-bold">Client Details</legend>
                            

                            <!-- Client Name -->
                            <div class="form-group">
                                <label class="text-semibold">Name :</label>
                                <input type="text" class="form-control" name="client_name" value="{{ $data->name }}" autocomplete="off" autofocus="" required="" placeholder="UEM Group">
                            </div>

                            <!-- Client Abbreviation Name -->
                            <div class="form-group">
                                <label class="text-semibold">Abbreviation Name:</label>
                                <input type="text" class="form-control" name="abbreviation_name" value="{{ $data->abbreviation_name }}" autocomplete="off" autofocus="" required="" placeholder="UEM">
                            </div>

                            <!--Logo -->
                            <div class="form-group">
                                <label class="text-semibold">Logo :</label>
                                <div class="media no-margin-top">
                                    <div class="media-left">
                                        <a href="{{ ($data->logo == null ? 'javascript:void(0)' : url('uploads/client_logo/' . $data->logo) ) }}" data-popup="lightbox">
                                            <img src="{{ ($data->logo == null ? url('assets/images/placeholder.jpg') : url('uploads/client_logo/' . $data->logo) ) }}" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_logo">
                                        </a>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" class="file-styled-primary image_upload" name="logo" value="" accept="image/*" id="logo">
                                        <span class="help-block">Accepted formats: gif, png, jpg.</span>
                                    </div>
                                </div>
                            </div>

                            <!--APP Logo -->
                            <div class="form-group">
                                <label class="text-semibold">App Logo :</label>
                                <div class="media no-margin-top">
                                    <div class="media-left">

                                        <a href="{{ ($data->app_logo == null ? 'javascript:void(0)' : url('uploads/client_logo/' . $data->app_logo) ) }}" data-popup="lightbox">
                                            <img src="{{ ($data->app_logo == null ? url('assets/images/placeholder.jpg') : url('uploads/client_logo/' . $data->app_logo) ) }}" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_app_logo">
                                        </a>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" class="file-styled-primary image_upload" name="app_logo" value="" accept="image/*" id="app_logo">
                                        <span class="help-block">Accepted formats: gif, png, jpg.</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <legend class="text-bold">Power User Details</legend>

                            <!-- User Name -->
                            <div class="form-group">
                                <label class="text-semibold">Name :</label>
                                <input type="text" class="form-control" name="user_name" value="{{ $data->user_name }}"  autocomplete="off" placeholder="Samantha">
                            </div>


                            <!-- User Email -->
                            <div class="form-group">
                                <label class="text-semibold">Email :</label>
                                <input type="email" class="form-control" name="email" value="{{ $data->user_email }}" required="" placeholder="Samantha@gmail.com" autocomplete="off" disabled="" readonly="">
                            </div>

                            <!-- Contact No -->
                            <div class="form-group">
                                <label class="text-semibold">Contact No :</label>
                                <input type="text" class="form-control" name="contact" value="{{ $data->user_contact }}" autocomplete="off" placeholder="012571152">
                            </div>

                            <!-- user id -->
                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">

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


