@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        
        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-profile"></i> @lang('profile.header')
                </h5>
            </div>

            <div class="panel-body">

                <form method="POST" action="{{ route('profile.update', [$user->id]) }}" enctype="multipart/form-data">
                    
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-10">
                            <div class="panel-body">

                                <div class="form-group" align="center">
                                    <!-- AVATAR VIEW -->
                                    <div class="col-md-12 col-xs-12">
                                        <div class="media no-margin-top">
                                            <div class="media-left">
                                                <a href="#"><img src="{{ ($user->avatar != '') ? url('uploads/avatars/'.$user->avatar) : url('assets/images/placeholder.jpg')  }}" style="width: 130px; height: 130px;" alt="" class="img-circle" id="preview_avatar"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- AVATAR VIEW -->
                                </div>

                                <div class="form-group">
                                    <!-- AVATAR FIELD -->
                                    <div class="col-md-12 col-xs-12">
                                        <label class="text-semibold">@lang('profile.avatar'):</label>
                                        <div class="media-body">
                                            <input type="file" class="file-styled-primary image_upload" name="avatar" value="" accept="image/*" id="avatar">
                                            <span class="help-block">@lang('general.acceptUploadImage')</span>
                                        </div>
                                    </div>
                                    <!-- AVATAR FIELD -->
                                </div>
                                
                                <div class="form-group">

                                    <!-- EMAIL FIELD -->
                                    <div class="col-md-6 col-xs-12">
                                        <label>@lang('profile.email'):</label>
                                        <input type="text" class="form-control" name="email" value="{{ $user->email }}" readonly="" disabled="">
                                    </div>
                                    <!-- EMAIL FIELD -->

                                    <!-- NAME FIELD -->
                                    <div class="col-md-6 col-xs-12">
                                        <label>@lang('profile.name'):</label>
                                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" placeholder="amirul adib" required="" autocomplete="off" autofocus="">
                                    </div>
                                    <!-- NAME FIELD -->

                                    <!-- CONTACT NO FIELD -->
                                    <div class="col-md-6 col-xs-12" style="margin-bottom: 10px;">
                                        <label>@lang('profile.contact'):</label>
                                        <input type="text" class="form-control" name="contact_no" value="{{ $user->contact }}" placeholder="0115104544" autocomplete="off">
                                    </div>
                                    <!-- CONTACT FIELD -->

                                    <!-- language FIELD -->
                                    <div class="col-md-6 col-xs-12" style="margin-bottom: 10px;">
                                        <label>@lang('profile.language'):</label>
                                        <select class="form-control" name="language">
                                            @foreach($language as $lang)
                                                <option value="{{ $lang->id }}" {{ ($lang->id == $user->language_id ? 'selected' : '') }}>{{ $lang->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- language FIELD -->
                                </div>

                                <div class="text-right">
                                    <div class="col-md-12 col-xs-12">
                                        <button type="submit" class="btn btn-primary">@lang('general.submit')<i class="icon-circle-right2 position-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            
            </div>

        </div>
    </div>
    
@endsection
